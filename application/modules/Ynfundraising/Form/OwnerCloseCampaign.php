<?php
class Ynfundraising_Form_OwnerCloseCampaign extends Engine_Form {
	public function init() {
		$this->setTitle ( 'Close Campaign' )
		->setDescription ( '' )
		->setAttrib ( 'class', 'global_form_popup')
		->setAttrib ('id','send_message_toOwner')
		->setAction ( $action )
		->setMethod ( 'POST' );
		$this->addElement('Textarea', 'reason', array(
				'label' => '*Reason',
				'required' => true,
				'description' => 'This reason will be sent to the owner to notify why the campaign is closed',
				'allowEmpty' => false,
				'filters' => array(
						'StripTags',
				)
		));
		$this->reason->getDecorator ( "Description" )->setOption ( "placement", "append" );

		// Buttons
		$this->addElement ( 'Button', 'submit', array (
				'label' => 'Close Campaign',
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