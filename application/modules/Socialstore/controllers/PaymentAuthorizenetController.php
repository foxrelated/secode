<?php

class Socialstore_PaymentAuthorizenetController extends Core_Controller_Action_Standard {
	
	/**
	 * @return Zend_Log
	 */
	public function getLog($filename='store.notify.log'){
		$writer =  new Zend_Log_Writer_Stream(APPLICATION_PATH .'/temporary/log/store.notify.log');
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

		$gateway =  $this->_getParam('gateway','authorizenet');
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
		$gateway = $this->_getParam('gateway','authorizenet');
		$payment =  new Socialstore_Payment(array('gateway'=>$gateway));
		$request =  new Socialstore_Payment_Request('sale');
		$order_id   = $this->_getParam('id');
		$order   = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		// check payment method

		
		$form = $this->view->form = new Socialstore_Form_Payment_Credit();
		
		if(!$this -> getRequest() -> isPost()) {
			return ;
		}

		$post = $this -> getRequest() -> getPost();

		if(!$form -> isValid($post)) {
			return ;
		}
		
		$values = $form->getValues();
		if (($values['credit_type'] != 'AE' && strlen($values['credit_number']) != 16) ||($values['credit_type'] == 'AE' && strlen($values['credit_number']) != 15) ) {
			return $form->getElement('credit_number')->addError('Credit Card Number is invalid!');
		}
		
		if (strlen($values['credit_expire']) != 5 && substr($values['credit_expire'], 2, 1) != '-') {
			return $form->getElement('credit_expire')->addError('Credit Card Expiration Date is invalid!');
		}
		else {
			$expire = $values['credit_expire'];
			(int)$month = substr($expire, 0, 2);
			(int)$year = substr($expire, 3, 2);
			if (($month > 12)) {
				return $form->getElement('credit_expire')->addError('Credit Card Expiration Date is invalid!');
			}
		}
		//$method = new Socialstore_Payment_Method_Card('AE', '370000000000002',12,12);
		$method = new Socialstore_Payment_Method_Card($values['credit_type'],$values['credit_number'],$month,$year);
		
		$request->setMethod($method);
		
		
		$router =  $this->getFrontController()->getRouter();
		$return_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-authorizenet','action'=>'review','id'=>$order_id,'gateway'=>$gateway),'default',true);
		$notify_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-authorizenet','action'=>'notify','id'=>$order_id,'gateway'=>$gateway),'default',true);
		$cancel_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-authorizenet','action'=>'cancel','id'=>$order_id,'gateway'=>$gateway),'default',true);
		
		$options =  array(
			'return_url'=>$this->getBaseUrl().  $return_url,
			'notify_url'=>$this->getBaseUrl().  $notify_url,
			'cancel_url'=>$this->getBaseUrl().  $cancel_url,
		);
		
		$request->setOrder($order);
		$request->setOptions($options);
		$response =  $payment->process($request);
		if ($response->getStatus() == 'approved') {
			$allParams = $this->_getAllParams();
			$plugin =  $order->getPlugin();
			
			/**
			 * add transaction
			 */
		 	$allParams['gateway'] = 'authorize';
		 	$allParams['payment_status'] = 'Completed';
			Socialstore_Api_Transaction::getInstance()->addTransaction($allParams, $order->toArray(), $response->getOptions()->toArray());
			
			$plugin->onSuccess();
			return $this->_forward('process-success');
		}
		else {
			$this->view->response = $response;
			return $this->_forward('process-error');
		}
	}
	
	public function reviewAction(){
		if(!$this->_isValidProcess()){
			return ;
		}
		
		$form = $this->view->form =  new Socialstore_Form_Payment_Review;		
		// get result from review action
		$token =  $this->_getParam('token');
		$payer_id =  $this->_getParam('PayerID');
		$order_id =  $this->_getParam('id');
		$gateway =  $this->_getParam('gateway');
		$baseUrl =  $this->getBaseUrl();
		
		
		
		if($this->_request->isGet()){
			return ;
		}

		if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
			
			// get the payment
			$payment= new Socialstore_Payment(array('gateway'=>$gateway));
			
			// set request order
			$request = new Socialstore_Payment_Request('sale');
			$order   = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
			$request->setOrder($order);
			
			// get notify url
			$router =  $this->getFrontController()->getRouter();
			$notify_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment','action'=>'notify','id'=>$order_id,'gateway'=>$gateway),'default',true);
			
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
				$this->getLog('store.response.log')->log(var_export($response->getOptions(),true), Zend_Log::DEBUG);
							
				// get payment status
				$status =  $response->getOption('payment_status');
				$status =  strtolower($status);
				
				// cucess result
				if($response->isSuccess()){
					// process plugin.
					if($status == 'pending'){
						$plugin->onPending();
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
	
	
}
