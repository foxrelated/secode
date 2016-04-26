<?php
class Ynaffiliate_Form_MyAccount_Cancel extends Engine_Form
{
	public function init()
	{
		$this -> setTitle('Cancel Request') 
			-> setDescription('Are you sure you want to cancel this request?') 
			-> setAttrib('class', 'global_form_popup') 
			-> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array())) -> setMethod('POST');
		// Buttons
		$this -> addElement('Button', 'submit', array(
			'label' => 'Cancel Request',
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));

		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true,
			'prependText' => ' or ',
			'href' => '',
			'onclick' => 'parent.Smoothbox.close();',
			'decorators' => array('ViewHelper')
		));
		$this -> addDisplayGroup(array(
			'submit',
			'cancel'
		), 'buttons');
		$button_group = $this -> getDisplayGroup('buttons');
	}

}
