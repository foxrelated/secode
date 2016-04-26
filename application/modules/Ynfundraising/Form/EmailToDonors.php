<?php
class Ynfundraising_Form_EmailToDonors extends Engine_Form {
	public function init() {
		$this->setTitle ( 'Email To All Donors' )
		->setDescription ( '' )
		->setAttrib ( 'class', 'global_form_popup')
		->setAttrib ('id','send_message_toOwner')
		->setAction ( $action )
		->setMethod ( 'POST' );

		$this->addElement('Text', 'subject', array(
				'label' => 'Subject',
				'required' => true,
				'allowEmpty' => false,
				'filters' => array(
						'StripTags',
				)
		));

		$this->addElement('Textarea', 'message', array(
				'label' => 'Personal Message',
				'required' => true,
				'allowEmpty' => false,
				'filters' => array(
						'StripTags',
				)
		));

		// Buttons
		$this->addElement ( 'Button', 'submit', array (
				'label' => 'Send Mail',
				'type' => 'submit',
				'ignore' => true,
				'decorators' => array (
						'ViewHelper'
				)
		) );

		$this->addElement ( 'Cancel', 'cancel', array (
				'label' => 'cancel',
				'link' => true,
				'prependText' => ' or ',
				'href' => '',
				'onclick' => 'parent.Smoothbox.close()',
				'decorators' => array (
						'ViewHelper'
				)
		) );
		$this->addDisplayGroup ( array (
				'submit',
				'cancel'
		), 'buttons' );
		$button_group = $this->getDisplayGroup ( 'buttons' );
	}
}