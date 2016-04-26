<?php

class Socialstore_AdminOrderController extends Core_Controller_Action_Admin 
{
	
	public function init(){
		parent::init();
		Zend_Registry::set('admin_active_menu', 'socialstore_admin_main_order');
	}

	public function indexAction(){
		
  		
      	$page = $this->_getParam('page',1);
    	$this->view->form = $form = new Socialstore_Form_Admin_Order_Search();
		$values = array();  
    	if ($form->isValid($this->_getAllParams())) {
    		$values = $form->getValues();
    	
    	}
    	$limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.page', 10);
    	$values['limit'] = $limit;
    	$values['paytype_id'] = 'shopping-cart';
    	$this->view->paginator = Socialstore_Api_Order::getInstance()->getOrdersPaginator($values); 
    	
    	$this->view->paginator->setCurrentPageNumber($page);
    	$this->view->formValues = $values; 
  	
	}
  	
  	public function orderDetailAction() {
  		
  		$order_id = $this->_getParam('order_id');
  		$Order = new Socialstore_Model_DbTable_Orders;
  		$order = $Order->getByOrderId($order_id);
  		$order_items = $order->getItems();
  		$this->view->order_id = $order_id;
		$this->view->order_items = $order_items;
		$billing = $order->getBillingAddress();
		$address = Engine_Api::_()->getApi('shipping','socialstore')->getAddressString($billing->value);
		$this->view->billing = $address;
		$Packages = new Socialstore_Model_DbTable_ShippingPackages;
		$Items = new Socialstore_Model_DbTable_OrderItems;
		$this->view->item_model = $Items;
		$ShippingAddresses = new Socialstore_Model_DbTable_ShippingAddresses;
		$packages = $Packages->getPackagesByOrder($order_id);
		$temp_packages = $packages->toArray();
		$order_packages = array();
		
		/**
		 * $order_packages[] = array (
		 * 						shippingadress_id => x,
 		 *						products => array (
 	 	 *									 orderitem_id => array (
	 	 *										 				 product_id => y, 
	 	 *														 quantity => z, 
	 	 * 														 total_amount => t, 
	 	 * 														 shipping_amount => s
	 	 * 													 ), .....
	 	 * 									),
		 * 						shipping_cost => m 
		 */
		
		foreach ($temp_packages as $package) {
			$shipping_packages = array();
			$shipping_packages['shippingaddress_id'] = $ShippingAddresses->getShippingAddressString($package['shippingaddress_id']);
			$product_array = array();
			$orderitems = $Items->getPackageItems($package,$order_id);
			foreach ($orderitems as $orderitem) {
				$product_array[$orderitem->orderitem_id] = array(
															'product_id' => $orderitem->object_id,
															'quantity' => $orderitem->quantity,
															'total_amount' => $orderitem->total_amount,
															'shipping_amount' => round($orderitem->shipping_amount + $orderitem->handling_amount,2)
														);
			}
			$shipping_packages['products'] = $product_array;
			$shipping_packages['shipping_cost'] = round($package['shipping_cost'] + $package['handling_cost'],2);
			$order_packages[] = $shipping_packages;
		}
		$this->view->packages = $order_packages;
		$this->view->order = $order;
  	}
}
