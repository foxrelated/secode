<?php
class Socialstore_Model_DbTable_ShippingAddresses extends Engine_Db_Table{
	
	protected $_name = 'socialstore_shippingaddresses';
	
	protected $_rowClass = 'Socialstore_Model_ShippingAddress';
	
	public function getShippingAddresses($order_id) {
		$select = $this->select()->where('order_id = ?', $order_id)->where('is_form != ?', 1)->order('creation_date DESC');
		$results = $this->fetchAll($select);
		$shippingAddresses = array();
		if (count($results) > 0) {
			foreach ($results as $result) {
				$shippingAddresses[$result->shippingaddress_id] = Zend_Json::decode($result->value);
			}
		}
		return $shippingAddresses;
	}
	
	public function getDetailShippingAddresses($order_id) {
		$select = $this->select()->where('order_id = ?', $order_id)->order('creation_date DESC');
		$results = $this->fetchAll($select);
		$shippingAddresses = array();
		if (count($results) > 0) {
			foreach ($results as $result) {
				$address = Zend_Json::decode($result->value);
				$count = count((array)$address);
				$i = 0;
				$string = "";
				foreach ($address as $add) {
					$i++;
					if ($i == $count) {
						$string .= $add;
					}
					else {
						if ($add != '') {
							$string .=  $add. ', ';
						}
					}
				}
				$shippingAddresses[$result->shippingaddress_id] = $string;
			}
		}
		return $shippingAddresses;
	}
	
	public function getFormAddress($order_id) {
		$select = $this->select()->where('order_id = ?', $order_id)->where('is_form = ?', 1);
		$results = $this->fetchRow($select);
		return $results;
	}
	
	public function getLatestAddress($order_id) {
		$select = $this->select()->where('order_id = ?', $order_id)->order('creation_date DESC');
		$results = $this->fetchAll($select);
		if (count($results) > 0) {
			$result = $results[0];
		}
		return $result;
	}
	
	public function getAddress($shipping_id) {
		$select = $this->select()->where('shippingaddress_id = ?', $shipping_id);
		$results = $this->fetchRow($select);
		return $results;
	}
	
	public function getLatestAddresses($order_id, $number) {
		$select = $this->select()->where('order_id = ?', $order_id)->order('creation_date DESC')->limit($number);
		$results = $this->fetchAll($select);
		$shippingAddresses = array();
		if (count($results) > 0) {
			foreach ($results as $result) {
				$shippingAddresses[$result->shippingaddress_id] = Zend_Json::decode($result->value);
			}
		}
		return $shippingAddresses;
	}
	
	public function getAddressBookId($order_id) {
		$select = $this->select()->where('order_id = ?', $order_id)->order('creation_date DESC');
		$results = $this->fetchAll($select);
		$shippingAddresses = array();
		if (count($results) > 0) {
			foreach ($results as $result) {
				$shippingAddresses[] = $result->addressbook_id;
			}
		}
		return $shippingAddresses;
	}
	public function getShippingCountry($address_id) {
		$select = $this->select()->where('shippingaddress_id = ?', $address_id);
		$result = $this->fetchRow($select);
		if (is_object($result)) {
			$values = Zend_Json::decode($result->value);
			return $values['country']; 
		}
	}
	
	public function getShippingAddressString($shipping_id) {
		$address = $this->getAddress($shipping_id);
		if (!($address)) {
			return;
		}
		$value = Zend_Json::decode($address->value);
		$count = count((array)$value);
		$i = 0;
		$add = '';
		foreach ($value as $a) {
			$i++;
			if ($i == $count) {
				$add .= $a;
			}
			else {
				if ($a != null && $a != '') { 
					$a .= ', ';
					$add .= $a;
				}
			}
		}
		return $add;
	}
}