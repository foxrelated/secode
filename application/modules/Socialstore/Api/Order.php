<?php

class Socialstore_Api_Order {

	const SESSION_NAME = 'STORE_ORDER';

	protected $_order;

	static private $_instance;

	private function __construct() {

	}

	public function escapeCurrentOrder() {
		$this -> _session = new Zend_Session_Namespace(self::SESSION_NAME);
		$this -> _session -> order_id = null;
	}

	public function getOrder() {
		if($this -> _order == null) {
			$Orders = new Socialstore_Model_DbTable_Orders;
			$order = NULL;

			$this -> _session = new Zend_Session_Namespace(self::SESSION_NAME);

			if($this -> _session -> order_id) {
				$order_id = $this -> _session -> order_id;
				$order = $Orders -> find($order_id) -> current();
			}

			if(!is_object($order)) {
				$order = $Orders -> fetchNew();
				$order -> paytype_id = 'shopping-cart';
				$order -> save();
			}

			$this -> _session -> order_id = $order -> getId();

			$this -> _order = $order;
		}
		return $this -> _order;
	}

	/**
	 * clear all cart
	 */
	public function cleanAll() {
		$this -> getOrder() -> cleanAll();
	}

	/**
	 * @return Socialstore_Api_Cart
	 */
	static public function getInstance() {
		if(self::$_instance == NULL) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	protected $_modelProducts;

	public function getModelProducts() {
		if($this -> _modelProducts == NULL) {
			$this -> _modelProducts = new Socialstore_Model_DbTable_Products;
		}
		return $this -> _modelProducts;
	}

	/**
	 * @param    int   $product_id
	 * @param    int   $product_quantity
	 */
	public function addItem($product_id, $product_qty = 1) {
		// make sure session is an array

		$product = $this -> getModelProducts() -> find($product_id) -> current();
		$this -> getOrder() -> getPlugin() -> addItem($product, $product_qty);
		return $this;
	}

	/**
	 * @param    int   $product_id
	 * @param    int   $product_quantity
	 */
	public function setItemQty($product_id, $product_qty) {
		$product = $this -> getModelProducts() -> find($product_id) -> current();
		$this -> getOrder() -> getPlugin() -> setItemQty($product, $product_qty);
		return $this;
	}

	/**
	 * @param    int   $product_id
	 */
	public function getItemQty($product_id) {
		$product = $this -> getModelProducts() -> find($product_id) -> current();
		return $this -> getOrder() -> getPlugin() -> getItemQty($product, $product_qty);
	}

	/**
	 * @param    int|array   $product_id
	 */
	public function removeItem($orderitem_id) {
		$remove = new Socialstore_Model_DbTable_OrderItems;
		$order_item = $remove -> find($orderitem_id) -> current();
		return $this -> getOrder() -> removeItem($order_item);
	}

	public function removeAll(){
		return $this->getOrder()->removeAll();
	}
	
	public function countAllQty() {
		return $this -> getOrder() -> getQty();
	}

	/**
	 * @return array{[product_id]=>[product_quatity]}
	 */
	public function getItems() {
		return $this -> getOrder() -> getItems();
	}
	
	public function noShipping(){
		return $this->getOrder()->noShipping();
	}
	public function noBilling(){
		return $this->getOrder()->noBilling();
	}
	
	public function getOrdersPaginator($params = array())
	{
	    $paginator = Zend_Paginator::factory($this->getOrdersSelect($params));
	   
	    if( !empty($params['page']) )
	    {
	      $paginator->setCurrentPageNumber($params['page']);
	    }
	    if( !empty($params['limit']) )
	    {
	      $paginator->setItemCountPerPage($params['limit']);
	    }
	    return $paginator;
	}
	  
	public function getOrdersSelect($params = array())
	{
	  	$table = new Socialstore_Model_DbTable_Orders();
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName)->setIntegrityCheck(false);
		$userTable  = new User_Model_DbTable_Users;
	    $userName = $userTable->info('name');
	    $select -> joinLeft($userName, "owner_id = user_id",'username as owner_name');
	    $select->where("payment_status <> 'initial'");
		
	    // by search
	    
	    if( isset($params['order_id']) && $params['order_id'] != '')
	    {
	        $select->where($rName.".order_id = ? ",$params['order_id']);
	    }
	   
	    // by Buyer
	    
   	    if(!empty($params['owner_name']) && $params['owner_name'] != "") {
	    	$select->where("$userName.username LIKE ?",'%'.$params['owner_name'].'%');
   	    }
	    
   	    if(!empty($params['paytype_id']) && $params['paytype_id'] != "")
	    	$select->where("$rName.paytype_id = ?",$params['paytype_id']);	
	    	
	    if(!empty($params['location']) && $params['location'] > 0)
	    {
	    	$ids=  Engine_Api::_()->getDbTable('locations','Socialstore')->getDescendent($params['location']);
			$select->where("$rName.location_id in (?)", $ids);
	    }
		
	    // by status
	    if(!empty($params['user_id']) && is_numeric($params['user_id']))
	    	$select->where("$rName.owner_id = ?",$params['user_id']);
		
	  	if(isset($params['status']) && $params['status'] != ''){
			$select->where("$rName.state = ?", $params['status']);
		}
	    if(isset($params['orderby']) && $params['orderby']) {
	       	$select->order($params['orderby'].' DESC');
		}
		elseif (!empty($params['order'])) {
			$select->order($params['order'].' '.$params['direction']);
		}
	    else
	    {
	        $select->order("$rName.creation_date DESC");
	    }
	    
	    
		if(getenv('DEVMODE') == 'localdev'){
			print_r($params);
			echo $select;	
		}
		
	    return $select;
	  }
	  
