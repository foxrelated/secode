<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    List
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class List_Api_Core extends Core_Api_Abstract {
  const IMAGE_WIDTH = 720;
  const IMAGE_HEIGHT = 720;
  const THUMB_WIDTH = 140;
  const THUMB_HEIGHT = 160;

  public function createPhoto($params, $file) {

    if ($file instanceof Storage_Model_File) {
      $params['file_id'] = $file->getIdentity();
    } else {

      //GET IMAGE INFO AND RESIZE
      $name = basename($file['tmp_name']);
      $path = dirname($file['tmp_name']);
      $extension = ltrim(strrchr($file['name'], '.'), '.');

      $mainName = $path . '/m_' . $name . '.' . $extension;
      $thumbName = $path . '/t_' . $name . '.' . $extension;

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
              ->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
              ->write($mainName)
              ->destroy();

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
              ->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
              ->write($thumbName)
              ->destroy();

      //STORE PHOTO
      $photo_params = array(
          'parent_id' => $params['listing_id'],
          'parent_type' => 'list_listing',
      );

      $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
      $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
      $photoFile->bridge($thumbFile, 'thumb.normal');

      $params['file_id'] = $photoFile->file_id;
      $params['photo_id'] = $photoFile->file_id;

      //REMOVE TEMP FILES
      @unlink($mainName);
      @unlink($thumbName);
    }

    $row = Engine_Api::_()->getDbtable('photos', 'list')->createRow();
    $row->setFromArray($params);
    $row->save();
    return $row;
  }

  //CHECK VIDEO PLUGIN ENABLE / DISABLE
  public function enableVideoPlugin() {
    return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video');
  }

  /**
   * Gets an absolute URL to the page to view listing
   *
   * @return string
   */
  public function getHref($listing_id, $owner_id, $slug =null) {

    $params = array_merge(array('user_id' => $owner_id, 'listing_id' => $listing_id, 'slug' => $slug));
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, 'list_entry_view', true);
  }

  /**
   * Page base network enable
   *
   * @return bool
   */
  public function pageBaseNetworkEnable() {
    return (bool) ( (Engine_Api::_()->getApi('settings', 'core')->getSetting('list.network', 0) || Engine_Api::_()->getApi('settings', 'core')->getSetting('list.default.show', 0)));
  }

  //APROVED/ DISAPROVED EMAIL NOTIFICATION FOR CLASSIFEID
  public function aprovedEmailNotification(Core_Model_Item_Abstract $object, $params=array()) {

    $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;

    Engine_Api::_()->getApi('mail', 'core')->sendSystem($params['mail_id'], 'LIST_APPROVED_EMAIL_NOTIFICATION', array(
        'host' => $_SERVER['HTTP_HOST'],
        'subject' => $params['subject'],
        'title' => $params['title'],
        'message' => $params['message'],
        'object_link' => $this->getHref($object->listing_id, $object->owner_id, $object->getSlug()),
        'email' => $email,
        'queue' => false
    ));
  }

  /**
   * Check location is enable
   *
   * @param array $params
   * @return int $check
   */
  public function enableLocation($params=array()) {
    $list_recent_info = Zend_Registry::isRegistered('list_recent_info') ? Zend_Registry::get('list_recent_info') : null;
    if (!empty($list_recent_info)) {
      $check = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.location', 1);

      if (!empty($check)) {
        $check = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.locationfield', 1);
      }
    } else {
      exit();
    }

    return $check;
  }

  public function removeMapLink($string) {
    if (!empty($string)) {
      $reqStartMapPaterrn = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . 'maps.google.com/?q=';
      $reqEndPatern = '>map</a>]';
      $positionMapStart = strpos($string, $reqStartMapPaterrn);
      if ($positionMapStart !== false) {
        $reqStartPatern = "<a";
        $positionStart = strpos($string, $reqStartPatern, ($positionMapStart - 10));
        $positionEnd = strpos($string, $reqEndPatern, $positionStart);
        if ($positionStart < $positionMapStart && $positionMapStart < $positionEnd)
          $string = substr_replace($string, "", $positionStart - 1, ($positionEnd + 10) - $positionStart);
      }
    }
    return $string;
  }

  public function allowVideo($subject_list, $viewer) {

    $allowed_upload_videoEnable = $this->enableVideoPlugin();
    if (empty($allowed_upload_videoEnable))
      return false;
    $viewer_id = $viewer->getIdentity();

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = Engine_Api::_()->user()->getViewer()->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    $allowed_upload_video_video = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');
    if (empty($allowed_upload_video_video))
      return false;

    $allowed_upload_video = Engine_Api::_()->authorization()->isAllowed($subject_list, $viewer, 'video');
    if (empty($allowed_upload_video))
      return false;

    return true;
  }

  public function listing_Like($resourceType, $resourceId) {

    $LIMIT = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.listinglike.view', 3);
    $sub_status_table = Engine_Api::_()->getItemTable('core_like');
    $sub_status_name = $sub_status_table->info('name');
    $sub_status_select = $sub_status_table->select()
            ->from($sub_status_name, array('poster_id'))
            ->where('resource_type = ?', $resourceType)
            ->where('resource_id = ?', $resourceId)
            ->order('like_id DESC')
            ->limit($LIMIT);
    $fetch_sub = $sub_status_select->query()->fetchAll();
    return $fetch_sub;
  }

  /**
   * Check widget is exist or not
   *
   */
  public function existWidget($widget = '', $identity = 0) {

    //GET CONTENT TABLE
    $tableContent = Engine_Api::_()->getDbtable('content', 'core');
    $tableContentName = $tableContent->info('name');

    //GET PAGE TABLE
    $tablePage = Engine_Api::_()->getDbtable('pages', 'core');
    $tablePageName = $tablePage->info('name');

    if ($widget == 'browse_categories') {

      //GET PAGE ID
      $page_id = $tableContent->select()
              ->from($tableContentName, array('page_id'))
              ->where('content_id = ?', $identity)
              ->where('name = ?', 'list.categories-list')
              ->query()
              ->fetchColumn();

      if (empty($page_id)) {
        return false;
      }

      //GET CONTENT ID
      $content_id = $tableContent->select()
              ->from($tableContentName, array('content_id'))
              ->where('page_id = ?', $page_id)
              ->where('name = ?', 'list.search-list')
              ->query()
              ->fetchColumn();

      return $content_id;
    } elseif ($widget == 'list_reviews') {
      //GET PAGE ID
      $page_id = $tablePage->select()
              ->from($tablePageName, array('page_id'))
              ->where('name = ?', 'list_index_view')
              ->query()
              ->fetchColumn();

      if (empty($page_id)) {
        return 0;
      }

      $content_id = $tableContent->select()
              ->from($tableContent->info('name'), array('content_id'))
              ->where('page_id = ?', $page_id)
              ->where('name = ?', 'list.review-list')
              ->query()
              ->fetchColumn();

      return $content_id;
    }
  }

  /**
   * Get videos according to search
   *
   */
  public function getAutoSuggestedVideo($params = null) {

    //MAKE QUERY
    $tableVideo = Engine_Api::_()->getDbtable('videos', 'video');
    $tableVideoName = $tableVideo->info('name');
    $select = $tableVideo->select()
            ->where('title  LIKE ? ', '%' . $params['text'] . '%')
            ->where('owner_id = ?', $params['viewer_id'])
            ->where('status = ?', 1)
            ->order('title ASC')
            ->limit($params['limit']);

    //RETURN RESULTS
    return $tableVideo->fetchAll($select);
  }

  /**
   * Plugin which return the error, if Siteadmin not using correct version for the plugin.
   *
   */
  public function isModulesSupport() {
    $modArray = array(
        'suggestion' => '4.2.3',
    );
    $finalModules = array();
    foreach ($modArray as $key => $value) {
      $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
      if (!empty($isModEnabled)) {
        $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
        $isModSupport = strcasecmp($getModVersion->version, $value);
        if ($isModSupport < 0) {
          $finalModules[] = $getModVersion->title;
        }
      }
    }
    return $finalModules;
  }

  /**
   * Expiry Enable setting
   *
   * @return bool
   */
  public function expirySettings() {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    return $settings->getSetting('list.expirydate.enabled', 0);
  }

  public function adminExpiryDuration() {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $duration = $settings->getSetting('list.expirydate.duration', array('1', 'week'));
    $interval_type = $duration[1];
    $interval_value = $duration[0];
    $part = 1;
    $interval_value = empty($interval_value) ? 1 : $interval_value;
    $rel = time();
    // Calculate when the next payment should be due

    switch ($interval_type) {
      case 'day':
        $part = Zend_Date::DAY;
        break;
      case 'week':
        $part = Zend_Date::WEEK;
        break;
      case 'month':
        $part = Zend_Date::MONTH;
        break;
      case 'year':
        $part = Zend_Date::YEAR;
        break;
    }

    $relDate = new Zend_Date($rel);
    $relDate->sub((int) $interval_value, $part);

    return date("Y-m-d i:s:m", $relDate->toValue());
  }

}