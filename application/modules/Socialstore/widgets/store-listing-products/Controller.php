<?php

class Socialstore_Widget_StoreListingProductsController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
		if (Zend_Registry::isRegistered('product_search_params')) {
			$values = Zend_Registry::get('product_search_params');
		}
		else {
			$values = array();
		}
		if (Zend_Registry::isRegistered('store_detail_id')) {
			$values['store_id'] = Zend_Registry::get('store_detail_id');
		}
		$items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.product.page', 10);
		$this->view->items_per_page = $items_per_page;
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$values['page'] = $request -> getParam('page');
		$viewer = Engine_Api::_()->user()->getViewer();
		$this -> view -> user_id = $user_id = $viewer -> getIdentity();
		$values['view_status'] = 'show';
		$values['approve_status'] = 'approved';
		$this -> view -> paginator = $paginator = Engine_Api::_()->getApi('product','Socialstore')->getStoreSearchProductsPaginator($values);
		$paginator->setItemCountPerPage($items_per_page);
		unset($values['rewrite']);
		unset($values['module']);
		unset($values['controller']);
		$this->view->values = $values;
		$this->view->className = "layout_socialstore_store_listing_products";
	}

}
