<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Userconnection_IndexController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
    $this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
		if( !$this->_helper->requireUser()->isValid() ) return;
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('Userconnection_main');
		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		//Call function from core.php
		$userconnection_combind_path_contacts_array = Engine_Api::_()->userconnection()->user_connection_path($user_id, 0, 4, "user_home");
		$user_contacts_degree = $userconnection_combind_path_contacts_array[1];
		$user_ids_firstlevel = array_keys ($user_contacts_degree,1);
		// Set the Pagination for "Second Level Friends".
		$first_user_object = Engine_Api::_()->userconnection()->level_fetch_data($user_ids_firstlevel);
		if(!empty($first_user_object))
		{
			$this->view->first_degree_fetch_record = $paginator = Zend_Paginator::factory($first_user_object);
			$paginator->setCurrentPageNumber($this->_getParam('page'));
			$paginator->setItemCountPerPage(20);
		}else {
			$this->view->first_degree_fetch_record = $first_user_object;
		}
	}

	//Display the SecondLevelFriends.	 
	public function secondlevelfriendsAction()
	{
		if( !$this->_helper->requireUser()->isValid() ) return;
		// Send Base URL in tpl.
		$this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
		
		// Get navigation
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('Userconnection_main');
		
		//CURRENT USER ID!
		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		//Call function from core.php
		$userconnection_combind_path_contacts_array = Engine_Api::_()->userconnection()->user_connection_path($user_id, 0, 4, "user_home");
		$user_contacts_degree = $userconnection_combind_path_contacts_array[1];
		$user_ids_secondlevel = array_keys ($user_contacts_degree,2);
		// Set the Pagination for "Second Level Friends".
		$second_user_object = Engine_Api::_()->userconnection()->level_fetch_data($user_ids_secondlevel);
		if(!empty($second_user_object))
		{
			$this->view->second_degree_fetch_record = $paginator = Zend_Paginator::factory($second_user_object);
			$paginator->setCurrentPageNumber($this->_getParam('page'));
			$paginator->setItemCountPerPage(20);
		}
		else {
			$this->view->second_degree_fetch_record = $second_user_object;
		}
	}

	// Display the ThirdLevelFriends.
	public function thirdlevelfriendsAction()
	{
		if( !$this->_helper->requireUser()->isValid() ) return;
		// Send Base URL in tpl.
		$this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
		
		// Get navigation
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('Userconnection_main');
		//CURRENT USER ID!
		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		//Call function from core.php
		$userconnection_combind_path_contacts_array = Engine_Api::_()->userconnection()->user_connection_path($user_id, 0 ,4, "user_home");

		$user_contacts_degree = $userconnection_combind_path_contacts_array[1];
		$id = array_keys ($user_contacts_degree,3);
		$third_user_object = Engine_Api::_()->userconnection()->level_fetch_data($id);
		if(!empty($third_user_object))
		{
		$this->view->third_degree_fetch_record = $paginator = Zend_Paginator::factory($third_user_object);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setItemCountPerPage(20);
		}
		else {
			$this->view->third_degree_fetch_record = $third_user_object;
		}
	}

	// Actions for connection settings
	public function connectionsettingsAction()
	{
		if( !$this->_helper->requireUser()->isValid() ) return;
		//CURRENT USER ID!
		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		// Get navigation
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('Userconnection_main');
		// Make the object for connection setting form
		$this->view->form = $form = new Userconnection_Form_connectionsettingform();
		$userconnection_host_name = str_replace('www.','',strtolower($_SERVER['HTTP_HOST']));
		//check the ID from userconnection table
		$table  = Engine_Api::_()->getItemTable('userconnection');
		$select = $table->select()->where("user_id = $user_id");
		$fetch_record = $table->fetchAll($select);
		$userid_check = 0;
		foreach( $fetch_record as $row ){
			if($user_id==$row->user_id){
				$userid_check = 1;
				$currnt_user_userconnection_id = $row->userconnection_id;
			}
		}
		$communityad_ads_field = convert_uuencode($userconnection_host_name);
		Engine_Api::_()->getApi('settings', 'core')->setSetting('userconnection.path.name', $communityad_ads_field);
		//IF ID NOT EXIST THEN INSERT THE DATA
		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) && $userid_check==0) 
		{
			$connection_setting_table = Engine_Api::_()->getItemTable('userconnection');
			$viewer = Engine_Api::_()->user()->getViewer();
			$values = $form->getValues();
			$check_value = $values['connection'];
			if($check_value==1){
				// Begin database transaction
				$db = $connection_setting_table->getAdapter();
				$db->beginTransaction();
				try{
					$connection_setting_row = $connection_setting_table->createRow();
					$connection_setting_row->setFromArray($values);
					$connection_setting_row->userconnection_id = $viewer->getIdentity();
					$connection_setting_row->user_id = $viewer->getIdentity();
					$connection_setting_row->save();
					$db->commit();
				}
				catch( Exception $e ){
					$db->rollBack();
					throw $e;
				}
			}
		}
		elseif($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) && $userid_check==1){
			$values = $form->getValues();
			$check_value = $values['connection'];
			if ($check_value==0) {
				$user_setting = Engine_Api::_()->getItem('userconnection', $currnt_user_userconnection_id);
				$user_setting->delete();
			}
		}
	}
}