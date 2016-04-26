<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminExtensionController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Siteevent_AdminExtensionController extends Core_Controller_Action_Admin
{
//	public function indexAction()
//  {
//		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_extensions');
//	}
	public function upgradeAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_extensions');
	}

  public function informationAction()
  {
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_extensions');
  }

//  public function deletemoduleAction() {
//
//    //GET MODULE NAME
//    $moduleName = $this->_getParam('modulename');
//    $menuitemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
//    $selectMenuitemsTable = $menuitemsTable->select()->where('name =?', "core_admin_main_plugins_$moduleName");
//    $resultMenuitems = $menuitemsTable->fetchRow($selectMenuitemsTable);
//    if(!empty($resultMenuitems->enabled)) {
//    $name = $resultMenuitems->name;
//    $menuitemsTable->update(array('enabled' => '0')
//                , array(
//            'name =?' => $name
//        ));
//    }
//    else {
//      $name = $resultMenuitems->name;
//      $menuitemsTable->update(array('enabled' => '1')
//                , array(
//            'name =?' => $name
//        ));
//    }
//    $this->_redirect('admin/siteevent/extension');
//  }

}
?>