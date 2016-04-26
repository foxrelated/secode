<?php

class Replyrate_Form_Admin_Settings extends Engine_Form
{
  public function init()
  {

    $this->setMethod('post');
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Text', 'bgcolor', array(
      'label' => 'Color of 1st part in line',
      'description' => '',
	  'onclick' => 'startColorPicker(this)',
	  'value' => '#5f93b4',
    ));

    $this->addElement('Text', 'tcolor', array(
      'label' => 'Color of 2nd part in line',
      'description' => '',
	  'onclick' => 'startColorPicker(this)',
	  'value' => '#d0e2ec',
    ));

	$this->addElement('Text', 'current', array(
	  'label' => 'Reply Rate (example)',
	  'disabled' => 'disabled',
	));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
    ));
  }  
}