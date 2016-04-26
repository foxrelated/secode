<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfundraising
 * @author     YouNet Company
 */

class Ynfundraising_Form_Admin_Global extends Engine_Form {
	public function init() {
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$this->setTitle('Global Settings')->setDescription('These settings affect all members in your community.');

		$this->addElement('Radio', 'ynfundraising_mode', array(
			'label' => '*Enable Test Mode?',
			'description' => 'Allow admin to test fundraisings by using development mode?',
			'required'=>true,
			'multiOptions' => array(
				1 => 'Yes',
				0 => 'No'
			),
			'value' => $settings->getSetting('ynfundraising.mode', 1),
		));

		$this->addElement ( 'Text', 'ynfundraising_pubid', array (
				'label' => 'AddThis Profile ID',
				'description' => '',
				'required' => false,
				'value' => Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising.pubid' )
		) );

		$this->addElement('Select', 'ynfundraising_currency', array(
				'label' => '*Default Currency',
				'required'=>true,
				'multiOptions' => Ynfundraising_Model_DbTable_Currencies::getMultiOptions(),
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfundraising.currency', 'USD'),
		));

		$this->addElement('Select', 'ynfundraising_country', array(
				'label' => '*Default Country',
				'required'=>true,
				'multiOptions' => Ynfundraising_Model_DbTable_Countries::getMultiOptions(),
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfundraising.country', 'VNM'),
		));

		// rate campaign
		$this->addElement('Radio', 'ynfundraising_rate', array(
				'label' => 'Rate Campaign',
				'description' => 'Allow user to rate their own campaign?',
				'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
				),
				'value' => $settings->getSetting('ynfundraising.rate', 0),
		));

		// number of campaign per page
		$this->addElement('Text', 'ynfundraising_page', array(
				'label' => 'Number of items per page',
				'description' => 'How many items will be shown per page? (Enter a number from 1 to 999)',
				'value' => $settings->getSetting('ynfundraising.page', null),
				'validators' => array(
						new Zend_Validate_Between(1,999)
				)
		));

		// rate campaign
		$this->addElement('Text', 'ynfundraising_timeout', array(
				'label' => 'Create Campaign Time Out',
				'description' => 'Time out (hours) for requester to create campaign after their request is accepted by idea/trophy owners',
				'validators' => array(
						//array('Int', true),
						array('GreaterThan', true, array(0)),
						//array('LessThan', true, array(1000)),
				),
				'value' => $settings->getSetting('ynfundraising.timeout', null),
		));

		// Add submit button
		$this->addElement('Button', 'submit', array(
				'label' => 'Save changes',
				'type' => 'submit',
				'ignore' => true
		));
	}
}