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
class Sesmusic_Widget_ManageAlbumSongsController extends Engine_Content_Widget_Abstract {

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
    $this->view->information = $information = isset($params['information']) ? $params['information'] : $this->_getParam('information', array('playCount', 'downloadCount', 'likeCount', 'commentCount', 'viewCount', 'favouriteCount', 'ratingStars', 'artists', 'addplaylist', 'downloadIcon', 'share', 'report', 'favourite'));

    $HTTP_REFERER = $_SERVER['REQUEST_URI'];
    if (!empty($HTTP_REFERER) && strstr($HTTP_REFERER, '/song/')) {
      $refere_array = explode('/song/', $HTTP_REFERER);
      $this->view->action = $action = $refere_array['1'];
    }
    
    if ($this->view->viewmore)
      $this->getElement()->removeDecorator('Container');
    $itemCount = isset($params['itemCount']) ? $params['itemCount'] : $this->_getParam('itemCount', 10);
    $action = isset($params['action']) ? $params['action'] : $this->_getParam('action', $action);

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();

    $this->view->params = $params = array('paginationType' => $paginationType, 'information' => $information, 'itemCount' => $itemCount, 'column' => '*', 'viewType' => $viewType, 'action' => $action);

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


    if ($action == 'favourite-songs') {
      $this->view->resultShow = '%s favorite song.';
      $this->view->resultsShow = '%s favorite songs.';
      $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->getFavourites(array('resource_type' => 'sesmusic_albumsong'));
    } elseif ($action == 'like-songs') {
      $this->view->resultShow = '%s liked song.';
      $this->view->resultsShow = '%s liked songs.';
      $this->view->paginator = $paginator = Engine_Api::_()->sesmusic()->getLikesContents(array('resource_type' => 'sesmusic_albumsong'));
    } elseif ($action == 'rated-songs') {
      $this->view->resultShow = '%s rated song.';
      $this->view->resultsShow = '%s rated songs.';
      $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('ratings', 'sesmusic')->getRatedItems(array('resource_type' => 'sesmusic_albumsong'));
    }

    $paginator->setItemCountPerPage($itemCount);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->count = $paginator->getTotalItemCount();
  }

}