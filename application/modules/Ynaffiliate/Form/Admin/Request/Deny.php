<?php

class Ynaffiliate_Form_Admin_Request_Deny extends Engine_Form {

	public function init() {
	$this->setAttribs(array(
		'class'=>'global_form_popup',
	));
	
	$this -> setTitle('Deny Request') -> setDescription('Send deny message to Seller.');

	$this->addElement('textarea','response_message',array(
		'label'=>'Request Message',
		'filters'=>array('StringTrim'),
	));
	$this->response_message->getDecorator("Description")->setOption("placement", "append");
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
      'onclick' => 'parent.Smoothbox.close();',
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
