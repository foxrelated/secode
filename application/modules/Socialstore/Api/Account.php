<?php

class Socialstore_Api_Account {
	
	static public function getAccount($owner_id, $store_id, $gateway = 'paypal') {
		$Table =  new Socialstore_Model_DbTable_Accounts;
		$select =  $Table->select()->where('owner_id=?', $owner_id)->where('gateway_id=?',$gateway)->where('store_id=?', $store_id);
		$item =  $Table->fetchRow($select);		
		return $item;
	}
}
