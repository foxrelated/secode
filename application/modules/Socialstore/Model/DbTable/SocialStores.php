<?php

class Socialstore_Model_DbTable_Socialstores extends Engine_Db_Table{
	
	/**
	 * model table name
	 * @see engine4_stores
	 * @var string
	 */
	protected $_name = 'socialstore_stores';
	
	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Socialstore_Model_Store';
	
	/**
	 * @param  int     $user_id
	 * @return true|false
	 * 
	 */
	public function userHasStore($user_id){
		$select = $this->select()->where('owner_id=?',(int)$user_id);
		$item  = $this->fetchRow($select);
		if(is_object($item)){
			return true;
		}
		return false;
	}
	
	/**
	 * get store by user_id
	 * @param   int $owner_id
	 * @return Socialstore_Model_Store
	 */
	public function getStoreByOwnerId($owner_id){
		$select = $this->select()->where('owner_id=?',(int)$owner_id) ->where('deleted = 0');
		$item  = $this->fetchRow($select);
		return $item;
	}
	

}
