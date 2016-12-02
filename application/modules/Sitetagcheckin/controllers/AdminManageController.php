<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR SHWOING THE CONTENT TYPE WHICH ARE TAGGABLE
  public function indexAction() {

    //NOT VALID USER THE RETURN
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitetagcheckin_admin_main', array(), 'sitetagcheckin_admin_manage_modules');

    //GET ENABLED MODUULES
    $this->view->enabled_modules_array = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();

    //GET PAGE 
    $page = $this->_getParam('page', 1);

    //SET PAGINATOR
    $this->view->paginator = Zend_Paginator::factory(Engine_Api::_()->getDbtable('contents', 'sitetagcheckin')->select());
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator->setCurrentPageNumber($page);

    //CHECK FORM SUBMISSTION
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $obj = Engine_Api::_()->getItem('sitetagcheckin_contents', $value);
          if (empty($obj->is_delete)) {
            $obj->delete();
          }
        }
      }
    }
  }

}