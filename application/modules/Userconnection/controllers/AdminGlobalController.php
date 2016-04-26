<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminGlobalController.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Userconnection_AdminGlobalController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
		if( !empty($_POST['user_licensekey']) ) { $_POST['user_licensekey'] = trim($_POST['user_licensekey']); }
		$userconnection_form_content = array('structure', 'userconnection_message', 'show_msg', 'arrow', 'indicators', 'submit');
    include_once(APPLICATION_PATH ."/application/modules/Userconnection/controllers/license/license1.php");
		Engine_Api::_()->getDbtable('menuItems', 'core')->delete(array('name = ?' => 'core_main_Userconnection'));
  }

  public function faqAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('Userconnection_admin', array(), 'Userconnection_admin_main_faq');
  }

	// This is the 'readme Action' which will call first time only when plugin will install.
  public function readmeAction() {}
}