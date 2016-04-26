<?php

class Socialstore_PaymentController extends Core_Controller_Action_Standard {
	
	
	public function init(){
		Zend_Registry::set('active_menu','socialstore_main_mycart');
	}
	
	/**
	 * @return Zend_Log
	 */
	public function getLog($filename='store.notify.log'){
		$writer =  new Zend_Log_Writer_Stream(APPLICATION_PATH .'/temporary/log/'.$filename);
		return new Zend_Log($writer);
	}

	public function indexAction() {
		$Orders = new Socialstore_Model_DbTable_Orders;
		$order  = $Orders->fetchNew();
		$order->save();
	}

	protected function _isValidProcess(){
		$order_id =  $this->_getParam('id');
		$order = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		
		if(!is_object($order)){
			$this->_forward('order-notfound');
			return false;
		}
		
		if(!is_string($order->getPaytype())){
			$this->_forward('paytype-notfound');
			return false;
		}
		
		// load paytype object.
		
		return true;
	}
	
	public function gatewayAction() {
		Zend_Registry::set('PAYMENTMENU_ACTIVE','payment-method');
		Zend_Registry::set('order_id', $this->_getParam('id'));
		$form =  $this->view->form = new Socialstore_Form_Payment_Gateway;
	}
	
	protected function getBaseUrl(){
		$baseUrl= Engine_Api::_()->getApi('settings','core')->getSetting('store.baseUrl',null);
		if(APPLICATION_ENV =='development'){
			$request =  Zend_Controller_Front::getInstance()->getRequest();
			$baseUrl = sprintf('%s://%s', $request->getScheme(), $request->getHttpHost());
			Engine_Api::_()->getApi('settings','core')->setSetting('store.baseUrl',$baseUrl);
		}
		return $baseUrl;
	}
	
	public function processAction() {
		Zend_Registry::set('PAYMENTMENU_ACTIVE','payment-method');
		$order_id =  $this->_getParam('id');
		$viewer = Engine_Api::_()->user()->getViewer();
		$order = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		if ($order->guest_id == 0 && $order->owner_id != $viewer->getIdentity()) {
			$this->_forward('order-notfound');
			return;
		}
		if(!is_object($order)){
			$this->_forward('order-notfound');
			return ;		
		}
		
		if(!is_string($order->getPaytype())){
			$this->_forward('paytype-notfound');
			return ;
		}
		
		if (!$order->noShipping() && !is_object($order->getShippingAddress())){			
			$this -> _helper -> redirector -> gotoRoute(array('controller'=>'payment','action'=>'shipping-address','id'=>$order_id),'socialstore_extended');
			return;
		}
		
		if (!$order->noBilling()  && !is_object($order->getBillingAddress())) {			
			$this -> _helper -> redirector -> gotoRoute(array('controller'=>'payment','action'=>'billing-address','id'=>$order_id),'socialstore_extended');
		}
		
		if ($order->paytype_id == 'shopping-cart' && !$order->noPackage()) {
			$this -> _helper -> redirector -> gotoRoute(array('controller'=>'payment','action'=>'manage-package','id'=>$order_id),'socialstore_extended');
		}
		
		if ($order->paytype_id == 'shopping-cart' && (!($this->_getParam('review') || $this->_getParam('review') != 'done'))) {		
			$this -> _helper -> redirector -> gotoRoute(array('controller'=>'payment','action'=>'review-order','id'=>$order_id),'socialstore_extended');
		}
		
		$Google = new Socialstore_Model_DbTable_Gateways;
		$checkGoogle = $Google->getConfig('google');
		if (@$checkGoogle['enabled'] == 1) {
			$payment =  new Socialstore_Payment(array('gateway'=>'google'));
			$request =  new Socialstore_Payment_Request('sale');
			// check payment method
			$router =  $this->getFrontController()->getRouter();
			$return_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-google','action'=>'process-success','id'=>$order_id),'default',true);
			$notify_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-google','action'=>'notify','id'=>$order_id,'gateway'=>'google'),'default',true);
			$cancel_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-google','action'=>'cancel','id'=>$order_id,'gateway'=>'google'),'default',true);
			$options =  array(
				'return_url'=>$this->getBaseUrl().  $return_url,
				'notify_url'=>$this->getBaseUrl().  $notify_url,
				'cancel_url'=>$this->getBaseUrl().  $cancel_url,
			);
			$request->setOrder($order);
			$request->setOptions($options);
			$response =  $payment->process($request);
			$this->view->cart = $response;
		}
		$gateway =  $_POST['gateway'];

		if(!$gateway){
			$this->_forward('gateway');
			return ;
		}		
		// load paytype object.
	if ($gateway != "google") {
			if ($gateway == 'paypaladaptive') {
				$gateway = 'paypal-adaptive';
			}
			$this->_helper->redirector->gotoSimple('process', 'payment-'.$gateway,'socialstore',array('id'=>$order_id));
		}
	}
	
