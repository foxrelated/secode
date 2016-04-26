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
class Sesmusic_Widget_BrowseAlbumsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (isset($_POST['params']))
      $params = json_decode($_POST['params'], true);
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $authorizationApi = Engine_Api::_()->authorization();

    $this->view->viewer_id = $viewer->getIdentity();
    $this->view->viewmore = $this->_getParam('viewmore', 0);

    $this->view->paginationType = $paginationType = $this->_getParam('paginationType', 1);
    $this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width', 200);
    $this->view->viewType = $viewType = isset($params['viewType']) ? $params['viewType'] : $this->_getParam('viewType', 'gridview');    
    $this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height', 200);
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

    $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : (isset($params['category_id']) ? $params['category_id'] : '');
    $subcat_id = isset($_GET['subcat_id']) ? $_GET['subcat_id'] : (isset($params['subcat_id']) ? $params['subcat_id'] : '');
    $subsubcat_id = isset($_GET['subsubcat_id']) ? $_GET['subsubcat_id'] : (isset($params['subsubcat_id']) ? $params['subsubcat_id'] : '');
    $alphabet = isset($_GET['alphabet']) ? $_GET['alphabet'] : (isset($params['alphabet']) ? $params['alphabet'] : '');
    $showPhoto = isset($_GET['showPhoto']) ? $_GET['showPhoto'] : (isset($params['showPhoto']) ? $params['showPhoto'] : '');
    
    $popularity = isset($_GET['popularity']) ? $_GET['popularity'] : (isset($params['popularity']) ? $params['popularity'] : 'creation_date');
    
    
    $title = isset($_GET['title_name']) ? $_GET['title_name'] : (isset($params['title_name']) ? $params['title_name'] : '');
    $show = isset($_GET['show']) ? $_GET['show'] : (isset($params['show']) ? $params['show'] : '');
    $itemCount = isset($params['itemCount']) ? $params['itemCount'] : $this->_getParam('itemCount', 10);
    $artists = isset($_GET['artists']) ? $_GET['artists'] : (isset($params['artists']) ? $params['artists'] : '');

    $users = array();
    if (isset($_GET['show']) && $_GET['show'] == 2 && $viewer->getIdentity()) {
      $users = $viewer->membership()->getMembershipsOfIds();
    }

    $this->view->all_params = $values = array('paginationType' => $paginationType, 'width' => $width, 'height' => $height, 'information' => $information, 'category_id' => $category_id, 'subcat_id' => $subcat_id, 'subsubcat_id' => $subsubcat_id, 'alphabet' => $alphabet, 'title' => $title, 'showPhoto' => $showPhoto, 'popularity' => $popularity, 'show' => $show, 'users' => $users, 'itemCount' => $itemCount, 'artists' => $artists, 'viewType' => $viewType);

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

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('albums', 'sesmusic')->getPlaylistPaginator($values);
    $paginator->setItemCountPerPage($itemCount);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->count = $paginator->getTotalItemCount();
  }

}