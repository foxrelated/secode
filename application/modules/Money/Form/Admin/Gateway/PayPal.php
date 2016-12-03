<?php
class Money_Form_Admin_Gateway_PayPal extends Money_Form_Admin_Gateway_Abstract
{
  public function init()
  {
      parent::init();

    $this->setTitle('Money Gateway: PayPal');
    
    $description = $this->getTranslator()->translate('PAYMENT_FORM_ADMIN_GATEWAY_PAYPAL_DESCRIPTION');
    $description = vsprintf($description, array(
      'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-signature',
      'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-ipn-notify',
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'module' => 'money',
          'controller' => 'ipn',
          'action' => 'PayPal'
        ), 'default', true),
    ));
    $this->setDescription($description);

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
      
      $this->addElement('Text', 'username', array(
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
      
     
      
  }
}