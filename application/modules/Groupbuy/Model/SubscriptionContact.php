<?php

class Groupbuy_Model_SubscriptionContact extends Core_Model_Item_Abstract{
	
	public function addCondition($data){
		// check to add more condition from array of message;
		$table  = new Groupbuy_Model_DbTable_SubscriptionConditions();
		return $table->addCondition($this, $data);
	}
}
