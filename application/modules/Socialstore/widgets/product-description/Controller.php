<?php

class Socialstore_Widget_ProductDescriptionController extends Engine_Content_Widget_Abstract {
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
			$this->setNoRender(true);
		}
		else {
			$this->view->notViewable = 0;
		}
    	$this->view->product = $product;
    	$this->view->viewer = $viewer;
	}
}
