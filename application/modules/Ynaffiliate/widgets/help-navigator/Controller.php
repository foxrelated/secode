<?php

class Ynaffiliate_Widget_HelpNavigatorController extends Engine_Content_Widget_Abstract{
	public function indexAction(){
		// check from help pages.
		$helpTable = Engine_Api::_() -> getDbTable('helppages', 'ynaffiliate');
		$select = $helpTable -> select();
		$select -> where("status = 'show'") -> order("ordering ASC");
		$items = $helpTable -> fetchAll($select);
		$this->view->items  =  $items;
		if(Zend_Registry::isRegistered('ACTIVE_HELP_PAGE')){
			$active_menu = Zend_Registry::get('ACTIVE_HELP_PAGE');
		}
		$this->view->active = $active_menu;
	}
}
