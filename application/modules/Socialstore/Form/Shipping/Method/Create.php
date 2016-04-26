<?php

class Socialstore_Form_Shipping_Method_Create extends Engine_Form{
	public function init(){
		//Set Form Informations
	    $this -> setAttribs(array('class' => 'global_form','method' => 'post', 'id' =>'ynstore_addbookform'))
	          -> setTitle('Add New Shipping Method');
		$this->addElement('Hidden','shippingmethod_id');  
	    $this->addElement('text','name',array(
			'label'=>'Name*',
			'required'=>true,
	    	'allowEmpty'=>false,
			'filters'=>array('StringTrim'),
		));
		
		$this->addElement('textarea','description',array(
			'label'=>'Description*',
			'required'=>true,
			'allowEmpty'=>false,
			'filters'=>array('StringTrim'),
		));
		
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
	      'onclick' => 'javascript:parent.Smoothbox.close()',
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
