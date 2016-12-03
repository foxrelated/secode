<?php
/**
 * SocialEnginePro
 *
 * @category   Application_Extensions
 * @package    E-money
 * @author     Azim
 */

/**
 * @category   Application_Extensions
 * @package    E-money
 */
class Money_SubscriptionController extends Core_Controller_Action_Standard
{
    protected $_user;

    protected $_gateway;

    protected $_session;

    protected $_package;

    public function init() {
        if (Engine_Api::_()->getDbtable('gateways', 'money')->getEnabledGatewayCount() <= 0 ) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }
        $this->_user = Engine_Api::_()->user()->getViewer();
        $this->_session = new Zend_Session_Namespace('Money_Subscription');

        // Check viewer and user
        if (!$this->_user || !$this->_user->getIdentity()) {
            if (!empty($this->_session->user_id)) {
                $this->_user = Engine_Api::_()->getItem('user', $this->_session->user_id);
            }
            // If no user, redirect to home?
            if (!$this->_user || !$this->_user->getIdentity()) {
                $this->_session->unsetAll();
                return $this->_helper->redirector->gotoRoute(array(), 'default', true);
            }
        }
    }

    public function chooseAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('money_main', array(), 'money_main_recharge');
        $this->view->form = $form = new Money_Form_Recharge();
         $this->view->commission = $commission = Engine_Api::_()->getApi('settings', 'core')->getSetting('money.commission');
        unset($this->_session->package_id);
        unset($this->_session->subscription_id);
        unset($this->_session->gateway_id);
        unset($this->_session->order_id);
        unset($this->_session->errorMessage);

        if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost()))
            return;
        $values = $form->getValues();

       

        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'money');
        $user = $this->_user;
        $currentSubscription = $subscriptionsTable->fetchRow(array(
            'user_id = ?' => $user->getIdentity(),
            'active = ?' => true,
                ));

        Engine_Api::_()->getDbtable('subscriptions', 'money')
                ->cancelAll($user, 'User cancelled the subscription.', $currentSubscription);

        $values = $this->getRequest()->getPost();

        $db = $subscriptionsTable->getAdapter();
        $db->beginTransaction();

        try {
            $subscription = $subscriptionsTable->createRow();
            $subscription->setFromArray(array(
                'user_id' => $user->getIdentity(),
                'status' => 'initial',
                'active' => false, // Will set to active on payment success
                'creation_date' => new Zend_Db_Expr('NOW()'),
            ));
            $subscription->save();

            $subscription_id = $subscription->subscription_id;

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if ($values['gateway'] != 3) {
            if(empty($values['amount'])){
                 $package = Engine_Api::_()->getItem('money_package', $values['plan']);
                 
                 $this->_session->amount = $package->price + round(($package->price * $commission) / 100, 2);
                 
            }
            else{
                $this->_session->amount = $values['amount'] + round(($values['amount'] * $commission) / 100, 2);
            }
            
        } else {
             $package = Engine_Api::_()->getItem('money_package', $values['plan']);
             $subscription->package_id = $package->package_id;
                 $subscription->save();
            $this->_session->package = $values['plan'];
        }
        $this->_session->subscription_id = $subscription_id;
        $this->_session->gateway_id = $values['gateway'];

        return $this->_helper->redirector->gotoRoute(array('action' => 'process'));
    }

    public function processAction() {
        $gatewayId = $this->_session->gateway_id;
        if (!$gatewayId || !($gateway = Engine_Api::_()->getItem('money_gateway', $gatewayId)) || !($gateway->enabled)) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'choose'));
        }



        $this->view->gateway = $gateway;


        $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);

        if (!$subscriptionId || !($subscription = Engine_Api::_()->getItem('money_subscription', $subscriptionId))) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'choose'));
        }

        $this->view->subscription = $subscription;

        $ordersTable = Engine_Api::_()->getDbtable('orders', 'money');
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
            'source_type' => 'money_subscription',
            'source_id' => $subscription->subscription_id,
        ));

        $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

        unset($this->_session->subscription_id);
        unset($this->_session->gateway_id);

        $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
        $plugin = $gateway->getPlugin();

        $schema = 'http://';
        if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
            $schema = 'https://';
        }
        $host = $_SERVER['HTTP_HOST'];


        // Prepare transaction
        $params = array();
        $params['language'] = $this->_user->language;
        $localeParts = explode('_', $this->_user->language);
        if (count($localeParts) > 1) {
            $params['region'] = $localeParts[1];
        }
        $params['vendor_order_id'] = $order_id;
        
        $params['user_back_url'] = $schema . $host
                . $this->view->baseUrl().'/e-money';
        
        $params['return_url'] = $schema . $host
                . $this->view->url(array('action' => 'return'))
                . '?order_id=' . $order_id
                //. '?gateway_id=' . $this->_gateway->gateway_id
                //. '&subscription_id=' . $this->_subscription->subscription_id
                . '&amp;state=' . 'return';
        $params['cancel_url'] = $schema . $host
                . $this->view->url(array('action' => 'return'))
                . '?order_id=' . $order_id
                . '&state=' . 'cancel';


        if ($gateway->getIdentity() == 2) {
            $params['result_url'] = $schema . $host
                    . $this->view->url(array('action' => 'return'))
                    . '?order_id=' . $order_id
                    . 'state=' . 'result';
            $params['succes_url'] = $schema . $host
                    . $this->view->url(array('action' => 'return'))
                    . '?order_id=' . $order_id
                    //. '?gateway_id=' . $this->_gateway->gateway_id
                    //. '&subscription_id=' . $this->_subscription->subscription_id
                    . '&state=' . 'succes';
        }


        $params['amount'] = $this->_session->amount;
        $params['title'] = 'Recharge';

        $package = Engine_Api::_()->getItem('money_package', $this->_session->package);
       
        $transaction = $plugin->createSubscriptionTransaction($this->_user, $subscription, $package, $params);



        $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
        $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
        $this->view->transactionData = $transactionData = $transaction->getData(); 
    }

    public function returnAction() {

        // Get order
        if (!$this->_user ||
                !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
                !($order = Engine_Api::_()->getItem('money_order', $orderId)) ||
                $order->user_id != $this->_user->getIdentity() ||
                $order->source_type != 'money_subscription' ||
                !($subscription = $order->getSource()) ||
                !($gateway = Engine_Api::_()->getItem('money_gateway', $order->gateway_id))) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        // Get gateway plugin
        $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
        $plugin = $gateway->getPlugin();
        
        // Process return
        unset($this->_session->errorMessage);
        try {
            $status = $plugin->onSubscriptionTransactionReturn($order, $this->_getAllParams());
        } catch (Money_Model_Exception $e) {
            $status = 'failure';
            $this->_session->errorMessage = $e->getMessage();
        }

        return $this->_finishPayment($status);
    }

    public function finishAction() {
        $this->view->status = $status = $this->_getParam('state');
        $this->view->error = $this->_session->errorMessage;
    }

    protected function _finishPayment($state = 'active') {
        $viewer = Engine_Api::_()->user()->getViewer();
        $user = $this->_user;

        // No user?
        if (!$this->_user) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        // Log the user in, if they aren't already
        if (($state == 'active' || $state == 'free') &&
                $this->_user &&
                !$this->_user->isSelf($viewer) &&
                !$viewer->getIdentity()) {
            Zend_Auth::getInstance()->getStorage()->write($this->_user->getIdentity());
            Engine_Api::_()->user()->setViewer();
            $viewer = $this->_user;
        }

        // Handle email verification or pending approval
        if ($viewer->getIdentity() && !$viewer->enabled) {
            Engine_Api::_()->user()->setViewer(null);
            Engine_Api::_()->user()->getAuth()->getStorage()->clear();

            $confirmSession = new Zend_Session_Namespace('Signup_Confirm');
            $confirmSession->approved = $viewer->approved;
            $confirmSession->verified = $viewer->verified;
            $confirmSession->enabled = $viewer->enabled;
            return $this->_helper->_redirector->gotoRoute(array('action' => 'confirm'), 'user_signup', true);
        }

        // Clear session
        $errorMessage = $this->_session->errorMessage;
        $userIdentity = $this->_session->user_id;
        $this->_session->unsetAll();
        $this->_session->user_id = $userIdentity;
        $this->_session->errorMessage = $errorMessage;

        // Redirect
        if ($state == 'free') {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state));
        }
    }

}