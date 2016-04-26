<?php

class Socialstore_MyOrdersController extends Core_Controller_Action_Standard{
	public function init(){
		// private page
		if(!$this -> _helper -> requireUser() -> isValid()){
			return ;
		}
		Zend_Registry::set('active_menu','socialstore_main_myorders');
	}
	public function indexAction(){
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this->view->viewer = $viewer;
		$page = $this->_getParam('page',1);
    	$this->view->form = $form = new Socialstore_Form_Admin_Order_Search();
		$form->removeElement('owner_name');
		$form->removeElement('search');
    	$values = array();  
    	if ($form->isValid($this->_getAllParams())) {
    		$values = $form->getValues();
    	
    	}
    	$limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.page', 10);
    	$values['limit'] = $limit;
    	$values['paytype_id'] = 'shopping-cart';
    	$values['user_id'] = $viewer->getIdentity();
    	$this->view->paginator = Socialstore_Api_Order::getInstance()->getOrdersPaginator($values); 
    	$this->view->paginator->setCurrentPageNumber($page);
    	$this->view->formValues = $values; 
	}
	
	public function orderDetailAction() {
  		
  		$order_id = $this->_getParam('order_id');
  		$Order = new Socialstore_Model_DbTable_Orders;
  		$order = $Order->getByOrderId($order_id);
  		$viewer = Engine_Api::_() -> user() -> getViewer();
  		if ($viewer->getIdentity() != $order->owner_id) {
  			return $this->_forward('no-permission');
  		}
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
		$output = $this->view->render('my-orders/order-detail.tpl');
		Zend_Registry::set('pdf_output', $output);
		//print_r($output);die;
  	}
  	
  	public function downpdfAction() {
		$view = Zend_Registry::get('Zend_View');
		$order_id = $this->_getParam('order_id');
  		$Order = new Socialstore_Model_DbTable_Orders;
  		$order = $Order->getByOrderId($order_id);
  		if (!$order->pdf_id) {
	  		$order_items = $order->getItems();
	  		$view->order_id = $order_id;
			$view->order_items = $order_items;
			$billing = $order->getBillingAddress();
			$address = Engine_Api::_()->getApi('shipping','socialstore')->getAddressString($billing->value);
			$view->billing = $address;
			$Packages = new Socialstore_Model_DbTable_ShippingPackages;
			$Items = new Socialstore_Model_DbTable_OrderItems;
			$view->item_model = $Items;
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
			$view->packages = $order_packages;
			$view->order = $order;
			$output = $view->render('my-orders/_order-detail-pdfoutput.tpl');
			$order->pdfrender($output);
  		}
		$path = $order->getPdfPath();
		if( file_exists($path) && is_file($path) ) {
			// Kill zend's ob
			while( ob_get_level() > 0 ) {
				ob_end_clean();
			}
			header("Content-Disposition: attachment; filename=" . $order->getIdentity().'.pdf', true);
			header("Content-Transfer-Encoding: Binary", true);
			header("Content-Type: application/force-download", true);
			header("Content-Type: application/octet-stream", true);
			//header("Content-Type: application/download", true);
			header("Content-Description: File Transfer", true);
			header("Content-Length: " . filesize($path), true);
			flush();

			$fp = fopen($path, "r");
			while( !feof($fp) )
			{
				echo fread($fp, 65536);
				flush();
			}
			fclose($fp);
		}

		exit(); // Hm....
  	}
   	 
  	public function noPermissionAction() {
  		
  	}
  	
  	public function refundAction() {
  		$form = $this->view->form = new Socialstore_Form_Refund();
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	  		$values = $form->getValues();
	  		$orderitem_id = $values['orderitem_id'];
	  		$OrderItem = new Socialstore_Model_DbTable_OrderItems;
	  		$order_item = $OrderItem->find($orderitem_id)->current();
	  		$order_item->refund_status = 1;
	  		$order_item->save();
	  		$product = $order_item->getObject();
	    	if (!is_object($product)) {
				throw new Zend_Exception('No product exists');
			}
	    	
			// Send Email to Seller
			
			$modelBilling = new Socialstore_Model_DbTable_Addresses;
			$select = $modelBilling->select()->where("order_id = ?", $order_item->order_id)->where("address_type = ?",'billing');
			$result = $modelBilling -> fetchRow($select);
			$seller = Engine_Api::_()->getItem('user', $product->owner_id)->email;
        	$sellerparams = array();
      		$sellerparams['product_title'] = $product->title;
      		$sellerparams['product_link'] = $product->getHref();
      		$store = Engine_Api::_()->getItem('social_store', $order_item->store_id);
      		$sellerparams['store_title'] = $store->title;
      		$sellerparams['store_link'] = $store->getHref();
        	$sellerparams['store_orderid'] = $order_item->order_id;
        	$sellerparams['buyer_name'] = $result->firstname.' '.$result->lastname;
			$sellerparams['buyer_email'] = $result -> email;
			$sellerparams['buyer_address'] = $result->street.' '.$result->city.' City, '.$result->country;
        	Engine_Api::_()->getApi('mail','Socialstore')->send($seller, 'store_refundseller',$sellerparams);
	  		
        	// Send Email to Buyer
        	
			$buyerparams = array();
			$buyerparams['store_orderid'] = $order_item->order_id;
			$buyerparams['product_title'] = $product->title;
      		$buyerparams['product_link'] = $product->getHref();
      		$buyerparams['store_title'] = $store->title;
      		$buyerparams['store_link'] = $store->getHref();
			Engine_Api::_()->getApi('mail','Socialstore')->send($buyerparams['buyer_email'], 'store_refundbuyer',$buyerparams);
      		
	  		$this->view->success = true;
		    $this->_forward('success', 'utility', 'core', array(
						'smoothboxClose' => 10, 
						'parentRefresh' => 10, 
						'messages' => array('')));
	    }	
	    
	    if (!$orderitem_id = $this->_getParam('orderitem_id')) {
      		throw new Zend_Exception('Refund not valid');
	    }

	    //Generate form
	    $form->populate(array('orderitem_id' => $this->_getParam('orderitem_id')));
	    
	    //Output
	    $this->renderScript('my-orders/form.tpl');
  	}

}
