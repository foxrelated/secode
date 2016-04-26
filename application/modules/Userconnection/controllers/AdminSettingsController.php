<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Userconnection_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
		$update_table = Engine_Api::_()->getDbtable('menuItems', 'core');
		$update_name = $update_table->info('name');
		$check_table = $update_table->select()
			->from($update_name, array('id'))
			->where('name = ?', 'core_admin_main_plugins_Userconn');
		$fetch_result = $check_table->query()->fetchAll();
		if( !empty($fetch_result) ) {
			$update_table->update(array("params" => '{"route":"admin_default","module":"userconnection","controller":"settings"}'), array('name =?' => 'core_admin_main_plugins_Userconn'));
		}
  	// Redirecting to "Global page".
  	$this->_helper->redirector->gotoRoute(array('action' => 'index', 'controller' => 'global'));
  }
}