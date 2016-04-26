<?php

class Socialstore_Model_DbTable_AttributesOptions extends Engine_Db_Table{
	
	protected $_name = 'socialstore_attributes_options';
	protected $_primary  = 'option_id';
	protected $_rowClass = 'Socialstore_Model_Attributes_Option';
	
	public function deleteOption($option) {
		$Values = new Socialstore_Model_DbTable_AttributesValues;
		$value_select = $Values->select()->where('type_id = ?', $option->type_id)->where('value = ?', $option->option_id);
		$values = $Values->fetchAll($value_select);
		if (count($values)>0) {
			foreach ($values as $value) {
				$value->delete();
			}
		}
		$option->delete();
	}
	
	public function getOption($params) {
		$select = $this->select()->where('type_id = ?', $params['type_id'])->where('option_id = ?', $params['option_id']);
		$option = $this->fetchRow($select);
		if (count($option) > 0) {
			return $option;
		}
	}
	
	public function deleteOptionsByProId($product_id) {
		$select = $this->select()->where('product_id = ?', $product_id);
		$results = $this->fetchAll($select);
		foreach ($results as $result) {
			$this->deleteOption($result);
		}
	}
}
