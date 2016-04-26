<?php

class Socialstore_RequestCallbackController extends Core_Controller_Action_Standard{
	
	protected $_logger;
	
	protected $_transactionData = array();
	
	public function init(){
		$this->_helper->layout->disableLayout();
		$params =  $this->_getAllParams();
		$log_message = var_export($params, true);
		$this->log($log_message, Zend_Log::DEBUG);
	}
	
	public function indexAction(){
		
	}
	
	public function getLog(){
		if($this->_logger == NULL){
			$filename = APPLICATION_PATH_TMP . '/log/request-callback.'.date('Y-m-d'). '.log';
			$this->_logger = new Zend_Log(new Zend_Log_Writer_Stream($filename));
		}
		return $this->_logger;
	}
	
	public function log($message, $priority){
		$this->getLog()->log($message, $priority);
	}
	
	public function notifyAction(){
		$gateway = 'paypal';
		$table = new Socialstore_Model_DbTable_Requests;
		$id = $this -> _getParam('id', 0);
		$request_item = $table -> find($id) -> current();
		$account = $request_item->getAccount();
		$currency = Socialstore_Api_Core::getDefaultCurrency();
		$amount =  $request_item->request_amount;
		
		$method_name =  '_processResponse'.ucfirst($gateway);
		
		$this->log(var_export($request_item->toArray(),true), Zend_Log::DEBUG);
		$this->log("method name $method_name",Zend_Log::DEBUG);
		
		if(method_exists($this, $method_name)){
			$this->{$method_name}();
		}else{
			return ;
		}
		
		$status =  $this->_transactionData['payment_status'];
		
		if($status == 'completed'){
			$request_item->request_status= 'completed';
			$request_item->response_message = $this->_transactionData['response_message'];
			$request_item->response_date = date('Y-m-d H:i:s');
			$request_item->save();
			$this->_saveTransaction($request_item);
			
			// SEND EMAIL COMPLETE REQUEST 
			$sendTo = Engine_Api::_()->getItem('user', $request_item->owner_id);
			$params = $request_item->toArray();
      		Engine_Api::_()->getApi('mail','Socialstore')->send($sendTo, 'store_requestaccept',$params);	
		}
		else{
			$request_item->request_status = $status;
			$request_item->response_date = date('Y-m-d H:i:s');
			$request_item->save(); 	
		}
		
		$this->log(var_export($this->_transactionData, true), Zend_Log::DEBUG);
		// check the validate and make request money to the seller.
	}	
	
	protected function _processResponsePaypal(){
		$params = $this->_getAllParams();
		$maps =  array(
			'transaction_id'=>'txn_id',
			'payment_type'=>'payment_type',
			'transaction_type'=>'txn_type',			
			'pending_reason'=>'pending_reason',
			'payment_type'=>'payment_type',
			'currency'=>'mc_currency',			
			'payment_status'=>'payment_status',	
			'gateway_fee'=>'gateway_fee',
			'transaction_type'=>'transaction_type',
			'gateway_token'=>'gateway_token',
			'amount'=>'amount',
			'error_code'=>'error_code',
			'timestamp'=>'timestamp'
		);
		
		foreach($maps as $key=>$key2){
			if(isset($params[$key2])){
				$this->_transactionData[$key] =  $params[$key2];
			}
		}

		$this->_transactionData['request_id'] = $this->_getParam('id');
		$this->_transactionData['payment_status'] = strtolower($this->_transactionData['payment_status']);
		$this->_transactionData['creation_date'] = date('Y-m-d H:i:s');
		
	}
	
	protected function _saveTransaction($request){
		$model = new Socialstore_Model_DbTable_ReqTrans;
		$item = $model->fetchNew();
		$item->setFromArray($this->_transactionData);
		$item->setFromArray($request->toArray());
		$item->save();
	}
	
}
