<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminMessageController.php 9719 2012-05-16 23:19:40Z richard $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Qrcode_AdminQrmappingController extends Core_Controller_Action_Admin
{
	
	public function settingAction()
	
	{
		
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('qrcode_admin_main', array(), 'qrcode_admin_main_setting');
		
		$settings = Engine_Api::_()->getDbtable('settings', 'core');
		$this->view->form = $form = new Qrcode_Form_Admin_Mapping_Setting();
		$form->getElement('website')->setValue($settings->__get( 'qrcode.website'));
		$form->getElement('phone')->setValue($settings->__get( 'qrcode.phone'));
		$form->getElement('contact')->setValue($settings->__get( 'qrcode.contact'));
	
		if( !$this->getRequest()->isPost() ) {
			return;
		}

		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}
		$website = $this->_getParam('website');
		$phone = $this->_getParam('phone');
		$contact = $this->_getParam('contact');
	
		try {
			 

			$settings->insert(array(
				           'name' => 'qrcode.website',
					       'value' => $website
			));
			$settings->insert(array(
							'name' => 'qrcode.phone',
							'value' => $phone
			));
		
	
			$settings->insert(array(
							 'name' => 'qrcode.contact',
                             'value' => implode(',', $contact)
			));
		} catch (Exception $e) {
			try{
				$settings->__set( 'qrcode.website',$website);
				$settings->__set( 'qrcode.phone',$phone);
				$settings->__set( 'qrcode.contact',implode(',', $contact));
			} catch (Exception $e) {
			}
		}
	}
	public function updatesAction(){
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('qrcode_admin_main', array(), 'qrcode_admin_main_updates');
	}
}
