<?php
class Socialstore_Form_Admin_Location_Create extends Engine_Form {
	protected $_field;

	public function init() {
		$this->setTitle('Add Location');
		$this -> setMethod('post');
		$this -> addElement('text', 'name', array(
		'label'=>'Location Name*',
		'required' => true, 'class' => 'text', 'maxlength' => 64, 'filters' => array('StringTrim'), 'validators' => array('NotEmpty', )));

		// Buttons
		$this -> addElement('Button', 'submit', array('label' => 'Save', 'type' => 'submit', 'ignore' => true, 'decorators' => array('ViewHelper')));

		$this -> addElement('Cancel', 'cancel', array('label' => 'cancel', 'link' => true, 'prependText' => ' or ', 'href' => '', 'onClick' => 'javascript:parent.Smoothbox.close();', 'decorators' => array('ViewHelper')));
		$this -> addDisplayGroup(array('submit', 'cancel'), 'buttons');
		$button_group = $this -> getDisplayGroup('buttons');
	}

}
