<?php

class Socialstore_Model_Attributes_Type extends Core_Model_Item_Abstract{
	protected $_primary  = 'type_id';
	public function getOptions($product_id) {
		$Options = new Socialstore_Model_DbTable_AttributesOptions;
		$select = $Options->select()->where('type_id = ?',$this->type_id)->where('product_id = ?', $product_id);
		return $Options->fetchAll($select);
	}
}
