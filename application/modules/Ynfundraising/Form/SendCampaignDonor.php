<?php
class Ynfundraising_Form_SendCampaignDonor extends Engine_Form {
	public function init() {
		$action = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'send'));

		$this->setTitle ( 'Send Message to Donors' )
		->setDescription ( '' )
		->setAttrib ( 'class', 'global_form_popup')
		->setAttrib ('id','send_message_toDonors')
		->setAction ( $action )
		->setMethod ( 'POST' );
		$this->addElement('Textarea', 'content', array(
				'label' => '*Content: ',
				'required' => true,
				'allowEmpty' => false,
				'filters' => array(
						'StripTags',
				)
		));

		// Buttons
		$this->addElement ( 'Button', 'submit_', array (
				'label' => 'Send Message',
				'type' => 'submit',
				'ignore' => true,
				'decorators' => array (
						'ViewHelper'
				)
		) );

		$this->addElement ( 'Cancel', 'cancel', array (
				'label' => 'skip this step',
				'link' => true,
				'prependText' => 'or ',
				'href' => '',
				'onclick' => 'form_submit()',
				'decorators' => array (
						'ViewHelper'
				)
		) );
		$this->addDisplayGroup ( array (
				'submit_',
				'cancel'
		), 'buttons' );
		$button_group = $this->getDisplayGroup ( 'buttons' );
	}
}