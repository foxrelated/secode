<?php
class Winkgreeting_AdminSettingsController extends Core_Controller_Action_Admin
{

  public function indexAction() 
  {
	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('winkgreeting_admin_main', array(), 'winkgreeting_admin_main_settings');

	$settings = Engine_Api::_()->getApi('settings', 'core');
	
	$form = $this->view->form = new Winkgreeting_Form_Admin_Settings();

	if (isset($settings->winkgreeting_wink))    
	  $form->getElement('wink')->setValue($settings->winkgreeting_wink);
	if (isset($settings->winkgreeting_greeting))    
	  $form->getElement('greeting')->setValue($settings->winkgreeting_greeting);
	if (isset($settings->winkgreeting_confirm))    
	  $form->getElement('confirm')->setValue($settings->winkgreeting_confirm);	  

	if ( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $settings->winkgreeting_wink = $this->_getParam('wink', false);
	  $settings->winkgreeting_greeting = $this->_getParam('greeting', false);
	  $settings->winkgreeting_confirm = $this->_getParam('confirm', false);
      $form->addNotice('Settings saved');
	}
  }

  public function levelAction()
  {
	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('winkgreeting_admin_main', array(), 'winkgreeting_admin_main_level');
	  
		// Get level id
		if( null !== ($id = $this->_getParam('level_id')) ) 
			$level = Engine_Api::_()->getItem('authorization_level', $id);
		else
			$level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();

		if( !$level instanceof Authorization_Model_Level )
			throw new Engine_Exception('missing level');
    
		$id = $level->level_id;

		// Make form
		$this->view->form = $form = new Winkgreeting_Form_Admin_Level();
		$form->level_id->setValue($id);
		if ($id == 5) { //Public Level
		   $form->wink->disabled = 'disabled';
		   $form->greeting->disabled = 'disabled';
		}
		$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

		// Check post
		if( !$this->getRequest()->isPost() )
		{
			$form->populate($permissionsTable->getAllowed('winkgreeting', $id, array_keys($form->getValues())));
			return;
		}

		// Check validitiy
		if( !$form->isValid($this->getRequest()->getPost()) )
			return;
		
		// Process
		$values = $form->getValues();
		$values = array('wink' => $values['wink'], 'greeting' => $values['greeting']);

		$db = $permissionsTable->getAdapter();
		$db->beginTransaction();

		try
		{
			// Set permissions
			$permissionsTable->setAllowed('winkgreeting', $id, $values);

			// Commit
			$db->commit();
			
			$form->addNotice('Save ok');
		}

		catch( Exception $e )
		{
			$db->rollBack();
			throw $e;
		}
  
  }
    
  //More Plugins
  public function moreAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('winkgreeting_admin_main', array(), 'winkgreeting_admin_main_more');
  }
}