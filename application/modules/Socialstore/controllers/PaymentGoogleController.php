<?php
class Socialstore_PaymentGoogleController extends Core_Controller_Action_Standard 
{
	/**
	 * @return Zend_Log
	 */
	public function getLog($filename='store.notify.log'){
		$writer =  new Zend_Log_Writer_Stream(APPLICATION_PATH .'/temporary/log/'.$filename);
		return new Zend_Log($writer);
	}
	public function process_new_order_notification($response) {
		$transaction_id = $response['new-order-notification']['google-order-number']['VALUE'];
		$params = array();
		$params = $response['new-order-notification']['shopping-cart']['merchant-private-data'];
		  foreach ($params as $key => $value) {
				$params[$key] = $value['VALUE'];
		  }
		$params['transaction_id'] = $transaction_id;
		$params['amount'] = $params['total_amount'];
		$params['gateway'] = 'google';
		Socialstore_Api_Transaction::getInstance()->addTransaction($params);
	//getLog('store.response.log')->log(print_r($response,true), Zend_Log::DEBUG);

	/*$select = $Trans->select()->from($Trans->info('name'));
	$google_order = $db->Execute("select orders_id ".
                                " from " . $googlepayment->table_order . " " .
                                " where google_order_number = " . 
                                $data[$root]['google-order-number']['VALUE'] );
    if($google_order->RecordCount() != 0) {
//  Order already processed, send ACK http 200 to avoid notification resend
    	$Gresponse->log->logError(sprintf(GOOGLECHECKOUT_ERR_DUPLICATED_ORDER,
                                   $data[$root]['google-order-number']['VALUE'],
                                   $google_order->fields['orders_id']));
        $Gresponse->SendAck(); 
      }*/
	}
	public function process_order_state_change_notification($response) {
		$new_financial_state = $response['order-state-change-notification']['new-financial-order-state']['VALUE'];
	  	$new_fulfillment_order = $response['order-state-change-notification']['new-fulfillment-order-state']['VALUE'];
	  	$previous_financial_state = $response['order-state-change-notification']['previous-financial-order-state']['VALUE'];
	  	$previous_fulfillment_order = $response['order-state-change-notification']['previous-fulfillment-order-state']['VALUE'];
		$this->getLog('store.response.log')->log(print_r($previous_financial_state,true), Zend_Log::DEBUG);
		$this->getLog('store.response.log')->log(print_r($new_financial_state,true), Zend_Log::DEBUG);
	  	if ($previous_financial_state != $new_financial_state) {
		    switch ($new_financial_state) {
		      case 'REVIEWING' :
		        {
		        	break;
		        }
		      case 'CHARGEABLE' :
		        {
		        	break;
		        }
		      case 'CHARGING' :
		        {
		        	break;
		        }
		      case 'CHARGED' :
		        {
		        	$transaction_id = $response['order-state-change-notification']['google-order-number']['VALUE'];
		    		$transaction =  Socialstore_Model_DbTable_PayTrans::getByTransId($transaction_id,'google');
					$order = Socialstore_Model_DbTable_Orders::getByOrderId($transaction->order_id);
					$plugin = $order->getPlugin();
		        	$transaction -> payment_status = 'Completed';
		        	$transaction -> save();
					$plugin->onSuccess();
		        	break;
		        }
		
		      case 'PAYMENT-DECLINED' :
		        {
		        	break;
		        }
		      case 'CANCELLED' :
		        {
		        	$plugin->onCancel();
		        	break;
		        }
		      case 'CANCELLED_BY_GOOGLE' :
		        {
		        	$plugin->onCancel();
		        	break;
		        }
		      default :
		      	    break;
		    }
		 }  
			
	}
		

	public function indexAction() {
		$Orders = new Socialstore_Model_DbTable_Orders;
		$order  = $Orders->fetchNew();
		$order->save();
	}

	public function gatewayAction() {
		$form =  $this->view->form = new Socialstore_Form_Payment_Gateway;
	}
	
