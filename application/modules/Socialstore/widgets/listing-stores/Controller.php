<?php

class Socialstore_Widget_ListingStoresController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() {
		
		if (Zend_Registry::isRegistered('store_search_params')) {
			$values = Zend_Registry::get('store_search_params');
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();	
		$items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.store.page', 10);
		$this->view->items_per_page = $items_per_page;
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$values['page'] = $request -> getParam('page');
		$values['view_status'] = 'show';
		$values['approve_status'] = 'approved';
 		$this -> view -> user_id = $user_id = $viewer -> getIdentity();
		$this -> view -> paginator = $paginator = Engine_Api::_()->getApi('store','Socialstore')->getStoresPaginator($values);
		$paginator->setItemCountPerPage($items_per_page);
		$this->view->values = $values;
		$this->view->className = "layout_socialstore_listing_stores";
		
	}

}
