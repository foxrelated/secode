<?php

class Socialstore_Form_Payment_Billing extends Engine_Form{
	public function init(){
		//Set Form Informations
	    $this -> setAttribs(array('class' => 'global_form','method' => 'post'))
	          -> setTitle('Shipping Information')
		  -> setDescription('SHIPPING_INFORMATION_DESCRIPTION');
		  
		$this->addElement('text','fullname',array(
			'label'=>'Full Name',
			'maxlength'=>128,
			'required'=>true,
			'filters'=>array('StringTrim'),
		));
		
		$this->addElement('Text', 'email', array(
		      'label' => 'Email Address',
		      'required' => true,
		      'allowEmpty' => false,
		      'validators' => array(
		        array('NotEmpty', true),
		        array('EmailAddress', true),
		      ),
		));	
		$this->addElement('text','street',array(
			'label'=>'Address Line 1',
			'maxlength'=>128,
			'required'=>true,
			'filters'=>array('StringTrim'),
		));
		
		$this->addElement('text','street2',array(
			'label'=>'Address Line 2',
			'maxlength'=>128,
			'filters'=>array('StringTrim'),
		));
		
		$this->addElement('text','city',array(
			'label'=>'City',
			'maxlength'=>128,
			'required'=>true,
			'filters'=>array('StringTrim'),
		));
		
		$this->addElement('text','region',array(
			'label'=>'State/Province',
			'maxlength'=>128,
			'required'=>false,
			'filters'=>array('StringTrim'),
		));
		
		$this->addElement('text','postcode',array(
			'label'=>'Zip/PostCode',
			'maxlength'=>64,
			'required'=>true,
			'filters'=>array('StringTrim'),
		));
		
		$sql = "Select code,country FROM engine4_socialstore_countries";
		$db = Engine_Db_Table::getDefaultAdapter();
		$countries = $db -> fetchPairs($sql);
		$this->addElement('Select','country',array(
			'label'=>'Country',
			'required'=>true,
			'multiOptions' => $countries,
		));
		
		$this->addElement('text','phone',array(
			'label'=>'Phone',
			'maxlength'=>64,
			'required'=>true,
			'filters'=>array('StringTrim'),
		));
		
		
		$this->addElement('radio','use_for_billing',array(
			'label'=>'Use for billing',
			'description' =>'Is this address also your billing address (the address that appears on your credit card or bank statement)?',
			'multiOptions'=>array(
				0=>'No',
				1=>'Yes',
			),
			'value'=>1
		));
		//$this->use_for_billing->getDecorator("Description")->setOption("placement", "append");
		
		$this->addElement('Button', 'execute', array(
	      'label' => 'Continue',
	      'type' => 'submit',
	      'ignore' => true,
	      'decorators' => array(
	        'ViewHelper',
	      ),
	    ));
		$order_id = '';
	    if (Zend_Registry::isRegistered('order_id')) {
	    	$order_id = Zend_Registry::get('order_id');
	    }
	    $this->addElement('Button', 'addshippingaddress', array(
	      'label' => 'Add Another Address',
	      'type' => 'button',
	      'onclick' => "javascript:en4.socialstore.shipping.addAnotherBox('".$order_id."')",
	      'ignore' => true,
	      'decorators' => array(
	        'ViewHelper',
	      ),
	    ));
	    $this->addElement('Button', 'addfrombook', array(
	      'label' => 'Add From Address Book',
	      'type' => 'button',
	      'onclick' => "javascript:en4.socialstore.shipping.addBookBox('".$order_id."')",
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
	      'href' => '',
	      'onclick' => 'javascript:parent.Smoothbox.close();',
	      'decorators' => array(
	        'ViewHelper',
	      ),
	      
	    ));
     // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      	'execute',
    	'addshippingaddress',
    	'addfrombook',
    	'cancel',
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
	}
}
