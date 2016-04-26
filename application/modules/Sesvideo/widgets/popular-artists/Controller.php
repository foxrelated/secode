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

class Sesvideo_Widget_PopularArtistsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->viewType = $this->_getParam('viewType', 'gridview');
    $this->view->height = $this->_getParam('height', 200);
    $this->view->width = $this->_getParam('width', 100);
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$this->view->information = $this->_getParam('information',array('title','favouriteCount','ratingCount'));
    $params = array();
    $params['popularity'] = $this->_getParam('popularity', 'favourite_count');
    $params['limit'] = $this->_getParam('limit', 3);
    $this->view->results = Engine_Api::_()->getDbtable('artists', 'sesvideo')->getArtistsPaginator($params);
    if (count($this->view->results) <= 0)
      return $this->setNoRender();
  }

}
