<?php

//Socialstore_Api_Following::getInstance()->isFollowing();

class Socialstore_Api_Following extends Core_Api_Abstract
{
	static private $_instance;
	
	static public function getInstance(){
		if(self::$_instance == null){
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	private function __construct(){}
	
	public function getDbAdapter(){
		return Engine_Db_Table::getDefaultAdapter();
	}
	
	public function isFollowing($user_id, $store_id) {
		$sql  = "select * from engine4_socialstore_follows where user_id=%d and store_id=%d";
		$row = $this->getDbAdapter()->fetchRow(sprintf($sql, $user_id, $store_id));
		return (bool)$row;
	}
	
	public function addFollower($user_id, $store_id){
		try{
			$this->getDbAdapter()->insert('engine4_socialstore_follows', array(
				'store_id'=>$store_id,
				'user_id'=>$user_id,
				'creation_date' => date('Y-m-d H:i:s'),
			));
			$store = Engine_Api::_()->getItem('social_store', $store_id);
			$store->follow_count++;
			$store->save();
		}
		catch(Exception $e){
		
		}
	}
	
	public function deleteFollower($user_id, $store_id){
		try{
			$this->getDbAdapter()->delete('engine4_socialstore_follows', array(
				'store_id=?'=>$store_id,
				'user_id=?'=>$user_id,
			));
			$store = Engine_Api::_()->getItem('social_store', $store_id);
			$store->follow_count--;
			$store->save();
		}
		catch(Exception $e){
					
		}
	}
	
	public function getFollowedStoresPaginators($params = array()){
		$paginator = Zend_Paginator::factory($this->getFollowedStoresSelect($params));
   
	    if( !empty($params['page']) )
	    {
	      $paginator->setCurrentPageNumber($params['page']);
	    }
	    if( !empty($params['limit']) )
	    {
	      $paginator->setItemCountPerPage($params['limit']);
	    }
	    return $paginator;
	}
	
	public function getFollowedStoresSelect($params = array()) {
		$table = new Socialstore_Model_DbTable_SocialStores();
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName)->setIntegrityCheck(false);
		
	    $select->joinLeft('engine4_socialstore_follows', "$rName.store_id = engine4_socialstore_follows.store_id", 'engine4_socialstore_follows.user_id as user_id');
	    $select->where('view_status = ?', 'show');
	    $select->where('approve_status =?', 'approved');
	    if(isset($params['user_id']) && $params['user_id'] != "") {
	    	$select->where('user_id = ?', $params['user_id']);
	    }
	    
	    return $select;	
	}
}

