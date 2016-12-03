<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventpaid_Widget_ListPackagesController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->siteevent()->hasPackageEnable()) {
            //REDIRECT
            return $this->setNoRender();
        }

        $this->view->parent_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_id', null);
        //ADDED DUE TO PLUGIN INTEGRATION
        $this->view->parent_type = Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_type', null);
        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');

        //WIDGET SETTINGS ARRAY - INFO ARRAY WHICH IS TO BE SHOWN IN PACKAGE DETAILS.
        $this->view->packageInfoArray = $coreSettingsApi->getSetting('siteevent.package.information', array("price", "billing_cycle", "duration", "featured", "sponsored", "rich_overview", "videos", "photos", "description", "ticket_type"));
        $siteeventpaidListPackage = Zend_Registry::isRegistered('siteeventpaidListPackage') ? Zend_Registry::get('siteeventpaidListPackage') : null;

        $this->view->package_view = $coreSettingsApi->getSetting('siteevent.package.view', 1);
        $this->view->overview = $coreSettingsApi->getSetting('siteevent.overview', 1);
        $this->view->package_description = $coreSettingsApi->getSetting('siteevent.package.description', 1);

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $paginator = Engine_Api::_()->getDbtable('packages', 'siteeventpaid')->getPackagesSql($viewer_id);
        $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page'));
        
        if(empty($siteeventpaidListPackage)) {
          return $this->setNoRender();
        }
        
        $moduleName = Engine_API::_()->seaocore()->isSiteMobileModeEnabled() ? 'sitemobile' : 'core';
        $this->view->navigationTabTitle = $this->view->translate($this->_getParam('navigationTabTitle', 'Events'));
        $this->view->package_show = Zend_Controller_Front::getInstance()->getRequest()->getParam('package', 0);
           
        //$this->view->navigationTabCount = $this->_getParam('navigationTabCount', 8);
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', $moduleName)->getNavigation("siteevent_main");
    }

}
