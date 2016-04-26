<?php

class Socialstore_Model_DbTable_Taxes extends Engine_Db_Table {

	protected $_rowClass = 'Socialstore_Model_Tax';

	protected function _getMultiOptions($store_id = null) {
		$options = array();
		$options[''] = '';
		if ($store_id == null) {
			return;
		}
		$select = $this->select()->where('store_id = ?', $store_id);
		$results = $this->fetchAll($select);
		foreach($results as $item) {
			$options[$item -> tax_id] = sprintf('%s %s%%', @$options[$item -> name], $item -> value);
		}

		return $options;
	}

	static public function getMultiOptions($store_id = null) {
		$t = new self;
		return $t -> _getMultiOptions($store_id);
	}

	protected function _getValue($tax_id) {
		
		$item = $this -> find((int)$tax_id) -> current();
		if(is_object($item)) {
			return $item -> value;
		}
		return 0;
	}

	static public function getValue($tax_id) {
		$t = new self;
		$item = $this -> find((int)$tax_id) -> current();
		if(is_object($item)) {
			return $item -> value;
		}
		return 0;
		//return $t -> _getValue;
	}		
	
	public function getTaxById($tax_id) {
		$item = $this->find((int)$tax_id)->current();
		if(is_object($item)) {
			return $item;
		}
		return;
	}

}
