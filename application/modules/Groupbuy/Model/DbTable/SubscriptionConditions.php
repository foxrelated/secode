<?php

class Groupbuy_Model_DbTable_SubscriptionConditions extends Engine_Db_Table {
	protected $_name = 'groupbuy_subscription_conditions';
	protected $_rowClass = 'Groupbuy_Model_SubscriptionCondition';
	
	

	/**
	 * XXX: PREVENT DUPLICATE CONDITION
	 */
	public function checkDupliatedCondition($data) {
		return true;
	}

	/**
	 *
	 * @param   Groupbuy_Model_SubscriptionContact  $contact
	 * @param   Object|Array                       $data
	 * @return  Groupbuy_Model_SubscriptionCondition
	 */
	public function addCondition($contact, $data) {
		if(!$this -> checkDupliatedCondition($data)) {
			return NULL;
		}
		$item = $this -> fetchNew();
		$item -> setFromArray((array)$data);
		$item -> contact_id = $contact -> getIdentity();
		$item -> save();
		return $item;
	}

}
