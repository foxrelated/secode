<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Fundraising
 * @copyright  Copyright 2012 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: CreateStep6.php
 * @author     Minh Nguyen
 */
class Ynfundraising_Form_CreateStepSix extends Engine_Form {
	public function init() {
		$this->setAttrib('class','global_form')->setAttrib ( 'name', 'ynfundraising_create_step_six');
		$this->setTitle('Invite Friends');
		$user = Engine_Api::_ ()->user ()->getViewer ();
		$user_level = Engine_Api::_ ()->user ()->getViewer ()->level_id;

		// Element: user
		$this->addElement('Text', 'to',array(
          'autocomplete' => 'off',
          'label' => 'Invite Friends',
          'style' => 'width: 300px;',
          'filters' => array(
            new Engine_Filter_Censor(),
          ),
        ));
       // Init to Values
	    $this->addElement('Hidden', 'toValues', array(
	      'label' => '',
	      'required' => false,
	      'allowEmpty' => true,
	      'order' => 1,
	      'validators' => array(
	        'NotEmpty'
	      ),
	      'filters' => array(
	        'HtmlEntities'
	      ),
	    ));
	    Engine_Form::addDefaultDecorators($this->toValues);

		// recipients
		$this->addElement ( 'textarea', 'recipients', array (
				'label' => 'Recipients',
				'description' => 'Separate emails with commas.',
				'value' => ''
		) );
		$this -> recipients -> getDecorator("Description") -> setOption("placement", "append");
		// Message
		$this->addElement ( 'textarea', 'custom_message', array (
				'label' => 'Custom Message',
				'value' => ''
		) );

		$this->addElement ( 'Button', 'submit', array (
				'label' => 'Send Invites',
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
	      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'campaign'), 'ynfundraising_general', true),
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
