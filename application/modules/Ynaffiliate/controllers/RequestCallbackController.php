<?php

class Ynaffiliate_RequestCallbackController extends Core_Controller_Action_Standard
{
	protected $_logger;
	protected $_transactionData = array();
	public function indexAction()
	{
	}

	public function getLog()
	{
		if ($this -> _logger == NULL)
		{
			$filename = APPLICATION_PATH_TMP . '/log/affiliate-request-callback.' . date('Y-m-d') . '.log';
			$this -> _logger = new Zend_Log(new Zend_Log_Writer_Stream($filename));
		}
		return $this -> _logger;
	}

	public function log($message, $priority)
	{
		$this -> getLog() -> log($message, $priority);
	}

	public function notifyAction()
	{
		$gateway = 'paypal';
		$requestTable = Engine_Api::_() -> getDbTable("requests", 'ynaffiliate');
		$accountTable = Engine_Api::_() -> getDbTable('accounts', 'ynaffiliate');
		$id = $this -> _getParam('id', 0);
		$request_item = $requestTable -> find($id) -> current();
		$info_account = $accountTable -> getPaymentAccount($request_item -> user_id);
		$currency = $request_item -> currency;
		$amount = $request_item -> request_amount;
		$method_name = '_processResponse' . ucfirst($gateway);
		if (method_exists($this, $method_name))
		{
			$this -> {$method_name}();
		}
		else
		{
			return;
		}
		$status = $this -> _transactionData['payment_status'];
		if ($status == 'completed')
		{
			$request_item -> request_status = 'completed';
			$request_item -> response_date = date('Y-m-d H:i:s');
			$request_item -> save();

			$owner = $request_item->getOwner();
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($owner, $request_item, $request_item, 'ynaffiliate_request_approved');
		}
		else
		{
			$request_item -> request_status = $status;
			$request_item -> response_date = date('Y-m-d H:i:s');
			$request_item -> save();
		}
		$this -> log(var_export($this -> _transactionData, true), Zend_Log::DEBUG);
	}

	protected function _processResponsePaypal()
	{
		$params = $this -> _getAllParams();
		$maps = array(
			'transaction_id' => 'txn_id',
			'payment_type' => 'payment_type',
			'transaction_type' => 'txn_type',
			'pending_reason' => 'pending_reason',
			'payment_type' => 'payment_type',
			'currency' => 'mc_currency',
			'payment_status' => 'payment_status',
			'gateway_fee' => 'gateway_fee',
			'transaction_type' => 'transaction_type',
			'gateway_token' => 'gateway_token',
			'amount' => 'amount',
			'error_code' => 'error_code',
			'timestamp' => 'timestamp'
		);

		foreach ($maps as $key => $key2)
		{
			if (isset($params[$key2]))
			{
				$this -> _transactionData[$key] = $params[$key2];
			}
		}
		$this -> _transactionData['request_id'] = $this -> _getParam('id');
		$this -> _transactionData['payment_status'] = strtolower($this -> _transactionData['payment_status']);
		$this -> _transactionData['creation_date'] = date('Y-m-d H:i:s');
	}

	protected function _saveTransaction($request)
	{
		$model = new Socialstore_Model_DbTable_ReqTrans;
		$item = $model -> fetchNew();
		$item -> setFromArray($this -> _transactionData);
		$item -> setFromArray($request -> toArray());
		$item -> save();
	}

}
