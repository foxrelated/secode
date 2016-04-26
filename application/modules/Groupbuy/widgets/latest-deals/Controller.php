<?php

class Groupbuy_Widget_LatestDealsController extends Groupbuy_Content_Widget_Listing {
	public function indexAction() {
		$this->init();
		$params = array('limit' => $this -> getLimit(), 'status' => 30, 'orderby' => 'start_time');		
		$this -> view -> paginator = $data = Engine_Api::_() -> groupbuy() -> getDealsPaginator($params);
	}
}