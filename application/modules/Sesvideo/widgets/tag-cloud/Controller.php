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
class Sesvideo_Widget_tagCloudController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $countItem = $this->_getParam('itemCountPerPage', '25');
    $this->view->type = $type = $this->_getParam('type', 'video');
    $this->view->height = $this->_getParam('height', '300');
    $this->view->color = $this->_getParam('color', '#00f');
    $this->view->textHeight = $this->_getParam('text_height', '15');
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if ($type != 'video' && !$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
    $paginator = Engine_Api::_()->sesvideo()->tagCloudItemCore('', array('type' => $type));

    $this->view->paginator = $paginator;
    $paginator->setItemCountPerPage($countItem);
    $paginator->setCurrentPageNumber(1);
    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }
  }

}
