<?php

class Socialstore_Model_DbTable_Countries extends Engine_Db_Table{
	
	protected $_name = 'socialstore_countries';
	
	protected $_rowClass = 'Socialstore_Model_Country';
	
	protected $_primary = 'code';
	
	public function getName($country_id) {
		$select = $this->select()->where('code = ?', $country_id);
		$row = $this->fetchRow($select);
		if ($row) {
			return $row->country;
		}
	}
	
}
