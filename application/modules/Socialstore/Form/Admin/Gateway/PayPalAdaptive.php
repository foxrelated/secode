<?php

class Socialstore_Form_Admin_Gateway_PayPalAdaptive extends Socialstore_Form_Admin_Gateway_Abstract
{
	

  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: PayPal');
    
    $description = $this->getTranslator()->translate('STORE_FORM_ADMIN_GATEWAY_PAYPALADAPTIVE_DESCRIPTION');
    $description = vsprintf($description, array(
      'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-signature',
      'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-ipn-notify',
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'module' => $this->_module,
          'controller' => 'payment',
          'action' => 'ipn',
          'gateway_id'=>'paypal'
        ), 'default', true),
    ));
    $this->setDescription($description);

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);


    // Elements
    $this->addElement('Text', 'user', array(
      'label' => 'API Username',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));

    $this->addElement('Text', 'password', array(
      'label' => 'API Password',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));

    $this->addElement('Text', 'signature', array(
      'label' => 'API Signature',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
    
    $this->addElement('Text', 'appid', array(
      'label' => 'API Application ID',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
    
	$this->addElement('Text','account_username',array(
		'label'=>'Paypal Email Address',
		'required'=>true,
		'maxlength'=>128,
		'description'=>'',
		'filters'=>array('StringTrim'),
		'validators'=>array(
			'EmailAddress',
		),
	));
  }
}