	public function getOrderItemsPaginator($params = array())
	{
	    $paginator = Zend_Paginator::factory($this->getOrderItemsSelect($params));
	   
	    if( !empty($params['page']) )
	    {
	      $paginator->setCurrentPageNumber($params['page']);
	    }
	    if( !empty($params['limit']) )
	    {
	      $paginator->setItemCountPerPage($params['limit']);
	    }
	    return $paginator;
	}
	  
	public function getOrderItemsSelect($params = array())
	{
	  	$table = new Socialstore_Model_DbTable_OrderItems;
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName)->setIntegrityCheck(false);
	    $select->where("payment_status <> 'initial'");
	    // by search
	    
	   if(@$params['search'] && $search = trim($params['search']))
	    {
	      $select->where($rName.".title LIKE ? OR ".$rName.".description LIKE ?", '%'.$search.'%');
	    }
	   if(@$params['title'] && $title = trim($params['title']))
	    {
	      $select->where($rName.".title LIKE ? ", '%'.$title.'%');
	    }
	    if( isset($params['refund_status']) && $params['refund_status'] == 1)
	    {
	      $select->where("refund_status = 1");
	    }
		if( isset($params['product_id']) && $params['product_id'] != '')
	    {
	      $select->where("object_id = ?", $params['product_id'] );
	      $select->where("object_type = 'shopping-cart'");
	    }
	    // by where
	    if(isset($params['where']) && $params['where'] != "")
	    	$select->where($params['where']);
	    // by User
	    if(!empty($params['user_id']) && is_numeric($params['user_id']))
	    	$select->where("$rName.owner_id = ?",$params['user_id']);
	    // by Buyer
	    
   	    if(!empty($params['paytype_id']) && $params['paytype_id'] != "")
	    	$select->where("$rName.paytype_id = ?",$params['paytype_id']);	
	    	
	    if(!empty($params['location']) && $params['location'] > 0)
	    {
	    	$ids=  Engine_Api::_()->getDbTable('locations','Socialstore')->getDescendent($params['location']);
			$select->where("$rName.location_id in (?)", $ids);
	    }
		
	    // by status
	    
		
	  	if(isset($params['status']) && $params['status'] != ''){
			$select->where("$rName.state = ?", $params['status']);
		}

	    
	    
		if(getenv('DEVMODE') == 'localdev'){
			print_r($params);
			echo $select;	
		}
		
	    return $select;
	  }
	
}
