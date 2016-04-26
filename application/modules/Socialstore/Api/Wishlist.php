<?php
class Socialstore_Api_Wishlist extends Core_Api_Abstract
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
	
	public function addedToWishlist($user_id, $product_id) {
		$sql  = "select * from engine4_socialstore_wishlists where user_id=%d and product_id=%d";
		$row = $this->getDbAdapter()->fetchRow(sprintf($sql, $user_id, $product_id));
		return (bool)$row;
	}
	
	public function addToWishlist($user_id, $product_id){
		try{
			$this->getDbAdapter()->insert('engine4_socialstore_wishlists', array(
				'product_id'=>$product_id,
				'user_id'=>$user_id,
				'creation_date' => date('Y-m-d H:i:s'),
			));
		}
		catch(Exception $e){
		
		}
	}
	
	public function removeFromWishlist($user_id, $product_id){
		try{
			$this->getDbAdapter()->delete('engine4_socialstore_wishlists', array(
				'product_id=?'=>$product_id,
				'user_id=?'=>$user_id,
			));
		}
		catch(Exception $e){
					
		}
	}
	
	public function getWishlistProductsPaginators($params = array()){
		$paginator = Zend_Paginator::factory($this->getWishlistProductsSelect($params));
   
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
	
	public function getWishlistProductsSelect($params = array()) {
		$table = new Socialstore_Model_DbTable_Products();
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName)->setIntegrityCheck(false);
	    $select->joinLeft('engine4_socialstore_wishlists', "$rName.product_id = engine4_socialstore_wishlists.product_id", 'engine4_socialstore_wishlists.user_id as user_id');
	    $select->where('view_status = ?', 'show');
	    $select->where('approve_status =?', 'approved');
	    if(isset($params['user_id']) && $params['user_id'] != "") {
	    	$select->where('user_id = ?', $params['user_id']);
	    }
	    return $select;	
	}
}

