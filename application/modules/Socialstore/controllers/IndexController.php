<?php

class Socialstore_IndexController extends Core_Controller_Action_Standard {

	public function init() {
		// set active menu item on home page.
		Zend_Registry::set('active_menu', 'socialstore_main_store');
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$this -> _helper -> requireAuth() -> setAuthParams('social_store', $viewer, 'store_view') ) {
      		return false;
    	}

	}

	public function indexAction() {
		// load landing page
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}
	
	
	public function listingAction() {
		$values = $this->_getAllParams();
		Zend_Registry::set('store_search_params', $values);
		
		// load landing page
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}
	
	public function detailAction(){
		$this->view->headScript()
    	->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places');
		
		$store_id = $this->_getParam('store_id');
		Zend_Registry::set('store_detail_id', $store_id);
		$this->_helper->content
         ->setNoRender()
           ->setEnabled();
		
	}
	
	
	
	public function editStoreActon(){
		// edit action.
		
	}
	
	public function editProductActon(){
		// edit action.
		
	}
	public function clickAction() {
		$download_url = $this->_getParam('href');
		if ($download_url) {
        	$target = base64_decode($download_url);
      	}
		$Links = new Socialstore_Model_DbTable_Downloadurls;
		$select = $Links->select()->where('download_url = ?', $download_url);
		print_r($download_url);
		$result = $Links->fetchRow($select);
		if (count($result) <= 0) {
			$link = $Links->fetchNew();
			$link->download_url = $download_url;
			$link->file_url = $target;
			$link->used_time = 1;
			$link->save();
		}
		else {
			$result->used_time++;
			$result->save();
		}
      	$this->_helper->redirector->setPrependBase(false)->gotoUrl($target);
	}
}
