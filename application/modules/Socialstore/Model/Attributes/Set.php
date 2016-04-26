<?php

class Socialstore_Model_Attributes_Set extends Core_Model_Item_Abstract{
	public function checkUsed() {
		$Product = new Socialstore_Model_DbTable_Products;
		$select = $Product->select()->where('attributeset_id = ?', $this->set_id);
		$results = $Product->fetchAll($select);
		if (count($results) > 0) {
			return true;
		}
		else {
			return false;
		}
	}
}
