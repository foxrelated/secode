<?php

class Socialstore_Payment2checkoutController extends Core_Controller_Action_Standard {
	
	/**
	 * @return Zend_Log
	 */
	
	const GATEWAY_URL   = 'https://www.2checkout.com/checkout/purchase';
    const SANDBOX_URL   = 'https://www.2checkout.com/checkout/purchase';
	
	public function getUrl()
    {
        $url = self::SANDBOX_URL;
        return $url;
    }
	
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
		//if (isset($this->_getParam('order_id')) && $this->_getParam('order_id')!='') {
			$order_id = $this->_getParam('order_id');
		//}
		//else {
		//	$order_id =  $this->_getParam('id');
		//}
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

		$gateway =  $this->_getParam('gateway','2Checkout');
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
	}
	
	public function processAction() {
		Zend_Registry::set('active_menu', 'socialstore_main_mycart');
		Zend_Registry::set('PAYMENTMENU_ACTIVE','payment-confirm');
		$this->view->transactionData = $data = array();
		// check valid gateway has posted to.
		$gateway = $this->_getParam('gateway','2Checkout');
		$payment =  new Socialstore_Payment(array('gateway'=>$gateway));
		$request =  new Socialstore_Payment_Request('sale');
		$order_id   = $this->_getParam('id');
		$order   = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		// check payment method

		
		$router =  $this->getFrontController()->getRouter();
		$return_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-2checkout','action'=>'review','id'=>$order_id,'gateway'=>$gateway),'default',true);
		$notify_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-2checkout','action'=>'notify','id'=>$order_id,'gateway'=>$gateway),'default',true);
		$cancel_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-2checkout','action'=>'cancel','id'=>$order_id,'gateway'=>$gateway),'default',true);
		
		$options =  array(
			'return_url'=>$this->getBaseUrl().  $return_url,
			'notify_url'=>$this->getBaseUrl().  $notify_url,
			'cancel_url'=>$this->getBaseUrl().  $cancel_url,
		);
		$request->setOrder($order);
		$request->setOptions($options);
		$checkoutGateway = new Socialstore_Model_DbTable_Gateways();
		$config = $checkoutGateway->getConfig('2checkout');
		$orderArray = $order->toArray();
		
		$checkoutArray = array();
		$orderArray['sid'] = $config['2checkoutno'];
		$orderArray['total'] = $orderArray['total_amount'];
		$orderArray['cart_order_id'] = $order_id;
		$sandbox = Engine_Api::_()->getApi('settings','core')->getSetting('store.mode',1);
		if ($sandbox == 1) {
			$orderArray['demo'] = 'Y';
		}
		else {
			$orderArray['demo'] = 'N';
		}
		$redirect_url = $this->getUrl()."?".http_build_query($orderArray) . "\n";
		$this->_redirect($redirect_url);
	}
	
	public function reviewAction(){
		if(!$this->_isValidProcess()){
			return ;
		}
		
		// get result from review action
		$token =  $this->_getParam('token');
		$payer_id =  $this->_getParam('PayerID');
		$order_id =  $this->_getParam('order_id');
		$order   = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		$gateway =  $this->_getParam('gateway');
		$baseUrl =  $this->getBaseUrl();
		$allParams = $this->_getAllParams();
		$creditProcess = $this->_getParam('credit_card_processed');
		$plugin =  $order->getPlugin();		
		try{
			if ($creditProcess != 'Y') {
				$plugin->onFailure();
				return $this->_forward('process-error');
			}
			else {
				$plugin->onSuccess();
				$allParams['gateway'] = '2checkout';
				$allParams['transaction_id'] = $allParams['invoice_id'];
				$allParams['amount'] = $allParams['total_amount'];
				$allParams['payment_status'] = 'Completed';
				Socialstore_Api_Transaction::getInstance()->addTransaction($allParams);
			}
			return $this->_forward('process-success');	

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
		$order_id =  $this->_getParam('order_id');
		$this->view->order =   $order   =  Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		
	}
	
	public function processSuccessAction(){
		$order_id =  $this->_getParam('order_id');
		$this->view->order =   $order   =  Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		
	}
	
	
}
