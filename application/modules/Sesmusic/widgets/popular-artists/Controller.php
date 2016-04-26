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
class Sesmusic_Widget_PopularArtistsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->viewType = $this->_getParam('viewType', 'gridview');
    $this->view->height = $this->_getParam('height', 200);
    $this->view->width = $this->_getParam('width', 100);
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $params = array();
    $params['popularity'] = $this->_getParam('popularity', 'favourite_count');
    $params['limit'] = $this->_getParam('limit', 3);
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();
    $this->view->results = Engine_Api::_()->getDbtable('artists', 'sesmusic')->getArtistsPaginator($params);
    if (count($this->view->results) <= 0)
      return $this->setNoRender();
  }

}