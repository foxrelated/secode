<?php
class Socialstore_Widget_MyFavouriteProductsController extends Engine_Content_Widget_Abstract {
	
	public function indexAction() {

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$items_per_page = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('store.product.page', 10);
		$this -> view -> items_per_page = $items_per_page;
		$params['page'] = $request -> getParam('page');
		$this -> view -> user_id = $user_id = $viewer -> getIdentity();
		$params['user_id'] = $user_id;
		$this -> view -> paginator = $paginator = Socialstore_Api_Favourite::getInstance() -> getFavouritedProductsPaginators($params);
		$paginator -> setItemCountPerPage($items_per_page);
		$this->view->className = "layout_socialstore_my_favourite_products";
	}

}
