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

class Sesvideo_Widget_VideoCategoryController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->height = $this->_getParam('height', '160px');
    $this->view->width = $this->_getParam('width', '260px');
		$this->view->mouse_over_title = $this->_getParam('mouse_over_title', '1');
    $params['criteria'] = $this->_getParam('criteria', '');
		$params['limit'] = $this->_getParam('limit', 0);
		$params['video_required'] = $this->_getParam('video_required',0);
    $show_criterias = $this->_getParam('show_criteria', array('title', 'countVideos', 'icon'));
    if (in_array('countVideos', $show_criterias))
      $params['countVideos'] = true;
		if($params['video_required'])
			$params['videoRequired'] = true;
    foreach ($show_criterias as $show_criteria)
      $this->view->$show_criteria = $show_criteria;
    // Get videos
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('categories', 'sesvideo')->getCategory($params);
    if (count($paginator) == 0)
      return;
  }

}