	protected function getBaseUrl(){
		$baseUrl= Engine_Api::_()->getApi('settings','core')->getSetting('store.baseUrl',null);
		if(APPLICATION_ENV =='development'){
			$request =  Zend_Controller_Front::getInstance()->getRequest();
			$baseUrl = sprintf('%s://%s', $request->getScheme(), $request->getHttpHost());
			Engine_Api::_()->getApi('settings','core')->setSetting('store.baseUrl',$baseUrl);
		}
		return $baseUrl;
	}
	protected function _isValidProcess(){
		$order_id =  $this->_getParam('id');
		$order = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		Zend_Registry::set('order', $order_id);
		if(!is_object($order)){
			$this->_forward('order-notfound');
			return false;
		}
		
		if(!is_string($order->getPaytype())){
			$this->_forward('paytype-notfound');
			return false;
		}

		$gateway =  $this->_getParam('gateway','google');
		if(!$gateway){
			$this->_forward('gateway');
			return false;
		}
		
		
		// load paytype object.
		
		return true;
	}
	
	public function processSaleAction(){
		if(!$this->_isValidProcess()){
			return ;
		}
		
		// check valid gateway has posted to.
		$order_id   = $this->_getParam('id');
		$order   = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		$gateway = $this->_getParam('gateway',null);
		$payment =  new Socialstore_Payment(array('gateway'=>$gateway));
		$request =  new Socialstore_Payment_Request('capture');
		
		
		$router =  $this->getFrontController()->getRouter();
		$return_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment','action'=>'review','id'=>$order_id,'gateway'=>$gateway),'default',true);
		$notify_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment','action'=>'notify','id'=>$order_id,'gateway'=>$gateway),'default',true);
		$cancel_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment','action'=>'cancel','id'=>$order_id,'gateway'=>$gateway),'default',true);
		
		$options =  array(
			'return_url'=>$this->getBaseUrl().  $return_url,
			'notify_url'=>$this->getBaseUrl().  $notify_url,
			'cancel_url'=>$this->getBaseUrl().  $cancel_url,
		);
		$request->setOrder($order);
		$request->setOptions($options);
		$response =  $payment->process($request);
		if($response->isSuccess()){
			$url =  $response->getOption('redirect_url');
			if($url){
				return $this->_redirect($url);
			}
		}
		$this->view->response = $response;
		var_dump($response);
		//return $this->_forward('process-error');
	}
	
	public function processAction() {
		Zend_Registry::set('active_menu', 'socialstore_main_mycart');
		Zend_Registry::set('PAYMENTMENU_ACTIVE','payment-confirm');
		$this->view->transactionData = $data = array();
		// check valid gateway has posted to.
		$gateway = $this->_getParam('gateway','google');
		$payment =  new Socialstore_Payment(array('gateway'=>$gateway));
		$request =  new Socialstore_Payment_Request('sale');
		$order_id   = $this->_getParam('id');
		$order   = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		// check payment method

		
		
		$router =  $this->getFrontController()->getRouter();
		$return_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-google', 'action=>process-success'),'default',true);
		$notify_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-google','action'=>'notify','id'=>$order_id,'gateway'=>$gateway),'default',true);
		$cancel_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-google','action'=>'cancel','id'=>$order_id,'gateway'=>$gateway),'default',true);
		
		$options =  array(
			'return_url'=>$this->getBaseUrl().  $return_url,
			'notify_url'=>$this->getBaseUrl().  $notify_url,
			'cancel_url'=>$this->getBaseUrl().  $cancel_url,
		);
		$request->setOrder($order);
		$request->setOptions($options);
		$response =  $payment->process($request);
		//$this->view->cart = $response;

		
		/*if ($response->getStatus() == 'approved') {
			$allParams = $this->_getAllParams();
			$plugin =  $order->getPlugin();
			
	
			Socialstore_Api_Transaction::getInstance()->addTransaction($allParams, $order->toArray(), $response->getOptions()->toArray());
			
			$plugin->onSuccess();
			return $this->_forward('process-success');
		}
		else {
			$this->view->response = $response;
			return $this->_forward('process-error');
		}*/
	}
	
