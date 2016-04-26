<?php

class Socialstore_Widget_MainHighlightProductsController extends Socialstore_Content_Widget_MainProductList {
	public function indexAction() {
		parent::init();
		
		$Model = new Socialstore_Model_DbTable_Products;

		$select = $Model -> select();
		$select -> where('deleted=?', 0) -> where('highlight = 1') -> where('approve_status=?', 'approved') -> where('view_status=?', 'show')-> order('approved_date desc')->limit($this->_limit);

		$this -> view -> items = $items = $Model -> fetchAll($select);
		$this -> view -> totalItems = $totalItems = count($items);

		if(!$totalItems) {
			$this -> setNoRender();
		}

		//$this -> view -> title = $this->_getParam('title','Recent Products');
		$this->view->show_options['creation'] =  1;
		$this->view->show_options['socialstore'] = 1;		
		$this->view->show_options['author'] = 1 ;
		$this->view->className = "layout_socialstore_main_highlight_products";
		
	}

}
