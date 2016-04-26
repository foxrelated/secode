<?php

 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: AdminSettingsController.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Birthday_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
	    ->getNavigation('birthday_admin_main', array(), 'birthday_admin_main_settings');

    // generate the form
    $this->view->form  = $form = new Birthday_Form_Admin_Global();

    if( $this->getRequest()->isPost() ) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
				Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
			$this->_helper->redirector->gotoRoute(array('route' => 'admin-default'));
    }
  }

	public function birthdayemailAction() {
		// Here redirect to 'Birthday Email' tab and check that 'Birthday email' plugin should be install.
		$isEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('birthdayemail');
		if( !empty($isEnable) ) {
			$this->_helper->redirector->gotoRoute(array('module' => 'birthdayemail', 'controller' => 'settings'), 'admin_default', true);
		}	else {
			$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
					->getNavigation('birthday_admin_main', array(), 'birthdayemail_admin_main_settings');
		}
	}

  public function faqAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('birthday_admin_main', array(), 'birthday_admin_main_faq');
  }
}
