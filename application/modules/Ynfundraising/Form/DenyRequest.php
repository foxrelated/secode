<?php
class Ynfundraising_Form_DenyRequest extends Engine_Form {
	public function init() {
		$this->setTitle ( 'Deny Request' )->setAttrib ( 'class', 'global_form_popup' );
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		$request_id = $request->getParam ( 'request_id' );

		$this->setDescription ( 'Are you sure you want to deny this request?' );

		$this->setAction ( Zend_Controller_Front::getInstance ()->getRouter ()->assemble ( array () ) )->setMethod ( 'POST' );

		$this->addElement ( 'Hidden', 'request_id', array (
				'value' => $request_id,
				'order' => 1
		) );

		// Content
		$this->addElement('Textarea', 'reason', array (
			'label' => 'Reason',
			'required' => false
		));
		// Buttons
		$this->addElement ( 'Button', 'submit', array (
				'label' => 'Deny Request',
				'type' => 'submit',
				'ignore' => true,
				'decorators' => array (
						'ViewHelper'
				)
		) );

		$this->addElement ( 'Cancel', 'cancel', array (
				'label' => 'cancel',
				'link' => true,
				'prependText' => 'or ',
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