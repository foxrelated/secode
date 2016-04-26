<?php

class Socialstore_Widget_MostRatedProductsController extends Socialstore_Content_Widget_ProductList {
	public function indexAction() {
		
		// initialize
		parent::init();
		
		$Model = new Socialstore_Model_DbTable_Products;

		$select = $Model -> select();
		if (Zend_Registry::isRegistered('store_detail_id')) {
			$store_id = Zend_Registry::get('store_detail_id');
			$select -> where('deleted=?', 0) 
			-> where('approve_status=?', 'approved') 
			-> where('view_status=?', 'show')
			-> where('store_id =?', $store_id)
			-> order('rate_ave desc')
			->limit($this->_limit);
		}
		else {
			$select -> where('deleted=?', 0) 
			-> where('approve_status=?', 'approved') 
			-> where('view_status=?', 'show')
			-> order('rate_ave desc')
			->limit($this->_limit);
		}

		$this -> view -> items = $items = $Model -> fetchAll($select);
		$this -> view -> totalItems = $totalItems = count($items);

		if(!$totalItems) {
			$this -> setNoRender();
		}

		//$this -> view -> title = $this->_getParam('title','Most Rated Products');
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();		
	}

}
