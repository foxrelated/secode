<?php


class Socialstore_Model_DbTable_ShippingPackages extends Engine_Db_Table{
	
	protected $_name = 'socialstore_shippingpackages';
	
	protected $_rowClass =  'Socialstore_Model_ShippingPackage';
	
	public function getTotalShippingFee($order_id) {
		$select = $this->select()->from($this->info('name'),array('sum(shipping_cost) as sum'))->where('order_id = ?', $order_id);
		$result = $this->fetchRow($select);
		return round($result->sum,2);
	}
	
	public function getTotalHandlingFee($order_id) {
		$select = $this->select()->from($this->info('name'),array('sum(handling_cost) as sum'))->where('order_id = ?', $order_id);
		$result = $this->fetchRow($select);
		return round($result->sum,2);
	}
	
	public function getShippingByStore($order_id, $store_id) {
		$select = $this->select()->from($this->info('name'),array('sum(shipping_cost) as sum'))->where('order_id = ?', $order_id)->where('store_id = ?', $store_id);
		$result = $this->fetchRow($select);
		return round($result->sum,2);
	}
	
	public function getHandlingByStore($order_id, $store_id) {
		$select = $this->select()->from($this->info('name'),array('sum(handling_cost) as sum'))->where('order_id = ?', $order_id)->where('store_id = ?', $store_id);
		$result = $this->fetchRow($select);
		return round($result->sum,2);
	}
	
	public function deleteOldIds($order_id) {
		$select = $this->select()->where('order_id = ?', $order_id);
		$results = $this->fetchAll($select);
		foreach ($results as $result) {
			$result->delete();
		}
	}
	
	public function getPackagesByOrder($order_id) {
		$select = $this->select()->from($this->info('name'),array('store_id', 'shippingaddress_id','shipping_cost','handling_cost'))->where('order_id = ?', $order_id);
		$results = $this->fetchAll($select);
		return $results;
	}
	
}
