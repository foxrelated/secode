<?php
class Money_Form_Admin_Gateway_Abstract extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Payment Gateway')
      ;

    /*
    // Element: vendor_identity
    $this->addElement('Text', 'vendor_identity', array(
      'label' => 'Vendor Identity',
    ));

    // Element: vendor_secret
    $this->addElement('Text', 'vendor_secret', array(
      'label' => 'Vendor Secret',
    ));

    // Element: vendor_signature
    $this->addElement('Text', 'vendor_signature', array(
      'label' => 'Vendor Signature',
    ));

    // Element: vendor_certificate
    $this->addElement('Textarea', 'vendor_certificate', array(
      'label' => 'Vendor Certificate',
    ));
     * 
     */

    // Element: enabled
    $this->addElement('Radio', 'enabled', array(
      'label' => 'Enabled?',
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
      'order' => 10000,
    ));

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'decorators' => array('ViewHelper'),
      'order' => 10001,
      'ignore' => true,
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'prependText' => ' or ',
      'link' => true,
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'gateway', 'gateway_id' => null)),
      'decorators' => array('ViewHelper'),
      'order' => 10002,
      'ignore' => true,
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
      'order' => 10003,
    ));
  }
}