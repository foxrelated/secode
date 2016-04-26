<?php

class Socialstore_Model_DbTable_OrderItems extends Engine_Db_Table{
	protected $_name = 'socialstore_orderitems';
	
	protected $_rowClass = 'Socialstore_Model_OrderItem';
	
	static public function getByOrderItemId($orderitem_id){
		$self =  new self;
		return $self->find($orderitem_id)->current();
	}
	
	public function deleteOrderItemById($id) {
		$self = new self;
		$item = $self->find($id)->current();
		$item->delete();
	}
	
	public function getPackages($order_id) {
		$select = $this->select()->distinct(true)->from($this->info('name'),array('store_id','shippingaddress_id'));
		$select->where('order_id = ?', $order_id);
		return $this->fetchAll($select);
	}
	
	public function getPackageItems($pair,$order_id) {
		$address_id = $pair['shippingaddress_id'];
		$store_id = $pair['store_id'];
		//$select = $this->select()->from($this->info('name'),array('order_shipping_amount','shipping_amount','handling_amount','shippingrule_id'))->where('shippingaddress_id = ?', $address_id);
		$select = $this->select()->where('shippingaddress_id = ?', $address_id);
		$select->where('store_id = ?', $store_id);
		$select->where('order_id = ?', $order_id);
		$results = $this->fetchAll($select);
		/*$temp = array();
		foreach ($results as $result) {
			$temp['order_shipping_amount'] = $result->order_shipping_amount;
			$temp['shipping_amount'] = $result->shipping_amount;
			$temp['handling_amount'] = $result->handling_amount;
 		}
 		print_r($temp);die;*/
		return $results;
	}
	
	public function getHandling($order_id) {
		$select = $this->select()->from($this->info('name'),array('order_handling_amount','shippingrule_id'))->where('order_id = ?', $order_id);
		$results = $this->fetchAll($select);
	}
	
	public function getOldIds($order_id) {
		$select = $this->select()->where('order_id = ?', $order_id);
		$results = $this->fetchAll($select);
		$return = array();
		foreach ($results as $result) {
			$return[] = $result->orderitem_id;
		}
		return $return;
	}
	
}
