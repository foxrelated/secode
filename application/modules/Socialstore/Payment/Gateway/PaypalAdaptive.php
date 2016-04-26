<?php
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(__FILE__))))));

include_once  APPLICATION_PATH . '/application/modules/Socialstore/externals/scripts/adaptivelib/AdaptivePayments.php';

class Socialstore_Payment_Gateway_PaypalAdaptive extends Socialstore_Payment_Gateway_Abstract
{
    const ACTION_CHECKOUT_DETAILS   = 'checkout_details';
	const X_PAYPAL_SERVICE_VERSION    = '1.8.0';
    const SANDBOX_CHECKOUT_URL    = 'https://svcs.sandbox.paypal.com/AdaptivePayments';
    const LIVE_CHECKOUT_URL  = 'https://svcs.paypal.com/AdaptivePayments';

    const TRXTYPE_SET_EXPRESS_CHECKOUT  = 'SetExpressCheckout';
    const TRXTYPE_GET_EXPRESS_CHECKOUT  = 'GetExpressCheckoutDetails';
    const TRXTYPE_DO_EXPRESS_CHECKOUT   = 'DoExpressCheckoutPayment';
    const TRXTYPE_DO_AUTHORIZATION      = 'DoAuthorization'; /* for payment action order only */
    const TRXTYPE_MASSPAY =  'MassPay';
	
	const RECEIVETYPE_EMAIL = 'EmailAddress';
	
	protected $_credentialKeys = array(
        'user' => 'X-PAYPAL-SECURITY-USERID',
        'password'  => 'X-PAYPAL-SECURITY-PASSWORD',
        'signature' => 'X-PAYPAL-SECURITY-SIGNATURE',
		'appid' => 'X-PAYPAL-APPLICATION-ID',
		'account_username' => 'account_username'
    );

    /**
     * Prepare list of requirements
     *
     * @return Socialstore_Payment_Request_Requirements
     */
    
	public function getLog($filename='store.notify.log'){
		$writer =  new Zend_Log_Writer_Stream(APPLICATION_PATH .'/temporary/log/'.$filename);
		return new Zend_Log($writer);
	}
    
    public function getUrl()
    {
        return $this->isSandboxMode() ? self::SANDBOX_CHECKOUT_URL : self::LIVE_CHECKOUT_URL;
    }
    
    protected function _initQuery ($request)
    {
        parent::_initQuery($request);
        $this->_queryParams['X-PAYPAL-SERVICE-VERSION']  = self::X_PAYPAL_SERVICE_VERSION;
        $this->_queryParams['X-PAYPAL-REQUEST-DATA-FORMAT'] = 'NV';
        $this->_queryParams['X-PAYPAL-RESPONSE-DATA-FORMAT'] = 'NV';
        return $this;
    }
    
    
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
    	/*$this->_queryParams['actionType']   = 'PAY';
        $this->_initQueryOrder($request->getOrder());
        $this->_initQuerySetCheckoutInfo($request);
        $response = $this->_sendRequest(array('TOKEN' => 'token'));
		Zend_Registry::get('Zend_Log')->log(print_r($response,true), 7);
        $sandboxMode = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.mode', '1');
        $url = $sandboxMode ? self::SANDBOX_CHECKOUT_URL : self::LIVE_CHECKOUT_URL;
        $url.=$response->getOption('TOKEN');
        $response->getOptions()->set('redirect_url', $url);
        Zend_Registry::get('Zend_Log')->log(print_r($response,true), 7);
        return $response;*/
    	try {
			
		        /* The servername and serverport tells PayPal where the buyer
		           should be directed back to after authorizing payment.
		           In this case, its the local webserver that is running this script
		           Using the servername and serverport, the return URL is the first
		           portion of the URL that buyers will return to after authorizing payment                */
			$order = $request->getOrder();
			$this->_initQueryOrder($order);

			$this->_initQuerySetCheckoutInfo($request);
    		
			$payRequest = new PayRequest();
			$payRequest->actionType = "PAY";
			$payRequest->cancelUrl = $this->_queryParams['CANCELURL'];
			$payRequest->returnUrl = $this->_queryParams['RETURNURL'];
			$payRequest->ipnNotificationUrl = $this->_queryParams['NOTIFYURL'];
			$payRequest->apiPassword = $this->_queryParams['X-PAYPAL-SECURITY-PASSWORD'];
			$payRequest->apiUsername = $this->_queryParams['X-PAYPAL-SECURITY-USERID'];
			$payRequest->apiSignature = $this->_queryParams['X-PAYPAL-SECURITY-SIGNATURE'];
			$payRequest->clientDetails = new ClientDetailsType();
			$payRequest->clientDetails->applicationId =$this->_queryParams['X-PAYPAL-APPLICATION-ID'];
			//$payRequest->clientDetails->deviceId = '127001';
			//$payRequest->clientDetails->ipAddress = '127.0.0.1';
			$payRequest->currencyCode = $this->_queryParams['PAYMENTREQUEST_0_CURRENCYCODE'];
			//$payRequest->senderEmail = 'pbehar_1227728571_biz@paypal.com';
			$payRequest->requestEnvelope = new RequestEnvelope();
			$payRequest->requestEnvelope->errorLanguage = 'en_US';
			$receivers = $this->_queryParams['receivers'];
			$receive = array();
			if ($order->paytype_id == 'shopping-cart') {
				foreach ($receivers as $re) {
					$receiver = new receiver();
					$receiver->email = $re['email'];
					$receiver->amount = $re['amount'];
					$receiver->primary = false;
					$receive[] = $receiver;
				}			
				$admin_receiver = new receiver();
				$admin_receiver->email = $this->_queryParams['account_username'];
				$admin_receiver->amount = $order->total_amount;
				$admin_receiver->primary = true;	
				$receive[] = $admin_receiver;
			}
			else {
				$admin_receiver = new receiver();
				$admin_receiver->email = $this->_queryParams['account_username'];
				$admin_receiver->amount = $order->total_amount;
				$receive[] = $admin_receiver;
			}
			$payRequest->receiverList = $receive;
		
			// Create service wrapper object
			$ap = new AdaptivePayments();
			
			// invoke business method on service wrapper passing in appropriate request params
			$response = $ap->Pay($payRequest);
		
			// Check response
			if(strtoupper($ap->isSuccess) == 'FAILURE')
			{
				$soapFault = $ap->getLastError();
				return $soapFault;
			} else {
				return $response;
				/*$token = $response->payKey;
				echo "Transaction Successful! PayKey is $token \n";*/
			}
		}
    	catch(Exception $e){
				$fault = new FaultMessage();
				$errorData = new ErrorData();
				$errorData->errorId = $e->getFile() ;
  				$errorData->message = $e->getMessage();
		  		$fault->error = $errorData;
				return $fault;
		}
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
        
