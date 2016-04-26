<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_BrowsenevigationSitegroupController extends Engine_Content_Widget_Abstract {

  protected $_navigation;

  public function indexAction() {


    $front = Zend_Controller_Front::getInstance(); 
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $controllerName = $front->getRequest()->getControllerName();    
    if($controllerName == 'album' && $action == 'browse') {
      //GET NAVIGATION TABS 
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main', array(), 'sitegroup_main_album');
    }
    elseif($module == 'sitegroupvideo' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main', array(), 'sitegroup_main_video');
    }
    elseif($module == 'sitegroupdocument' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main', array(), 'sitegroup_main_document');
    }
    elseif($module == 'sitegroupevent' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main', array(), 'sitegroup_main_event');
    }
    elseif($module == 'sitegroupnote' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main', array(), 'sitegroup_main_note');
    }
    elseif($module == 'sitegroupmusic' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main', array(), 'sitegroup_main_music');
    }
    elseif($module == 'sitegroupmember' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main', array(), 'sitegroup_main_member');
    }
    elseif($module == 'sitegroupreview' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main', array(), 'sitegroup_main_review');
    }
    elseif($module == 'sitegroupoffer' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main', array(), 'sitegroup_main_offer');
    }
    elseif($module == 'sitegroupevent' && $action == 'by-locations') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main', array(), 'sitegroup_main_event');
    }
    else {
    //GET NAVIGATION TABS 
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main');
    }
  }
}

?>