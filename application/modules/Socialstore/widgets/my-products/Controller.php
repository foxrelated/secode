<?php
class Socialstore_Widget_MyProductsController extends Engine_Content_Widget_Abstract
{
	public function indexAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.product.page', 10);
		$this->view->items_per_page = $items_per_page;
		$params['page'] = $request -> getParam('page');
		$this -> view -> user_id = $user_id = $viewer -> getIdentity();
		
		$params['store_id'] = Zend_Registry::get('store_id');
		$this -> view -> paginator = $paginator = Engine_Api::_()->getApi('product','Socialstore')->getProductsPaginator($params);
		$paginator->setItemCountPerPage($items_per_page);
		
	}

}
