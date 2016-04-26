<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideoview
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2012-06-028 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideoview_Api_Core extends Core_Api_Abstract {

  protected $_table;

  public function getPrevVideo($current_video, $params=array()) {
    return $this->getVideo($current_video, $params, -1);
  }

  public function getNextVideo($current_video, $params=array()) {
    return $this->getVideo($current_video, $params, 1);
  }

  public function getVideo($collectible, $params=array(), $direction) {
    if (!isset($params['offset']) || empty($params['offset']))
      $index = $this->getCollectibleIndex($collectible, $params);
    else
      $index = $params['offset'];


    $index = $index + (int) $direction;

    $select = $this->getCollectibleSql($collectible, $params);

    // Check index bounds
    $count = $params['count'];
    if ($index >= $count) {
      $index -= $count;
    } else if ($index < 0) {
      $index += $count;
    }

    $select->limit(1, (int) $index);

    $rowset = $this->_table->fetchAll($select);
    if (null === $rowset) {
      // @todo throw?
      return null;
    }
    $row = $rowset->current();
    return Engine_Api::_()->getItem($collectible->getType(), $row->video_id);
  }

  /**
   * get the current video index
   */
  public function getCollectibleIndex($collectible, $params=array()) {
    $select = $this->getCollectibleSql($collectible, $params);

    $i = 0;
    $index = 0;
    if (isset($params['count']) && !empty($params['count'])) {
      $select->limit($params['count']);
    }
    $rows = $this->_table->fetchAll($select);
    $totalCount = $rows->count();
    foreach ($rows as $row) {
      if ($row->getIdentity() == $collectible->getIdentity()) {
        $index = $i;
        break;
      }
      $i++;
    }
    return $index;
  }

  /**
   * get the current video index sql
   */
  public function getCollectibleSql($collectible, $params=array()) {
    $collectibleType = $collectible->getType();
    $this->_table = $table = Engine_Api::_()->getItemTable($collectibleType);
    $tableName = $table->info('name');
    $col = current($table->info("primary"));
    $metaDataInfo = $table->info('metadata');
    if (isset($metaDataInfo['user_id'])) {
      $owner_id = 'user_id';
    } else {
      $owner_id = 'owner_id';
    }
    $select = $table->select()
            ->from($tableName, $col);
    $select
            ->where('search = ?', true)
            ->where('status = ?', 1);

    $type = $params['type'];
    // Page video
    if ($collectibleType == 'sitepagevideo_video') {
      $owner_id = 'page_id';
      if (!in_array($type, array('same-poster', 'same-tags', 'also-liked')) && strstr($params['subject_guid'], 'sitepage_page_')) {
        $select
                ->where($tableName . '.page_id = ?', $collectible->page_id);
      }
      // Page profile Page
      if ($type == 'page-profile') {
        if (!empty($params['search_text'])) {
          $select->where($tableName . ".title LIKE ? OR " . $tableName . ".description LIKE ?", '%' . $params['search_text'] . '%');
        }

        if (!empty($params['my_video'])) {
          $select->where("$tableName .owner_id = ?", $collectible->owner_id);
        }
        if (empty($params['browse'])) {
          $select->order("$tableName.highlighted DESC");
        } elseif ($params['browse'] == 'highlighted') {
          $select->where($tableName . '.highlighted = ?', 1);
        } elseif ($params['browse'] == 'featured') {
          $select->where($tableName . '.featured = ?', 1);
        } else {
          $browse = $params['browse'];
          if ($browse != 'creation_date')
            $select->order("$tableName.$browse DESC");
        }
        $select->order("$tableName.video_id DESC");
        return $select;
      }
    } /* Business Video */ elseif ($collectibleType == 'sitebusinessvideo_video') {
      $owner_id = 'business_id';
      if (!in_array($type, array('same-poster', 'same-tags', 'also-liked')) && strstr($params['subject_guid'], 'sitebusiness_business_')) {
        $select
                ->where($tableName . '.business_id = ?', $collectible->business_id);
      }
      // Business profile Page
      if ($type == 'business-profile') {
        if (!empty($params['search_text'])) {
          $select->where($tableName . ".title LIKE ? OR " . $tableName . ".description LIKE ?", '%' . $params['search_text'] . '%');
        }

        if (!empty($params['my_video'])) {
          $select->where("$tableName .owner_id = ?", $collectible->owner_id);
        }
        if (empty($params['browse'])) {
          $select->order("$tableName.highlighted DESC");
        } elseif ($params['browse'] == 'highlighted') {
          $select->where($tableName . '.highlighted = ?', 1);
        } elseif ($params['browse'] == 'featured') {
          $select->where($tableName . '.featured = ?', 1);
        } else {
          $browse = $params['browse'];
          if ($browse != 'creation_date')
            $select->order("$tableName.$browse DESC");
        }
        $select->order("$tableName.video_id DESC");
        return $select;
      }
    }/* Directory Group Video */ else if ($collectibleType == 'sitegroupvideo_video') {
      $owner_id = 'group_id';
      if (!in_array($type, array('same-poster', 'same-tags', 'also-liked')) && strstr($params['subject_guid'], 'sitegroup_group_')) {
        $select
                ->where($tableName . '.group_id = ?', $collectible->group_id);
      }
      // Page profile Page
      if ($type == 'group-profile') {
        if (!empty($params['search_text'])) {
          $select->where($tableName . ".title LIKE ? OR " . $tableName . ".description LIKE ?", '%' . $params['search_text'] . '%');
        }

        if (!empty($params['my_video'])) {
          $select->where("$tableName .owner_id = ?", $collectible->owner_id);
        }
        if (empty($params['browse'])) {
          $select->order("$tableName.highlighted DESC");
        } elseif ($params['browse'] == 'highlighted') {
          $select->where($tableName . '.highlighted = ?', 1);
        } elseif ($params['browse'] == 'featured') {
          $select->where($tableName . '.featured = ?', 1);
        } else {
          $browse = $params['browse'];
          if ($browse != 'creation_date')
            $select->order("$tableName.$browse DESC");
        }
        $select->order("$tableName.video_id DESC");
        return $select;
      }
    }/* Directory Store Video */ else if ($collectibleType == 'sitestorevideo_video') {
      $owner_id = 'store_id';
      if (!in_array($type, array('same-poster', 'same-tags', 'also-liked')) && strstr($params['subject_guid'], 'sitestore_store_')) {
        $select
                ->where($tableName . '.store_id = ?', $collectible->store_id);
      }
      // Page profile Page
      if ($type == 'store-profile') {
        if (!empty($params['search_text'])) {
          $select->where($tableName . ".title LIKE ? OR " . $tableName . ".description LIKE ?", '%' . $params['search_text'] . '%');
        }

        if (!empty($params['my_video'])) {
          $select->where("$tableName .owner_id = ?", $collectible->owner_id);
        }
        if (empty($params['browse'])) {
          $select->order("$tableName.highlighted DESC");
        } elseif ($params['browse'] == 'highlighted') {
          $select->where($tableName . '.highlighted = ?', 1);
        } elseif ($params['browse'] == 'featured') {
          $select->where($tableName . '.featured = ?', 1);
        } else {
          $browse = $params['browse'];
          if ($browse != 'creation_date')
            $select->order("$tableName.$browse DESC");
        }
        $select->order("$tableName.video_id DESC");
        return $select;
      }
    } /* Ynvideo Video Playlist */ elseif (strstr($params['subject_guid'], 'ynvideo_playlist_')) {
      $subject_guid = $params['subject_guid'];
      if (!empty($subject_guid))
        $subject = Engine_Api::_()->getItemByGuid($subject_guid);

      $playlistAssocTbl = Engine_Api::_()->getDbTable('playlistassoc', 'ynvideo');
      $playlistAssocTblName = $playlistAssocTbl->info('name');
      $select->setIntegrityCheck(false);
      $select->join($playlistAssocTblName, "$playlistAssocTblName.video_id = $tableName.video_id")
              ->where("$playlistAssocTblName.playlist_id = ?", $subject->getIdentity());
      $select->order("$tableName.creation_date DESC");
      return $select;
    }

    switch ($type) {
      case 'most-viewed':
        $select->order($tableName . '.view_count DESC');
        break;
      case 'recent':
        $select->order("$tableName.video_id DESC");
        break;
      case 'modified':
        $select->order("$tableName.modified_date DESC");
        break;
      case 'my-favorite':
      case 'profile-favorite':
        $favoriteTable = Engine_Api::_()->getDbTable('favorites', 'ynvideo');
        $favoriteTableName = $favoriteTable->info('name');

        $select->setIntegrityCheck(false)
                ->join($favoriteTableName, $favoriteTableName . ".video_id = " . $tableName . ".video_id");

        if ($type == 'my-favorite') {
          $viewer = Engine_Api::_()->user()->getViewer();
          $select->where("$favoriteTableName.user_id = ?", $viewer->getIdentity());
        } else if ($type == 'profile-favorite') {
          $subject_guid = $params['subject_guid'];
          if (!empty($subject_guid))
            $subject = Engine_Api::_()->getItemByGuid($subject_guid);

          $select->where("$favoriteTableName.user_id = ?", $subject->getIdentity());
        }
        $select->order("$tableName.creation_date DESC");
        break;
      case 'profile-listing':
        $subject_guid = $params['subject_guid'];
        if (!empty($subject_guid))
          $subject = Engine_Api::_()->getItemByGuid($subject_guid);

        $listvideoTable = Engine_Api::_()->getDbTable('clasfvideos', 'list');
        $listvideoTableName = $listvideoTable->info('name');
        $select = $select
                ->setIntegrityCheck(false)
                ->join($listvideoTableName, $tableName . '.video_id = ' . $listvideoTableName . '.video_id', array())
                ->group($tableName . '.video_id')
                ->where($listvideoTableName . '.listing_id  = ?', $subject->getIdentity());

        break;
      case 'profile-recipe':
        $subject_guid = $params['subject_guid'];
        if (!empty($subject_guid))
          $subject = Engine_Api::_()->getItemByGuid($subject_guid);
        $listvideoTable = Engine_Api::_()->getDbTable('clasfvideos', 'recipe');
        $listvideoTableName = $listvideoTable->info('name');
        $select = $select
                ->setIntegrityCheck(false)
                ->join($listvideoTableName, $tableName . '.video_id = ' . $listvideoTableName . '.video_id', array())
                ->group($tableName . '.video_id')
                ->where($listvideoTableName . '.recipe_id  = ?', $subject->getIdentity());

        break;
         case 'profile-sitereview':
        $subject_guid = $params['subject_guid'];
        if (!empty($subject_guid))
          $subject = Engine_Api::_()->getItemByGuid($subject_guid);
        if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereview.show.video', 1)){
        $listvideoTable = Engine_Api::_()->getDbTable('clasfvideos', 'sitereview');
        $listvideoTableName = $listvideoTable->info('name');
        $select = $select
                ->setIntegrityCheck(false)
                ->join($listvideoTableName, $tableName . '.video_id = ' . $listvideoTableName . '.video_id', array())
                ->group($tableName . '.video_id')
                ->where($listvideoTableName . '.listing_id  = ?', $subject->getIdentity());
        }else{
          $select->where($tableName . '.listing_id  = ?', $subject->getIdentity());
        }
        break;
         case 'profile-sitestoreproduct':
        $subject_guid = $params['subject_guid'];
        if (!empty($subject_guid))
          $subject = Engine_Api::_()->getItemByGuid($subject_guid);
        if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.video', 1)){
        $listvideoTable = Engine_Api::_()->getDbTable('clasfvideos', 'sitestoreproduct');
        $listvideoTableName = $listvideoTable->info('name');
        $select = $select
                ->setIntegrityCheck(false)
                ->join($listvideoTableName, $tableName . '.video_id = ' . $listvideoTableName . '.video_id', array())
                ->group($tableName . '.video_id')
                ->where($listvideoTableName . '.product_id  = ?', $subject->getIdentity());
        }else{
          $select->where($tableName . '.product_id  = ?', $subject->getIdentity());
        }
        break;
      case 'my-watch-later':
        $viewer = Engine_Api::_()->user()->getViewer();
        $watchLaterTbl = Engine_Api::_()->getDbTable('watchlaters', 'ynvideo');
        $watchLaterTblName = $watchLaterTbl->info('name');
        $select->setIntegrityCheck(false)
                ->join($watchLaterTblName, $watchLaterTblName . ".video_id = " . $tableName . ".video_id", "$watchLaterTblName.watched")
                ->order(array("$watchLaterTblName.watched ASC", "$watchLaterTblName.creation_date DESC"))
                ->where("$watchLaterTblName.user_id = ?", $viewer->getIdentity());
        break;
      case 'most-liked':
        if (!empty($params['duration']) && $params['duration'] != 'overall') {
          if ($params['duration'] == 1) {
            $start_date = date('Y-m-d');
          } else {
            $params['duration'] = $params['duration'] - 1;
            //DATE IS CONVERT IN TO SECONDS
            $duration_seconds = $params['duration'] * 24 * 60 * 60;
            $seconds_enddate = time();
            $seconds_startdate = $seconds_enddate - $duration_seconds;
            //START DATE CAN GET FROM THE DATE FUNCTION AND MINUS RESULT
            $start_date = date('Y-m-d', $seconds_startdate);
          }
          $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
          $likeTableName = $likeTable->info('name');
          $select->setIntegrityCheck(false)
                  ->join($likeTableName, "($likeTableName .resource_id =  $tableName.video_id AND  $likeTableName.resource_type = '$collectibleType' AND $likeTableName.creation_date >= '$start_date')", array());
        }

        $select->order($tableName . '.like_count DESC');
        break;
      case 'most-commented':
        $select->order($tableName . '.comment_count DESC');
        break;
      case 'top-rated':
        $select->order($tableName . '.rating DESC');
        break;
      case 'most-favorite':
        $select->order($tableName . '.favorite_count DESC');
        break;
      case 'featured':
        $select->where($tableName . '.featured = ?', 1);
        break;
      case 'highlight':
        $select->where($tableName . '.highlighted = ?', 1);
        break;
      case 'same-categories':
        $select->where($tableName . '.category_id = ?', $collectible->category_id);
        break;
      case 'also-liked':
        $subject_guid = $params['subject_guid'];
        if (!empty($subject_guid))
          $subject = Engine_Api::_()->getItemByGuid($subject_guid);
        $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
        $likesTableName = $likesTable->info('name');
        $select->distinct(true)
                ->joinLeft($likesTableName, $likesTableName . '.resource_id=video_id', null)
                ->joinLeft($likesTableName . ' as l2', $likesTableName . '.poster_id=l2.poster_id', null)
                ->where($likesTableName . '.poster_type = ?', 'user')
                ->where('l2.poster_type = ?', 'user')
                ->where($likesTableName . '.resource_type = ?', $subject->getType())
                ->where('l2.resource_type = ?', $subject->getType())
                ->where($likesTableName . '.resource_id != ?', $subject->getIdentity())
                ->where('l2.resource_id = ?', $subject->getIdentity())
                ->where('search = ?', true)
                ->where('video_id != ?', $subject->getIdentity());
        break;
      case 'same-tags':
        $tagMapsTable = Engine_Api::_()->getDbtable('tagMaps', 'core');
        $tagsTable = Engine_Api::_()->getDbtable('tags', 'core');
        $subject_guid = $params['subject_guid'];
        if (!empty($subject_guid))
          $subject = Engine_Api::_()->getItemByGuid($subject_guid);
        // Get tags
        $tags = $tagMapsTable->select()
                ->from($tagMapsTable, 'tag_id')
                ->where('resource_type = ?', $subject->getType())
                ->where('resource_id = ?', $subject->getIdentity())
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        // Get other with same tags
        if (!empty($tags)) {
          $select
                  ->distinct(true)
                  ->joinLeft($tagMapsTable->info('name'), 'resource_id = video_id', null)
                  ->where('resource_type = ?', $subject->getType())
                  ->where('resource_id != ?', $subject->getIdentity())
                  ->where('tag_id IN(?)', $tags);
        }
        break;
      case 'same-poster':
        $subject_guid = $params['subject_guid'];
        if (!empty($subject_guid))
          $subject = Engine_Api::_()->getItemByGuid($subject_guid);
        $select->where($tableName . '.video_id != ?', $subject->getIdentity())
                ->where("$tableName .$owner_id = ?", $subject->$owner_id);
        break;
      case 'subject_recent':
        $subject_guid = $params['subject_guid'];
        if (!empty($subject_guid))
          $subject = Engine_Api::_()->getItemByGuid($subject_guid);

        $select->where('parent_type = ?', $subject->getType())
                ->where('parent_id = ?', $subject->getIdentity());
        $select->order("$tableName.creation_date DESC");
        break;

      default :
        $select
                ->where("$tableName .$owner_id = ?", $collectible->$owner_id);
        $select->order($tableName . '.video_id DESC');
        break;
    }

    if (in_array($collectibleType, array('sitepagevideo_video', 'sitebusinessvideo_video'))) {
      $select->order($tableName . '.video_id DESC');
    }
    return $select;
  }

  public function getCountTotal($collectible, $params=array()) {
    $select = $this->getCollectibleSql($collectible, $params);
    if (isset($params['limit'])) {
      $select->limit($params['limit']);
    }

    $rows = $this->_table->fetchAll($select);
    $totalCount = $rows->count();
    return $totalCount;
  }
  
  /**
   * Plugin which return the error, if Siteadmin not using correct version for the plugin.
   *
   */
  public function isModulesSupport() {

    $modArray = array(
        'sitepage' => '4.2.5p1',
        'sitebusiness' => '4.2.5p1',
        'recipe' => '4.2.5',
        'sitepagevideo'=> '4.2.5',
        'sitebusinessvideo'=> '4.2.5',
        'list'=> '4.2.5',
        'advancedactivity'=> '4.2.5',
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
}
?>