<?php

class Groupbuy_Model_DbTable_SubscriptionRelations extends Engine_Db_Table {
	protected $_name = 'groupbuy_subscription_relations';
	protected $_rowClass = 'Groupbuy_Model_SubscriptionRelation';
	
	

	/**
	 * XXX: PREVENT DUPLICATE CONDITION
	 */
	public function checkDupliatedCondition($data) {
		return true;
	}


}
