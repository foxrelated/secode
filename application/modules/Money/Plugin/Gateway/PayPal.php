<?php

class Money_Plugin_Gateway_PayPal extends Money_Plugin_Gateway_Abstract {

    protected $_gatewayInfo;
    protected $_gateway;



    // General

    /**
     * Constructor
     */
    public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo) {
        $this->_gatewayInfo = $gatewayInfo;

        // @todo
        // https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_profile-recurring-payments&encrypted_profile_id=
    }

    /**
     * Get the service API
     *
     * @return Engine_Service_PayPal
     */
    public function getService() {
        return $this->getGateway()->getService();
    }

    /**
     * Get the gateway object
     *
     * @return Engine_Payment_Gateway
     */
    public function getGateway() {
        if (null === $this->_gateway) {
            $class = 'Engine_Payment_Gateway_PayPal';
            Engine_Loader::loadClass($class);
            $gateway = new $class(array(
                        'config' => (array) $this->_gatewayInfo->config,
                        'testMode' => $this->_gatewayInfo->test_mode,
                        'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('money.currency', 'USD'),
                    ));
            if (!($gateway instanceof Engine_Payment_Gateway)) {
                throw new Engine_Exception('Plugin class not instance of Engine_Payment_Gateway');
            }
            $this->_gateway = $gateway;
        }

        return $this->_gateway;
    }

    // Actions

    /**
     * Create a transaction object from specified parameters
     *
     * @return Engine_Payment_Transaction
     */
    public function createTransaction(array $params) {

        $transaction = new Engine_Payment_Transaction($params);
        $transaction->process($this->getGateway());
        return $transaction;
    }



    // SEv4 Specific

    /**
     * Create a transaction for a subscription
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $subscription
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createSubscriptionTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $subscription, $package, array $params = array()) {
        
        

        $amount = $params['amount'];
        
        $amount = round($amount, 2);
       

        $params['driverSpecificParams']['PayPal'] = array(
            'AMT' => $amount,
            'DESC' => '',
            'CUSTOM' => $subscription->subscription_id,
            'INVNUM' => $params['vendor_order_id'],
            'ITEMAMT' => $amount,
            'ITEMS' => array(
                array(
                    'NAME' => $params['title'],
                    'DESC' => '',
                    'AMT' => $amount,
                    'NUMBER' => $subscription->subscription_id,
                    'QTY' => 1,
                ),
            )
    
        );

        // Should fix some issues with GiroPay
        if (!empty($params['return_url'])) {
            $params['driverSpecificParams']['PayPal']['GIROPAYSUCCESSURL'] = $params['return_url']
                    . ( false === strpos($params['return_url'], '?') ? '?' : '&' ) . 'giropay=1';
            $params['driverSpecificParams']['PayPal']['BANKTXNPENDINGURL'] = $params['return_url']
                    . ( false === strpos($params['return_url'], '?') ? '?' : '&' ) . 'giropay=1';
        }
        if (!empty($params['cancel_url'])) {
            $params['driverSpecificParams']['PayPal']['GIROPAYCANCELURL'] = $params['cancel_url']
                    . ( false === strpos($params['return_url'], '?') ? '?' : '&' ) . 'giropay=1';
        }



        // Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

    /**
     * Process return of subscription transaction
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onSubscriptionTransactionReturn(
    Money_Model_Order $order, array $params = array()) {
        // Check that gateways match
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

        // Check params
        if (empty($params['token'])) {
            // Cancel order and subscription?
            $order->onFailure();
            $subscription->onPaymentFailure();
            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Money_Model_Exception('There was an error processing your ' .
                    'transaction. Please try again later.');
        }

        // Get details
        try {
            $data = $this->getService()->detailExpressCheckout($params['token']);
        } catch (Exception $e) {
            // Cancel order and subscription?
            $order->onFailure();
            $subscription->onPaymentFailure();
            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your ' .
                    'transaction. Please try again later.');
        }

        // Let's log it
        $this->getGateway()->getLog()->log('ExpressCheckoutDetail: '
                . print_r($data, true), Zend_Log::INFO);


        // Do payment
        try {
            $rdata = $this->getService()->doExpressCheckoutPayment($params['token'], $params['PayerID'], array(
                        'PAYMENTACTION' => 'Sale',
                        'AMT' => $data['AMT'],
                        'CURRENCYCODE' => $this->getGateway()->getCurrency(),
                    ));
        } catch (Exception $e) {
            // Log the error
            $this->getGateway()->getLog()->log('DoExpressCheckoutPaymentError: '
                    . $e->__toString(), Zend_Log::ERR);

            // Cancel order and subscription?
            $order->onFailure();
            $subscription->onPaymentFailure();
            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Money_Model_Exception('There was an error processing your ' .
                    'transaction. Please try again later.');
        }

        // Let's log it
        $this->getGateway()->getLog()->log('DoExpressCheckoutPayment: '
                . print_r($rdata, true), Zend_Log::INFO);

        // Get payment state
        $paymentStatus = null;
        $orderStatus = null;
        switch (strtolower($rdata['PAYMENTINFO'][0]['PAYMENTSTATUS'])) {
            case 'created':
            case 'pending':
                $paymentStatus = 'pending';
                $orderStatus = 'complete';
                break;

            case 'completed':
            case 'processed':
            case 'canceled_reversal': // Probably doesn't apply
                $paymentStatus = 'okay';
                $orderStatus = 'complete';
                break;

            case 'denied':
            case 'failed':
            case 'voided': // Probably doesn't apply
            case 'reversed': // Probably doesn't apply
            case 'refunded': // Probably doesn't apply
            case 'expired':  // Probably doesn't apply
            default: // No idea what's going on here
                $paymentStatus = 'failed';
                $orderStatus = 'failed'; // This should probably be 'failed'
                break;
        }

        // Update order with profile info and complete status?
        $order->state = $orderStatus;
        $order->gateway_transaction_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];
        $order->save();

        // Insert transaction
        $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'money');
        $transactionsTable->insert(array(
            'user_id' => $order->user_id,
            'gateway_id' => $this->_gatewayInfo->gateway_id,
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'order_id' => $order->order_id,
            'type' => 1,
            'state' => $paymentStatus,
            'gateway_transaction_id' => $rdata['PAYMENTINFO'][0]['TRANSACTIONID'],
            'amount' => $rdata['AMT'], // @todo use this or gross (-fee)?
            'currency' => $rdata['PAYMENTINFO'][0]['CURRENCYCODE'],
        ));



        if ($paymentStatus == 'okay' && $orderStatus == 'complete') {
            $money = Engine_Api::_()->getDbtable('money', 'money')->updateMoneyPayPal($user, $rdata['AMT']);
        }

        // Check payment status
        if ($paymentStatus == 'okay' || ($paymentStatus == 'pending')) {

            // Update subscription info
            $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
            $subscription->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];

            // Payment success
            $subscription->onPaymentSuccess();

           

            return 'active';
        } else if ($paymentStatus == 'pending') {

            // Update subscription info
            $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
            $subscription->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];

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
        return new Money_Form_Admin_Gateway_PayPal();
    }

    public function processAdminGatewayForm(array $values) {
        return $values;
    }

}