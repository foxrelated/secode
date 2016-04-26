<?php

class Socialstore_Model_Store extends Core_Model_Item_Abstract{
	
	protected $_type = 'social_store';
	
	protected $_totalAmount;
	
	protected $_receivedAmount;
	
	protected $_availableAmount;
	
	protected $_commissionAmount;
	
	protected $_remainAmount;
	
	protected $_waitingAmount;
	
	protected $_pendingAmount;
	
	protected $_maxRequestAmount;
	
	protected $_minRequestAmount;
	
	
	public function getPaymentPlugin($plugin_name){

	}
	
	public function getHref($params = array()) {
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
			'store_id'=>$this->store_id,
			'slug'=>$this->slug
		), 'socialstore_front', true);
	}

	public function getDescription() {
		$tmpBody = strip_tags($this -> description);
		return (Engine_String::strlen($tmpBody) > 155 ? Engine_String::substr($tmpBody, 0, 155) . '...' : $tmpBody);
	}
	public function getSlideShowDescription() {
		$tmpBody = strip_tags($this -> description);
		return (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody);
	}

	public function getSlug() {
		return trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($this -> name))), '-');
	}

	public function comments() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
	}

	/**
	 * Gets a proxy object for the like handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function likes() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
	}
	
	public function getPhotoUrl($type = 'thumb.normal', $baseUrl = '') {
		if($file = Engine_Api::_() -> getItemTable('storage_file') -> getFile($this -> photo_id, $type)) {
			return $file -> map();
		} else {
			return "application/modules/Socialstore/externals/images/nophoto_store_$type.png";
		}

	}
	
	public function getImageHtml($class = "store_thumb_medium", $type = 'thumb.normal', $width = 339, $height=195, $baseUrl="") {
		return sprintf('<img width="%d" height="%d" class="%s" src="application/modules/Socialstore/externals/images/background_%s.png" style="background-image: url(%s)" />', $width, $height,  $class, $type, $this -> getPhotoUrl($type, $baseUrl));
	}
	
	public function addPhoto($file_id) {
		$file = Engine_Api::_() -> getItemTable('storage_file') -> getFile($file_id);
		$album = $this -> getSingletonAlbum();
		$params = array(
		// We can set them now since only one album is allowed
		'collection_id' => $album -> getIdentity(), 'album_id' => $album -> getIdentity(), 'store_id' => $this -> getIdentity(), 'user_id' => $file -> user_id, 'file_id' => $file_id);
		$photo = Engine_Api::_() -> getDbtable('storephotos','Socialstore') -> createRow();
		$photo -> setFromArray($params);
		$photo -> save();
		return $photo;
	}

	public function getPhoto($photo_id) {
		$photoTable = Engine_Api::_() -> getItemTable('socialstore_store_photo');
		$select = $photoTable -> select() -> where('file_id = ?', $photo_id) -> limit(1);
		$photo = $photoTable -> fetchRow($select);
		return $photo;
	}
	
	public function getSingletonAlbum() {
		$table = Engine_Api::_() -> getItemTable('socialstore_store_album');
		$select = $table -> select() -> where('store_id = ?', $this -> getIdentity()) -> order('storealbum_id ASC') -> limit(1);
		$album = $table -> fetchRow($select);
		if(null === $album) {
			$album = $table -> createRow();
			$album -> setFromArray(array('title' => $this -> getTitle(), 'store_id' => $this -> getIdentity()));
			$album -> save();
		}

		return $album;
	}
	
	public function isPublished() {
		if ($this->approve_status != 'approved' || $this->view_status == 'hide') {
			return false;
		}
		return true;
	}
	
	public function setPhoto($photo) {
		if($photo instanceof Zend_Form_Element_File) {
			$file = $photo -> getFileName();
		} else if(is_array($photo) && !empty($photo['tmp_name'])) {
			$file = $photo['tmp_name'];
		} else if(is_string($photo) && file_exists($photo)) {
			$file = $photo;
		} else {
			throw new Socialstore_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array('parent_type' => 'social_store', 'parent_id' => $this -> getIdentity());

		// Save
		$storage = Engine_Api::_() -> storage();

		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(720, 720) -> write($path . '/m_' . $name) -> destroy();

		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(400, 400) -> write($path . '/p_' . $name) -> destroy();
        
       // Resize image (normal1)
        $image = Engine_Image::factory();
        $image -> open($file) -> resize(339, 195) -> write($path . '/in1_' . $name) -> destroy();

        
		// Resize image (normal)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(170, 140) -> write($path . '/in_' . $name) -> destroy();

		// Resize image (icon)
		$image = Engine_Image::factory();
		$image -> open($file);

		$size = min($image -> height, $image -> width);
		$x = ($image -> width - $size) / 2;
		$y = ($image -> height - $size) / 2;

		$image -> resample($x, $y, $size, $size, 48, 48) -> write($path . '/is_' . $name) -> destroy();

		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
        $iIconNormal = $storage -> create($path . '/in_' . $name, $params);
		$iIconNormal1 = $storage -> create($path . '/in1_' . $name, $params);
		$iSquare = $storage -> create($path . '/is_' . $name, $params);

		$iMain -> bridge($iProfile, 'thumb.profile');
        $iMain -> bridge($iIconNormal, 'thumb.normal');
		$iMain -> bridge($iIconNormal1, 'thumb.normal1');
		$iMain -> bridge($iSquare, 'thumb.icon');

		// Remove temp files
		@unlink($path . '/p_' . $name);
		@unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
		@unlink($path . '/in1_' . $name);
		@unlink($path . '/is_' . $name);
		// Add to album
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$photoTable = Engine_Api::_() -> getItemTable('socialstore_store_photo');
		$storeAlbum = $this -> getSingletonAlbum();
		$photoItem = $photoTable -> createRow();
		$photoItem -> setFromArray(array('store_id' => $this -> getIdentity(), 'album_id' => $storeAlbum -> getIdentity(), 'user_id' => $viewer -> getIdentity(), 'file_id' => $iMain -> getIdentity(), 'collection_id' => $storeAlbum -> getIdentity(), ));
		$photoItem -> save();
		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> photo_id = $photoItem -> file_id;
		$storeAlbum->photo_id = $photoItem->storephoto_id;
		$storeAlbum->save();
		$this -> save();
		return $this;
	}

	public function hasNotPublished(){
		if ($this->approve_status =='new') {
			return true;
		}
		return false;
	}
	
	public function isFollowed($user_id = 0){
		if($user_id == 0){
			return false;
		}
		$sql  = "select follow_id from engine4_socialstore_follows where user_id=$user_id and store_id={$this->store_id}";
		$row = Engine_Db_Table::getDefaultAdapter()->fetchOne($sql);
		return (bool)$row;
	}
	
	public function getLocation() {
		$location = new Socialstore_Model_DbTable_Locations;
		$select = $location -> select() -> where('location_id = ?', $this->location_id);
		return $location -> fetchRow($select);
	}
	public function getCategory() {
		$category = new Socialstore_Model_DbTable_Storecategories;
		$select = $category -> select() -> where('storecategory_id = ?', $this->category_id);
		return $category -> fetchRow($select);
	}
	public function getProductsOfStore() {
		$Model = new Socialstore_Model_DbTable_Products;
		$select = $Model-> select() -> where('store_id =? ', $this->store_id);
		return $Model->fetchAll($select);
	}
	
	public function delete(){
		$this->deleted  = 1;
		$this->save();
	}
	
	public function getAccount(){
		$model = new Socialstore_Model_DbTable_Accounts;
		$select = $model->select()->where('store_id=?', $this->store_id)->where('owner_id=?', $this->owner_id);
		return $model->fetchRow($select);
	}
	
	public function getTotalAmount(){
		if($this->_totalAmount === NULL){
			$store_id = $this->store_id;
			$sql = "select 
				sum(items.total_amount) as total_amount
				from engine4_socialstore_orderitems as items
				join engine4_socialstore_orders as orders on (orders.order_id = items.order_id)
				where orders.paytype_id =  'shopping-cart'
				and orders.payment_status = 'completed'
				and items.store_id =  $store_id;
			  ";
			$db = Engine_Db_Table::getDefaultAdapter();
			$result = $db -> fetchOne($sql);
			$another_sql = "
					select sum(packages.shipping_cost + packages.handling_cost) as sh_amount
					from engine4_socialstore_shippingpackages as packages
					join engine4_socialstore_orders as orders on (orders.order_id = packages.order_id)
					where orders.payment_status = 'completed'
					and packages.store_id = $store_id;
				";
			$another_result = $db->fetchOne($another_sql);
			$this->_totalAmount = round((double)$result + (double)$another_result, 2);
		}	
		return $this->_totalAmount;
	}

	public function getCommissionAmount() {
		if($this->_commissionAmount == NULL){
			$store_id = $this->store_id;
			$sql = "
				select 
				sum(items.commission_amount) as commission_amount
				from engine4_socialstore_orderitems as items
				join engine4_socialstore_orders as orders on (orders.order_id = items.order_id)
				where orders.paytype_id =  'shopping-cart'
				and orders.payment_status = 'completed'
				and items.store_id =  $store_id;
			";
			$db = Engine_Db_Table::getDefaultAdapter();
			$result = $db -> fetchOne($sql);
			$this->_commissionAmount = round((double)$result, 2);	
		}
		return $this->_commissionAmount;
	}
	
	public function getRemainAmount(){
		return $this->getTotalAmount()- $this->getCommissionAmount();
	}
	
	public function getReceivedAmount(){
		if($this->_receivedAmount == NULL){
			$store_id = $this->store_id;
			$sql = "
					select 
					sum(reqs.request_amount) as requested_amount 
					from engine4_socialstore_requests as reqs
					where reqs.request_status  = 'completed'
					and reqs.store_id = $store_id;
					";
			$db = Engine_Db_Table::getDefaultAdapter();
			$result = $db -> fetchOne($sql);
			$another_sql = "
					select sum( items.total_amount - items.commission_amount )
					from engine4_socialstore_orderitems as items
					join engine4_socialstore_orders as orders on (orders.order_id = items.order_id)
					where orders.paytype_id =  'shopping-cart'
					and orders.paypal_paykey != 'none'
					and orders.payment_status = 'completed'
					and items.store_id =  $store_id
					";
			$another_result = $db -> fetchOne($another_sql);
			$other_sql = "
					select sum(packages.shipping_cost + packages.handling_cost) as sh_amount
					from engine4_socialstore_shippingpackages as packages
					where packages.paypal_paykey != 'none'
					and packages.store_id = $store_id;
					";
			$other_result = $db->fetchOne($other_sql);
			$this->_receivedAmount = round((double)$result  + (double)$another_result + (double)$other_result, 2);
		}
		return $this->_receivedAmount;
	}
	
	public function getWaitingAmount() {
		if($this->_waitingAmount === NULL){
			$store_id = $this->store_id;
			$sql = "
					select 
					sum(reqs.request_amount) as requested_amount 
					from engine4_socialstore_requests as reqs
					where reqs.request_status  = 'waiting'
					and reqs.store_id = $store_id;
					";
			$db = Engine_Db_Table::getDefaultAdapter();
			$result = $db -> fetchOne($sql);
			$this->_waitingAmount = round((double)$result, 2);	
		}
		return $this->_waitingAmount;
	}
	
	public function getPendingAmount() {
		if($this->_pendingAmount === NULL){
			$store_id = $this->store_id;
			$sql = "
					select 
					sum(reqs.request_amount) as requested_amount 
					from engine4_socialstore_requests as reqs
					where reqs.request_status = 'pending'
					and reqs.store_id = $store_id;
					";
			$db = Engine_Db_Table::getDefaultAdapter();
			$result = $db -> fetchOne($sql);
			$this->_pendingAmount = round((double)$result, 2);	
		}
		return $this->_pendingAmount;
	}
	
	public function getAvailableAmount(){
		if($this->_availableAmount === NULL){
			$this->_availableAmount = $this->getTotalAmount() - $this->getCommissionAmount() - $this->getReceivedAmount() -  $this->getPendingAmount()- $this->getWaitingAmount();
		}
		return $this->_availableAmount;
	}
	
	public function getMaxRequestAmount(){
		if($this->_maxRequestAmount == NULL){
			$limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.maxrequest', 10000.00);
		 	$this->_maxRequestAmount = min($this->getAvailableAmount(), $limit);
		}
		return $this->_maxRequestAmount;
	}
	
	public function getMinRequestAmount(){
		if($this->_minRequestAmount == NULL){
			$this->_minRequestAmount = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.minrequest', 100.00);
		}
		return $this->_minRequestAmount;
	}
	
	public function ownerCanRequest(){
		$limit =  $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.minrequest', 100.00);
		return $this->getAvailableAmount() > $limit;
	}
	
	public function isApproved(){
		return $this->approve_status == 'approved';
	}
	
	protected $_totalProduct;
	
	public function getTotalProduct(){
		if($this->_totalProduct === NULL){
			$store_id = $this->store_id;
			$sql = "
					select 
					count(products.product_id) as total_products 
					from engine4_socialstore_products as products
					where
					products.store_id = $store_id
					and 
					products.deleted = 0
					";
			$db = Engine_Db_Table::getDefaultAdapter();
			$result = $db -> fetchOne($sql);
			$this->_totalProduct = round((double)$result, 2);	
		}
		return $this->_totalProduct;
	}
	
	protected $_availableProduct;
	
	/** save these for Discount features
	 *    				and products.available_date <  '$date'
					and products.expire_date >  '$date'
	 */
	
	public function getAvailableProduct(){
		if($this->_availableProduct === NULL){
			$store_id = $this->store_id;
			$date =  date('Y-m-d H:i:s');
			$sql = "
					select 
					count(products.product_id) as total_products 
					from engine4_socialstore_products as products
					where
					products.approve_status =  'approved'
					and products.view_status =  'show'
					and products.deleted = 0
					and products.store_id = $store_id;
					";
			
			$db = Engine_Db_Table::getDefaultAdapter();
			$result = $db -> fetchOne($sql);
			$this->_availableProduct = round((double)$result, 2);	
		}
		return $this->_availableProduct;
	}
	
	public function makeSlug($title = NULL){
		return preg_replace("#\s+#", '-', $title?$title:$this->title);
	}
	
	protected $_featuredProduct;
	public function getFeaturedProduct() {
		if($this->_featuredProduct === NULL){
			$store_id = $this->store_id;
			$sql = "
					select 
					count(products.product_id) as total_products 
					from engine4_socialstore_products as products
					where
					products.approve_status =  'approved'
					and products.view_status =  'show'
					and products.featured = 1
					and products.store_id = $store_id;
					";
			
			$db = Engine_Db_Table::getDefaultAdapter();
			$result = $db -> fetchOne($sql);
			$this->_featuredProduct = round((double)$result, 2);	
		}
		return $this->_featuredProduct;
	}
	
	public function getCommissionRate() {
		$owner_id = $this->owner_id;
		$user = Engine_Api::_()->getItem('user', $owner_id);
		return Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_product', $user, 'product_com');
	}
	
		protected $_featuredFee;
	public function getFeaturedFee() {
		if($this->_featuredFee === NULL){
			$owner_id = $this->owner_id;
			$sql = "select 
				sum(items.total_amount) as total_amount
				from engine4_socialstore_orderitems as items
				join engine4_socialstore_orders as orders on orders.order_id = items.order_id
				where orders.payment_status = 'completed'
				and items.store_id =  $this->store_id
				and (items.object_type =  'feature-product' or items.object_type = 'feature-store')
			  ";
			$db = Engine_Db_Table::getDefaultAdapter();
			$result = $db -> fetchOne($sql);
			$this->_featuredFee = round((double)$result, 2);
		}	
		return $this->_featuredFee;
	}
	
		protected $_publishFee;
	public function getPublishedFee() {
		if($this->_publishFee === NULL){
			$owner_id = $this->owner_id;
			$sql = "select 
				sum(items.total_amount) as total_amount
				from engine4_socialstore_orderitems as items
				join engine4_socialstore_orders as orders on orders.order_id = items.order_id
				where orders.payment_status = 'completed'
				and items.store_id =  $this->store_id
				and (items.object_type =  'publish-product' or items.object_type = 'publish-store')
			  ";
			$db = Engine_Db_Table::getDefaultAdapter();
			$result = $db -> fetchOne($sql);
			$this->_publishFee = round((double)$result, 2);
		}	
		return $this->_publishFee;
	}
	
	public function getTotalStoreFee() {
		$item_id = $this->store_id;
		$sql = "select 
				sum(items.total_amount) as total_amount
				from engine4_socialstore_orderitems as items
				join engine4_socialstore_orders as orders on orders.order_id = items.order_id
				where orders.payment_status = 'completed'
				and items.store_id =  $item_id
				and items.object_type in ('publish-store','feature-store')
			  ";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}

}
