<?php

class Socialstore_Model_DbTable_AttributesSets extends Engine_Db_Table{
	
	protected $_name = 'socialstore_attributes_sets';
	
	protected $_rowClass = 'Socialstore_Model_Attributes_Set';
	
	protected $_primary = 'set_id';
	
	public function getSetById($set_id) {
		$item = $this->find((int)$set_id)->current();
		if(is_object($item)) {
			return $item;
		}
		return;
	}
	
	public function getSetsByStoreId($store_id) {
		$select = $this->select()->where('store_id = ?', $store_id);
		$results = $this->fetchAll($select);
		return $results;
	}
	
	public function getSetName($set_id) {
		$select = $this->select()->where('set_id = ?', $set_id);
		$result = $this->fetchRow($select);
		return $result->name;
	}
	
}
