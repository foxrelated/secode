<?php
class Socialstore_Model_DbTable_BillingAddresses extends Engine_Db_Table{
	
	protected $_name = 'socialstore_billingaddresses';
	
	protected $_rowClass = 'Socialstore_Model_BillingAddress';
	
	public function getBillingAddress($order_id) {
		$select = $this->select()->where('order_id = ?', $order_id);
		$results = $this->fetchRow($select);
		$billingAddress = '';
		if (count($results) > 0) {
				$billingAddress = Zend_Json::decode($results->value);
		}
		return $billingAddress;
	}
	
	public function getBillingItem($order_id) {
		$select = $this->select()->where('order_id = ?', $order_id);
		$results = $this->fetchRow($select);
		return $results;
	}

}