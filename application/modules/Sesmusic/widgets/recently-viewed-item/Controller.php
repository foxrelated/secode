<?php

class Sesmusic_Widget_RecentlyViewedItemController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $authorizationApi = Engine_Api::_()->authorization();
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();
    $this->view->content_type = $type = $this->_getParam('category', 'sesmusic_album');
    $this->view->viewType = $this->_getParam('viewType', 'listView');
    $userId = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (($type == 'by_me' || $type == 'by_myfriend') && $userId == 0) {
      return $this->setNoRender();
    }

    $limit = $this->_getParam('limit_data', 10);
    $this->view->type = $criteria = $this->_getParam('criteria', 'by_me');
    $this->view->height = $defaultHeight = isset($params['height']) ? $params['height'] : $this->_getParam('height', '180');
    $this->view->width = $defaultWidth = isset($params['width']) ? $params['width'] : $this->_getParam('width', '180');
    $this->view->title_truncation = $title_truncation = isset($params['title_truncation']) ? $params['title_truncation'] : $this->_getParam('title_truncation', '45');
    $this->view->information = $this->_getParam('information', array('likeCount', 'commentCount', 'ratingCount', 'postedby', 'viewCount'));

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


    //Songs Settings
    //Songs settings.
    $this->view->songlink = unserialize($settings->getSetting('sesmusic.songlink'));

    $this->view->information = $this->_getParam('information', array('featuredLabel', 'sponsoredLabel', 'newLabel', 'likeCount', 'commentCount', "downloadCount", 'viewCount', 'title', 'postedby'));

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();

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


    if ($type == 'sesmusic_album') {
      $params = array('type' => 'sesmusic_album', 'limit' => $limit, 'criteria' => $criteria);
    } else if ($type == 'sesmusic_albumsong') {
      $params = array('type' => 'sesmusic_albumsong', 'limit' => $limit, 'criteria' => $criteria);
    }
    else
      return $this->setNoRender();

    $result = Engine_Api::_()->getDbtable('recentlyviewitems', 'sesmusic')->getitem($params);
    if (count($result) == 0)
      return $this->setNoRender();

    $this->view->results = $result->toArray();
    $this->view->typeWidget = $type;
  }

}
