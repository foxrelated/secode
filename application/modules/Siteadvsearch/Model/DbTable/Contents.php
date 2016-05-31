<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Contents.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Model_DbTable_Contents extends Engine_Db_Table {

  protected $_name = 'siteadvsearch_contents';
  protected $_rowClass = 'Siteadvsearch_Model_Content';

  /**
   * Get content list
   *
   * @param int $order
   * @param int $enabledIcon
   * @return array $paginator;
   */
  public function getContenListPaginator($order, $enabledIcon = 0) {

    $paginator = Zend_Paginator::factory($this->getContentListSelect($order, $enabledIcon));
    return $paginator;
  }

  /**
   * Get content select query
   *
   * @param array $params
   * @return string $selectTable;
   */
  public function getContentListSelect($order, $enabledIcon) {

    $moduleTableName = Engine_Api::_()->getDbtable('modules', 'core')->info('name');
    $searchContentTableName = $this->info('name');

    $select = $this->select()->setIntegrityCheck(false);
    if (!empty($order))
      $select->from($searchContentTableName, array('resource_type'));
    else
      $select->from($searchContentTableName, array('resource_type', 'resource_title', 'content_id', 'content_tab', 'default', 'main_search', 'listingtype_id', 'file_id', 'enabled', 'listingtype_id'));
    $select->join($moduleTableName, "$searchContentTableName.module_name = $moduleTableName.name", array("$moduleTableName.title as module_title"));
    $select->where($moduleTableName . '.enabled  = ?', 1);
    if (!empty($enabledIcon))
      $select->where($searchContentTableName . '.main_search  = ?', 1);
    $select->group($searchContentTableName . '.resource_type')
            ->order($searchContentTableName . '.order ASC');
    return $select;
  }

  /**
   * Get content type list for user
   * @param int $showDefaultContent
   * @return string $items;
   */
  public function getContentTypes($showDefaultContent = 0) {

    $moduleTableName = Engine_Api::_()->getDbtable('modules', 'core')->info('name');
    $searchContentTableName = $this->info('name');

    $select = $this->select()->setIntegrityCheck(false);
    if ($showDefaultContent != 1)
      $select->from($searchContentTableName, array('resource_type', 'resource_title', 'file_id', 'listingtype_id'));
    else
      $select->from($searchContentTableName, array('resource_type', 'listingtype_id'));

    $select->join($moduleTableName, "$searchContentTableName.module_name = $moduleTableName.name", array($moduleTableName . '.name'))
            ->where($searchContentTableName . '.content_tab =?', 1)
            ->where($moduleTableName . '.enabled =?', 1)
            ->group($searchContentTableName . '.resource_type')
            ->order($searchContentTableName . '.order ASC');
    if ($showDefaultContent == 1 || $showDefaultContent == 2)
      $select->where($searchContentTableName . '.main_search =?', 1);
    elseif ($showDefaultContent == 4) {
      $select
              ->where($searchContentTableName . '.content_tab =?', 1)
              ->where($searchContentTableName . '.main_search =?', 1);
    }
    return $items = $this->fetchAll($select);
  }

  /**
   * Get defualt module list
   *
   * @return string $select;
   */
  public function getIncludedModules() {

    $select = $this->select()
            ->from($this->info('name'), "resource_type");
    return $select->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
  }

  /**
   * Get title of content type
   * @param string $itemType
   * @param int $listingTypeId
   * @return string $items;
   */
  public function getResourceTitle($itemType, $listingTypeId) {

    if ($itemType == 'sitereview_listing')
      $itemType = 'sitereview_listing_' . $listingTypeId;
    return $resourceTitle = $this->select()
            ->from($this->info('name'), 'resource_title')
            ->where('resource_type = ?', $itemType)
            ->where('content_tab = ?', 1)
            ->query()
            ->fetchColumn();
  }

  public function iconUpload() {

    //MAKE DIRECTORY IN PUBLIC FOLDER
    @mkdir(APPLICATION_PATH . "/temporary/siteadvsearch_search_icons", 0777, true);

    //COPY THE ICONS IN NEWLY CREATED FOLDER
    $dir = APPLICATION_PATH . "/application/modules/Siteadvsearch/externals/images/icons";
    $public_dir = APPLICATION_PATH . "/temporary/siteadvsearch_search_icons";

    if (is_dir($dir) && is_dir($public_dir)) {
      $files = scandir($dir);
      foreach ($files as $file) {
        if (strstr($file, '.png')) {
          @copy(APPLICATION_PATH . "/application/modules/Siteadvsearch/externals/images/icons/$file", APPLICATION_PATH . "/temporary/siteadvsearch_search_icons/$file");
        }
      }
      @chmod(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons', 0777);
    }

    //MAKE QUERY
    $select = $this->select()->from($this->info('name'), array('content_id', 'resource_title', 'file_id'));
    $contentTypes = $this->fetchAll($select);

    //UPLOAD DEFAULT ICONS
    foreach ($contentTypes as $contentType) {
      $contentTypeName = $contentType->resource_title;
      $iconName = $contentTypeName . '.png';

      @chmod(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons', 0777);

      $file = array();
      $file['tmp_name'] = APPLICATION_PATH . "/temporary/siteadvsearch_search_icons/$iconName";
      $file['name'] = $iconName;

      if (file_exists($file['tmp_name'])) {
        $name = basename($file['tmp_name']);
        $path = dirname($file['tmp_name']);
        $mainName = $path . '/' . $file['name'];

        @chmod($mainName, 0777);

        $photo_params = array(
            'parent_id' => $contentType->content_id,
            'parent_type' => "siteadvsearch_content",
        );

        //RESIZE IMAGE WORK
        $image = Engine_Image::factory();
        $image->open($file['tmp_name']);
        $image->open($file['tmp_name'])
                ->resample(0, 0, $image->width, $image->height, $image->width, $image->height)
                ->write($mainName)
                ->destroy();

        $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);

        //UPDATE FILE ID IN CATEGORY TABLE
        if (!empty($photoFile->file_id)) {
          $contentType = Engine_Api::_()->getItem('siteadvsearch_content', $contentType->content_id);
          $contentType->file_id = $photoFile->file_id;
          $contentType->save();
        }
      }
    }

    //REMOVE THE CREATED PUBLIC DIRECTORY
    if (is_dir(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons')) {
      $files = scandir(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons');
      foreach ($files as $file) {
        $is_exist = file_exists(APPLICATION_PATH . "/temporary/siteadvsearch_search_icons/$file");
        if ($is_exist) {
          @unlink(APPLICATION_PATH . "/temporary/siteadvsearch_search_icons/$file");
        }
      }
      @rmdir(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons');
    }
  }

}