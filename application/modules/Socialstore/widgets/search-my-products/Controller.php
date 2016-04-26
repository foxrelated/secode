<?php

class Socialstore_Widget_SearchMyProductsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->form = $form = new Socialstore_Form_Product_MyProductSearch();
		if (Zend_Registry::isRegistered('store_id')) {
			$store_id = Zend_Registry::get('store_id');
			$form->populate(array('store_id' => $store_id));
		}	
		$category_id = $form->getValue('category_id');
		$route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
		$this->view->route = $route;
		if ($category_id != '') {
			$category = new Socialstore_Model_DbTable_Storecategories();
			$select = $category -> select() -> where('parent_category_id = ?', $category_id);
			$count = $category -> fetchRow($select);
			if ($count) {
				$row = $category->find($category_id)->current();
				$this->view->level = $row->level - 1;
				
			}
		}
	}

}
