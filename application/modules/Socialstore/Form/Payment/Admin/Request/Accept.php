<?php

class Socialstore_Form_Payment_Admin_Request_Accept extends Engine_Form {

	public function init() {
	$this -> setTitle('Send Request') -> setDescription('Send a money request to administrators.');

	$this->addElement('text','request_amount',array(
		'label'=>'Request Amount',
		'required'=>true,
		'value'=>'10.00',
		'filters'=>array('StringTrim'),
		'validators'=>array('Float'),
	));
	
	$this->addElement('textarea','response_message',array(
		'label'=>'Request Message',
		'filters'=>array('StringTrim'),
	));

	/**
	 * add button groups
	 */
	$this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	
	
    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'socialstore_mystore_general', true),
      'onclick' => '',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
     // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'submit',
    	'cancel',
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));

	}

}
