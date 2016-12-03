<?php

/**
 * SocialEnginePro
 *
 * @category   Application_Extensions
 * @package    money
 * @author     Azim
 */

/**
 * @category   Application_Extensions
 * @package    money
 */
class Money_Plugin_Gateway_LiqPay extends Money_Plugin_Gateway_Abstract {

    protected $_gatewayInfo;
    protected $_gateway;

    public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo) {
        $this->_gatewayInfo = $gatewayInfo;
    }

    public function getService() {
        return $this->getGateway()->getService();
    }

    public function getGateway() {
        if (null === $this->_gateway) {
            $class = 'Engine_Payment_Gateway_LiqPay';
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

    public function createTransaction(array $params) {
        $transaction = new Engine_Payment_Transaction($params);
        $transaction->process($this->getGateway());
        return $transaction;
    }


    public function createSubscriptionTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $subscription, $package, array $params = array())
    {
        $params['driverSpecificParams']['LiqPay'] = array(
            'amount' => $params['amount'],
            'order_id' => $params['vendor_order_id'],
            'user_back_url' => $params['user_back_url']
        );
       

        // Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

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
        
        
        $xml_decoded=base64_decode($params['operation_xml']);
        
//       if(!$this->getService()->validateSignature()){
//           var_dump($params);die;
//       } 

        // Let's log it
        $this->getGateway()->getLog()->log('Return: '
                . print_r($params, true), Zend_Log::INFO);

        
        $xml = simplexml_load_string($xml_decoded);
        
        if($xml->status == 'success'){
            $order->state = 'complete';
        $order->gateway_order_id = $xml->order_id;
        $order->save();

        $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'money');
        $transactionsTable->insert(array(
            'user_id' => $order->user_id,
            'gateway_id' => $this->_gatewayInfo->gateway_id,
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'order_id' => $order->order_id,
            'type' => 11,
            'state' => $order->state,
            'gateway_transaction_id' => $xml->transaction_id,
            'amount' => $xml->amount,
            'currency' => $xml->currency,
        ));

        
        // @todo
        Engine_Api::_()->getDbtable('money', 'money')->updateMoneyPayPal($user, $xml->amount);
        }
        else if($xml->status == 'failure'){
            $order->onCancel();
            $subscription->onPaymentFailure();
            // Error
            throw new Money_Model_Exception('Your payment has been cancelled and ' .
                    'not been charged. If this is not correct, please try again later.');
        }
        else if($xml->status == 'wait_secure'){
            
        }
        
        
    }



    public function cancelSubscription($transactionId) {
        return $this;
    }

    public function getOrderDetailLink($orderId) {
        return 'https://www.2checkout.com/va/sales/detail?sale_id=' . $orderId;
    }

    public function getTransactionDetailLink($transactionId) {
        return 'https://www.2checkout.com/va/sales/get_list_sale_paged?invoice_id=' . $transactionId;
    }

    public function getOrderDetails($orderId) {
        return $this->getService()->detailSale($orderId);
    }

    public function getTransactionDetails($transactionId) {
        return $this->getService()->detailInvoice($transactionId);
    }

    public function onIpn(Engine_Payment_Ipn $ipn) {
      
    }

    public function getAdminGatewayForm() {
        return new Money_Form_Admin_Gateway_LiqPay();
    }

    public function processAdminGatewayForm(array $values) {
        return $values;
    }

}