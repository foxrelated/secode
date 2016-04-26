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
class Sesmusic_Widget_AlbumCoverController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $authorizationApi = Engine_Api::_()->authorization();
    $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sesmusic');
    $this->view->categoriesTable = Engine_Api::_()->getDbTable('categories', 'sesmusic');

    $this->view->album = $album = Engine_Api::_()->core()->getSubject('sesmusic_album');
    if (!$album)
      return $this->setNoRender();

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();

    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->albumCover = $settings->getSetting('sesmusic.show.albumcover', 1);
    $this->view->viewer_id = $viewer->getIdentity();
    $this->view->height = $this->_getParam('height', 400);
    $this->view->mainPhotoHeight = $this->_getParam('mainPhotoHeight', 350);
    $this->view->mainPhotowidth = $this->_getParam('mainPhotowidth', 350);
    $this->view->albumCoverPhoto = $settings->getSetting('sesmusic.albumcover.photo');

    //Can create
    $this->view->canCreate = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'create');

    //Can delete
    $this->view->canDelete = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'delete');

    $this->view->canAddPlaylist = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addplaylist_album');

    $this->view->canAddFavourite = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addfavourite_album');

    $this->view->albumlink = unserialize($settings->getSetting('sesmusic.albumlink'));

    //Favourite work
    $this->view->isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_album", 'resource_id' => $album->getIdentity()));

    //Rating work
    $this->view->mine = $mine = true;
    if (!$viewer->isSelf($album->getOwner()))
      $this->view->mine = $mine = false;

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

    if ($showRating) {
      $this->view->canRate = $canRate = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'rating_album');
      $this->view->allowRateAgain = $allowRateAgain = $settings->getSetting('sesmusic.ratealbum.again', 1);
      $this->view->allowRateOwn = $allowRateOwn = $settings->getSetting('sesmusic.ratealbum.own', 1);

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
      $this->view->rating_type = $rating_type = 'sesmusic_album';
      $this->view->rating_count = $ratingTable->ratingCount($album->getIdentity(), $rating_type);
      $this->view->rated = $rated = $ratingTable->checkRated($album->getIdentity(), $viewer->getIdentity(), $rating_type);

      if (!$allowRateAgain && $rated)
        $rated = false;
      else
        $rated = true;
      $this->view->ratedAgain = $rated;
    }
    //End rating work

    $this->view->information = $this->_getParam('information', array('featured', 'sponsored', 'hot', 'postedBy', 'creationDate', 'commentCount', 'viewCount', 'likeCount', 'ratingCount', 'description', 'ratingStars', 'favouriteCount', 'uploadButton', 'editButton', 'deleteButton', 'addplaylist', 'share', 'report', 'downloadButton', 'addFavouriteButton', 'photo', "category"));
  }

}