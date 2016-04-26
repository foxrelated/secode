<?php

class Socialstore_MyFavouriteProductController extends Core_Controller_Action_Standard{
	public function init(){
		// private page
		
		Zend_Registry::set('active_menu','socialstore_main_myfavouriteproduct');
	}
	
	public function indexAction(){
	if(!$this -> _helper -> requireUser() -> isValid()){
			return ;
		}
		$this->_helper->content
         ->setNoRender()
           ->setEnabled()
            ; 
        
        
	}
	
	public function favouriteAction(){
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
		if ($product->isFavourited($user_id)) { Socialstore_Api_Favourite::getInstance()->deleteFavouriter($user_id, $product_id);
			$this->view->favourite = 0;
			$this->view->text = Zend_Registry::get('Zend_Translate')->_('Favourite');
		}
		else { Socialstore_Api_Favourite::getInstance()->addFavouriter($user_id, $product_id);
			$this->view->favourite = 1;
			$this->view->text = Zend_Registry::get('Zend_Translate')->_('Unfavourite');
		}
	}
	
}
