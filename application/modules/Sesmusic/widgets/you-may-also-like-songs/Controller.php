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
class Sesmusic_Widget_YouMayAlsoLikeSongsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $authorizationApi = Engine_Api::_()->authorization();
    
    $this->view->viewer_id = $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->viewType = $this->_getParam('viewType', 'gridview');
    $this->view->information = $this->_getParam('information', array('featuredLabel', 'sponsoredLabel', 'newLabel', 'likeCount', 'commentCount', 'viewCount', 'songsCount', 'title', 'postedby'));
    $this->view->height = $this->_getParam('height', 200);
    $this->view->width = $this->_getParam('width', 100);

    //Songs Settings
    $this->view->songlink = unserialize($settings->getSetting('sesmusic.songlink'));

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

    $params = array();
    $params['limit'] = $this->_getParam('itemCount', 3);
    $params['popularity'] = 'You May Also Like';

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();

    $params['column'] = array('*');

    $this->view->results = $results = Engine_Api::_()->getDbtable('albumsongs', 'sesmusic')->widgetResults($params);

    if (count($results) <= 0)
      return $this->setNoRender();
  }

}