	/**
	 * Manage Shipping Buyers
	 * 
	 */
	
	public function shippingAddressAction() {
		Zend_Registry::set('PAYMENTMENU_ACTIVE','shipping-address');
		
		$order_id =  $this->_getParam('id');
		$order = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($order->guest_id == 0 && $order->owner_id != $viewer->getIdentity()) {
			$this->_forward('order-notfound');
			return;
		}
		$this->view->order_id = $order_id;
		Zend_Registry::set('order_id', $order_id);
		$gateway =  $this->_getParam('gateway','');
		$ShippingAddresses = new Socialstore_Model_DbTable_ShippingAddresses;
		$shippingaddresses = $ShippingAddresses->getShippingAddresses($order_id);
		$this->view->addresses = $shippingaddresses;
		
		$order = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		$shippingaddress = $ShippingAddresses->getFormAddress($order_id);
		$form = $this->view->form = new Socialstore_Form_Payment_Shipping();
		if (count($shippingaddress) < 1) {
			$shippingaddress = $ShippingAddresses->createRow();
		}
		$req = $this->getRequest();
		if($req -> isGet()) {
			$form->populate((array)Zend_Json::decode($shippingaddress->value));
			return;
		}
		$post = $this -> getRequest() -> getPost();
		
		if(!$form -> isValid($post)) {
			return ;
		}

		$values = $form -> getValues();
		$use_for_billing = '';
		if (isset($values['use_for_billing']) && $values['use_for_billing'] == 1){
			$use_for_billing = 1;
		}
		unset($values['use_for_billing']);
		$data_encode = Zend_Json::encode($values);
		if ($use_for_billing == 1){
			$BillingAddresses = new Socialstore_Model_DbTable_BillingAddresses;
			$billing = $BillingAddresses->getBillingItem($order_id);
			if (count($billing) > 0) {
				$billing->value = $data_encode;
				$billing->save();	
			}
			else {
				$new_billing = $BillingAddresses->createRow();
				$new_billing->order_id = $order_id;
				$new_billing->value = $data_encode;
				$new_billing->save();
			}
		}
		$shippingaddress->order_id = $order_id;
		$shippingaddress->value = $data_encode;
		$shippingaddress->creation_date = date('Y-m-d H:i:s');
		$shippingaddress->is_form = 1;
		$shippingaddress->save();
		Zend_Registry::set('use_for_billing',$use_for_billing);
		$this -> _helper -> redirector -> gotoRoute(array('controller'=>'payment','action'=>'billing-address','id'=>$order_id,'reuse'=>$use_for_billing,'gateway'=>$gateway),'socialstore_extended');
		
	}
	
	
	public function addShippingAddressAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$order_id = $this->_getParam('order_id');
		$this->_helper->layout->setLayout('default-simple');
    	$this->view->form = $form = new Socialstore_Form_Payment_Shipping();
		$form->removeElement('use_for_billing');
		$form->removeElement('addshippingaddress');
		$form->removeElement('addfrombook');
		if($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$values = $form -> getValues();
			$ShippingAddress = new Socialstore_Model_DbTable_ShippingAddresses;
			if (isset($values['use_for_billing'])) {
				unset($values['use_for_billing']);
			}
			$data_encode = Zend_Json::encode($values);
			$shippingaddress = $ShippingAddress->createRow();
			$shippingaddress->order_id = $order_id;
			$shippingaddress->value = $data_encode;
			$shippingaddress->creation_date = date('Y-m-d H:i:s');
			$shippingaddress->save();
			$this->view->headScript()->appendScript("window.parent.en4.socialstore.shipping.updateShippingList('".$order_id."')");
			$this->_forward('success', 'utility', 'core', array(
	       			'smoothboxClose' => 10,
	       			//'parentRefresh' => 10,
					'messages' => array('')
        	));			
		}
	}
	
	public function updateShippingListAction() {
		$order_id = $this->_getParam('order_id');
		$number = $this->_getParam('count',0);
		$ShippingAddress = new Socialstore_Model_DbTable_ShippingAddresses;
		if ($number == 0) {
			$shippingaddress = $ShippingAddress->getLatestAddress($order_id);
			$address = Zend_Json::decode($shippingaddress->value);
			$count = count((array)$address);
			$i = 0;
			$add = '';
			foreach ($address as $a) {
				$i++;
				if ($i == $count) {
					$add .= $a;
				}
				else {
					if ($a != '') {
						$a .= ', ';	
						$add .= $a;
					}
				}
			}
			$address_id = $shippingaddress->shippingaddress_id;
			$this -> view -> add = $add;
			$this->view->address_id = $address_id;
		}
		else {
			$shippingaddresses = $ShippingAddress->getLatestAddresses($order_id, $number);
			$ids = array();
			$adds = array();
			foreach ($shippingaddresses as $key=>$address) {
				$count = count((array)$address);
				$i = 0;
				$temp_address = array();
				$add = '';
				foreach ($address as $a) {
					$i++;
					if ($i == $count) {
						$add .= $a;
					}
					else {
						if ($a != '') {
							$a .= ', ';	
							$add .= $a;
						}
					}
				}
				$temp_address['id'] = $key;
				$temp_address['add'] = $add;
				$adds[] = $temp_address;
			}
			$this->view->adds = $adds;
		}
	}
	
	public function editShippingAddressAction() {
		$shipping_id = $this->_getParam('id');
		$this -> _helper -> layout -> setLayout('default-simple');
		$form = $this->view->form = new Socialstore_Form_Payment_Shipping();
		$form->removeElement('use_for_billing');
		$form->removeElement('addshippingaddress');
		$form->removeElement('addfrombook');
	    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	
	      $values = $form->getValues();
	      $db = Engine_Db_Table::getDefaultAdapter();
	      $db->beginTransaction();
	      try {
	        //$shippingmethod_id = $values["shippingmethod_id"];
	        $table  = new Socialstore_Model_DbTable_ShippingAddresses;
	        $select = $table -> select() -> where('shippingaddress_id = ?', "$shipping_id");
	        $row    = $table -> fetchRow($select);
			$data_encode = Zend_Json::encode($values);
	        $row->value = $data_encode;
	        $row->creation_date = date('Y-m-d H:i:s');
	        //Database Commit
	        $row->save();
	        $order_id = $row->order_id;
	        $db->commit();
	      } catch (Exception $e) {
	        $db->rollBack();
	        throw $e;
	      }
	      //Close Form If Editing Successfully
	      $this->view->headScript()->appendScript("window.parent.en4.socialstore.shipping.updateShippingList('".$order_id."')");
	      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'messages' => array('')));
	    }
	
	    // Generate and assign form
	    $table = new Socialstore_Model_DbTable_ShippingAddresses;
	    $select = $table->select()->where('shippingaddress_id = ?', "$shipping_id");
	    $address = $table->fetchRow($select);
	    $array_values = Zend_Json::decode($address->value);
	    $form->execute->setLabel('Edit Shipping Address');
	    $form->populate((array)$array_values);
	    
	    //Output
	    $this->renderScript('payment/add-shipping-address.tpl');
	}
	
	public function deleteShippingAddressAction() {
		$shipping_id = $this->_getParam('id');
		$form = $this->view->form = new Socialstore_Form_Payment_ShippingDelete();
	    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	    	$table = new Socialstore_Model_DbTable_ShippingAddresses;
			$address = $table->getAddress($shipping_id);
			$address->delete();
			$this->view->headScript()->appendScript("window.parent.en4.socialstore.shipping.deleteShippingList('".$shipping_id."')");
			$this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'messages' => array('')));
	    }
	    $this->renderScript('payment/delete-shipping-address.tpl');
	}
	
	public function addFromBookAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		$viewer = Engine_Api::_()->user()->getViewer();
		$order_id = $this->_getParam('order_id');
		$AddressBook = new Socialstore_Model_DbTable_Addressbooks;
		$addresses = $AddressBook->getShippingAddressBook($viewer->getIdentity(),$order_id);
		$this->view->addresses = $addresses;
		$params = $this -> _getAllParams();
		if(isset($params['submit'])) {
			$id_array = $params['address'];
			$ShippingAddress = new Socialstore_Model_DbTable_ShippingAddresses;
			$count = 0;
			foreach ($id_array as $id) {
				$count++;
				$values = $addresses[$id];
				$data_encode = Zend_Json::encode($values);
				$shippingaddress = $ShippingAddress->createRow();
				$shippingaddress->order_id = $order_id;
				$shippingaddress->value = $data_encode;
				$shippingaddress->creation_date = date('Y-m-d H:i:s');
				$shippingaddress->addressbook_id = $id;
				$shippingaddress->save();
				
			}
			$this->view->headScript()->appendScript("window.parent.en4.socialstore.shipping.updateShippingBook('".$order_id."',".$count.")");
			$this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'messages' => array('')));
		}
		$this->renderScript('payment/add-from-book.tpl');
	}
	public function billingAddressAction() {
		Zend_Registry::set('PAYMENTMENU_ACTIVE','billing-address');	
		$order_id =  $this->_getParam('id');
		$order = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($order->guest_id == 0 && $order->owner_id != $viewer->getIdentity()) {
			$this->_forward('order-notfound');
			return;
		}
		$this->view->order_id = $order_id;
		Zend_Registry::set('order_id', $order_id);
		$gateway =  $this->_getParam('gateway','');
		$BillingAddresses = new Socialstore_Model_DbTable_BillingAddresses;
		$billingaddress = $BillingAddresses->getBillingItem($order_id);
		$order = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		
		$form = $this->view->form = new Socialstore_Form_Payment_Billing();
		$form->removeElement('addshippingaddress');
		$form->removeElement('addfrombook');
		if (count($billingaddress) < 1) {
			$billingaddress = $BillingAddresses->createRow();
		}
		
		$req = $this->getRequest();
		if($req -> isGet()) {
			$form->populate((array)Zend_Json::decode($billingaddress->value));
			return ;
		}
		
		$post = $this -> getRequest() -> getPost();
		
		if(!$form -> isValid($post)) {
			return ;
		}
		$values = $form -> getValues();
		$data_encode = Zend_Json::encode($values);
		$billingaddress->order_id = $order_id;
		$billingaddress->value = $data_encode;
		$billingaddress->save();
		$this -> _helper -> redirector -> gotoRoute(array('controller'=>'payment','action'=>'manage-package','id'=>$order_id,'gateway'=>$gateway),'socialstore_extended');
		
	}	
	
	/**
	 * 
	 * End Mangage Shipping Buyers
	 */
	
	/**
	 * 
	 * Create Package Order
	 */
	
	public function managePackageAction() {
		Zend_Registry::set('PAYMENTMENU_ACTIVE','manage-package');
		$gateway =  $this->_getParam('gateway','');
		$order_id = $this->_getParam('id');
		Zend_Registry::set('order_id', $order_id);
		$order = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($order->guest_id == 0 && $order->owner_id != $viewer->getIdentity()) {
			$this->_forward('order-notfound');
			return;
		}
		$this->view->products = $products = $order->getProducts();
		$ShippingAddress = new Socialstore_Model_DbTable_ShippingAddresses;
		$shippings = $ShippingAddress->getDetailShippingAddresses($order_id);
		$this->view->shippings = $shippings;		
		$cart_items =  $order->getItems();
		$this->view->cart_items = $cart_items;
		$i = 0;
		$temp = array();
		foreach (@$cart_items as $item) {
			$i++;
			if ($item->options) {
				if (!(array_key_exists($item->options, $temp))) {
					$temp[$item->options][$item->object_id] = array ($i);
					$temp[$item->options][$item->object_id][] = $item->orderitem_id;
				}
				else {
					$temp[$item->options][$item->object_id][] = $i;
				}
			}
			else {
				if (!(array_key_exists('0', $temp))) {
					$temp[0] = array();
				}
				if (!(array_key_exists($item->object_id, $temp[0]))) {
					$temp[0][$item->object_id] = array($i);
					$temp[0][$item->object_id][] = $item->orderitem_id;
				}
				else {
					$temp[0][$item->object_id][] = $i;
				}
			}
		}
		$this->view->temp = $temp;
		$this->view->order_id = $order_id;
		$params = $this -> _getAllParams();
		if(isset($params['submit'])) {
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();
			try {
				$products = $this->_getParam('cartitem_qty');
				$Items = new Socialstore_Model_DbTable_OrderItems;
				$Product = new Socialstore_Model_DbTable_SocialProducts;
				$ShippingRules = new Socialstore_Model_DbTable_ShippingRules;
				$delete_item = $Items->getOldIds($order_id);
				foreach ($products as $product => $packages) {
					foreach ($packages as $package) {
						$old_item = $order->getItemByProOpt($product,$package['options']);
						$item_array = $old_item->toArray();
						unset($item_array['orderitem_id']);
						$product_object = Engine_Api::_()->getItem('social_product', $product);
						if ($product_object->product_type == 'downloadable') {
							$new_item = $Items->createRow($item_array);
							$quantity = $package['quantity'];
							$new_item->quantity = $quantity;
							$new_item -> commission_amount = round($new_item->item_commission_amount * $new_item -> quantity, 2);
							$new_item -> sub_amount = $new_item -> quantity * $new_item -> pretax_price;
							$new_item -> total_amount = $new_item -> sub_amount + $new_item -> tax_amount * $new_item -> quantity;
							$new_item -> seller_amount = $new_item -> total_amount - $new_item -> commission_amount;
							$new_item->save();
						}
						else {
							$address_id = $package['address'];
							$rule_id = $package['rule'];
							$quantity = $package['quantity'];
							$rule = $ShippingRules->getRuleById($rule_id);
							$new_item = $Items->createRow($item_array);
							$new_item->quantity = $quantity;
							if ($rule->order_minimum != 0) {
								$new_item->shippingaddress_id = $address_id;
								$new_item -> commission_amount = round($new_item->item_commission_amount * $new_item -> quantity, 2);
								$new_item -> sub_amount = $new_item -> quantity * $new_item -> pretax_price;
								$new_item -> total_amount = $new_item -> sub_amount + $new_item -> tax_amount * $new_item -> quantity;
								$new_item -> seller_amount = $new_item -> total_amount - $new_item -> commission_amount;
								$new_item->shippingrule_id = $rule_id;
								$new_item->save();
							}
							else {
								$order_cost = $rule->order_cost;
								if ($rule->cal_type == 'item') {
									$shipping_amount = $rule->type_amount * $quantity;
								}
								else {
									$product_item = $Product->getProduct($product);
									$weight = $product_item->getWeight();
									$shipping_amount = $rule->type_amount * $weight * $quantity;
								}
								if ($rule->handling_type == 'order') {
									$handling_amount = '0';
									$order_handling = '0';
								}
								else if ($rule->handling_type == 'item') {
									$order_handling = '0';
									if ($rule->handling_fee_type == 'fixed') {
										$handling_amount = $rule->handling_fee * $quantity;
									}
									else {
										$handling_amount = round($shipping_amount * $rule->handling_fee/100, 2);
									}
								}
								else {
									$handling_amount = 0;
									$order_handling = 0;
								}
								$new_item->shippingrule_id = $rule_id;
								$new_item->shipping_amount = $shipping_amount;
								$new_item->order_shipping_amount = $order_cost;
								$new_item->shippingaddress_id = $address_id;
								$new_item->handling_amount = $handling_amount;
								$new_item->order_handling_amount = $order_handling;
								$new_item -> commission_amount = round($new_item->item_commission_amount * $new_item -> quantity, 2);
								$new_item -> sub_amount = $new_item -> quantity * $new_item -> pretax_price;
								$new_item -> total_amount = $new_item -> sub_amount + $new_item -> tax_amount * $new_item -> quantity;
								$new_item -> seller_amount = $new_item -> total_amount - $new_item -> commission_amount;
								$new_item->save();
							}
						}
					}
				}
				foreach ($delete_item as $del) {
					$Items->deleteOrderItemById($del);
				}
				$order->saveInsecurity();
				
				/**
				 * 
				 * Create Shipping Package
				 * 
				 */
				
				$store_address_pairs = $Items->getPackages($order_id);
				$pairs = $store_address_pairs->toArray();
				$package_table = new Socialstore_Model_DbTable_ShippingPackages;
				$package_table->deleteOldIds($order_id);
				foreach ($pairs as $pair) {
					$package_order_cost = array();
					$package_cost = $Items->getPackageItems($pair,$order_id);
					$shipping_cost = '0';
					$handlings = '0'; 
					$package_handling_cost = '0';
					$count_product_in_order = '0';
					$category = array();
					foreach ($package_cost as $cost) {
						$shipping_cost+= $cost->shipping_amount;
						$package_order_cost[] = $cost->order_shipping_amount;
						$handlings+= $cost->handling_amount;
						$handling_fee = 0;
						$rule_id = $cost->shippingrule_id;
						$rule = $ShippingRules->getRuleById($rule_id);
						// Calculate Handling 
						if (is_object($rule) && $rule->handling_type == 'order') {
							$product = $Product->getProduct($cost->object_id);
							if (!array_key_exists($rule_id,$category)) {
								if ($rule->handling_fee_type == 'fixed') {
									$category[$rule_id]['fee'] = $rule->handling_fee;
								}
								else {
									$category[$rule_id]['rate'] = $rule->handling_fee;
								}
							}
						}
					}
					$package_handling_cost += $handlings;
					$order_cost = (max($package_order_cost));
					$shipping_cost += $order_cost;
					print_r($package_handling_cost);print_r('!!!');
					print_r($category);print_r('123123');
					foreach ($category as $cat) {
						print_r($cat);print_r('---');
						if (array_key_exists('fee',$cat)) {
							$package_handling_cost += $cat['fee'];
						}
						if (array_key_exists('rate',$cat)) {
							$package_handling_cost += round($cat['rate']/100 * $shipping_cost,2);
						}
					}

					$new_package = $package_table->createRow();
					$new_package->order_id = $order_id;
					$new_package->store_id = $pair['store_id'];
					$new_package->shippingaddress_id = $pair['shippingaddress_id'];
					$new_package->shipping_cost = $shipping_cost;
					$new_package->handling_cost = $package_handling_cost;
					$new_package->save();
				}
				$total_shipping = $package_table->getTotalShippingFee($order_id);
				$total_handling = $package_table->getTotalHandlingFee($order_id);
				$order->shipping_amount = $total_shipping;
				$order->handling_amount = $total_handling;
				$order->total_amount += $total_handling;
				$order->total_amount += $total_shipping;
				$order->save();
				$db -> commit();
			}
			catch (Exception $e) {
				$db -> rollBack();
				throw $e;
			}
			$this -> _helper -> redirector -> gotoRoute(array('controller'=>'payment','action'=>'review-order','id'=>$order_id,'gateway'=>$gateway),'socialstore_extended');
		}
	}
	
	public function getShippingMethodAction() {
		$store_id = $this->_getParam('store_id');
		$category_id = $this->_getParam('category');
		$address_id = $this->_getParam('address_id');
		$order_id = $this->_getParam('order_id');
		$rules = Engine_Api::_()->getApi('shipping', 'socialstore')->getRules($store_id,$address_id,$category_id,$order_id);
		if (!$rules) {
			$this->view->error = 1;
			$this->view->text = Zend_Registry::get('Zend_Translate')->_("There is no shipping method from this store!");
		}
		else {
			$this->view->error = 0;
			$rhis->view->none = Zend_Registry::get('Zend_Translate')->_("None");
			$this->view->rules = $rules;
		}
	}
	
	public function addPackageAction() {
		$orderitem_id = $this->_getParam('orderitem_id');
		$row_id = $this->_getParam('row_id');
		//$orderitem_id = $this->_getParam('orderitem_id');
		$this -> _helper -> layout -> setLayout('default-simple');
		$form = $this->view->form = new Socialstore_Form_Payment_Quantity();
		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
    		$values = $form->getValues();   
    		$quantity = $values['quantity'];
    		$string = Zend_Registry::get('Zend_Translate')->_('None');
    		$remove = Zend_Registry::get('Zend_Translate')->_('Remove');
			$translate_array = array();
			$translate_array['none'] = $string;
			$translate_array['remove'] = $remove;
			$translate_array['na'] = Zend_Registry::get('Zend_Translate')->_("N/A");
			$trans = Zend_Json::encode($translate_array);
    		$this->view->headScript()->appendScript("window.parent.en4.socialstore.packages.addPackage($orderitem_id,$row_id,'$trans',$quantity)");
	      	$this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'messages' => array('')));
	    } 
	    //Output
	    $this->renderScript('payment/add-package.tpl');
	}
	/**
	 * 
	 * End Create Package Order
	 */
	
	/**
	 * 
	 * Order Review
	 */
	
	public function reviewOrderAction() {
		Zend_Registry::set('PAYMENTMENU_ACTIVE','review-order');
		$order_id = $this->_getParam('id');
		$this->view->id = $order_id;
		Zend_Registry::set('order_id', $order_id);
		$viewer = Engine_Api::_()->user()->getViewer();
		$order = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		if ($order->guest_id == 0 && $order->owner_id != $viewer->getIdentity()) {
			$this->_forward('order-notfound');
			return;
		}
		$this->view->order = $order;
		if (Zend_Registry::isRegistered('review-done')) {
			Zend_Registry::set('review-done', 0);
		}
		$billing = $order->getBillingAddress();
		$address = Engine_Api::_()->getApi('shipping','socialstore')->getAddressString($billing->value);
		$this->view->billing = $address;
		$Packages = new Socialstore_Model_DbTable_ShippingPackages;
		$Items = new Socialstore_Model_DbTable_OrderItems;
		$ShippingAddresses = new Socialstore_Model_DbTable_ShippingAddresses;
		$packages = $Packages->getPackagesByOrder($order_id);
		$temp_packages = $packages->toArray();
		$order_packages = array();
		
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
		$form = $this->view->form =  new Socialstore_Form_Payment_OrderReview;	
		if($this->_request->isGet()){
			return ;
		}
		if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
			$this -> _helper -> redirector -> gotoRoute(array('controller'=>'payment','action'=>'process','id'=>$order_id,'review'=>'done'),'socialstore_extended');
		}
	}
	/**
	 * 
	 * End Order Review
	 */
	public function reviewAction(){
		if(!$this->_isValidProcess()){
			return ;
		}

		Zend_Registry::set('PAYMENTMENU_ACTIVE','payment-confirm');
		Zend_Registry::set('payment_review', 'payment_review');
		$form = $this->view->form =  new Socialstore_Form_Payment_Review;		
		// get result from review action
		$token =  $this->_getParam('token');
		$payer_id =  $this->_getParam('PayerID');
		$order_id = $this->_getParam('id');
		Zend_Registry::set('order_id', $order_id);
		$gateway =  'paypal';
		$baseUrl =  $this->getBaseUrl();
		
		
		
		if($this->_request->isGet()){
			return ;
		}

		if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
			
			// get the payment
			$payment= new Socialstore_Payment(array('gateway'=>$gateway));
			
			// set request order
			$request = new Socialstore_Payment_Request('sale');
			$order   = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
			$request->setOrder($order);
			
			// get notify url
			$router =  $this->getFrontController()->getRouter();
			$notify_url  = $router->assemble(array('module'=>'socialstore','controller'=>'payment-paypal','action'=>'notify','id'=>$order_id),'default',true);
			
			// check request option
			$options =  array(
				'token'=>$token,
				'payer_id'=> $payer_id,
				'notify_url'=>$baseUrl . $notify_url,
			);
			$request->setOptions($options);
		
			// process plugin
			$plugin =  $order->getPlugin();			
			try{
				// process request.
				$response =  $payment->process($request);
				// log response result.
				$this->getLog('store.response.log')->log(var_export($response->getOptions(),true), Zend_Log::DEBUG);
							
				// get payment status
				$status =  $response->getOption('payment_status');
				$status =  strtolower($status);
				
				// cucess result
				if($response->isSuccess()){
					// process plugin.
					if($status == 'pending'){
						$plugin->onPending();
					}else if($status == 'completed'){
						$plugin->onSuccess();
					}else if($status == 'cancel'){
						$plugin->onCancel();
					}else{
						$plugin->onFailure();
					}		
					return $this->_forward('process-success');	
				}
				else
				{
					// failture procss
					$plugin->onFailure();
					
					// foward to process error.
					$this->view->response =  $response;		
					$this->_forward('process-error');
				}
				
				/**
				 * clean current session
				 */
				$cart  = Socialstore_Api_Cart::getInstance()->escapeCurrentOrder();
				
			}catch(Exception $e){
				$this->getLog('store.error.log')->log($e->getMessage(), Zend_Log::ERR);
				// foward to process error.
				$this->view->response =  $response;		
				$this->_forward('process-error');	
			}
		}
	}
	public function removePackageAction() 
	{
		$orderitem_id = $this -> _getParam('orderitem-id');
		$order_id = $this -> _getParam('order-id');
		$order = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
		if($order && $orderitem_id)
		{
			$remove = new Socialstore_Model_DbTable_OrderItems;
			$order_item = $remove -> find($orderitem_id) -> current();
			$order -> removeItem($order_item);
			if(!$order -> getQty())
			{
				$session = new Zend_Session_Namespace(Socialstore_Api_Order::SESSION_NAME);
				$session -> order_id = null;
				$this -> _forward('success', 'utility', 'core', 
					array('smoothboxClose' => true, 
						'parentRedirect' => $this -> getFrontController() -> getRouter() -> assemble(array('module' => 'socialstore', 'controller' => 'my-cart', 'action' => 'index'), 'default', true), 
						'format' => 'smoothbox', 
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Remove package successfully.'))));
			}
			else {
				$this -> _forward('success', 'utility', 'core',
					 array('smoothboxClose' => 10, 
					 		'parentRefresh' => 10,
					 		'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Remove package successfully.'))));
			}
		}
		else {
			$this -> _forward('success', 'utility', 'core',
				 array('smoothboxClose' => 10, 
				 		'parentRefresh' => 10,
				 		'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Remove package unsuccessfully.'))));
		}
	}
}
