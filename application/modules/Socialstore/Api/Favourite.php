<?php
class Socialstore_Api_Favourite extends Core_Api_Abstract
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
	
	public function isFavourited($user_id, $product_id) {
		$sql  = "select * from engine4_socialstore_favourites where user_id=%d and product_id=%d";
		$row = $this->getDbAdapter()->fetchRow(sprintf($sql, $user_id, $product_id));
		return (bool)$row;
	}
	
	public function addFavouriter($user_id, $product_id){
		try{
			$this->getDbAdapter()->insert('engine4_socialstore_favourites', array(
				'product_id'=>$product_id,
				'user_id'=>$user_id,
				'creation_date' => date('Y-m-d H:i:s'),
			));
			$product = Engine_Api::_()->getItem('social_product', $product_id);
			$product->favourite_count++;
			$product->save();
		}
		catch(Exception $e){
		
		}
	}
	
	public function deleteFavouriter($user_id, $product_id){
		try{
			$this->getDbAdapter()->delete('engine4_socialstore_favourites', array(
				'product_id=?'=>$product_id,
				'user_id=?'=>$user_id,
			));
			$product = Engine_Api::_()->getItem('social_product', $product_id);
			$product->favourite_count--;
			$product->save();
		}
		catch(Exception $e){
					
		}
	}
	
	public function getFavouritedProductsPaginators($params = array()){
		$paginator = Zend_Paginator::factory($this->getFavouritedProductsSelect($params));
   
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
	
	public function getFavouritedProductsSelect($params = array()) {
		$table = new Socialstore_Model_DbTable_Products();
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName)->setIntegrityCheck(false);
	    $select->joinLeft('engine4_socialstore_favourites', "$rName.product_id = engine4_socialstore_favourites.product_id", 'engine4_socialstore_favourites.user_id as user_id');
	    $select->where('view_status = ?', 'show');
	    $select->where('approve_status =?', 'approved');
	    if(isset($params['user_id']) && $params['user_id'] != "") {
	    	$select->where('user_id = ?', $params['user_id']);
	    }
	    return $select;	
	}
}

