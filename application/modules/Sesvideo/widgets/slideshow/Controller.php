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

class Sesvideo_Widget_SlideshowController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $coreApi = Engine_Api::_()->core();
    $this->view->full_width = $this->_getParam('full_width', 1);
    $this->view->logo = $this->_getParam('logo', 1);
    $this->view->height = $this->_getParam('height', '583');
		 $this->view->autoplay = $this->_getParam('autoplay', '1');
    $this->view->gallery_id = $gallery_id = $this->_getParam('gallery_id', 0);
		$this->view->searchEnable = $searchEnable = $this->_getParam('searchEnable',1);
		$this->view->thumbnail = $this->_getParam('thumbnail',1);
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if (!$gallery_id)
      return $this->setNoRender();
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('slides', 'sesvideo')->getSlides($gallery_id);
    if (count($paginator) == 0)
      return $this->setNoRender();
    $this->view->main_navigation = $main_navigation = $this->_getParam('main_navigation', 0);
		$this->view->mini_navigation = $mini_navigation = $this->_getParam('mini_navigation', 0);
    if ($main_navigation) {
      //main menu widget
      $this->view->navigation = $navigation = Engine_Api::_()
              ->getApi('menus', 'core')
              ->getNavigation('core_main');
      $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
      $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
      if (!$require_check && !$viewer->getIdentity()) {
        $navigation->removePage($navigation->findOneBy('route', 'user_general'));
      }
    }
		$this->view->menumininavigation = $menumininavigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_mini');
  	
		 if( $viewer->getIdentity() )
    {
      $this->view->notificationCount = Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer);
    }
		$request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->notificationOnly = $request->getParam('notificationOnly', false);
    $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.notificationupdate');
	}
}
