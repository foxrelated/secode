<?php

class Socialstore_AdminGatewayController extends Core_Controller_Action_Admin {

	public function init() {
		parent::init();
		Zend_Registry::set('admin_active_menu', 'socialstore_admin_main_gateway');

	}

	public function indexAction() {

		// Test curl support
		if(!function_exists('curl_version') || !($info = curl_version())) {
			$this -> view -> error = $this -> view -> translate('The PHP extension cURL ' . 'does not appear to be installed, which is required ' . 'for interaction with payment gateways. Please contact your ' . 'hosting provider.');
		} else if(!($info['features'] & CURL_VERSION_SSL) || !in_array('https', $info['protocols'])) {
			$this -> view -> error = $this -> view -> translate('The installed version of ' . 'the cURL PHP extension does not support HTTPS, which is required ' . 'for interaction with payment gateways. Please contact your ' . 'hosting provider.');
		}

		// Make paginator
		$select = Engine_Api::_() -> getDbtable('gateways','Socialstore') -> select();
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
	}

	public function editAction() {
		// Get gateway

		$gateway_id = $this -> _getParam('gateway_id', '');
		$gateway = Engine_Api::_() -> getDbtable('gateways','Socialstore') -> find($gateway_id) -> current();

		// Make form
		$this -> view -> form = $form = $gateway -> getAdminGatewayForm();

		// Populate form
		$form -> populate($gateway -> toArray());
		if ($gateway->getConfig()) {
			$form -> populate($gateway -> getConfig());
		}
		// Check method/valid
		if(!$this -> getRequest() -> isPost()) {
			return ;
		}
		if(!$form -> isValid($this -> getRequest() -> getPost())) {
			return ;
		}

		// Process
		$values = $form -> getValues();

		$enabled = (bool)$values['enabled'];

		$gateway -> setFromArray($values);
		$gateway -> setConfig($values);
		$gateway -> save();
		$form -> addNotice("Save changed!");
	}

	public function deleteAction() {
		$this -> view -> form = $form = new Socialstore_Form_Admin_Gateway_Delete();
	}

}
