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
class Sesmusic_Widget_ProfileArtistController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //Songs settings.
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $authorizationApi = Engine_Api::_()->authorization();
    $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sesmusic');

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();
    $artist_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('artist_id');

    $this->view->artists = $artist = Engine_Api::_()->getItem('sesmusic_artists', $artist_id);

    //Song Settings
    $this->view->songlink = unserialize($settings->getSetting('sesmusic.songlink'));

    $this->view->canAddPlaylistAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addplaylist_albumsong');

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->_forward('notfound', 'error', 'core');

    $this->view->downloadAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'download_albumsong');

    $this->view->canAddFavouriteAlbumSong = Engine_Api::_()->authorization()->isAllowed('sesmusic_album', $viewer, 'addfavourite_albumsong');

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

    //Song Settings
    $this->view->artistSongs = Engine_Api::_()->getDbTable('albumsongs', 'sesmusic')->artistsSongs(array('artist' => $artist_id));

    //Artists settings.
    $this->view->artistlink = unserialize($settings->getSetting('sesmusic.artistlink'));

    $this->view->isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_artist", 'resource_id' => $artist->getIdentity()));
    
    $this->view->informationArtist = $this->_getParam('informationArtist', array( "favouriteCountAr", "ratingCountAr", "description", "ratingStarsAr", "addFavouriteButtonAr"));
    
    $this->view->information = $this->_getParam('information', array('postedBy', 'creationDate', 'commentCount', 'viewCount', 'likeCount', 'ratingCount', 'favouriteCount', 'playCount', 'ratingStars', 'playButton', 'editButton', 'deleteButton', 'addplaylist', 'share', 'report', 'downloadButton', 'addFavouriteButton', "printButton", 'photo', 'category', "favouriteCountAr", "viewCountAr", "description", "ratingStarsAr"));

    //Rating work
    $this->view->mine = $mine = true;
    $this->view->mine = $mine = false;
    $this->view->allowShowRating = $allowShowRating = $settings->getSetting('sesmusic.rateartist.show', 1);
    $this->view->allowRating = $allowRating = $settings->getSetting('sesmusic.artist.rating', 1);
    if ($allowRating == 0) {
      if ($allowShowRating == 0)
        $showRating = false;
      else
        $showRating = true;
    }
    else
      $showRating = true;

    $this->view->showRating = $showRating;
    if ($showRating) {
      $this->view->canRate = $canRate = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'rating_artist');
      $this->view->allowRateAgain = $allowRateAgain = $settings->getSetting('sesmusic.rateartist.again', 1);
      $this->view->allowRateOwn = $allowRateOwn = $settings->getSetting('sesmusic.rateartist.own', 1);

      if ($canRate == 0 || $allowRating == 0)
        $allowRating = false;
      else
        $allowRating = true;

      if ($allowRateOwn == 0 && $mine)
        $allowMine = false;
      else
        $allowMine = true;

      $this->view->allowMine = $allowMine;
      $this->view->allowRating = $allowRating;
      $this->view->rating_type = $rating_type = 'sesmusic_artists';
      $this->view->rating_count = $ratingTable->ratingCount($artist->getIdentity(), $rating_type);
      $this->view->rated = $rated = $ratingTable->checkRated($artist->getIdentity(), $viewer->getIdentity(), $rating_type);

      if (!$allowRateAgain && $rated)
        $rated = false;
      else
        $rated = true;
      $this->view->ratedAgain = $rated;
    }
    //End rating work
  }

}