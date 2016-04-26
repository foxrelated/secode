<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Albums.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Model_DbTable_Albums extends Engine_Db_Table {

  protected $_rowClass = 'Sesmusic_Model_Album';

  /**
   * Get special playlist
   *
   * User_Model_User $user
   * @$type string
   * @return $select
   */
  public function getSpecialPlaylist(User_Model_User $user, $type) {

    $allowedTypes = array('profile', 'wall', 'message');
    if (!in_array($type, $allowedTypes))
      throw new Sesalbum_Model_Exception('Unknown special album type');

    $select = $this->select()
            ->where('owner_type = ?', $user->getType())
            ->where('owner_id = ?', $user->getIdentity())
            ->where('special = ?', $type)
            ->order('album_id ASC')
            ->limit(1);
    $album = $this->fetchRow($select);

    //Create if it doesn't exist yet
    if (null === $album) {
      $translate = Zend_Registry::get('Zend_Translate');
      $album = $this->createRow();
      $album->owner_type = 'user';
      $album->owner_id = $user->getIdentity();
      $album->special = $type;
      if ($type == 'message') {
        $album->title = $translate->_('Message Music Album');
        $album->search = 0;
      } else {
        $album->title = $translate->_('Profile Music Album');
        $album->search = 1;
      }
      $album->save();

      //Authorizations
      if ($type != 'message') {
        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($album, 'everyone', 'view', true);
        $auth->setAllowed($album, 'everyone', 'comment', true);
      }
    }

    return $album;
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

    $albumSongsTableName = Engine_Api::_()->getDbTable('albumsongs', 'sesmusic')->info('name');

    $albumTable = Engine_Api::_()->getDbTable('albums', 'sesmusic');
    $albumTableName = $albumTable->info('name');

    $select = $albumTable->select()
            ->from($albumTable)
            ->group("$albumTableName.album_id");
    $select->where($albumTableName . ".resource_type != ?", 'sesvideo_chanel');
    $select->where($albumTableName . ".resource_id =?", 0);
    //WALL SEARCH
    if (!empty($params['wall']))
      $select->where('`special` = ?', 'wall');

    //USER SEARCH
    if (!empty($params['user'])) {
      if (is_object($params['user'])) {
        $select->where('owner_id = ?', $params['user']->getIdentity());
      } elseif (is_numeric($params['user'])) {
        $select->where('owner_id = ?', $params['user']);
      }
      if (!empty($params['searchBit'])) {
        $select->where('search = 1');
      }
    } else if (!empty($params['users'])) {
      $select->where('owner_id IN(?)', $params['users']);
      if (!empty($params['searchBit'])) {
        $select->where('search = 1');
      }
    } else {
      $select->where('search = 1')
              ->joinLeft($albumSongsTableName, "$albumTableName.album_id = $albumSongsTableName.album_id", '')
              ->where("$albumSongsTableName.albumsong_id IS NOT NULL");
    }

    if (isset($params['artists'])) {
      $select->joinLeft($albumSongsTableName, "$albumTableName.album_id = $albumSongsTableName.album_id", '')
              ->where($albumSongsTableName . ".artists LIKE ? ", '%' . $params['artists'] . '%');
    }

    if (!empty($params['category_id']) && isset($params['category_id']))
      $select->where($albumTableName . ".category_id =?", $params['category_id']);


    if (!empty($params['subcat_id']) && isset($params['subcat_id']))
      $select->where($albumTableName . ".subcat_id =?", $params['subcat_id']);

    if (!empty($params['subsubcat_id']) && isset($params['subsubcat_id']))
      $select->where($albumTableName . ".subsubcat_id =?", $params['subsubcat_id']);

    if (!empty($params['alphabet']) && $params['alphabet'] != 'all')
      $select->where($albumTableName . ".title LIKE ?", $params['alphabet'] . '%');

    if (isset($params['popularity'])) {
      switch ($params['popularity']) {
        case "featured" :
          $select->order($albumTableName . '.featured' . ' DESC')
                  ->order($albumTableName . '.album_id DESC');
          break;
        case "sponsored" :
          $select->order($albumTableName . '.sponsored' . ' DESC')
                  ->order($albumTableName . '.album_id DESC');
          break;
        case "bothfesp":
          $select->order($albumTableName . '.sponsored' . ' DESC')
                  ->order($albumTableName . '.featured' . ' DESC')
                  ->order($albumTableName . '.album_id DESC');
          break;
        case "hot":
          $select->order($albumTableName . '.hot' . ' DESC')
                  ->order($albumTableName . '.album_id DESC');
          break;
        case "upcoming":
          $select->order($albumTableName . '.upcoming' . ' DESC')
                  ->order($albumTableName . '.album_id DESC');
          break;
        case "view_count":
          $select->order($albumTableName . '.view_count DESC');
          break;
        case "song_count":
          $select->order($albumTableName . '.song_count DESC');
          break;
        case "like_count":
          $select->order($albumTableName . '.like_count DESC');
          break;
        case "comment_count":
          $select->order($albumTableName . '.comment_count DESC');
          break;
        case "favourite_count":
          $select->order($albumTableName . '.favourite_count DESC');
          break;
        case "creation_date":
          $select->order($albumTableName . '.creation_date DESC');
          break;
        case "modified_date":
          $select->order($albumTableName . '.modified_date DESC');
          break;
        case "rating":
          $select->order($albumTableName . '.rating DESC');
          break;
        case "You May Also Like":
          $select->order('RAND() DESC');
          break;
      }
    }

    if (isset($params['showPhoto']) && !empty($params['showPhoto']))
      $select->where($albumTableName . ".photo_id > ?", 0);

    if (!empty($params['title'])) {
      $select->where("$albumTableName.title LIKE ?", "%{$params['title']}%");
    }

    return $select;
  }

  //Widget Results according to parameters
  public function widgetResults($params = array()) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $albumTableName = $this->info('name');
    $select = $this->select()->from($albumTableName, $params['column']);

    $select->where($albumTableName . ".resource_type != ?", 'sesvideo_chanel');
    $select->where($albumTableName . ".resource_id =?", 0);

    if (isset($params['widgteName'])) {
      if ($params['widgteName'] == "Recommanded Albums")
        $select->where($albumTableName . ".owner_id <> ?", $viewer_id);

      if ($params['widgteName'] == "Related Albums") {
        $select->where($albumTableName . ".album_id <> ?", $params['album_id'])
                ->where($albumTableName . ".category_id = ?", $params['category_id']);
        //->where($albumTableName . ".owner_id <> ?", $viewer_id);
      }

      if ($params['widgteName'] == "Other Albums") {
        $select->where($albumTableName . ".album_id <> ?", $params['album_id'])
                ->where($albumTableName . ".owner_id = ?", $viewer_id);
      }
    }

    $select->where($albumTableName . ".search = ?", 1);

    if (isset($params['showPhoto']) && !empty($params['showPhoto']))
      $select->where($albumTableName . ".photo_id > ?", 0);

    if (isset($params['popularity']) || isset($params['displayContentType'])) {
      if ($params['popularity'] == 'featured' || (isset($params['displayContentType']) && $params['displayContentType'] == 'featured')) {
        $select->where($albumTableName . ".featured = ?", 1);
      } elseif ($params['popularity'] == 'sponsored' || (isset($params['displayContentType']) && $params['displayContentType'] == 'sponsored')) {
        $select->where($albumTableName . ".sponsored = ?", 1);
      } elseif ($params['popularity'] == 'bothfesp' || (isset($params['displayContentType']) && $params['displayContentType'] == 'feaspo')) {
        $select->where($albumTableName . ".featured = ?", 1)
                ->where($albumTableName . ".sponsored = ?", 1);
      } elseif (isset($params['displayContentType']) && $params['displayContentType'] == 'hotlat') {
        $select->where($albumTableName . ".hot = ?", 1)
                ->where($albumTableName . ".upcoming  = ?", 1);
      } elseif (isset($params['displayContentType']) && $params['displayContentType'] == 'hot') {
        $select->where($albumTableName . ".hot = ?", 1);
      } elseif (isset($params['displayContentType']) && $params['displayContentType'] == 'upcoming') {
        $select->where($albumTableName . ".upcoming = ?", 1);
      }

      //You may also like work
      if ($params['popularity'] == "You May Also Like") {
        $albumIds = Engine_Api::_()->sesmusic()->likeIds(array('type' => 'sesmusic_album', 'id' => $viewer_id));
        $select->where($albumTableName . '.album_id NOT IN(?)', $albumIds)
                ->where($albumTableName . ".owner_id <> ?", $viewer_id);
      }
    }

    if (isset($params['popularity'])) {
      switch ($params['popularity']) {
        case "featured" :
          $select->order($albumTableName . '.featured' . ' DESC')
                  ->order($albumTableName . '.album_id DESC');
          break;
        case "sponsored" :
          $select->order($albumTableName . '.sponsored' . ' DESC')
                  ->order($albumTableName . '.album_id DESC');
          break;
        case "bothfesp":
          $select->order($albumTableName . '.sponsored' . ' DESC')
                  ->order($albumTableName . '.featured' . ' DESC')
                  ->order($albumTableName . '.album_id DESC');
          break;
        case "hot":
          $select->order($albumTableName . '.hot' . ' DESC')
                  ->order($albumTableName . '.album_id DESC');
          break;
        case "upcoming":
          $select->order($albumTableName . '.upcoming' . ' DESC')
                  ->order($albumTableName . '.album_id DESC');
          break;
        case "view_count":
          $select->order($albumTableName . '.view_count DESC');
          break;
        case "song_count":
          $select->order($albumTableName . '.song_count DESC');
          break;
        case "like_count":
          $select->order($albumTableName . '.like_count DESC');
          break;
        case "comment_count":
          $select->order($albumTableName . '.comment_count DESC');
          break;
        case "favourite_count":
          $select->order($albumTableName . '.favourite_count DESC');
          break;
        case "creation_date":
          $select->order($albumTableName . '.creation_date DESC');
          break;
        case "modified_date":
          $select->order($albumTableName . '.modified_date DESC');
          break;
        case "rating":
          $select->order($albumTableName . '.rating DESC');
          break;
        case "You May Also Like":
          $select->order('RAND() DESC');
          break;
      }
    }

    if (isset($params['limit']) && !empty($params['limit']))
      $select->limit($params['limit']);

    if (isset($params['widgteName']) && $params['widgteName'] == "Tabbed Widget")
      return Zend_Paginator::factory($select);
    else
      return $this->fetchAll($select);
  }

  public function getAlbums($params = array()) {

    return $this->select()
                    ->from($this->info('name'))
                    ->query()
                    ->fetchAll();
  }

  public function getAlbumsCount($params = array()) {

    return $this->select()
                    ->from($this->info('name'), $params['column_name'])
                    ->where('owner_type = ?', 'user')
                    ->where('owner_id = ?', $params['viewer_id'])
                    ->query()
                    ->fetchAll();
  }

  public function getOfTheDayResults() {

    $select = $this->select()
            ->from($this->info('name'), array('*'))
            ->where('offtheday =?', 1)
            ->where('starttime <= DATE(NOW())')
            ->where('endtime >= DATE(NOW())')
            ->order('RAND()');
    return $this->fetchRow($select);
  }

}
