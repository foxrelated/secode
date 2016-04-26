<?php

class Socialstore_Form_Payment_Order extends Engine_Form{
	public function init()
    {
       //Set Form Informations
    $this -> setAttribs(array('class' => 'global_form','method' => 'post'))
          -> setTitle('Complete Order')
	  -> setDescription('Test only');

	$this->addElement('text','total_amount',array(
		'label'=>'Total Amount',
		'required'=>true,
	));
	
	$this->addElement('select','paytype_id',array(
		'label'=>'pay type',
		'multiOptions'=>array(
			'publish-store'=>'Publish Store'
		)
	));
	
	$this->addElement('select','currency',array(
		'label'=>'Currency',
		'multiOptions'=>Socialstore_Model_DbTable_Currencies::getMultiOptions(),
		'required'=>true,
	));
	
	
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
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'socialstore_mystore_general', true),
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
