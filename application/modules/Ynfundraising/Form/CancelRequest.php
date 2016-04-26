<?php
class Ynfundraising_Form_CancelRequest extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Cancel Request')->setAttrib('class','global_form_popup');
	$request = Zend_Controller_Front::getInstance()->getRequest();
	$request_id = $request->getParam('request_id');

	$this->setDescription('Are you sure you want to cancel the request to create campaign?');

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      	->setMethod('POST');

	$this->addElement('Hidden', 'request_id',array(
      'value' => $request_id,
      'order' => 1
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Cancel Request',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => 'or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close()',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}