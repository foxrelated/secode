<?php

class Groupbuy_Widget_MostRatedDealsController extends Groupbuy_Content_Widget_Listing {
	
	public function indexAction() {
		$this->init();
		$params = array('limit' => $this -> getLimit(), 'status' => 30, 'orderby' => 'rates');
		$this -> view-> paginator= $data = Engine_Api::_() -> groupbuy() -> getDealsPaginator($params);
	}

}
