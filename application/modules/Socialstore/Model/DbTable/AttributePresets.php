<?php

class Socialstore_Model_DbTable_AttributePresets extends Engine_Db_Table{
	
	protected $_name = 'socialstore_attributepresets';
	
	protected $_rowClass =  'Socialstore_Model_AttributePreset';
	
	public function getPresetsByStore($store_id) {
		$select = $this->select()->where('store_id = ?', $store_id);
		return $this->fetchAll($select);
	}
}
