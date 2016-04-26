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
class Sesvideo_Widget_BrowseArtistsController extends Engine_Content_Widget_Abstract {
  public function indexAction() {
    if (isset($_POST['params']))
      $params = json_decode($_POST['params'],true);
			
    $values = array();
    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->view->viewer_id = $viewer->getIdentity();
    $this->view->viewmore = $this->_getParam('viewmore', 0);
    $this->view->paginationType = $paginationType = $this->_getParam('paginationType', 1);

    $this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width', 200);

    $this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height', 200);

    $this->view->information = $information = isset($params['information']) ? $params['information'] : $this->_getParam('information', array('showfavourite', 'showrating'));
    $itemCount = isset($params['itemCount']) ? $params['itemCount'] : $this->_getParam('itemCount', 10);

    $popularity = isset($params['popularity']) ? $params['popularity'] : $this->_getParam('popularity', 'order');

    $title = isset($_GET['title_name']) ? $_GET['title_name'] : (isset($params['title_name']) ? $params['title_name'] : '');

    $values['alphabet'] = isset($_GET['alphabet']) ? $_GET['alphabet'] : (isset($params['alphabet']) ? $params['alphabet'] : '');

    $this->view->all_params = $values = array('paginationType' => $paginationType, 'width' => $width, 'height' => $height, 'information' => $information, 'itemCount' => $itemCount, 'popularity' => $popularity, 'name' => $title, 'alphabet' => $values['alphabet']);

    if ($this->view->viewmore)
      $this->getElement()->removeDecorator('Container');

    //Artists settings.
    $this->view->artistlink = unserialize($settings->getSetting('sesvideo.artistlink'));

    $allowShowRating = $settings->getSetting('sesvideo.rateartist.show', 1);
    $allowRating = $settings->getSetting('sesvideo.artist.rating', 1);
    if ($allowRating == 0) {
      if ($allowShowRating == 0)
        $showRating = false;
      else
        $showRating = true;
    } else
      $showRating = true;
    $this->view->showArtistRating = $showRating;

    $values['widgteName'] = 'Browse Artists';

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('artists', 'sesvideo')->getArtistsPaginator($values);
    $paginator->setItemCountPerPage($itemCount);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->count = $paginator->getTotalItemCount();
  }

}
