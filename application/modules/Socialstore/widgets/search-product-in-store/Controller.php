<?php

class Socialstore_Widget_SearchProductInStoreController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		if (Zend_Registry::isRegistered('product_search_params')) {
			$values = Zend_Registry::get('product_search_params');
			$store_id = $values['store_id'];
			Zend_Registry::set('store_id', $values['store_id']);
		}
		if (Zend_Registry::isRegistered('store_detail_id')) {
			$store_id = Zend_Registry::get('store_detail_id');
			Zend_Registry::set('store_id', $store_id);
		}
		$this->view->form = $form = new Socialstore_Form_Product_StoreSearch();
		if (Zend_Registry::isRegistered('store_detail_id')) {
			$store_id = Zend_Registry::get('store_detail_id');
			$form->populate(array('store_id' => $store_id));
		}	
		$category_id = $form->getValue('category_id');
		$route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
		$this->view->route = $route;		
		if ($category_id != '') {
			$category = new Socialstore_Model_DbTable_Customcategories();
			$select = $category -> select() -> where('store_id = ?', $store_id)-> where('parent_category_id = ?', $category_id);
			$count = $category -> fetchRow($select);
			if ($count) {
				$row = $category->find($category_id)->current();
				$this->view->level = $row->level - 1;
			}
		}
	}

}
