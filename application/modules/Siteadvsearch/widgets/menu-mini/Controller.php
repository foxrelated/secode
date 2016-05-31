<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Widget_MenuMiniController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $siteadvsearch_menu_mini = Zend_Registry::isRegistered('siteadvsearch_menu_mini') ? Zend_Registry::get('siteadvsearch_menu_mini') : null;
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewerId = $viewer->getIdentity();

    $coreApi = Engine_Api::_()->getApi('settings', 'core');
    $require_check = $coreApi->core_general_search;
    if (!$require_check) {
      if ($viewerId)
        $this->view->search_check = true;
      else
        $this->view->search_check = false;
    }
    else
      $this->view->search_check = true;

    if (empty($siteadvsearch_menu_mini))
      return $this->setNoRender();

    $this->view->navigation = Engine_Api::_()
            ->getApi('menus', 'core')
            ->getNavigation('core_mini');

    if ($viewerId)
      $this->view->notificationCount = Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer);
    $this->view->searchbox_width = $this->_getParam('advsearch_search_width', 275);
    $this->view->updateSettings = $coreApi->getSetting('core.general.notificationupdate');
  }

}