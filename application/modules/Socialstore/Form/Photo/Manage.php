<?php
class Socialstore_Form_Photo_Manage extends Engine_Form
{
	 public function init()
  {
    // Init form
    $this
      ->setTitle('Manage Photos')
      ->setAttribs(array(
      'style' => 'width: 700px'))
      //->setDescription('Manage Your Store Photos.')
      ;
  	$this->addElement('Radio', 'cover', array(
      'label' => 'Album Cover',
    ));
   	$this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
   	  'decorators' => array(
        'ViewHelper',
      ),
    ));
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'Cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'socialstore_mystore_general', true),
      'onclick' => '',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    
	 $this->addDisplayGroup(array(
      'submit',
    	'cancel',
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }
}