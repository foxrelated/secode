<?php
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(__FILE__))))));

include_once  APPLICATION_PATH . '/application/modules/Socialstore/externals/scripts/library/googlecart.php';
include_once  APPLICATION_PATH . '/application/modules/Socialstore/externals/scripts/library/googleitem.php';
include_once  APPLICATION_PATH . '/application/modules/Socialstore/externals/scripts/library/googleshipping.php';
include_once  APPLICATION_PATH . '/application/modules/Socialstore/externals/scripts/library/googletax.php';


class Socialstore_Payment_Gateway_Google extends Socialstore_Payment_Gateway_Abstract
{
	const SANDBOX_URL   = 'https://sandbox.google.com/checkout/api/checkout/v2/checkoutForm/Merchant/';
    const GATEWAY_URL   = 'https://checkout.google.com/api/checkout/v2/checkoutForm/Merchant/';
    
    protected $_credentialKeys = array(
        'login' => 'x_login',
        'key'   => 'x_tran_key',
    );

    /**
     * Get requirements
     *
     * @return Socialstore_Payment_Request_Requirements
     */
    protected function _getRequirements()
    {
        $requirements = parent::_getRequirements();
        /*$requirements->setOnMethod(
            array(Socialstore_Payment::ACTION_AUTH, Socialstore_Payment::ACTION_SALE),
            array('Socialstore_Payment_Method_Card', 'Socialstore_Payment_Method_Echeck')
        );
        $requirements->setOnMethod(
            array(self::ACTION_CARD_REFUND, self::ACTION_VERIFICATION),
            array('Socialstore_Payment_Method_Card')
        );
        $requirements->setOnOptions(Socialstore_Payment::ACTION_REFUND, array('card_number'));
        $requirements->setOnOptions(self::ACTION_CAPTURE_VOICE_AUTHORIZATION, array('auth_code'));
        $requirements->setOnOrder(
            array(self::ACTION_CAPTURE_VOICE_AUTHORIZATION, self::ACTION_VERIFICATION, self::ACTION_CARD_REFUND), true
        );*/
        return $requirements;
    }

    /**
     * Method overwritten.
     *
     * Line items custom processing.
     *
     * @param Zend_Http_Client $httpClient
     * @return Socialstore_Payment_GatewayAbstract
     */
    protected function _setRequestParams($httpClient)
    {
        $items = '';
        if (isset($this->_queryParams['x_line_item'])) {
            foreach ($this->_queryParams['x_line_item'] as $item) {
                $items.= '&x_line_item='.urlencode($item);
            }
            unset($this->_queryParams['x_line_item']);
        }
        $data = http_build_query($this->_queryParams, '', '&') . $items;
        $httpClient->setRawData(urldecode($data));
        return $this;
    }

    /**
     * Gateway url getter
     *
     * @return string
     */
    public function getUrl()
    {
        $url = $this->isSandboxMode() ? self::SANDBOX_URL : self::GATEWAY_URL;
        return $url;
    }

    /**
     * Send authorization request to gateway
     *
     * This transaction type is sent for authorization only.
     * The transaction will not be sent for settlement until
     * the transaction type RT_CAPTURE_PRIOR_AUTHORIZATION is submitted
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processAuth($request)
    {
        $this->_initQueryMethod($request);
        $this->_initQueryOrder($request);
        $this->_initQueryOptions($request);
        $this->_queryParams['x_type'] = self::TRXTYPE_AUTH_ONLY;

        return $this->_sendRequest();
    }

    /**
     * Send capture request to gateway
     *
     * The amount is sent for authorization, and if approved, is automatically submitted for settlement
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processSale($request)
    {
        /*$this->_initQueryMethod($request);
        $this->_initQueryOrder($request);
        $this->_initQueryOptions($request);
        $this->_queryParams['x_type']   = self::TRXTYPE_AUTH_CAPTURE;

        return $this->_sendRequest();*/
    	$order = $request->getOrder();
    	$order_items = $order->getItems();
    	$merchant_id = $this->_credentials['x_login'];  // Your Merchant ID
      	$merchant_key = $this->_credentials['x_tran_key'];  // Your Merchant Key
      	$server_type = $this->isSandboxMode() ? 'sandbox' : 'real'; 
      	$currency = $order->currency;
      	$cart = new GoogleCart($merchant_id, $merchant_key, $server_type,$currency);
        if ($order->paytype_id == 'shopping-cart') {
        	$item = new GoogleItem($order->order_id, $order->order_id, '1', $order->total_amount);
        	$cart->AddItem($item);
        }
        else {
	      	foreach ($order_items as $order_item) {
	    		$item = new GoogleItem($order_item->name, $order_item->description, $order_item->quantity, $order_item->price);
	    		$cart->AddItem($item);
	        }
        }
      	$cart->SetMerchantPrivateData(new MerchantPrivateData($order->toArray()));
      // Specify "Return to xyz" link
	    $cart->SetContinueShoppingUrl($request->getOption('return_url'));

