<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: BillPayment.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_BillPayment extends Engine_Form {

  public function init() {
    
    $this->setTitle('Make a Bill Payment');
    
    $this->setName('bill_payment');
    
    $this->addElement('Text', 'total_bill_amount', array(
//        'value' => @round($this->_totalBillAmount, 2),
        //'attribs' => array('disabled' => 'disabled'),
    ));
    
    $this->addElement('text', 'bill_amount_pay', array(
        'description' => 'This amount cannot be greater than your total bill amount.',
//        'value' => @round($this->_totalBillAmount, 2),
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
          array('Float', true),
          array('GreaterThan', false, array(0))
        ),
    ));
    $this->bill_amount_pay->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    $this->addElement('Textarea', 'message', array(
        'label' => 'Message',
        'description' => '',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
    )));
            
    $this->addElement('Button', 'submit', array(
        'label' => 'Make Payment',
        'order' => '998',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'order' => '999',
        'onclick' => "javascript:parent.Smoothbox.close();",
        'href' => "javascript:void(0);",
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    
    $this->addDisplayGroup(array(
        'submit',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->setOrder('999');
  }
}