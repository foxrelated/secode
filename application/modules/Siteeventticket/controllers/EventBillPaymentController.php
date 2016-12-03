<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EventBillPaymentController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_EventBillPaymentController extends Core_Controller_Action_Standard {

    protected $_session;

    public function init() {
        // Get session
        $this->_session = new Zend_Session_Namespace('Event_Bill_Payment_Siteeventticket');
    }

    public function processAction() {

        // Process
        $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
        if (!empty($this->_session->order_id)) {
            $previousOrder = $ordersTable->find($this->_session->order_id)->current();
            if ($previousOrder && $previousOrder->state == 'pending') {
                $previousOrder->state = 'incomplete';
                $previousOrder->save();
            }
        }

        $gateway_id = 2;
        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.paymentmethod', 'paypal');
        if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && $paymentMethod != 'paypal') {
            $gatewayPlugin = "Sitegateway_Plugin_Gateway_".ucfirst($paymentMethod);
            $gateway_id = Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => $gatewayPlugin));
        }        
        
        // Create order
        $ordersTable->insert(array(
            'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
            'gateway_id' => $gateway_id,
            'state' => 'pending',
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'source_type' => 'siteeventticket_eventbill',
            'source_id' => $this->_session->bill_id,
        ));
        $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

        $gateway = Engine_Api::_()->getItem('siteeventticket_eventpaypalbill', $gateway_id);

        // Get gateway plugin
        $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
        $plugin = $gateway->getPlugin();

        // Prepare host info
        $schema = 'http://';
        if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
            $schema = 'https://';
        }
        $host = $_SERVER['HTTP_HOST'];

        // Prepare transaction
        $params = array();
//    $params['language'] = $this->_user->language;
        $params['vendor_order_id'] = $order_id;

        $params['return_url'] = $schema . $host
                . $this->view->url(array('action' => 'return', 'controller' => 'event-bill-payment', 'module' => 'siteeventticket'), 'default')
                . '?order_id=' . $order_id
                . '&state=' . 'return';
        $params['cancel_url'] = $schema . $host
                . $this->view->url(array('action' => 'return', 'controller' => 'event-bill-payment', 'module' => 'siteeventticket'), 'default')
                . '?order_id=' . $order_id
                . '&state=' . 'cancel';
        $params['ipn_url'] = $schema . $host
                . $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'payment'), 'default')
                . '?order_id=' . $order_id;

        // Process transaction
        $transaction = $plugin->createEventBillTransaction($this->_session->event_id, $this->_session->bill_id, $params);

        $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
        $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
        $this->view->transactionData = $transactionData = $transaction->getData();

        unset($this->_session->user_order_id);
        $this->view->transactionMethod = $transactionMethod;
        // Handle redirection
        if ($transactionMethod == 'GET' && !Engine_Api::_()->seaocore()->isSiteMobileModeEnabled()) {
            $transactionUrl .= '?' . http_build_query($transactionData);
            return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
        }
    }

    public function returnAction() {

        // Get order
        if (!($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
                !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
                $order->source_type != 'siteeventticket_eventbill') {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        $gateway_id = 2;
        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.paymentmethod', 'paypal');
        if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && $paymentMethod != 'paypal') {
            $gatewayPlugin = "Sitegateway_Plugin_Gateway_".ucfirst($paymentMethod);
            $gateway_id = Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => $gatewayPlugin));
        }               
        
        $gateway = Engine_Api::_()->getItem('siteeventticket_eventpaypalbill', $gateway_id);

        if (!$gateway)
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);

        // Get gateway plugin
        $plugin = $gateway->getPlugin();

        unset($this->_session->errorMessage);

        try {
            $status = $plugin->onEventBillTransactionReturn($order, $this->_getAllParams());
        } catch (Payment_Model_Exception $e) {
            $status = 'failure';
            $this->_session->errorMessage = $e->getMessage();
        }

        return $this->_finishPayment($status);
    }

    protected function _finishPayment($state = 'active') {

        // Clear session
        $errorMessage = $this->_session->errorMessage;
        $event_id = $this->_session->event_id;
        $this->_session->unsetAll();
        $this->_session->errorMessage = $errorMessage;

        // Redirect
        if ($state == 'free') {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state, 'event_id' => $event_id));
        }
    }

    public function finishAction() {
        $session = new Zend_Session_Namespace('Siteeventticket_Event_Bill_Payment_Detail');

        if (!empty($session->siteeventticketEventBillPaymentDetail))
            $session->siteeventticketEventBillPaymentDetail = '';

        $paymentDetail = array();
        $paymentDetail['state'] = $this->_getParam('state');

        if (!empty($this->_session->errorMessage)) {
            $paymentDetail['errorMessage'] = $this->_session->errorMessage;
        }

        $session->siteeventticketEventBillPaymentDetail = $paymentDetail;

        return $this->_helper->redirector->gotoRoute(array('controller' => 'order', 'action' => 'event-transaction', 'event_id' => $this->_getParam('event_id'), 'tab' => 1), 'siteeventticket_order', true);
    }

}
