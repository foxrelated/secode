<?php

class Socialstore_Form_Payment_OrderReview extends Engine_Form{
	public function init()
    {
       //Set Form Informations
    $this -> setAttribs(array('class' => 'global_form','method' => 'post'));
         // -> setTitle('Complete the payment')
	  //-> setDescription('STORE_FORM_PAYMENT_REVIEW_DESCRIPTION');

       //VAT Id
	
	$this->addElement('Hidden','token');
	$this->addElement('Hidden','PayerId');
	    
        // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Confirm',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

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

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
		
	}
}
