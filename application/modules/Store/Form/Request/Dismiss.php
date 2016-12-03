<?php

class Store_Form_Request_Dismiss extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Business Request Rejection')
      ->setDescription('Do you really want to reject this "Business Request"?')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');
      ;

    //$this->addElement('Hash', 'token');

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Reject',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
        $this->getElement('submit')->setAttrib('class', 'dismiss');
	
    $this->addElement('Button', 'cancel', array(
      'label' => 'Cancel',
      'onclick' => 'parent.Smoothbox.close();',
     'decorators' => array('ViewHelper')
      ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}