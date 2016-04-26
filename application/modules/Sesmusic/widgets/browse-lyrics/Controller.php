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
class Sesmusic_Widget_BrowseLyricsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (isset($_POST['params']))
      $params = json_decode($_POST['params'], true);

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $authorizationApi = Engine_Api::_()->authorization();

    $this->view->viewmore = $this->_getParam('viewmore', 0);
    $this->view->paginationType = $paginationType = $this->_getParam('paginationType', 1);
    $this->view->viewType = $viewType = isset($params['viewType']) ? $params['viewType'] : $this->_getParam('viewType', 'gridview');
    $this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width', 200);
    $this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height', 200);
    $this->view->widgteName = $widgteName = isset($params['widgteName']) ? $params['widgteName'] : $this->_getParam('widgteName', 'Lyrics Action');
    
   // $params['widgteName'] = 'Lyrics Action';
    $this->view->information = $information = isset($params['information']) ? $params['information'] : $this->_getParam('information', array('playCount', 'downloadCount', 'likeCount', 'commentCount', 'viewCount', 'favouriteCount', 'ratingStars', 'artists', 'addplaylist', 'downloadIcon', 'share', 'report', 'favourite'));


    if ($this->view->viewmore)
      $this->getElement()->removeDecorator('Container');


    $itemCount = isset($params['itemCount']) ? $params['itemCount'] : $this->_getParam('itemCount', 10);


    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();

    $this->view->params = $params = array('paginationType' => $paginationType, 'information' => $information, 'itemCount' => $itemCount, 'column' => '*', 'viewType' => $viewType, 'widgteName' => $widgteName);

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    //Songs settings.
    $this->view->songlink = unserialize($settings->getSetting('sesmusic.songlink'));

    $this->view->canAddPlaylistAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addplaylist_albumsong');

    $this->view->canAddFavouriteAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addfavourite_albumsong');

    $this->view->downloadAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'download_albumsong');

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

    
    $params['popularity'] = $this->_getParam('popularity', 'creation_date');
    $select = Engine_Api::_()->getDbtable('albumsongs', 'sesmusic')->widgetResults($params);
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($itemCount);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->count = $paginator->getTotalItemCount();
  }

}