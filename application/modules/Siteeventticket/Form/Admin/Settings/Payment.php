<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Payment.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Admin_Settings_Payment extends Engine_Form {

    public function init() {

        $this->setTitle('Payment Settings')
                ->setDescription('Here, you can configure the payment based settings for your event tickets.')
                ->setName('siteeventticket_ticket_payment');

        $settings = Engine_Api::_()->getApi('settings', 'core');

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        //SETTINGS FOR "DIRECT PAYEMENT TO SELLERS" OR "PAYMENT TO WEBSITE / SITEADMIN"
        $this->addElement('Radio', 'siteeventticket_payment_to_siteadmin', array(
            'label' => 'Payment for Orders',
            'description' => 'Please choose the default payment flow for orders on your website.',
            'multiOptions' => array(
                '0' => 'Direct Payment to Sellers',
                '1' => 'Payment to Website / Site Admin'
            ),
            'onchange' => 'showPaymentForOrdersGateway(this.value)',
            'value' => $settings->getSetting('siteeventticket.payment.to.siteadmin', '0'),
        ));           

        //PAYMENT GATEWAY FOR "DIRECT PAYEMENT TO SELLERS"
        
        $siteeventticket_allowed_payment_gateway_options = array(
                'paypal' => 'PayPal'
            );
        
        if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $getEnabledGateways = Engine_Api::_()->sitegateway()->getAdditionalEnabledGateways(array('pluginLike' => 'Sitegateway_Plugin_Gateway_'));
            $otherGateways = array();
            foreach($getEnabledGateways as $getEnabledGateway) {
                $gatewayKey = strtolower($getEnabledGateway->title);
                $otherGateways[$gatewayKey] = $getEnabledGateway->title;
            }
            
            $siteeventticket_allowed_payment_gateway_options = array_merge($siteeventticket_allowed_payment_gateway_options, $otherGateways);  
        }
        
        $otherPaymentOptions = array(
                'cheque' => 'By Cheque',
                'cod' => 'Pay at the Event'
            );
        
        $siteeventticket_allowed_payment_gateway_options = array_merge($siteeventticket_allowed_payment_gateway_options, $otherPaymentOptions);        

        $this->addElement('MultiCheckbox', 'siteeventticket_allowed_payment_gateway', array(
            'label' => 'Payment Gateways',
            'description' => "Select the payment gateway to be available for 'Direct Payment to Sellers'.",
            'multiOptions' => $siteeventticket_allowed_payment_gateway_options,
            'value' => $settings->getSetting('siteeventticket.allowed.payment.gateway', array('paypal', 'cheque', 'cod')),
        ));   
        
        $siteeventticket_admin_gateway_description = sprintf(Zend_Registry::get('Zend_Translate')->_('Select the payment gateway to be available during checkout process. [To enable payment gateways PayPal and 2Checkout, click %1$shere%2$s.]'), "<a href='" . $view->url(array('module' => 'payment', 'controller' => 'gateway', 'action' => 'index'), 'admin_default', true) . "' target='_blank'>", "</a>");

        //PAYMENT GATEWAY FOR "PAYMENT TO WEBSITE / SITE ADMIN"
        $this->addElement('MultiCheckbox', 'siteeventticket_admin_gateway', array(
            'label' => 'Payment Gateways',
            'description' => $siteeventticket_admin_gateway_description,
//        'allowEmpty' => false,
//        'required' => true,
            'multiOptions' => array(
                'cheque' => 'By Cheque',
                'cod' => 'Pay at the Event [If enabled payment will be recieved by the Event Owner]'
            ),
            'value' => $settings->getSetting('siteeventticket.admin.gateway', array('cheque', 'cod')),
        ));
        $this->siteeventticket_admin_gateway->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
        
        if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            
            $gatewayOptions = array_merge(array('paypal' => 'Paypal'), $otherGateways);
            
            $this->addElement('Radio', 'siteeventticket_paymentmethod', array(
                'label' => "Payment for 'Commissions Bill'",
                'description' => "Select the payment gateway to be available to sellers for admin ‘Commissions Bill’ payment, if ‘Direct Payment to Sellers’ is selected.",
                'multiOptions' => $gatewayOptions,
                'value' => $settings->getSetting('siteeventticket.paymentmethod', 'paypal'),
            ));     
        }        
        
        $this->addElement('Radio', 'siteeventticket_thresholdnotification', array(
            'label' => 'Email Notification for Commission Bill Payment',
            'description' => 'Do you want to enable email notifications for event owners for your commission bill payment? Once total commission bill amount exceed to threshold amount, event owners will start getting email notifications for your due commission payment and it will continue until the payment has been not made.',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'onchange' => 'thresholdNotification(this.value)',
            'value' => $settings->getSetting('siteeventticket.thresholdnotification', 0),
        ));
        
        $this->addElement('Text', 'siteeventticket_thresholdnotificationamount', array(
            'label' => 'Enter Threshold Amount',
            'value' => $settings->getSetting('siteeventticket.thresholdnotificationamount', 100),
        )); 
        
        $this->addElement('MultiCheckbox', 'siteeventticket_thresholdnotify', array(
            'description' => 'Please select to whom this email notification will send. This notification will repeat on every order placed.',
            'multiOptions' => array(
                'owner' => 'Send Email Notification to Event Owner',
                'admin' => 'Send Email Notification to Site Admin',
            ),
            'value' => $settings->getSetting('siteeventticket.thresholdnotify', array('owner', 'admin')),
        ));         

        //PAYMENT GATEWAY FOR "PAYMENT TO WEBSITE / SITE ADMIN"
        $this->addElement('MultiCheckbox', 'siteeventticket_admin_gateway', array(
            'label' => 'Payment Gateways',
            'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('Select the payment gateway to be available during checkout process. [To enable payment gateways PayPal and 2Checkout, click %1$shere%2$s.]'), "<a href='" . $view->url(array('module' => 'payment', 'controller' => 'gateway', 'action' => 'index'), 'admin_default', true) . "' target='_blank'>", "</a>"),
//        'allowEmpty' => false,
//        'required' => true,
            'multiOptions' => array(
                'cheque' => 'By Cheque',
                'cod' => 'Pay at the Event [If enabled payment will be recieved by the Event Owner]'
            ),
            'value' => $settings->getSetting('siteeventticket.admin.gateway', array('cheque')),
        ));
        $this->siteeventticket_admin_gateway->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));


        $this->addElement('Textarea', 'siteeventticket_send_cheque_to', array(
            'label' => 'Send Cheque To',
            'description' => 'Enter your account details which buyers will fill in the cheques for making payments for their orders. This information will be shown when buyers choose "By Cheque" method in the "Payment Information" section during their checkout process. [You can enable / disable this cheque option from Member Level Settings.]',
            'value' => $settings->getSetting('siteeventticket.send.cheque.to', ''),
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
