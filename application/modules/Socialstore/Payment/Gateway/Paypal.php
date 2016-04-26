<?php

class Socialstore_Payment_Gateway_Paypal extends Socialstore_Payment_Gateway_PaypalAbstract
{
    const ACTION_CHECKOUT_DETAILS   = 'checkout_details';

    const LIVE_CHECKOUT_URL     = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
    const SANDBOX_CHECKOUT_URL  = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';

    const TRXTYPE_SET_EXPRESS_CHECKOUT  = 'SetExpressCheckout';
    const TRXTYPE_GET_EXPRESS_CHECKOUT  = 'GetExpressCheckoutDetails';
    const TRXTYPE_DO_EXPRESS_CHECKOUT   = 'DoExpressCheckoutPayment';
    const TRXTYPE_DO_AUTHORIZATION      = 'DoAuthorization'; /* for payment action order only */
    const TRXTYPE_MASSPAY =  'MassPay';
	
	const RECEIVETYPE_EMAIL = 'EmailAddress';

    /**
     * Prepare list of requirements
     *
     * @return Socialstore_Payment_Request_Requirements
     */
    protected function _getRequirements()
    {
        $requirements = parent::_getRequirements();
        $requirements->setOnOptions(Socialstore_Payment::ACTION_INIT, array('return_url', 'cancel_url'));
        $requirements->setOnOptions(self::ACTION_CHECKOUT_DETAILS, array('token'));
        $requirements->setOnOptions(
            array(Socialstore_Payment::ACTION_AUTH, Socialstore_Payment::ACTION_SALE), array('token', 'payer_id')
        );
        return $requirements;
    }

	protected function _initQueryMassPay($options){
		$options = $options->toArray();
		$pay_items =  $options['pay_items'];
		$this->_queryParams['CURRENCYCODE'] = $options['currency'];
		$this->_queryParams['RECEIVERTYPE'] = self::RECEIVETYPE_EMAIL;
		$this->_queryParams['EMAILSUBJECT'] = 'Pay for requested money';
		
		foreach($pay_items as $index=>$item){
			$this->_queryParams['L_EMAIL'.$index] = $item['email'];
			$this->_queryParams['L_AMT'.$index] = $item['amount'];
		}
		
	}
	
	protected function _processMassPay($request){
		
		$this->_queryParams['METHOD'] = self::TRXTYPE_MASSPAY;
		$options = $request->getOptions();
		$this->_initQueryMassPay($options);
		
		$response = $this->_sendRequest();
		//var_dump($response);
		return $response;
	}

