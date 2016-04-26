<?php

class Socialstore_Widget_StoreInfoController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		
	    if (Zend_Registry::isRegistered('store_detail_id')) {
			$store_id = Zend_Registry::get('store_detail_id');
		}
		else {
			$this->setNoRender(true);
			return;
		}
	    if (Zend_Registry::isRegistered('product_detail_id')) {
			$product_id = Zend_Registry::get('product_detail_id');
		}
		$store = Engine_Api::_()->getItem('social_store', $store_id);
		
		if (!$store) {
			$this->setNoRender(true);
			return;
		}
		if($store->photo_id) {
        	$this->view->main_photo = $store->getPhoto($store->photo_id);
      	}
      	if ($viewer->getIdentity() == 0) {
      		
      		$this->view->isAdmin = 0;
      	}
      	else {
			$level = Engine_Api::_()->getItem('authorization_level', $viewer->level_id);
	    	if( in_array($level->type, array('admin', 'moderator')) ) {
	      		$this->view->isAdmin = 1;
	    	}
	    	else {
	    		$this->view->isAdmin = 0;
	    	}
      	}
      	$product = Engine_Api::_()->getItem('social_product', $product_id);
		if ($product->deleted == 1 || (($viewer->getIdentity() != $product->owner_id || $isAdmin == 0) && ($product->view_status == "hide" || $product->approve_status != "approved"))) {
			$this->setNoRender(true);
			return;
		};
    	$this->view->store = $store;
    	$this->view->viewer = $viewer;
		$this->view->viewer_id = $viewer->getIdentity();
		$owner = Engine_Api::_()->getItem('user', $store->owner_id);
    	if( !$owner->isSelf($viewer) ) {
    		$store->view_count++;
    		$store->save();
     	}
    	      // album material
		$this->view->album = $album = $store->getSingletonAlbum();
		$this->view->paginator = $paginator = $album->getCollectiblesPaginator();
		$paginator->setCurrentPageNumber($this->_getParam('page', 1));
		$paginator->setItemCountPerPage(100);
    	$this->view->store = $store;
    	$view = $this->view;
      	$view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
      	$this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($store);
	}
}
