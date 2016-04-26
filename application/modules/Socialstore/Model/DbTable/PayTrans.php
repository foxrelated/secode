<?php

class Socialstore_Model_DbTable_PayTrans extends Engine_Db_Table{
	
	protected $_name = 'socialstore_paytrans';
	
	protected $_rowClass = 'Socialstore_Model_PayTran';
	
	public function getByTransId($transaction_id,$gateway){
		$self = new self();
		$select =  $self->select()
					->where('transaction_id = ?', $transaction_id)
					->where('gateway = ?', $gateway);
		return $self->fetchRow($select);
	}
}
