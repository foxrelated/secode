<?php

class Store_Form_Request_Confirm extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Business Request Approval')
      ->setDescription('Do you really want to accept this "Business Request"?')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');
      ;

    //$this->addElement('Hash', 'token');

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Accept',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
        $this->getElement('submit')->setAttrib('class', 'accept');
	
    $this->addElement('Button', 'cancel', array(
      'label' => 'Cancel',
      'onclick' => 'parent.Smoothbox.close();',
     'decorators' => array('ViewHelper')
      ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}