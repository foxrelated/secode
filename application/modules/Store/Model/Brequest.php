<?php

class Store_Model_Brequest extends Store_Model_Item_Abstract {

	protected $_type = 'store_brequest';

	protected $_searchTriggers = false;
	
	public function getOrder() {
		return Engine_Api::_()->getItem('store_order', $this->order_id);
	}
	
	public function getOrderItem() {
		return Engine_Api::_()->getItem('store_orderitem', $this->order_item_id);
	}
	
	public function getProduct() {
		return Engine_Api::_()->getItem('store_product', $this->product_id);
	}
	
	public function getProducts() {
		$params = array();
		$params['owner'] = true;
		$params['user_id'] = $this->user_id;
		$params['quantity'] = true;
		$params['ids'] = $this->product_ids;
	    $table = Engine_Api::_()->getDbTable('products', 'store');
		$select = $table->getSelect($params);
		$products = $table->fetchAll($select);
		return $products;
	}
	
	public function Dismiss() {
		$order = $this->getOrder();
		if ($order) {
			$order->status = 'declined';
			$order->save();
		}
		$orderitem = $this->getOrderItem();
		if ($orderitem) {
			$orderitem->status = 'declined';
			$orderitem->save();
		}
		$this->status = 'dismiss';
		$this->save();
	}
	
