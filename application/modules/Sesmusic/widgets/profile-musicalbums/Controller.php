<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Widget_ProfileMusicalbumsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $coreApi = Engine_Api::_()->core();
    $authorizationApi = Engine_Api::_()->authorization();

    if (empty($_POST['is_ajax'])) {

      if (!$coreApi->hasSubject())
        return $this->setNoRender();

      $subject = $coreApi->getSubject();
    }

    //Default option for tabbed widget
    if (isset($_POST['params']))
      $params = json_decode($_POST['params'], true);

    $this->view->defaultOpenTab = $defaultOpenTab = ($this->_getParam('openTab') != NULL ? $this->_getParam('openTab') : (isset($params['openTab']) ? $params['openTab'] : 'profilemusicalbums'));

    $this->view->defaultOptionsShow = $defaultOptionsShow = $this->_getParam('defaultOptionsShow', array('profilemusicalbums', 'songofyou', 'playlists', 'favouriteSong', 'favouriteArtist'));

    if (!$defaultOptionsShow)
      return $this->setNoRender();

    //Initialize type variable type
    $type = '';
    switch ($defaultOpenTab) {
      case 'favouriteSong':
        $type = 'favouriteSong';
        $albumPhotoOption = 'photo';
        break;
      case 'songofyou':
        $type = 'songofyou';
        $albumPhotoOption = 'photo';
        break;
      case 'profilemusicalbums':
        $type = 'profilemusicalbums';
        $albumPhotoOption = 'album';
        break;
      case 'playlists':
        $type = 'playlists';
        $albumPhotoOption = 'playlist';
        break;
      case 'favouriteArtist':
        $type = 'artists';
        $albumPhotoOption = 'artist';
        break;
    }

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();

    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
    $this->view->defaultOptions = array('profilemusicalbums', 'songofyou', 'playlists', 'favouriteSong', 'favouriteArtist');
    $this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
    $this->view->height = $defaultHeight = isset($params['height']) ? $params['height'] : $this->_getParam('Height', '180px');
    $this->view->width = $defaultWidth = isset($params['width']) ? $params['width'] : $this->_getParam('Width', '180px');
    $this->view->limit_data = $limit_data = isset($params['limit_data']) ? $params['limit_data'] : $this->_getParam('limit_data', '20');
    $this->view->limit = ($page - 1) * $limit_data;
    $this->view->albumPhotoOption = $albumPhotoOption;
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'comment', 'rating', 'by', 'title', 'social_sharing', 'view'));

    $this->view->informationPlaylist = $informationPlaylist = isset($params['informationPlaylist']) ? $params['informationPlaylist'] : $this->_getParam('informationPlaylist', array("sharePl", "addFavouriteButtonPl", "viewCountPl", "description", "postedByPl", "showSongsList"));
    
    $this->view->informationArtist = $informationArtist = isset($params['informationArtist']) ? $params['informationArtist'] : $this->_getParam('informationArtist', array("showfavourite", "showrating"));

    $this->view->information = $information = isset($params['information']) ? $params['information'] : $this->_getParam('information', array('featured', 'sponsored', 'new', 'likeCount', 'commentCount', "downloadCount", 'viewCount', 'title', 'postedby'));

    $this->view->informationAlbum = $informationAlbum = isset($params['informationAlbum']) ? $params['informationAlbum'] : $this->_getParam('informationAlbum', array('featured', 'sponsored', 'new', 'likeCount', 'commentCount', "downloadCount", 'viewCount', 'title', 'postedby'));

    foreach ($show_criterias as $show_criteria)
      $this->view->$show_criteria = $show_criteria;

    $params = $this->view->params = array('height' => $defaultHeight, 'width' => $defaultWidth, 'limit_data' => $limit_data, 'albumPhotoOption' => $albumPhotoOption, 'openTab' => $defaultOpenTab, 'pagging' => $loadOptionData, 'show_criterias' => $show_criterias, 'defaultOptionsShow' => $defaultOptionsShow, "informationPlaylist" => $informationPlaylist, 'information' => $information, "informationAlbum" => $informationAlbum, 'informationArtist' => $informationArtist);
    $this->view->loadMoreLink = $this->_getParam('openTab') != NULL ? true : false;
    $this->view->type = $type;

    $this->view->songlink = unserialize($settings->getSetting('sesmusic.songlink'));

    $this->view->canAddPlaylistAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addplaylist_albumsong');

    $this->view->addfavouriteAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addfavourite_albumsong');

    $allowShowRating = $settings->getSetting('sesmusic.ratealbum.show', 1);
    $allowRating = $settings->getSetting('sesmusic.album.rating', 1);
    if ($allowRating == 0) {
      if ($allowShowRating == 0)
        $showRating = false;
      else
        $showRating = true;
    }
    else
      $showRating = true;
    $this->view->showRating = $showRating;


    $allowShowRating = $settings->getSetting('sesmusic.ratealbumsong.show', 1);
    $allowRating = $settings->getSetting('sesmusic.albumsong.rating', 1);
    if ($allowRating == 0) {
      if ($allowShowRating == 0)
        $showRating = false;
      else
        $showRating = true;
    }
    else
      $showRating = true;
    $this->view->showAlbumSongRating = $showRating;

    //Album Settings
    $this->view->canAddPlaylist = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addplaylist_album');

    $this->view->canAddFavourite = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addfavourite_album');

    $this->view->albumlink = unserialize($settings->getSetting('sesmusic.albumlink'));

    $allowShowRating = $settings->getSetting('sesmusic.ratealbum.show', 1);
    $allowRating = $settings->getSetting('sesmusic.album.rating', 1);
    if ($allowRating == 0) {
      if ($allowShowRating == 0)
        $showRating = false;
      else
        $showRating = true;
    }
    else
      $showRating = true;
    $this->view->showRating = $showRating;

    //Artists settings.
    $this->view->artistlink = unserialize($settings->getSetting('sesmusic.artistlink'));
    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();

    $allowShowRating = $settings->getSetting('sesmusic.rateartist.show', 1);
    $allowRating = $settings->getSetting('sesmusic.artist.rating', 1);
    if ($allowRating == 0) {
      if ($allowShowRating == 0)
        $showRating = false;
      else
        $showRating = true;
    }
    else
      $showRating = true;
    $this->view->showArtistRating = $showRating;

    if (empty($_POST['is_ajax'])) {
      if ($subject->user_id != $viewer->getIdentity()) {
        $userObject = Engine_Api::_()->getItem('user', $subject->user_id);
        $profile = 'other';
        $userId = $subject->user_id;
      } else {
        $userObject = Engine_Api::_()->getItem('user', $viewer->getIdentity());
        $profile = 'own';
        $userId = $viewer->getIdentity();
      }
    }
    else
      $userId = $_POST['identityObject'];

    $this->view->identityObject = $userId;

    if ($type == 'profilemusicalbums') {
      $table = Engine_Api::_()->getItemTable('sesmusic_album');
      $tableName = $table->info('name');

      $select = $table->select()
              ->from($tableName)
              ->where($tableName . '.search = ?', true)
              ->where($tableName . '.owner_id = ?', $userId)
              ->order('creation_date DESC');
    } else if ($type == 'favouriteSong') {
      $favouritesTable = Engine_Api::_()->getDbTable('favourites', 'sesmusic');
      $favouritesTableName = $favouritesTable->info('name');
      $select = $favouritesTable->select()
              ->from($favouritesTableName)
              ->where('resource_type =?', 'sesmusic_albumsong')
              ->where('user_id =?', $userId);
      $this->view->makeObjectOfPhoto = true;
    } else if ($type == 'songofyou') {
      $albumTable = Engine_Api::_()->getItemTable('sesmusic_album');
      $albumTableName = $albumTable->info('name');

      $albumSongTable = Engine_Api::_()->getItemTable('sesmusic_albumsong');
      $albumSongTableName = $albumSongTable->info('name');

      $select = $albumSongTable->select()
              ->from($albumSongTableName)
              ->setIntegrityCheck(false)
              ->joinLeft($albumTableName, $albumTableName . '.album_id=' . $albumSongTableName . '.album_id', null)
              ->where($albumTableName . '.owner_id = ?', $userId)
              ->order($albumTableName . '.creation_date DESC');
    } else if ($type == 'playlists') {
      $playlistsTable = $favouritesTable = Engine_Api::_()->getDbTable('playlists', 'sesmusic');
      $playlistsTableName = $playlistsTable->info('name');

      $select = $playlistsTable->select()
              ->from($playlistsTableName)
              ->where($playlistsTableName . '.owner_id = ?', $userId)
              ->order($playlistsTableName . '.creation_date DESC');
    } else if ($type == 'artists') {
      $favouritesTable = Engine_Api::_()->getDbTable('favourites', 'sesmusic');
      $favouritesTableName = $favouritesTable->info('name');
      $select = $favouritesTable->select()
              ->from($favouritesTableName)
              ->where('resource_type =?', 'sesmusic_artist')
              ->where('user_id =?', $userId);
      //$this->view->makeObjectOfPhoto = true;
    }

    if (empty($_POST['is_ajax'])) {
      if ($profile == 'own') {
        $this->view->profile = 'own';
      } else {
        $name = explode(' ', $userObject->displayname);
        if (isset($name[0]))
          $name = ucfirst($name[0]);
        else
          $name = ucfirst($name[1]);
        $this->view->profile = $name;
      }
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    //Set item count per page and current page number
    $paginator->setItemCountPerPage($limit_data);
    $this->view->page = $page;
    $paginator->setCurrentPageNumber($page);

    if ($is_ajax)
      $this->getElement()->removeDecorator('Container');
  }

}