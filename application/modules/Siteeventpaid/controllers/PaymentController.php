<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PaymentController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventpaid_PaymentController extends Core_Controller_Action_Standard {

    /**
     * @var User_Model_User
     */
    protected $_user;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * @var Payment_Model_Order
     */
    protected $_order;

    /**
     * @var Payment_Model_Gateway
     */
    protected $_gateway;

    /**
     * @var Siteevent_Model_Event
     */
    protected $_event;

    /**
     * @var Payment_Model_Package
     */
    protected $_package;
    protected $_success;

    public function init() {

        // Get user and session
        $this->_user = Engine_Api::_()->user()->getViewer();

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            return;

        // If no user, redirect to home?
        if (!$this->_user || !$this->_user->getIdentity()) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "siteevent_general", true);
        }
        
        $siteeventpaidListPackage = Zend_Registry::isRegistered('siteeventpaidListPackage') ? Zend_Registry::get('siteeventpaidListPackage') : null;
        if(empty($siteeventpaidListPackage))
          return;
        
        // If there are no enabled gateways or packages, disable
        if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0 ||
                Engine_Api::_()->getDbtable('packages', 'siteeventpaid')->getEnabledNonFreePackageCount() <= 0) {
            return $this->_forward('show-error');
        }
        $this->_session = new Zend_Session_Namespace('Payment_Siteevent');
        $this->_success = new Zend_Session_Namespace('Payment_Siteevent_Success');
    }

    public function indexAction() {
        return $this->_forward('gateway');
    }

    public function showErrorAction() {
        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation("siteevent_main");
        if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
            $this->view->show = 1;
        } else {
            $this->view->show = 0;
        }
        
         // Hide siteevent navigation in case of webview for native app.
        $this->view->hideNavigation = 0;
        $session = new Zend_Session_Namespace();
        if (isset($session->hideHeaderAndFooter) && !empty($session->hideHeaderAndFooter) && $session->hideHeaderAndFooter == true) {
            $this->view->hideNavigation = 1;
        }
        
    }

    public function gatewayAction() {
        // Get subscription
        $eventId = $this->_getParam('event_id', $this->_session->event_id);
        if (!$eventId ||
                !($event = Engine_Api::_()->getItem('siteevent_event', $eventId))) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "siteevent_general", true);
        }
        $this->view->event = $event;

        // Check subscription status
        if ($this->_checkEventStatus($event)) {
            return;
        }

        // Get subscription
        if (!$this->_user ||
                !( $eventId = $this->_getParam('event_id', $this->_session->event_id)) ||
                !($event = Engine_Api::_()->getItem('siteevent_event', $eventId)) || !($package = Engine_Api::_()->getItem('siteeventpaid_package', $event->package_id))) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "siteevent_general", true);
        }
        $this->view->event = $event;
        $this->view->package = $package;

        // Unset certain keys
        unset($this->_session->gateway_id);
        unset($this->_session->order_id);

         // Hide siteevent navigation in case of webview for native app.
        $this->view->hideNavigation = 0;
        $session = new Zend_Session_Namespace();
        if (isset($session->hideHeaderAndFooter) && !empty($session->hideHeaderAndFooter) && $session->hideHeaderAndFooter == true) {
            $this->view->hideNavigation = 1;
        }
        
        // Gateways
        $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable->select()
                ->where('enabled = ?', 1)
        ;
        $gateways = $gatewayTable->fetchAll($gatewaySelect);

        $gatewayPlugins = array();
        foreach ($gateways as $gateway) {
            // Check billing cycle support
            if (!$package->isOneTime()) {
                $sbc = $gateway->getGateway()->getSupportedBillingCycles();
                if (!in_array($package->recurrence_type, array_map('strtolower', $sbc))) {
                    continue;
                }
            }
            $gatewayPlugins[] = array(
                'gateway' => $gateway,
                'plugin' => $gateway->getGateway(),
            );
        }
        $this->view->gateways = $gatewayPlugins;
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation("siteevent_main");
    }

    public function processAction() {

        //GET GATEWAY
        $gatewayId = $this->_getParam('gateway_id', $this->_session->gateway_id);
        if (!$gatewayId ||
                !($gateway = Engine_Api::_()->getItem('siteeventpaid_gateway', $gatewayId)) ||
                !($gateway->enabled)) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
        }
        $this->view->gateway = $gateway;

        //GET SUBSCRIPTION
        $eventId = $this->_getParam('event_id', $this->_session->event_id);
        if (!$eventId ||
                !($event = Engine_Api::_()->getItem('siteevent_event', $eventId))) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "siteevent_general", true);
        }
        $this->view->event = $event;

        //GET PACKAGE
        $package = $event->getPackage();
        if (!$package || $package->isFree()) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "siteevent_general", true);
        }
        $this->view->package = $package;

        //CHECK SUBSCRIPTION?
        if ($this->_checkEventStatus($event)) {
            return;
        }

        //PROCESS
        //CREATE ORDER
        $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
        if (!empty($this->_session->order_id)) {
            $previousOrder = $ordersTable->find($this->_session->order_id)->current();
            if ($previousOrder && $previousOrder->state == 'pending') {
                $previousOrder->state = 'incomplete';
                $previousOrder->save();
            }
        }
        $ordersTable->insert(array(
            'user_id' => $this->_user->getIdentity(),
            'gateway_id' => $gateway->gateway_id,
            'state' => 'pending',
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'source_type' => 'siteevent_event',
            'source_id' => $event->event_id,
        ));
        $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

        //UNSET CERTAIN KEYS
        unset($this->_session->package_id);
        unset($this->_session->event_id);
        unset($this->_session->gateway_id);

        //GET GATEWAY PLUGIN
        $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
        $plugin = $gateway->getPlugin();


        //PREPARE HOST INFO
        $schema = 'http://';
        if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
            $schema = 'https://';
        }
        $host = $_SERVER['HTTP_HOST'];

        //PREPARE TRANSACTION
        $params = array();
        $params['language'] = $this->_user->language;
        $localeParts = explode('_', $this->_user->language);
        if (count($localeParts) > 1) {
            $params['region'] = $localeParts[1];
        }
        $params['vendor_order_id'] = $order_id;
        $params['return_url'] = $schema . $host
                . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'siteeventpaid'), 'default')
                . '?order_id=' . $order_id
                . '&state=' . 'return';
        $params['cancel_url'] = $schema . $host
                . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'siteeventpaid'), 'default')
                . '?order_id=' . $order_id
                . '&state=' . 'cancel';
        $params['ipn_url'] = $schema . $host
                . $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'siteeventpaid'), 'default')
                . '&order_id=' . $order_id;
        //PROCESS TRANSACTION
        $transaction = $plugin->createEventTransaction($this->_user, $event, $package, $params);

        //PULL TRANSACTION PARAMS
        $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
        $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
        $this->view->transactionData = $transactionData = $transaction->getData();

        //HANDLE REDIRECTION
        if ($transactionMethod == 'GET') {
            $transactionUrl .= '?' . http_build_query($transactionData);
            return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
        }

        //POST WILL BE HANDLED BY THE VIEW SCRIPT
    }

    public function returnAction() {

        //GET ORDER
        if (!$this->_user ||
                !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
                !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
                $order->user_id != $this->_user->getIdentity() ||
                $order->source_type != 'siteevent_event' ||
                !($event = $order->getSource()) ||
                !($package = $event->getPackage()) ||
                !($gateway = Engine_Api::_()->getItem('siteeventpaid_gateway', $order->gateway_id))) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "siteevent_general", true);
        }

        $this->_event = $event;
        //GET GATEWAY PLUGIN
        $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
        $plugin = $gateway->getPlugin();

        //PROCESS RETURN
        unset($this->_session->errorMessage);
        try {
            $status = $plugin->onPageTransactionReturn($order, $this->_getAllParams());
        } catch (Payment_Model_Exception $e) {
            $status = 'failure';
            $this->_session->errorMessage = $e->getMessage();
        }
        $this->_success->succes_id = $event->event_id;
        return $this->_finishPayment($status);
    }

    public function finishAction() {

        $this->view->status = $status = $this->_getParam('state');
        $this->view->error = $this->_session->errorMessage;
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation("siteevent_main");
        
        // Hide form buttons in case of webview for native app.
        $this->view->hideButtons = false;
        $session = new Zend_Session_Namespace();
        if (isset($session->hideHeaderAndFooter) && !empty($session->hideHeaderAndFooter))
            $this->view->hideButtons = true;
        
        if (isset($this->_success->succes_id)) {
            $this->view->id = $this->_success->succes_id;
            unset($this->_success->succes_id);
        }
    }

    protected function _checkEventStatus(
    Zend_Db_Table_Row_Abstract $event = null) {
        if (!$this->_user) {
            return false;
        }

        if (null == $event) {
            $event = Engine_Api::_()->getItem('siteevent_event', $this->_session->event_id);
        }

        if ($event->getPackage()->isFree()) {
            $this->_finishPayment('free');
            return true;
        }

        return false;
    }

    protected function _finishPayment($state = 'active') {

        //REDIRECT
        if ($state == 'free') {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "siteevent_general", true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'controller' => 'payment', 'state' => $state), "siteeventpaid_extended", true);
        }
    }

}
