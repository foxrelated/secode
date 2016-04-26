<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Widget_BrowsePlaylistsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (isset($_POST['params']))
      $params = json_decode($_POST['params'], true);

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();
    $this->view->viewmore = $this->_getParam('viewmore', 0);
    $this->view->paginationType = $paginationType = $this->_getParam('paginationType', 1);

    $this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width', 200);
    $this->view->popularity = $popularity = isset($params['popularity']) ? $params['popularity'] : $this->_getParam('popularity', 'creation_date');

    $this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height', 200);
		 $this->view->description_truncation = $description_truncation = isset($params['description_truncation']) ? $params['description_truncation'] : $this->_getParam('description_truncation', 60);
    $this->view->information = $information = isset($params['information']) ? $params['information'] : $this->_getParam('information', array('viewCount', 'title', 'postedby', 'share', 'watchLater', 'favouriteButton', 'showVideosList', 'playlist','description','favouriteCount','featuredLabel', 'sponsoredLabel','likeButton','socialSharing','likeCount'));
 		
    if ($this->view->viewmore)
      $this->getElement()->removeDecorator('Container');

    $alphabet = isset($_GET['alphabet']) ? $_GET['alphabet'] : (isset($params['alphabet']) ? $params['alphabet'] : '');
    $itemCount = isset($params['itemCount']) ? $params['itemCount'] : $this->_getParam('itemCount', 10);
    $popularity = isset($_GET['popularity']) ? $_GET['popularity'] : (isset($params['popularity']) ? $params['popularity'] : 'creation_date');


    $title = isset($_GET['title_name']) ? $_GET['title_name'] : (isset($params['title_name']) ? $params['title_name'] : '');
    $show = isset($_GET['show']) ? $_GET['show'] : (isset($params['show']) ? $params['show'] : 1);
    $users = array();
    if (isset($_GET['show']) && $_GET['show'] == 2 && $viewer->getIdentity()) {
      $users = $viewer->membership()->getMembershipsOfIds();
    }

    $action = isset($_GET['action']) ? $_GET['action'] : (isset($params['action']) ? $params['action'] : 'browse');

    $page = isset($_GET['page']) ? $_GET['page'] : $this->_getParam('page', 1);


    $this->view->all_params = $values = array('paginationType' => $paginationType, 'width' => $width, 'height' => $height, 'information' => $information, 'alphabet' => $alphabet, 'itemCount' => $itemCount, 'popularity' => $popularity, 'show' => $show, 'users' => $users, 'title' => $title, 'action' => $action, 'page' => $page,'description_truncation'=>$description_truncation);

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('playlists', 'sesvideo')->getPlaylistPaginator($values);
    $paginator->setItemCountPerPage($itemCount);
    $paginator->setCurrentPageNumber($page);
    $this->view->count = $paginator->getTotalItemCount();
  }

}
