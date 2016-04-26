<?php

class Socialstore_PaymentPaypalController extends Core_Controller_Action_Standard {
	
	/**
	 * @return Zend_Log
	 */
	public function getLog($filename='store.notify.log'){
		$writer =  new Zend_Log_Writer_Stream(APPLICATION_PATH .'/temporary/log/'.$filename);
		return new Zend_Log($writer);
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
		$order =  Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		
		if(!is_object($order)){
			$this->_forward('order-notfound');
			return false;
		}
		$gateway_id = 'paypal';
		$gateway = Engine_Api::_() -> getDbtable('gateways','Socialstore') -> find($gateway_id) -> current();
		if(!is_string($order->getPaytype()) || (!$gateway->getConfig())){
			$this->_forward('paytype-notfound');
			return false;
		}
		
		// load paytype object.
		
		return true;
	}
	
	public function processInitAction(){
		if(!$this->_isValidProcess()){
			return ;
		}
		
		// check valid gateway has posted to.
		$order_id   = $this->_getParam('id');
		$order   =  Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		
		$gateway  = 'paypal';
				
		$request =  new Socialstore_Payment_Request('init');
		
		$router =  $this->getFrontController()->getRouter();
		$return_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-paypal','action'=>'review','id'=>$order_id,'gateway'=>$gateway),'default',true);
		$notify_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-paypal','action'=>'notify','id'=>$order_id,'gateway'=>$gateway),'default',true);
		$cancel_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-paypal','action'=>'cancel','id'=>$order_id,'gateway'=>$gateway),'default',true);
		
		$options =  array(
			'return_url'=>$this->getBaseUrl().  $return_url,
			'notify_url'=>$this->getBaseUrl().  $notify_url,
			'cancel_url'=>$this->getBaseUrl().  $cancel_url,
			'no_shipping'=>'1',
		);
		
		$request->setOrder($order);
		$request->setOptions($options);
		$payment =  new Socialstore_Payment(array('gateway'=>$gateway));
		$response =  $payment->process($request);
		
		
		if($response->isSuccess()){
			$url =  $response->getOption('redirect_url');
			if($url){
				return $this->_redirect($url);
			}
		}
		
		$this->view->response = $response;
		return $this->_forward('process-error');
	}
	
	public function processSaleAction(){
		if(!$this->_isValidProcess()){
			return ;
		}
		
		// check valid gateway has posted to.
		$order_id   = $this->_getParam('id');
		$order   =  Socialstore_Model_DbTable_Orders::getByOrderId($order_id);

		$gateway = 'paypal';
		$payment =  new Socialstore_Payment(array('gateway'=>$gateway));
		$request =  new Socialstore_Payment_Request('capture');
		
		
		$router =  $this->getFrontController()->getRouter();
		$return_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-review','action'=>'review','id'=>$order_id,'gateway'=>$gateway),'default',true);
		$notify_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-paypal','action'=>'notify','id'=>$order_id,'gateway'=>$gateway),'default',true);
		$cancel_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-paypal','action'=>'cancel','id'=>$order_id,'gateway'=>$gateway),'default',true);
		
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
		
		//return $this->_forward('process-error');
	}
	
	public function processAction() {
		$this->_forward('process-init');
		
	}
	
