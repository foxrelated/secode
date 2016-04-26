<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Playlists.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
class Sesvideo_Model_DbTable_Playlists extends Engine_Db_Table {
  protected $_rowClass = 'Sesvideo_Model_Playlist';
  public function getOfTheDayResults() {
    $select = $this->select()
            ->from($this->info('name'), array('*'))
            ->where('offtheday =?', 1)
            ->where('starttime <= DATE(NOW())')
            ->where('endtime >= DATE(NOW())')
            ->order('RAND()');
    return Zend_Paginator::factory($select);
  }
  public function getPlaylistPaginator($params = array()) {
    $paginator = Zend_Paginator::factory($this->getPlaylistSelect($params));
    if (!empty($params['page']))
      $paginator->setCurrentPageNumber($params['page']);
    if (!empty($params['limit']))
      $paginator->setItemCountPerPage($params['limit']);
    return $paginator;
  }
  public function getPlaylistSelect($params = array(),$paginator = true) {
    $playlistTableName = $this->info('name');
    $playlistVideosTableName = Engine_Api::_()->getDbTable('playlistvideos', 'sesvideo')->info('name');
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $this->select()
            ->from($playlistTableName)
            ->joinLeft($playlistVideosTableName, "$playlistTableName.playlist_id = $playlistVideosTableName.playlist_id", '');
    if (isset($params['action']) && ($params['action'] != 'manage' || $params['action'] != 'browse')) {
      $select->where("$playlistVideosTableName.playlistvideo_id IS NOT NULL");
    }
    if ($viewer_id) {
      $select->where("($playlistTableName.is_private = '0' ||  ($playlistTableName.is_private = 1 && $playlistTableName.owner_id = $viewer_id))");
    } else
      $select->where("$playlistTableName.is_private = '0' ");
    if (!empty($params['user']))
      $select->where("owner_id =?", $params['user']);
    if (!empty($params['is_featured']))
      $select = $select->where($playlistTableName . '.is_featured =?', 1);
    if (!empty($params['is_sponsored']))
      $select = $select->where($playlistTableName . '.is_sponsored =?', 1);
    //USER SEARCH
    if (!empty($params['show']) && $params['show'] == 2) {
      $select->where($playlistTableName . '.owner_id IN(?)', $viewer_id);
    }
    if (!empty($params['alphabet']) && $params['alphabet'] != 'all')
      $select->where($playlistTableName . ".title LIKE ?", $params['alphabet'] . '%');

    if (isset($params['popularity']) && $params['popularity'] == 'is_featured') {
      $select->where($playlistTableName . ".is_featured = ?", 1);
    }
    if (isset($params['popularity']) && $params['popularity'] == 'is_sponsored') {
      $select->where($playlistTableName . ".is_sponsored = ?", 1);
    }
		 if (!empty($params['popularCol']))
      $select = $select->order($params['popularCol'] . ' DESC');
    //String Search
    if (!empty($params['title']) && !empty($params['title'])) {
      $select->where("$playlistTableName.title LIKE ?", "%{$params['title']}%")
              ->orWhere("$playlistTableName.description LIKE ?", "%{$params['title']}%");
    }
    if (isset($params['widgteName']) && $params['widgteName'] == "Recommanded Playlist") {
      $select->where($playlistTableName . ".owner_id <> ?", $viewer_id);
    }
    if (isset($params['widgteName']) && $params['widgteName'] == "Other Playlist") {
      $select->where($playlistTableName . ".playlist_id <> ?", $params['playlist_id'])
              ->where($playlistTableName . ".owner_id = ?", $params['owner_id']);
    }
    $select->group("$playlistTableName.playlist_id");
    if (isset($params['popularity'])) {
      switch ($params['popularity']) {
        case "featured" :
          $select->where($playlistTableName . '.is_featured = 1')
                  ->order($playlistTableName . '.playlist_id DESC');
          break;
				case "sponsored" :
          $select->where($playlistTableName . '.is_sponsored = 1')
                  ->order($playlistTableName . '.playlist_id DESC');
        break;
        case "view_count":
          $select->order($playlistTableName . '.view_count DESC');
          break;
				case "like_count":
          $select->order($playlistTableName . '.like_count DESC');
          break;
        case "favourite_count":
          $select->order($playlistTableName . '.favourite_count DESC');
          break;
        case "video_count":
          $select->order($playlistTableName . '.video_count DESC');
          break;
        case "creation_date":
          $select->order($playlistTableName . '.creation_date DESC');
          break;
        case "modified_date":
          $select->order($playlistTableName . '.modified_date DESC');
          break;
      }
    }
		if (isset($params['limit_data']))
      $select = $select->limit($params['limit_data']);
			
    if (!$paginator)
      return $this->fetchAll($select);      
    return $select;
  }
  public function getPlaylistsCount($params = array()) {
    return $this->select()
                    ->from($this->info('name'), $params['column_name'])
                    ->where('owner_id = ?', $params['viewer_id'])
                    ->query()
                    ->fetchAll();
  }
}