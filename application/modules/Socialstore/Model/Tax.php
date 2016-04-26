<?php

class Socialstore_Model_Tax extends Core_Model_Item_Abstract{
	
	public function checkUsed() {
		$Product = new Socialstore_Model_DbTable_Products;
		$select = $Product->select()->where('tax_id = ?', $this->tax_id);
		$results = $Product->fetchAll($select);
		if (count($results) > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
}
