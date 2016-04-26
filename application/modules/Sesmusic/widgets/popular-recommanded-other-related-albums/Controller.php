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
class Sesmusic_Widget_PopularRecommandedOtherRelatedAlbumsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->showType = $showType = $this->_getParam('showType', 'all');

    $coreApi = Engine_Api::_()->core();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $authorizationApi = Engine_Api::_()->authorization();

    if ($showType == 'other') {
      $this->getElement()->removeDecorator('Title');

      $album = $coreApi->getSubject('sesmusic_album');
      if (!$album)
        return $this->setNoRender();
    } elseif ($showType == 'related') {

      if (!$coreApi->hasSubject('sesmusic_album'))
        return $this->setNoRender();

      $album = $coreApi->getSubject('sesmusic_album');
      if (!$album)
        return $this->setNoRender();

      if (!$album->category_id)
        return $this->setNoRender();
    }

    $this->view->viewType = $this->_getParam('viewType', 'gridview');
    $this->view->height = $this->_getParam('height', 200);
    $this->view->width = $this->_getParam('width', 100);
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    $this->view->canAddPlaylist = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addplaylist_album');

    $this->view->canAddFavourite = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addfavourite_album');

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();

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

    $this->view->information = $this->_getParam('information', array('featuredLabel', 'sponsoredLabel', 'newLabel', 'likeCount', 'commentCount', 'viewCount', 'songsCount', 'title', 'postedby', 'ratingCount'));

    $params = array();
    if ($showType == 'recommanded') {
      $params['widgteName'] = 'Recommanded Albums';
    } elseif ($showType == 'other') {
      $params['widgteName'] = 'Other Albums';
      $params['album_id'] = $album->album_id;
    } elseif ($showType == 'related') {
      $params['widgteName'] = 'Related Albums';
      $params['album_id'] = $album->album_id;
      $params['category_id'] = $album->category_id;
    }

    $params['popularity'] = $this->_getParam('popularity', 'creation_date');
    $params['showPhoto'] = $this->_getParam('showPhoto', 1);
    $params['limit'] = $this->_getParam('limit', 3);
    $params['column'] = array('album_id', 'title', 'description', 'photo_id', 'owner_id', 'view_count', 'like_count', 'comment_count', 'song_count', 'featured', 'hot', 'sponsored', 'rating', 'special');
    $this->view->results = Engine_Api::_()->getDbtable('albums', 'sesmusic')->widgetResults($params);
    if (count($this->view->results) <= 0)
      return $this->setNoRender();
  }

}