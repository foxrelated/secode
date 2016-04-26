<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLevelController.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Userconnection_AdminLevelController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    // For getting the tab from the database.
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
    ->getNavigation('Userconnection_admin', array(), 'Userconnection_admin_main_user');

		$session = new Zend_Session_Namespace();  	
    $level_id = $this->_getParam('id');// Get user level id.
    if(empty($level_id)) 
    { 
      $level_id = 1; 
    }

    if ($this->getRequest()->isPost()) 
    {
			$userconnection_admin_tab = 'member_level_settings';
			// Array which contain all form data with "Decorator" data.
			$from_values = $this->getRequest()->getPost();
			// Make final array for "Authrization Permition" which will insert in data base.
			$values = array("level_id" => $from_values['level_id'], "level" => $from_values['level']);
			include_once(APPLICATION_PATH ."/application/modules/Userconnection/controllers/license/license2.php");	
    }

    // Here we are make the session variable for prefield of decorator.
    $permissionTable   = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $PermisionName = $permissionTable->info('name');
    $select = $permissionTable->select()
      ->setIntegrityCheck(false)
      ->from($PermisionName, array('name', 'params', 'value'))
      ->where('level_id = ?', $level_id)
      ->where('type = ?', 'userconnection');
    $fetch_permision = $select->query()->fetchAll();
    foreach ($fetch_permision as $row_permition)
    {    	
			if($row_permition['name'] == 'level')
			{
				if(!empty($row_permition['params']))
				{    			
					$session->level = $row_permition['params'];
				}
				else {
					$session->level = $row_permition['value'];
				}
			}
    }

    // Make form for rendering.
    $this->view->form = $form = new Userconnection_Form_Admin_Level(array('public' => $level_id == 5));
    if (!empty($form->level_id)) {
      $form->level_id->setValue($level_id);
    }
    // Unset session value that value has been used in form.
    unset($session->level);
  }
}