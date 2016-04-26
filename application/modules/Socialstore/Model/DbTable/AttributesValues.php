<?php

class Socialstore_Model_DbTable_AttributesValues extends Engine_Db_Table{
	
	protected $_name = 'socialstore_attributes_values';
	protected $_primary  = 'value_id';
	protected $_rowClass = 'Socialstore_Model_Attributes_Value';
	
	public function getValue($params) {
		$product_id = $params['product_id'];
		$type_id = $params['type_id'];
		$select = $this->select()->where('product_id = ?', $product_id)->where('type_id = ?', $type_id);
		$result = $this->fetchRow($select);
		return $result;
	}
	
	public function addValue($params) {
		$checkValue = $this->getValue($params);
		if (count($checkValue) > 0) {
			$checkValue->value = $params['content'];
			$checkValue->save();
		}
		else {
			$value = $this->createRow();
			$value->product_id = $params['product_id'];
			$value->type_id = $params['type_id'];
			$value->value = $params['content'];
			$value->save();
		}
	}
	
	public function removeValue($params) {
		$value = $this->getValue($params);
		$value->delete();
	}
	
}
