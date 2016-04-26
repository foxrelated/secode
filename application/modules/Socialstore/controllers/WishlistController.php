<?php
class Socialstore_WishlistController extends Core_Controller_Action_Standard{
	
	public function addToWishlistAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$product_id = $this->_getParam('product_id');
		if ($product_id != '') {
			Zend_Registry::set('tempProductID', $product_id);
		}
		if ($viewer->getIdentity() == 0) {
			$this->view->signin = 0;
		}
		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$product =  Engine_Api::_()->getItem('social_product', Zend_Registry::get('tempProductID'));
		if ($product->addedToWishlist($user_id)) { 
			Socialstore_Api_Wishlist::getInstance()->removeFromWishlist($user_id, $product_id);
			$this->view->wishlist = 0;
			$this->view->text = Zend_Registry::get('Zend_Translate')->_('Add to Wishlist');
		}
		else { Socialstore_Api_Wishlist::getInstance()->addToWishlist($user_id, $product_id);
			$this->view->wishlist = 1;
			$this->view->text = Zend_Registry::get('Zend_Translate')->_('Remove from Wishlist');
		}
	}
	
	public function removeFromWishlistAction() {
		
	}
}