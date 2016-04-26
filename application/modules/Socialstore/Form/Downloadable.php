<?php
class Socialstore_Form_Downloadable extends Engine_Form{
    public function init()
    {
       //Set Form Informations
    $this -> setAttribs(array('class' => 'global_form_popup','method' => 'post'))
           -> setTitle('Purchase Product');
	  //-> setDescription('Please choose ');
    
        // Buttons
    $this->addElement('Hidden', 'option');
    $this->addElement('Text', 'quantity', array(
      'label' => 'Quantity',
      'allowEmpty' => false,
      'required'=>true,
      'value'=>1,
      'title' => '',             
      'attribs' => array(
       'readonly' => 'readonly',
       ),
      //'description' => '0 means not required',  
      'validators' => array(
        array('Int', true),
       	array('GreaterThan',true,array(0))
    )));

    $this->addElement('Button', 'checkout', array(
      'label' => 'Check Out',
      'type' => 'submit',
      'value' => 'checkout',
      //'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Continue Shopping',
      'type' => 'submit',
      	'value' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
	
    
    
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addDisplayGroup(array('checkout', 'submit', 'cancel'), 'buttons');
		
	}

}
