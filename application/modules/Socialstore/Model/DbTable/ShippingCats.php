<?php

class Socialstore_Model_DbTable_ShippingCats extends Engine_Db_Table{
	
	protected $_name = 'socialstore_shippingcats';
	
	protected $_rowClass =  'Socialstore_Model_ShippingCat';
	
	public function checkCat($catId,$shippingrule_id = null,$shippingmethod_id) {
		$select = $this->select()->where("category_id = $catId OR category_id ='0'")->where('shippingmethod_id = ?', $shippingmethod_id);
		if ($shippingrule_id) {
			$select->where('shippingrule_id <> ?', $shippingrule_id);
		}
		$result = $this->fetchRow($select);
		if (count($result) > 0) {
			return true;
		}
		return false;
	}
	public function checkFreeCat($catId,$shippingrule_id = null,$shippingmethod_ids) {
		$select = $this->select()->where("category_id = $catId OR category_id ='0'")->where('shippingmethod_id IN (?)', $shippingmethod_ids);
		if ($shippingrule_id) {
			$select->where('shippingrule_id != ?', $shippingrule_id);
		}
		$result = $this->fetchRow($select);
		if (count($result) > 0) {
			return true;
		}
		return false;
	}
	
	public function deleteCat($rule_id) {
		$select = $this->select()->where("shippingrule_id = ?", $rule_id);
		$results = $this->fetchAll($select);
		foreach ($results as $result) {
			$result->delete();
		}
	}
}
