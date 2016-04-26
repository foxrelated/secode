<?php

class Groupbuy_Model_DbTable_Vats extends Engine_Db_Table {

	protected $_rowClass = 'Groupbuy_Model_Vat';

	protected function _getMultiOptions() {
		$options = array();
		$options[''] = '';

		foreach($this->fetchAll() as $item) {
			$options[$item -> vat_id] = sprintf('%s %s%%', $options[$item -> name], $item -> value);
		}

		return $options;
	}

	static public function getMultiOptions() {
		$t = new self;
		return $t -> _getMultiOptions();
	}

	protected function _getValue($vat_id) {
		
		$item = $this -> find((int)$vat_id) -> current();
		if(is_object($item)) {
			return $item -> value;
		}
		return 0;
	}

	static public function getValue($vat_id) {
		$t = new self;
		$item = $this -> find((int)$vat_id) -> current();
		if(is_object($item)) {
			return $item -> value;
		}
		return 0;
		//return $t -> _getValue;
	}

}
