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
class Sesmusic_Widget_ManageMusicAlbumsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (isset($_POST['params']))
      $params = json_decode($_POST['params'], true);

    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $authorizationApi = Engine_Api::_()->authorization();

    $this->view->viewer_id = $viewer->getIdentity();
    $this->view->viewmore = $this->_getParam('viewmore', 0);

    $HTTP_REFERER = $_SERVER['REQUEST_URI'];
    if (!empty($HTTP_REFERER) && strstr($HTTP_REFERER, '/album/')) {
      $refere_array = explode('/album/', $HTTP_REFERER);
      $this->view->action = $action = $refere_array['1'];
    }
    
    $this->view->paginationType = $paginationType = $this->_getParam('paginationType', 1);
    $this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width', 200);
    $this->view->viewType = $viewType = isset($params['viewType']) ? $params['viewType'] : $this->_getParam('viewType', 'gridview');
    $this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height', 200);
    $action = isset($params['action']) ? $params['action'] : $this->_getParam('action', $action);
    
    $this->view->information = $information = isset($params['information']) ? $params['information'] : $this->_getParam('information', array('featured', 'sponsored', 'hot', 'likeCount', 'commentCount', 'viewCount', 'title', 'postedby', 'favourite', 'addplaylist', 'share', 'ratingStars'));

    if ($this->view->viewmore)
      $this->getElement()->removeDecorator('Container');

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();

    //Can create?
    $this->view->canCreate = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'create');
    $this->view->canAddPlaylist = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addplaylist_album');
    $this->view->canAddFavourite = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addfavourite_album');
    $this->view->albumlink = unserialize($settings->getSetting('sesmusic.albumlink'));

    $itemCount = isset($params['itemCount']) ? $params['itemCount'] : $this->_getParam('itemCount', 10);

    $this->view->all_params = $values = array('paginationType' => $paginationType, 'width' => $width, 'height' => $height, 'information' => $information, 'itemCount' => $itemCount, 'viewType' => $viewType, 'action' => $action);

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


    if ($action == 'favourite-albums') {
      $this->view->resultShow = '%s favorite music album.';
      $this->view->resultsShow = '%s favorite music albums.';
      $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->getFavourites(array('resource_type' => 'sesmusic_album'));
    } elseif ($action == 'like-albums') {
      $this->view->resultShow = '%s liked music album.';
      $this->view->resultsShow = '%s liked music albums.';
      $this->view->paginator = $paginator = Engine_Api::_()->sesmusic()->getLikesContents(array('resource_type' => 'sesmusic_album'));
    } elseif ($action == 'rated-albums') {
      $this->view->resultShow = '%s rated music album.';
      $this->view->resultsShow = '%s rated music albums.';
      $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('ratings', 'sesmusic')->getRatedItems(array('resource_type' => 'sesmusic_album'));
    }
    $paginator->setItemCountPerPage($itemCount);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->count = $paginator->getTotalItemCount();
  }

}