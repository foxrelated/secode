<?php 

class Socialstore_Widget_PaymentMenuController extends Engine_Content_Widget_Abstract{
	public function indexAction(){
		$active_menu = "info";
		
		if(Zend_Registry::isRegistered('PAYMENTMENU_ACTIVE')){
			$active_menu = Zend_Registry::get('PAYMENTMENU_ACTIVE');
		}
				
		$this->view->review = 0;
		$this->view->checkout = 0;
		$this->view->id = 0;
		
		if (Zend_Registry::isRegistered('payment_review')) {
			$this->view->review = 1;
		}
		
		if (Zend_Registry::isRegistered('order_id')) {
			$this->view->checkout = 1;
			$this->view->id = $order_id = Zend_Registry::get('order_id');
			$order = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
			if($order->noShipping()){
				$this->setNoRender();	
			}
			
		}else{
			$this->setNoRender();
			
		}
				
		if (Zend_Registry::isRegistered('use_for_shipping')) {
			$this->view->use_for_shipping = Zend_Registry::get('use_for_shipping');
		}
				
		$this->view->active_menu =  $active_menu;
	}
}
