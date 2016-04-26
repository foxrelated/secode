<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Videos.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Model_DbTable_Videos extends Engine_Db_Table {

  protected $_rowClass = "Sesvideo_Model_Video";
  protected $_name = 'video_videos';

  public function getWatchLaterStatus($video_id) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();
    $tableName = $this->info('name');
    $select = $this->select()
            ->from($tableName)
            ->where($tableName . '.video_id = ?', $video_id);
    $watchLaterTable = Engine_Api::_()->getDbTable('watchlaters', 'sesvideo')->info('name');
    $select = $select->setIntegrityCheck(false);
    $select = $select->joinLeft($watchLaterTable, '(' . $watchLaterTable . '.video_id = ' . $tableName . '.video_id AND ' . $watchLaterTable . '.owner_id = ' . $user_id . ')', array('watchlater_id'));
    $select->where('watchlater_id != ?', '');
    return $this->fetchAll($select);
  }

  public function getVideo($params = array(), $paginator = true) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();
    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tmName = $tmTable->info('name');
    $tableName = $this->info('name');
    $select = $this->select()
            ->from($tableName)
            ->where($tableName . '.video_id != ?', '');
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.watchlater', 1)) {
      $watchLaterTable = Engine_Api::_()->getDbTable('watchlaters', 'sesvideo')->info('name');
      $select = $select->setIntegrityCheck(false);
      $select = $select->joinLeft($watchLaterTable, '(' . $watchLaterTable . '.video_id = ' . $tableName . '.video_id AND ' . $watchLaterTable . '.owner_id = ' . $user_id . ')', array('watchlater_id'));
    }
    
    if (isset($params['lat']) && isset($params['miles']) && $params['miles'] != 0 && isset($params['lng']) && $params['lat'] != '' && $params['lng'] != '' && ((isset($params['location']) && $params['location'] != '') || isset($params['locationWidget']))) {
      $tableLocation = Engine_Api::_()->getDbtable('locations', 'sesbasic');
      $tableLocationName = $tableLocation->info('name');
      $origLat = $params['lat'];
      $origLon = $params['lng'];
      $select = $select->setIntegrityCheck(false);
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo.search.type', 1) == 1) {
        $searchType = 3956;
      } else
        $searchType = 6371;
      $dist = $params['miles']; // This is the maximum distance (in miles) away from $origLat, $origLon in which to search
      $select->joinLeft($tableLocationName, $tableLocationName . '.resource_id = ' . $tableName . '.video_id AND ' . $tableLocationName . '.resource_type = "sesvideo_video" ', array($searchType . " * 2 * ASIN(SQRT( POWER(SIN(($origLat - abs(lat))*pi()/180/2),2) + COS($origLat*pi()/180 )*COS(abs(lat)*pi()/180) *POWER(SIN(($origLon-lng)*pi()/180/2),2))) as distance", 'lat', 'lng'));
      $select->where($tableLocationName . ".lng between ($origLon-$dist/abs(cos(radians($origLat))*69)) and ($origLon+$dist/abs(cos(radians($origLat))*69)) and " . $tableLocationName . ".lat between ($origLat-($dist/69)) and ($origLat+($dist/69))");
      $select->order('distance');
      $select->having("distance < $dist");
    }

    if (isset($params['widgetName']) && $params['widgetName'] == 'oftheday') {
      $select->where($tableName . '.offtheday =?', 1)
              ->where($tableName . '.starttime <= DATE(NOW())')
              ->where($tableName . '.endtime >= DATE(NOW())')
              ->order('RAND()');
    }

    if (isset($params['widgetName'])) {
      if ($params['widgetName'] == 'oftheday') {
        $select->where($tableName . '.offtheday =?', 1)
                ->where($tableName . '.starttime <= DATE(NOW())')
                ->where($tableName . '.endtime >= DATE(NOW())')
                ->order('RAND()');
      }

      if ($params['widgetName'] == 'artistViewPage') {
        $select->where("artists LIKE ? ", '%' . $params['artist'] . '%')
                ->order('creation_date DESC');
      }
    }

    if (!empty($params['tag'])) {
      $select
              ->joinLeft($tmName, "$tmName.resource_id = $tableName.video_id", NULL)
              ->where($tmName . '.resource_type = ?', 'video')
              ->where($tmName . '.tag_id = ?', $params['tag']);
    }
    if (!empty($params['sameTag'])) {
      $select->joinLeft($tmName, "$tmName.resource_id=$tableName.video_id", null)
              ->where('resource_type = ?', 'video')
              ->distinct(true)
              ->where('resource_id != ?', $params['sameTagresource_id'])
              ->where('tag_id IN(?)', $params['sameTagTag_id']);
    }
    if (!empty($params['video_id']))
      $select = $select->where($tableName . '.video_id =?', $params['video_id']);
    if (!empty($params['not_video_id']))
      $select = $select->where($tableName . '.video_id != ?', $params['not_video_id']);
		 if (!empty($params['notin_video_id']))
      $select = $select->where($tableName . '.video_id NOT IN (?)', $params['notin_video_id']);
    if (!empty($params['popularCol']))
      $select = $select->order($params['popularCol'] . ' DESC');

    if (!empty($params['user_id']) && $params['user_id'] != '')
      $select = $select->where($tableName . '.owner_id =?', $params['user_id']);

    if (!empty($params['search'])) {
      if (!empty($params['fixedData']) && $params['fixedData'] != '')
        $select = $select->where($tableName . '.' . $params['fixedData'] . ' =?', 1);
    }

    if (!empty($params['parent_id']) && $params['parent_type'] == 'sesevent_event') {
      $select = $select->where($tableName . '.parent_id =?', $params['parent_id'])
              ->where($tableName . '.parent_type =?', $params['parent_type']);
    }

    if (!empty($params['search']))
      $select = $select->where($tableName . '.search =?', 1);

    if (isset($params['show']) && $params['show'] == 2 && $viewer->getIdentity()) {
      $users = $viewer->membership()->getMembershipsOfIds();
      if ($users)
        $select->where($tableName . '.owner_id IN (?)', $users);
      else
        $select->where($tableName . '.owner_id IN (?)', 0);
    }

    if (!empty($params['alphabet']) && $params['alphabet'] != 'all')
      $select->where($tableName . ".title LIKE ?", $params['alphabet'] . '%');

    if (isset($params['criteria'])) {
      if ($params['criteria'] == 1)
        $select->where($tableName . '.is_featured =?', '1');
      else if ($params['criteria'] == 2)
        $select->where($tableName . '.is_sponsored =?', '1');
      else if ($params['criteria'] == 6)
        $select->where($tableName . '.is_hot =?', '1');
      else if ($params['criteria'] == 3)
        $select->where($tableName . '.is_featured = 1 OR ' . $tableName . '.is_sponsored = 1');
      else if ($params['criteria'] == 4)
        $select->where($tableName . '.is_featured = 0 AND ' . $tableName . '.is_sponsored = 0');
    }

    if (isset($params['criteria'])) {
      switch ($params['info']) {
        case 'recently_created':
          $select->order('creation_date DESC');
          break;
        case 'most_viewed':
          $select->order('view_count DESC');
          break;
        case 'most_liked':
          $select->order('like_count DESC');
          break;
        case 'most_rated':
          $select->order('rating DESC');
          break;
        case 'most_favourite':
          $select->order('favourite_count DESC');
          break;
        case 'most_commented':
          $select->order('comment_count DESC');
          break;
        case 'random':
          $select->order('Rand()');
          break;
      }
    }
		
		if(empty($params['manageVideo']))
			$select->where($tableName.'.approve = ?',1);
		
    if (!empty($params['is_featured']))
      $select = $select->where($tableName . '.is_featured =?', 1);

    if (!empty($params['is_sponsored']))
      $select = $select->where($tableName . '.is_sponsored =?', 1);

    if (!empty($params['is_hot']))
      $select = $select->where($tableName . '.is_hot =?', 1);

    if (!empty($params['status']))
      $select = $select->where($tableName . '.status =?', 1);

    if (!empty($params['category_id']))
      $select = $select->where($tableName . '.category_id =?', $params['category_id']);

    if (!empty($params['subcat_id']))
      $select = $select->where($tableName . '.subcat_id =?', $params['subcat_id']);

    if (!empty($params['subsubcat_id']))
      $select = $select->where($tableName . '.subsubcat_id =?', $params['subsubcat_id']);

    if (!empty($params['text']))
      $select = $select->where($tableName . '.title LIKE "%' . $params['text'] . '%"');
		$select = $select->order('video_id DESC');
    if (isset($params['limit_data']))
      $select = $select->limit($params['limit_data']);
    if ($paginator)
      return Zend_Paginator::factory($select);
    else
      return $this->fetchAll($select);
  }

  public function peopleAlsoLiked($id = 0) {
    $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
    $likesTableName = $likesTable->info('name');
		$tableName = $this->info('name');
		$viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();
    $select = $this->select()
            ->distinct(true)
            ->from($this->info('name'))
            ->joinLeft($likesTableName, $likesTableName . '.resource_id=video_id', null)
            ->joinLeft($likesTableName . ' as l2', $likesTableName . '.poster_id=l2.poster_id', null)
            ->where($likesTableName . '.poster_type = ?', 'user')
            ->where('l2.poster_type = ?', 'user')
            ->where($likesTableName . '.resource_type = ?', 'video')
            ->where('l2.resource_type = ?', 'video')
            ->where($likesTableName . '.resource_id != ?', $id)
            ->where('l2.resource_id = ?', $id)
            ->where('search = ?', true)
            ->where($tableName.'.video_id != ?', $id)
    //->order(new Zend_Db_Expr('COUNT(like_id)'))
    ;
		if (Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.watchlater', 1)) {
      $watchLaterTable = Engine_Api::_()->getDbTable('watchlaters', 'sesvideo')->info('name');
      $select = $select->setIntegrityCheck(false);
      $select = $select->joinLeft($watchLaterTable, '(' . $watchLaterTable . '.video_id = ' . $tableName . '.video_id AND ' . $watchLaterTable . '.owner_id = ' . $user_id . ')', array('watchlater_id'));
    }
    return Zend_Paginator::factory($select);
  }

  public function videoLightBox($video = null, $nextPreviousCondition, $getallvideos = false, $paginator = false,$type = '',$item_id = '') {

//getSEVersion for lower version of SE
    $getmodule = Engine_Api::_()->getDbTable('modules', 'core')->getModule('core');
    if (!empty($getmodule->version) && version_compare($getmodule->version, '4.8.6') < 0) {
      $toArray = true;
    } else
      $toArray = false;
    $tableNameVideo = $this->info('name');
    $select = $this->select()
            ->from($tableNameVideo);
		switch ($type){
			case 'sesvideo_chanel':
				$getChanelVideoTableName = Engine_Api::_()->getDbTable('chanelvideos', 'sesvideo')->info('name');
				$select->setIntegrityCheck(false);
				$select->joinLeft($getChanelVideoTableName, $getChanelVideoTableName . '.video_id = ' . $tableNameVideo . '.video_id', null)
								->where("chanel_id = ".$item_id);
				if(!$getallvideos)
								$select->where($getChanelVideoTableName.'.video_id '.$nextPreviousCondition.' ?',$video->video_id);
			if ($nextPreviousCondition == '>')
				$select->order("$getChanelVideoTableName.video_id ASC");
			else if ($nextPreviousCondition == '<')
				$select->order("$getChanelVideoTableName.video_id DESC");
			else
				$select->order("$getChanelVideoTableName.video_id ASC");
			break;
			case 'sesvideo_playlist':
				$getPlaylistVideoTableName = Engine_Api::_()->getDbTable('playlistvideos', 'sesvideo')->info('name');
				$select->setIntegrityCheck(false);
				$select->joinLeft($getPlaylistVideoTableName, $getPlaylistVideoTableName . '.file_id = ' . $tableNameVideo . '.video_id', null)
								->where("playlist_id = ".$item_id);
				if(!$getallvideos)
								$select->where($getPlaylistVideoTableName.'.file_id '.$nextPreviousCondition.' ?',$video->video_id);
			if ($nextPreviousCondition == '>')
				$select->order("$getPlaylistVideoTableName.file_id ASC");
			else if ($nextPreviousCondition == '<')
				$select->order("$getPlaylistVideoTableName.file_id DESC");
			else
				$select->order("$getPlaylistVideoTableName.file_id ASC");
			break;
			default:
			$select->where("$tableNameVideo.owner_id =  ?", $video->owner_id);
			break;
		}
		// custom query as per status assign
    if ($getallvideos) {
      $select->order('creation_date DESC');
      return Zend_Paginator::factory($select);
    }
    $select->limit('1');     
		if($type == ''){
			if ($nextPreviousCondition == '<'){
				$select->order('video_id ASC');
				 $select->where("$tableNameVideo.video_id > $video->video_id");
			}else{
				$select->order('video_id DESC');
				 $select->where("$tableNameVideo.video_id < $video->video_id");
			}
		}
    $select->order('creation_date DESC'); 
    if ($paginator)
      return Zend_Paginator::factory($select);
    if ($toArray) {
      $video = $tableNameVideo->fetchAll($select);
      if (!empty($video))
        $video = $this->toArray();
      else
        $video = '';
    }else {
      $video = $this->fetchRow($select);
    }
    return $video;
  }
public function getFavourite($params = array()){
		$tableFav = Engine_Api::_()->getDbtable('favourites', 'sesvideo');
		$tableFav = $tableFav->info('name');
		$select = $this->select()
							->from($this->info('name'))
							->where('video_id = ?',$params['resource_id'])
							->setIntegrityCheck(false)
							->where('resource_type =?',$params['type'])
							->order('favourite_id DESC')
							->joinLeft($tableFav, $tableFav . '.resource_id=' . $this->info('name') . '.video_id',array('user_id'));
							return  Zend_Paginator::factory($select);
	}
  public function countVideos() {
    $select = $this->select()
            ->from($this->info('name'), array('*'));
    return Zend_Paginator::factory($select);
  }

}
