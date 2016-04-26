<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_NewgroupSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->creationLink = $this->_getParam('creationLink', 1);
    
    if($this->view->creationLink) {
    //GET QUICK NAVIGATION
    $this->view->quickNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_quick');
    }
    else {
      //WHO CAN CREATE STORE
      $menusClass = new Sitegroup_Plugin_Menus();
      $this->view->canCreateGroups = $menusClass->canCreateSitegroups();
      $this->view->canCreate = 1;
    }
    
  }
}

?>