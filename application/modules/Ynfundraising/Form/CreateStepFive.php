<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Fundraising
 * @copyright  Copyright 2012 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: CreateStep1.php
 * @author     Minh Nguyen
 */
class Ynfundraising_Form_CreateStepFive extends Engine_Form {
	public function init() {
		$this->setAttrib('class','global_form')->setAttrib ( 'name', 'ynfundraising_create_step_five');
		$this->setTitle('Email Message and Conditions');
		$user = Engine_Api::_ ()->user ()->getViewer ();
		$user_level = Engine_Api::_ ()->user ()->getViewer ()->level_id;
		// Element: full name
		$this->addElement ( 'Dummy', 'email_sub', array (
					'label' => 'Subject:',
					'required' => false,
					'style'	=> 'width: 300px',
					'content' => 'Thank you for contributing a campaign',
					'readonly' => true,
					'filters' => array (
							new Engine_Filter_Censor (),
							'StripTags',
						new Engine_Filter_StringLength ( array (
								'max' => '256'
						) )
				)
		) );

		// Message
		$this->addElement ( 'textarea', 'email_message', array (
				'label' => 'Message',
				'style' => 'width: 430px; height: 200px',
				'value' => ''
		) );

		// Message
		$this->addElement ( 'textarea', 'terms_conditions', array (
				'label' => 'Terms and Conditions',
				'style' => 'width: 430px; height: 200px',
				'value' => '',
				//'required' => true
		) );

		$this->addElement ( 'Button', 'submit', array (
				'label' => 'Save Changes',
				'type' => 'submit',
				'ignore' => true,
				'onclick' => 'removeSubmit()',
				'decorators' => array(
		        'ViewHelper',
		        ),
		));
		$this->addElement('Cancel', 'cancel', array(
	      'label' => 'cancel',
	      'link' => true,
	      'prependText' => Zend_Registry::get('Zend_Translate')->_('or '),
	      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'create'), 'ynfundraising_general', true),
	      'onclick' => '',
	      'decorators' => array(
	        'ViewHelper'
	      )
	    ));
	     // DisplayGroup: buttons
        $this->addDisplayGroup(array(
          'submit',
          'cancel',
        ), 'buttons', array(
          'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
          ),
        ));
	}
}
