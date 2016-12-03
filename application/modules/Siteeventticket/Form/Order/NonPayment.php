<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: NonPayment.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Order_NonPayment extends Engine_Form {

    public function init() {
        $this
                ->setAttrib('id', 'order_non_payment')
                ->setAttrib('name', 'order_non_payment')
                ->setTitle("Order Non-Payment");

        $this->addElement('Radio', 'non_payment_seller_reason', array(
            'label' => 'Please select the reason for non-payment',
            'multiOptions' => array(
                '1' => 'Chargeback',
                '2' => 'Payment not received',
                '3' => 'Cancelled payment',
            ),
            'allowEmpty' => false,
            'required' => true,
        ));

        $this->addElement('Textarea', 'non_payment_seller_message', array(
            'label' => 'Message',
            'description' => Zend_Registry::get('Zend_Translate')->translate('Please add any information that will help site administrator to investigate this issue further.'),
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Report Non-Payment',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'onclick' => 'javascript:parent.Smoothbox.close()',
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));
    }

}
