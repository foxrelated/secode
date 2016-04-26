<?php

class Socialstore_Widget_ProductCategoriesController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$Model = new Socialstore_Model_DbTable_Storecategories;
		$select = $Model -> select();
		$select -> where('level = ? ', 1);
		$category = $Model->fetchAll($select);
		$this->view->category = $category;
	}
}
