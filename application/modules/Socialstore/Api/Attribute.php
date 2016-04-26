<?php
class Socialstore_Api_Attribute extends Core_Api_Abstract {
	public function getTypeLabel($type) {
		$Types = new Socialstore_Model_DbTable_AttributesTypes;
		$label = $Types->getTypeLabelById($type);
		return $label;
	}
	
	public function getDefaultOption($product_id, $type) {
		$Options = new Socialstore_Model_DbTable_AttributesOptions;
		$option_select = $Options->select()->where('product_id = ?', $product_id)->where('type_id = ?', $type);
		$option = $Options->fetchRow($option_select);
		if (count($option) > 0) {
			return $option;
		}
		return null;
	}
	
	public function getOptionsByItemId($orderitem_id) {
		$OrderItems = new Socialstore_Model_DbTable_OrderItems;
		$select = $OrderItems->select()->where('orderitem_id = ?', $orderitem_id);
		$result = $OrderItems->fetchRow($select);
		if ($result->options_jsons != '' && $result->options_jsons != null) {
			$options = Zend_Json::decode($result->options_jsons);
			return $options;
		} 
		else {
			return null;
		}
	}
	
	public function getAttributes($options = null) {
		$str = '';
		if ($options != null && $options != '') {
			$ProductOptions = new Socialstore_Model_DbTable_Productoptions;
			$pro_op_select = $ProductOptions->select()->where('productoption_id = ?', $options);
			$pro_options = $ProductOptions->fetchRow($pro_op_select);
			$opts = explode('-', $pro_options->options);
			$Options = new Socialstore_Model_DbTable_AttributesOptions;
			$i = 0;
			$l = count($opts);
			foreach ($opts as $opt) {
				$i++;
				$opt_select = $Options->select()->where('option_id = ?', $opt);
				$result = $Options->fetchRow($opt_select);
				
				if ($i < $l) {
					$str .= $result->label. ' - ';
				}	
				else {
					$str .= $result->label;
				}
			}
		}
		if ($str == '') {
			$str = 'N/A';
		}
		return $str;
	}
	
}