	public function Approve() {
		$order = $this->getOrder();
		if ($order) {
			$order->status = 'shipping';
			$order->save();
		}
		$orderitem = $this->getOrderItem();
		if ($orderitem) {
			$orderitem->status = 'shipping';
			$orderitem->save();
		}
		$this->status = 'approve';
		$this->save();
		$requester = $this->getOwner();
		$owner = $this->getProduct()->getOwner();
		if ($this->credit) {
			$totalCredits = Engine_Api::_()->store()->getCredits($order->item_amt + $order->tax_amt + $order->shipping_amt);
			$confirm_id = Engine_Api::_()->credit()->buyProducts($this->getOwner(), $order->ukey, (-1)*$totalCredits);
			$params = array(
				'status' => 'completed', 'ukey' => $order->ukey, 'confirm_id' => $confirm_id
			);
			$gateway = Engine_Api::_()->getItem('store_gateway', $order->gateway_id);
			$plugin = $gateway->getPlugin();
			$status = $plugin->onCartTransactionReturn($order, $params);
			
			$ownerBalance = Engine_Api::_()->getItem('credit_balance', $owner->getIdentity());
			if (!$ownerBalance) {
				$ownerBalance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
				$ownerBalance->balance_id = $owner->getIdentity();
				$ownerBalance->save();
			}
			$ownerBalance->setCredits($totalCredits);
						$actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');    		$logTable = Engine_Api::_()->getDbTable('logs', 'credit');			$action = $actionTypes->getActionType('sell_products');			if ($action) {				$row = $logTable->createRow();			    $row->user_id = $owner->getIdentity();			    $row->action_id = $action->action_id;			    $row->credit = $totalCredits;			    $row->object_type = $this->getProduct()->getType();			    $row->object_id = $this->getProduct()->getIdentity();			    $row->creation_date = new Zend_Db_Expr('NOW()');				    $row->save();			}
		}
		else {
			$transactionsTable = Engine_Api::_()->getDbtable('transactions', 'store');
			$db = $transactionsTable->getAdapter();
			$db->beginTransaction();

			try {
				$transactionsTable->insert(array(
					'item_id'                => $order->item_id,
					'item_type'              => $order->item_type,
					'order_id'               => $order->order_id,
					'user_id'                => $order->user_id,
					'timestamp'              => new Zend_Db_Expr('NOW()'),
					'state'                  => 'completed',
					'gateway_id'             => 99,
					'gateway_transaction_id' => $order->ukey,
					'gateway_fee'            => 0.00,
					'amt'                    => $order->total_amt,
					'currency'               => $order->currency,
					'via_credits'            => 0,
					'token'                  => $order->token
				));
				
				$table = Engine_Api::_()->getDbTable('orders', 'store');
				$itemsTable = Engine_Api::_()->getDbTable('orderitems', 'store');
				$detailsTbl = Engine_Api::_()->getDbTable('details', 'store');
				$shipping_details = Engine_Api::_()->getDbTable('details', 'store')->getDetails($owner);
				$gateway_id = 99;
				$settings = Engine_Api::_()->getDbTable('settings', 'core');
				$currency = $settings->getSetting('payment.currency', 'USD');
				if (null == ($location_id = $detailsTbl->getDetail($owner, 'state'))) {
					$location_id = $detailsTbl->getDetail($owner, 'country');
				}
				foreach ($this->getProducts() as $product) {
					$data = array(
					  'user_id' => $owner->getIdentity(),
					  'gateway_id' => $gateway_id,
					  'item_type' => $product->getType(),
					  'item_id' => $product->getIdentity(),
					  'item_amt' => 0,
					  'tax_amt' => (double) $product->getTax(),
					  'shipping_amt' => (double)$product->getShippingPrice($location_id),
					  'total_amt' => (double) $product->getTax() + (double)$product->getShippingPrice($location_id),
					  'commission_amt' => 0,
					  'currency' => $currency,
					  'shipping_details' => $shipping_details,
					  'via_credits' => 0,
					  'offer_id' => 0,
					  'token' => null,
					  'status' => 'shipping',
					  'payment_date' => date('Y-m-d H:i:s')
					);
					/**
					 * @var $order Store_Model_Order
					 */
					$pOrder = $table->createRow();
					$pOrder->setFromArray($data);
					$pOrder->save();
					
					$itemsTable->delete(array('order_id = ?' => $pOrder->getIdentity()));
					$data = array(
					  'page_id' => $product->page_id,
					  'order_id' => $pOrder->getIdentity(),
					  'item_id' => $product->getIdentity(),
					  'item_type' => $product->getType(),
					  'name' => $product->getTitle(),
					  'params' => '[]',
					  'qty' => 1,
					  'item_amt' => 0,
					  'tax_amt' => (double) $product->getTax(),
					  'shipping_amt' => (double)$product->getShippingPrice($location_id),
					  'commission_amt' => 0,
					  'total_amt' => (double) $product->getTax() + (double)$product->getShippingPrice($location_id),
					  'currency' => $currency,
					  'via_credits' => 0,
					  'status' => 'shipping',
					);
					$orderItem = $itemsTable->createRow();
					$orderItem->setFromArray($data);
					$orderItem->save();
					
					$transactionsTable->insert(array(
						'item_id'                => $pOrder->item_id,
						'item_type'              => $pOrder->item_type,
						'order_id'               => $pOrder->order_id,
						'user_id'                => $pOrder->user_id,
						'timestamp'              => new Zend_Db_Expr('NOW()'),
						'state'                  => 'completed',
						'gateway_id'             => 99,
						'gateway_transaction_id' => $pOrder->ukey,
						'gateway_fee'            => 0.00,
						'amt'                    => $pOrder->total_amt,
						'currency'               => $pOrder->currency,
						'via_credits'            => 0,
						'token'                  => $pOrder->token
					));
				}
				
				$db->commit();
			} catch (Exception $e) {
				$db->rollBack();
				print_log($e);
				throw $e;
			}
		}
		
		$product = $this->getProduct();
		if ($product->type == 'simple' && $product->quantity > 0) {
			$quantity = $product->quantity;
			$product->quantity = $quantity-1;
			$product->save();
		}
		
		if (!$this->credit) {
			foreach ($this->getProducts() as $product) {
				if ($product->type == 'simple' && $product->quantity > 0) {
					$quantity = $product->quantity;
					$product->quantity = $quantity-1;
					$product->save();
				}
			}
		}
		
		$notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
		$notificationTable->addNotification($requester, $owner, $this->getProduct(), 'store_request_approved');
		
		Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner, 'store_brequest_complete', array(
            'order_details' => $order->getDetails()
      	));
		
