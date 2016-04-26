<?php

class Socialstore_Form_Admin_Gateway_2Checkout extends Socialstore_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();
    
    $this->setTitle('Payment Gateway: 2Checkout');

    $description = $this->getTranslator()->translate('STORE_FORM_ADMIN_GATEWAY_2CHECKOUT_DESCRIPTION');
    $description = vsprintf($description, array(
      'https://www.2checkout.com/va/acct/list_usernames',
      'https://www.2checkout.com/va/notifications/',
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'module' => $this->_module,
          'controller' => 'payment',
          'action' => 'ipn',
          'gateway-id'=>'2checkout'
        ), 'default', true),
      'https://www.2checkout.com/va/acct/detail_company_info',
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'module'=>'socialstore',
          'controller' => 'payment-2checkout',
          'action' => 'review'
        ), 'default', true),
      'https://www.2checkout.com/2co/signup',
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
    $this->addElement('Text', '2checkoutno', array(
      'label' => '2Checkout Account Number',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
  }
}