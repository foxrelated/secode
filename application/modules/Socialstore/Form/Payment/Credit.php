<?php

class Socialstore_Form_Payment_Credit extends Engine_Form{
	public function init(){
		//Set Form Informations
	    $this -> setAttribs(array('class' => 'global_form','method' => 'post'))
	          -> setTitle('Credit Card Information')
		  -> setDescription('STORE_CREDITCARD_INFORMATION_DESCRIPTION');
		  
		$this->addElement('Select','credit_type',array(
			'label'=>'Credit Card Type',
			//'required'=>true,
			'multiOptions' => array(
        		'AE'=>'American Express',
				'VI'=>'VISA',
				'MA'=>'Master Card',
				'DI'=>'Discover'
      		),
		));
		
		$this->addElement('text','credit_number',array(
			'label'=>'Card Number',
			'maxlength'=>128,
			'required'=>true,
			'validators' => array(
        		array('NotEmpty', true),
        		array('Int', true),
      		),
		));
		
		$this->addElement('text','credit_expire',array(
			'label'=>'Expiration Date(mm-yy)',
			'maxlength'=>128,
			'required'=>true,
			'allowEmpty' => false,
      		'validators' => array(
        		array('NotEmpty', true),
      		),
		));
		
		$this->addElement('text','credit_cvv',array(
			'label'=>'CVC',
			'maxlength'=>128,
			'required'=>true,
			'allowEmpty' => false,
      		'validators' => array(
        		array('NotEmpty', true),
        		array('Int', true),
      		),
		));
		
		$this->addElement('Button', 'execute', array(
	      'label' => 'Continue',
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
	      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'socialstore_general', true),
	      'onclick' => '',
	      'decorators' => array(
	        'ViewHelper',
	      ),
	      
	    ));
     // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'execute',
    	'cancel',
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
	}
}
