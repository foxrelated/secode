<?php
class Socialstore_ProductDiscountController extends Core_Controller_Action_Standard {
	
	public function init(){
		
		Zend_Registry::set('active_menu','socialstore_main_mystore');
		Zend_Registry::set('STOREMINIMENU_ACTIVE','my-products');
		$viewer = Engine_Api::_()->user()->getViewer();
		if(!$this -> _helper -> requireUser() -> isValid()){
			return ;
		}
		if( (!$this -> _helper -> requireAuth() -> setAuthParams('social_store', $viewer, 'store_view')) || (!$this -> _helper -> requireAuth() -> setAuthParams('social_product', $viewer, 'product_view'))) 
		{
      		return;
    	}
    	$store = Engine_Api::_() -> getDbTable('SocialStores', 'Socialstore') -> getStoreByOwnerId($viewer -> getIdentity());
    	if (!is_object($store)) {
    		return;
    	}
    	Zend_Registry::set('MYSTORE_ID', $store->store_id);
    	$headScript = new Zend_View_Helper_HeadScript();
		$headScript -> appendFile('application/modules/Socialstore/externals/scripts/jquery-1.6.1.min.js');
		$headScript -> appendFile('application/modules/Socialstore/externals/scripts/jquery-ui-1.8.16.custom.min.js');
	}
	
	public function indexAction(){
		
		$this->view->product_id = $this->_getParam('product_id');
	}
	
	public function discountAction(){
		$product_id = $this->_getParam('product_id');
		$product = Engine_Api::_()->getItem('social_product', $product_id);
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity() != $product->owner_id) {
			$this->view->invalid = 1;
		}
		$params = $this -> _getAllParams();
		//print_r($params);die;
		if (isset($params['discount']) && $params['discount'] != '') {
			$discount = $params['discount'];
			$product->deleteDiscounts();
			$float_validator = new Zend_Validate_Float();
			$int_validator = new Zend_Validate_Int();
			foreach ($discount as $disc) {
				if ($disc['quantity'] <= 1 || $disc['price'] <= 0 || !$float_validator->isValid($disc['price']) || !$int_validator->isValid($disc['quantity'])) {
					continue;
				}
				$product->addDiscount($disc['quantity'], $disc['price']);
			}
		}
		$this->view->product = $product;
		$this->view->discounts = $product->getDiscount();
	}
}