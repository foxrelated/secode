<?php

class Socialstore_Model_DbTable_ShippingMethods extends Engine_Db_Table{
	
	protected $_name = 'socialstore_shippingmethods';
	
	protected $_rowClass =  'Socialstore_Model_ShippingMethod';
	
	public function getShippingMethods($store_id){
		$select = $this->select()->where('store_id = ?', $store_id)->where('free_shipping = ?', '0');
		$results = $this->fetchAll($select);
		return $results;
	}
	public function getFreeShippingMethods($store_id){
		$select = $this->select()->where('store_id = ?', $store_id)->where('free_shipping = ?', '1');
		$results = $this->fetchAll($select);
		return $results;
	}
	
	public function getMethods($store_id) {
		$select = $this->select()->where('store_id = ?', $store_id);
		$results = $this->fetchAll($select);
		return $results;
	}
	
	public function getMethodById($shippingmethod_id) {
		$select = $this->select()->where('shippingmethod_id = ?', $shippingmethod_id);
		$results = $this->fetchRow($select);
		if (count($results) > 0) {
			return $results;
		}
		return false;	
	}
	
}
