<?php
class Ynfundraising_Form_DenyReason extends Engine_Form {
	public function init() {
		$this->setTitle ( 'Reason' )
		->setAttrib ( 'class', 'global_form_popup' );

		$this->addElement('Textarea', 'reason', array(
			'label' => '',
			'readonly' => true
		));
		// Buttons
		$this->addElement ( 'Button', 'submit', array (
				'label' => 'Close',
				'ignore' => true,
				'onclick' => 'parent.Smoothbox.close();',
				'style' => 'margin-top:20px;'
		) );

	}
}