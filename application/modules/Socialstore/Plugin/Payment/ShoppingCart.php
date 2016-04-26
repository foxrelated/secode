<?php

class Socialstore_Plugin_Payment_ShoppingCart extends Socialstore_Plugin_Payment_Abstract {

	protected static $_baseUrl;
	
	public static function getBaseUrl(){
		if(self::$_baseUrl == NULL){
			$request =  Zend_Controller_Front::getInstance()->getRequest();
			self::$_baseUrl = sprintf('%s://%s', $request->getScheme(), $request->getHttpHost());
			
		}
		return self::$_baseUrl;
	}
	/**
	 * @param   string $type
	 * @return  string
	 */
	public function selfURL() 
    {
      return self::getBaseUrl();
    }
	
	public function getSuccessRedirectUrl(){
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$url =  $router->assemble(array('module'=>'socialstore','controller'=>'my-cart'),'default',true);
		return $url;
	}
	
	public function getCancelRedirectUrl(){
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$url =  $router->assemble(array('module'=>'socialstore','controller'=>'my-cart'),'default',true);
		return $url;
	}
	
	public function onSuccess() {
		$order = $this -> getOrder();
		$order -> payment_status = 'completed';
		$order -> save();
		
		
		 /**
         * Call Event from Affiliate
         */
         
        $module = 'ynaffiliate';
        $modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
			$mselect = $modulesTable->select()
			->where('enabled = ?', 1)
			->where('name  = ?', $module);
		$module_result = $modulesTable->fetchRow($mselect);
		$params = array();
		if(count($module_result) > 0)	{
			$params['module'] = 'socialstore';
			$params['user_id'] = $order->owner_id;
			$params['rule_name'] = 'buy_product';
			$params['total_amount'] = $order->total_amount;
			$params['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.currency', 'USD');
        	Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
		}
        
        /**
         * End Call Event from Affiliate
         */
		// User credit integration
        $module = 'yncredit';
        $mselect = $modulesTable->select()->where('enabled = ?', 1)->where('name  = ?', $module);
        $module_result = $modulesTable->fetchRow($mselect);
        if(count($module_result) > 0)    
        {
           $params['user_id'] = $order->owner_id;
           $params['rule_name'] = 'social_product_buy';
           Engine_Hooks_Dispatcher::getInstance()->callEvent('onPurchaseItemAfter', $params);
        }
		
		$cart = Socialstore_Api_Cart::getInstance();
		$cart->removeCarts();
		$orderitems = $order->getItems();
		$xhtml = "";
		$transactionModel = new Socialstore_Model_DbTable_PayTrans;
		$transelect = $transactionModel->select()->where('order_id = ?', $order->order_id);
		$transactionId = $transactionModel->fetchRow($transelect)->transaction_id;
		$modelBilling = new Socialstore_Model_DbTable_BillingAddresses;
		$select = $modelBilling->select()->where("order_id = ?", $order->order_id);
		$result = $modelBilling -> fetchRow($select);
		$params = array();
		$result = (array)Zend_Json::decode($result->value);
		$params['store_orderid'] = $order->order_id;
		$params['buyer_name'] = $result['fullname'];
		$params['buyer_email'] = $result['email'];
		$params['buyer_address'] = $result['street'].' '.$result['city'].' City, '.$result['country'];
		foreach ($orderitems as $item) {
			
			// Update Product Quantity
			$product = $item->getObject();
			if ($product->product_type == 'default') {
				$item->delivery_status = 'shipping';
			}
			elseif ($product->product_type == 'downloadable') {
				$item->delivery_status = 'delivered';
			}
			$item->payment_status = $order->payment_status;
			$item->save();
			$product->sold_qty += $item->quantity;
			$product->save();
			
			// Update Store Product Quantity
			
			$store = $product->getStore();
			$store->sold_products += $item->quantity;
			$store->save();
			// Send Email to Seller of each products
			
			$sendTo = Engine_Api::_()->getItem('user', $product->owner_id)->email;
        	$params['product_title'] = $product->title;
        	$params['store_title'] = $store->title;
			$params['store_link'] = $this->selfURL().$store->getHref();
			$params['product_link'] = $this->selfURL().$product->getHref();
			$params['product_quantity'] = $item->quantity;
			$params['product_price'] = $item->price;
			$params['product_total'] = $item->total_amount;
      		Engine_Api::_()->getApi('mail','Socialstore')->send($sendTo, 'store_purchaseseller',$params);
      		
      		// Prepare html content to send to Buyer 
      		
      		$xhtml .= "<div>==========Product Detail==============</div>
						<div><span>Product Name: </span>
							 <span>".$product->title."</span>
						</div>
						<div><span>Product Link: </span>
							<span>".$this->selfURL().$product->getHref()."</span>
						</div>					
						<div><span>Store Name: </span>
							 <span>".$store->title."</span>
						</div>
						<div><span>Store Link: </span>
							<span>".$this->selfURL().$store->getHref()."</span>
						</div>
						<div><span>Quantity: </span>
							<span>".$item->quantity."</span>
						</div>
						<div><span>Unit Price: </span>
							<span>".$item->price."</span>
						</div>
						<div><span>Total: </span>
							<span>".$item->total_amount."</span>							
						</div>
					";
      		if ($product->product_type == 'downloadable') {
      			$download_link = $product->generateDownloadUrl($order->order_id);
      			$xhtml .= "<div><span>Product Download Link: </span>
								<span>".$download_link."</span>
							</div>
			";
      		}
		}
		
		// Prepare html content to send to Buyer 
		

		$xhtml .= "<div>==========Transaction Detail==============</div>
				<div><span>Order ID: </span>
				<span>".$order->order_id."</span>
				</div>
				<div><span>Transaction ID: </span>
				<span>".$transactionId."</span>
				</div>
			";
		
		
		$email = $result['email'];
        $buyerparams = $order->toArray();
        $buyerparams['buyer_name'] = $result['fullname'];
        $buyerparams['buyer_email'] = $email;
        $buyerparams['buyer_address'] = $result['street'].' '.$result['city'].' City, '.$result['country'];
      	$buyerparams['ordercontent'] = $xhtml;
      	
        Engine_Api::_()->getApi('mail','Socialstore')->send($email, 'store_purchasebuyer',$buyerparams);
	}