	public function reviewAction(){
		if(!$this->_isValidProcess()){
			return ;
		}
		
		Zend_Registry::set('active_menu', 'socialstore_main_mycart');
		Zend_Registry::set('PAYMENTMENU_ACTIVE','payment-confirm');
		$form = $this->view->form =  new Socialstore_Form_Payment_Review;		
		// get result from review action
		$token =  $this->_getParam('token');
		$payer_id =  $this->_getParam('PayerID');
		$order_id =  $this->_getParam('id');
		$gateway =  'paypal';
		$this->view->id = $order_id;
		$order = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		$this->view->order = $order;
		$Packages = new Socialstore_Model_DbTable_ShippingPackages;
		$Items = new Socialstore_Model_DbTable_OrderItems;
		$ShippingAddresses = new Socialstore_Model_DbTable_ShippingAddresses;
		$packages = $Packages->getPackagesByOrder($order_id);
		$temp_packages = $packages->toArray();
		$order_packages = array();
		
		foreach ($temp_packages as $package) {
			$shipping_packages = array();
			$shipping_packages['shippingaddress_id'] = $ShippingAddresses->getShippingAddressString($package['shippingaddress_id']);
			$product_array = array();
			$orderitems = $Items->getPackageItems($package,$order_id);
			foreach ($orderitems as $orderitem) {
				$product_array[$orderitem->orderitem_id] = array(
															'product_id' => $orderitem->object_id,
															'quantity' => $orderitem->quantity,
															'total_amount' => $orderitem->total_amount,
															'shipping_amount' => round($orderitem->shipping_amount + $orderitem->handling_amount,2)
														);
			}
			$shipping_packages['products'] = $product_array;
			$shipping_packages['shipping_cost'] = round($package['shipping_cost'] + $package['handling_cost'],2);
			$order_packages[] = $shipping_packages;
		}
		$this->view->packages = $order_packages;
		$baseUrl =  $this->getBaseUrl();
		$allParams = $this->_getAllParams();

		if($this->_request->isGet()){
			return ;
		}

		if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
			
			// get the payment
			$payment= new Socialstore_Payment(array('gateway'=>$gateway));
			
			// set request order
			$request = new Socialstore_Payment_Request('sale');
			$order   =  Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
			$request->setOrder($order);
			
			// get notify url
			$router =  $this->getFrontController()->getRouter();
			$notify_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-paypal','action'=>'notify','id'=>$order_id),'default',true);
			
			// check request option
			$options =  array(
				'token'=>$token,
				'payer_id'=> $payer_id,
				'notify_url'=>$baseUrl . $notify_url,
			);
			$request->setOptions($options);
		
			// process plugin
			$plugin =  $order->getPlugin();			
			try{
				// process request.
				$response =  $payment->process($request);
				// log response result.
				//$response_options = $response->getOptions();
				//$this->getLog('store.response.log')->log(var_export($response_options,true), Zend_Log::DEBUG);
				
				/**
				 * add transaction
				 */
			 	Socialstore_Api_Transaction::getInstance()->addTransaction($allParams, $order->toArray(), $response->getOptions()->toArray());
				// get payment status
				$status =  $response->getOption('payment_status');
				$status =  strtolower($status);
				
				// cucess result
				if($response->isSuccess()){
					// process plugin.
					if($status == 'pending'){
						$plugin->onPending();
						return $this->_forward('process-pending');	
					}else if($status == 'completed'){
						$plugin->onSuccess();
					}else if($status == 'cancel'){
						$plugin->onCancel();
					}else{
						$plugin->onFailure();
					}		
					return $this->_forward('process-success');	
				}
				else
				{
					// failture procss
					$plugin->onFailure();
					
					// foward to process error.
					$this->view->response =  $response;		
					$this->_forward('process-error');
				}
				
				/**
				 * clean current session
				 */
				$cart  = Socialstore_Api_Cart::getInstance()->flushCurrentOrder();
				
			}catch(Exception $e){
				$this->getLog('store.error.log')->log($e->getMessage(), Zend_Log::ERR);
				// foward to process error.
				$this->view->response =  $response;		
				$this->_forward('process-error');	
			}
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
	
	protected function _getCheckoutDetails(){
		$gateway = 'paypal';
		
		$payment = Socialstore_Payment::factory(array('gateway'=>$gateway));
		
		$request = new Socialstore_Payment_Request('CheckoutDetails');
		
		// set request token
		$request->setOptions(array('token'=>$this->_getParam('token')));
		
		$response = $payment->process($token);
		
		//'amount','currency',''
		
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
		$order_id =  $this->_getParam('id');
		$this->view->order =   $order   =  Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		
	}
	
	public function processPendingAction(){
		$order_id =  $this->_getParam('id');
		$this->view->order =   $order   =  Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		
	}
	
}
