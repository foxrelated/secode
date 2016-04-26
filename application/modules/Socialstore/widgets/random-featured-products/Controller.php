<?php

class Socialstore_Widget_RandomFeaturedProductsController extends Socialstore_Content_Widget_ProductList{
	public function indexAction(){
		parent::init();
		
		
		//$select =  $Model->select()->where('deleted=?',0)->where('approve_status=?','approved')->where('view_status=?','show')->limit($this->_limit)->order('approved_date desc');
		$db = Engine_Db_Table::getDefaultAdapter();
		/*$select = "SELECT * FROM `engine4_store_products` WHERE product_id >= (
														SELECT FLOOR( MAX( product_id ) * RAND( ) )
														FROM engine4_store_products
														)
												  AND deleted = 0 AND approve_status = 'approved'
												  AND view_status = 'show'
												  AND featured = 1
												ORDER BY store_id
												LIMIT 5";*/
		$Model  = new Socialstore_Model_DbTable_Products;
		if (Zend_Registry::isRegistered('store_detail_id')) {
			$store_id = Zend_Registry::get('store_detail_id');
			$select =  $Model->select() 
						-> where('deleted=?', 0)
						-> where('featured=?', 1) 
						-> where('approve_status=?', 'approved') 
						-> where('view_status=?', 'show') 
						-> where('store_id =? ', $store_id)
						-> limit($this->_limit) 
						-> order('rand()');
		}
		else {
			$select =  $Model->select() 
						-> where('deleted=?', 0)
						-> where('featured=?', 1) 
						-> where('approve_status=?', 'approved') 
						-> where('view_status=?', 'show') 
						-> limit($this->_limit) 
						-> order('rand()');
		}
		$items = $db -> fetchAll($select);
		$objects = array();
		foreach ($items as $item) {
			$objects[] = Engine_Api::_()->getItem('social_product', $item['product_id']);
		}
		$this -> view -> items = $objects;
		$this -> view -> totalItems = $totalItems = count($objects);

		if(!$totalItems) {
			$this -> setNoRender();
		}

		//$this -> view -> title = $this->_getParam('title','Featured Products');
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();		
		
	}
}
