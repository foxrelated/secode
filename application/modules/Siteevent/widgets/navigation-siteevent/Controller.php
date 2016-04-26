<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_NavigationSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $moduleName = Engine_API::_()->seaocore()->isSiteMobileModeEnabled() ? 'sitemobile' : 'core';
        $this->view->navigationTabTitle = $this->view->translate($this->_getParam('navigationTabTitle', 'Events'));
        $this->view->package_show = Zend_Controller_Front::getInstance()->getRequest()->getParam('package', 0);
           
        //$this->view->navigationTabCount = $this->_getParam('navigationTabCount', 8);
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', $moduleName)->getNavigation("siteevent_main");
        
    }

}