		Engine_Api::_()->getApi('mail', 'core')->sendSystem($requester, 'store_brequest_complete', array(
            'order_details' => $this->getDetails()
      	));
	}

    	// USED FOR INFORMING VENDOR ABOUT THIS ORDER

	public function getDetails()
	{
        /**
         * @var $translate Zend_Translate
         * @var $item      Store_Model_Orderitem
         */
        $translate = Zend_Registry::get('Zend_Translate');
        $details = "";
		
		if ($this->credit) {
			$details .= $translate->_('Deducted Amount') . ": OGV " . $this->credit_value;
		}
		else {
			$checkShippable = false;
			$shipping_amt = 0;
			$tax_amt = 0;
			$owner = $this->getProduct()->getOwner();
			$detailsTbl = Engine_Api::_()->getDbTable('details', 'store');
			
			$shipping_details = Engine_Api::_()->getDbTable('details', 'store')->getDetails($owner);
			if (!empty($details) && $details['c_location']) {
		      $details['country'] = $details['c_country'];
		      $details['state'] = $details['c_state'];
		    } else {
		
		      if (!empty($details['location_id_1']) && null != ($country = Engine_Api::_()->getDbTable('locations', 'store')->findRow($details['location_id_1']))) {
		        $details['country'] = $country->location;
		      }
		      if (!empty($details['location_id_2']) && null != ($state = Engine_Api::_()->getDbTable('locations', 'store')->findRow($details['location_id_2']))) {
		        $details['state'] = $state->location;
		      }
		    }
			
			if (null == ($location_id = $detailsTbl->getDetail($owner, 'state'))) {
		      	$location_id = $detailsTbl->getDetail($owner, 'country');
		    }
        	foreach ($this->getProducts() as $item) {

                if (!$item->type == 'digital') $checkShippable = true;

                $product = "
			    " . $translate->_('What to send:') . "<br>
			    " . $translate->_('Product') . ": " . $item->getTitle() . "
			    " . $translate->_('Price') . ": OGV " . $item->getPrice() . "
			    " . $translate->_('STORE_Quantity') . ": " . '1'; $product .= "
			    " . $translate->_('Parameters') . ": " . Engine_Api::_()->store()->params_string(array());

                		    	$details .= $product . "
			    ";
				$tax_amt += (double) $item->getTax();
            		    }		
            		    	$details .= "
			    " . $translate->_('Tax Amount') . ": " . $tax_amt; 
				$details .= "<br>

			    " . $translate->_("Where to send:") . "<br>

			    " . $translate->_("Name") . " - " . $shipping_details['first_name'] . ' ' . $shipping_details['last_name'] . "
			    " . $translate->_("Email") . " - " . $shipping_details['email'] . "
			    " . $translate->_("Phone") . " - " . $shipping_details['phone_extension'] . " - " . $shipping_details['phone'] . "
			    " . $translate->_("Country") . " - " . $shipping_details['country'] . "
			    " . $translate->_("State") . " - " . $shipping_details['state'] . "
			    " . $translate->_("Zip Code") . " - " . $shipping_details['zip'] . "
			    " . $translate->_("City") . " - " . $shipping_details['city'] . "
			    " . $translate->_("STORE_Address Line") . " - " . $shipping_details['address_line_1'] . "
			    " . $translate->_("STORE_Address Line 2") . " - " . $shipping_details['address_line_2'];		
		}

                 return $details;
	}
	
	public function getHref($params = array()) {
		$params = array_merge(array(
		  'route' => 'store_request',
		  'reset' => true,
		  'id' => $this->product_id,
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);

		return Zend_Controller_Front::getInstance()->getRouter()
		  ->assemble($params, $route, $reset);
	}
	
	public function getTitle() {
		$product = $this->getProduct();
		if (!$product) return '';
		return $product->getTitle();
		
	}
}
