<?php

class Socialstore_Model_Product extends Core_Model_Item_Abstract{
	
	protected $_qty =  0;
	protected $_type = 'social_product';
	protected static $_baseUrl;
	static protected $_siteUrl;
	protected $_options = '';
	public function getSiteUrl() {
      if (self::$_siteUrl) {
         return self::$_siteUrl;
      }

      $baseUrl = null;

      if (APPLICATION_ENV == 'development') {
         $request = Zend_Controller_Front::getInstance()->getRequest();
         $baseUrl = sprintf('%s://%s', $request->getScheme(), $request->getHttpHost());
         Engine_Api::_()->getApi('settings', 'core')->setSetting('ynaffiliate.baseUrl', $baseUrl);
      } else {
         $baseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.baseUrl', null);
      }

      if ($baseUrl == null) {
         $request = Zend_Controller_Front::getInstance()->getRequest();
         $baseUrl = sprintf('%s://%s', $request->getScheme(), $request->getHttpHost());
         Engine_Api::_()->getApi('settings', 'core')->setSetting('ynaffiliate.baseUrl', $baseUrl);
      }

      self::$_siteUrl = $baseUrl;

      return $baseUrl;
   }
	public static function getBaseUrl(){
		if(self::$_baseUrl == NULL){
			$request =  Zend_Controller_Front::getInstance()->getRequest();
			self::$_baseUrl = sprintf('%s://%s%s', $request->getScheme(), $request->getHttpHost(),$request->getBaseUrl());
			
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
	public function getHref($params = array()) {
		$params = array_merge(array('route' => 'socialstore_product_general', 'reset' => true,  'action'=>'detail', 'product_id' => $this -> getIdentity(), ), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
			'product_id'=>$this->product_id,
			'slug'=>$this->slug,
		), 'socialproduct_detail', true);
	}
	public function isApproved(){
		return $this->approve_status == 'approved';
	}
	
	public function getImageHtml($class = "store_thumb_medium", $type = 'thumb.normal', $width = 339, $height=195, $baseUrl="") {
		return sprintf('<img width="%d" height="%d" class="%s" src="application/modules/Socialstore/externals/images/background_%s.png" style="background-image: url(%s)" />', $width, $height,  $class, $type, $this -> getPhotoUrl($type, $baseUrl));
	}
	
	public function getDescription() {
		$tmpBody = strip_tags($this -> description);
		return (Engine_String::strlen($tmpBody) > 155 ? Engine_String::substr($tmpBody, 0, 155) . '...' : $tmpBody);
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
	
	public function getCategoryName() {
		return Engine_Api::_()->getApi('core','Socialstore')->getCategoryName($this->category_id);
	}
	
	public function getStore() {
		$store = Engine_Api::_()->getItem('social_store', $this->store_id);
		return $store;
	}
	
	public function isPublished() {
		if ($this->approve_status != 'approved' || $this->view_status == 'hide') {
			return false;
		}
		return true;
	}
	
	public function isFavourited($user_id = 0){
		if($user_id == 0){
			return false;
		}
		$sql  = "select favourite_id from engine4_socialstore_favourites where user_id=$user_id and product_id={$this->product_id}";
		$row = Engine_Db_Table::getDefaultAdapter()->fetchOne($sql);
		return (bool)$row;
	}
	public function addedToWishlist($user_id = 0){
		if($user_id == 0){
			return false;
		}
		$sql  = "select wishlist_id from engine4_socialstore_wishlists where user_id=$user_id and product_id={$this->product_id}";
		$row = Engine_Db_Table::getDefaultAdapter()->fetchOne($sql);
		return (bool)$row;
	}
	
	public function getSingletonAlbum() {
		$table = Engine_Api::_() -> getItemTable('socialstore_product_album');
		$select = $table -> select() -> where('product_id = ?', $this -> getIdentity()) -> order('productalbum_id ASC') -> limit(1);

		$album = $table -> fetchRow($select);

		if(null === $album) {
			$album = $table -> createRow();
			$album -> setFromArray(array('title' => $this -> getTitle(), 'product_id' => $this -> getIdentity()));
			$album -> save();
		}

		return $album;
	}
	
	public function addPhoto($file_id) {
		$file = Engine_Api::_() -> getItemTable('storage_file') -> getFile($file_id);
		$album = $this -> getSingletonAlbum();
		$params = array(
		// We can set them now since only one album is allowed
		'collection_id' => $album -> getIdentity(), 'album_id' => $album -> getIdentity(), 'product_id' => $this -> getIdentity(), 'user_id' => $file -> user_id, 'file_id' => $file_id);
		$photo = Engine_Api::_() -> getDbtable('productphotos','Socialstore') -> createRow();
		$photo -> setFromArray($params);
		$photo -> save();
		return $photo;
	}

	public function getPhoto($photo_id) {
		$photoTable = Engine_Api::_() -> getItemTable('socialstore_product_photo');
		$select = $photoTable -> select() -> where('file_id = ?', $photo_id) -> limit(1);
		$photo = $photoTable -> fetchRow($select);
		return $photo;
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
		$params = array('parent_type' => 'social_product', 'parent_id' => $this -> getIdentity());

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
		$photoTable = Engine_Api::_() -> getItemTable('socialstore_product_photo');
		$productAlbum = $this -> getSingletonAlbum();
		$photoItem = $photoTable -> createRow();
		$photoItem -> setFromArray(array('product_id' => $this -> getIdentity(), 'album_id' => $productAlbum -> getIdentity(), 'user_id' => $viewer -> getIdentity(), 'file_id' => $iMain -> getIdentity(), 'collection_id' => $productAlbum -> getIdentity(), ));
		$photoItem -> save();
	
		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> photo_id = $photoItem -> file_id;
		$productAlbum->photo_id = $photoItem->productphoto_id;
		$productAlbum->save();
		$this -> save();

		return $this;
	}
	
	public function hasNotPublished(){
		if ($this->approve_status =='new') {
			return true;
		}
		return false;
	}
	
	public function getWeight() {
		return $this->weight;
	}
	
	public function getAttributes() {
		$str = '';
		if ($this->getCartOptions() != null && $this->getCartOptions() != '') {
			$options = $this->getCartOptions();
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
	
	public function getPretaxPrice(){
		if ($this->getDiscountPrice() == 0) {
			$price = $this->pretax_price;
		}
		else {
			$price = $this->getDiscountPrice();
		}
		$qty = $this->getQuantity();
		$options = $this->getCartOptions();
		if ($this->getDiscount() && $this->getDiscount()!= '' && $qty !='') {
			$discounts = $this->getDiscount();
			$qty = $this->getQuantity();
			foreach ($discounts as $discount) {
				if ($qty >= $discount->quantity) {
					$price = $discount->price;
				}
				else {
					continue;
				}
			}
		}
		if ($options != null && $options != '') {
			$ProductOptions = new Socialstore_Model_DbTable_Productoptions;
			$pro_op_select = $ProductOptions->select()->where('productoption_id = ?', $options);
			$pro_options = $ProductOptions->fetchRow($pro_op_select);
			$opts = explode('-', $pro_options->options);
			$Options = new Socialstore_Model_DbTable_AttributesOptions;
			foreach ($opts as $opt) {
				$opt_select = $Options->select()->where('option_id = ?', $opt);
				$result = $Options->fetchRow($opt_select);
				$adjust_price = $result->adjust_price;
				if ($adjust_price != 0) {
					$price = round($price + $adjust_price,2);
				}
			}
		}
		return $price;
	}
	
	public function getPrice(){
		$pretax_price = $this->getPretaxPrice();
		$item_tax_amount =  round( ($pretax_price * $this->tax_percentage)/100,2);
		$price = $item_tax_amount + $pretax_price;
		return $price;
	}
	
	public function getItemTaxAmount(){
		$pretax_price = $this->getPretaxPrice();
		$item_tax_amount =  round( ($pretax_price * $this->tax_percentage)/100,2);
		return $item_tax_amount;
	}
	public function getTaxPercentage(){
		return $this->tax_percentage;
	}
	
	public function getAmount($qty){
		return $this->price * $qty;
	}
	public function getCurrency(){
		return $this->currency;
	}
	
	public function getTotalAmount(){
		return $this->getPrice() * $this->_qty;
	}
	
	public function setQuantity($qty){
		$this->_qty +=  $qty;
		return $this;
	}
	
	public function setOptions($options) {
		$this->_options = $options;
		return $this;
	}
	public function getCartOptions() {
		return $this->_options;
	}
	public function getQuantity(){
		return $this->_qty;
	}
	
	public function getTaxAmount(){
		return $this->_qty * $this->item_tax_amount;
	}
	
	public function delete(){
		$this->deleted = 1;
		$this->save();
	}
	public function makeSlug($title = NULL){
		return preg_replace("#\s+#", '-', $title?$title:$this->title);
	}
	
	protected $_featuredFee;
	public function getFeaturedFee() {
		if($this->_featuredFee === NULL){
			$owner_id = $this->owner_id;
			$sql = "select 
				sum(items.total_amount) as total_amount
				from engine4_socialstore_orderitems as items
				join engine4_socialstore_orders as orders on orders.order_id = items.order_id
				where items.object_type =  'feature-product'
				and orders.payment_status = 'completed'
				and items.object_id = $this->product_id;
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
				where items.object_type =  'publish-product'
				and orders.payment_status = 'completed'
				and items.object_id = $this->product_id;
			  ";
			$db = Engine_Db_Table::getDefaultAdapter();
			$result = $db -> fetchOne($sql);
			$this->_publishFee = round((double)$result, 2);
		}	
		return $this->_publishFee;
	}
	
	
	public function getTotalPaidFee() {
		$result = round((double)$this->getPublishedFee() + (double)$this->getFeaturedFee(), 2);
		return $result;
	}
	
	public function getDiscount() {
		$Discounts = new Socialstore_Model_DbTable_Discounts;
		$select = $Discounts->select()->where('product_id = ?', $this->product_id);
		return $Discounts->fetchAll($select);
	}
	
	public function deleteDiscounts() {
		$Discounts = new Socialstore_Model_DbTable_Discounts;
		$where = $Discounts->getAdapter()->quoteInto('product_id = ?', $this->product_id);
		$Discounts->delete($where);
	}
	
	public function addDiscount($quantity, $price) {
		$Discounts = new Socialstore_Model_DbTable_Discounts;
		$insert = array('product_id' => $this->product_id,'quantity' => $quantity, 'price' => $price);
		$Discounts->insert($insert);
	}
	
	public function getDiscountPrice() {
		if ($this->discount_price == 0) {
			return 0;
		}
		else {
			$now = strtotime(date('Y-m-d H:i:s'));
			$available_date = strtotime($this->available_date);
			$expire_date = strtotime($this->expire_date);
			if ($now >= $available_date && $now <= $expire_date) {
				return $this->discount_price;
			}
			elseif ($now > $expire_date) {
				$this->setReadOnly(false);
				$this->discount_price = 0.00;
				$this->save();
				return 0;
			}
			else {
				return 0;
			}
		}
	}
	
	public function checkDiscount() {
		if ($this->discount_price == 0) {
			return false;
		}
		else {
			$now = strtotime(date('Y-m-d H:i:s'));
			$expire_date = strtotime($this->expire_date);
			if ($now <= $expire_date) {
				return true;
			}
			else {
				$this->setReadOnly(false);
				$this->discount_price = 0;
				$this->save();
				return false;
			}
		}
	}
	
	public function getCurrentAvailable() {
		if ($this->available_quantity == 0) {
			if ($this->max_qty_purchase == 0) {
				$str = 'unlimited';
				return $str;
			}
			else {
				return $this->max_qty_purchase;
			}
		}
		else {
			$quantity = $this->available_quantity - $this->sold_qty;
			if ($this->max_qty_purchase  == 0) {
				return $quantity; 
			}
			else {
				if ($quantity >= $this->max_qty_purchase) {
					return $this->max_qty_purchase;
				}
				else {
					return $quantity;
				}
			}
		}
	}
	
	public function checkStock() {
		if ($this->available_quantity == 0) {
			return true;
		}
		else {
			$quantity = $this->available_quantity - $this->sold_qty;
			if ($quantity <= 0) {
				return false;
			}
			else {
				if ($quantity < $this->min_qty_purchase) {
					return false;
				}
				else {
					return true;
				}
			}
		}	
	}
	public function getTotalProductFee() {
		$item_id = $this->product_id;
		$sql = "select 
				sum(items.total_amount) as total_amount
				from engine4_socialstore_orderitems as items
				join engine4_socialstore_orders as orders on orders.order_id = items.order_id
				where orders.payment_status = 'completed'
				and items.object_id =  $item_id
				and items.object_type in ('publish-product','feature-product')
			  ";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	/**
	 *  Start Product Downloadable
	 */
	
	public function setPreviewFile($preview_file) {
		if($preview_file instanceof Zend_Form_Element_File) {
			$file = $preview_file -> getFileName();
		} else if(is_array($preview_file) && !empty($preview_file['tmp_name'])) {
			$file = $preview_file['tmp_name'];
		} else if(is_string($preview_file) && file_exists($preview_file)) {
			$file = $preview_file;
		} else {
			throw new Socialstore_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array('parent_type' => 'social_product', 'parent_id' => $this -> getIdentity());
		copy($file,$path . DIRECTORY_SEPARATOR.'preview_' . $name);
		// Save
		$storage = Engine_Api::_() -> storage();
		// Store
		$iMain = $storage -> create($path . DIRECTORY_SEPARATOR.'preview_' . $name, $params);
		@unlink($path . DIRECTORY_SEPARATOR.'preview_' . $name);
		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> previewfile_id = $iMain -> getIdentity();
		$this -> save();

		return $this;
	}
	
	public function setDownloadableFile($download_file) {
		if($download_file instanceof Zend_Form_Element_File) {
			$file = $download_file -> getFileName();
		} else if(is_array($download_file) && !empty($preview_file['tmp_name'])) {
			$file = $download_file['tmp_name'];
		} else if(is_string($download_file) && file_exists($download_file)) {
			$file = $download_file;
		} else {
			throw new Socialstore_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array('parent_type' => 'social_product', 'parent_id' => $this -> getIdentity());
		// Save
		$storage = Engine_Api::_() -> storage();
		// Store
		copy($file,$path .DIRECTORY_SEPARATOR.'download_' . $name);
		$iMain = $storage -> create($path . DIRECTORY_SEPARATOR.'download_' . $name, $params);
		@unlink($path . DIRECTORY_SEPARATOR.'download_' . $name);
		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> file_id = $iMain -> getIdentity();
		$this -> save();
		return $this;
	}
	
	public function generateDownloadUrl($order_id = null) {
		//$base_url = $this->selfURL();
		$Storage = new Storage_Model_DbTable_Files;
		$select = $Storage->select()->where('file_id = ?', $this->file_id);
		$result = $Storage->fetchRow($select);
		//$real_url = $base_url."/".$result->storage_path;
		$real_url  = $result->getHref();
		$href = base64_encode($real_url);
		$router = Zend_Controller_Front::getInstance()->getRouter();
	    $click_url = $router->assemble(array('href' => $href), 'socialstore_click', true);
      	return $this->getSiteUrl() . $click_url;
	}
	
	public function getPreiewUrl() {		
		$Storage = new Storage_Model_DbTable_Files;
		$select = $Storage->select()->where('file_id = ?', $this->previewfile_id);
		$result = $Storage->fetchRow($select);
		return $result->getHref();
		
	}
	
	/**
	 * End Product Downloadable
	 */
	
	public function getOptions() {
		$Types = new Socialstore_Model_DbTable_AttributesTypes;
		$Options = new Socialstore_Model_DbTable_AttributesOptions;
		//$type_select = $Types->select()->where('product_id = ?', $this->product_id);
		//$types = $Types->fetchAll($type_select);
		$opt = array();
		//if (count($types) > 0) {
		//	foreach ($types as $type) {
				//$option_select = $Options->select()->where('product_id = ?',$product_id)->where('type_id = ?', $type->type_id);
				$option_select = $Options->select()->where('product_id = ?',$this->product_id);
				$options = $Options->fetchAll($option_select);
				if (count($options) > 0) {
					foreach ($options as $option) {
						$opt[$option->type_id][$option->option_id] = $option->toArray();	
					}
				}
			//}
		//}
		return $opt;
	}
	
	public function getAttributeText() {
		$Values = new Socialstore_Model_DbTable_AttributesValues;
		$select = $Values->select()->where('product_id = ?', $this->product_id);
		$results = $Values->fetchAll($select);
		return $results;
	}

}