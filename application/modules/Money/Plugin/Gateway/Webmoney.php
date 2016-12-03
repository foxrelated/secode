<?php

class Money_Plugin_Gateway_Webmoney extends Money_Plugin_Gateway_Abstract
{
    protected $_gatewayInfo;

    protected $_gateway;

    // General

    /**
     * Constructor
     */
    public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo) {
        $this->_gatewayInfo = $gatewayInfo;
    }

    public function getService() {
        return $this->getGateway()->getService();
    }

    public function getGateway() {

        if (null === $this->_gateway) {
            $class = 'Engine_Payment_Gateway_Webmoney';
            Engine_Loader::loadClass($class);
            $gateway = new $class(array(
                        'config' => (array) $this->_gatewayInfo->config,
                        'testMode' => $this->_gatewayInfo->test_mode,
                        'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('money.currency', 'USD'),
                    ));
            if (!($gateway instanceof Engine_Payment_Gateway_Webmoney)) {
                throw new Engine_Exception('Plugin class not instance of Money_Plugin_Webmoney');
            }
            $this->_gateway = $gateway;
        }

        return $this->_gateway;
    }

    public function createTransaction(array $params) {

        $transaction = new Engine_Payment_Transaction($params);
        $transaction->process($this->getGateway());
        return $transaction;
    }



    public function createSubscriptionTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $subscription, $package, array $params = array()) {

        $params = array(
            'LMI_PAYMENT_AMOUNT' => $params['amount'],
            'LMI_PAYMENT_NO' => $params['vendor_order_id'],
            'LMI_PAYMENT_DESC' => $params['title'],
            'LMI_FAIL_URL' => $params['cancel_url'],
            'LMI_FAIL_METHOD' => 1,
            'LMI_SUCCESS_URL' => $params['succes_url'],
            'LMI_SUCCESS_METHOD' => 1,
                 'LMI_RESULT_URL' => $params['result_url'],
                'LMI_PREREQUEST' => 1,
        );

        $transaction = $this->createTransaction($params);
        return $transaction;
    }

    

    public function onSubscriptionTransactionReturn(Money_Model_Order $order, array $params = array()) {


        if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
            throw new Engine_Payment_Plugin_Exception('Gateways do not match');
        }

        // Get related info
        $user = $order->getUser();
        $subscription = $order->getSource();


        // Check subscription state
        if ($subscription->status == 'active' ||
                $subscription->status == 'trial') {
            return 'active';
        } else if ($subscription->status == 'pending') {
            return 'pending';
        }

        // Check for cancel state - the user cancelled the transaction
        if ($params['state'] == 'cancel') {
            // Cancel order and subscription?
            $order->onCancel();
            $subscription->onPaymentFailure();
            // Error
            throw new Money_Model_Exception('Your payment has been cancelled and ' .
                    'not been charged. If this is not correct, please try again later.');
        }

        if ($params['state'] == 'succes') {
            $data['status'] = 'completed';
        }
        if($params['state'] == 'result'){
            $this->getService()->checkWebmoney($params);
            $data['status'] = 'completed';
        }

        try {
            $data = $this->getService()->_WMXML18($params);
        } catch (Exception $e) {
            // Cancel order and subscription?
            $order->onFailure();
            $subscription->onPaymentFailure();
            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your ' .
                    'transaction. Please try again later.');
        }



        $paymentStatus = null;
        $orderStatus = null;
        switch ($data['status']) {
            case 'failed':

                $paymentStatus = 'failed';
                $orderStatus = 'failed';
                break;

            case 'completed':

                $paymentStatus = 'okay';
                $orderStatus = 'complete';
                break;
            default: // No idea what's going on here
                $paymentStatus = 'failed';
                $orderStatus = 'failed'; // This should probably be 'failed'
                break;
        }

        // Update order with profile info and complete status?
        $order->state = $orderStatus;
        $order->gateway_transaction_id = $params['LMI_SYS_TRANS_NO'];
        $order->gateway_order_id = $params['LMI_SYS_INVS_NO'];
        $order->save();


        if ($paymentStatus == 'okay' && $orderStatus == 'complete') {
            // Insert transaction
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'money');
            $transactionsTable->insert(array(
                'user_id' => $order->user_id,
                'gateway_id' => $this->_gatewayInfo->gateway_id,
                'timestamp' => new Zend_Db_Expr('NOW()'),
                'order_id' => $order->order_id,
                'type' => 2,
                'state' => $paymentStatus,
                'gateway_transaction_id' => $params['LMI_SYS_TRANS_NO'],
                'amount' => $data['amount'],
                'currency' => 'RUB',
            ));


            
            
            if ($paymentStatus == 'okay' && $orderStatus == 'complete') {
                $money = Engine_Api::_()->getDbtable('money', 'money')->updateMoneyPayPal($user, $data['amount']);
            }

            // Check payment status
            if ($paymentStatus == 'okay' || ($paymentStatus == 'pending')) {

                // Update subscription info
                //   $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
                //  $subscription->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];
                // Payment success
                $subscription->onPaymentSuccess();


                return 'active';
            } else if ($paymentStatus == 'pending') {

                // Update subscription info
                $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
                // $subscription->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];
                // Payment pending
                $subscription->onPaymentPending();            

                return 'pending';
            } else if ($paymentStatus == 'failed') {
                // Cancel order and subscription?
                $order->onFailure();
                $subscription->onPaymentFailure();
                // Payment failed
                throw new Money_Model_Exception('Your payment could not be ' .
                        'completed. Please ensure there are sufficient available funds ' .
                        'in your account.');
            } else {
                // This is a sanity error and cannot produce information a user could use
                // to correct the problem.
                throw new Money_Model_Exception('There was an error processing your ' .
                        'transaction. Please try again later.');
            }
        }
    }


    /**
     * Cancel a subscription (i.e. disable the recurring payment profile)
     *
     * @params $transactionId
     * @return Engine_Payment_Plugin_Abstract
     */
    public function cancelSubscription($transactionId, $note = null) {
        $profileId = null;

        if ($transactionId instanceof Payment_Model_Subscription) {
            $package = $transactionId->getPackage();
            if ($package->isOneTime()) {
                return $this;
            }
            $profileId = $transactionId->gateway_profile_id;
        } else if (is_string($transactionId)) {
            $profileId = $transactionId;
        } else {
            // Should we throw?
            return $this;
        }

        try {
            $r = $this->getService()->cancelRecurringPaymentsProfile($profileId, $note);
        } catch (Exception $e) {
            // throw?
        }

        return $this;
    }

    /**
     * Generate href to a page detailing the order
     *
     * @param string $transactionId
     * @return string
     */
    public function getOrderDetailLink($orderId) {
        // @todo make sure this is correct
        // I don't think this works
        if ($this->getGateway()->getTestMode()) {
            // Note: it doesn't work in test mode
            return 'https://www.sandbox.paypal.com/vst/?id=' . $orderId;
        } else {
            return 'https://www.paypal.com/vst/?id=' . $orderId;
        }
    }

    /**
     * Generate href to a page detailing the transaction
     *
     * @param string $transactionId
     * @return string
     */
    public function getTransactionDetailLink($transactionId) {
        // @todo make sure this is correct
        if ($this->getGateway()->getTestMode()) {
            // Note: it doesn't work in test mode
            return 'https://www.sandbox.paypal.com/vst/?id=' . $transactionId;
        } else {
            return 'https://www.paypal.com/vst/?id=' . $transactionId;
        }
    }

    /**
     * Get raw data about an order or recurring payment profile
     *
     * @param string $orderId
     * @return array
     */
    public function getOrderDetails($orderId) {
        // We don't know if this is a recurring payment profile or a transaction id,
        // so try both
        try {
            return $this->getService()->detailRecurringPaymentsProfile($orderId);
        } catch (Exception $e) {
            echo $e;
        }

        try {
            return $this->getTransactionDetails($orderId);
        } catch (Exception $e) {
            echo $e;
        }

        return false;
    }

    /**
     * Get raw data about a transaction
     *
     * @param $transactionId
     * @return array
     */
    public function getTransactionDetails($transactionId) {
        return $this->getService()->detailTransaction($transactionId);
    }


    // Forms

    /**
     * Get the admin form for editing the gateway info
     *
     * @return Engine_Form
     */
    public function getAdminGatewayForm() {
        return new Money_Form_Admin_Gateway_Webmoney();
    }

    public function processAdminGatewayForm(array $values) {
        return $values;
    }

}