    /**
     * Sets up the Express Checkout transaction.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processInit($request)
    {
        $this->_queryParams['METHOD']   = self::TRXTYPE_SET_EXPRESS_CHECKOUT;
        $this->_queryParams['PAYMENTREQUEST_0_PAYMENTACTION'] = self::PAYMENT_ACTION_AUTH;
        $this->_initQueryOrder($request->getOrder());
        $this->_initQuerySetCheckoutInfo($request);
		
		
        $response = $this->_sendRequest(array('TOKEN' => 'token'));
		$sandboxMode = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.mode', '1');
        $url = $sandboxMode ? self::SANDBOX_CHECKOUT_URL : self::LIVE_CHECKOUT_URL;
        $url.=$response->getOption('TOKEN');
        $response->getOptions()->set('redirect_url', $url);
        return $response;
    }

	

    /**
     * Obtains information about the buyer from PayPal, including shipping information.
     *
     * @todo implement Data_Order and response conversion to this object
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processCheckoutDetails($request)
    {
        $this->_queryParams['METHOD']   = self::TRXTYPE_GET_EXPRESS_CHECKOUT;
        $this->_queryParams['TOKEN']    = $request->getOptions()->get('TOKEN');
        return $this->_sendRequest();
    }

    /**
     * Completes the Express Checkout transaction, including the actual total amount of the order.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processAuth($request)
    {
        $this->_initQueryOrder($request->getOrder());
        $this->_initQueryDoCheckoutInfo($request);
        $this->_queryParams['METHOD']   = self::TRXTYPE_DO_EXPRESS_CHECKOUT;
        $this->_queryParams['PAYMENTREQUEST_0_PAYMENTACTION'] = self::PAYMENT_ACTION_AUTH;
        $this->_queryParams['RETURNFMFDETAILS'] = 1;
        return $this->_sendRequest();
    }

    /**
     * Completes the Express Checkout transaction, including the actual total amount of the order.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processSale($request)
    {
        
    	$this->_initQueryOrder($request->getOrder());
        $this->_initQueryDoCheckoutInfo($request);
        $this->_queryParams['METHOD']   = self::TRXTYPE_DO_EXPRESS_CHECKOUT;
        $this->_queryParams['PAYMENTREQUEST_0_PAYMENTACTION']    = self::PAYMENT_ACTION_SALE;
        $this->_queryParams['RETURNFMFDETAILS'] = 1;
        return $this->_sendRequest();
    }

    /**
     * Initialize order details
     *
     * @param Socialstore_Payment_Order_Interface $order
     * @param integer                    $index
     */
    protected function _initQueryOrder($order, $index=0)
    {
        $prefix       = 'PAYMENTREQUEST_'.$index.'_';

        $this->_queryParams[$prefix.'AMT']          = $order->getTotalAmount();
        $this->_queryParams[$prefix.'CURRENCYCODE'] = $order->getCurrency();
        $this->_queryParams[$prefix.'INVNUM']       = $order->getId();
		$this->_queryParams[$prefix.'NOSHIPPING'] =  '1';
	
        $billing    = $order->getBillingAddress();
        $shipping   = $order->getShippingAddress();
        /*
        if ($billing && $billing->getEmail()) {
            $this->_queryParams['EMAIL']        = $billing->getEmail();
        }
		
       if ($shipping) {
            $this->_queryParams[$prefix.'SHIPTONAME']       = $shipping->getFullName();
            $this->_queryParams[$prefix.'SHIPTOSTREET']     = $shipping->getStreet();
            $this->_queryParams[$prefix.'SHIPTOSTREET2']    = $shipping->getStreet2();
            $this->_queryParams[$prefix.'SHIPTOCITY']       = $shipping->getCity();
            $this->_queryParams[$prefix.'SHIPTOSTATE']      = $shipping->getRegion();
            $this->_queryParams[$prefix.'SHIPTOCOUNTRYCODE']= $shipping->getCountry();
            $this->_queryParams[$prefix.'SHIPTOZIP']        = $shipping->getPostCode();
            $this->_queryParams[$prefix.'SHIPTOPHONENUM']   = $shipping->getPhone();
        }
		 */
        if ($order->getItems()) {
            $this->_queryParams[$prefix.'SHIPPINGAMT']  = $order->getShippingAmount();
            $this->_queryParams[$prefix.'HANDLINGAMT']  = $order->getHandlingAmount();
            $this->_queryParams[$prefix.'TAXAMT']       = $order->getTaxAmount();
            $this->_initQueryOrderItems($order->getItems(), $index, $order);
        }
        if ($order->getOptions()) {
            $this->_initQueryOrderOptions($order->getOptions(), $index);
        }
    }

    /**
     * Initialize line items
     *
     * @param array   $items
     * @param integer $index
	 * @param Socialstore_Payment_Order_Interface
     */
    protected function _initQueryOrderItems($items, $index, $order)
    {
        $prefix = 'L_PAYMENTREQUEST_'.$index.'_';
        $lineIndex = 0;
        $itemsAmount = 0;
        foreach ($items as $item) 
        {
            $this->_queryParams[$prefix.'NAME'.$lineIndex]    = $item->getName();
            $this->_queryParams[$prefix.'DESC'.$lineIndex]    = $item->getDescription();
            $this->_queryParams[$prefix.'AMT'.$lineIndex]     = $item->getPreTaxPrice();
            $this->_queryParams[$prefix.'NUMBER'.$lineIndex]  = $item->getId();
            $this->_queryParams[$prefix.'QTY'.$lineIndex]     = $item->getQty();
            $this->_queryParams[$prefix.'TAXAMT'.$lineIndex]  = $item->getItemTaxAmount();

            $this->_queryParams[$prefix.'ITEMWEIGHTVALUE'.$lineIndex]    = $item->getWeight();
            $this->_queryParams[$prefix.'ITEMLENGTHVALUE'.$lineIndex]    = $item->getLength();
            $this->_queryParams[$prefix.'ITEMWIDTHVALUE'.$lineIndex]     = $item->getWidth();
            $this->_queryParams[$prefix.'ITEMHEIGHTVALUE'.$lineIndex]    = $item->getHeight();

            $this->_queryParams[$prefix.'ITEMURL'.$lineIndex]            = $item->getUrl();
            $itemsAmount+= $item->getSubAmount();
            $lineIndex++;
        }	
        $this->_queryParams['PAYMENTREQUEST_'.$index.'_ITEMAMT']  = $order->getSubAmount();
    }

