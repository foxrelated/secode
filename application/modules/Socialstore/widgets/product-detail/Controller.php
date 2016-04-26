<?php

class Socialstore_Widget_ProductDetailController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $viewer = Engine_Api::_()->user()->getViewer();
		if (Zend_Registry::isRegistered('product_detail_id') ) {
			$product_id = Zend_Registry::get('product_detail_id');
		}
		else {
			$this->setNoRender(true);
			return;
		}
		
		$product = Engine_Api::_()->getItem('social_product', $product_id);
		if ($viewer->getIdentity() == 0) {
      		
      		$isAdmin = 0;
      	}
		else {
      		$level = Engine_Api::_()->getItem('authorization_level', $viewer->level_id);
	    	if( in_array($level->type, array('admin', 'moderator')) ) {
	      		$isAdmin = 1;
	    	}
	    	else {
	    		$isAdmin = 0;
	    	}
		}
		if ($product->deleted == 1 || (($viewer->getIdentity() != $product->owner_id || $isAdmin == 0) && ($product->view_status == "hide" || $product->approve_status != "approved"))) {
			$this->view->notViewable = 1;
		}
		else {
			$this->view->notViewable = 0;
		}
		
		if($product->photo_id) {
        	$this->view->main_photo = $product->getPhoto($product->photo_id);
      	}
      	
      	$discount_price = $product->getDiscountPrice();
      	
      	if ($discount_price == 0) {
      		$this->view->discount = 0;
      	}
      	else {
      		$this->view->discount = $discount_price;
      	};
      	if ($product->checkStock()) {
      		$this->view->stock = 1;
      	}
      	else {
      		$this->view->stock = 0;
      	}
    	$this->view->product = $product;
    	$this->view->viewer = $viewer;
		if (!$viewer->getIdentity()){
    		$this->view->can_rate = $can_rate = 0;
    	}        
    	else{
    		$this->view->can_rate = $can_rate = Engine_Api::_()->getApi('product','Socialstore')->canRate($product,$viewer->getIdentity());
    	}
    	$route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
    	$this->view->route = $route;
	 	$owner = Engine_Api::_()->getItem('user', $product->owner_id);
    	if( !$owner->isSelf($viewer) ) {
    		$product->view_count++;
    		$product->save();
     	}
    	      // album material
		$this->view->album = $album = $product->getSingletonAlbum();
		$this->view->paginator = $paginator = $album->getCollectiblesPaginator();
		$paginator->setCurrentPageNumber($this->_getParam('page', 1));
		$paginator->setItemCountPerPage(100);
		$options = $product->getOptions();
		$this->view->options = $options;
		
	}
}
