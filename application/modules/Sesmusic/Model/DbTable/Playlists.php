<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Playlists.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Model_DbTable_Playlists extends Engine_Db_Table {

  protected $_rowClass = 'Sesmusic_Model_Playlist';

  public function getOfTheDayResults() {

    $select = $this->select()
            ->from($this->info('name'), array('*'))
            ->where('offtheday =?', 1)
            ->where('starttime <= DATE(NOW())')
            ->where('endtime >= DATE(NOW())')
            ->order('RAND()');
    return $this->fetchRow($select);
  }

  public function getPlaylistPaginator($params = array()) {

    $paginator = Zend_Paginator::factory($this->getPlaylistSelect($params));
    if (!empty($params['page']))
      $paginator->setCurrentPageNumber($params['page']);

    if (!empty($params['limit']))
      $paginator->setItemCountPerPage($params['limit']);

    return $paginator;
  }

  public function getPlaylistSelect($params = array()) {

    $playlistTableName = $this->info('name');
    $playlistSongsTableName = Engine_Api::_()->getDbTable('playlistsongs', 'sesmusic')->info('name');
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $select = $this->select()
            ->from($playlistTableName)
            ->joinLeft($playlistSongsTableName, "$playlistTableName.playlist_id = $playlistSongsTableName.playlist_id", '');

    if (isset($params['action']) && ($params['action'] != 'manage' || $params['action'] != 'browse')) {
      $select->where("$playlistSongsTableName.albumsong_id IS NOT NULL");
    }

    if (!empty($params['user']))
      $select->where("owner_id =?", $params['user']);

    //USER SEARCH
    if (!empty($params['show']) && $params['show'] == 2) {
      // $select->where($playlistTableName . '.owner_id = ?', $params['user']);
      $select->where($playlistTableName . '.owner_id IN(?)', $params['users']);
    }

    if (!empty($params['alphabet']) && $params['alphabet'] != 'all')
      $select->where($playlistTableName . ".title LIKE ?", $params['alphabet'] . '%');

    if (isset($params['popularity'])) {
      if ($params['popularity'] == 'featured') {
        $select->where($playlistTableName . ".featured = ?", 1);
      }
    }

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
          $select->order($playlistTableName . '.featured' . ' DESC')
                  ->order($playlistTableName . '.playlist_id DESC');
          break;
        case "view_count":
          $select->order($playlistTableName . '.view_count DESC');
          break;
        case "favourite_count":
          $select->order($playlistTableName . '.favourite_count DESC');
          break;
        case "song_count":
          $select->order($playlistTableName . '.song_count DESC');
          break;
        case "creation_date":
          $select->order($playlistTableName . '.creation_date DESC');
          break;
        case "modified_date":
          $select->order($playlistTableName . '.modified_date DESC');
          break;
      }
    }
    return $select;
  }

  public function getPlaylistsCount($params = array()) {

    return $this->select()
                    ->from($this->info('name'), $params['column_name'])
                    ->where('owner_type = ?', 'user')
                    ->where('owner_id = ?', $params['viewer_id'])
                    ->query()
                    ->fetchAll();
  }

}