    /**
     * Initialize order options
     *
     * @param Socialstore_Payment_Options $options
     * @param integer                      $index
     */
    protected function _initQueryOrderOptions($options, $index)
    {
        $prefix       = 'PAYMENTREQUEST_'.$index.'_';
        $map = array(
            'description'       => $prefix.'DESC',
            'notify_url'        => $prefix.'NOTIFYURL',
            'note'              => $prefix.'NOTETEXT',
            'seller_id'         => $prefix.'SELLERID',
            'paypal_seller_id'  => $prefix.'SELLERPAYPALACCOUNTID',
            'no_shipping'       => $prefix.'NOSHIPPING',
        );
        $additional = $options->map($map);
        $this->_queryParams = array_merge($this->_queryParams, $additional);
    }

    /**
     * Initialize SetExpressCheckout parameters from info object
     *
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_SetExpressCheckout
     * @param Socialstore_Payment_Request $request
     */
    protected function _initQuerySetCheckoutInfo($request)
    {
        $options = $request->getOptions();
        $map = array(
            'return_url'    => 'RETURNURL',
            'cancel_url'    => 'CANCELURL',
            'notify_url'    => 'NOTIFYURL',
            'surveyquestion'=> 'SURVEYQUESTION',
            'surveyenable'  => 'SURVEYENABLE',
            'customer_id'   => 'CUSTOMERSERVICENUMBER',
        );
        $additional = $options->map($map);
        $survey  =$options->get('surveychoice');
        if ($survey) {
            $survey = is_array($survey) ? $survey : array($survey);
            $index = 0;
            foreach ($survey as $choice) {
                $additional['L_SURVEYCHOICE'.$index] = $choice;
                $index++;

            }
        }
        $this->_queryParams = array_merge($this->_queryParams, $additional);
    }

    /**
     * Initialize DoExpressCheckout parameters from info object
     *
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoExpressCheckoutPayment
     * @param Socialstore_Payment_Request $request
     */
    protected function _initQueryDoCheckoutInfo($request)
    {
        $map = array(
            'token'             => 'TOKEN',
            'payer_id'          => 'PAYERID',
            'giftmessage'       => 'GIFTMESSAGE',
            'giftreceiptenable' => 'GIFTRECEIPTENABLE',
            'giftwrapname'      => 'GIFTWRAPNAME',
            'giftwrapamount'    => 'GIFTWRAPAMOUNT',
            'notify_url'        => 'NOTIFYURL',
            'surveyquestion'    => 'SURVEYQUESTION',
            'surveychoiceselected' => 'SURVEYCHOICESELECTED',
        );
        $options = $request->getOptions();
        $additional = $options->map($map);
        $this->_queryParams = array_merge($this->_queryParams, $additional);
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
        $responseMap = array_merge($responseMap, array(
            'PAYMENTINFO_0_AMT'             => 'amount',
            'PAYMENTINFO_0_CURRENCYCODE'    => 'currency',
            'PAYMENTINFO_0_FEEAMT'			=> 'gateway_fee',
            'PAYMENTINFO_0_TRANSACTIONID'   => 'transaction_id',
            'PAYMENTINFO_0_TRANSACTIONTYPE' => 'transaction_type',
            'PAYMENTINFO_0_PAYMENTSTATUS'   => 'payment_status',
            'PAYMENTINFO_0_PAYMENTTYPE'     => 'payment_type',
            'PAYMENTINFO_0_ORDERTIME'       => 'order_time',
            'TOKEN'                         => 'gateway_token',
            'PAYMENTINFO_0_PENDINGREASON'   => 'pending_reason',
            'PAYMENTINFO_0_ERRORCODE'       => 'error_code',
        ));
        return parent::_prepareResponse($response, $responseMap);
    }
}
