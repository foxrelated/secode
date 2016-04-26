<?php

class Profileinfochecker_Form_Admin_Settings extends Engine_Form
{
  public function init()
  {

    $this->setMethod('post');
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Text', 'percent', array(
      'label' => 'Percent',
      'description' => 'Hide the widget if the profile information filled out more than or equal to (in %)',
      'value' => 100,
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('Int', true),
        array('Between', true, array(1, 100, true)),
      ),
    ));
	$this->percent->getDecorator( 'Description' )->setOptions( array( 'placement' => 'append' ) );		
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

	$this->addElement('Text', 'current_p', array(
	  'label' => 'Example',
	  'description' => '',
	  'disabled' => 'disabled',
	));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
    ));
  }  
}