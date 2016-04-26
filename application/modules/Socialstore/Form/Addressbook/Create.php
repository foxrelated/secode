<?php

class Socialstore_Form_Addressbook_Create extends Engine_Form{
	public function init(){
		//Set Form Informations
	    $this -> setAttribs(array('class' => 'global_form','method' => 'post', 'id' =>'ynstore_addbookform'))
	          -> setTitle('Add New Address');
		 // -> setDescription('STORE_BILLING_INFORMATION_DESCRIPTION');
		  
	    $this->addElement('text','fullname',array(
			'label'=>'Full Name',
			'maxlength'=>128,
			'required'=>true,
			'filters'=>array('StringTrim'),
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
		
		/*$statesql = "Select code,state FROM engine4_socialstore_states";
		$db = Engine_Db_Table::getDefaultAdapter();
		$states = $db -> fetchPairs($statesql);
		$this->addElement('Select','state',array(
			'label'=>'State/Province*',
			'required'=>true,
			'multiOptions' => $states,
		));*/
		
		$this->addElement('Button', 'execute', array(
	      'label' => 'Save',
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
	      'href' => '',
	      'onclick' => 'javascript:parent.cancelAddress();parent.Smoothbox.close();',
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
