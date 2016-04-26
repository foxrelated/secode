<?php

class Socialstore_Model_DbTable_AttributesTypes extends Engine_Db_Table{
	
	protected $_name = 'socialstore_attributes_types';
	protected $_primary  = 'type_id';
	protected $_rowClass = 'Socialstore_Model_Attributes_Type';
	
	public function getTypes($set_id) {
		$select = $this->select()->where('set_id = ?', $set_id);
		return $this->fetchAll($select);
	}
	
	public function deleteAttribute($type) {
		$Options = new Socialstore_Model_DbTable_AttributesOptions;
		$Values = new Socialstore_Model_DbTable_AttributesValues;
		$option_select = $Options->select()->where('type_id = ?', $type->type_id);
		$options = $Options->fetchAll($option_select);
		if (count($options)>0) {
			foreach ($options as $option) {
				$option->delete();
			}
		}
		$value_select = $Values->select()->where('type_id = ?', $type->type_id);
		$values = $Values->fetchAll($value_select);
		if (count($values)>0) {
			foreach ($values as $value) {
				$value->delete();
			}
		}
		$type->delete();
	}
	
	public function getTypeLabelById($type_id) {
		$select = $this->select()->where('type_id = ?', $type_id);
		$type = $this->fetchRow($select);
		return $type->label;
	}
	
}
