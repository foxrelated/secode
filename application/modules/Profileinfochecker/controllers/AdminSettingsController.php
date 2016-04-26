<?php
class Profileinfochecker_AdminSettingsController extends Core_Controller_Action_Admin
{

  public function indexAction() 
  {
	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('profileinfochecker_admin_main', array(), 'profileinfochecker_admin_main_settings');

	$settings = Engine_Api::_()->getApi('settings', 'core');
	
	$form = $this->view->form = new Profileinfochecker_Form_Admin_Settings();

	if (isset($settings->checker_percent))    
	  $form->getElement('percent')->setValue($settings->checker_percent);
	if (isset($settings->checker_bgcolor))    
	  $form->getElement('bgcolor')->setValue($settings->checker_bgcolor);
	if (isset($settings->checker_tcolor))    
	  $form->getElement('tcolor')->setValue($settings->checker_tcolor);

	if ( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
	  $settings->checker_percent = $this->_getParam('percent', 100);
      $settings->checker_bgcolor = $this->_getParam('bgcolor', '#5f93b4');
	  $settings->checker_tcolor = $this->_getParam('tcolor', '#d0e2ec');
      $form->addNotice('Settings saved');
	}
  }
  
  //More Plugins
  public function moreAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('profileinfochecker_admin_main', array(), 'profileinfochecker_admin_main_more');
  }
}