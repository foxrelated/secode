<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfundraising
 * @author     YouNet Company
 */

class Ynfundraising_Form_Admin_Paymentsettings extends Engine_Form {
	public function init() {
		$this->setTitle('Payment Gateways')
		->setDescription('Paypal API Information')
		->setAttrib('class','global_form_popup');
		// Account field
		$this->addElement('Text', 'account', array(
				'label' => '*Account',
		        'required' => true,
                'style' => 'min-width:300px;',
		       )
		);

		// Username field
		$this->addElement('Text', 'username', array(
				'label' => '*Username',
		        'required' => true,
		        'style' => 'min-width:300px;',
				)
		);
		// Password field
		$this->addElement('Text', 'password', array(
				'label' => '*Password',
		        'required' => true,
		        'style' => 'min-width:300px;',
		));
		// Signature field
		$this->addElement('Text', 'signature', array(
				'label' => '*Signature',
		        'required' => true,
		        'style' => 'min-width:300px;',
				)
		);
		// App id field
		$this->addElement('Text', 'appid', array(
		        'label' => '*Application ID',
		        'required' => true,
		        'style' => 'min-width:300px;',
		));

		// Add submit button
		$this->addElement('Button', 'submit', array(
				'label' => 'Save changes',
				'type' => 'submit',
				'ignore' => true,
		        'decorators' => array('ViewHelper')
		));
		//Cancel link
		$this->addElement('Cancel', 'cancel', array(
		        'label' => 'cancel',
		        'link' => true,
		        'prependText' => ' or ',
		        'href' => '',
		        'onClick'=> 'javascript:parent.Smoothbox.close();',
		        'decorators' => array('ViewHelper'),
		));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
	}
}