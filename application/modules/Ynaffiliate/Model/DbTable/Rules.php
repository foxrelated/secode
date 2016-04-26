<?php

class Ynaffiliate_Model_DbTable_Rules extends Engine_Db_Table {

	protected $_rowClass = "Ynaffiliate_Model_Rule";

	public function getRuleByName($rule_name) {
		$result = $this -> select() -> where('rule_name = ?', $rule_name) -> where('enabled = 1');
		return $this -> fetchRow($result);
	}

	public function getRuleById($rule_id) {
		$result = $this -> select() -> where('rule_id = ?', $rule_id);
		return $this -> fetchRow($result);
	}

	public function getRuleEnabled() {
		$select = $this -> select();
		$enabledModuleNames = Engine_Api::_() -> getDbtable('modules', 'core')->getEnabledModuleNames();
      	$select = $this -> select() -> where('module IN(?)', $enabledModuleNames);
		return $this -> fetchAll($select);
	}

}