	public function onFailure() {
		$order = $this -> getOrder();
		$order -> payment_status = 'failure';
		$order -> save();
		$cart = Socialstore_Api_Cart::getInstance();
		$cart->removeCarts();
		$orderitems = $order->getItems();
		foreach ($orderitems as $item) {
			$item->payment_status = $order->payment_status;
			$item->save();
		}
	}

	public function onPending() {
		$order = $this -> getOrder();
		$order -> payment_status = 'pending';
		$order -> save();
		$orderitems = $order->getItems();
		foreach ($orderitems as $item) {
			$item->payment_status = $order->payment_status;
			$item->save();
		}
	}

	public function onCancel() {
		$order = $this -> getOrder();
		$order -> payment_status = 'cancel';
		$order -> save();
		foreach ($orderitems as $item) {
			$item->payment_status = $order->payment_status;
			$item->save();
		}
	}

	public function noBilling() {
		return false;
	}
	
	public function noShipping() {
		return false;
	}

	public function addItem($item, $qty, $save_order, $options = null) {
		$order = $this -> getOrder();
		$orderItem = $this -> getByObjectId($item -> getIdentity(),$options);
		// check not exists

		if(!is_object($orderItem)) {
			$item->setQuantity($qty);
			$user = Engine_Api::_()->getItem('user', $item->owner_id);
			$commission = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_product', $user, 'product_com');
			$orderItem = $this -> getModelOrderItems() -> fetchNew();
			if ($options != null) {
				$orderItem->options = $options;
				$item->setOptions($options);
				$ProductOptions = new Socialstore_Model_DbTable_Productoptions;
				$pro_op_select = $ProductOptions->select()->where('productoption_id = ?', $options);
				$pro_options = $ProductOptions->fetchRow($pro_op_select);
				$orderItem->save();
				$pro_options->orderitem_id = $orderItem->orderitem_id;
				$pro_options->order_id = $order->order_id;
				$pro_options->save();
				$opts = explode('-', $pro_options->options);
				$Options = new Socialstore_Model_DbTable_AttributesOptions;
				$option_array = array();
				foreach ($opts as $opt) {
					$opt_select = $Options->select()->where('option_id = ?', $opt);
					$result = $Options->fetchRow($opt_select);
					$option_array[$result->option_id]['label'] = $result->label;
					$option_array[$result->option_id]['adjusted_price'] = $result->adjust_price;
				}
				$option_json = Zend_Json::encode($option_array);
				$orderItem->options_jsons = $option_json;
			}
			$orderItem -> name = $item -> getTitle();
			$orderItem -> description = $item -> getTitle();
			$orderItem -> order_id = $this -> getOrder() -> getId();
			$orderItem -> pretax_price = $item -> getPretaxPrice();
			$orderItem -> item_commission_amount = round($item->getPretaxPrice() * $commission/100, 2);
			$orderItem -> currency = $item -> getCurrency();
			$orderItem -> object_id = $item -> getIdentity();
			$orderItem -> object_type = $this -> getOrder() -> getPaytype();
			$orderItem -> quantity = $qty;
			$orderItem -> item_tax_amount = $item->getItemTaxAmount();
		} else {
			$orderItem -> quantity += $qty;
			$item->setQuantity($orderItem -> quantity);
		}

		$orderItem ->store_id =  $item->store_id;
		$orderItem->sku = $item->sku;
		
		$orderItem -> commission_amount = round($orderItem->item_commission_amount * $orderItem -> quantity, 2);
		$orderItem -> price = round($item -> getPretaxPrice() * $item -> getTaxPercentage() / 100, 2) + $item -> getPretaxPrice();
		$tax_amount = $item -> getPretaxPrice() * $item -> getTaxPercentage() / 100;
		$orderItem -> tax_amount = round($tax_amount, 2);
		$orderItem -> sub_amount = $orderItem -> quantity * $orderItem -> pretax_price;
		$orderItem -> total_amount = $orderItem -> sub_amount + $orderItem -> tax_amount * $orderItem -> quantity;
		$orderItem -> seller_amount = $orderItem -> total_amount - $orderItem -> commission_amount;
		// persistance this order items
		$orderItem -> save();

		// notify parent order update properties
		if($save_order != false){
			
			$this -> getOrder() -> saveInsecurity();
		}
	}

}
