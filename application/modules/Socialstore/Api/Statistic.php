<?php 
class Socialstore_Api_Statistic extends Core_Api_Abstract {  
	
	static private $_instance;
	
	static public function getInstance() {
		if(self::$_instance == NULL) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getTotalStores() {
		$sql = "
				select 
				count(stores.store_id) as total_stores 
				from engine4_socialstore_stores as stores
				where stores.deleted = 0
				";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	public function getFeaturedStores() {
		$sql = "
				select 
				count(stores.store_id) as featured_stores 
				from engine4_socialstore_stores as stores
				where
				stores.featured = 1 AND stores.approve_status = 'approved' AND stores.view_status = 'show' AND stores.deleted = 0
				";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	public function getApprovedStores() {
		$sql = "
				select
				count(stores.store_id) as approved_stores
				from engine4_socialstore_stores as stores
				where
				stores.approve_status = 'approved' AND stores.deleted = 0
			   	";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	public function getShowStores() {
		$sql = "
				select
				count(stores.store_id) as show_stores
				from engine4_socialstore_stores as stores
				where
				stores.approve_status = 'approved' 
				and
				stores.view_status = 'show' AND stores.deleted = 0
				";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	public function getTotalProducts() {
		$sql = "
				select
				count(products.product_id) as total_products
				from engine4_socialstore_products as products
				where 
				products.deleted = 0
				";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);	
	}

	public function getFeaturedProducts() {
		$sql = "
				select 
				count(products.product_id) as featured_products 
				from engine4_socialstore_products as products
				where
				products.deleted = 0 AND products.featured = 1 AND products.approve_status = 'approved'
				";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	public function getApprovedProducts() {
		$sql = "
				select
				count(products.product_id) as approved_products
				from engine4_socialstore_products as products
				where
				products.approve_status = 'approved' AND
				products.deleted = 0
			   	";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	public function getShowProducts() {
		$sql = "
				select
				count(products.product_id) as show_products
				from engine4_socialstore_products as products
				where
				products.approve_status = 'approved' 
				and
				products.view_status = 'show'
				and
				products.deleted = 0
				";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}	
	
	public function getTotalSoldProducts() {
		$sql = "
				select
				sum(stores.sold_products) as sold_products
				from engine4_socialstore_stores as stores where stores.deleted = 0
				";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	public function getStoresPublishFee() {
		$sql = "select 
				sum(orderitems.total_amount) as total_amount
				from engine4_socialstore_orderitems as orderitems
				join engine4_socialstore_orders as orders on orders.order_id = orderitems.order_id
				where orderitems.object_type =  'publish-store'
				and orders.payment_status = 'completed'
			  ";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);		
	}

	public function getStoresFeaturedFee() {
		$sql = "select 
				sum(orderitems.total_amount) as total_amount
				from engine4_socialstore_orderitems as orderitems
				join engine4_socialstore_orders as orders on orders.order_id = orderitems.order_id
				where orderitems.object_type =  'feature-store'
				and orders.payment_status = 'completed'
			  ";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);		
	}
	
	public function getProductsPublishFee() {
		$sql = "select 
				sum(orderitems.total_amount) as total_amount
				from engine4_socialstore_orderitems as orderitems
				join engine4_socialstore_orders as orders on orders.order_id = orderitems.order_id 
				where orderitems.object_type =  'publish-product'
				and orders.payment_status = 'completed'
			  ";		
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	public function getProductsFeaturedFee() {
		$sql = "select 
				sum(orderitems.total_amount) as total_amount
				from engine4_socialstore_orderitems as orderitems
				join engine4_socialstore_orders as orders on orders.order_id = orderitems.order_id 
				where orderitems.object_type =  'feature-product'
				and orders.payment_status = 'completed'
			  ";		
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	public function getCommission() {
		$sql = "select 
				sum(orders.commission_amount) as commission_amount
				from engine4_socialstore_orders as orders
				where orders.paytype_id =  'shopping-cart'
				and orders.payment_status = 'completed'
			  ";		
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	public function getUsersFollow() {
		$sql = "
				select
				count(distinct follows.user_id) as follow_count
				from engine4_socialstore_follows as follows
				";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	public function getUsersFavourite() {
		$sql = "
				select
				count(distinct favourites.user_id) as favourite_count
				from engine4_socialstore_favourites as favourites
				";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	public function getStoresFollowed() {
		$sql = "
				select
				count(stores.store_id) as store_follow
				from engine4_socialstore_stores as stores
				where 
				stores.follow_count > 0 AND stores.deleted = 0
				";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	public function getProductsFavourited() {
		$sql = "
				select
				count(products.product_id) as product_favourite
				from engine4_socialstore_products as products
				where
				products.favourite_count > 0 AND products.deleted = 0
				";	
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}	
	
}

