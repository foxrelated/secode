<?php

class Socialstore_Form_Payment_Gateway extends Engine_Form{
	public function init()
    {
       //Set Form Informations
    $this -> setAttribs(array('class' => 'global_form','method' => 'post'))
          -> setTitle('Select a gateway to purchase')
	  -> setDescription('STORE_FORM_PAYMENT_SELECT_GATEWAY');

	$gateways = Socialstore_Model_DbTable_Gateways::getSupportedGateways();
	
	if(!count($gateways)){
		$this->addError('Sorry, there are no available gateways at this time.');
		return ;
	}
	
	$groups = array();
	$this->addElement('hidden','gateway');
	
	foreach($gateways as $name=>$title){
		// Buttons
	    if ($title != 'GoogleCheckout') {
			$this->addElement('Button', $name, array(
		      'label' => sprintf("Pay with %s", $title),
		      'type' => 'button',
		      'onclick' => "this.form.gateway.value='$name'; this.form.submit()",
		      'ignore' => true,
		      'decorators' => array('ViewHelper')
		    ));
			$groups[] = $name;
	    }
	}

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'socialstore_mystore_general', true),
      'onclick' => '',
      'decorators' => array(
        'ViewHelper'
      )
    ));
	$groups[] =  'cancel';

    $this->addDisplayGroup($groups, 'buttons');
		
	}
}