	public function reviewAction(){
		
		require_once APPLICATION_PATH . '/application/modules/Socialstore/externals/scripts/library/googlemerchantcalculations.php';
		require_once APPLICATION_PATH . '/application/modules/Socialstore/externals/scripts/library/googleresult.php';
		require_once APPLICATION_PATH . '/application/modules/Socialstore/externals/scripts/library/googlerequest.php';
		require_once APPLICATION_PATH . '/application/modules/Socialstore/externals/scripts/library/googleresponse.php';
		try {
		$Gresponse = new GoogleResponse();
		
		$xml_response = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : file_get_contents("php://input");
		if (get_magic_quotes_gpc()) {
		    $xml_response = stripslashes($xml_response);
		}
		list ($root, $data) = $Gresponse->GetParsedXML($xml_response);
		$response = $data;
		
		switch ($root) {
		  case "request-received": {
		      break;
		    }
		  case "error": {
		      break;
		    }
		  case "diagnosis": {
		      break;
		    }
		  case "checkout-redirect": {
		      //process_checkout_redirect($Gresponse);
		      break;
		    }
		  case "merchant-calculation-callback" :
		    {
		        break;
		    }
		  case "merchant-calculation-callback-single" :
		    {
		      //process_merchant_calculation_callback_single($Gresponse);
	 	       break;
		    }
		  case "new-order-notification" :
		    {
		      	$this->process_new_order_notification($response);
		 		break;
		    }
		  case "order-state-change-notification": {
		  	$this->getLog('store.response.log')->log(print_r($response,true), Zend_Log::DEBUG);
		      $this->process_order_state_change_notification($response);
		      break;
		    }
		  case "charge-amount-notification": {
		      //process_charge_amount_notification($response);
		      break;
		    }
		  case "chargeback-amount-notification": {
		     // process_chargeback_amount_notification($Gresponse);
		      break;
		    }
		  case "refund-amount-notification": {
		  //process_refund_amount_notification($Gresponse, $googlepayment);
		      break;
		    }
		  case "risk-information-notification": {
		      //process_risk_information_notification($response);
		      break;
	    	}
  			default: {
      			//$Gresponse->SendBadRequestStatus("Invalid or not supported Message");
      			break;
    		}
		}
		$cart  = Socialstore_Api_Cart::getInstance()->flushCurrentOrder();
	}catch(Exception $e){
				$this->getLog('store.error.log')->log($e->getMessage(), Zend_Log::ERR);
				// foward to process error.
				$this->_forward('process-error');	
			}
	}
	
	public function acceptAction(){
		$params =  $this->getRequest()->getParams();
		$log_message =  var_export($params, true);
		$this->getLog()->log($log_message, Zend_Log::DEBUG);
	}
	
	public function cancelAction(){
		$params =  $this->getRequest()->getParams();
		$log_message =  var_export($params, true);
		
		$this->getLog()->log($log_message, Zend_Log::DEBUG);
	}

	public function notifyAction(){
		
		$params =  $this->getRequest()->getParams();
		$log_message =  var_export($params, true);
		
		$this->getLog()->log($log_message, Zend_Log::DEBUG);
	}
	
	public function orderNotfoundAction(){
		#invalid order id.
	}
	
	public function paytypeNotfoundAction(){
		#invalid order id.
	}

	public function processErrorAction(){
		$order_id =  $this->_getParam('id');
		$this->view->order =   $order   =  Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		
	}
	
	public function processSuccessAction(){
		$cart  = Socialstore_Api_Cart::getInstance()->flushCurrentOrder();
		Socialstore_Api_Cart::getInstance()->removeCarts();
		$order_id =  $this->_getParam('id');
		$this->view->order =   $order   =  Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		
	}
	
}