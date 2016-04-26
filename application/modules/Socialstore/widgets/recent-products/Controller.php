<?php

class Socialstore_Widget_RecentProductsController extends Socialstore_Content_Widget_ProductList{
	public function indexAction(){
		parent::init();
		//$this->view->title =  $this->_getParam('title','Recent Products');
		
		
		$Model  = new Socialstore_Model_DbTable_Products;
		if (Zend_Registry::isRegistered('store_detail_id')) {
			$store_id = Zend_Registry::get('store_detail_id');
			$select =  $Model->select()
						->where('deleted=?',0)
						->where('approve_status=?','approved')
						->where('view_status=?','show')
						->where('store_id = ?', $store_id)
						->limit($this->_limit)
						->order('approved_date desc');
		}
		else {
		$select =  $Model->select()
					->where('deleted=?',0)
					->where('approve_status=?','approved')
					->where('view_status=?','show')
					->limit($this->_limit)
					->order('approved_date desc');
		}
		$this -> view -> items = $items = $Model -> fetchAll($select);
		$this -> view -> totalItems = $totalItems = count($items);

		if(!$totalItems) {
			$this -> setNoRender();
		}

		//$this -> view -> title = $this->_getParam('title','Recent Products');
		$this->view->show_options['creation'] =  1;		
		
	}
}
