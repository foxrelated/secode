<?php

class Socialstore_Widget_StoreProductCategoriesController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		if (Zend_Registry::isRegistered('store_detail_id')) {
			$store_id = Zend_Registry::get('store_detail_id');
			$Model = new Socialstore_Model_DbTable_Customcategories;
			$select = $Model -> select();
			$select -> where('store_id = ? ', $store_id) -> where('level = ? ', 1);
			$category = $Model->fetchAll($select);
			$this->view->category = $category;
		}
		else {
			$this->setNoRender(true);
		}
	}
}
