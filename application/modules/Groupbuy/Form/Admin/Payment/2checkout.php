<?php
class Groupbuy_Form_Admin_Payment_2checkout extends Engine_Form
{
	public function init()
	  {
	    $this
	      ->setTitle('Payment Gateway: 2Checkout')
	      ;

		  $this->addElement('Text', 'admin_account', array(
	      'label' => 'Your 2Checkout account number*',
	      'required' => true,
	      'allowEmpty' => false,
		));
	    $this->admin_account->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
	    
	    $this->addElement('select', 'currency', array(
        'label' => 'Your 2Checkout currency*',
        'description' => 'Select default currency ',
        'required'=>true,
        'multiOptions' => Groupbuy_Model_DbTable_Currencies::getMultiOptions(),
      ));

	    $this->addElement('Text', 'api_username', array(
      'label' => 'API Username',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));

	    $this->addElement('Text', 'api_password', array(
	      'label' => 'API Password',
	      'filters' => array(
	        new Zend_Filter_StringTrim(),
	      ),
	    ));
	
	    $this->addElement('Text', 'api_signature', array(
	      'label' => 'API Signature',
	      //'description' => 'You only need to fill in either Signature or ' .
	      //    'Certificate, depending on what type of API account you create.',
	      'filters' => array(
	        new Zend_Filter_StringTrim(),
	      ),
	    ));
        
        // Element: enabled
    $this->addElement('Radio', 'is_active', array(
      'label' => 'Enabled?',
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
    ));
	   $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
	    
	  }
}