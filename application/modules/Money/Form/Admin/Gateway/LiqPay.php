<?php
/**
 * SocialEnginePro
 *
 * @category   Application_Extensions
 * @package    money
 * @author     Azim
 */

/**
 * @category   Application_Extensions
 * @package    money
 */
class Money_Form_Admin_Gateway_LiqPay extends Money_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();
    
    $this->setTitle('Payment Gateway: LigPay');

    

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);


    // Elements
    $this->addElement('Text', 'idMobile', array(
      'label' => 'ID to a mobile phone',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
    
    $this->addElement('Text', 'idCard', array(
      'label' => 'ID to payments to a card/account',
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
    
    $this->addElement('Select', 'payWay', array(
        'label' => 'Pay Way',
        'multioptions' => array(
            'liqpay' => 'liqpay',
            'card' => 'card'
        )
    ));
  }
}