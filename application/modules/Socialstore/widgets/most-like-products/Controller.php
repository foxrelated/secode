<?php

class Socialstore_Widget_MostLikeProductsController extends Socialstore_Content_Widget_ProductList {
	public function indexAction() {
		
		// initialzie
		parent::init();

		$Model = Engine_Api::_()->getDbtable('SocialProducts', 'Socialstore');
		$likeTable  = Engine_Api::_()->getDbtable('likes', 'core');
		$likeName = $likeTable->info('name');
		$select = $Model -> select()->from($Model->info('name'));
		if (Zend_Registry::isRegistered('store_detail_id')) {
			$store_id = Zend_Registry::get('store_detail_id');
			$select -> joinLeft($likeName, "resource_id = product_id",'')
    			-> where("resource_type  LIKE 'social_product'") 
    			-> where('deleted=?', 0) 
    			-> where('store_id =?', $store_id)
    			-> where('approve_status=?', 'approved') 
    			-> where('view_status=?', 'show')
    			-> group('resource_id')
    			-> order('Count(resource_id) desc')->limit($this->_limit);
		}
		else {
			$select -> joinLeft($likeName, "resource_id = product_id",'')
    			-> where("resource_type  LIKE 'social_product'") 
    			-> where('deleted=?', 0) 
    			-> where('approve_status=?', 'approved') 
    			-> where('view_status=?', 'show')
    			-> group('resource_id')
    			-> order('Count(resource_id) desc')->limit($this->_limit);
			
		}
		$this -> view -> items = $items = $Model -> fetchAll($select);
		$this -> view -> totalItems = $totalItems = count($items);

		if(!$totalItems) {
			$this -> setNoRender();
		}

		//$this -> view -> title = $this->_getParam('title','Most Liked Products');
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();		
	}
}
