<?php
class Socialstore_Form_Payment_Quantity extends Engine_Form{
	
	/**
	 * 
	 * 
	 * @var unknown_type
	 */
	
    public function init()
    {
       //Set Form Informations
    $this -> setAttribs(array('class' => 'global_form_popup','method' => 'post'))
          -> setTitle('Item Quantity')
		  ;
	
	 $this->addElement('Text', 'quantity', array(
      'label' => 'Quantity',
      'allowEmpty' => false,
      'required'=>true,
      'value'=>1,
      'title' => '',             
      //'description' => '0 means not required',  
      'validators' => array(
        array('Int', true),
       	array('GreaterThan',true,array(0))
       
    )));
    
        // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type' => 'submit',
      //'ignore' => true,
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
	

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
		
	}

}
