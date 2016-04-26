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
class Sesmusic_Widget_AlbumSongPlaylistArtistDayOfTheController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->contentType = $contentType = $this->_getParam('contentType', 'album');
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $authorizationApi = Engine_Api::_()->authorization();
    $favouriteTable = Engine_Api::_()->getDbTable('favourites', 'sesmusic');

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();

    if ($contentType == 'album') {

      $item = Engine_Api::_()->getDbtable('albums', 'sesmusic')->getOfTheDayResults();

      //Get all settings
      $this->view->information = $this->_getParam('information', array('featured', 'sponsored', 'hot', 'likeCount', 'commentCount', 'viewCount', 'ratingCount', 'songsCount', 'title', 'postedby'));

      $this->view->canAddPlaylist = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addplaylist_album');

      $this->view->canAddFavourite = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addfavourite_album');

      if ($item && $item->album_id)
        $this->view->isFavourite = $favouriteTable->isFavourite(array('resource_type' => "sesmusic_album", 'resource_id' => $item->album_id));

      $this->view->albumlink = unserialize($settings->getSetting('sesmusic.albumlink'));

      $this->view->allowShowRating = $allowShowRating = $settings->getSetting('sesmusic.ratealbum.show', 1);
      $this->view->allowRating = $allowRating = $settings->getSetting('sesmusic.album.rating', 1);
      if ($allowRating == 0) {
        if ($allowShowRating == 0)
          $showRating = false;
        else
          $showRating = true;
      }
      else
        $showRating = true;
      $this->view->showRating = $showRating;

      $this->view->album = $item; //Engine_Api::_()->getItem('sesmusic_album', $item->album_id);
      if (empty($this->view->album))
        return $this->setNoRender();
    } elseif ($contentType == 'albumsong') {

      $item = Engine_Api::_()->getDbtable('albumsongs', 'sesmusic')->getOfTheDayResults();
      //Get all settings
      $this->view->information = $this->_getParam('information', array('featured', 'sponsored', 'hot', 'likeCount', 'commentCount', 'viewCount', 'ratingCount', 'title', 'postedby'));

      //Songs settings.
      $this->view->songlink = unserialize($settings->getSetting('sesmusic.songlink'));

      //Favourite work
      if ($item)
        $this->view->isFavourite = $favouriteTable->isFavourite(array('resource_type' => "sesmusic_albumsong", 'resource_id' => $item->albumsong_id));

      $this->view->canAddPlaylistAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addplaylist_albumsong');

      $this->view->addfavouriteAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addfavourite_albumsong');

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

      //Album and Song object according to song_id and alsbum_id: Written by SocialEngineSolutions
      $this->view->song = $song = $item; //Engine_Api::_()->getItem('sesmusic_albumsong', $item->albumsong_id);
      if ($song)
        $this->view->album = Engine_Api::_()->getItem('sesmusic_album', $song->album_id);
      if (empty($song))
        return $this->setNoRender();
    } elseif ($contentType == 'artist') {

      $item = Engine_Api::_()->getDbtable('artists', 'sesmusic')->getOfTheDayResults();
      //Get all settings
      $this->view->information = $this->_getParam('information', array('ratingCount', 'favouriteCount', 'title'));

      if ($item)
        $this->view->isFavourite = $favouriteTable->isFavourite(array('resource_type' => "sesmusic_artist", 'resource_id' => $item->artist_id));

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
      $this->view->artistlink = unserialize($settings->getSetting('sesmusic.artistlink'));
      $this->view->artist = $item; //Engine_Api::_()->getItem('sesmusic_artist', $item->artist_id);
      if (empty($this->view->artist))
        return $this->setNoRender();
    } elseif ($contentType == 'playlist') {

      $item = Engine_Api::_()->getDbtable('playlists', 'sesmusic')->getOfTheDayResults();

      $this->view->information = $this->_getParam('information', array('ratingCount', 'favouriteCount', 'title'));
      $this->view->playlist = $item; //Engine_Api::_()->getItem('sesmusic_playlist', $item->playlist_id);

      if (empty($this->view->playlist))
        return $this->setNoRender();

      //Songs settings.
      $this->view->songlink = unserialize($settings->getSetting('sesmusic.songlink'));

      $this->view->canAddPlaylistAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addplaylist_albumsong');

      $this->view->downloadAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'download_albumsong');

      $this->view->canAddFavouriteAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addfavourite_albumsong');

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
    }
  }

}