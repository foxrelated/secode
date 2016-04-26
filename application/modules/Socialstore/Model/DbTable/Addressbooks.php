<?php
class Socialstore_Model_DbTable_Addressbooks extends Engine_Db_Table{
	
	protected $_name = 'socialstore_addressbooks';
	
	protected $_rowClass = 'Socialstore_Model_Addressbook';
	
	public function getAddressBook($user_id) {
		$select = $this->select()->where('user_id = ?', $user_id);
		$results = $this->fetchAll($select);
		$addressbook = array();
		if (count($results) > 0) {
			foreach ($results as $result) {
				$addressbook[$result->addressbook_id] = Zend_Json::decode($result->value);
			}
		}
		return $addressbook;
	}
	
	public function getShippingAddressBook($user_id, $order_id) {
		$ShippingAddresses = new Socialstore_Model_DbTable_ShippingAddresses;
		$addresses = $ShippingAddresses->getAddressBookId($order_id);
		$usedbook = array();
		foreach ($addresses as $key => $address) {
			$usedbook[] = $address;
		}
		$select = $this->select()->where('user_id = ?', $user_id);
		if (count($usedbook) > 0){
			$select->where('addressbook_id NOT IN (?)', $usedbook);
		}
		$results = $this->fetchAll($select);
		$addressbook = array();
		if (count($results) > 0) {
			foreach ($results as $result) {
				$addressbook[$result->addressbook_id] = Zend_Json::decode($result->value);
			}
		}
		return $addressbook;
	}

}