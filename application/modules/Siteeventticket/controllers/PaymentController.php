<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PaymentController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_PaymentController extends Core_Controller_Action_Standard {

    protected $_user;
    protected $_session;
    protected $_order;
    protected $_gateway;
    protected $_subscription;
    protected $_user_order;
    protected $_success;
    protected $_event_gateway_id;

    public function init() {

        // Get user and session
        $this->_session = new Zend_Session_Namespace('Payment_Siteeventticket');
        $this->_success = new Zend_Session_Namespace('Payment_Success');

        // Check viewer and user
        if (!$this->_user_order) {
            if (!empty($this->_session->user_order_id)) {
                $this->_user_order = Engine_Api::_()->getItem('siteeventticket_order', $this->_session->user_order_id);
            }
        }
    }

    public function processAction() { 
        if (!$this->_user_order) {
            $this->_session->unsetAll();

            return $this->_helper->redirector->gotoRoute(array(), 'siteevent_general', true);
        }

        $parent_order_id = $this->_session->user_order_id;

        if (!empty($this->_session->checkout_event_id)) {
            $event_id = $this->_session->checkout_event_id;
            
            $plugin = 'Payment_Plugin_Gateway_PayPal';
            if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
                $gatewayObject = Engine_Api::_()->getItem('payment_gateway', $this->_user_order->gateway_id);
                if(!empty($gatewayObject)) {
                  $plugin = $gatewayObject->plugin;
                }
            }            
            
            $event_gateway_id = Engine_Api::_()->getDbtable('sellergateways', 'siteeventticket')->getPayPalGatewayId($event_id, $plugin);
            if (empty($event_gateway_id))
                return $this->_helper->redirector->gotoRoute(array(), 'siteevent_general', true);
            else
                $this->_event_gateway_id = $event_gateway_id;
        }

        // Get order
        if (!$parent_order_id ||
                !($user_order = Engine_Api::_()->getItem('siteeventticket_order', $parent_order_id))) {
            return $this->_helper->redirector->gotoRoute(array(), 'siteevent_general', true);
        }

        // Process
        $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
        if (!empty($this->_session->order_id)) {
            $previousOrder = $ordersTable->find($this->_session->order_id)->current();
            if ($previousOrder && $previousOrder->state == 'pending') {
                $previousOrder->state = 'incomplete';
                $previousOrder->save();
            }
        }

        $sourceType = 'siteeventticket_order';
        $sourceId = $parent_order_id;
        $gateway_id = $user_order->gateway_id;

        // Create order
        $ordersTable->insert(array(
            'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
            'gateway_id' => $gateway_id,
            'state' => 'pending',
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ));
        $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

        if (empty($event_id))
            $gateway = Engine_Api::_()->getItem('siteeventticket_usergateway', $gateway_id);
        else
            $gateway = Engine_Api::_()->getItem('siteeventticket_sellergateway', $event_gateway_id);
        
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && $coreSettings->getSetting('sitegateway.stripeconnect', 0)) {
            $stripeGatewayId = Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => 'Sitegateway_Plugin_Gateway_Stripe', 'columnName' => 'gateway_id'));
            if($gateway_id == $stripeGatewayId) {
                if($coreSettings->getSetting('sitegateway.stripechargemethod', 1) && !$coreSettings->getSetting('siteeventticket.payment.to.siteadmin', 0)) {
                    $gateway = Engine_Api::_()->getItem('siteeventticket_usergateway', $gateway_id);
                }
                elseif(!$coreSettings->getSetting('sitegateway.stripechargemethod', 1) && $coreSettings->getSetting('siteeventticket.payment.to.siteadmin', 0)) {
                    $eventGatewayObj = Engine_Api::_()->getDbtable('gateways', 'siteeventticket')->fetchRow(array('event_id = ?' => $user_order->event_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_Stripe\''));
                    $gateway = Engine_Api::_()->getItem('siteeventticket_gateway', $eventGatewayObj->gateway_id);
                }       
            }
        }
        
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
                . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'siteeventticket'), 'default')
                . '?order_id=' . $order_id
                . '&state=' . 'return';
        $params['cancel_url'] = $schema . $host
                . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'siteeventticket'), 'default')
                . '?order_id=' . $order_id
                . '&state=' . 'cancel';
        $params['ipn_url'] = $schema . $host
                . $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'payment'), 'default')
                . '?order_id=' . $order_id;

        if (!empty($event_id)) {
            $params['event_id'] = $event_id;

            $params['return_url'] .= '&event_id=' . $event_id . '&event_gateway_id=' . $event_gateway_id;
            $params['cancel_url'] .= '&event_id=' . $event_id . '&event_gateway_id=' . $event_gateway_id;
            $params['ipn_url'] .= '&event_id=' . $event_id . '&event_gateway_id=' . $event_gateway_id;
        }
        
        $params['source_type'] = $sourceType;

        // Process transaction
        $transaction = $plugin->createUserOrderTransaction($parent_order_id, $params, $this->_user);

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
                ($order->source_type != 'siteeventticket_order') ||
                !($user_order = $order->getSource())) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        $event_gateway_id = $this->_getParam('event_gateway_id');
        if (empty($event_gateway_id))
            $gateway = Engine_Api::_()->getItem('siteeventticket_usergateway', $order->gateway_id);
        else
            $gateway = Engine_Api::_()->getItem('siteeventticket_sellergateway', $event_gateway_id);

        if (!$gateway)
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);

        // Get gateway plugin
        $plugin = $gateway->getPlugin();

        unset($this->_session->errorMessage);

        try {
            $status = $plugin->onUserOrderTransactionReturn($order, $this->_getAllParams());
        } catch (Payment_Model_Exception $e) {
            $status = 'failure';
            $this->_session->errorMessage = $e->getMessage();
        }

        $this->_success->succes_id = $user_order->order_id;

        return $this->_finishPayment($status);
    }

    protected function _finishPayment($state = 'active') {

        // Clear session
        $errorMessage = $this->_session->errorMessage;
        $this->_session->unsetAll();
        $this->_session->errorMessage = $errorMessage;
        $event_id = $this->_session->checkout_event_id;
        // Redirect
        if ($state == 'free') {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state, 'event_id' => $event_id));
        }
    }

    public function finishAction() {
        $session = new Zend_Session_Namespace('Siteeventticket_Order_Payment_Detail');

        if (!empty($session->siteeventticketOrderPaymentDetail))
            $session->siteeventticketOrderPaymentDetail = '';

        $paymentDetail = array('success_id' => $this->_success->succes_id, 'state' => $this->_getParam('state'), 'errorMessage' => $this->_session->errorMessage);
        $event_id = $this->_session->checkout_event_id;
        $session->siteeventticketOrderPaymentDetail = $paymentDetail;

        return $this->_helper->redirector->gotoRoute(array('action' => 'success', 'event_id' =>$event_id), 'siteeventticket_order', false);
    }

    public function detailTransactionAction() {
        $transaction_id = $this->_getParam('transaction_id', null);

        if (empty($transaction_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $transaction = Engine_Api::_()->getItem('siteeventticket_transaction', $transaction_id);
        $gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);

        $link = null;
        if ($this->_getParam('show-parent')) {
            if (!empty($transaction->gateway_parent_transaction_id)) {
                $link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_parent_transaction_id);
            }
        } else {
            if (!empty($transaction->gateway_transaction_id)) {
                $link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_transaction_id);
            }
        }

        if ($link) {
            return $this->_helper->redirector->gotoUrl($link, array('prependBase' => false));
        } else {
            die();
        }
    }

}
