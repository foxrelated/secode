<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Fundraising
 * @copyright  Copyright 2012 YouNet Developments
 * @license    http://www.modules2buy.com/
 */
class Ynfundraising_Form_Summary extends Engine_Form {
	public $_error = array ();
	public function init() {
		$this->setTitle ( 'Summary' )->setAttrib ( 'name', 'ynfundraising_summary' )->setDescription ( '' );

		$translate = Zend_Registry::get ( 'Zend_Translate' );
		$view = Zend_Registry::get ( 'Zend_View' );

		$viewer = Engine_Api::_()->user()->getViewer();
		$campaign_id = Zend_Controller_Front::getInstance ()->getRequest ()->getParam ( 'campaignId' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );

		$donationId = $this->_getParam ( 'donation_id', 0 );
		if ($donationId) {
			$this->view->donation = $donation = Engine_Api::_ ()->getItem ( 'ynfundraising_donation', $donationId );
		}

		// -- FORM ELEMENT --
		// donation campaign title
		$this->addElement('Text', 'campaign', array(
			'label' => 'Donation Campaign',
			'value' => ''
		));

		if ($donation->user_id > 0 || ($donation->user_id <= 0 && $donation->guest_name != '')) {
			$this->addElement('Text', 'fullname', array (
				'label' => 'Full Name',
				'value' => ''
			));
		}

		if ($viewer || (!$viewer && $donation->guest_email != '')) {
			$this->addElement('Text', 'email', array (
				'label' => 'Email',
				'value' => ''
			));
		}

		// donation amount
		$this->addElement ( 'Text', 'amount', array (
				'label' => 'Your donation' . ' (' . $view->currencyfund( '', $campaign->currency ) . ')',
				'validators' => array(
						array('Int', true),
				),
		) );



		$this->message->getDecorator ( 'Description' )->setOption ( 'placement', 'append' );

		// Terms and Conditions
		$this->addElement ( 'Textarea', 'terms_conditions', array (
				'label' => 'Terms and Conditions',
				'readonly' => true,
				'style' => 'max-width: 500px; width: 500px; max-height: 200px; height: 200px'
		) );

		// Check box
		$this->addElement ( 'Checkbox', 'is_agreed', array (
				'label' => 'I have read and agreed with all terms and conditions'
		) );

		$this->addElement ( 'Hidden', 'no_shipping', array (
				'value' => 1,
				'order' => 100
		) );

		$this->addElement ( 'Hidden', 'cmd', array (
				'value' => '_xclick',
				'order' => 101
		) );

		$this->addElement ( 'Hidden', 'item_name', array (
				'value' => 'Donation for campaign ' . $campaign->title,
				'order' => 102
		) );

		// currency
		$this->addElement ( 'Hidden', 'currency_code', array (
				'value' => $campaign->currency,
				'order' => 103
		) );
		$this->addElement ( 'Hidden', 'notify_url', array (
				'value' => '',
				'order' => 104
		) );
		$this->addElement ( 'Hidden', 'return', array (
				'value' => '',
				'order' => 105
		) );

		$this->addElement ( 'Hidden', 'rm', array (
				'value' => '1',
				'order' => 107
		) );

		// account username
		$this->addElement ( 'Hidden', 'business', array (
				'value' => $campaign->paypal_account,
				'order' => 109
		) );

		// Submit Button
		$this->addElement ( 'Button', 'submit', array (
				'label' => 'Donate',
				'type' => 'submit',
				'style' => ''
		) );
	}
}
