<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Fundraising
 * @copyright  Copyright 2012 YouNet Developments
 * @license    http://www.modules2buy.com/
 */
class Ynfundraising_Form_DonateCampaign extends Engine_Form {
	public $_error = array ();
	public function init() {
		$this->setTitle ( 'Donate Campaign' )->setAttrib ( 'name', 'ynfundraising_donate_campaign' )->setDescription ( 'Thank you for helping out' );

		$translate = Zend_Registry::get ( 'Zend_Translate' );
		$view = Zend_Registry::get ( 'Zend_View' );

		$viewer = Engine_Api::_()->user()->getViewer();
		$campaign_id = Zend_Controller_Front::getInstance ()->getRequest ()->getParam ( 'campaign_id' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		/*
		 * Check allow_anonymous on campaign to show is_anonymous field
		 */
		//$allow_guest = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising.guest', 0 );
		$allow_anonymous = $campaign->allow_anonymous;

		// pre-defined list
		$predefined_list = explode ( ',', $campaign->predefined );
		$predefined_list = array_filter($predefined_list, 'strlen');
		if (count ( $predefined_list ) > 0) {
			sort ( $predefined_list );

			foreach ( $predefined_list as $item ) {
				$name = $item;
				$label = $view->currencyfund( $item, $campaign->currency );
				$item = str_replace($view->currencyfund('',$campaign->currency), '', $label);
				
				$this->addElement ( 'Cancel', "list_" . $item, array (
						'label' => $item,
						'link' => true,
						'name' => $name,
						'href' => "javascript: void(0)",
						'onclick' => "selectAmount(this);",
						'for' => $name,
						'style' => 'padding-left:5pt, float:left',
						'class' => 'ynfundraising_selectamount',
						'decorators' => array (
								'ViewHelper'
						)
				) );
			}

			$this->addElement ( 'Cancel', "list_other", array (
					'label' => 'Other',
					'link' => true,
					'href' => "javascript: void(0)",
					'onclick' => "selectAmount(this);",
					'for' => 'other',
					'style' => 'padding-left:5pt, float:left',
					'class' => 'ynfundraising_selectamount',
					'decorators' => array (
							'ViewHelper'
					)
			) );
		}



		// donation amount
		$this->addElement ( 'Text', 'amount', array (
				'label' => Zend_Registry::get('Zend_Translate')->_('Your donation') . ' (' .$campaign->currency. ')',
				'validators' => array(
					array('NotEmpty', false),
					array('Float', true),
					array('GreaterThan', true, array(0))
				),
		) );

		//$this->amount->addErrorMessage("Your donation must be at least " . $view->currencyfund( $campaign->minimum_donate, $campaign->currency));

		if ($viewer->getIdentity() <= 0) {
			// fullname amount
			$this->addElement ( 'Text', 'guest_name', array (
					'label' => 'Full name'
			) );

			// email
			$this->addElement('Text', 'guest_email', array(
				'label' => 'Email',
				'allowEmpty' => true,
				'validators' => array(
					array('EmailAddress',  TRUE )
				),
			));

		}

		// Check box - make donation anonymous
		if ($allow_anonymous) {
			$this->addElement ( 'Checkbox', 'is_anonymous', array (
					'label' => 'Make Donation Anonymous',
					//'title' => 'This will hide your name and donor information from all public activity feeds however the campaign will still receive your donor information',

			 ));

		}

		// Message
		$this->addElement ( 'Textarea', 'message', array (
				'label' => 'Leave your message',
				'required' => false,
				'description' => 'Let them know why you donated, honor a love one, or send words of encouragement. Your comment will appear on their campaign activity',
				'filters' => array (
						new Engine_Filter_Censor (),
						'StripTags',
						new Engine_Filter_StringLength ( array (
								'max' => '256'
						) )
				)
		) );

		$this->message->getDecorator ( 'Description' )->setOption ( 'placement', 'append' );

		if ($campaign->terms_conditions) {
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
		}

		// Submit Button
		$this->addElement ( 'Button', 'submit', array (
				'label' => 'Continue',
				'type' => 'submit',
				'style' => ''
		) );
	}
}
