<?php

class Socialstore_FaqsController extends Core_Controller_Action_Standard {
	
	public function init() {
		
		// set active menu item on home page.
		Zend_Registry::set('active_menu', 'socialstore_main_faqs');
	}

	public function indexAction() {
		$Table = new Socialstore_Model_DbTable_Faqs;
		$select = $Table->select()->where('status=?','show')->order('ordering asc');
		$this->view->items = $items =  $Table->fetchAll($select);
	}

}
