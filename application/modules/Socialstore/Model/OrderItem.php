<?php

class Socialstore_Model_OrderItem extends Core_Model_Item_Abstract implements Socialstore_Payment_Order_Item_Interface {
	
	protected $_searchTriggers = false;
	/**
	 * @return string
	 */
    public function getId(){
    	return $this->orderitem_id;
    }
    
    public function getOrder() {
    	$Order = new Socialstore_Model_DbTable_Orders;
    	$select = $Order->select()->where('order_id = ?', $this->order_id);
    	return $Order->fetchRow($select);
    }
	public function getStore() {
		$store = Engine_Api::_()->getItem('social_store', $this->store_id);
		return $store;
	}
	public function getSellerAmount(){
		return $this->seller_amount;
	}
    /**
     * Tax getter
     *
     * @return decimal (16,2)
     */
    public function getTaxAmount(){
    	return $this->tax_amount;
    }
	
	/**
	 * get shiping amount
	 * @return decimal (16,2)
	 */
	public function getShippingAmount(){
		return $this->shipping_amount;
	}
	
	/**
	 * get handing amount
	 * @return decimal (16,2)
	 */
	public function getHandlingAmount(){
		return $this->handling_amount;
	}
	
	/**
	 * get discount amount
	 * @return decimal
	 */
	public function getDiscountAmount(){
		return $this->discount_amount;
	}

	
	/**
	 * get commission amount
	 * @return decimal (16,2)
	 */
	public function getCommissionAmount(){
		return $this->commission_amount;
	}
	
	/**
	 * get total amount
	 */
	public function getTotalAmount(){
		return $this->total_amount;
	}
	
	public function getSubAmount(){
		return $this->sub_amount;
	}
	
	/**
	 * @return decimal (16,2)
	 */
	public function getPrice(){
		return $this->price;
	}
	
	/**
	 * pretax price
	 * @return decimal (16,2)
	 */
	public function getPreTaxPrice(){
		return $this->pretax_price;
	}
    /**
     * Qty getter
     *
     * @return numeric
     */
    public function getQty(){
    	return $this->quantity;
    }
	
	/**
     * Name getter
     *
     * @return string
     */
    public function getName(){
    	return $this->name;
    }
    
    public function getAuthorizeName() {
    	$name = $this->name;
    	return (Engine_String::strlen($name) > 20 ? Engine_String::substr($name, 0, 20) . '...' : $name);
    }
	
    /**
     * Description getter
     *
     * @return string
     */
    public function getDescription(){
    	return $this->description;
    }
    public function getAuthorizeDes(){
    	$description = $this->description;
    	return (Engine_String::strlen($description) > 20 ? Engine_String::substr($description, 0, 20) . '...' : $description);
    }


    public function getCurrency(){
    	return $this->currency;
    }

    /**
     * Weight getter
     *
     * @return float
     */
    public function getWeight(){
    	return null;
    }

    /**
     * Width getter
     *
     * @return float
     */
    public function getWidth(){
    	return null;
    }
	

    /**
     * Height getter
     *
     * @return float
     */
    public function getHeight(){
    	return null;
    }


    /**
     * Length getter
     *
     * @return float
     */
    public function getLength(){
    	return null;
    }
	
    /**
     * Url getter
     *
     * @return string
     */
    public function getUrl(){
    	return '';
    	// return $this->url;
    }
	
	public function updateItem(){
		
	}
	
	public function getItemTaxAmount(){
		return $this->item_tax_amount;
	}
	
	
	public function getObject() {
		return self::getOriginalObject($this->object_type, $this->object_id);
	}
	static public function getOriginalObject($object_type, $object_id){
		$maps = array(
			'shopping-cart'=>'Socialstore_Model_DbTable_Products',
			'my-cart'=>'Socialstore_Model_DbTable_Products',
			'publish-store'=>'Socialstore_Model_DbTable_SocialStores',
			'publish-product'=>'Socialstore_Model_DbTable_Products'
		);

		$model_class =  @$maps[$object_type];
		if(!$model_class){
			throw Exception("invalid object type $object_type");
		}
		
		$model =  new $model_class;
		$item =  $model->find($object_id)->current();
		
		if(!is_object($item)){
			//throw new Exception("the original object was deleted, [$object_type, $object_id]");
		}
				
		return $item;
	}
	
	public function getItemQuantity(){
		return $this->quantity;
	}
	
	public function getTotalProductQuantity($product_id) {
		$OrderItems = new Socialstore_Model_DbTable_OrderItems;
		$select = $OrderItems->select()->from($OrderItems->info('name'),array('sum(quantity) as sum'));
		$select->where('order_id = ?', $this->order_id)->where('object_id = ?', $product_id);
		$result = $OrderItems->fetchRow($select);
		return $result['sum'];
	}
	
	public function getTotalProQtyByOpt($product_id,$options) {
		$OrderItems = new Socialstore_Model_DbTable_OrderItems;
		$select = $OrderItems->select()->from($OrderItems->info('name'),array('sum(quantity) as sum'));
		$select->where('order_id = ?', $this->order_id)->where('object_id = ?', $product_id);
		if ($options != null) {
			$select->where('options = ?', $options);
		}
		$result = $OrderItems->fetchRow($select);
		return $result['sum'];
	}
	public function getAttributes() {
		$str = '';
		if ($this->options != null && $this->options != '') {
			$options = $this->options;
			$ProductOptions = new Socialstore_Model_DbTable_Productoptions;
			$pro_op_select = $ProductOptions->select()->where('productoption_id = ?', $options);
			$pro_options = $ProductOptions->fetchRow($pro_op_select);
			$opts = explode('-', $pro_options->options);
			$Options = new Socialstore_Model_DbTable_AttributesOptions;
			$i = 0;
			$l = count($opts);
			foreach ($opts as $opt) {
				$i++;
				$opt_select = $Options->select()->where('option_id = ?', $opt);
				$result = $Options->fetchRow($opt_select);
				
				if ($i < $l) {
					$str .= $result->label. ' - ';
				}	
				else {
					$str .= $result->label;
				}
			}
		}
		if ($str == '') {
			$str = 'N/A';
		}
		return $str;
	}
}
