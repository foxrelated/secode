<?php

class Socialstore_Model_Paytype extends Engine_Db_Table_Row {

	protected $_plugin;

	public function getIdentity(){
		return $this->paytype_id;
	}
	/**
	 * @return Socialstore_Plugin_Payment_Abstract
	 */
	public function getPlugin() {
		if($this -> _plugin == null) {
			$plugin_class = $this -> plugin_class;
			$this -> _plugin = new $plugin_class;
		}
		return $this -> _plugin;
	}

}
