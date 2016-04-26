<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Ynaffiliate_Form_Signup extends Engine_Form {

   protected $_item;

   public function getItem() {
      return $this->_item;
   }

   public function setItem(Core_Model_Item_Abstract $item) {
      $this->_item = $item;
      return $this;
   }

   public function init() {
      $user = Engine_Api::_()->user()->getViewer();

      // Init form
      $this
              ->setTitle('Sign up to become Affiliate')
              ->setDescription('Enter your affiliate information')
              ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
      ;

      //Contact Name
      $this->addElement('Text', 'contact_name', array(
          'label' => 'Contact Name',
          'description' => '',
          'required' => true,
          'allowEmpty' => false,
          'attribs' => array('readonly' => 'readonly'),
          'filters' => array(
              'StringTrim'
          ),
          'value' => $this->getItem()->displayname
      ));


      //Contact Email
      $this->addElement('Text', 'contact_email', array(
          'label' => 'Contact Email',
          'description' => '',
          'required' => false,
          'allowEmpty' => true,
          'validators' => array(
              array('NotEmpty', true),
              array('EmailAddress', true),
          ),
          'filters' => array(
              'StringTrim'
          ),
          'tabindex' => 1,
          'value' => $user->email
      ));
      $this->contact_email->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
      $this->contact_email->getValidator('NotEmpty')->setMessage('Please enter a valid email address.', 'isEmpty');

      
      //Contact Adress
      $this->addElement('Text', 'contact_address', array(
          'label' => 'Contact Address',
          'description' => '',
          'required' => false,
          'allowEmpty' => true,
          'filters' => array(
              'StringTrim'
          ),
      ));
      //Contact Phone
      $this->addElement('Text', 'contact_phone', array(
          'label' => 'Contact Phone',
          'description' => '',
          'required' => false,
          'allowEmpty' => false,
          'filters' => array(
              'StringTrim'
          ),
      ));
      // Element: terms
      $description = Zend_Registry::get('Zend_Translate')->_('I have read and agree to the <a target="_blank" href="%s/affiliate/index/terms">Terms of Service</a>.');
      $description = sprintf($description, Zend_Controller_Front::getInstance()->getBaseUrl());

      $this->addElement('Checkbox', 'terms', array(
          'label' => 'Terms of Service',
          'description' => $description,
          'required' => true,
          'validators' => array(
              'notEmpty',
              array('GreaterThan', false, array(0)),
          )
      ));
      $this->terms->getValidator('GreaterThan')->setMessage('You must agree to the terms of service to continue.', 'notGreaterThan');

      $this->terms->clearDecorators()
              ->addDecorator('ViewHelper')
              ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'label', 'class' => 'null', 'escape' => false, 'for' => 'terms'))
              ->addDecorator('DivDivDivWrapper');

      // Init submit
      $this->addElement('Button', 'submit', array(
          'label' => 'Submit',
          'type' => 'submit',
          'ignore' => true,
      ));
   }

}

?>