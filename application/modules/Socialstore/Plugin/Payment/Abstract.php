<?php

abstract class Socialstore_Plugin_Payment_Abstract {

	/**
	 * @see  schema.engine4_store_paytypes
	 * @var  string
	 */
	protected $_payType;

	/**
	 * @see  schema.engine4_store_orders
	 * @var  string
	 */
	protected $_orderId;

	/**
	 * @var Socialstore_Payment_Order_Interface
	 */
	protected $_order;

	/**
	 * @param   string   $order_id
	 * @return Socialstore_Plugin_Payment_Abstract
	 */
	public function setOrderId($order_id) {
		$this -> _orderId = $order_id;
		return $this;
	}

	/**
	 * @return  string
	 */
	public function getOrderId() {
		if($this -> _orderId == null) {
			throw new Exception("order id has not set.");
		}
		return $this -> _orderId;
	}

	/**
	 * @param  string  $pay_type
	 * @return Socialstore_Plugin_Payment_Abstract
	 */
	public function setPaytype($pay_type) {
		$this->_payType =  $pay_type;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPaytype() {
		if($this -> _payType == null) {
			throw new Exception("Paytype has not set");
		}
		return $this -> _payType;
	}

	/**
	 * @return Socialstore_Payment_Order_Interface
	 */
	public function getOrder() {
		if($this -> _order == null) {
			throw new Exception("Order has not set");
		}
		return $this->_order;
	}

	/**
	 * @param Socialstore_Payment_Order_Interface $order
	 * @return Socialstore_Plugin_Payment_Abstract
	 */
	public function setOrder(Socialstore_Payment_Order_Interface $order) {
		$this -> _order = $order;
		$this -> _payType = $order -> getPaytype();
		$this -> _orderId = $order -> getId();
		return $this;
	}
	
	/**
	 * @return Socialstore_Model_DbTable_OrderItems
	 */
	public function getModelOrderItems(){
		return new Socialstore_Model_DbTable_OrderItems;
	}
	
	public function getByObjectId($object_id, $options = null){
		$order_id =  $this->getOrder()->getId();
		$Items =  $this->getModelOrderItems();		
		$select =  $Items->select()->where('order_id=?', $order_id)->where('object_id=?', (string)$object_id);
		if ($options != null) {
			$select->where('options = ?', $options);
		}
		return $Items->fetchRow($select);
	}
	
	abstract function onSuccess();
	
	abstract function onPending();
	
	abstract function onFailure();
	
	abstract function onCancel();
	
	public function noBilling(){
		return true;
	}
	
	abstract function addItem($item, $qty = 1, $params);
	
	public function noShipping(){
		return true;
	}
	
}
