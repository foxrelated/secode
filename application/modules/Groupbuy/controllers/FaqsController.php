<?php

class Groupbuy_FaqsController extends Core_Controller_Action_Standard {
	
	public function init() {
		// private page
		//if(!$this -> _helper -> requireUser() -> isValid()) {
		//	return ;
		//}
		// set active menu item on home page.
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('groupbuy_main', array(), 'groupbuy_main_faqs');
	}

	public function indexAction() {
		$Table = new Groupbuy_Model_DbTable_Faqs;
		$select = $Table->select()->where('status=?','show')->order('ordering asc');
		$this->view->items = $items =  $Table->fetchAll($select);
	}

}
