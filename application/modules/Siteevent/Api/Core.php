<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventsiteevent.recently-popular-random-siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Api_Core extends Core_Api_Abstract {

    const IMAGE_WIDTH = 1600;
    const IMAGE_HEIGHT = 1600;
    const THUMB_WIDTH = 140;
    const THUMB_HEIGHT = 160;
    const THUMB_LARGE_WIDTH = 250;
    const THUMB_LARGE_HEIGHT = 250;

    protected $_getOccurrencesEventViews = 1872;
    protected $_getOccurrencesEventViewCount = 2651;
    protected $_getOccurrencesEventTypeCount = 892623;

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
            $thumbLargeName = $path . '/t_l_' . $name . '.' . $extension;

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
            $image = Engine_Image::factory();
            $image->open($file['tmp_name'])
                    ->resize(self::THUMB_LARGE_WIDTH, self::THUMB_LARGE_HEIGHT)
                    ->write($thumbLargeName)
                    ->destroy();

            //RESIZE IMAGE (ICON)
            $iSquarePath = $path . '/is_' . $name . '.' . $extension;
            $image = Engine_Image::factory();
            $image->open($file['tmp_name']);

            $size = min($image->height, $image->width);
            $x = ($image->width - $size) / 2;
            $y = ($image->height - $size) / 2;

            $image->resample($x, $y, $size, $size, 48, 48)
                    ->write($iSquarePath)
                    ->destroy();

            //STORE PHOTO
            $photo_params = array(
                'parent_id' => $params['event_id'],
                'parent_type' => 'siteevent_event',
            );

            $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
            $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
            $photoFile->bridge($thumbFile, 'thumb.normal');

            $thumbLargeFile = Engine_Api::_()->storage()->create($thumbLargeName, $photo_params);
            $photoFile->bridge($thumbLargeFile, 'thumb.large');

            $iSquare = Engine_Api::_()->storage()->create($iSquarePath, $photo_params);
            $photoFile->bridge($iSquare, 'thumb.icon');
            $params['file_id'] = $photoFile->file_id;
            $params['photo_id'] = $photoFile->file_id;

            //REMOVE TEMP FILES
            @unlink($mainName);
            @unlink($thumbName);
            @unlink($thumbLargeName);
            @unlink($iSquarePath);
        }

        $row = Engine_Api::_()->getDbtable('photos', 'siteevent')->createRow();
        $row->setFromArray($params);
        $row->save();

        return $row;
    }

    //FUNCTION FOR SHOWING 'LIKED LINK'
    public function check_availability($resourceType, $resourceId) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $sub_status_table = Engine_Api::_()->getItemTable('core_like');
        $columns = array('like_id');

        $sub_status_name = $sub_status_table->info('name');
        $sub_status_select = $sub_status_table->select()
                ->from($sub_status_name, $columns)
                ->where('resource_type = ?', $resourceType)
                ->where('resource_id = ?', $resourceId)
                ->where('poster_type = ?', $viewer->getType())
                ->where('poster_id = ?', $viewer->getIdentity())
                ->query()
                ->fetchColumn();

        return $sub_status_select;
    }

    //CHECK VIDEO PLUGIN ENABLE / DISABLE
    public function enableVideoPlugin() {

        $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
        if ($sitevideoEnabled && (Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => "siteevent_event", 'item_module' => 'siteevent')))) {
            return true;
        } else {
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video', 1)) {
                return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video');
            } else {
                return 1;
            }
        }
    }

    //CHECK DOCUMENT PLUGIN ENABLE / DISABLE
    public function enableDocumentPlugin() {

        $documentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document');
        if ($documentEnabled && (Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => "siteevent_event", 'item_module' => 'siteevent')))) {
            return true;
        } else {
            return 1;
        }
    }

    /**
     * Page base network enable
     *
     * @return bool
     */
    public function pageBaseNetworkEnable() {

        return (bool) ( (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.network', 0) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.default.show', 0)));
    }

    //APROVED/ DISAPROVED EMAIL NOTIFICATION FOR CLASSIFEID
    public function aprovedEmailNotification(Core_Model_Item_Abstract $object, $params = array()) {

        $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $object->event_id);
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($params['mail_id'], 'SITEEVENT_APPROVED_EMAIL_NOTIFICATION', array(
            'host' => $_SERVER['HTTP_HOST'],
            'subject' => $params['subject'],
            'title' => $params['title'],
            'message' => $params['message'],
            'object_link' => $siteevent->getHref(),
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
    public function enableLocation() {

        return Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1);
    }

    public function friend_number_of_like($resourceType, $resourceId) {

        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $sub_status_table = Engine_Api::_()->getItemTable('core_like');
        $sub_status_name = $sub_status_table->info('name');
        $membership_table = Engine_Api::_()->getDbtable('membership', 'user');
        $member_name = $membership_table->info('name');
        $fetch_count = $sub_status_table->select()
                ->from($sub_status_name, array('COUNT(' . $sub_status_name . '.like_id) AS like_count'))
                ->joinInner($member_name, "$member_name . user_id = $sub_status_name . poster_id", NULL)
                ->where($member_name . '.resource_id = ?', $user_id)
                ->where($member_name . '.active = ?', 1)
                ->where($sub_status_name . '.resource_type = ?', $resourceType)
                ->where($sub_status_name . '.resource_id = ?', $resourceId)
                ->where($sub_status_name . '.poster_id != ?', $user_id)
                ->where($sub_status_name . '.poster_id != ?', 0)
                ->group($sub_status_name . '.resource_id')
                ->query()
                ->fetchColumn();

        if (!empty($fetch_count)) {
            return $fetch_count;
        } else {
            return 0;
        }
    }

    public function number_of_like($resourceType, $resourceId) {

        //GET THE VIEWER (POSTER) AND RESOURCE.
        $poster = Engine_Api::_()->user()->getViewer();
        $resource = Engine_Api::_()->getItem($resourceType, $resourceId);
        return Engine_Api::_()->getDbtable('likes', 'core')->getLikeCount($resource, $poster);
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

    public function allowVideo($subject_siteevent, $viewer, $counter = 0, $uploadVideo = 0) {

        $allowed_upload_videoEnable = $this->enableVideoPlugin();
        if (empty($allowed_upload_videoEnable))
            return false;

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video', 1)) {

            //GET USER LEVEL ID
            $viewer_id = $viewer->getIdentity();
            if (!empty($viewer_id)) {
                $level_id = Engine_Api::_()->user()->getViewer()->level_id;
            } else {
                $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
            }

            //CHECK FOR SOCIAL ENGINE CORE VIDEO PLUGIN
            $allowed_upload_video_video = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');
            if (empty($allowed_upload_video_video))
                return false;
        }

        $allowed_upload_video = Engine_Api::_()->authorization()->isAllowed($subject_siteevent, $viewer, "video");
        if (Engine_Api::_()->siteevent()->hasPackageEnable()) {
            if (Engine_Api::_()->siteeventpaid()->allowPackageContent($subject_siteevent->package_id, "video")) {
                $videoCount = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getPackageOption($subject_siteevent->package_id, 'video_count');
                if (empty($videoCount))
                    $allowed_upload_video = 1;
                elseif ($videoCount > $counter)
                    $allowed_upload_video = 1;
                else
                    $allowed_upload_video = 0;
            } else
                $allowed_upload_video = 0;
        }
        if (empty($allowed_upload_video))
            return false;

        return true;
    }

    public function event_Like($resourceType, $resourceId) {

        $LIMIT = 3;
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

        if ($widget == 'siteevent_reviews') {
            //GET PAGE ID
            $page_id = $tablePage->select()
                    ->from($tablePageName, array('page_id'))
                    ->where('name = ?', "siteevent_index_view")
                    ->query()
                    ->fetchColumn();

            if (empty($page_id)) {
                return 0;
            }

            $content_id = $tableContent->select()
                    ->from($tableContent->info('name'), array('content_id'))
                    ->where('page_id = ?', $page_id)
                    ->where('name = ?', 'siteevent.user-siteevent')
                    ->query()
                    ->fetchColumn();

            return $content_id;
        } elseif ($widget == 'siteevent.profile-announcements-siteevent') {
            //GET PAGE ID
            $page_id = $tablePage->select()
                    ->from($tablePageName, array('page_id'))
                    ->where('name = ?', "siteevent_index_view")
                    ->query()
                    ->fetchColumn();

            if (empty($page_id)) {
                return 0;
            }

            $content_id = $tableContent->select()
                    ->from($tableContent->info('name'), array('content_id'))
                    ->where('page_id = ?', $page_id)
                    ->where('name = ?', 'siteevent.profile-announcements-siteevent')
                    ->query()
                    ->fetchColumn();

            return $content_id;
        } elseif ($widget == 'editor_reviews_siteevent') {
            //GET PAGE ID
            $page_id = $tablePage->select()
                    ->from($tablePageName, array('page_id'))
                    ->where('name = ?', "siteevent_index_view")
                    ->query()
                    ->fetchColumn();

            if (empty($page_id)) {
                return 0;
            }

            $content_id = $tableContent->select()
                    ->from($tableContent->info('name'), array('content_id'))
                    ->where('page_id = ?', $page_id)
                    ->where('name = ?', 'siteevent.editor-reviews-siteevent')
                    ->query()
                    ->fetchColumn();

            return $content_id;
        } elseif ($widget == 'siteevent_view_reviews') {
            //GET PAGE ID
            $page_id = $tablePage->select()
                    ->from($tablePageName, array('page_id'))
                    ->where('name = ?', "siteevent_review_view")
                    ->query()
                    ->fetchColumn();

            if (empty($page_id)) {
                return 0;
            }

            $content_id = $tableContent->select()
                    ->from($tableContent->info('name'), array('content_id'))
                    ->where('page_id = ?', $page_id)
                    ->where('name = ?', 'siteevent.profile-review-siteevent')
                    ->query()
                    ->fetchColumn();

            return $content_id;
        } elseif ($widget == 'occurrences') {
            //GET PAGE ID
            $page_id = $tablePage->select()
                    ->from($tablePageName, array('page_id'))
                    ->where('name = ?', "siteevent_index_view")
                    ->query()
                    ->fetchColumn();

            if (empty($page_id)) {
                return 0;
            }

            $content_id = $tableContent->select()
                    ->from($tableContent->info('name'), array('content_id'))
                    ->where('page_id = ?', $page_id)
                    ->where('name = ?', 'siteeventrepeat.occurrences')
                    ->query()
                    ->fetchColumn();

            return $content_id;
        }
    }

    public function getWidgetInfo($widgetName = '', $content_id = 0, $page_id = 0) {

        //GET CONTENT TABLE
        $tableContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableContentName = $tableContent->info('name');

        //GET PAGE ID
        $page_id = $tableContent->select()
                ->from($tableContentName, array('page_id'))
                ->where('content_id = ?', $content_id)
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {
            return null;
        }

        //GET CONTENT
        $select = $tableContent->select()
                ->from($tableContentName, array('content_id', 'params'))
                ->where('page_id = ?', $page_id)
                ->where('name = ?', $widgetName);

        return $tableContent->fetchRow($select);
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
    public function isModulesSupport($modName = null) {
        if (empty($modName)) {
            $modArray = array(
                'sitestore' => '4.8.8p3',
                'sitepage' => '4.8.8p1',
                'sitebusiness' => '4.8.8p1',
                'sitegroup' => '4.8.8p1',
                'communityad' => '4.7.1',
                'communityadsponsored' => '4.7.1',
                'suggestion' => '4.7.1',
                'advancedactivity' => '4.7.1',
                'sitevideoview' => '4.7.1',
                'facebookse' => '4.7.1',
                'facebooksefeed' => '4.7.1',
                'sitetagcheckin' => '4.7.1',
                'sitecontentcoverphoto' => '4.7.1',
                'sitelike' => '4.7.1',
                'sitemailtemplates' => '4.7.1',
                'sitereview' => '4.7.1p2',
                'sitereviewlistingtype' => '4.7.1p2'
            );
        } else {
            $modArray[$modName['modName']] = $modName['version'];
        }
        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
            if (!empty($isModEnabled)) {
                $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
                $isModSupport = $this->checkVersion($getModVersion->version, $value);
                if (empty($isModSupport)) {
                    $finalModules[] = $getModVersion->title;
                }
            }
        }
        return $finalModules;
    }

    public function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }

    /**
     * Return array for prefield star's and rectangles
     *
     * @param array $post_data
     * @return Zend_Db_Table_Select
     */
    public function prefieldRatingData($post_data) {

        //SHOW PRE-FIELD THE RATINGS IF OVERALL RATING IS EMPTY
        $reviewRateData = array();
        foreach ($post_data as $key => $ratingdata) {
            $string_exist = strstr($key, 'review_rate_');
            if ($string_exist) {
                $ratingparam_id = explode('review_rate_', $key);
                $reviewRateData[$ratingparam_id[1]]['ratingparam_id'] = $ratingparam_id[1];
                $reviewRateData[$ratingparam_id[1]]['rating'] = $ratingdata;
            }
        }

        return $reviewRateData;
    }

    /**
     * Show rating stars and rectangles
     *
     * @param float rating
     * @param string image_type
     * @return Zend_Db_Table_Select
     */
    public function showRatingImage($rating = 0, $image_type = 'star') {

        switch ($rating) {
            case 0:
                $rating_value = '';
                break;
            case $rating < .5:
                $rating_value = '';
                $rating_valueTitle = 0;
                break;
            case $rating < 1:
                $rating_value = 'halfstar';
                $rating_valueTitle = .5;
                break;
            case $rating < 1.5:
                $rating_value = 'onestar';
                $rating_valueTitle = 1;
                break;
            case $rating < 2:
                $rating_value = 'onehalfstar';
                $rating_valueTitle = 1.5;
                break;
            case $rating < 2.5:
                $rating_value = 'twostar';
                $rating_valueTitle = 2;
                break;
            case $rating < 3:
                $rating_value = 'twohalfstar';
                $rating_valueTitle = 2.5;
                break;
            case $rating < 3.5:
                $rating_value = 'threestar';
                $rating_valueTitle = 3;
                break;
            case $rating < 4:
                $rating_value = 'threehalfstar';
                $rating_valueTitle = 3.5;
                break;
            case $rating < 4.5:
                $rating_value = 'fourstar';
                $rating_valueTitle = 4;
                break;
            case $rating < 5:
                $rating_value = 'fourhalfstar';
                $rating_valueTitle = 4.5;
                break;
            case $rating >= 5:
                $rating_value = 'fivestar';
                $rating_valueTitle = 5;
                break;
        }
        if ($image_type != 'star') {
            $rating_value .='-small-box';
            $rating_valueTitle = null;
        }

        $showRatingImage = array();
        $showRatingImage['rating_value'] = $rating_value;
        $showRatingImage['rating_valueTitle'] = $rating_valueTitle;

        return $showRatingImage;
    }

    /**
     * Return video
     *
     * @param string $params
     * @param int $type_video
     * @return video
     */
    public function GetEventVideo($params = array(), $type_video = null) {

        // MAKE QUERY
        if ($type_video && isset($params['corevideo_id'])) {
            $main_video_id = $params['corevideo_id'];
            $videoTable = Engine_Api::_()->getDbtable('videos', 'video');
            $select = $videoTable->select()
                    ->where('status = ?', 1)
                    ->where('video_id = ?', $main_video_id);
            return $videoTable->fetchRow($select);
        } elseif (isset($params['reviewvideo_id'])) {
            $main_video_id = $params['reviewvideo_id'];
            $reviewvideoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');
            $select = $reviewvideoTable->select()
                    ->where('status = ?', 1)
                    ->where('video_id = ?', $main_video_id);
            return $reviewvideoTable->fetchRow($select);
        }
    }

    /**
     * Get siteevent tags created by users
     * @param int $owner_id : siteevent owner id
     * @param int $total_tags : number tags to show
     */
    public function getTags($owner_id = 0, $total_tags = 100, $count_only = 0, $params = array()) {

        //GET DOCUMENT TABLE
        $tableSiteevent = Engine_Api::_()->getDbtable('events', 'siteevent');
        $tableSiteeventName = $tableSiteevent->info('name');

        //MAKE QUERY
        $select = $tableSiteevent->select()
                ->setIntegrityCheck(false)
                ->from($tableSiteeventName, array("event_id"))
                ->where($tableSiteeventName . '.approved = ?', '1')
                ->where($tableSiteeventName . '.draft = ?', '0')
                ->where($tableSiteeventName . ".search = ?", 1)
                ->where($tableSiteeventName . '.closed = ?', '0');
        if (!empty($owner_id)) {
            $select->where($tableSiteeventName . '.owner_id = ?', $owner_id);
        }


        if (isset($params['eventType']) && !empty($params['eventType']) && $params['eventType'] == 'upcoming') {
            $siteeventOccurTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
            $siteeventOccurTableName = $siteeventOccurTable->info('name');
            $select->joinInner($siteeventOccurTableName, "$tableSiteeventName.event_id = $siteeventOccurTableName.event_id", array());
            $select->where("$siteeventOccurTableName.endtime >" . time());
        }
        $select->distinct(true);

        $select = $tableSiteevent->getNetworkBaseSql($select, array('not_groupBy' => 1));

        $eventIds = $select
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        if (empty($eventIds)) {
            return;
        }


        $tableTagMaps = Engine_Api::_()->getDbtable('tagMaps', 'core');
        $tableTagMapsName = $tableTagMaps->info('name');

        //GET TAG TABLE NAME
        $tableTags = 'engine4_core_tags';


        //MAKE QUERY
        $select = $tableTagMaps->select()
                ->setIntegrityCheck(false)
                ->from($tableTagMapsName, array("COUNT($tableTagMapsName.resource_id) AS Frequency"))
                ->joinInner($tableTags, "$tableTags.tag_id = $tableTagMapsName.tag_id", array('text', 'tag_id'))
                ->where($tableTagMapsName . '.resource_type = ?', 'siteevent_event')
                ->where($tableTagMapsName . '.resource_id IN(?)', (array) $eventIds)
                ->group("$tableTags.text");

        if (isset($params['orderingType']) && !empty($params['orderingType']))
            $select->order("$tableTags.text");
        else
            $select->order("Frequency DESC");

        if (!empty($total_tags)) {
            $select = $select->limit($total_tags);
        }

        if (!empty($count_only)) {
            $total_results = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            return Count($total_results);
        }

        //RETURN RESULTS
        return $select->query()->fetchAll();
    }

    /**
     * Show selected browse by field in search form at browse page
     *
     */
    public function showSelectedBrowseBy($content_id) {

        //GET CORE CONTENT TABLE
        $coreContentTable = Engine_Api::_()->getDbTable('content', 'core');
        $coreContentTableName = $coreContentTable->info('name');

        $page_id = $coreContentTable->select()
                ->from($coreContentTableName, array('page_id'))
                ->where('content_id = ?', $content_id)
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {
            return 0;
        }

        //GET DATA
        $params = $coreContentTable->select()
                ->from($coreContentTableName, array('params'))
                ->where($coreContentTableName . '.page_id = ?', $page_id)
                ->where($coreContentTableName . '.name = ?', 'siteevent.browse-events-siteevent')
                ->query()
                ->fetchColumn();

        $paramsArray = Zend_Json::decode($params);

        if (isset($paramsArray['orderby']) && !empty($paramsArray['orderby'])) {
            return $paramsArray['orderby'];
        } else {
            return 0;
        }
    }

    /**
     * Page base network enable
     *
     * @return bool
     */
    public function listBaseNetworkEnable() {

        $settings = Engine_Api::_()->getApi('settings', 'core');

        return (bool) ( $settings->getSetting('siteevent.networks.type', 0) && ($settings->getSetting('siteevent.network', 0) || $settings->getSetting('siteevent.default.show', 0)));
    }

    public function isUpload() {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $imageUpload = 1;
        $isReturn = empty($imageUpload) ? "<a href='javascript:void(0);' onclick='javascript:void(0);'>" . $view->translate("here") . '</a>' : "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>" . $view->translate("here") . '</a>';

        return $isReturn;
    }

    /**
     * Return a video
     *
     * @param array $params
     * @param array $file
     * @param array $values
     * @return video object
     * */
    public function createSiteeventvideo($params, $file, $values) {

        if ($file instanceof Storage_Model_File) {
            $params['file_id'] = $file->getIdentity();
        } else {
            //CREATE VIDEO ITEM
            $video = Engine_Api::_()->getDbtable('videos', 'siteevent')->createRow();
            $file_ext = pathinfo($file['name']);
            $file_ext = $file_ext['extension'];
            $video->code = $file_ext;
            $video->save();

            //STORE VIDEO IN TEMPORARY STORAGE OBJECT FOR FFMPEG TO HANDLE
            $storage = Engine_Api::_()->getItemTable('storage_file');
            $storageObject = $storage->createFile($file, array(
                'parent_id' => $video->getIdentity(),
                'parent_type' => $video->getType(),
                'user_id' => $video->owner_id,
            ));

            //REMOVE TEMPORARY FILE
            @unlink($file['tmp_name']);

            $video->file_id = $storageObject->file_id;
            $video->save();

            //ADD TO JOBS
            $html5 = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.video.html5', false);
            Engine_Api::_()->getDbtable('jobs', 'core')->addJob('siteevent_video_encode', array(
                'video_id' => $video->getIdentity(),
                'type' => $html5 ? 'mp4' : 'flv',
            ));
        }
        return $video;
    }

    public function getTabId($widgetName = null, $pageName = "siteevent_index_view") {


        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            //GET PAGE OBJECT
            $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
            $pageSelect = $pageTable->select()->where('name = ?', $pageName);
            $page_id = $pageTable->fetchRow($pageSelect)->page_id;

            if (empty($page_id))
                return false;

            //GET CONTENT TABLE
            $tableContent = Engine_Api::_()->getDbtable('content', 'core');
            $tableContentName = $tableContent->info('name');

            //GET MAIN CONTAINER 
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('page_id =?', $page_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'main')
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return false;

            //GET MIDDLE CONTAINER 
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'middle')
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return false;

            //GET CORE CONTAINER TAB
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'core.container-tabs')
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return false;

            //GET PAGE ID
            if ($widgetName) {
                $content_id = $tableContent->select()
                        ->from($tableContentName, array('content_id'))
                        ->where('type = ?', 'widget')
                        ->where('name = ?', $widgetName)
                        ->where('parent_content_id = ?', $content_id)
                        ->query()
                        ->fetchColumn();

                return $content_id;
            }

            return true;
        } else {
            $modulename = Engine_Api::_()->seaocore()->isSitemobileApp() ? "sitemobileapp" : 'sitemobile';
            if (Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
                $pageTable = Engine_Api::_()->getDbtable('pages', $modulename);
                $tableContent = Engine_Api::_()->getDbtable('content', $modulename);
            } else {
                $pageTable = Engine_Api::_()->getDbtable('tabletpages', $modulename);
                $tableContent = Engine_Api::_()->getDbtable('tabletcontent', $modulename);
            }

            $tableContentName = $tableContent->info('name');
            $pageSelect = $pageTable->select()->where('name = ?', "siteevent_index_view");
            $page_id = $pageTable->fetchRow($pageSelect)->page_id;

            if (empty($page_id))
                return null;

            //GET MAIN CONTAINER 
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('page_id =?', $page_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'main')
                    ->query()
                    ->fetchColumn();
            //GET MIDDLE CONTAINER 
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'middle')
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return null;

            //GET CORE CONTAINER TAB
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'sitemobile.container-tabs-columns')
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return null;

            //GET PAGE ID
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'widget')
                    ->where('name = ?', $widgetName)
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();
            return $content_id;
        }
    }

    /**
     * Set Meta Keywords
     *
     * @param array $params
     */
    public function setMetaKeywords($params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $siteinfo = $view->layout()->siteinfo;
        $keywords = "";

        if (isset($params['page']) && $params['page'] == 'browse') {

            if (isset($params['subsubcategoryname_keywords']) && !empty($params['subsubcategoryname_keywords'])) {
                if (!empty($keywords))
                    $keywords .= ', ';
                $keywords .= $params['subsubcategoryname_keywords'];
            }

            if (isset($params['subcategoryname_keywords']) && !empty($params['subcategoryname_keywords'])) {
                if (!empty($keywords))
                    $keywords .= ', ';
                $keywords .= $params['subcategoryname_keywords'];
            }

            if (isset($params['categoryname_keywords']) && !empty($params['categoryname_keywords'])) {
                if (!empty($keywords))
                    $keywords .= ', ';
                $keywords .= $params['categoryname_keywords'];
            }
        } else {

            if (isset($params['subsubcategoryname']) && !empty($params['subsubcategoryname'])) {
                if (!empty($keywords))
                    $keywords .= ', ';
                $keywords .= $params['subsubcategoryname'];
            }

            if (isset($params['subcategoryname']) && !empty($params['subcategoryname'])) {
                if (!empty($keywords))
                    $keywords .= ', ';
                $keywords .= $params['subcategoryname'];
            }

            if (isset($params['categoryname']) && !empty($params['categoryname'])) {
                if (!empty($keywords))
                    $keywords .= ', ';
                $keywords .= $params['categoryname'];
            }
        }

        if (isset($params['location']) && !empty($params['location'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['location'];
        }

        if (isset($params['tag']) && !empty($params['tag'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['tag'];
        }

        if (isset($params['search'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['search'];
        }

        if (isset($params['keywords'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['keywords'];
        }

        if (isset($params['diary_creator_name'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['diary_creator_name'];
        }

        if (isset($params['diary'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['diary'];
        }

        if (isset($params['displayname'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['displayname'];
        }

        if (isset($params['event_type_title'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['event_type_title'];
        }

        if (isset($params['event_title'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['event_title'];
        }

        $siteinfo['keywords'] = $keywords;
        $view->layout()->siteinfo = $siteinfo;
    }

    /**
     * Set Meta Titles
     *
     * @param array $params
     */
    public function setMetaTitles($params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $siteinfo = $view->layout()->siteinfo;
        $titles = $siteinfo['title'];

        if (isset($params['subsubcategoryname']) && !empty($params['subsubcategoryname'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['subsubcategoryname'];
        }

        if (isset($params['subcategoryname']) && !empty($params['subcategoryname'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['subcategoryname'];
        }

        if (isset($params['categoryname']) && !empty($params['categoryname'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['categoryname'];
        }

        if (isset($params['location']) && !empty($params['location'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['location'];
        }

        if (isset($params['tag']) && !empty($params['tag'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['tag'];
        }

        if (isset($params['diary_creator_name'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['diary_creator_name'];
        }

        if (isset($params['default_title'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['default_title'];
        }

        if (isset($params['dashboard'])) {
            if (isset($params['event_type_title'])) {
                if (!empty($titles))
                    $titles .= ' - ';
                $titles .= $params['event_type_title'];
            }

            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['dashboard'];
        }

        $siteinfo['title'] = $titles;
        $view->layout()->siteinfo = $siteinfo;
    }

    /**
     * Set Meta Description
     *
     * @param array $params
     */
    public function setMetaDescriptionsBrowse($params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $siteinfo = $view->layout()->siteinfo;
        $descriptions = '';
        if (isset($params['description'])) {
            $descriptions .= $params['description'];
            $descriptions .= ' -';
        }

        $siteinfo['description'] = $descriptions;
        $view->layout()->siteinfo = $siteinfo;
    }

    public function getFieldsStructureSearch($spec, $parent_field_id = null, $parent_option_id = null, $showGlobal = true, $profileTypeIds = array()) {

        $fieldsApi = Engine_Api::_()->getApi('core', 'fields');

        $type = $fieldsApi->getFieldType($spec);

        $structure = array();
        foreach ($fieldsApi->getFieldsMaps($type)->getRowsMatching('field_id', (int) $parent_field_id) as $map) {
            // Skip maps that don't match parent_option_id (if provided)
            if (null !== $parent_option_id && $map->option_id != $parent_option_id) {
                continue;
            }

            //FETCHING THE FIELDS WHICH BELONGS TO SOME SPECIFIC LISTNIG TYPE
            if ($parent_field_id == 1 && !empty($profileTypeIds) && !in_array($map->option_id, $profileTypeIds)) {
                continue;
            }

            // Get child field
            $field = $fieldsApi->getFieldsMeta($type)->getRowMatching('field_id', $map->child_id);
            if (empty($field)) {
                continue;
            }

            // Add to structure
            if ($field->search) {
                $structure[$map->getKey()] = $map;
            }

            // Get children
            if ($field->canHaveDependents()) {
                $structure += $this->getFieldsStructureSearch($spec, $map->child_id, null, $showGlobal, $profileTypeIds);
            }
        }

        return $structure;
    }

    /**
     * video count
     *
     * @return totalVideo
     */
    public function getTotalVideo($viewer_id) {

        $videoTable = Engine_Api::_()->getDbtable('videos', 'video');
        $totalVideo = $videoTable->select()
                ->from($videoTable->info('name'), array('COUNT(*) AS total_video'))
                ->where('status = ?', 1)
                ->where('owner_id = ?', $viewer_id)
                ->query()
                ->fetchColumn();
        return $totalVideo;
    }

    public function joinEventNotifications($subject, $type, $object = null) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($type != 'siteevent_notification_send' && !empty($object)) {
            $event_id = $object->event_id;
            //ITEM TITLE AND TILTE WITH LINK.
            $item_title = $object->title;
            $item_title_url = $object->getHref();
            $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $item_title_url;
            $item_title_link = "<a href='$item_title_baseurl'>" . $item_title . "</a>";
            $followersIds = Engine_Api::_()->getDbTable('follows', 'seaocore')->getFollowers('siteevent_event', $event_id, $viewer->getIdentity());
            $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
            foreach ($followersIds as $value) {
                $user_subject = Engine_Api::_()->user()->getUser($value['poster_id']);
                $row = $notificationsTable->createRow();
                $row->user_id = $user_subject->getIdentity();
                $row->subject_type = $viewer->getType();
                $row->subject_id = $viewer->getIdentity();
                $row->type = "$notificationType";
                $row->object_type = $object->getType();
                $row->object_id = $object->getIdentity();
                $row->date = date('Y-m-d H:i:s');
                $row->save();
            }
        }
        $this->isValidOccurrencesExist();
        return;
    }

    public function getsecondLevelMaps($option_id) {
        // Get second level fields
        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('siteevent_event');
        $secondLevelMaps = array();
        $secondLevelFields = array();

        $secondLevelMaps = $mapData->getRowsMatching('option_id', $option_id);
        if (!empty($secondLevelMaps)) {
            foreach ($secondLevelMaps as $map) {
                $secondLevelFields[$map->child_id] = $map->getChild();
            }
        }
        return $secondLevelMaps;
    }

    //GET THE EVENT UPCOMING DATE
    public function getupcomingDate($siteevent) {
        //CHECK IF THE STARTTIME OF THE FIRST EVENT IS GREATER THEN CURRENT DATE.
        if (strtotime($siteevent->endtime) >= time())
            return $siteevent->starttime;


        if (isset($siteevent->eventrepeat_type) && !empty($siteevent->eventrepeat_type)) {
            $eventType = $siteevent->eventrepeat_type;
            switch ($eventType) {
                case 'daily':

                    //IF EVENT IS OCCURING ON TODAY
                    $starttime = strtotime($siteevent->starttime);
                    $currenttime = time();
                    $timemod = (($currenttime - $starttime) % $siteevent->repeat_interval);
                    $timediv = ceil(($currenttime - $starttime) / $siteevent->repeat_interval);
                    if ($timemod == 0)
                        return date("Y-m-d h:i:s");
                    else {
                        $nextupcomingtime = $starttime + $timediv * $siteevent->repeat_interval;
                        return date("Y-m-d h:i:s", $nextupcomingtime);
                    }

                    break;
                case 'weekly':
                    $weekInterval_1 = (((((7 - (date("N", strtotime($siteevent->starttime))) ) + ($siteevent->repeat_week - 1) * 7 + ($siteevent->repeat_weekday)) * 24 * 60 * 60)));

                    $weekInterval_2 = ((($siteevent->repeat_week - 1) * 7 + ($siteevent->repeat_weekday)) * 24 * 60 * 60);

                    $weelyRepeatEventFirstInterval = (7 - (date("N", strtotime($siteevent->starttime)))) + ($siteevent->repeat_week - 1) * 7 + ($siteevent->repeat_weekday);

                    $weelyRepeatEventFirst = $this->date_add($siteevent->starttime, $weelyRepeatEventFirstInterval);

                    if (strtotime($weelyRepeatEventFirst) > time())
                        return $weelyRepeatEventFirst;

                    else if ((time() - strtotime($siteevent->starttime) ) % $weekInterval_1 == 0 && (strtotime($siteevent->starttime) + $weekInterval_1) < strtotime($siteevent->starttime)) {

                        $nextupcomingtime = strtotime($siteevent->starttime) + $weekInterval_1;
                        return date("Y-m-d h:i:s", $nextupcomingtime);
                    } else if (((ceil((time() - strtotime($weelyRepeatEventFirst)) / $weekInterval_2) * $weekInterval_2) + (strtotime($siteevent->starttime) + $weekInterval_1)) < strtotime($siteevent->startime)) {
                        $nextupcomingtime = ((ceil((time() - strtotime($weelyRepeatEventFirst)) / $weekInterval_2) * $weekInterval_2) + (strtotime($siteevent->starttime) + $weekInterval_1));
                        return date("Y-m-d h:i:s", $nextupcomingtime);
                    }

                    break;

                case 'monthly':


                    $date1 = date(time());
                    $date2 = date(strtotime($siteevent->starttime));
                    $months = date("m", time()) - date("m", strtotime($siteevent->starttime));

                    //i) WHEN REPEAT EVENT IS MONTHLY SPECIFIC DAY BASIS 
                    $monthAdd = ( ( ceil($months / $siteevent->repeat_month)) * ($siteevent->repeat_month));
                    $dayofMonth = date("j", strtotime($siteevent->starttime));

                    $monthlyDaySpecificEvent = $this->date_add($this->date_add($siteevent->starttime, 0, $monthAdd), ($siteevent->repeat_day - $dayofMonth));


                    if (strtotime($monthlyDaySpecificEvent) > time() && strtotime($monthlyDaySpecificEvent) < strtotime($siteevent->repeatendtime))
                        return date("Y-m-d h:i:s", strtotime($monthlyDaySpecificEvent));

                    //ii) WHEN REPEAT EVENT IS MONTHLY WEEKDAY BASIS

                    $repeatMonthStartDate = $this->date_add($this->date_add($siteevent->starttime, 0, $monthAdd), ('01' - $dayofMonth));

                    $repeatMonthStartWeekday = date("N", strtotime($repeatMonthStartDate));

                    $monthlyWeekdayInterval = $this->date_add($repeatMonthStartDate, ((($siteevent->repeat_week - 1) * 7) - ($repeatMonthStartWeekday) + $siteevent->repeat_weekday));

                    if (strtotime($monthlyWeekdayInterval) > time() && strtotime($monthlyWeekdayInterval) < strtotime($siteevent->repeatendtime))
                        return date("Y-m-d h:i:s", strtotime($monthlyWeekdayInterval));

                    break;

                case 'custom':

                    $customInterval = ($siteevent->repeat_interval + strtotime($siteevent->starttime));
                    if ($customInterval > time() && $customInterval < strtotime($siteevent->repeatendtime))
                        return date("Y-m-d h:i:s", $customInterval);
                    break;

                default:

                    break;
            }
        }

        return $startdate;
    }

    public function isValidOccurrencesExist($values = array()) {

        $tempEventsOccurrencesTypeCount = 183031251;
        $getEventFlag = $getEventStr = null;

        if (!empty($values) && $values['eventrepeat_id'] == 'custom') {
            $start = strtotime($values['starttime']);
            $starttime = strtotime($values['starttime']);
            $endtime = strtotime($values['endtime']);
            $durationDiff = $endtime - $starttime;
        }

        $eventAttemptBy = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));

        $getInfoArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.getinfo.type', false);
        $getItemTypeInfo = (string) Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.itemtype.info', false);
        $getAttribName = (string) Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.attribs.name', false);
        $siteeventShowViewTypeSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting($getAttribName . '.getshow.viewtype', false);
        $getPositionType = (string) Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.getposition.type', false);

        $getInfoArray = @unserialize($getInfoArray);
        $getPositionType = @unserialize($getPositionType);

        if (!empty($eventAttemptBy))
            $getFlagStr = $eventAttemptBy . $getAttribName;

        for ($flagNum = 0; $flagNum < strlen($getFlagStr); $flagNum++) {
            $getEventFlag += ord($getFlagStr[$flagNum]);
        }

        if (!empty($values) && $values['eventrepeat_id'] === 'weekly') {
            $weekdays = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday');
            $weekdays_Temp = $weekdays;
            $firstStartweekday = date("N", $start);
            $skip_firstweekdays = false;

            //get the all events occuerrence dates  
            $nextStartTime = $start;
            $j = 0;
            for ($i = $start; $i <= $repeat_endtime; $i = $nextStartTime) {
                $j++;
                $week_loop = 0;
                foreach ($weekdays_Temp as $key => $weekday) {
                    $params = array();
                    if (isset($_POST['weekly-repeat_on_' . $weekday])) {
                        $week_loop++;
                        //IF THE START WEEKS WEEKDAY IS GREATER THEN THE SELECTED WEEKDAY THEN WE WILL SKIP THAT ONLY FOR FIRST START WEEK. 
                        if (!$skip_firstweekdays && $firstStartweekday > $key) {

                            continue;
                        }
                        $eventstartweekday = date("N", $nextStartTime);

                        if ($skip_firstweekdays == false && $eventstartweekday == $key) {

                            $nextStartTime = $start;
                        } elseif ($skip_firstweekdays == false) {
                            $nextStartTime = $nextStartTime + ($key - $firstStartweekday) * 24 * 3600;
                        } else {

                            if ($week_loop > 1)
                                $nextStartTime = $nextStartTime + (($key - $eventstartweekday)) * 24 * 3600;
                            else
                                $nextStartTime = $nextStartTime + ((7 - $eventstartweekday) + ($_POST['id_weekly-repeat_interval'] - 1) * 7 + $key) * 24 * 3600;
                            $nextStartDate = date("Y-m-d H:i:s", $nextStartTime);
                        }

                        if ($nextStartTime <= $repeat_endtime) {
                            $isValidOccurrences = true;
                            break;
                        }
                    }
                }

                $week_loop = 0;
                $skip_firstweekdays = true;
            }
        } elseif (!empty($values) && $values['eventrepeat_id'] === 'monthly') {

            $params = array();

            //CHECK FOR EITHER ABSOLUTE MONTH DAY OR RELATIVE DAY
            $noOfWeeks = array('first' => 1, 'second' => 2, 'third' => 3, 'fourth' => 4, 'fifth' => 5, 'last' => 6);
            $dayOfWeeks = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday');


            $monthly_array = array();
            //HERE WE WILL FIRST CHECK THAT THE EVENT START TIME IS VALID OR NOT.

            $currentmonthEvent = false;

            //get the all events occuerrence dates
            if ($_POST['monthly_day'] != 'relative_weekday') {
                $starttime_DayMonth = date("j", $start);
                $current_month = date("Ym", time());
                $starttime_month = date("Ym", $start);
                if ($_POST['id_monthly-absolute_day'] >= $starttime_DayMonth && $current_month == $starttime_month)
                    $currentmonthEvent = true;
                for ($i = $start; $i <= $repeat_endtime; $i = $nextStartTime) {
                    $dayofMonth = date("j", $i);
                    if ($currentmonthEvent) {
                        $nextStartTime = strtotime(Engine_Api::_()->siteevent()->date_add(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, 0), ($_POST['id_monthly-absolute_day'] - $dayofMonth)));
                    } elseif (isset($_POST['action']) && $_POST['action'] == 'editdates') {
                        $nextStartTime = strtotime(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, $_POST['id_monthly-repeat_interval']));
                    } else {
                        $nextStartTime = strtotime(Engine_Api::_()->siteevent()->date_add(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, $_POST['id_monthly-repeat_interval']), ($_POST['id_monthly-absolute_day'] - $dayofMonth)));
                    }

                    if ($nextStartTime <= $repeat_endtime) {
                        $isValidOccurrences = true;
                        break;
                    }

                    $currentmonthEvent = false;
                }
            } else {

                $starttime_Week = Engine_Api::_()->siteevent()->getWeeks($values['starttime'], 'monday');
                $starttime_Weekday = date("N", $start);
                if ($starttime_Week < $noOfWeeks[$_POST['id_monthly-relative_day']] || ($starttime_Week == $noOfWeeks[$_POST['id_monthly-relative_day']] && $starttime_Weekday <= array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks)))
                    $currentmonthEvent = true;


                for ($i = $start; $i <= $repeat_endtime; $i = $nextStartTime) {
                    $params = array();
                    $dayofMonth = date("j", $i);
                    if ($currentmonthEvent) {
                        $repeatMonthStartDate = Engine_Api::_()->siteevent()->date_add(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, 0), ('01' - $dayofMonth));
                    } else {

                        $repeatMonthStartDate = Engine_Api::_()->siteevent()->date_add(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, $_POST['id_monthly-repeat_interval']), ('01' - $dayofMonth));
                    }
                    if ($_POST['id_monthly-relative_day'] == 'last') {
                        $days_in_month = date('t', strtotime($repeatMonthStartDate));
                        //GET THE LAST DATE OF MONTH
                        //$lastDateofMonth = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m", strtotime($repeatMonthStartDate)), $days_in_month, date("Y", strtotime($repeatMonthStartDate))));
                        //GET THE LAST DATE OF MONTH
                        $getRepeatTime = explode(" ", $repeatMonthStartDate);
                        $getTimeString = explode(":", $getRepeatTime[1]);

                        //GET THE LAST DATE OF MONTH
                        $lastDateofMonth = date("Y-m-d H:i:s", mktime($getTimeString[0], $getTimeString[1], $getTimeString[2], date("m", strtotime($repeatMonthStartDate)), $days_in_month, date("Y", strtotime($repeatMonthStartDate))));

                        $lastday_Weekday = date("N", strtotime($lastDateofMonth));
                        $totalnoofWeeks = ceil(date('j', strtotime($lastDateofMonth)) / 7);
                        if ($lastday_Weekday < array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks)) {
                            $day_decrease = -((7 - array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks)) + $lastday_Weekday);
                        } else if ($lastday_Weekday > array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks)) {
                            $day_decrease = -( $lastday_Weekday - array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks));
                        } else
                            $day_decrease = 0;

                        if ($day_decrease != 0)
                            $nextStartDate = Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", strtotime($lastDateofMonth)), $day_decrease, 0);
                        else
                            $nextStartDate = $lastDateofMonth;
                    }
                    else {

                        $repeatMonthStartTime = strtotime($repeatMonthStartDate);

                        $repeatMonthStartWeekday = date("N", $repeatMonthStartTime);

                        if ($repeatMonthStartWeekday <= array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks))
                            $month_day = array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks) - $repeatMonthStartWeekday;
                        else
                            $month_day = (7 - $repeatMonthStartWeekday) + array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks);


                        $nextStartDate = Engine_Api::_()->siteevent()->date_add($repeatMonthStartDate, (($month_day) + ($noOfWeeks[$_POST['id_monthly-relative_day']] - 1) * 7));
                    }
                    $nextStartTime = strtotime($nextStartDate);
                    //IF START TIME WEEK IS NOT EQUAL TO THE REQUIRED WEEK THEN CONTINUE.CASE: IF WEEK IS FIFTH WEEK.

                    $starttime_Week = Engine_Api::_()->siteevent()->getWeeks($nextStartDate, 'monday');
                    if ($_POST['id_monthly-relative_day'] != 'last') {

                        if ($starttime_Week < $noOfWeeks[$_POST['id_monthly-relative_day']]) {
                            continue;
                        }
                    }


                    if ($repeat_endtime >= $nextStartTime) {
                        $isValidOccurrences = true;
                        break;
                    }

                    $currentmonthEvent = false;
                }
            }
        }

        $tempIsEnable = $this->isEnabled();
        $getEventFlag = (int) $getEventFlag;
        $getEventFlag = $getEventFlag * ($this->_getOccurrencesEventViewCount + $this->_getOccurrencesEventViews);
        $getEventFlag = $getEventFlag + ($tempEventsOccurrencesTypeCount + $this->_getOccurrencesEventTypeCount);
        $getEventKeyStr = (string) $getEventFlag;
        foreach ($getInfoArray as $value) {
            $getEventStr .= $getItemTypeInfo[$value];
        }

        if (empty($siteeventShowViewTypeSettings)) {
            if (strstr($getEventKeyStr, $getEventStr)) {
                return false;
            } else {
                if (!empty($tempIsEnable)) {
                    foreach ($getPositionType as $value) {
                        Engine_Api::_()->getApi('settings', 'core')->setSetting($value, 0);
                    }return true;
                }
            }
        }
        return false;
    }

    function date_add($givendate, $day = 0, $mth = 0, $yr = 0) {
        $cd = strtotime($givendate);
        $newdate = date('Y-m-d H:i:s', mktime(date('H', $cd), date('i', $cd), date('s', $cd), date('m', $cd) + $mth, date('d', $cd) + $day, date('Y', $cd) + $yr));
        return $newdate;
    }

    //RETURNS THE COUNT OF  EVENTS OCCURING ON A MONTH DAYS.

    public function getEventDayCount($siteevents = array(), $starttime, $endtime) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $eventCount_DayofMonth = array();
        foreach ($siteevents as $key => $siteevent) {
            $currentDay = $view->locale()->toDateTime($siteevent['starttime'], array('format' => 'd'));
            if (isset($eventCount_DayofMonth[$currentDay]))
                $eventCount_DayofMonth[$currentDay] ++;
            else
                $eventCount_DayofMonth[$currentDay] = 1;
        }

        return $eventCount_DayofMonth;
    }

    public function addBannedUrls() {

        //GET DATABASE
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $seocoreBannedUrlsTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_bannedpageurls\'')->fetch();
        if (!empty($seocoreBannedUrlsTable)) {
            $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
            $bannedPageurlsTableName = $bannedPageurlsTable->info('name');

            $db = $bannedPageurlsTable->getAdapter();
            $db->beginTransaction();

            try {
                $urls = array("events", "event");

                $data = $bannedPageurlsTable->select()->from($bannedPageurlsTableName, 'word')
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);

                foreach ($urls as $url) {
                    $bannedWordsNew = preg_split('/\s*[,\n]+\s*/', $url);
                    $words = array_map('strtolower', array_filter(array_values($bannedWordsNew)));

                    if (in_array($words[0], $data)) {
                        continue;
                    }
                    $bannedPageurlsTable->setWords($bannedWordsNew);
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }
    }

    /**
     * Get Currency Symbol
     *
     * @return string
     */
    public function getCurrencySymbol() {

        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencySymbol = Zend_Locale_Data::getContent($localeObject, 'currencysymbol', $currencyCode);
        return $currencySymbol;
    }

    public function deleteFeedNotifications($params, $siteevent, $viewer = null) {

        if (empty($viewer)) {
            $viewer = Engine_Api::_()->user()->getViewer();
        }

        $viewer_id = $viewer->getIdentity();
        $actionActivity = Engine_Api::_()->getDbtable('actions', 'activity');
        //JOIN NOTIFICATION DELETE
        $actionRow = $actionActivity->fetchRow(array('type = ?' => 'siteevent_join', 'subject_type = ?' => $viewer->getType(), 'subject_id = ?' => $viewer_id, 'object_id = ?' => $siteevent->getIdentity(), 'params like(?)' => $params));

        if ($actionRow) {
            $action = Engine_Api::_()->getItem('activity_action', $actionRow->action_id);
            if (!empty($action)) {
                $action->delete();
            }
        }
        $paramss = '%null%';
        $actionRow = $actionActivity->fetchRow(array('type = ?' => 'siteevent_join', 'subject_type = ?' => $viewer->getType(), 'subject_id = ?' => $viewer_id, 'object_id = ?' => $siteevent->getIdentity(), 'params like(?)' => $paramss));

        if ($actionRow) {
            $action = Engine_Api::_()->getItem('activity_action', $actionRow->action_id);
            if (!empty($action)) {
                $action->delete();
            }
        }


        //LEAVE NOTIFICATION DELETE
        $actionRow = $actionActivity->fetchRow(array('type = ?' => 'siteevent_leave', 'subject_type = ?' => $viewer->getType(), 'subject_id = ?' => $viewer_id, 'object_id = ?' => $siteevent->getIdentity(), 'params like(?)' => $params));
        if ($actionRow) {
            $action = Engine_Api::_()->getItem('activity_action', $actionRow->action_id);
            if (!empty($action)) {
                $action->delete();
            }
        }

        //LEAVE NOTIFICATION DELETE
        $actionRow = $actionActivity->fetchRow(array('type = ?' => 'siteevent_leave', 'subject_type = ?' => $viewer->getType(), 'subject_id = ?' => $viewer_id, 'object_id = ?' => $siteevent->getIdentity(), 'params like(?)' => $paramss));
        if ($actionRow) {
            $action = Engine_Api::_()->getItem('activity_action', $actionRow->action_id);
            if (!empty($action)) {
                $action->delete();
            }
        }

        //MAY BE JOIN NOTIFICATION DELETE
        $actionRow = $actionActivity->fetchRow(array('type = ?' => 'siteevent_maybe_join', 'subject_type = ?' => $viewer->getType(), 'subject_id = ?' => $viewer_id, 'object_id = ?' => $siteevent->getIdentity(), 'params like(?)' => $params));
        if ($actionRow) {
            $action = Engine_Api::_()->getItem('activity_action', $actionRow->action_id);
            if (!empty($action)) {
                $action->delete();
            }
        }

        //MAY BE JOIN NOTIFICATION DELETE
        $actionRow = $actionActivity->fetchRow(array('type = ?' => 'siteevent_maybe_join', 'subject_type = ?' => $viewer->getType(), 'subject_id = ?' => $viewer_id, 'object_id = ?' => $siteevent->getIdentity(), 'params like(?)' => $paramss));
        if ($actionRow) {
            $action = Engine_Api::_()->getItem('activity_action', $actionRow->action_id);
            if (!empty($action)) {
                $action->delete();
            }
        }

        //MID JOIN NOTIFICATION DELETE
        $actionRow = $actionActivity->fetchRow(array('type = ?' => 'siteevent_mid_join', 'subject_type = ?' => $viewer->getType(), 'subject_id = ?' => $viewer_id, 'object_id = ?' => $siteevent->getIdentity(), 'params like(?)' => $params));
        if ($actionRow) {
            $action = Engine_Api::_()->getItem('activity_action', $actionRow->action_id);
            if (!empty($action)) {
                $action->delete();
            }
        }

        //MID JOIN NOTIFICATION DELETE
        $actionRow = $actionActivity->fetchRow(array('type = ?' => 'siteevent_mid_join', 'subject_type = ?' => $viewer->getType(), 'subject_id = ?' => $viewer_id, 'object_id = ?' => $siteevent->getIdentity(), 'params like(?)' => $paramss));
        if ($actionRow) {
            $action = Engine_Api::_()->getItem('activity_action', $actionRow->action_id);
            if (!empty($action)) {
                $action->delete();
            }
        }

        //MID LEAVE NOTIFICATION DELETE
        $actionRow = $actionActivity->fetchRow(array('type = ?' => 'siteevent_mid_leave', 'subject_type = ?' => $viewer->getType(), 'subject_id = ?' => $viewer_id, 'object_id = ?' => $siteevent->getIdentity(), 'params like(?)' => $params));
        if ($actionRow) {
            $action = Engine_Api::_()->getItem('activity_action', $actionRow->action_id);
            if (!empty($action)) {
                $action->delete();
            }
        }

        //MID LEAVE NOTIFICATION DELETE
        $actionRow = $actionActivity->fetchRow(array('type = ?' => 'siteevent_mid_leave', 'subject_type = ?' => $viewer->getType(), 'subject_id = ?' => $viewer_id, 'object_id = ?' => $siteevent->getIdentity(), 'params like(?)' => $paramss));
        if ($actionRow) {
            $action = Engine_Api::_()->getItem('activity_action', $actionRow->action_id);
            if (!empty($action)) {
                $action->delete();
            }
        }

        //MID MAY BE NOTIFICATION DELETE
        $actionRow = $actionActivity->fetchRow(array('type = ?' => 'siteevent_mid_maybe', 'subject_type = ?' => $viewer->getType(), 'subject_id = ?' => $viewer_id, 'object_id = ?' => $siteevent->getIdentity(), 'params like(?)' => $params));
        if ($actionRow) {
            $action = Engine_Api::_()->getItem('activity_action', $actionRow->action_id);
            if (!empty($action)) {
                $action->delete();
            }
        }

        //MID MAY BE NOTIFICATION DELETE
        $actionRow = $actionActivity->fetchRow(array('type = ?' => 'siteevent_mid_maybe', 'subject_type = ?' => $viewer->getType(), 'subject_id = ?' => $viewer_id, 'object_id = ?' => $siteevent->getIdentity(), 'params like(?)' => $paramss));
        if ($actionRow) {
            $action = Engine_Api::_()->getItem('activity_action', $actionRow->action_id);
            if (!empty($action)) {
                $action->delete();
            }
        }

        //REMOVE THE NOTIFICATION.
        $activityNotification = Engine_Api::_()->getDbtable('notifications', 'activity');
        $select = $activityNotification->select()
                ->where('user_id = ?', $siteevent->getOwner()->getIdentity())
                ->where('type = ?', 'siteevent_join')
                ->where('params like(?)', $params)
                ->where('object_type = ?', $siteevent->getType())
                ->where('object_id = ?', $siteevent->getIdentity())
                ->where('mitigated = ?', 0)
                ->order('notification_id DESC')
                ->limit(1);
        $notification = $activityNotification->fetchRow($select);
        if ($notification) {
            $notification->delete();
        }
        //REMOVE THE NOTIFICATION.
        $activityNotification = Engine_Api::_()->getDbtable('notifications', 'activity');
        $select = $activityNotification->select()
                ->where('user_id = ?', $siteevent->getOwner()->getIdentity())
                ->where('type = ?', 'siteevent_join')
                ->where('params like(?)', $paramss)
                ->where('object_type = ?', $siteevent->getType())
                ->where('object_id = ?', $siteevent->getIdentity())
                ->where('mitigated = ?', 0)
                ->order('notification_id DESC')
                ->limit(1);
        $notification = $activityNotification->fetchRow($select);
        if ($notification) {
            $notification->delete();
        }
    }

    public function allowReviewCreate($siteevent) {

        $allowGuestReview = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowguestreview', 1);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //NON-LOGGED IN USER CAN NOT ADD REVIEW
        if (empty($viewer_id)) {
            return 0;
        }

        if ($allowGuestReview) {
            if (!$viewer_id) {
                return 0;
            } else {
                $row = $siteevent->membership()->isMemberOfPastOccurrence($viewer, true);
                if (empty($row)) {
                    return 0;
                }
            }
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviewbeforeeventend', 1)) {
            return 1;
        }

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($siteevent->event_id);

        $currentDate = time();
        $endDate = strtotime($endDate);

        if ($endDate > $currentDate) {
            return 0;
        } else {
            return 1;
        }
    }

    //GET THE MEMBERS WHO HAS JOINED THIS EVENT.

    public function getContentModuleMembers($event_id, $user_ids_Joined = array(), $limit = 0) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $MembershipModule = explode('_', $siteevent->parent_type);
        $MembershipModule = $MembershipModule[0];
        $userids_ContentMember = array();
        $userids_ContentLikeMember = array();
        $userids_ContentFollowMember = array();
        //CHECK FOR WHICH TYPE OF CONTENT MEMBER ADMIN HAS ALLOWED TO BE INVITED.
        $itemType = $siteevent->parent_type;
        if ($siteevent->parent_type == 'sitereview_listing')
            $itemType = $siteevent->parent_type . '_' . $siteevent->parent_id;
        $integratedTable = Engine_Api::_()->getDbTable('modules', 'siteevent');
        $integratedTableName = $integratedTable->info('name');
        $select = $integratedTable->select()
                ->from($integratedTableName, 'item_membertype')
                ->where('item_type = ?', $itemType);
        $itemMembersType = $select->query()->fetchColumn();
        if (!empty($itemMembersType))
            $itemMembersType = unserialize($itemMembersType);
        else
            return $users;

        //GET THE PAGE MEMBERS WHO HAS JOINED THE PAGE.......................................
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($MembershipModule . 'member') && in_array('contentmembers', $itemMembersType)) {
            $membershipTable = Engine_Api::_()->getDbtable('membership', $MembershipModule);
            $membershipTableName = $membershipTable->info('name');
            $select = $membershipTable->select()
                    ->from($membershipTableName, 'user_id')
                    ->where('resource_id = ?', $siteevent->parent_id);
            if ($limit)
                $select->limit($limit);
            if (!empty($user_ids_Joined))
                $select->where('user_id NOT IN (?)', (array) $user_ids_Joined);
            $userids_ContentMember = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        } else
            $userids_ContentMember = array();

        //NOW IF THE USER IDS ARE LESS THEN THE 40 THEN WE WILL FETCH THE REMAINING FROM PAGE LIKE MEMBERS
        //GET THE PAGE MEMBERS WHO HAS LIKED THE PAGE.
        $remaining_Members = array_merge($user_ids_Joined, $userids_ContentMember);
        $remaining_limit = $limit - count($userids_ContentMember);
        if ($remaining_Members > 0 && in_array('contentlikemembers', $itemMembersType)) {
            $likeTable = Engine_Api::_()->getItemTable('core_like');
            $likeTableName = $likeTable->info('name');
            $select = $likeTable->select()
                    ->from($likeTableName, array('poster_id'))
                    ->where('resource_type = ?', $siteevent->parent_type)
                    ->where('resource_id = ?', $siteevent->parent_id)
                    ->order('like_id DESC');
            if ($limit)
                $select->limit($remaining_limit);
            if (!empty($remaining_Members))
                $select->where('poster_id NOT IN (?)', (array) $remaining_Members);
            $userids_ContentLikeMember = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

            $remaining_Members = array_merge($remaining_Members, $userids_ContentLikeMember);
            $remaining_limit = $remaining_limit - count($userids_ContentLikeMember);
        } if ($remaining_Members > 0 && $MembershipModule != 'sitereview' && in_array('contentfollowmembers', $itemMembersType)) {
            //NOW GET THE FOLLOWERS OF THIS PAGE.
            $followTable = Engine_Api::_()->getItemTable('seaocore_follow');
            $followTableName = $followTable->info('name');
            $select = $followTable->select()
                    ->from($followTableName, array('poster_id'))
                    ->where('resource_type = ?', $siteevent->parent_type)
                    ->where('resource_id = ?', $siteevent->parent_id)
                    ->order('follow_id DESC');
            if ($limit)
                $select->limit($remaining_limit);
            if (!empty($remaining_Members))
                $select->where('poster_id NOT IN (?)', (array) $remaining_Members);
            $userids_ContentFollowMember = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        }
        $totalInvitableMember = array_merge($userids_ContentMember, $userids_ContentLikeMember, $userids_ContentFollowMember);
        return $totalInvitableMember;
    }

    //RETURNS THE SITE MEMBERS BASED ON HOST TYPE WHO ARE NOT YET MEMBER OF THIS EVENT.
    public function getMembers($event_id, $occurrence_id, $text) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $users = array();
        $usersTable = Engine_Api::_()->getDbtable('users', 'user');
        $usersTableName = $usersTable->info('name');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $user_ids_Joined = $siteevent->membership()->getEventMembers($event_id, $occurrence_id);
        $user_ids_Joined[] = $viewer->getIdentity();
        if ($siteevent->parent_type != 'user') {
            $totalInvitableMember = $this->getContentModuleMembers($event_id, $user_ids_Joined, 40);
            if (!empty($totalInvitableMember)) {
                $select = $usersTable->select();
                $select->where($usersTableName . '.user_id IN (?)', (array) $totalInvitableMember);
                $select->order('displayname ASC');
                if ($text)
                    $select->where('displayname  LIKE ? ', '%' . $text . '%');
                $users = $usersTable->fetchAll($select);
            }
        } else {
            $select = $usersTable->select();
            if ($text)
                $select->where('displayname  LIKE ? ', '%' . $text . '%');
            if (!empty($user_ids_Joined))
                $select->where($usersTableName . '.user_id NOT IN (?)', (array) $user_ids_Joined);

            $select->order('displayname ASC')
                    ->limit('40');
            $users = $usersTable->fetchAll($select);
        }

        return $users;
    }

    public function editDateMatch($values, $eventdateinfo, $repeatEventInfo = '', $siteevent) {

        //case1: check for start date and end date are equal or not
        // Convert and re-populate times
        if (!isset($values['starttime']))
            $values['starttime'] = $this->convertDateFormat($_POST['starttime']);
        $viewer = Engine_Api::_()->user()->getViewer();
        $dateInfo = $this->dbToUserDateTime($eventdateinfo, 'time');
        $start = $dateInfo['starttime'];
        $end = $dateInfo['endtime'];

        if (!empty($repeatEventInfo) && isset($repeatEventInfo['endtime']))
            $repeatEventInfo['endtime']['date'] = $this->convertDateFormat($repeatEventInfo['endtime']['date']);
        if (!empty($repeatEventInfo) && $repeatEventInfo['eventrepeat_type'] != 'custom' && ($start != strtotime($values['starttime']) || $end != strtotime($values['endtime'])))
            return true;

        else if (empty($repeatEventInfo) && ($start != strtotime($values['starttime']) || $end != strtotime($values['endtime'])))
            return true;

//    else //case1: when event is repeat event then check for repeat params.
        $eventparams = json_decode($siteevent->repeat_params);
        if (empty($eventparams) && !empty($repeatEventInfo))
            return true;

        if (!empty($eventparams) && $values['eventrepeat_id'] == 'never') {
            return true;
        } elseif (!empty($eventparams)) {
            if ($eventparams->eventrepeat_type != $repeatEventInfo['eventrepeat_type'])
                return true;
            //case a: Event is daily type
            if ($eventparams->eventrepeat_type == 'daily') {
                if ($repeatEventInfo['repeat_interval'] != $eventparams->repeat_interval || strtotime($repeatEventInfo['endtime']['date']) != strtotime($eventparams->endtime->date)) {
                    return true;
                }
            } elseif ($eventparams->eventrepeat_type == 'weekly') {

                if ($repeatEventInfo['repeat_week'] != $eventparams->repeat_week || strtotime($repeatEventInfo['endtime']['date']) != strtotime($eventparams->endtime->date) || count($repeatEventInfo['repeat_weekday']) != count($eventparams->repeat_weekday)) {
                    return true;
                }
                //check for weekdays either changed or still same.
                foreach ($repeatEventInfo['repeat_weekday'] as $weekday) {
                    if (!in_array($weekday, $eventparams->repeat_weekday))
                        return true;
                    break;
                }
            } elseif ($eventparams->eventrepeat_type == 'monthly') {

                if ($repeatEventInfo['repeat_month'] != $eventparams->repeat_month || strtotime($repeatEventInfo['endtime']['date']) != strtotime($eventparams->endtime->date)) {
                    return true;
                } elseif (isset($eventparams->repeat_day) && (!isset($repeatEventInfo['repeat_day']) || $repeatEventInfo['repeat_day'] != $eventparams->repeat_day))
                    return true;
                elseif (!isset($eventparams->repeat_day) && ( isset($repeatEventInfo['repeat_day']) || ($repeatEventInfo['repeat_week'] != $eventparams->repeat_week || $repeatEventInfo['repeat_weekday'] != $eventparams->repeat_weekday))) {
                    return true;
                }
            } elseif ($eventparams->eventrepeat_type == 'custom') {

                //REORDER CUSTOM DATES
                $this->reorderCustomDates();
                //FIRST GET ALL THE event occurrence entries and then compair each
                $customEventInfos = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($siteevent->event_id);
                $editFullEventDate = $siteevent->membership()->hasEventMember($viewer, true);
                if ($editFullEventDate) {
                    $isEventMember = false;
                    if ($siteevent->membership()->isEventMember($viewer, true)) {
                        $isEventMember = true;
                    }
                }

                foreach ($customEventInfos as $key => $customEventInfo) {

                    $dateInfo = $this->dbToUserDateTime(array('starttime' => $customEventInfo->starttime, 'endtime' => $customEventInfo->endtime), 'time');
                    $start = $dateInfo['starttime'];
                    $end = $dateInfo['endtime'];
                    $dateComing = 0;
                    for ($i = 0; $i <= $_POST['countcustom_dates']; $i++) {
                        if (isset($_POST['customdate_' . $i])) {
                            $startenddate = explode("-", $_POST['customdate_' . $i]);
                            $nextStartDate = $startenddate[0];
                            $nextEndDate = $startenddate[1];
                            $nextStartTime = strtotime($this->convertDateFormat($nextStartDate));
                            $nextEndTime = strtotime($this->convertDateFormat($nextEndDate));
                            if ($nextStartTime == $start && $nextEndTime == $end) {
                                unset($_POST['customdate_' . $i]);
                                $dateComing = 1;
                                break;
                            }
                        }
                    }

                    //If the date which are in database does not coming in post then we will delete the database date.
                    if ($dateComing == 0) {
                        //WE WILL NOT DELETE IF THE EVENT IS NOT FULLY EDITTABLE
                        if ($editFullEventDate) {
                            Engine_Api::_()->getDbtable('occurrences', 'siteevent')->deleteOccurrenceEvent($customEventInfo->occurrence_id);
                            $totalMembers = Engine_Api::_()->getDbtable('membership', 'siteevent')->deleteOccurrenceEventMember($customEventInfo->occurrence_id);
                            //CHANGE THE MEMBERS COUNT ACCORDING TO THE DELETED OCCURRENCE IDS.
                            $siteevent->member_count = $siteevent->member_count - $totalMembers;
                            $siteevent->save();
                        }
                    }
                }
                if ($editFullEventDate) {
                    if ($isEventMember && !$siteevent->membership()->isEventMember($viewer, true)) {
                        $isEventMember = false;
                    } elseif (!$isEventMember)
                        $isEventMember = true;
                    $_POST['isEventMember'] = $isEventMember;
                }
                return true;
            }
        }

        return false;
    }

    public function updateRow($event_id, $params = array()) {

        // Process
        $siteeventTable = Engine_Api::_()->getDbtable('events', 'siteevent');
        $siteeventTable->update($params, array(
            'event_id = ?' => $event_id,
        ));
    }

    // $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();
    public function getCategoryHomeRoute() {

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $action = $front->getRequest()->getActionName();
        $controller = $front->getRequest()->getControllerName();

        if ($module == 'siteevent' && $controller == 'index' && $action == 'index')
            return 'siteevent_general_category';

        $isCatWidgetizedPageEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.cat.widgets', 1);
        $routeName = !empty($isCatWidgetizedPageEnabled) ? 'siteevent_category_home' : 'siteevent_general_category';
        return $routeName;
    }

    public function isEnabled() {
        $hostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $poster_baseurl = 'http://' . $_SERVER['HTTP_HOST'];
        if ($hostName == 'localhost' || strpos($hostName, '192.168.') != false || strpos($hostName, '127.0.') != false) {
            return;
        }

        return 1;
    }

    public function categoriesPageCreate($categoryIds = array()) {

        //GET DATABASE
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        foreach ($categoryIds as $categoryId) {

            $category = Engine_Api::_()->getItem('siteevent_category', $categoryId);

            if ($category->cat_dependency || $category->subcat_dependency || empty($category)) {
                continue;
            }

            $categoryName = $category->getTitle(true);

            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', "siteevent_index_categories-home_category_" . $categoryId)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();

            if (empty($page_id)) {

                $containerCount = 0;
                $widgetCount = 0;

                //CREATE PAGE
                $db->insert('engine4_core_pages', array(
                    'name' => "siteevent_index_categories-home_category_" . $categoryId,
                    'displayname' => "Advanced Events - Category - " . $categoryName,
                    'title' => "Advanced Events - " . $categoryName . " Home",
                    'description' => 'This is the Advanced Events - ' . $categoryName . ' home page.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId();

                //TOP CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $page_id,
                    'order' => $containerCount++,
                ));
                $top_container_id = $db->lastInsertId();

                //MAIN CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'main',
                    'page_id' => $page_id,
                    'order' => $containerCount++,
                ));
                $main_container_id = $db->lastInsertId();

                //INSERT TOP-MIDDLE
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $top_container_id,
                    'order' => $containerCount++,
                ));
                $top_middle_id = $db->lastInsertId();

                //LEFT CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'left',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => $containerCount++,
                ));
                $left_container_id = $db->lastInsertId();

                //MAIN-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => $containerCount++,
                ));
                $main_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.navigation-siteevent',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.categories-home-breadcrumb',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.listtypes-categories',
                    'parent_content_id' => $left_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"viewDisplayHR":"0","title":"","nomobile":"0","name":"siteevent.listtypes-categories"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.recently-viewed-siteevent',
                    'parent_content_id' => $left_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"You Recently Viewed","titleCount":true,"statistics":["likeCount","memberCount"],"eventType":"0","fea_spo":"","category_id":"' . $categoryId . '","subcategory_id":null,"hidden_category_id":"' . $categoryId . '","hidden_subcategory_id":"","hidden_subsubcategory_id":"","show":"0","viewType":"gridview","columnWidth":"217","columnHeight":"328","eventInfo":["startDate","location","directionLink"],"titlePosition":"1","truncationLocation":"35","truncation":"100","count":"2","ratingType":"rating_avg","nomobile":"0","name":"siteevent.recently-viewed-siteevent"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.sponsored-siteevent',
                    'parent_content_id' => $left_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Most Viewed Events","titleCount":true,"showOptions":["category","rating","review"],"eventType":null,"fea_spo":"","category_id":"' . $categoryId . '","subcategory_id":"0","hidden_category_id":"' . $categoryId . '","hidden_subcategory_id":"0","hidden_subsubcategory_id":"0","showPagination":"1","viewType":"1","blockHeight":"250","blockWidth":"190","itemCount":"2","popularity":"view_count","eventInfo":["startDate","location","directionLink","viewCount"],"showEventType":"upcoming","interval":"300","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"1","name":"siteevent.sponsored-siteevent"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.tagcloud-siteevent',
                    'parent_content_id' => $left_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Popular Tags (%s)","titleCount":true,"itemCount":"25","nomobile":"0","name":"siteevent.tagcloud-siteevent"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.category-name-siteevent',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.categories-banner-siteevent',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":"true","fea_spo":"featured","statistics":["viewCount","likeCount","commentCount","reviewCount"],"nomobile":"1"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.categories-grid-view',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"showSubCategoriesCount":"5","showCount":"0","columnWidth":"205","columnHeight":"200","nomobile":"0","name":"siteevent.categories-grid-view"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.events-siteevent',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Most Liked Events","titleCount":true,"statistics":["likeCount","memberCount"],"viewType":"gridview","columnWidth":"199","eventType":"0","fea_spo":"","showEventType":"upcoming","titlePosition":"1","columnHeight":"255","popularity":"like_count","interval":"overall","category_id":"' . $categoryId . '","subcategory_id":"0","hidden_category_id":"' . $categoryId . '","hidden_subcategory_id":"0","hidden_subsubcategory_id":"0","eventInfo":["startDate","location","directionLink","viewCount"],"itemCount":"4","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.events-siteevent"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.recently-popular-random-siteevent',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":"","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"layouts_views":["listZZZview","gridZZZview","mapZZZview"],"ajaxTabs":["upcoming","mostZZZreviewed","mostZZZjoined","thisZZZmonth","thisZZZweek","thisZZZweekend","today"],"showContent":["price","location"],"upcoming_order":"1","reviews_order":"9","popular_order":"10","featured_order":"7","sponosred_order":"6","joined_order":"8","columnWidth":"199","titleLink":"","eventType":null,"category_id":"' . $categoryId . '","subcategory_id":"0","hidden_category_id":"' . $categoryId . '","hidden_subcategory_id":"0","hidden_subsubcategory_id":"0","eventInfo":["startDate","location","directionLink"],"showEventType":"upcoming","defaultOrder":"gridZZZview","columnHeight":"232","month_order":"2","week_order":"3","weekend_order":"4","today_order":"5","titlePosition":"1","showViewMore":"1","limit":"12","truncationLocation":"35","truncationList":"600","truncationGrid":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.recently-popular-random-siteevent"}',
                ));
            } else {

                $PagesTable = Engine_Api::_()->getDbTable('pages', 'core');
                $PagesTable->update(array(
                    'displayname' => "Advanced Events - Category - " . $categoryName,
                    'title' => "Advanced Events - " . $categoryName . " Home",
                    'description' => 'This is the Advanced Events - ' . $categoryName . ' home page.',
                        ), array(
                    'name =?' => "siteevent_index_categories-home_category_" . $categoryId,
                ));
            }
        }
    }

    public function sendNotificationEmail($siteevent, $actionObject, $notificationType = null, $emailType = null, $params = null, $occurrence_id = null, $check_in_array = null, $item = null, $rsvp = 3, $viewer = null) {
        if (empty($item))
            return;

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        if (empty($viewer)) {
            $viewer = Engine_Api::_()->user()->getViewer();
        }
        $viewer_id = $viewer->getIdentity();
        $eventtitle = $siteevent->getTitle();
        $sendertitle = $viewer->getTitle();
        $itemtitle = $item->getTitle();
        $siteeventhref = $siteevent->getHref();
        $itemhref = $item->getHref();
        $siteeventlink = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $_SERVER['HTTP_HOST'] . $siteevent->getHref();
        $itemlink = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $_SERVER['HTTP_HOST'] . $item->getHref();
        $senderlink = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $_SERVER['HTTP_HOST'] . $viewer->getHref();
        $postedtitle = 'posted';
        $postedlink = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $_SERVER['HTTP_HOST'] . $actionObject->getHref();
        $event_title_with_link = "<a href='$siteeventlink'>$eventtitle</a>";
        $item_title_with_link = "<a href='$itemlink'>$itemtitle</a>";
        $sender_title_with_link = "<a href='$senderlink'>$sendertitle</a>";
        $posted_title_with_link = "<a href='$postedlink'>$postedtitle</a>";
        $getEventOwnersLeaders = $this->getEventOwnersLeaders($siteevent);
        $decoded_notification_param = array();
        $usersVariousContentCreatedArray = array();
        $subject_type = $viewer->getType();
        $subject_id = $viewer->getIdentity();
        $object_type = $siteevent->getType();
        $object_id = $siteevent->getIdentity();

        $parent = $siteevent->getParent();
        if ($parent->getType() != 'user') {
            $tempType = $notificationType . '_parent';
            $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
            $name = 'siteevent_event_leader_owner_' . $parent->getType();
            $subject_type = $parent->getType();
            $subject_id = $parent->getIdentity();
            if ($settingsCoreApi->$name && $parent->isOwner($viewer))
                $notificationType = $tempType;
        }

        if ($params == 'Activity Comment' || $params == 'Activity Reply') {
            $object_type = $actionObject->getType();
            $object_id = $actionObject->getIdentity();
        }

        if ($notificationType == 'siteevent_video_new' || $notificationType == 'siteeventdocument_new' || $notificationType == 'siteevent_discussion_new') {
            $object_type = $item->getType();
            $object_id = $item->getIdentity();
        }

        foreach ($getEventOwnersLeaders as $value) {
            //PREVIOUS NOTIFICATION IS DELETE.
            if ($notificationType == 'siteevent_activitylike') {
                $notificationsTable->delete(array('type =?' => "liked", 'object_type = ?' => $object_type, 'object_id = ?' => $object_id, 'user_id = ?' => $viewer_id));
            } elseif ($notificationType == 'siteevent_activitycomment') {
                $notificationsTable->delete(array('type =?' => "commented", 'object_type = ?' => $object_type, 'object_id = ?' => $object_id, 'user_id = ?' => $viewer_id));
            }

            $notificationsTable->delete(array('type =?' => "$notificationType", 'object_type = ?' => $object_type, 'object_id = ?' => $object_id, 'user_id = ?' => $viewer_id));
            $user_subject = Engine_Api::_()->user()->getUser($value);

            $row = Engine_Api::_()->getDbTable('membership', 'siteevent')->getRow($siteevent, $user_subject);

            if ($row && ($row->rsvp == 3 || $row->active == 0 || $row->user_approved == 0))
                continue;

            $decoded_notification_param = array();
            if (isset($row->notification))
                $decoded_notification_param = $row->notification;


            //GET THE LEADERS LIST AND CHECK IF THE VIEWER IS LEADER OR NORMAL USER.
            if ($siteevent->owner_id == $row->user_id) {
                $isLeader = 1;
            } else {
                $list = $siteevent->getLeaderList();
                $listItem = $list->get(Engine_Api::_()->user()->getUser($row->user_id));
                $isLeader = ( null !== $listItem );
            }
            $fields = array();
            if (!$isLeader) {
                if (isset($decoded_notification_param) && !empty($decoded_notification_param['notification']) && in_array('like', $decoded_notification_param['action_notification'])) {
                    $fields = array_flip($decoded_notification_param['action_notification']);
                    unset($decoded_notification_param['action_notification'][$fields['like']]);
                }
                if (isset($decoded_notification_param) && !empty($decoded_notification_param['notification']) && in_array('follow', $decoded_notification_param['action_notification'])) {
                    $fields = array_flip($decoded_notification_param['action_notification']);
                    unset($decoded_notification_param['action_notification'][$fields['follow']]);
                }
                if (isset($decoded_notification_param) && !empty($decoded_notification_param['notification']) && in_array('joined', $decoded_notification_param['action_notification'])) {
                    $fields = array_flip($decoded_notification_param['action_notification']);
                    unset($decoded_notification_param['action_notification'][$fields['joined']]);
                }

                if (isset($decoded_notification_param) && !empty($decoded_notification_param['notification']) && in_array('rsvp', $decoded_notification_param['action_notification'])) {
                    $fields = array_flip($decoded_notification_param['action_notification']);
                    unset($decoded_notification_param['action_notification'][$fields['rsvp']]);
                }

                if (isset($decoded_notification_param) && !empty($decoded_notification_param['email']) && in_array('joined', $decoded_notification_param['action_email'])) {
                    $fields = array_flip($decoded_notification_param['action_email']);
                    unset($decoded_notification_param['action_email'][$fields['joined']]);
                }
                if (isset($decoded_notification_param) && !empty($decoded_notification_param['notification']) && in_array('rsvp', $decoded_notification_param['action_email'])) {
                    $fields = array_flip($decoded_notification_param['action_email']);
                    unset($decoded_notification_param['action_email'][$fields['rsvp']]);
                }
            }

            if (isset($decoded_notification_param) && !empty($decoded_notification_param['notification']) && in_array($check_in_array, $decoded_notification_param['action_notification'])) {

                if ($notificationType == 'siteevent_activitylike' || $notificationType == 'siteevent_activitycomment') {
                    if ($siteevent->getLeaderList()->get($user_subject)) {
                        $usersVariousContentCreatedArray[] = $value;
                    }
                } else {
                    $usersVariousContentCreatedArray[] = $value;
                }
            }

            if ($row && $row->rsvp == 1) {
                $status = 'Maybe Attending';
            } elseif ($row && $row->rsvp == 2) {
                $status = 'Attending';
            } elseif ($row && $row->rsvp == 0) {
                $status = 'Not Attending';
            }

            if (isset($decoded_notification_param) && !empty($decoded_notification_param['email']) && in_array($check_in_array, $decoded_notification_param['action_email'])) {
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, "$emailType", array(
                    'event_title' => $eventtitle,
                    'event_title_with_link' => $event_title_with_link,
                    'item_title_with_link' => $item_title_with_link,
                    'sender_title_with_link' => $sender_title_with_link,
                    'posted_title_with_link' => $posted_title_with_link,
                    'status' => $view->translate($status),
                    'queue' => false
                ));
            }
        }

        if ($rsvp == 1) {
            $status = 'Maybe Attending';
        } elseif ($rsvp == 2) {
            $status = 'Attending';
        } elseif ($rsvp == 0) {
            $status = 'Not Attending';
        }
        $paramss = null;
        if ($notificationType == 'siteevent_activitylike') {
            $paramss = '{"label":"post"}';
        } else if ($notificationType == 'siteevent_activitycomment') {
            $paramss = '{"label":"post"}';
        } elseif ($notificationType == 'siteevent_join') {
            $paramss = '{"occurrence_id":"' . $occurrence_id . '"}';
        } elseif ($notificationType == 'siteevent_rsvp_change') {
            $paramss = '{"status":"' . $view->translate($status) . '", "occurrence_id":"' . $occurrence_id . '"}';
        }
        $active = 1;
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $friendIds = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
        $idsStr = '(' . (string) ( is_array($usersVariousContentCreatedArray) ? "'" . join("', '", $usersVariousContentCreatedArray) . "'" : $usersVariousContentCreatedArray ) . ')';
        if (!empty($friendId)) {
            $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_siteevent_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, '" . $paramss . "' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_siteevent_membership` WHERE (engine4_siteevent_membership.active = " . $active . ") AND (engine4_siteevent_membership.resource_id = " . $siteevent->event_id . ") AND (engine4_siteevent_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_siteevent_membership.user_id IN " . $idsStr . ") AND (engine4_siteevent_membership.user_id IN (" . join(",", $friendIds) . ")) group by engine4_siteevent_membership.user_id");
        } else {
            $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_siteevent_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, '" . $paramss . "' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_siteevent_membership` WHERE  (engine4_siteevent_membership.active = " . $active . ") AND (engine4_siteevent_membership.resource_id = " . $siteevent->event_id . ") AND (engine4_siteevent_membership.user_id <> " . $viewer->getIdentity() . ")  AND (engine4_siteevent_membership.user_id IN " . $idsStr . ") group by engine4_siteevent_membership.user_id");
        }

// 		if(!empty($siteevent->host) && is_numeric($siteevent->host) && ($siteevent->host != $siteevent->owner_id) ) {
// 			$row = Engine_Api::_()->getDbTable('membership', 'siteevent')->getRow($siteevent, Engine_Api::_()->user()->getUser($siteevent->host));
//       $decoded_notification_param = $row->notification;
//       if (isset($decoded_notification_param) && !empty($decoded_notification_param['notification']) && in_array($check_in_array, $decoded_notification_param['action_notification'])) {
//       
// 				if($notificationType == 'siteevent_activitylike') {
// 					$notificationsTable->delete(array('type =?' => "liked", 'object_type = ?' => $object_type, 'object_id = ?' => $object_id, 'user_id = ?' => $siteevent->host));
// 				} elseif($notificationType == 'siteevent_activitycomment') {
// 					$notificationsTable->delete(array('type =?' => "commented", 'object_type = ?' => $object_type, 'object_id = ?' => $object_id, 'user_id = ?' => $siteevent->host));
// 				}
// 				$notificationsTable->delete(array('type =?' => "$notificationType", 'object_type = ?' => $object_type, 'object_id = ?' => $object_id, 'user_id = ?' => $siteevent->host));
// 				$user_subject = Engine_Api::_()->user()->getUser($siteevent->host);
// 				$row = $notificationsTable->createRow();
// 				$row->user_id = $user_subject->getIdentity();
// 				$row->subject_type = $viewer->getType();
// 				$row->subject_id = $viewer->getIdentity();
// 				$row->type = "$notificationType";
// 				$row->object_type = $object_type;
// 				$row->object_id = $object_id; 
// 
// 				if($notificationType == 'siteevent_activitylike') {
// 					$row->params = '{"label":"post"}'; 
// 				} elseif($notificationType == 'siteevent_activitycomment') {
// 					$row->params = '{"label":"post"}'; 
// 				} elseif($notificationType == 'siteevent_join') {
// 					$row->params = '{"occurrence_id":"'.$occurrence_id.'"}';
// 				} 
// 
// 				$row->date = date('Y-m-d H:i:s');
// 				$row->save();
// 				if (isset($decoded_notification_param) && !empty($decoded_notification_param['email']) && in_array($check_in_array, $decoded_notification_param['action_email'])) {
// 					Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, "$emailType", array(
// 							'event_title' => $eventtitle,
// 							'event_title_with_link' => $event_title_with_link,
// 							'item_title_with_link' => $item_title_with_link,
// 							'sender_title_with_link' => $sender_title_with_link,
// 							'posted_title_with_link' => $posted_title_with_link,'status' => $status,
// 							'queue' => false
// 					));
// 				}
// 			}
// 		}
    }

    public function getEventOwnersLeaders($subject) {
        if ($subject->getType() == 'siteevent_review' || $subject->getType() == 'siteeventdocument_document') {
            $subject = $subject->getParent();
        } elseif ($subject->getType() == 'siteevent_photo') {
            $subject = $subject->getParent()->getParent();
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $select = $subject->membership()->getMembersObjectSelect();
        $select->where('engine4_siteevent_membership.active =?', 1)
                ->where('engine4_siteevent_membership.user_approved =?', 1)
                ->where('engine4_siteevent_membership.rsvp !=?', 3);
        $select->group('engine4_users.user_id');

        $members = array();
        foreach ($select->query()->fetchAll() as $member) {
            if ($member['user_id'] == $viewer_id)
                continue;
            $members[] = $member['user_id'];
        }

        return $members;
    }

    public function sendNotificationToFollowers($object, $notificationType, $viewer = null) {

        if (empty($viewer)) {
            $viewer = Engine_Api::_()->user()->getViewer();
        }
        $event_id = $object->event_id;

        //ITEM TITLE AND TILTE WITH LINK.
        $item_title = $object->title;
        $item_title_url = $object->getHref();
        $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $item_title_url;
        $item_title_link = "<a href='$item_title_baseurl'>" . $item_title . "</a>";
        $followersIds = Engine_Api::_()->getDbTable('follows', 'seaocore')->getFollowers('siteevent_event', $event_id, $viewer->getIdentity());
        $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        foreach ($followersIds as $value) {
            $user_subject = Engine_Api::_()->user()->getUser($value['poster_id']);
            $row = $notificationsTable->createRow();
            $row->user_id = $user_subject->getIdentity();
            $row->subject_type = $viewer->getType();
            $row->subject_id = $viewer->getIdentity();
            $row->type = "$notificationType";
            $row->object_type = $object->getType();
            $row->object_id = $object->getIdentity();
            $row->date = date('Y-m-d H:i:s');
            $row->save();
        }
    }

    //SEND NOTIFICATION TO EVENT LEADER WHEN OWN EVENT LIKE AND COMMENT.
    public function itemCommentLike($subject, $notificationType, $baseOnContentOwner = null, $check_in_array = null) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $item = $subject;
        if ($item->getType() != 'siteevent_event') {
            $item = $item->getParent();
            if ($item->getType() != 'siteevent_event')
                return;
        }

        $getEventOwnersLeaders = $this->getEventOwnersLeaders($item);
        $decoded_notification_param = array();
        $usersVariousContentCreatedArray = array();
        $decoded_notification_param = array();

        foreach ($getEventOwnersLeaders as $value) {

            $user_subject = Engine_Api::_()->user()->getUser($value);
            $row = Engine_Api::_()->getDbTable('membership', 'siteevent')->getRow($item, $user_subject);
            if ($row)
                $decoded_notification_param = $row->notification;

            //PREVIOUS NOTIFICATION IS DELETE.
            $notificationsTable->delete(array('type =?' => "liked", 'object_type = ?' => $subject->getType(), 'object_id = ?' => $subject->getIdentity(), 'user_id = ?' => $viewer_id));
            $notificationsTable->delete(array('type =?' => "commented", 'object_type = ?' => $subject->getType(), 'object_id = ?' => $subject->getIdentity(), 'user_id = ?' => $viewer_id));
            $notificationsTable->delete(array('type =?' => "$notificationType", 'object_type = ?' => $subject->getType(), 'object_id = ?' => $subject->getIdentity(), 'user_id = ?' => $viewer_id));
            if (isset($decoded_notification_param) && !empty($decoded_notification_param['notification']) && (in_array($check_in_array, $decoded_notification_param['action_notification']))) {
                //if($value != $subject->owner_id) {
                $usersVariousContentCreatedArray[] = $value;
                //}
                $row = $notificationsTable->createRow();
                $row->user_id = $user_subject->getIdentity();

                if ($notificationType == 'siteevent_contentcomment') {
                    if ($baseOnContentOwner) {
                        $subjectParent = $subject->getParent();
                        $row->subject_type = $subjectParent->getType();
                        $row->subject_id = $subjectParent->getIdentity();
                    } else {
                        $row->subject_type = $viewer->getType();
                        $row->subject_id = $viewer->getIdentity();
                    }
                } else {
                    $row->subject_type = $viewer->getType();
                    $row->subject_id = $viewer->getIdentity();
                }

                $row->type = "$notificationType";
                $row->object_type = $subject->getType();
                $row->object_id = $subject->getIdentity();
                $row->date = date('Y-m-d H:i:s');
                $row->save();
            }
        }

// 		if(!empty($subject->host) && is_numeric($subject->host) && ($subject->host != $subject->owner_id)) {
// 			$row = Engine_Api::_()->getDbTable('membership', 'siteevent')->getRow($subject, Engine_Api::_()->user()->getUser($subject->host));
//       $decoded_notification_param = $row->notification;
//       if (isset($decoded_notification_param) && !empty($decoded_notification_param['notification']) && in_array($check_in_array, $decoded_notification_param['action_notification'])) {
// 				$notificationsTable->delete(array('type =?' => "$notificationType", 'object_type = ?' => $subject->getType(), 'object_id = ?' => $subject->getIdentity(), 'user_id = ?' => $subject->host));
// 				$user_subject = Engine_Api::_()->user()->getUser($subject->host);
// 				$row = $notificationsTable->createRow();
// 				$row->user_id = $user_subject->getIdentity();
// 				$row->subject_type = $viewer->getType();
// 				$row->subject_id = $viewer->getIdentity();
// 				$row->type = "$notificationType";
// 				$row->object_type = $subject->getType();
// 				$row->object_id = $subject->getIdentity(); 
// 				$row->date = date('Y-m-d H:i:s');
// 				$row->save();
// 			}
// 		}
    }

    /**
     * Returns the amount of weeks into the month a date is
     * @param $date a YYYY-MM-DD formatted date
     * @param $rollover The day on which the week rolls over
     */
    function getWeeks($date, $rollover) {
        $cut = substr($date, 0, 8);
        $daylen = 86400;

        $timestamp = strtotime($date);
        $first = strtotime($cut . "00");
        $elapsed = ($timestamp - $first) / $daylen;

        $i = 1;
        $weeks = 1;

        for ($i; $i <= $elapsed; $i++) {
            $dayfind = $cut . (strlen($i) < 2 ? '0' . $i : $i);
            $daytimestamp = strtotime($dayfind);

            $day = strtolower(date("l", $daytimestamp));

            if ($day == strtolower($rollover))
                $weeks++;
        }

        return $weeks;
    }

    public function getProfileTypeName($option_id) {

        $table_options = Engine_Api::_()->fields()->getTable('siteevent_event', 'options');
        return $table_options->select()
                        ->from($table_options->info('name'), 'label')
                        ->where('option_id = ?', $option_id)
                        ->query()
                        ->fetchColumn();
    }

    //MAKE THE REPEAT EVENT INFO OBJECT.
    public function getRepeatEventInfo($postedValues, $event_id, $editFullEventDate = true, $action = 'save') {
        if (!isset($postedValues['eventrepeat_id']) || $postedValues['eventrepeat_id'] == 'never')
            return;
        if ($event_id) {
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        }

        if (isset($postedValues['eventrepeat_id']) && $postedValues['eventrepeat_id'] == 'daily') {
            if (!isset($postedValues['daily-repeat_interval'])) {
                $eventparams = json_decode($siteevent->repeat_params);
                $postedValues['daily-repeat_interval'] = ($eventparams->repeat_interval / (24 * 60 * 60));
            }
            $repeatEventInfo['repeat_interval'] = $postedValues['daily-repeat_interval'] * 24 * 60 * 60;
            $repeatEventInfo['eventrepeat_type'] = $postedValues['eventrepeat_id'];
            $repeatEventInfo['endtime'] = $postedValues[$postedValues['eventrepeat_id'] . '_repeat_time'];
        } elseif (isset($postedValues['eventrepeat_id']) && $postedValues['eventrepeat_id'] == 'weekly') {
            $weekdays = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday');
            if (!$editFullEventDate) {
                $eventparams = json_decode($siteevent->repeat_params);
                $postedValues['id_weekly-repeat_interval'] = $eventparams->repeat_week;
                foreach ($eventparams->repeat_weekday as $weekday) {
                    $postedValues['weekly-repeat_on_' . $weekdays[$weekday]] = 1;
                }
            }

            $repeatEventInfo['repeat_interval'] = 0;
            $repeatEventInfo['repeat_week'] = $postedValues['id_weekly-repeat_interval'];
            foreach ($weekdays as $key => $weekday) {
                if (isset($postedValues['weekly-repeat_on_' . $weekday])) {
                    $weekdaysSelected[] = $key;
                }
            }
            $repeatEventInfo['repeat_weekday'] = $weekdaysSelected;
            $repeatEventInfo['eventrepeat_type'] = $postedValues['eventrepeat_id'];
            $repeatEventInfo['endtime'] = $postedValues[$postedValues['eventrepeat_id'] . '_repeat_time'];
        } elseif (isset($postedValues['eventrepeat_id']) && $postedValues['eventrepeat_id'] == 'monthly') {
            //CHECK FOR EITHER ABSOLUTE MONTH DAY OR RELATIVE DAY

            $noOfWeeks = array('first' => 1, 'second' => 2, 'third' => 3, 'fourth' => 4, 'fifth' => 5, 'last' => 6);
            $dayOfWeeks = array('monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 7);

            if (!$editFullEventDate) {
                $eventparams = json_decode($siteevent->repeat_params);
                $postedValues['monthly_day'] = 'absolute_day';
                $postedValues['id_monthly-repeat_interval'] = $eventparams->repeat_month;
                if (isset($eventparams->repeat_week)) {
                    $postedValues['id_monthly-relative_day'] = array_search($eventparams->repeat_week, $noOfWeeks);
                    $postedValues['monthly_day'] = 'relative_weekday';
                }
                if (isset($eventparams->repeat_weekday))
                    $postedValues['id_monthly-day_of_week'] = array_search($eventparams->repeat_weekday, $dayOfWeeks);
                if (isset($eventparams->repeat_day))
                    $postedValues['id_monthly-absolute_day'] = $eventparams->repeat_day;
            }

            $repeatEventInfo['repeat_interval'] = 0;
            $repeatEventInfo['eventrepeat_type'] = 'monthly';
            $repeatEventInfo['repeat_month'] = $postedValues['id_monthly-repeat_interval'];
            $repeatEventInfo['endtime'] = $postedValues['monthly_repeat_time'];

            if ($postedValues['monthly_day'] == 'relative_weekday') {
                $repeatEventInfo['repeat_week'] = $noOfWeeks[$postedValues['id_monthly-relative_day']];
                $repeatEventInfo['repeat_weekday'] = $dayOfWeeks[$postedValues['id_monthly-day_of_week']];
            } else {
                $repeatEventInfo['repeat_day'] = $postedValues['id_monthly-absolute_day'];
            }
        } elseif (isset($postedValues['eventrepeat_id']) && $postedValues['eventrepeat_id'] === 'custom') {
            $repeatEventInfo['eventrepeat_type'] = 'custom';
            if ($action == 'display') {
                $customEventType = array();
                if ($event_id) {

                    $repeatEventInfo_temp = json_decode($siteevent->repeat_params, true);
                    if ($repeatEventInfo_temp['eventrepeat_type'] == 'custom' && !$editFullEventDate)
                        $customEventType = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getCustomEventInfo($event_id)->toarray();
                }

                if (!empty($postedValues)) {

                    $j = 0;
                    for ($i = 0; $i <= $postedValues['countcustom_dates']; $i++) {
                        if (isset($postedValues['customdate_' . $i])) {

                            $startenddate = explode("-", $postedValues['customdate_' . $i]);
                            if ($editFullEventDate) {
                                $customEventType[$j]['starttime'] = $startenddate[0];
                                $customEventType[$j]['endtime'] = $startenddate[1];
                                $j++;
                            } else {
                                $customEventType[$i]['starttime'] = $startenddate[0];
                                $customEventType[$i]['endtime'] = $startenddate[1];
                            }
                        }
                    }
                }
                $repeatEventInfo = array_merge($repeatEventInfo, $customEventType);
            }
        } else {
            $repeatEventInfo = '';
        }


        return $repeatEventInfo;
    }

    public function isCreatePrivacy($parent_type = null, $parent_id = null) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($parent_type == 'sitepage_page' && Engine_Api::_()->hasItemType('sitepage_page')) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
            $issecreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
            if (empty($issecreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitebusiness_business' && Engine_Api::_()->hasItemType('sitebusiness_business')) {
            $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
            $issecreate = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'secreate');
            if (empty($issecreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitegroup_group' && Engine_Api::_()->hasItemType('sitegroup_group')) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
            $issecreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'secreate');
            if (empty($issecreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitestore_store' && Engine_Api::_()->hasItemType('sitestore_store')) {
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
            $issecreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'secreate');
            if (empty($issecreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitereview_listing' && Engine_Api::_()->hasItemType('sitereview_listing')) {
            $sitereview = Engine_Api::_()->getItem('sitereview_listing', $parent_id);
            $issecreate = Engine_Api::_()->authorization()->isAllowed($sitereview, $viewer, "event_listtype_$sitereview->listingtype_id");
            if (empty($issecreate)) {
                return false;
            }
        } else {
            if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "create")) {
                return false;
            }
        }

        return true;
    }

    public function isParentEditPrivacy($parent_type = null, $parent_id = null) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($parent_type == 'sitepage_page' && Engine_Api::_()->hasItemType('sitepage_page')) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
            $issecreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
            if (empty($issecreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitebusiness_business' && Engine_Api::_()->hasItemType('sitebusiness_business')) {
            $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
            $issecreate = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'secreate');
            if (empty($issecreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitegroup_group' && Engine_Api::_()->hasItemType('sitegroup_group')) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
            $issecreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'secreate');
            if (empty($issecreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitestore_store' && Engine_Api::_()->hasItemType('sitestore_store')) {
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
            $issecreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'secreate');
            if (empty($issecreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitereview_listing' && Engine_Api::_()->hasItemType('sitereview_listing')) {
            $sitereview = Engine_Api::_()->getItem('sitereview_listing', $parent_id);
            $canEdit = $sitereview->authorization()->isAllowed($viewer, "edit_listtype_$sitereview->listingtype_id");
            if (empty($canEdit)) {
                return false;
            }
        }

        return true;
    }

    public function isParentViewPrivacy($siteevent) {
        $parent = $siteevent->getParent();
        if (!empty($parent)) {
            $parent_type = $parent->getType();
            $parent_id = $parent->getIdentity();
            if ($parent_type == 'sitepage_page' && $parent_id) {
                $sitepage = $parent;
                //PACKAGE BASE PRIYACY START
                if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                    if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent")) {
                        return false;
                    }
                } else {
                    $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
                    if (empty($isPageOwnerAllow)) {
                        return false;
                    }
                }
                //PACKAGE BASE PRIYACY END
                //START MANAGE-ADMIN CHECK
                $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
                if (empty($isManageAdmin)) {
                    return false;
                }

                //PAGE VIEW AUTHORIZATION
                if (!Engine_Api::_()->sitepage()->canViewPage($sitepage)) {
                    return false;
                }
            } elseif ($parent_type == 'sitebusiness_business' && $parent_id) {
                $sitebusiness = $parent;
                //PACKAGE BASE PRIYACY START
                if (Engine_Api::_()->sitebusiness()->hasPackageEnable()) {
                    if (!Engine_Api::_()->sitebusiness()->allowPackageContent($sitebusiness->package_id, "modules", "sitebusinessevent")) {
                        return false;
                    }
                } else {
                    $isBusinessOwnerAllow = Engine_Api::_()->sitebusiness()->isBusinessOwnerAllow($sitebusiness, 'secreate');
                    if (empty($isBusinessOwnerAllow)) {
                        return false;
                    }
                }
                //PACKAGE BASE PRIYACY END
                //START MANAGE-ADMIN CHECK
                $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'view');
                if (empty($isManageAdmin)) {
                    return false;
                }

                //BUSINESS VIEW AUTHORIZATION
                if (!Engine_Api::_()->sitebusiness()->canViewBusiness($sitebusiness)) {
                    return false;
                }
            } elseif ($parent_type == 'sitegroup_group' && $parent_id) {
                $sitegroup = $parent;
                //PACKAGE BASE PRIYACY START
                if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                    if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupevent")) {
                        return false;
                    }
                } else {
                    $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'secreate');
                    if (empty($isGroupOwnerAllow)) {
                        return false;
                    }
                }
                //PACKAGE BASE PRIYACY END
                //START MANAGE-ADMIN CHECK
                $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
                if (empty($isManageAdmin)) {
                    return false;
                }

                //GROUP VIEW AUTHORIZATION
                if (!Engine_Api::_()->sitegroup()->canViewGroup($sitegroup)) {
                    return false;
                }
            } elseif ($parent_type == 'sitestore_store' && $parent_id) {
                $sitestore = $parent;
                //PACKAGE BASE PRIYACY START
                if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                    if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreevent")) {
                        return false;
                    }
                } else {
                    $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'secreate');
                    if (empty($isStoreOwnerAllow)) {
                        return false;
                    }
                }
                //PACKAGE BASE PRIYACY END
                //START MANAGE-ADMIN CHECK
                $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
                if (empty($isManageAdmin)) {
                    return false;
                }

                //STORE VIEW AUTHORIZATION
                if (!Engine_Api::_()->sitestore()->canViewStore($sitestore)) {
                    return false;
                }
            }
        }
        return true;
    }

    //CHECK IF WEEKLY OR MONTHLY EVENT IS CREATED AND THERE IS NO OCCURRENCES FOR THAT EVENT.
    public function checkValidOccurrences($values) {

        $repeat_endtime = strtotime($this->convertDateFormat($_POST[$values['eventrepeat_id'] . '_repeat_time']['date'])) + (24 * 3600 - 1);
        if ($values['eventrepeat_id'] != 'custom') {
            $start = strtotime($values['starttime']);
            $starttime = strtotime($values['starttime']);
            $endtime = strtotime($values['endtime']);
            $durationDiff = $endtime - $starttime;
        }
        $isValidOccurrences = false;
        if ($values['eventrepeat_id'] === 'weekly') {
            $weekdays = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday');
            $weekdays_Temp = $weekdays;
            $firstStartweekday = date("N", $start);
            $skip_firstweekdays = false;

            //get the all events occuerrence dates  
            $nextStartTime = $start;
            $j = 0;
            for ($i = $start; $i <= $repeat_endtime; $i = $nextStartTime) {
                $j++;
                $week_loop = 0;
                foreach ($weekdays_Temp as $key => $weekday) {
                    $params = array();
                    if (isset($_POST['weekly-repeat_on_' . $weekday])) {
                        $week_loop++;
                        //IF THE START WEEKS WEEKDAY IS GREATER THEN THE SELECTED WEEKDAY THEN WE WILL SKIP THAT ONLY FOR FIRST START WEEK. 
                        if (!$skip_firstweekdays && $firstStartweekday > $key) {

                            continue;
                        }
                        $eventstartweekday = date("N", $nextStartTime);

                        if ($skip_firstweekdays == false && $eventstartweekday == $key) {

                            $nextStartTime = $start;
                        } elseif ($skip_firstweekdays == false) {
                            $nextStartTime = $nextStartTime + ($key - $firstStartweekday) * 24 * 3600;
                        } else {

                            if ($week_loop > 1)
                                $nextStartTime = $nextStartTime + (($key - $eventstartweekday)) * 24 * 3600;
                            else
                                $nextStartTime = $nextStartTime + ((7 - $eventstartweekday) + ($_POST['id_weekly-repeat_interval'] - 1) * 7 + $key) * 24 * 3600;
                            $nextStartDate = date("Y-m-d H:i:s", $nextStartTime);
                        }

                        if ($nextStartTime <= $repeat_endtime) {
                            $isValidOccurrences = true;
                            break;
                        }
                    }
                }

                $week_loop = 0;
                $skip_firstweekdays = true;
            }
        } elseif ($values['eventrepeat_id'] === 'monthly') {

            $params = array();

            //CHECK FOR EITHER ABSOLUTE MONTH DAY OR RELATIVE DAY
            $noOfWeeks = array('first' => 1, 'second' => 2, 'third' => 3, 'fourth' => 4, 'fifth' => 5, 'last' => 6);
            $dayOfWeeks = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday');


            $monthly_array = array();
            //HERE WE WILL FIRST CHECK THAT THE EVENT START TIME IS VALID OR NOT.

            $currentmonthEvent = false;

            //get the all events occuerrence dates
            if ($_POST['monthly_day'] != 'relative_weekday') {
                $starttime_DayMonth = date("j", $start);
                $current_month = date("Ym", time());
                $starttime_month = date("Ym", $start);
                if ($_POST['id_monthly-absolute_day'] >= $starttime_DayMonth && $current_month == $starttime_month)
                    $currentmonthEvent = true;
                for ($i = $start; $i <= $repeat_endtime; $i = $nextStartTime) {
                    $dayofMonth = date("j", $i);
                    if ($currentmonthEvent) {
                        $nextStartTime = strtotime(Engine_Api::_()->siteevent()->date_add(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, 0), ($_POST['id_monthly-absolute_day'] - $dayofMonth)));
                    } elseif (isset($_POST['action']) && $_POST['action'] == 'editdates') {
                        $nextStartTime = strtotime(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, $_POST['id_monthly-repeat_interval']));
                    } else {
                        $nextStartTime = strtotime(Engine_Api::_()->siteevent()->date_add(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, $_POST['id_monthly-repeat_interval']), ($_POST['id_monthly-absolute_day'] - $dayofMonth)));
                    }

                    if ($nextStartTime <= $repeat_endtime) {
                        $isValidOccurrences = true;
                        break;
                    }

                    $currentmonthEvent = false;
                }
            } else {

                $starttime_Week = Engine_Api::_()->siteevent()->getWeeks($values['starttime'], 'monday');
                $starttime_Weekday = date("N", $start);
                if ($starttime_Week < $noOfWeeks[$_POST['id_monthly-relative_day']] || ($starttime_Week == $noOfWeeks[$_POST['id_monthly-relative_day']] && $starttime_Weekday <= array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks)))
                    $currentmonthEvent = true;


                for ($i = $start; $i <= $repeat_endtime; $i = $nextStartTime) {
                    $params = array();
                    $dayofMonth = date("j", $i);
                    if ($currentmonthEvent) {
                        $repeatMonthStartDate = Engine_Api::_()->siteevent()->date_add(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, 0), ('01' - $dayofMonth));
                    } else {

                        $repeatMonthStartDate = Engine_Api::_()->siteevent()->date_add(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, $_POST['id_monthly-repeat_interval']), ('01' - $dayofMonth));
                    }
                    if ($_POST['id_monthly-relative_day'] == 'last') {
                        $days_in_month = date('t', strtotime($repeatMonthStartDate));
                        //GET THE LAST DATE OF MONTH
                        $getRepeatTime = explode(" ", $repeatMonthStartDate);
                        $getTimeString = explode(":", $getRepeatTime[1]);

                        //GET THE LAST DATE OF MONTH
                        $lastDateofMonth = date("Y-m-d H:i:s", mktime($getTimeString[0], $getTimeString[1], $getTimeString[2], date("m", strtotime($repeatMonthStartDate)), $days_in_month, date("Y", strtotime($repeatMonthStartDate))));

                        $lastday_Weekday = date("N", strtotime($lastDateofMonth));
                        $totalnoofWeeks = ceil(date('j', strtotime($lastDateofMonth)) / 7);
                        if ($lastday_Weekday < array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks)) {
                            $day_decrease = -((7 - array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks)) + $lastday_Weekday);
                        } else if ($lastday_Weekday > array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks)) {
                            $day_decrease = -( $lastday_Weekday - array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks));
                        } else
                            $day_decrease = 0;

                        if ($day_decrease != 0)
                            $nextStartDate = Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", strtotime($lastDateofMonth)), $day_decrease, 0);
                        else
                            $nextStartDate = $lastDateofMonth;
                    }
                    else {

                        $repeatMonthStartTime = strtotime($repeatMonthStartDate);

                        $repeatMonthStartWeekday = date("N", $repeatMonthStartTime);

                        if ($repeatMonthStartWeekday <= array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks))
                            $month_day = array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks) - $repeatMonthStartWeekday;
                        else
                            $month_day = (7 - $repeatMonthStartWeekday) + array_search($_POST['id_monthly-day_of_week'], $dayOfWeeks);


                        $nextStartDate = Engine_Api::_()->siteevent()->date_add($repeatMonthStartDate, (($month_day) + ($noOfWeeks[$_POST['id_monthly-relative_day']] - 1) * 7));
                    }
                    $nextStartTime = strtotime($nextStartDate);
                    //IF START TIME WEEK IS NOT EQUAL TO THE REQUIRED WEEK THEN CONTINUE.CASE: IF WEEK IS FIFTH WEEK.

                    $starttime_Week = Engine_Api::_()->siteevent()->getWeeks($nextStartDate, 'monday');
                    if ($_POST['id_monthly-relative_day'] != 'last') {

                        if ($starttime_Week < $noOfWeeks[$_POST['id_monthly-relative_day']]) {
                            continue;
                        }
                    }


                    if ($repeat_endtime >= $nextStartTime) {
                        $isValidOccurrences = true;
                        break;
                    }

                    $currentmonthEvent = false;
                }
            }
        }
        return $isValidOccurrences;
    }

    //Re-Order the custom dates
    public function reorderCustomDates() {
        //reorder the custom event datees.
        $starttime_array = array();
        $custome_dates = array();
        for ($i = 0; $i <= $_POST['countcustom_dates']; $i++) {
            if (isset($_POST['customdate_' . $i])) {
                $startenddate = explode("-", $_POST['customdate_' . $i]);
                $nextStartDate = $startenddate[0];
                $starttime_array['customdate_' . $i] = strtotime($this->convertDateFormat($nextStartDate));
                $custome_dates['customdate_' . $i] = $_POST['customdate_' . $i];
            }
        }

        asort($starttime_array);
        for ($i = 0; $i <= $_POST['countcustom_dates']; $i++) {
            foreach ($starttime_array as $key => $starttime) {
                if (isset($_POST['customdate_' . $i])) {
                    $_POST['customdate_' . $i] = $custome_dates[$key];
                    unset($starttime_array[$key]);
                    break;
                }
            }
        }
    }

    //RETURN THE ARRAY OF ALL OCCURRENCE DATES OF AN EVENT.
    public function getAllOccurrenceDate($datesInfo, $noAllOccurrencesField = false) {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');
        if (!$noAllOccurrencesField) {
            $filter_dates['all'] = $view->translate('All occurrences of this event');
        }
        $datesInfo = $datesInfo->toarray();
        foreach ($datesInfo as $date):
            $startDateObject = new Zend_Date(strtotime($date['starttime']));
            $endDateObject = new Zend_Date(strtotime($date['endtime']));
            if ($view->viewer() && $view->viewer()->getIdentity()) {
                $tz = $view->viewer()->timezone;
                $startDateObject->setTimezone($tz);
                $endDateObject->setTimezone($tz);
            }

            $date['starttime'] = $view->locale()->toEventDateTime($date['starttime']);
            $date['endtime'] = $view->locale()->toEventDateTime($date['endtime']);
            if ($startDateObject->toString('y-MM-dd') == $endDateObject->toString('y-MM-dd')) {

                $optionText = $view->locale()->toEventDateTime($startDateObject, array('format' => 'MMM'));


                $optionText = $view->locale()->toEventDateTime($startDateObject, array('format' => 'MMM')) . ' ' . $view->locale()->toEventDateTime($startDateObject, array('format' => 'd')) . ', ' . $view->locale()->toEventDateTime($startDateObject, array('format' => 'yyyy')) . ' ' . $view->locale()->toEventTime($startDateObject, array('size' => $datetimeFormat)) . ' - ' . $view->locale()->toEventTime($endDateObject, array('size' => $datetimeFormat));
            } else {

                $optionText = $view->locale()->toEventDateTime($startDateObject, array('format' => 'MMM')) . ' ' . $view->locale()->toEventDateTime($startDateObject, array('format' => 'd')) . ', ' . $view->locale()->toEventDateTime($startDateObject, array('format' => 'yyyy')) . ' ' . $view->locale()->toEventTime($startDateObject, array('size' => $datetimeFormat)) . ' - ' . $view->locale()->toEventDateTime($endDateObject, array('format' => 'MMM')) . ' ' . $view->locale()->toEventDateTime($endDateObject, array('format' => 'd')) . ', ' . $view->locale()->toEventDateTime($endDateObject, array('format' => 'yyyy')) . ' ' . $view->locale()->toEventTime($endDateObject, array('size' => $datetimeFormat));
            }

            $filter_dates[$date['occurrence_id']] = $optionText;
        endforeach;
        return $filter_dates;
    }

    public function getActivtyFeedType($event, $type, $user = null) {
        $parent = $event->getParent();
        if ($parent->getType() == 'user')
            return $type;
        $tempType = $type . '_parent';
        $typeInfo = Engine_Api::_()->getDbtable('actions', 'activity')->getActionType($tempType);

        if (!$typeInfo || !$typeInfo->enabled) {
            return $type;
        }

        if (!$user)
            $user = Engine_Api::_()->user()->getViewer();

        $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
        $name = 'siteevent_event_leader_owner_' . $parent->getType();

        if ($settingsCoreApi->$name && $parent->isOwner($user))
            $type = $tempType;
        return $type;
    }

    //get all occurrence date.
    public function convertDateFormat($date) {
        $date_orig = $date;
        //IF THE LOCALE DATE FORMAT IS DMY THEN CONVERT IT TO MDY.
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $dateFormat = $view->locale()->useDateLocaleFormat();
        if ($dateFormat == 'dmy') {
            $date = explode("/", $date);
            if (count($date) == 3) {
                $date = $date[1] . '/' . $date[0] . '/' . $date[2];
            } else
                $date = str_replace("/", "-", $date_orig);
        }
        //CHECK IF THE COVERTTED DATE RETURNS TRUE OR FALSE.
        if (!strtotime($date))
            return $date_orig;
        return $date;
    }

    //RETURNS DATE OR TIME DEPEND ON THE $DATETIME PARAMTER DATABASE TO CURRENT USER
    public function dbToUserDateTime($dateparams = array(), $dateTime = 'date') {

        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
        if ($viewer->getIdentity()) {
            $timezone = $viewer->timezone;
        }

        if (isset($dateparams['starttime']))
            $dateparams['starttime'] = strtotime($dateparams['starttime']);
        if (isset($dateparams['endtime']))
            $dateparams['endtime'] = strtotime($dateparams['endtime']);
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($timezone);
        if (isset($dateparams['starttime']))
            $dateparams['starttime'] = date("Y-m-d H:i:s", $dateparams['starttime']);
        if (isset($dateparams['endtime']))
            $dateparams['endtime'] = date("Y-m-d H:i:s", $dateparams['endtime']);
        date_default_timezone_set($oldTz);
        if ($dateTime == 'time') {
            isset($dateparams['starttime']) ? $dateparams['starttime'] = strtotime($dateparams['starttime']) : '';
            isset($dateparams['endtime']) ? $dateparams['endtime'] = strtotime($dateparams['endtime']) : '';
        }
        return $dateparams;
    }

    //RETURNS DATE OR TIME DEPEND ON THE $DATETIME PARAMTER DATABASE TO CURRENT USER
    public function userToDbDateTime($dateparams = array(), $dateTime = 'date') {
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
        if ($viewer->getIdentity()) {
            $timezone = $viewer->timezone;
        }
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($timezone);
        if (isset($dateparams['starttime']))
            $dateparams['starttime'] = strtotime($dateparams['starttime']);
        if (isset($dateparams['endtime']))
            $dateparams['endtime'] = strtotime($dateparams['endtime']);
        date_default_timezone_set($oldTz);
        if (isset($dateparams['starttime']))
            $dateparams['starttime'] = date("Y-m-d H:i:s", $dateparams['starttime']);
        if (isset($dateparams['endtime']))
            $dateparams['endtime'] = date("Y-m-d H:i:s", $dateparams['endtime']);
        return $dateparams;
    }

    public function getModulelabel($title) {
        $menuitemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
        $selectMenuitemsTable = $menuitemsTable->select()->where('name =?', "core_admin_main_plugins_$title");
        $resultMenuitems = $menuitemsTable->fetchRow($selectMenuitemsTable);
        return $resultMenuitems;
    }

    /**
     * Check package is enable or not for site
     * @return bool
     */
    public function hasPackageEnable() {

        if (!Zend_Registry::isRegistered('hasPackageEnable')) {
            $hasPackageEnable = (Engine_Api::_()->hasModuleBootstrap('siteeventpaid') && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.package.setting', 1)) ? true : false;
            Zend_Registry::set('hasPackageEnable', $hasPackageEnable);
        }

        return Zend_Registry::get('hasPackageEnable');
    }

    public function sendNotificationToHost($event_id) {
        if (empty($event_id)) {
            return;
        }
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $httpHost = _ENGINE_SSL ? 'https://' : 'http://';
        $viewerGetTitle = $viewer->getTitle();
        $event_title_with_link = '<a href = ' . $httpHost . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $siteevent->event_id, 'slug' => $siteevent->getSlug()), 'siteevent_entry_view') . ">$siteevent->title</a>";

        $sender_link = '<a href = ' . $httpHost . $_SERVER['HTTP_HOST'] . $viewer->getHref() . ">$viewerGetTitle</a>";

        $event_url = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $siteevent->event_id, 'slug' => $siteevent->getSlug()), 'siteevent_entry_view');

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.host', 1) && $siteevent->host_type == 'user' && is_numeric($siteevent->host_id) && $viewer_id != $siteevent->host_id) {
            $newHost = $siteevent->getHost();

            if ($newHost) {

                if (!$siteevent->membership()->getRow($newHost)) {
                    $siteevent->membership()->addMember($newHost)->setUserApproved($newHost);
                    $row = $siteevent->membership()->getRow($newHost);
                    $row->rsvp = 2;
                    $row->save();

                    //UPDATE THE MEMBER COUNT IN EVENT TABLE
                    $siteevent->member_count = $siteevent->membership()->getMemberCount();
                    $siteevent->save();
                }
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($newHost->email, 'SITEEVENT_HOST_EMAIL', array(
                    'event_title_with_link' => $event_title_with_link,
                    'sender' => $sender_link,
                    'event_url' => $event_url,
                    'queue' => true
                ));

                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $occurrence_id = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($siteevent->event_id, 1);
                $notifyApi->addNotification($newHost, $viewer, $siteevent, 'siteevent_host', array('occurrence_id' => $occurrence_id));
                $notifyApi->addNotification($newHost, $viewer, $siteevent, 'siteevent_member', array('occurrence_id' => $occurrence_id));
            }
        } elseif ($siteevent->host_type == 'sitepage_page') {
            $occurrence_id = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($siteevent->event_id, 1);
            $manageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdmin($siteevent->host_id, $viewer_id);
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

            foreach ($manageAdmins as $admins) {
                $newHost = Engine_Api::_()->getItem('user', $admins['user_id']);
                $sitepage = Engine_Api::_()->getItem('sitepage_page', $admins['page_id']);
                $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $sitepage->getHref();
                $item_title_link = "<a href='$item_title_baseurl'>" . $sitepage->getTitle() . "</a>";
                $notifyApi->addNotification($newHost, $viewer, $siteevent, 'siteevent_page_host', array('occurrence_id' => $occurrence_id, 'page' => $item_title_link));
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($newHost->email, 'SITEEVENT_PAGE_HOST', array(
                    'page_title_with_link' => $item_title_link,
                    'event_title_with_link' => $event_title_with_link,
                    'sender' => $sender_link,
                    'event_url' => $event_url,
                    'queue' => true
                ));
            }
        } elseif ($siteevent->host_type == 'sitebusiness_business') {
            $occurrence_id = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($siteevent->event_id, 1);
            $manageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitebusiness')->getManageAdmin($siteevent->host_id, $viewer_id);
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

            foreach ($manageAdmins as $admins) {
                $newHost = Engine_Api::_()->getItem('user', $admins['user_id']);
                $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $admins['business_id']);
                $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $sitebusiness->getHref();
                $item_title_link = "<a href='$item_title_baseurl'>" . $sitebusiness->getTitle() . "</a>";
                $notifyApi->addNotification($newHost, $viewer, $siteevent, 'siteevent_business_host', array('occurrence_id' => $occurrence_id, 'business' => $item_title_link));

                Engine_Api::_()->getApi('mail', 'core')->sendSystem($newHost->email, 'SITEEVENT_BUSINESS_HOST', array(
                    'business_title_with_link' => $item_title_link,
                    'event_title_with_link' => $event_title_with_link,
                    'sender' => $sender_link,
                    'event_url' => $event_url,
                    'queue' => true
                ));
            }
        } elseif ($siteevent->host_type == 'sitegroup_group') {
            $occurrence_id = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($siteevent->event_id, 1);
            $manageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdmin($siteevent->host_id, $viewer_id);
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

            foreach ($manageAdmins as $admins) {
                $newHost = Engine_Api::_()->getItem('user', $admins['user_id']);
                $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $admins['group_id']);
                $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $sitegroup->getHref();
                $item_title_link = "<a href='$item_title_baseurl'>" . $sitegroup->getTitle() . "</a>";
                $notifyApi->addNotification($newHost, $viewer, $siteevent, 'siteevent_group_host', array('occurrence_id' => $occurrence_id, 'group' => $item_title_link));
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($newHost->email, 'SITEEVENT_GROUP_HOST', array(
                    'group_title_with_link' => $item_title_link,
                    'event_title_with_link' => $event_title_with_link,
                    'sender' => $sender_link,
                    'event_url' => $event_url,
                    'queue' => true
                ));
            }
        } elseif ($siteevent->host_type == 'sitestore_store') {
            $occurrence_id = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($siteevent->event_id, 1);
            $manageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getManageAdmin($siteevent->host_id, $viewer_id);
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

            foreach ($manageAdmins as $admins) {
                $newHost = Engine_Api::_()->getItem('user', $admins['user_id']);
                $sitestore = Engine_Api::_()->getItem('sitestore_store', $admins['store_id']);
                $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $sitestore->getHref();
                $item_title_link = "<a href='$item_title_baseurl'>" . $sitestore->getTitle() . "</a>";
                $notifyApi->addNotification($newHost, $viewer, $siteevent, 'siteevent_store_host', array('occurrence_id' => $occurrence_id, 'store' => $item_title_link));

                Engine_Api::_()->getApi('mail', 'core')->sendSystem($newHost->email, 'SITEEVENT_STORE_HOST', array(
                    'store_title_with_link' => $item_title_link,
                    'event_title_with_link' => $event_title_with_link,
                    'sender' => $sender_link,
                    'event_url' => $event_url,
                    'queue' => true
                ));
            }
        }
    }

    /**
     * Send emails for perticuler event
     * @params $type : which mail send
     * $params $eventId : Id of event
     * */
    public function sendMail($type, $eventId) {

        if (empty($type) || empty($eventId)) {
            return;
        }
        $event = Engine_Api::_()->getItem('siteevent_event', $eventId);
        $event_url = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $eventId, 'slug' => $event->getSlug()), 'siteevent_entry_view');
        $mail_template = null;
        if (!empty($event)) {

            $owner = Engine_Api::_()->user()->getUser($event->owner_id);
            switch ($type) {
                case "APPROVAL_PENDING":
                    $mail_template = 'siteevent_event_approval_pending';
                    break;
                case "EXPIRED":
                    if (!$this->hasPackageEnable())
                        return;
                    if ($event->getPackage()->isFree())
                        $mail_template = 'siteevent_event_expired';
                    else
                        $mail_template = 'siteevent_event_renew';
                    break;
                case "OVERDUE":
                    $mail_template = 'siteevent_event_overdue';
                    break;
                case "CANCELLED":
                    $mail_template = 'siteevent_event_cancelled';
                    break;
                case "ACTIVE":
                    $mail_template = 'siteevent_event_active';
                    break;
                case "PENDING":
                    $mail_template = 'siteevent_event_pending';
                    break;
                case "REFUNDED":
                    $mail_template = 'siteevent_event_refunded';
                    break;
                case "APPROVED":
                    $mail_template = 'siteevent_event_approved';
                    break;
                case "DISAPPROVED":
                    $mail_template = 'siteevent_event_disapproved';
                    break;
                case "DECLINED":
                    $mail_template = 'siteevent_event_declined';
                    break;
                case "RECURRENCE":
                    $mail_template = 'siteevent_event_recurrence';
                    break;
            }
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner, $mail_template, array(
                'site_title' => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1),
                'event_title' => ucfirst($event->getTitle()),
                'event_description' => ucfirst($event->body),
                'event_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $eventId, 'slug' => $event->getSlug()), "siteevent_entry_view", true) . '"  >' . ucfirst($event->getTitle()) . ' </a>',
                'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $eventId, 'slug' => $event->getSlug()), "siteevent_entry_view", true),
            ));
        }
    }

    /**
     * Check ticket is enable or not for site
     * @return bool
     */
    public function hasTicketEnable() {

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventticket'))
            return (bool) 0;

        $ticket = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1);
        if (!empty($ticket))
            return (bool) 1;
        else
            return (bool) 0;
    }

    public function getGoogleCalenderLink($obj) {
        $siteevent = $obj;
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $eventdateinfo = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getEventDate($siteevent->getIdentity(), 0);
        $start_time = strtotime($eventdateinfo['starttime']);
        $end_time = strtotime($eventdateinfo['endtime']);
        $params = array('url' => 'https://www.google.com/calendar/event?',
            'title' => $siteevent->getTitle(),
            'datetime' => array('start' => date('Ymd\THis\Z', $start_time), 'end' => date('Ymd\THis\Z', $end_time)),
            'location' => $siteevent->location,
            'description' => $siteevent->body,
            'appendToURL' => 'action=TEMPLATE&amp;trp=true&amp;sprop=www.gothamvolleyball.org&amp;sprop=name:Gotham%20Volleyball',
            'linkTarget' => '_blank',
            'showImage' => 'true',
            'showText' => 'false',
            'linkImage' => '//www.google.com/calendar/images/ext/gc_button1.gif',
            'linkText' => $view->translate("Google Calender"),
            'anchorTitle' => $view->translate("Add to Google Calendar"),
            'target' => '_blank'
        );
        return '<a title="' . $params['anchorTitle'] . '" class="addtogooglecalendar" target="' . $params['target'] . '" href="' . $params['url'] . 'action=TEMPLATE&text=' . $params['title'] . '&dates=' . $params['datetime']['start'] . '/' . $params['datetime']['end'] . '&location=' . $params['location'] . '&details=' . $params['description'] . '&trp=true&sprop=website:http://www.gothamvolleyball.org&sprop=name:Gotham Volleyball"><span class="seao_icon_google">&nbsp;</span>' . $params['linkText'] . '</a>';
    }

    public function getYahooCalenderLink($obj) {

        $siteevent = $obj;
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $eventdateinfo = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getEventDate($siteevent->getIdentity(), 0);
        $start_time = strtotime($eventdateinfo['starttime']);
        $end_time = strtotime($eventdateinfo['endtime']);
        $params = array('url' => 'https://www.google.com/calendar/event?',
            'title' => $siteevent->getTitle(),
            'datetime' => array('start' => date('Ymd\THis\Z', $start_time), 'end' => date('Ymd\THis\Z', $end_time)),
            'location' => $siteevent->location,
            'description' => $siteevent->body,
            'linkTarget' => '_blank',
            'showImage' => 'true',
            'showText' => 'false',
            //'linkImage' => '//www.google.com/calendar/images/ext/gc_button1.gif',
            'linkText' => $view->translate("Yahoo! Calender"),
            'anchorTitle' => $view->translate("Add to Yahoo! Calendar"),
            'target' => '_blank'
        );
        $link = "http://calendar.yahoo.com/?v=60&ST=" . $params['datetime']['start'] . "&ET=" . $params['datetime']['end'] . "&REM1=01d&TITLE=" . urlencode($siteevent->getTitle()) . "&VIEW=d&in_loc=" . $params['location'];
        return '<a title="' . $params['anchorTitle'] . '" target="' . $params['target'] . '" href="' . $link . '"><span class="seao_icon_yahoo">&nbsp;</span>' . $params['linkText'] . '</a>';
    }

    public function isTicketBasedEvent() {

        if (!Zend_Registry::isRegistered('ticketCreationAllowed')) {

            $ticketCreationAllowed = (Engine_Api::_()->hasModuleBootstrap('siteeventticket') && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1)) ? true : false;
            Zend_Registry::set('ticketCreationAllowed', $ticketCreationAllowed);
        }

        return Zend_Registry::get('ticketCreationAllowed');
    }

    /**
     * Return count
     *
     * @param string $tablename
     * @param string $modulename
     * @param int $page_id
     * @param int $title_count
     * @return paginator
     */
    public function getTotalCount($event_id, $modulename, $tablename) {

        $table = Engine_Api::_()->getDbtable($tablename, $modulename);
        $count = 0;
        $count = $table
                ->select()
                ->from($table->info('name'), array('count(*) as count'))
                ->where("parent_type = ?", 'siteevent_event')
                ->where("parent_id =?", $event_id)
                ->query()
                ->fetchColumn();

        return $count;
    }

}