        /*if ($billing && $billing->getEmail()) {
            $this->_queryParams['EMAIL']        = $billing->getEmail();
        }*/
       /* if ($shipping) {
            $this->_queryParams[$prefix.'SHIPTONAME']       = $shipping->getFullName();
            $this->_queryParams[$prefix.'SHIPTOSTREET']     = $shipping->getStreet();
            $this->_queryParams[$prefix.'SHIPTOSTREET2']    = $shipping->getStreet2();
            $this->_queryParams[$prefix.'SHIPTOCITY']       = $shipping->getCity();
            $this->_queryParams[$prefix.'SHIPTOSTATE']      = $shipping->getRegion();
            $this->_queryParams[$prefix.'SHIPTOCOUNTRYCODE']= $shipping->getCountry();
            $this->_queryParams[$prefix.'SHIPTOZIP']        = $shipping->getPostCode();
            $this->_queryParams[$prefix.'SHIPTOPHONENUM']   = $shipping->getPhone();
        }*/
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
        $receivers = array();
        if ($order->paytype_id == 'shopping-cart') {
	        foreach ($items as $item) {
	        	$store = $item->getStore();
	        	$account = $store->getAccount();
	        	$email = $account->account_username;
	        	if ($receivers == null) {
	        		$temp = array();
	        		$temp['email'] = $email;
	        		$temp['amount'] = $item->getSellerAmount();
	        		$receivers[$store->store_id] = $temp;
	        	}
	        	else {
	        		if (array_key_exists($item->store_id, $receivers)) {
	        			$receivers[$item->store_id]['amount'] += $item->getSellerAmount();
	        		}
	        		else {
	        			$temp = array();
		        		$temp['email'] = $email;
		        		$temp['amount'] = $item->getSellerAmount();
	       				$receivers[$item->store_id] = $temp;
	       			}
	        	}
	        }
	        $ShippingPackages = new Socialstore_Model_DbTable_ShippingPackages;
        	foreach ($receivers as $store_id => &$receiver) {
        		$shipping = $ShippingPackages->getShippingByStore($order->order_id, $store_id);
        		$handling = $ShippingPackages->getHandlingByStore($order->order_id, $store_id);
        		$amount_temp = $receiver['amount'];
        		$receiver['amount'] = round($amount_temp + $shipping + $handling,2);
        	}
        }
//        Zend_Registry::get('Zend_Log')->log(print_r($receivers,true), 7);
        $this->_queryParams['receivers'] = $receivers;
        	
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
