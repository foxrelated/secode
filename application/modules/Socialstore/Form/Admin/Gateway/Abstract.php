<?php

class Socialstore_Form_Admin_Gateway_Abstract extends Engine_Form {

	protected $_module = 'socialstore';
	
	public function init() {
		$this -> setTitle('Payment Gateway');

		// Element: enabled
		$this -> addElement('Radio', 'enabled', array('label' => 'Enabled?', 'multiOptions' => array('1' => 'Yes', '0' => 'No', ), 'order' => 10000, ));

		// Element: execute
		$this -> addElement('Button', 'execute', array('label' => 'Save Changes', 'type' => 'submit', 'decorators' => array('ViewHelper'), 'order' => 10001, 'ignore' => true, ));

		// Element: cancel
		$this -> addElement('Cancel', 'cancel', array('label' => 'cancel', 'prependText' => ' or ', 'link' => true, 'href' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'index', 'gateway_id' => null)), 'decorators' => array('ViewHelper'), 'order' => 10002, 'ignore' => true, ));

		// DisplayGroup: buttons
		$this -> addDisplayGroup(array('execute', 'cancel'), 'buttons', array('decorators' => array('FormElements', 'DivDivDivWrapper', ), 'order' => 10003, ));
	}

}
