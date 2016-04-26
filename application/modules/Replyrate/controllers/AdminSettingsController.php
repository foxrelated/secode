<?php
class Replyrate_AdminSettingsController extends Core_Controller_Action_Admin
{

  public function indexAction() 
  {
	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('replyrate_admin_main', array(), 'replyrate_admin_main_settings');

	$settings = Engine_Api::_()->getApi('settings', 'core');
	
	$form = $this->view->form = new Replyrate_Form_Admin_Settings();

	if (isset($settings->reply_bgcolor))    
	  $form->getElement('bgcolor')->setValue($settings->reply_bgcolor);
	if (isset($settings->reply_tcolor))    
	  $form->getElement('tcolor')->setValue($settings->reply_tcolor);

	if ( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $settings->reply_bgcolor = $this->_getParam('bgcolor', '#5f93b4');
	  $settings->reply_tcolor = $this->_getParam('tcolor', '#d0e2ec');
      $form->addNotice('Settings saved');
	}
  }

  public function levelAction()
  {
	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('replyrate_admin_main', array(), 'replyrate_admin_main_level');
	  
		// Get level id
		if( null !== ($id = $this->_getParam('level_id')) ) 
			$level = Engine_Api::_()->getItem('authorization_level', $id);
		else
			$level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();

		if( !$level instanceof Authorization_Model_Level )
			throw new Engine_Exception('missing level');
    
		$id = $level->level_id;

		// Make form
		$this->view->form = $form = new Replyrate_Form_Admin_Level();
		$form->level_id->setValue($id);
		
		$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

		// Check post
		if( !$this->getRequest()->isPost() )
		{
			$form->populate($permissionsTable->getAllowed('replyrate', $id, array_keys($form->getValues())));
			return;
		}

		// Check validitiy
		if( !$form->isValid($this->getRequest()->getPost()) )
			return;
		
		// Process
		$values = $form->getValues();
		$values = array('view' => $values['view']);

		$db = $permissionsTable->getAdapter();
		$db->beginTransaction();

		try
		{
			// Set permissions
			$permissionsTable->setAllowed('replyrate', $id, $values);

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
      ->getNavigation('replyrate_admin_main', array(), 'replyrate_admin_main_more');
  }
}