      // Request buyer's phone number
      $cart->SetRequestBuyerPhone(false);
      // Display Google Checkout button
      return $cart->CheckoutButtonCode("SMALL");
    }

    /**
     * Send capture request to gateway based on previous transaction id
     *
     * This transaction type is used to complete an RT_AUTHORIZATION transaction
     * that was successfully authorized through the payment gateway.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processCapture($request)
    {
        $transaction = $request->getTransaction();
        $this->_queryParams['x_trans_id']   = $transaction->getId();
        if ($transaction->getAmount()) {
            $this->_queryParams['x_amount']         = $transaction->getAmount();
            $this->_queryParams['x_currency_code']  = $transaction->getCurrency();
        }
        $this->_queryParams['x_type']       = self::TRXTYPE_PRIOR_AUTH_CAPTURE;

        return $this->_sendRequest();
    }

    /**
     * Send capture request to gateway based on auth_code
     *
     * This transaction type is used to complete a previously authorized transaction
     * that was not originally submitted through the payment gateway
     * or that requires voice authorization.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processCaptureVoiceAuthorization($request)
    {
        $this->_initQueryOptions($request);
        $this->_initQueryOrder($request);
        $this->_queryParams['x_type']   = self::TRXTYPE_CAPTURE_ONLY;
        $this->_queryParams['x_auth_code']  = $request->getOptions()->get('auth_code');

        return $this->_sendRequest();
    }

    /**
     * Send void request to gateway
     *
     * This transaction type can be used to cancel either an original transaction that is not yet settled,
     * or an entire order composed of more than one transactions.
     * If the Void transaction errors, the original transaction is likely settled and you can submit
     * a RT_REFUND for the transaction.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processVoid($request)
    {
        $this->_queryParams['x_trans_id']   = $request->getTransaction()->getId();
        $this->_queryParams['x_type']       = self::TRXTYPE_VOID;
        return $this->_sendRequest();
    }

    /**
     * Send refund request to gateway based on previous transaction id
     *
     * This transaction type is used to refund a customer for a transaction
     * that was originally processed and successfully settled through the payment gateway.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processRefund($request)
    {
        $transaction = $request->getTransaction();
        $this->_queryParams['x_trans_id']   = $transaction->getId();
        if ($transaction->getAmount()) {
            $this->_queryParams['x_amount']   = $transaction->getAmount();
        }
        $this->_queryParams['x_card_num']   = $request->getOptions()->get('card_number');
        $this->_queryParams['x_type']       = self::TRXTYPE_CREDIT;

        return $this->_sendRequest();
    }

    /**
     * Send credit card refund request to gateway
     *
     * This transaction type is used to issue a refund for a transaction
     * that was not originally submitted through the payment gateway.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processCardRefund($request)
    {
        $this->_initQueryMethod($request);
        $this->_initQueryOrder($request);
        $this->_queryParams['x_type']   = self::TRXTYPE_CREDIT;

        return $this->_sendRequest();
    }

    /**
     * Send credit card verification request to gateway
     *
     * Visa Verification Transaction
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processVerification($request)
    {
        $this->_initQueryMethod($request);
        $this->_initQueryOrderAddresses($request->getOrder());
        $this->_queryParams['x_type']   = self::TRXTYPE_AUTH_ONLY;
        $this->_queryParams['x_amount'] = 0;

        return $this->_sendRequest();
    }

    /**
     * Prepare initial set of request parameters
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Authorizenet_AimGateway
     */
    protected function _initQuery($request)
    {
        parent::_initQuery($request);
        /*$this->_queryParams['x_version']        = self::API_VERSION;
        $this->_queryParams['x_delim_data']     = 'TRUE';
        $this->_queryParams['x_delim_char']     = self::DELIM_CHAR;
        $this->_queryParams['x_relay_response'] = 'FALSE';*/
        return $this;
    }

    /**
     * Initialize payment method details
     *
     * @param Socialstore_Payment_Request $request
     */
    protected function _initQueryMethod($request)
    {
        $method = $request->getMethod();
        if ($method instanceof Socialstore_Payment_Method_Echeck) {
            $this->_queryParams['x_method']   = self::METHOD_ECHECK;
            $this->_queryParams['x_bank_aba_code']  = $method->getRoutingCode();
            $this->_queryParams['x_bank_acct_num']  = $method->getAccountNumber();
            $this->_queryParams['x_bank_acct_type'] = $method->getAccountType();
            $this->_queryParams['x_bank_name']      = $method->getBankName();
            $this->_queryParams['x_bank_acct_name'] = $method->getAccountName();
            $this->_queryParams['x_echeck_type']    = $method->getTransactionType();
            if ($method->getCheckNumber()) {
                $this->_queryParams['x_bank_check_number']   = $method->getCheckNumber();
            }
        } else {
            $this->_queryParams['x_method']   = self::METHOD_CC;
            $this->_queryParams['x_card_num'] = $method->getNumber();
            $this->_queryParams['x_exp_date'] = $method->getExpirationDate();
            if ($method->getCvv()) {
                $this->_queryParams['x_card_code'] = $method->getCvv();
            }
        }
    }

    /**
     * Initialize request total amounts
     *
     * @param Socialstore_Payment_Request $request
     */
    protected function _initQueryOrder($request)
    {
        $order = $request->getOrder();
        
        $this->_queryParams['x_amount']         = $order->getTotalAmount();
        $this->_queryParams['x_currency_code']  = $order->getCurrency();
        $this->_queryParams['x_po_num']         = $order->getId();
        $this->_queryParams['x_invoice_num']    = $order->getId();
        if ($order->getShippingAmount()) {
            $this->_queryParams['x_freight']    = $order->getShippingAmount();
        }
        if ($order->getTaxAmount()) {
            $this->_queryParams['x_tax']        = $order->getTaxAmount();
        }
        $this->_initQueryOrderAddresses($order);
        if ($order->getItems()) {
            $this->_initQueryOrderItems($order->getItems());
        }
    }

    /**
     * Initialize request addresses
     *
     * @param Socialstore_Payment_Data_Order $order
     */
    protected function _initQueryOrderAddresses($order)
    {
        $billing    = $order->getBillingAddress();
        $shipping   = $order->getShippingAddress();
        if ($billing) {
            $this->_queryParams['x_first_name'] = $billing->getFirstName();
            $this->_queryParams['x_last_name']  = $billing->getLastName();
            $this->_queryParams['x_company']    = $billing->getCompany();
            $this->_queryParams['x_address']    = $billing->getStreet();
            $this->_queryParams['x_city']       = $billing->getCity();
            $this->_queryParams['x_state']      = $billing->getRegion();
            $this->_queryParams['x_zip']        = $billing->getPostcode();
            $this->_queryParams['x_country']    = $billing->getCountry();
            $this->_queryParams['x_phone']      = $billing->getPhone();
            $this->_queryParams['x_fax']        = $billing->getFax();
            $this->_queryParams['x_email']      = $billing->getEmail();
        }
       /* if ($shipping) {
            $this->_queryParams['x_ship_to_first_name'] = $billing->getFirstName();
            $this->_queryParams['x_ship_to_last_name']  = $billing->getLastName();
            $this->_queryParams['x_ship_to_company']    = $billing->getCompany();
            $this->_queryParams['x_ship_to_address']    = $billing->getFullStreet();
            $this->_queryParams['x_ship_to_city']       = $billing->getCity();
            $this->_queryParams['x_ship_to_state']      = $billing->getRegion();
            $this->_queryParams['x_ship_to_zip']        = $billing->getPostCode();
            $this->_queryParams['x_ship_to_country']    = $billing->getCountry();
        }*/
    }

    /**
     * Initialize request items
     *
     * @param array of Socialstore_Payment_Data_Order_Item
     */
    protected function _initQueryOrderItems($items)
    {
        $this->_queryParams['x_line_item'] = array();
        foreach ($items as $item) {
            $this->_queryParams['x_line_item'][] = sprintf(
                '%s<|>%s<|>%s<|>%s<|>%s<|>%s',
                $item->getId(),
                $item->getName(),
                $item->getDescription(),
                $item->getQty(),
                $item->getPrice(),
                $item->getTaxAmount() ? 'TRUE' : 'FALSE'
            );
        }
    }

    /**
     * Initialize additional information
     *
     * @param Socialstore_Payment_Request $request
     */
    protected function _initQueryOptions($request)
    {
        $options = $request->getOptions();
        if ($options) {
            if ($options->get('test_mode') || $this->isSandboxMode()) {
                $this->_queryParams['x_test_request'] = 'TRUE';
            }
            if ($options->get('recurring_billing')) {
                $this->_queryParams['x_recurring_billing'] = 'TRUE';
            }
            if ($options->has('partial_auth')) {
                if ($options->get('partial_auth')) {
                    $this->_queryParams['x_allow_partial_Auth'] = 'TRUE';
                } else {
                    $this->_queryParams['x_allow_partial_Auth'] = 'FALSE';
                }
            }
            $additional = $options->map(
                array(
                    'invoice_id'    => 'x_invoice_num',
                    'customer_id'   => 'x_cust_id',
                    'customer_ip'   => 'x_customer_ip',
                    'customer_tax'  => 'x_customer_tax_id',
                    'description'   => 'x_description',
                    'auth_indicator'=> 'x_authentication_indicator',
                    'auth_value'    => 'x_cardholder_authentication_value',
                    'card_number'   => 'x_card_num',
                )
            );
            $this->_queryParams = array_merge($this->_queryParams, $additional);
        }
    }

    /**
     * Prepare unified response based on HTTP response
     *
     * @param Zend_Http_Response $response
     * @param array              $responseMap
     * @return Socialstore_Payment_Response
     */
    protected function _prepareResponse(Zend_Http_Response $response, $responseMap)
    {
        $body = $response->getRawBody();
        $body = explode(self::DELIM_CHAR, $body);
        $responseMap = array(
            'code', 'subcode', 'reason_code', 'message', 'auth_code', 'avs_code', 'transaction_id',
            'invoice_id', 'description', 'amount', 'method', 'transaction_type', 'customer_id',
            37 => 'md5_hash', 38 =>  'card_verification', 39 => 'cavr_code', 50 => 'account_number',
            51 => 'card_type', 52 => 'tendet_id', 53 => 'requested_amount', 54 => 'card_balabce'
        );


        $options = new Socialstore_Payment_Options($body);
        $options->import($body, $responseMap);

        switch ((int)$options->get('code')) {
            case self::RESPONSE_APPROVED:
                $status = Socialstore_Payment_Response::STATUS_APPROVED;
                break;
            case self::RESPONSE_DECLINED:
                $status = Socialstore_Payment_Response::STATUS_DECLINED;
                break;
            case self::RESPONSE_ERROR:
                $status = Socialstore_Payment_Response::STATUS_ERROR;
                break;
            case self::RESPONSE_HELD:
                $status = Socialstore_Payment_Response::STATUS_PENDING;
                break;
            default:
                $status = Socialstore_Payment_Response::STATUS_ERROR;
                break;
        }


        $result = new Socialstore_Payment_Response($status);
        $result->setMessages($options->get('message'));
        $result->setOptions($options);
        return $result;
    }

}