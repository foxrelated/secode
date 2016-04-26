<?php

class Winkgreeting_Form_Admin_Settings extends Engine_Form
{
  public function init()
  {

    $this->setMethod('post');
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Checkbox', 'wink', array(
      'description' => 'Enable Wink Option',
    ));

    $this->addElement('Checkbox', 'greeting', array(
      'description' => 'Enable Greeting Option',
    ));
	
    $this->addElement('Checkbox', 'confirm', array(
      'description' => 'Show confirmation message before Wink/Greeting sending',
    ));	

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
    ));
  }  
}