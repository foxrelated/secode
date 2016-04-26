<?php

class Groupbuy_Widget_MostGreatDealsController extends Groupbuy_Content_Widget_Listing {
	
	public function indexAction() {
		$this->init();
		$params = array('limit' => $this -> getLimit(), 'status' => 30,'featured'=>1, 'orderby' => 'rates');
		$this -> view -> paginator = Engine_Api::_() -> groupbuy() -> getDealsPaginator($params);
	}

}
