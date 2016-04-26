<?php

class Socialstore_Form_Payment_Seller_Request extends Engine_Form {

	public function init() {
	$this -> setTitle('Send Request') -> setDescription('STORE_FORM_PAYMENT_SELLER_REQUEST_DESCRIPTION');
	
	
	
	$this->addElement('text','request_amount',array(
		'label'=>'Request Amount*',
		'required'=>true,
		'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('store.minrequest', 100.00),
		'filters'=>array('StringTrim'),
		'validators'=>array('Float'),
		'escape'=>false,
	));
	
	$this->addElement('textarea','request_message',array(
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
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'socialstore','controller'=>'my-account','action'=>'index'), 'default', true),
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
