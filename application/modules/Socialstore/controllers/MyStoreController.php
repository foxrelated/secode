<?php

class Socialstore_MyStoreController extends Core_Controller_Action_Standard {
	
	const MYSTORE_ID = 'MYSTORE_ID';
	
	protected $_myStore;
	
	public function getMyStore(){
		return $this->_myStore;
	}
	
	public function setMyStore($store){
		$this->_myStore =  $store;
		return $this;
	}
	public function noPermissionAction() {
		
	}
	public function init(){
		// private page
		if(!$this -> _helper -> requireUser() -> isValid()){
			return ;
		}
		
		// get get current viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$store = Engine_Api::_() -> getDbTable('SocialStores', 'Socialstore') -> getStoreByOwnerId($viewer -> getIdentity());
		$checkStore = Engine_Api::_()->getItem('social_store', $this->_getParam('store_id'));
		$this->setMyStore($store);
		
		Zend_Registry::set('active_menu','socialstore_main_mystore');
		
		// check if store is exists.
		if(is_object($store)){
			Zend_Registry::set(self::MYSTORE_ID, $store->getIdentity());
		}
	}
	
	public function indexAction() {
		
		Zend_Registry::set('STOREMINIMENU_ACTIVE','info');
		$this->view->headScript()
    	->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places');
	
		$store =  $this->getMyStore();
		if(!is_object($store)){
			if(!$this -> _helper -> requireAuth() -> setAuthParams('social_store', null, 'store_create') -> isValid()){
				return ;
			}else{
				// return to notify that you have no store to accept.
				return $this->_forward('no-store');	
			}
		}
		$this->view->store_id = $store->store_id;
		$this->view->store = $store;
		if($store->photo_id) {
        	$this->view->main_photo = $store->getPhoto($store->photo_id);
      	}
		$this->view->album = $album = $store->getSingletonAlbum();
		$this->view->paginator = $paginator = $album->getCollectiblesPaginator();
		$paginator->setCurrentPageNumber($this->_getParam('page', 1));
		$paginator->setItemCountPerPage(100);
		Zend_Registry::set('store_id', $store->store_id);
    
	}

	public function myProductsAction() {
		Zend_Registry::set('STOREMINIMENU_ACTIVE','my-products');
		$store =  $this->getMyStore();
		if(!is_object($store)){
				// return to notify that you have no store to accept.
				return $this->_forward('no-store');	
		}
		

		Zend_Registry::set('store_id', $store->store_id);
		$params = $this->_getAllParams();
		unset($params['module']);
		unset($params['controller']);
		unset($params['rewrite']);
		unset($params['start_time']);
		unset($params['end_time']);
		Zend_Registry::set('product_search_params', $params);
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity() != $store->owner_id) {
			return;
		}
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.product.page', 10);
		$this->view->items_per_page = $items_per_page;
		$params['page'] = $request -> getParam('page');
		$this -> view -> user_id = $user_id = $viewer -> getIdentity();
		$params['store_id'] = $store->store_id;
		$this->view->formValues = $params;
		$this -> view -> paginator = $paginator = Engine_Api::_()->getApi('product','Socialstore')->getStoreSearchProductsPaginator($params);
		$paginator->setItemCountPerPage($items_per_page);
	}
	
	public function postedStoreAction() {
		if(!$this -> _helper -> requireUser() -> isValid()){
			return ;
		}
					
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$viewer_id = $viewer -> getIdentity();
		$store = Engine_Api::_() -> getApi('store','Socialstore') -> getStoreByUserId($viewer_id);
		$this -> view -> store = $store;

	}

	public function createStoreAction() {

		if(!$this -> _helper -> requireAuth() -> setAuthParams('social_store', null, 'store_create') -> isValid()){
			return;
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		$storeCheck = Engine_Api::_() -> getDbTable('SocialStores', 'Socialstore') -> getStoreByOwnerId($viewer -> getIdentity());
		
		if(is_object($storeCheck)){
			$this->_forward('had-store');
		}	
		
		$this -> view -> form = $form = new Socialstore_Form_Create();
		// If not post or form not valid, return

		if(!$this -> getRequest() -> isPost()) {
			return ;
		}

		$post = $this -> getRequest() -> getPost();

		if(!$form -> isValid($post))
			return ;

		

		// Process
		$table = new Socialstore_Model_DbTable_SocialStores;
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			// Create store
			$values = array_merge($form -> getValues(), array('owner_id' => $viewer -> getIdentity(), ));
			$store = $table -> createRow();
			$store -> setFromArray($values);

			//check image
			if(!empty($values['thumbnail'])) {
				$file = $form -> thumbnail -> getFileName();
				$info = getimagesize($file);
				if($info[2] > 3 || $info[2] == "") {
					$form -> getElement('thumbnail') -> addError('The uploaded file is not supported or is corrupt.');

				}
			}
			$now = date('Y-m-d H:i:s');
			$store -> creation_date = $now;
			$store -> modified_date = $now;
			$store -> slug = $store->makeSlug(); 
			//$currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('store.currency', 'USD');
			//$store -> currency = $currency; 
			$store -> save();
			// Set photo
			if(!empty($values['thumbnail'])) {
				$store -> setPhoto($form -> thumbnail, 0);
			}
			
			// Add fields
      		$customfieldform = $form->getSubForm('fields');
      		$customfieldform->setItem($store);
      		$customfieldform->saveValues();
			
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
			$values['store_authview'] = 'everyone';

			if(empty($values['store_authcom'])) {
				$values['store_authcom'] = 'everyone';
			}

			$viewMax = array_search($values['store_authview'], $roles);
			$commentMax = array_search($values['store_authcom'], $roles);

			foreach($roles as $i => $role) {
				$auth -> setAllowed($store, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($store, $role, 'comment', ($i <= $commentMax));
			}

			$db -> commit();

		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'publish-store'), 'socialstore_mystore_general', true);

	}

	public function editStoreAction() {
		if(!$this -> _helper -> requireUser() -> isValid()){
			return ;
		}

		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$store = Engine_Api::_() -> getItem('social_store', Zend_Registry::get(self::MYSTORE_ID));
		
		
		
		if(!Engine_Api::_() -> core() -> hasSubject('social_store')) {
			Engine_Api::_() -> core() -> setSubject($store);
		}
		// Check auth
		if(!$this -> _helper -> requireSubject() -> isValid()) {
			return ;
		}

		if(!$this -> _helper -> requireAuth() -> setAuthParams('social_store', $viewer, 'store_edit') -> isValid()) {
			return ;
		}
		
		if ($store->owner_id != $viewer->getIdentity()) {
			return;
		}
		// Prepare form
		$this -> view -> form = $form = new Socialstore_Form_Edit( array('item' => $store));
		$form -> removeElement('thumbnail');

		$this -> view -> store = $store;
		// Populate form
		// date_default_timezone_set($viewer->timezone);
		$array = $store -> toArray();
		$form -> populate($array);
		$auth = Engine_Api::_() -> authorization() -> context;
		$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');

		foreach($roles as $role) {
			if($auth -> isAllowed($store, $role, 'comment')) {
				$form -> store_authcom -> setValue($role);
			}
		}
		// Check post/form
		if(!$this -> getRequest() -> isPost()) {
			return ;
		}

		$post = $this -> getRequest() -> getPost();
		if(!$form -> isValid($post))
			return ;
		// Process
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try {
			$values = $form -> getValues();
			$store -> setFromArray($values);
			$store -> modified_date = date('Y-m-d H:i:s');
			if(!empty($values['thumbnail'])) {
				$file = $form -> thumbnail -> getFileName();
				$info = getimagesize($file);
				if($info[2] > 3 || $info[2] == "") {
					$form -> getElement('thumbnail') -> addError('The uploaded file is not supported or is corrupt.');
				}
			}
			$store ->slug = $store->makeSlug();
			$store -> save();

			// Process
			$customfieldform = $form->getSubForm('fields');
      		$customfieldform->setItem($store);
      		$customfieldform->saveValues();

			// Auth

			$values['store_authview'] = 'everyone';

			if(empty($values['store_authcom'])) {
				$values['store_authcom'] = 'everyone';
			}

			$viewMax = array_search($values['store_authcom'], $roles);
			$commentMax = array_search($values['store_authcom'], $roles);

			foreach($roles as $i => $role) {
				$auth -> setAllowed($store, $role, 'store_view', ($i <= $viewMax));
				$auth -> setAllowed($store, $role, 'comment', ($i <= $commentMax));
			}
			$db -> commit();

		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		
		// redirect to publish store if this store has not been published.
		if($store->hasNotPublished()){
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'publish-store'), 'socialstore_mystore_general', true);	
		}else{
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'index'), 'socialstore_mystore_general', true);
		}
		
	}
	
	public function notApprovedAction(){
		
	}

	public function createProductAction() {
  		
	   if( !$this->_helper->requireUser()->isValid() ) return;
	   if( !$this->_helper->requireAuth()->setAuthParams('social_product', null, 'product_create')->isValid()) return;
	   $viewer = Engine_Api::_()->user()->getViewer();       
	   $store_id = $this->_getParam('store');
       $store = Engine_Api::_()->getItem('social_store', $store_id);
       if (!is_object($store)) {
	   		return $this->_forward('no-store');
	   }
	   if (!$store->getAccount()) {
	   		return $this->_forward('no-account');
	   }
	   Zend_Registry::set('store_id', $store_id);
	   if ($store->owner_id != $viewer->getIdentity() || (!$store->isPublished())) {
	   		return $this->_forward('not-approved');
	   }
	   $this->view->form = $form = new Socialstore_Form_Product_Create();

	   if( !$this->getRequest()->isPost() ) {
	     return;
	   }
	    $post = $this->getRequest()->getPost();
	    
		if(isset($post['product_type']) && $post['product_type'] =='downloadable')
		{
			$post['weight'] = 0;
		}
		
	    if(!$form->isValid($post)){
	        return;
	    }
	    
	  
	    $viewer = Engine_Api::_()->user()->getViewer();
	
	    // Process
	    $table = new Socialstore_Model_DbTable_Products;
	    $db = $table->getAdapter();
	    $db->beginTransaction();
	
	    try
	    {
	      // Create product
	    	
	    	
    	 $values = array_merge($form->getValues(), array(
	        'owner_id' => $viewer->getIdentity(),
	      	'store_id' => $store_id,
	      ));
	      
	     
	      $product = $table->createRow();
	      $product->setFromArray($values);
    	  if (isset($values['video_url']) && $values['video_url'] != '') {
	      	$url = $values['video_url'];
	      	$request = new Zend_Controller_Request_Http($url);
			Zend_Controller_Front::getInstance()->getRouter()->route($request);
			if ($request->getModuleName()!= 'video' && $request->getControllerName()!= 'index' && $request->getActionName()!= 'view') {
				return $form->getElement('video_url')->addError('Video URL is not valid!');	
			}
	      }
	       
	      if ($product->product_type == 'downloadable' && empty($values['downloadable_file'])) {
	      	return $form->getElement('downloadable_file')->addError('Please upload product!');
	      }
		    
	      if ($values['discount_price'] != 0 && $values['discount_price'] != '') {
	      	 
	      	if ($values['discount_price'] >= $values['pretax_price'] || $values['discount_price'] < 0) {
	      	  	 return $form->getElement('discount_price')->addError('Discount Price has to be lower than Pretax Price!');	
	      	  }
	      	  else {
	      	  	if (($values['available_date'] != 0) || ($values['expire_date'] != 0)) {
 			      	  
	      	  		$oldTz = date_default_timezone_get();
				      date_default_timezone_set($viewer->timezone); 
					  $available_date = strtotime($values['available_date']);
				      $expire_date =  strtotime($values['expire_date']);
					  $now = strtotime(date('Y-m-d H:i:s'));
				      date_default_timezone_set($oldTz);
				      if($available_date >= $expire_date)
				      {
				          return $form->getElement('expire_date')->addError('Expire Date should be greater than Available Date!');
				           
				      }
				      
				      if($available_date < $now)
				      {
				          return $form->getElement('available_date')->addError('Available Date should be equal or greater than Current Time!');
				          
				      }
				      $product->available_date = date('Y-m-d H:i:s', $available_date);
				      $product->expire_date = date('Y-m-d H:i:s', $expire_date); 
		     	 }
		     	 else {
		     	 	return $form->getElement('available_date')->addError('Available Date & Discount Date have to be set for discount!');
		     	 }
	      	  }	
	      }

	      if ($product->min_qty_purchase > $product->max_qty_purchase && $product->max_qty_purchase != 0) {
	      	  return $form->getElement('min_qty_purchase')->addError('Min quantity must be smaller than max quantity!');
	      }
	      if ($product->weight != 0 && $product->weight < 0) {
	      	  return $form->getElement('weight')->addError('Product Weight must be larger than 0!');
	      }
	  
	      
	      if( !empty($values['thumbnail']) ) {
	          $file = $form->thumbnail->getFileName();
	          $info = getimagesize($file);
	          if($info[2] > 3 || $info[2] == "")
	          {
	            $form->getElement('thumbnail')->addError('The uploaded file is not supported or is corrupt.');  
	            
	          }                
	      }
		     
	     $now = date('Y-m-d H:i:s');
	     $product->creation_date = $now;	
		 $product->modified_date = $now;
		 $product->storecategory_id = $store->category_id;
		 $currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('store.currency', 'USD');
		 $product -> currency = $currency; 
	     
		 // VAT VALUE 
		 $product->tax_percentage = Engine_Api::_()->getApi('core','Socialstore')->getTaxPercentageByTaxId($product->tax_id);
		 
		 // FINAL PRICE
		 $product->item_tax_amount =  round( ($product->pretax_price * $product->tax_percentage)/100,2);
		 $product->price = $product->item_tax_amount + $product->pretax_price;
		 $product->slug =  $product->makeSlug();
		 $product->save();
		 
	      // Set photo
	      if( !empty($values['thumbnail']) ) {
	
	        $product->setPhoto($form->thumbnail,0);
			  
	      }
		  
    	  if (!empty($values['downloadable_file'])) {
	      	$product->setDownloadableFile($form->downloadable_file);
	      }
	      if (!empty($values['preview_file'])) {
	      	$product->setPreviewFile($form->preview_file);
	      }
	      
	      
	      $auth = Engine_Api::_()->authorization()->context;
	      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
	      $values['product_authview'] = 'everyone';
	
	
	      if( empty($values['product_authcom']) ) {
	        $values['product_authcom'] = 'everyone';
	      }
	
	      $viewMax = array_search($values['product_authview'], $roles);
	      $commentMax = array_search($values['product_authcom'], $roles);
	
	      foreach( $roles as $i => $role ) {
	        $auth->setAllowed($product, $role, 'view', ($i <= $viewMax));
	        $auth->setAllowed($product, $role, 'comment', ($i <= $commentMax));
	      }
		 
	      $db->commit();
		  
	    }
		
	    catch( Exception $e )
	    {
	      $db->rollBack();
	      throw $e;
	    }
	    	
	    return $this -> _helper -> redirector -> gotoRoute(array('action' => 'publish-product', 'product_id' => $product -> product_id), 'socialstore_mystore_general', true);
		
	    	    
	}	
	
	public function editProductAction() {
	  	if( !$this->_helper->requireUser()->isValid() ) return;
	    
	  	$viewer = $this->_helper->api()->user()->getViewer();
	    $product = Engine_Api::_()->getItem('social_product', $this->_getParam('product'));
	    $store = $this->getMyStore();
	    Zend_Registry::set('store_id', $store->store_id);
	    if( !Engine_Api::_()->core()->hasSubject('social_product') ) {
	          Engine_Api::_()->core()->setSubject($product);
	    }
	    // Check auth
	    if( !$this->_helper->requireSubject()->isValid() ) {
	      return;
	    }
	    if( !$this->_helper->requireAuth()->setAuthParams($product, $viewer, 'product_edit')->isValid() ) 
	        return;
	    // Prepare form
	    
	    if ($product->owner_id != $viewer->getIdentity()) {
	    	return;
	    }
	        
	    $this->view->form = $form = new Socialstore_Form_Product_Edit(array(
	      'item' => $product
	    ));
	    $form->removeElement('thumbnail');
	    
	    $this->view->product = $product;
	    if ($product->product_type == 'downloadable') 
	    {
	    	$this->view->downloadable = '1';
	    }
		else 
		{
			$form->removeElement('downloadable_file');
	   	 	$form->removeElement('preview_file');
		}
	    // Populate form
	    $array = $product->toArray();
	    $options = array();
	    $options['format'] = 'Y-M-d H:m:s';
	    if ($array['available_date'] != "0000-00-00 00:00:00") {
	    	$array['available_date'] = date('Y-m-d H:i:s',strtotime($this->view->locale()->toDateTime($array['available_date'], $options)));
	    }
	    else {
	    	$array['available_date'] = '';
	    }
	    if ($array['expire_date'] != "0000-00-00 00:00:00") {
	    	$array['expire_date'] = date('Y-m-d H:i:s',strtotime($this->view->locale()->toDateTime($array['expire_date'], $options)));
	    }
	    else {
	    	$array['expire_date'] = '';	
	    }
	    $discount = $product->checkDiscount();
	    if (!$discount) {
	    	$array['discount_price'] = 0.00;
	    }
	    $previous_available = $array['sold_qty'];
	    $form->populate($array);
	    $auth = Engine_Api::_()->authorization()->context;
	    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
	
	    foreach( $roles as $role ) {
	      if( $auth->isAllowed($product, $role, 'comment') ) {
	        $form->product_authcom->setValue($role);
	      }
	    }
	    // Check post/form
	    if( !$this->getRequest()->isPost() ) {
	          return;
	    }
	        
	    $post = $this->getRequest()->getPost();
	    if(!$form->isValid($post))
	    	return;
	    // Process
	    $db = Engine_Db_Table::getDefaultAdapter();
	    $db->beginTransaction();
	    try
	    {
	      $values = $form->getValues();
	      $product->setFromArray($values);
	      if (isset($values['video_url']) && $values['video_url'] != '') {
	      	$url = $values['video_url'];
	      	$request = new Zend_Controller_Request_Http($url);
			Zend_Controller_Front::getInstance()->getRouter()->route($request);
			if ($request->getModuleName()!= 'video' && $request->getControllerName()!= 'index' && $request->getActionName()!= 'view') {
				return $form->getElement('video_url')->addError('Video URL is not valid!');	
			}
	      }
	      if ($values['available_quantity'] !=0) {
	      	if ($values['available_quantity'] < $previous_available) {
	      		return $form->getElement('available_quantity')->addError('Product has been sold more than '.$previous_available.' unit(s). Please enter valid Total Quantity!');
	      	}
	      }
	      if ($values['discount_price'] != 0 && $values['discount_price'] != '') {
	      	  if ($values['discount_price'] >= $values['pretax_price'] || $values['discount_price'] < 0) {
	      	  	 return $form->getElement('discount_price')->addError('Discount Price has to be lower than Pretax Price!');	
	      	  }
	      	  else {
	      	  	if (($values['available_date'] != 0) || ($values['expire_date'] != 0)) {
 			      	  $oldTz = date_default_timezone_get();
				      date_default_timezone_set($viewer->timezone); 
					  $available_date = strtotime($values['available_date']);
				      $expire_date =  strtotime($values['expire_date']);
					  $now = strtotime(date('Y-m-d H:i:s'));
				      date_default_timezone_set($oldTz);
				      if($available_date >= $expire_date)
				      {
				          return $form->getElement('expire_date')->addError('Expire Date should be greater than Available Date!');
				           
				      }
				      
				      if($available_date < $now)
				      {
				          return $form->getElement('available_date')->addError('Available Date should be equal or greater than Current Time!');
				          
				      }
				      $product->available_date = date('Y-m-d H:i:s', $available_date);
				      $product->expire_date = date('Y-m-d H:i:s', $expire_date); 
		     	 }
	      	  }	
	      }
	      else {
	      		$product->available_date = '';
				$product->expire_date = '';
	      }
	      $product->modified_date = date('Y-m-d H:i:s');
	      if( !empty($values['thumbnail']) ) {
	          $file = $form->thumbnail->getFileName();
	          $info = getimagesize($file);
	          if($info[2] > 3 || $info[2] == "")
	          {
	            $form->getElement('thumbnail')->addError('The uploaded file is not supported or is corrupt.');  
	          }                
	      }
	      // VAT VALUE 
		  $product->tax_percentage = Engine_Api::_()->getApi('core','Socialstore')->getTaxPercentageByTaxId($product->tax_id);
		 
		 // FINAL PRICE
		  $product->item_tax_amount =  round( ($product->pretax_price * $product->tax_percentage)/100,2);
		  $product->price = $product->item_tax_amount + $product->pretax_price;
		  $product->slug = $product->makeSlug();
	      $product->save();
	      
	      // Auth
		  
	      $values['product_authview'] = 'everyone';
			
		  // Set photo
	      if( !empty($values['thumbnail']) ) {
	        $product->setPhoto($form->thumbnail,0);
	      }
	
	      if( empty($values['product_authcom']) ) {
	        $values['product_authcom'] = 'everyone';
	      }
		  
		  if (!empty($values['downloadable_file'])) {
	      	$product->setDownloadableFile($form->downloadable_file);
	      }
	      if (!empty($values['preview_file'])) {
	      	$product->setPreviewFile($form->preview_file);
	      }
	
	      $viewMax = array_search($values['product_authcom'], $roles);
	      $commentMax = array_search($values['product_authcom'], $roles);
	
	      foreach( $roles as $i => $role ) 
	      {
	        $auth->setAllowed($product, $role, 'product_view', ($i <= $viewMax));
	        $auth->setAllowed($product, $role, 'comment', ($i <= $commentMax));
	      }
	   	  $db->commit();
	
	    }
	    catch( Exception $e )
	    {
	      $db->rollBack();
	      throw $e;
	    }
	    
		if($product->hasNotPublished()){
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'publish-product', 'product_id' => $product->product_id), 'socialstore_mystore_general', true);	
		}
		else {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'my-products'), 'socialstore_mystore_general', true);
		}
	}
	// forward to this action if the viewer can create store but he has no one.
	public function noStoreAction(){
		
	}
	
	/**
	 * the viewer have got a store then him try to create more.
	 */
	public function hadStoreAction(){
		
	}
	
	public function publishStoreAction(){
       	$store = $this->getMyStore();
       	
        // Check auth
        $this -> view -> form = $form = new Socialstore_Form_PublishStore();
		// If not post or form not valid, return

		if(!$this -> getRequest() -> isPost()) {
			return ;
		}

		$post = $this -> getRequest() -> getPost();

		if(!$form -> isValid($post)){
			return ;
		}

		if (!$store->hasNotPublished()) {
			return;
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity() != $store->owner_id) {
			return;
		}
		
        $viewer = $this->_helper->api()->user()->getViewer();
		
		$values = $form->getValues();
		
	    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
	    $publish_fee = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_store', $viewer, 'store_pubfee');
	    $feature_fee = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_store', $viewer, 'store_ftedfee');
		$auto_approve = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_store', $viewer, 'store_approve');
		
		
		$total_fee =  $publish_fee;
		
		$option = $values['publish_option'];

		if ($option) {
			$store->featured = 1;
			$total_fee += $feature_fee;
			$store->save();
		}
		
		if($total_fee == 0){
			if($auto_approve){
				$plugin =  new Socialstore_Plugin_Process_Store;
				$plugin->setStore($store)->process('accept');			
			}else{
				$store->approve_status = 'waiting';
				$store->save();
			}
			return $this->_forward('published-complete');
		}

		$Orders =  new Socialstore_Model_DbTable_Orders;
		$order  = $Orders->fetchNew();		
		$order->paytype_id =  'publish-store';
		$currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('store.currency', 'USD');
		$order->currency = $currency;
		$order->owner_id = $viewer->getIdentity();
		
		$order->addItem($store, 1, array('option'=>'publish-store','amount'=>$publish_fee));
		
		if($option){
			$order->addItem($store, 1, array('option'=>'feature-store','amount'=>$feature_fee));	
		}
		
		$order->saveInSecurity();
				
		$this -> _helper -> redirector -> gotoRoute(array('module'=>'socialstore','controller'=>'payment','action'=>'process','id'=>$order->getId()),'socialstore_extended');
		

	}
	
	
	public function publishProductAction(){
		if (!$this->_helper->requireUser()->isValid()) { return;}
       	
       	$product = Engine_Api::_()->getItem('social_product', $this->_getParam('product_id'));
        $this -> view -> form = $form = new Socialstore_Form_PublishProduct();
		// If not post or form not valid, return

		if(!$this -> getRequest() -> isPost()) {
			return ;
		}

		$post = $this -> getRequest() -> getPost();

		if(!$form -> isValid($post)){
			return ;
		}
			
		if (!$product->hasNotPublished()) {
			return;
		}
		
		$viewer = $this->_helper->api()->user()->getViewer();
		$values = $form->getValues();
		$publish_fee = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_product', $viewer, 'product_pubfee');
    	$feature_fee = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_product', $viewer, 'product_ftedfee');
		$gda_fee = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_product', $viewer, 'product_gdafee');
		$auto_approve = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_product', $viewer, 'product_approve');
		
		$total_fee =  $publish_fee;
		
		if(isset($values['gda']) && !empty($values['gda']))
        {
            $product->gda = 1;
            $total_fee += $gda_fee;
            $product->save();
        }
		
		$option = $values['publish_option'];
		
		if ($option) {
			$product->featured = 1;
			$total_fee += $feature_fee;
			$product->save();
		}
		
		if ($total_fee == 0) {
		    if($auto_approve == 1) {
				$plugin =  new Socialstore_Plugin_Process_Product;
				$plugin->setProduct($product)->process('accept');
		    }
		    else {
				$product->approve_status = 'waiting';
				$product->save();
		    }
			return $this->_forward('published-complete');
		}
		
		$Orders =  new Socialstore_Model_DbTable_Orders;
		$order  = $Orders->fetchNew();
		
		$order->paytype_id =  'publish-product';
		$currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('store.currency', 'USD');
		$order->currency = $currency;
		$order->owner_id = $viewer->getIdentity();
		$order->addItem($product,1,array('amount'=>$publish_fee,'option'=>'publish-product'));
		
		if($option){
			$order->addItem($product,1,array('amount'=>$feature_fee,'option'=>'feature-product'));	
		}
		
		if(isset($values['gda']) && !empty($values['gda']))
        {
            $order->addItem($product,1,array('amount'=>$gda_fee,'option'=>'gda-product'));    
        }
		
		$order->saveInSecurity();		
		$this -> _helper -> redirector -> gotoRoute(array('controller'=>'payment','action'=>'process','id'=>$order->getId()),'socialstore_extended');

	}
	public function noAccountAction(){
		
	}
	public function publishedCompleteAction() {
		
	}
	
	public function soldProductsAction() {
		Zend_Registry::set('STOREMINIMENU_ACTIVE','sold-products');
		$store =  $this->getMyStore();
		if(!is_object($store)){
				// return to notify that you have no store to accept.
				return $this->_forward('no-store');	
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity() != $store->owner_id) {
			return;
		}
		Zend_Registry::set('store_id', $store->store_id);
		
		$this->view->form = $form = new Socialstore_Form_Product_SoldProductSearch();
		$params = array();  
    	if ($form->isValid($this->_getAllParams())) {
    		$params = $form->getValues();
    	}
		$viewer = Engine_Api::_()->user()->getViewer();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.product.page', 10);
		$this->view->items_per_page = $items_per_page;
		$params['page'] = $request -> getParam('page');
		$this -> view -> user_id = $user_id = $viewer -> getIdentity();
		$params['store_id'] = $store->store_id;
		$params['user_id'] = $viewer->getIdentity();
		$this -> view -> paginator = $paginator = Engine_Api::_()->getApi('product','Socialstore')->getSoldProductsPaginator($params);
		$paginator->setItemCountPerPage($items_per_page);
		$this->view->formValues = $params;

	}
	
	public function changeDeliveryStatusAction() {
		$this->_helper->layout->setLayout('default-simple');
	    $viewer = Engine_Api::_()->user()->getViewer();
	    $orderitem_id = $this->_getParam('orderitem_id');
	    $owner_id = $this->_getParam('owner_id');
	    $Order = new Socialstore_Model_DbTable_OrderItems;
  		$orderitem = $Order->getByOrderItemId($orderitem_id);
	    $orderitem->delivery_status = 'delivered';
	    $orderitem->save();
	
	    $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format'=> 'smoothbox',
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Change delivery status successfully.'))
                  ));
	}	
	
	public function viewShippingInfoAction() {
		$store =  $this->getMyStore();
		if(!is_object($store)){
				// return to notify that you have no store to accept.
				return $this->_forward('no-store');	
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity() != $store->owner_id) {
			return;
		}
		$this->_helper->layout->setLayout('default-simple');
		$shippingaddress_id = $this->_getParam('sh_add');
		$ShippingAddress = new Socialstore_Model_DbTable_ShippingAddresses;
		$address = $ShippingAddress->getAddress($shippingaddress_id);
  		$address_array = Zend_Json::decode($address->value);
  		$this->view->address = $address_array;
	}
	
	/*public function deleteStoreAction()
  	{
	    $viewer = $this->_helper->api()->user()->getViewer();
  		$store =  $this->getMyStore();
		if(!is_object($store)){
				// return to notify that you have no store to accept.
				return $this->_forward('no-store');	
		}
	    if (!$this->_helper->requireAuth()->setAuthParams($store, $viewer, 'store_delete')->isValid())
	    	return;
	   
	    $form = $this->view->form = new Socialstore_Form_Delete();
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	  		$store_id = $store->store_id;
		      $this->view->store_id = $store->getIdentity();
		    // This is a smoothbox by default
		    if( null === $this->_helper->ajaxContext->getCurrentContext() )
		      $this->_helper->layout->setLayout('default-simple');
		    else // Otherwise no layout
		      $this->_helper->layout->disableLayout(true);
		    
		      		$sendTo = $deal->getOwner()->email;
		           $params = $deal->toArray();
				   
				   // send mail to the seller
				   Engine_Api::_()->getApi('mail','groupbuy')->send($sendTo, 'groupbuy_sellerdealdel',$params);
				   
				   // send mail to all buyers
				   foreach($deal->getBuyerEmails() as $buyerEmail){
					   	$params['total_amount'] =  $buyerEmail['total_amount'];
						$params['total_number'] =  $buyerEmail['total_number'];
					   	Engine_Api::_()->getApi('mail','groupbuy')->send($buyerEmail['email'], 'groupbuy_buyerdealdel',$params);
				   }
									   
		      $store->deleted = 1;
		      $store->save();
		      
		      $products = $store->getProductsOfStore();
		      foreach ($products as $product) {
			  		$product->deleted = 1;
			  		$product->save();	      	
		      }
		      
			$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => 10, 
					'parentRefresh' => 10, 
					'messages' => array('')));
		      
	    }
	    if (!($store_id = $store->store_id)) {
      		throw new Zend_Exception('No Store specified');
    	}

	    //Generate form
	    $form->populate(array('store_id' => $store_id));
	    
	    //Output
	    $this->renderScript('my-store/form.tpl');
  	}*/
	
	public function showAction() {
		$store =  $this->getMyStore();
		if(!is_object($store)){
				// return to notify that you have no store to accept.
				return $this->_forward('no-store');	
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity() != $store->owner_id) {
			return;
		}
		if( null === $this->_helper->ajaxContext->getCurrentContext() )
	      $this->_helper->layout->setLayout('default-simple');
	    else // Otherwise no layout
	      $this->_helper->layout->disableLayout(true);
		$store_id = $store->store_id;
		$store_show = $store->view_status;
		if ($store_show == 'show') {
			$store->view_status = 'hide';
			$store->save();
			$products = $store->getProductsOfStore();
			foreach ($products as $product) {
				$product -> view_status = 'hide';
				$product -> save();
			}
		}
		else {
			$store->view_status = 'show';
			$store->save();
		}
		 $this->view->success = true;
	      $this->_forward('success', 'utility', 'core', array(
	                  'smoothboxClose' => true,
	                  'parentRefresh' => true,
	                  'format'=> 'smoothbox',
	                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Change Successfully.'))
	                  ));		
	}
	public function showProductAction() {
		$product = Engine_Api::_()->getItem('social_product', $this->_getParam('product_id'));
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity() != $product->owner_id) {
			return;
		}
		if( null === $this->_helper->ajaxContext->getCurrentContext() )
	      $this->_helper->layout->setLayout('default-simple');
	    else // Otherwise no layout
	      $this->_helper->layout->disableLayout(true);
		$product_show = $product->view_status;
		$store = $product->getStore();
		if ($product_show == 'show') {
			$product->view_status = 'hide';
			$product->save();
			$this->view->success = true;
	     	$this->_forward('success', 'utility', 'core', array(
	                  'smoothboxClose' => true,
	                  'parentRefresh' => true,
	                  'format'=> 'smoothbox',
	                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Change Successfully.'))
	                  ));		
		}
		else if ($store->view_status == 'show') {
			$product->view_status = 'show';
			$product->save();
			$this->view->success = true;
	      	$this->_forward('success', 'utility', 'core', array(
	                  'smoothboxClose' => true,
	                  'parentRefresh' => true,
	                  'format'=> 'smoothbox',
	                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Change Successfully.'))
	                  ));		
		}
		else {
			$product->view_status = 'hide';
			$product->save();
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => false, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array(Zend_Registry::get('Zend_Translate')->_('Product cannot be shown. Your store is not showing.'))));
		}
	}
	
	public function statisticAction() {
		
		$store =  $this->getMyStore();
		if(!is_object($store)){
				// return to notify that you have no store to accept.
				return $this->_forward('no-store');	
		}
		Zend_Registry::set('STOREMINIMENU_ACTIVE','statistic');
		$this->view->store = $store;
	}
	
	public function productStatisticAction() {
		$store =  $this->getMyStore();
		if(!is_object($store)){
				// return to notify that you have no store to accept.
				return $this->_forward('no-store');	
		}
		Zend_Registry::set('STOREMINIMENU_ACTIVE','product-statistic');
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity() != $store->owner_id) {
			return;
		}
		$this->view->store = $store;
		$Product = new Socialstore_Model_DbTable_Products;
		$select = $Product->select()->where('store_id = ?', $store->store_id);
		$select->where('deleted = 0');
		$paginator = $this -> view -> paginator = Zend_Paginator::factory($select);
		$page = $this->_getParam('page', 1);
		$paginator -> setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(10);
	}
	
	public function transactionDetailAction(){
		$product_id = $this->_getParam('product_id');
		$product = Engine_Api::_()->getItem('social_product',$product_id);
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($product->owner_id != $viewer->getIdentity()) {
			return;
		}
		Zend_Registry::set('STOREMINIMENU_ACTIVE','product-statistic');
		$page = $this->_getParam('page',1);
    	//$this->view->form = $form = new Socialstore_Form_Admin_Order_Search();
		$values = array();  
    	//if ($form->isValid($this->_getAllParams())) {
    	//	$values = $form->getValues();
    	
    	//}
    	$limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.page', 10);
    	$values['limit'] = $limit;
    	$values['product_id'] = $product_id;
    	$this->view->paginator = Socialstore_Api_Order::getInstance()->getOrderItemsPaginator($values); 
    	$this->view->paginator->setCurrentPageNumber($page);
    	$this->view->formValues = $values; 
	}
	
	
	/** 
	 * Add Manage Tax for Sellers
	 */
	
	public function manageTaxAction() {
    	Zend_Registry::set('STOREMINIMENU_ACTIVE','manage-tax');
		$store =  $this->getMyStore();
    	$store_id = $store->store_id;
	    $table = new Socialstore_Model_DbTable_Taxes;
	    $select = $table->select()->where('store_id = ?', $store_id);
	
	    $paginator = $this->view->paginator = Zend_Paginator::factory($select);
	    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
	    $paginator->setItemCountPerPage(10);
  	}
	
  	public function editTaxAction() {
	    //Get Form Edit VAT
	    $form = $this->view->form = new Socialstore_Form_Tax_Edit();
	
	    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	
	      $values = $form->getValues();
	      $db = Engine_Db_Table::getDefaultAdapter();
	      $db->beginTransaction();
	      try {
	        // Edit VAT In The Database
	        $tax_id = $values["tax_id"];
	        $table  = new Socialstore_Model_DbTable_Taxes;
	        $select = $table -> select() -> where('tax_id = ?', "$tax_id");
	        $row    = $table -> fetchRow($select);
	
	        $row->name = $values["name"];
	        $row->value = $values["value"];
	        $row->modified_date = date('Y-m-d h:i:s');
	
	        //Database Commit
	        $row->save();
	        $db->commit();
	      } catch (Exception $e) {
	        $db->rollBack();
	        throw $e;
	      }
	      //Close Form If Editing Successfully
	      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	    }
	
	    // Get Code Id - Throw Exception If There Is No Code Id
	    if (!($tax_id = $this->_getParam('tax_id'))) {
	      throw new Zend_Exception('No Taxes Id specified');
	    }
	
	    // Generate and assign form
	    $table = new Socialstore_Model_DbTable_Taxes;
	    $select = $table->select()->where('tax_id = ?', "$tax_id");
	    $vat = $table->fetchRow($select);
	    
	    $form->submit->setLabel('Edit Tax');
	    $form->populate(array('name'   => $vat->name,
	                          'value'  => $vat->value,
	                          'tax_id' => $vat->tax_id));
	    
	    //Output
	    $this->renderScript('my-store/taxes-form.tpl');
  	}
	
  	public function deleteTaxAction()
  	{
	    //Get Delete Form
	    if (!($tax_id = $this->_getParam('tax_id'))) {
	      throw new Zend_Exception('No Taxed Id specified');
	    }
		$table = new Socialstore_Model_DbTable_Taxes;
	    $tax = $table->getTaxById($tax_id);
	    if (!$tax->checkUsed()) {
	  		$form = $this->view->form = new Socialstore_Form_Tax_Delete();
		    
		    //Check Post Method
		    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
		          $values = $form->getValues();
		          $db = Engine_Db_Table::getDefaultAdapter();
		          $db->beginTransaction();
		          try{
		              //Get Row From Database
		              $tax_id = $values["tax_id"];
		              $table = new Socialstore_Model_DbTable_Taxes;
		              $select = $table->select()->where('tax_id = ?', "$tax_id");
		              $row = $table ->fetchRow($select);
					  if (!$row->checkUsed()) {
			              $row->delete();
			              $db->commit();
			              $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
					  }
		              //Database Commit
		          } catch (Exception $e) {
		              $db->rollBack();
		              throw $e;
		          }
		          
		      //Close Form If Editing Successfully
		    }
		    
		    // Get Code Id - Throw Exception If There Is No Code Id
		    //Generate form
		    $form->populate(array('tax_id' => $tax_id));
		    
		    //Output
		    $this->renderScript('my-store/taxes-form.tpl');
	    }
	    else {
	    	$this->renderScript('my-store/tax-not-delete.tpl');
	    }
	  }
	
  	public function addTaxAction(){
	    //Get VAT Form
	    $form = $this->view->form = new Socialstore_Form_Tax_Create();
		$store =  $this->getMyStore();
    	$store_id = $store->store_id;
	    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	          //Get Form Values And Create Database Connection
	          $values = $form->getValues();
	          $db = Engine_Db_Table::getDefaultAdapter();
	          $db->beginTransaction();
	          try{
	             //Insert Values Into A Row.
	             $table = new Socialstore_Model_DbTable_Taxes;
	             $row = $table->createRow();
	             $row->name = $values["name"];
	             $row->store_id = $store_id;
	             $row->value = $values["value"];
	             $row->creation_date = date('Y-m-d h:i:s');
	             $row->modified_date = date('Y-m-d h:i:s');
	
	             $row->save();
	             $db->commit();
	          }
	          catch (Exception $e) {
	              $db->rollBack();
	              throw $e;
	          }
	
	      //Close Form If Editing Successfully
	      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	    }
	    //Output
	    $this->renderScript('my-store/taxes-form.tpl');
  	}
	/** 
	 * End Add Manage Tax for Sellers
	 */
  	
  	/**
  	 * Add Product Custom Categories
  	 */
  	
	public function customCategoriesAction(){
		Zend_Registry::set('STOREMINIMENU_ACTIVE','custom-categories');
		$table = Engine_Api::_() -> getDbTable('customcategories', 'Socialstore');
		$store =  $this->getMyStore();
		$pid = $this -> _getParam('pid', 0);
		$this->view->pid  = $pid = (int)$pid;

		$select = $table -> select() -> where('parent_category_id=?', $pid)->where('store_id =?',$store->store_id);
		$node = $table -> find($pid) -> current();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$page = $request -> getParam('page', 1);
		$this -> view -> categories = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this -> view -> category = $node;
	}
	
	public function addCustomCatAction() {
		$this -> _helper -> layout -> setLayout('admin-simple');

		// Generate and assign form
		$pid= $this -> _getParam('pid', 0);
		$table = Engine_Api::_() -> getDbTable('customcategories', 'Socialstore');
		$select = $table->select()->where('customcategory_id = ?', $pid);
		$checkNote = $table->fetchRow($select);
		$form = $this -> view -> form = new Socialstore_Form_Custom_Category_Create();
		/*if (count($checkNote) > 0) {
			$form->removeElement('category_id');
		}*/

		// Check post
		$req = $this -> getRequest();
		
		
		if($req-> isPost() && $form -> isValid($req-> getPost())) {
			// we will add the category
			$data = $form -> getValues();
			$store =  $this->getMyStore();
			$data['category_id'] = $store->category_id;
			/*if (!isset($data['category_id']) || $data['category_id'] == '') {
				$form->addError('Please select Store Category!');
			}*/
			$data['store_id'] = $store->store_id;
			$node = $table -> addNode($data, $pid);
			
			if(is_object($node)){
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
			}else{
				$form->addError('An error occurs');
			}
				
		}
		// Output
		$this -> renderScript('my-store/catform.tpl');
	}
	
	public function customCatEditAction() {
		$this -> _helper -> layout -> setLayout('admin-simple');

		$req =  $this->getRequest();
		
		$id =  $this->_getParam('id',0);
		
		$table = Engine_Api::_() -> getDbTable('customcategories', 'Socialstore');
		$form = $this -> view -> form = new Socialstore_Form_Custom_Category_Edit();
		$item = $table->find($id)->current();
		if(!is_object($item)){
			$form->setError("Category not found.");
			return ;
		}
		$itemArray = $item->toArray();
		if (isset($itemArray['store_category_id']) && ($itemArray['store_category_id'] != '' || $itemArray['store_category_id'] != '0')) {
			$itemArray['category_id'] = $itemArray['store_category_id'];
			$category_id = $itemArray['category_id'];
		}
		/*if ($itemArray['level'] > 1) {
			$form->removeElement('category_id');
		}*/
		$route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
		$this->view->route = $route;
		if ($category_id != '') {
			$category = new Socialstore_Model_DbTable_Storecategories();
			$select = $category -> select() -> where('storecategory_id = ?', $category_id);
			$count = $category -> fetchRow($select);
			if ($count) {
				$row = $category->find($category_id)->current();
				$this->view->level = $row->level - 1;
				
			}
		}
		$form->populate($itemArray);
		// Check post
		if($req-> isPost() && $form -> isValid($req-> getPost())) {
			// Ok, we're good to add field
			$item->name =  $form->getValue('name');
			$data = $form->getValues();
			/*if (isset($data['category_id']) && ($data['category_id'] != '' || $data['category_id'] != '0')) {
				$item->store_category_id = $data['category_id'];
			}
			else {
				$item->store_category_id = $itemArray['category_id'];
			}*/
			$item->save();
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('my-store/catform.tpl');
	}
	
	public function customCatDeleteAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> category_id = $id;
		$table = Engine_Api::_() -> getDbTable('customcategories', 'Socialstore');
		$node = $table -> find($id) -> current();
		
		if(!is_object($node)){
			$this -> renderScript('my-store/catdelete.tpl');
			return ;
		}
		$row = $node->getUsedCount();
		$this->view->usedCount = $total = count($row);
		if ($total > 0) {
			$store =  $this->getMyStore();
			$store_id = $store->store_id;
			Zend_Registry::set('store_id', $store_id);
			$this->view->form = $form = new Socialstore_Form_Custom_Category_CusCatChange();
		}
		$req  = $this->getRequest();
		
		
		$post = $this -> getRequest() -> getPost();

		
		// Check post
		if($req-> isPost()) {
			if ($total > 0) {
				if(!$form -> isValid($post)) {
					$this -> renderScript('my-store/catdelete.tpl');
					return;
				}
				$new_category = $form->getValue('category_id');
				$ids_object = $node->getDescendantIds();
		    	$ids_array = array();
		    	foreach ($ids_object as $id_ob) {
		    		$ids_array[] = $id_ob->customcategory_id;
		    	}
		    	$ids_array[] = $node->customcategory_id;
				if (in_array($new_category, $ids_array)) {
					$form->addError('You cannot select deleted category or sub-categories of deleted Category!');
				}
				else {
					if (!empty($row)) {
						foreach ($row as $store) {
							$store->category_id = $new_category;
							$store->save();
						}
					}
					$table -> deleteNode($node, 0);
					$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
				}
			}
			else {
				$table -> deleteNode($node, 0);
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
			}
		}

		// Output
		$this -> renderScript('my-store/catdelete.tpl');
	}
  	
  	/**
  	 * End Add Product Custom Categories
  	 */

	 /**
  	 * Manage Shipping Sellers
  	 */
public function shippingMethodAction() {
		Zend_Registry::set('STOREMINIMENU_ACTIVE','shipping-method');
		$ShippingMethod = new Socialstore_Model_DbTable_ShippingMethods;
		$store = $this->getMyStore();
		$store_id = $store->store_id;
		$methods = $ShippingMethod->getShippingMethods($store_id);
		$this->view->methods = $methods;
		$temp_rules = array();
		foreach ($methods as $method) {
			if ($temp_rules == null) {
				$temp_rules = $method->getShippingRule();
			}
			else {
				if (count($method->getShippingRule()) > 0) {
					$temp_rules = array_merge($temp_rules, $method->getShippingRule());
				}
			}
		}
		$rules = array();
		foreach ($temp_rules as $temp_rule) {
			$country_id = $temp_rule['country_id'];
			unset($temp_rule['country_id']);
			$category_id = $temp_rule['category_id'];
			unset($temp_rule['category_id']);
			if (!array_key_exists($temp_rule['shippingrule_id'], $rules)) {
				$temp_rule['country_id'] = array($country_id);
				$temp_rule['category_id'] = array($category_id);
				$rules[$temp_rule['shippingrule_id']] = $temp_rule;
			}
			else {
				if (!in_array($country_id,$rules[$temp_rule['shippingrule_id']]['country_id'])) {
					$rules[$temp_rule['shippingrule_id']]['country_id'][] = $country_id;
				}
				if (!in_array($category_id, $rules[$temp_rule['shippingrule_id']]['category_id'])) {
					$rules[$temp_rule['shippingrule_id']]['category_id'][] = $category_id;
				}
			}
		}
		$this->view->rules = $rules;
	}
	
	public function addShippingMethodAction() {
		$this -> _helper -> layout -> setLayout('admin-simple');
	    $form = $this->view->form = new Socialstore_Form_Shipping_Method_Create();
		$store =  $this->getMyStore();
    	$store_id = $store->store_id;
    	$free_shipping = $this->_getParam('free_shipping', 0);
	    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	          //Get Form Values And Create Database Connection
	          $values = $form->getValues();
	          $db = Engine_Db_Table::getDefaultAdapter();
	          $db->beginTransaction();
	          try{
	             //Insert Values Into A Row.
	             $table = new Socialstore_Model_DbTable_ShippingMethods;
	             $row = $table->createRow();
	             $row->store_id = $store_id;
	             $row->name = $values["name"];
	             $row->description = $values["description"];
	             if ($free_shipping == 1) {
	             	$row->free_shipping = 1;
	             }
	             $row->save();
	             $db->commit();
	          }
	          catch (Exception $e) {
	              $db->rollBack();
	              throw $e;
	          }
	    	  $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	    }
	    //Output
	    $this->renderScript('my-store/add-shippingmethod-form.tpl');
	}
	
	public function editShippingMethodAction() {
		$this -> _helper -> layout -> setLayout('admin-simple');
		$form = $this->view->form = new Socialstore_Form_Shipping_Method_Edit();
	
	    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	
	      $values = $form->getValues();
	      $db = Engine_Db_Table::getDefaultAdapter();
	      $db->beginTransaction();
	      try {
	        $shippingmethod_id = $values["shippingmethod_id"];
	        $table  = new Socialstore_Model_DbTable_ShippingMethods;
	        $select = $table -> select() -> where('shippingmethod_id = ?', "$shippingmethod_id");
	        $row    = $table -> fetchRow($select);
	
	        $row->name = $values["name"];
	        $row->description = $values["description"];
	        //Database Commit
	        $row->save();
	        $db->commit();
	      } catch (Exception $e) {
	        $db->rollBack();
	        throw $e;
	      }
	      //Close Form If Editing Successfully
	      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	    }
	
	    // Get Code Id - Throw Exception If There Is No Code Id
	    if (!($shippingmethod_id = $this->_getParam('shippingmethod_id'))) {
	      throw new Zend_Exception('No shipping method specified');
	    }
	
	    // Generate and assign form
	    $table = new Socialstore_Model_DbTable_ShippingMethods;
	    $select = $table->select()->where('shippingmethod_id = ?', "$shippingmethod_id");
	    $method = $table->fetchRow($select);
	    
	    //$form->execute->setLabel('Edit Shipping Method');
	    $form->populate(array('name'   => $method->name,
	                          'description'  => $method->description,
	                          'shippingmethod_id' => $method->shippingmethod_id));
	    
	    //Output
	    $this->renderScript('my-store/add-shippingmethod-form.tpl');
	}
	
	public function deleteShippingMethodAction() {
		if (!($shippingmethod_id = $this->_getParam('shippingmethod_id'))) {
	      throw new Zend_Exception('No shippingmethod_id specified');
	    }
		$table = new Socialstore_Model_DbTable_ShippingMethods;
    	$select = $table -> select() -> where('shippingmethod_id = ?', "$shippingmethod_id");
	    $row    = $table -> fetchRow($select);
  		$form = $this->view->form = new Socialstore_Form_Shipping_Method_Delete();
		    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	          $values = $form->getValues();
	          $db = Engine_Db_Table::getDefaultAdapter();
	          $db->beginTransaction();
	          try{
	              //Get Row From Database
	              $shippingmethod_id = $values["shippingmethod_id"];
	              $table = new Socialstore_Model_DbTable_ShippingMethods;
	              $select = $table->select()->where('shippingmethod_id = ?', "$shippingmethod_id");
	              $row = $table ->fetchRow($select);
	              $row->delete();
	              $Rules = new Socialstore_Model_DbTable_ShippingRules;
	              $rule_select = $Rules->select()->where('shippingmethod_id = ?', $shippingmethod_id);
	              $results = $Rules->fetchAll($rule_select);
	              if (count($results) > 0) {
	              		foreach($results as $result) {
	              			$result->delete();
	              		}
	              }
	              $ShippingCat = new Socialstore_Model_DbTable_ShippingCats;
	              $cat_select = $ShippingCat->select()->where('shippingmethod_id = ?', $shippingmethod_id);
	              $results = $ShippingCat->fetchAll($cat_select);
	              if (count($results) > 0) {
	              		foreach($results as $result) {
	              			$result->delete();
	              		}
	              }
	              $ShippingCountry = new Socialstore_Model_DbTable_ShippingCountries;
	              $coun_select = $ShippingCountry->select()->where('shippingmethod_id = ?', $shippingmethod_id);
	              $results = $Rules->fetchAll($coun_select);
	              if (count($results) > 0) {
	              		foreach($results as $result) {
	              			$result->delete();
	              		}
	              }
	              $db->commit();
	              $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	              //Database Commit
	          } catch (Exception $e) {
	              $db->rollBack();
	              throw $e;
	          }
		          
		      //Close Form If Editing Successfully
		    }
		    
		    // Get Code Id - Throw Exception If There Is No Code Id
		    //Generate form
		    $form->populate(array('shippingmethod_id' => $shippingmethod_id));
		    
		    //Output
		    $this->renderScript('my-store/taxes-form.tpl');
	}
	
	public function addShippingruleAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		$store = $this->getMyStore();
		$store_id = $store->store_id;
		Zend_Registry::set('store_id', $store_id);
		$shippingmethod_id = $this->_getParam('shippingmethod_id');
		$form = $this->view->form = new Socialstore_Form_Shipping_Rule_Create();
		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$values = $form->getValues();
			if ($values['all_cats'] == 0) {
				$cat_ids = $values['category'];
				if (count($cat_ids) == 0) {
					return $form->getElement('category')->addError('Please select at least 1 category!');	
				}
			}
			if ($values['all_countries'] == 0) {
				$countries = $values['country'];
				if (count($countries) == 0) {
					return $form->getElement('country')->addError('Please select at least 1 country!');	
				}
			}
			if ($values['handling_type'] != 'none') {
				if ($values['handling_fee_type'] == 'none') {
					return $form->getElement('handling_fee_type')->addError('Please select handling fee calculation type!');
				}
				if ($values['handling_fee'] == '0.00') {
					return $form->getElement('handling_fee')->addError('Please input value for handling fee!');
				}
			} 
			$checkExisted = Engine_Api::_()->getApi('shipping','socialstore')->checkCatCounExisted($shippingmethod_id,null,$values['all_cats'],$values['category'],$values['all_countries'],$values['country']);
			if ($checkExisted == false) {
				return $form->addError("A shipping rule with the same categories and countries settings is already existed!");
			}
			$ShippingRule = new Socialstore_Model_DbTable_ShippingRules;
			$ShippingCat = new Socialstore_Model_DbTable_ShippingCats;
			$ShippingCountry = new Socialstore_Model_DbTable_ShippingCountries;
			$rule = $ShippingRule->createRow();
			$rule_id = $rule->shippingrule_id;
			$rule->setFromArray($values);
			$rule->shippingrule_id = $rule_id;
			$rule->shippingmethod_id = $shippingmethod_id;
			$rule->save();
			$rule_id = $rule->shippingrule_id;
			if ($values['all_cats'] == 1){
				$cat = $ShippingCat->createRow();
				$cat->shippingrule_id = $rule_id;
				$cat->shippingmethod_id = $shippingmethod_id;
				$cat->category_id = '0';
				$cat->save();
			}
			else {
				foreach ($cat_ids as $cat_id) {
					$cat = $ShippingCat->createRow();
					$cat->shippingrule_id = $rule_id;
					$cat->shippingmethod_id = $shippingmethod_id;
					$cat->category_id = $cat_id;
					$cat->save();
				}
			}
			if ($values['all_countries'] == 1){
				$country = $ShippingCountry->createRow();
				$country->shippingrule_id = $rule_id;
				$country->shippingmethod_id = $shippingmethod_id;
				$country->country_id = '0';
				$country->save();
			}
			else {
				foreach ($countries as $country) {
					$coun = $ShippingCountry->createRow();
					$coun->shippingrule_id = $rule_id;
					$coun->shippingmethod_id = $shippingmethod_id;
					$coun->country_id = $country;
					$coun->save();
				}
			}
			$this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	    }
	    //Output
	    $this->renderScript('my-store/add-shippingrule.tpl');
	}
	
	public function editShippingruleAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
	    $store = $this->getMyStore();
	    Zend_Registry::set('store_id', $store->store_id);
		$form = $this->view->form = new Socialstore_Form_Shipping_Rule_Edit();
	    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	
	      $values = $form->getValues();
      	  if ($values['all_cats'] == 0) {
				$cat_ids = $values['category'];
				if (count($cat_ids) == 0) {
					return $form->getElement('category')->addError('Please select at least 1 category!');	
				}
		  }
		  if ($values['all_countries'] == 0) {
				$countries = $values['country'];
				if (count($countries) == 0) {
					return $form->getElement('country')->addError('Please select at least 1 country!');	
				}
		  }
  		  if ($values['handling_type'] != 'none') {
				if ($values['handling_fee_type'] == 'none') {
					return $form->getElement('handling_fee_type')->addError('Please select handling fee calculation type!');
				}
				if ($values['handling_fee'] <= '0.00') {
					return $form->getElement('handling_fee')->addError('Invalid value for handling fee!');
				}
		  } 
		  $shippingrule_id = $values["shippingrule_id"];
  	      $table = new Socialstore_Model_DbTable_ShippingRules;
    	  $select = $table->select()->where('shippingrule_id = ?', "$shippingrule_id");
    	  $rule = $table->fetchRow($select);
		  $shippingmethod_id = $rule->shippingmethod_id;
		  $catscouns = $rule->getCatsCouns();
		  $flag = 0;
		  if (count($values['category']) == 1 && count($values['country']) == 1) {
			  foreach ($catscouns as $catcoun) {
			  	if ($catcoun['category_id'] == $values['category'][0] && $catcoun['country_id'] == $values['country'][0]) {
			  		$flag = 1;
			  		continue;
			  	}
			  }
		  }
		  if ($flag == 0) {
			  $checkExisted = Engine_Api::_()->getApi('shipping','socialstore')->checkCatCounExisted($shippingmethod_id,$shippingrule_id,$values['all_cats'],$values['category'],$values['all_countries'],$values['country']);
			  if ($checkExisted == false) {
					return $form->addError("A shipping rule with the same categories and countries settings is already existed!");
			  }
		  }
		  $ShippingRule = new Socialstore_Model_DbTable_ShippingRules;
		  $ShippingCat = new Socialstore_Model_DbTable_ShippingCats;
		  $ShippingCountry = new Socialstore_Model_DbTable_ShippingCountries;
	      try {
	      	$rule->setFromArray($values);
			$rule->shippingmethod_id = $shippingmethod_id;
	      	$rule->save();
	      	$ShippingCat->deleteCat($rule->shippingrule_id);
			$ShippingCountry->deleteCountry($rule->shippingrule_id);
			if ($flag == 0) {
		      	$rule_id = $rule->shippingrule_id;
		      	if ($values['all_cats'] == 1){
					$cat = $ShippingCat->createRow();
					$cat->shippingrule_id = $rule_id;
					$cat->shippingmethod_id = $shippingmethod_id;
					$cat->category_id = '0';
					$cat->save();
				}
				else {
					foreach ($cat_ids as $cat_id) {
						$cat = $ShippingCat->createRow();
						$cat->shippingrule_id = $rule_id;
						$cat->shippingmethod_id = $shippingmethod_id;
						$cat->category_id = $cat_id;
						$cat->save();
					}
				}
				if ($values['all_countries'] == 1){
					$country = $ShippingCountry->createRow();
					$country->shippingrule_id = $rule_id;
					$country->shippingmethod_id = $shippingmethod_id;
					$country->country_id = '0';
					$country->save();
				}
				else {
					foreach ($countries as $country) {
						$coun = $ShippingCountry->createRow();
						$coun->shippingrule_id = $rule_id;
						$coun->shippingmethod_id = $shippingmethod_id;
						$coun->country_id = $country;
						$coun->save();
					}
				}
			}
	      } catch (Exception $e) {
	        throw $e;
	      }
	      //Close Form If Editing Successfully
	      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	    }
	
	    // Get Code Id - Throw Exception If There Is No Code Id
	    if (!($shippingrule_id = $this->_getParam('shippingrule_id'))) {
	      throw new Zend_Exception('No shipping rule specified');
	    }
	
	    // Generate and assign form
    	$table = new Socialstore_Model_DbTable_ShippingRules;
	    $select = $table->select()->where('shippingrule_id = ?', "$shippingrule_id");
	    $rule = $table->fetchRow($select);
	    //$form->execute->setLabel('Edit Shipping Rule');
		$category_id = $rule->getCategories();
		$country_id = $rule->getCountries();
	    $form_value = $rule->toArray();
	    if (count($country_id) == 1 && $country_id[0] == '0') {
	    	$form_value['all_countries'] = 1;
	    }
	    else {
	    	$form_value['all_countries'] = 0;
	    }
	    if (count($category_id) == 1 && $category_id[0] == 0) {
	    	$form_value['all_cats'] = 1;
	    }
	    else {
	    	$form_value['all_cats'] = 0;
	    }
		$form->populate($form_value);
	    $form->country->setValue($country_id);
	    $form->category->setValue($category_id);
	    
	    //Output
	    $this->renderScript('my-store/edit-shippingrule.tpl');
	}
	
	public function changeShippingruleStatusAction() {
		if( null === $this->_helper->ajaxContext->getCurrentContext() )
	    	$this->_helper->layout->setLayout('default-simple');
	    else { // Otherwise no layout
	    	$this->_helper->layout->disableLayout(true);
	    }
		$shippingrule_id = $this->_getParam('shippingrule_id');
		$status = $this->_getParam('status');
		$Rules = new Socialstore_Model_DbTable_ShippingRules;
		$select = $Rules->select()->where('shippingrule_id = ?', $shippingrule_id);
		$result = $Rules->fetchRow($select);
		if (count($result) > 0) {
			if ($status == 'Disabled') {
				$result->enabled = 0;
			}
			else {
				$result->enabled = 1;
			}
			$result->save();
			$this->view->success = true;
	   	 	$this->_forward('success', 'utility', 'core', array(
	                  'smoothboxClose' => true,
	                  'parentRefresh' => true,
	                  'format'=> 'smoothbox',
	                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Change Successfully.'))
	                  ));
		}
	}
	
	public function deleteShippingruleAction() {
		if (!($shippingrule_id = $this->_getParam('shippingrule_id'))) {
	    	throw new Zend_Exception('No shippingrule_id specified');
	    }
		$table = new Socialstore_Model_DbTable_ShippingRules;
    	$select = $table -> select() -> where('shippingrule_id = ?', "$shippingrule_id");
	    $row    = $table -> fetchRow($select);
	    
	    $form = $this->view->form = new Socialstore_Form_Shipping_Rule_Delete();
		    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	          $values = $form->getValues();
	          $db = Engine_Db_Table::getDefaultAdapter();
	          $db->beginTransaction();
	          try{
	              //Get Row From Database
	              $shippingrule_id = $values["shippingrule_id"];
                  $ShippingCats = new Socialstore_Model_DbTable_ShippingCats;
                  $cat_select = $ShippingCats->select()->where('shippingrule_id = ?', "$shippingrule_id");
				  $cats = $ShippingCats->fetchAll($cat_select);
				  $ShippingCountries = new Socialstore_Model_DbTable_ShippingCountries;
				  $country_select = $ShippingCountries->select()->where('shippingrule_id = ?', "$shippingrule_id");
				  $countries = $ShippingCountries->fetchAll($country_select);
				  $table = new Socialstore_Model_DbTable_ShippingRules;
	              $select = $table->select()->where('shippingrule_id = ?', "$shippingrule_id");
	              $row = $table ->fetchRow($select);
			      $row->delete();
			      foreach ($cats as $row) {
		        	  $row->delete();
			      }
  		          foreach ($countries as $row) {
		          	  $row->delete();
		        	}
				  $db->commit();
				  $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	              //Database Commit
	          } catch (Exception $e) {
	              $db->rollBack();
	              throw $e;
	          }
		          
		      //Close Form If Editing Successfully
		}
		    
		    // Get Code Id - Throw Exception If There Is No Code Id
		    //Generate form
		$form->populate(array('shippingrule_id' => $shippingrule_id));
		  
		    //Output
		$this->renderScript('my-store/taxes-form.tpl');
	}
	
	public function freeShippingAction() {
		Zend_Registry::set('STOREMINIMENU_ACTIVE','free-shipping');
		$ShippingMethod = new Socialstore_Model_DbTable_ShippingMethods;
		$store = $this->getMyStore();
		$store_id = $store->store_id;
		$methods = $ShippingMethod->getFreeShippingMethods($store_id);
		$this->view->methods = $methods;
		$temp_rules = array();
		foreach ($methods as $method) {
			if ($temp_rules == null) {
				$temp_rules = $method->getShippingRule();
			}
			else {
				if (count($method->getShippingRule()) > 0) {
					$temp_rules = array_merge($temp_rules, $method->getShippingRule());
				}
			}
		}
		$rules = array();
		foreach ($temp_rules as $temp_rule) {
			$country_id = $temp_rule['country_id'];
			unset($temp_rule['country_id']);
			$category_id = $temp_rule['category_id'];
			unset($temp_rule['category_id']);
			if (!array_key_exists($temp_rule['shippingrule_id'], $rules)) {
				$temp_rule['country_id'] = array($country_id);
				$temp_rule['category_id'] = array($category_id);
				$rules[$temp_rule['shippingrule_id']] = $temp_rule;
			}
			else {
				if (!in_array($country_id,$rules[$temp_rule['shippingrule_id']]['country_id'])) {
					$rules[$temp_rule['shippingrule_id']]['country_id'][] = $country_id;
				}
				if (!in_array($category_id, $rules[$temp_rule['shippingrule_id']]['category_id'])) {
					$rules[$temp_rule['shippingrule_id']]['category_id'][] = $category_id;
				}
			}
		}
		$this->view->rules = $rules;
	}
	
	public function addFreeshippingruleAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		$store = $this->getMyStore();
		$store_id = $store->store_id;
		Zend_Registry::set('store_id', $store_id);
		$shippingmethod_id = $this->_getParam('shippingmethod_id');
		$form = $this->view->form = new Socialstore_Form_Shipping_Free_Create();
		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$values = $form->getValues();
			if ($values['all_cats'] == 0) {
				$cat_ids = $values['category'];
				if (count($cat_ids) == 0) {
					return $form->getElement('category')->addError('Please select at least 1 category!');	
				}
			}
			if ($values['all_countries'] == 0) {
				$countries = $values['country'];
				if (count($countries) == 0) {
					return $form->getElement('country')->addError('Please select at least 1 country!');	
				}
			}
			$checkExisted = Engine_Api::_()->getApi('shipping','socialstore')->checkFreeCatCounExisted($store_id,null,$values['all_cats'],$values['category'],$values['all_countries'],$values['country']);
			if ($checkExisted == false) {
				return $form->addError("A shipping method with the same categories and countries settings is already existed!");
			}
			$ShippingRule = new Socialstore_Model_DbTable_ShippingRules;
			$ShippingCat = new Socialstore_Model_DbTable_ShippingCats;
			$ShippingCountry = new Socialstore_Model_DbTable_ShippingCountries;
			$rule = $ShippingRule->createRow();
			$rule_id = $rule->shippingrule_id;
			$rule->setFromArray($values);
			$rule->shippingrule_id = $rule_id;
			$rule->shippingmethod_id = $shippingmethod_id;
			$rule->save();
			$rule_id = $rule->shippingrule_id;
			if ($values['all_cats'] == 1){
				$cat = $ShippingCat->createRow();
				$cat->shippingrule_id = $rule_id;
				$cat->shippingmethod_id = $shippingmethod_id;
				$cat->category_id = '0';
				$cat->save();
			}
			else {
				foreach ($cat_ids as $cat_id) {
					$cat = $ShippingCat->createRow();
					$cat->shippingrule_id = $rule_id;
					$cat->shippingmethod_id = $shippingmethod_id;
					$cat->category_id = $cat_id;
					$cat->save();
				}
			}
			if ($values['all_countries'] == 1){
				$country = $ShippingCountry->createRow();
				$country->shippingrule_id = $rule_id;
				$country->shippingmethod_id = $shippingmethod_id;
				$country->country_id = '0';
				$country->save();
			}
			else {
				foreach ($countries as $country) {
					$coun = $ShippingCountry->createRow();
					$coun->shippingrule_id = $rule_id;
					$coun->shippingmethod_id = $shippingmethod_id;
					$coun->country_id = $country;
					$coun->save();
				}
			}
			$this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	    }
	    //Output
	    $this->renderScript('my-store/add-freeshippingrule.tpl');
	}
	public function editFreeshippingruleAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
	    $store = $this->getMyStore();
	    Zend_Registry::set('store_id', $store->store_id);
		$form = $this->view->form = new Socialstore_Form_Shipping_Free_Edit();
	    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	
	      $values = $form->getValues();
      	  if ($values['all_cats'] == 0) {
				$cat_ids = $values['category'];
				if (count($cat_ids) == 0) {
					return $form->getElement('category')->addError('Please select at least 1 category!');	
				}
		  }
		  if ($values['all_countries'] == 0) {
				$countries = $values['country'];
				if (count($countries) == 0) {
					return $form->getElement('country')->addError('Please select at least 1 country!');	
				}
		  }
		  $shippingrule_id = $values["shippingrule_id"];
  	      $table = new Socialstore_Model_DbTable_ShippingRules;
    	  $select = $table->select()->where('shippingrule_id = ?', "$shippingrule_id");
    	  $rule = $table->fetchRow($select);
		  $shippingmethod_id = $rule->shippingmethod_id;
		  $catscouns = $rule->getCatsCouns();
		  $flag = 0;
		  if (count($values['category']) == 1 && count($values['country']) == 1) {
			  foreach ($catscouns as $catcoun) {
			  	if ($catcoun['category_id'] == $values['category'][0] && $catcoun['country_id'] == $values['country'][0]) {
			  		$flag = 1;
			  		continue;
			  	}
			  }
		  }
		  if ($flag == 0) {
			  $checkExisted = Engine_Api::_()->getApi('shipping','socialstore')->checkFreeCatCounExisted($store->store_id,$shippingrule_id,$values['all_cats'],$values['category'],$values['all_countries'],$values['country'], $shippingmethod_id);
			  if ($checkExisted == false) {
					return $form->addError("A shipping rule with the same categories and countries settings is already existed!");
			  }
		  }
		  $ShippingRule = new Socialstore_Model_DbTable_ShippingRules;
		  $ShippingCat = new Socialstore_Model_DbTable_ShippingCats;
		  $ShippingCountry = new Socialstore_Model_DbTable_ShippingCountries;
	      try {
	      	$rule->setFromArray($values);
			$rule->shippingmethod_id = $shippingmethod_id;
			$ShippingCat->deleteCat($rule->shippingrule_id);
			$ShippingCountry->deleteCountry($rule->shippingrule_id);
	      	$rule->save();
			if ($flag == 0) {
		      	$rule_id = $rule->shippingrule_id;
				if ($values['all_cats'] == 1){
					$cat = $ShippingCat->createRow();
					$cat->shippingrule_id = $rule_id;
					$cat->shippingmethod_id = $shippingmethod_id;
					$cat->category_id = '0';
					$cat->save();
				}
				else {
					foreach ($cat_ids as $cat_id) {
						$cat = $ShippingCat->createRow();
						$cat->shippingrule_id = $rule_id;
						$cat->shippingmethod_id = $shippingmethod_id;
						$cat->category_id = $cat_id;
						$cat->save();
					}
				}
				if ($values['all_countries'] == 1){
					$country = $ShippingCountry->createRow();
					$country->shippingrule_id = $rule_id;
					$country->shippingmethod_id = $shippingmethod_id;
					$country->country_id = '0';
					$country->save();
				}
				else {
					foreach ($countries as $country) {
						$coun = $ShippingCountry->createRow();
						$coun->shippingrule_id = $rule_id;
						$coun->shippingmethod_id = $shippingmethod_id;
						$coun->country_id = $country;
						$coun->save();
					}
				}
			}
	      } catch (Exception $e) {
	        throw $e;
	      }
	      //Close Form If Editing Successfully
	      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	    }
	
	    // Get Code Id - Throw Exception If There Is No Code Id
	    if (!($shippingrule_id = $this->_getParam('shippingrule_id'))) {
	      throw new Zend_Exception('No shipping rule specified');
	    }
	
	    // Generate and assign form
    	$table = new Socialstore_Model_DbTable_ShippingRules;
	    $select = $table->select()->where('shippingrule_id = ?', "$shippingrule_id");
	    $rule = $table->fetchRow($select);
	    //$form->execute->setLabel('Edit Free Shipping');
		$category_id = $rule->getCategories();
		$country_id = $rule->getCountries();
	    $form_value = $rule->toArray();
		if (count($country_id) == 1 && $country_id[0] == '0') {
	    	$form_value['all_countries'] = 1;
	    }
	    else {
	    	$form_value['all_countries'] = 0;
	    }
	    if (count($category_id) == 1 && $category_id[0] == 0) {
	    	$form_value['all_cats'] = 1;
	    }
	    else {
	    	$form_value['all_cats'] = 0;
	    }
		$form->populate($form_value);
	    $form->country->setValue($country_id);
	    $form->category->setValue($category_id);
	    
	    //Output
	    $this->renderScript('my-store/edit-freeshippingrule.tpl');
	}
	/**
  	 * End Manage Shipping Sellers
  	 */
	
	/**
  	 * Manage Attributes
  	 */

	public function attributeSetAction() {
		Zend_Registry::set('STOREMINIMENU_ACTIVE','attribute-set');
		$store =  $this->getMyStore();
    	$store_id = $store->store_id;
	    $table = new Socialstore_Model_DbTable_AttributesSets();
	    $select = $table->select()->where('store_id = ?', $store_id);
	
	    $paginator = $this->view->paginator = Zend_Paginator::factory($select);
	    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
	    $paginator->setItemCountPerPage(10);
  	}
	
  	public function editAttributeSetAction() {
	    //Get Form Edit VAT
	    $store =  $this->getMyStore();
    	$store_id = $store->store_id;
    	Zend_Registry::set('store_id', $store_id);
	    $form = $this->view->form = new Socialstore_Form_Attribute_Set_Edit();
	
	    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	
	      $values = $form->getValues();
	      $db = Engine_Db_Table::getDefaultAdapter();
	      $db->beginTransaction();
	      try {
	        // Edit Set In The Database
	        $set_id = $values["set_id"];
	        $table  = new Socialstore_Model_DbTable_AttributesSets();
	        $row = $table->getSetById($set_id);
	        $row->name = $values["name"];
	
	        //Database Commit
	        $row->save();
	        $db->commit();
	      } catch (Exception $e) {
	        $db->rollBack();
	        throw $e;
	      }
	      //Close Form If Editing Successfully
	      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	    }
	
	    // Get Code Id - Throw Exception If There Is No Code Id
	    if (!($set_id = $this->_getParam('set_id'))) {
	      throw new Zend_Exception('No Sets Id specified');
	    }
	
	    // Generate and assign form
	    $table = new Socialstore_Model_DbTable_AttributesSets();
	    $set = $table->getSetById($set_id);
	    
	    $form->submit->setLabel('Edit Set');
	    $form->populate(array('name'   => $set->name,
	                          'set_id' => $set->set_id));
	    
	    //Output
	    $this->renderScript('my-store/set-form.tpl');
  	}
	
  	public function deleteAttributeSetAction()
  	{
	    //Get Delete Form
	    if (!($set_id = $this->_getParam('set_id'))) {
	      throw new Zend_Exception('No set Id specified');
	    }
		$table = new Socialstore_Model_DbTable_AttributesSets();
	    $set = $table->getSetById($set_id);
	    if (!$set->checkUsed()) {
	  		$form = $this->view->form = new Socialstore_Form_Attribute_Set_Delete();
		    
		    //Check Post Method
		    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
		          $values = $form->getValues();
		          $db = Engine_Db_Table::getDefaultAdapter();
		          $db->beginTransaction();
		          try{
		              //Get Row From Database
		              $set_id = $values["set_id"];
		              $table = new Socialstore_Model_DbTable_AttributesSets();
		              $select = $table->select()->where('set_id = ?', "$set_id");
		              $row = $table ->fetchRow($select);
					  if (!$row->checkUsed()) {
			              $row->delete();
			              $db->commit();
			              $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
					  }
		              //Database Commit
		          } catch (Exception $e) {
		              $db->rollBack();
		              throw $e;
		          }
		          
		      //Close Form If Editing Successfully
		    }
		    
		    // Get Code Id - Throw Exception If There Is No Code Id
		    //Generate form
		    $form->populate(array('set_id' => $set_id));
		    
		    //Output
		    $this->renderScript('my-store/set-form.tpl');
	    }
	    else {
	    	$this->renderScript('my-store/set-not-delete.tpl');
	    }
	  }
	
  	public function addAttributeSetAction(){
	    //Get VAT Form
	    $form = $this->view->form = new Socialstore_Form_Attribute_Set_Create();
		$store =  $this->getMyStore();
    	$store_id = $store->store_id;
	    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	          //Get Form Values And Create Database Connection
	          $values = $form->getValues();
	          try{
	             //Insert Values Into A Row.
	             $table = new Socialstore_Model_DbTable_AttributesSets();
	             $row = $table->createRow();
	             $row->name = $values["name"];
	             $row->store_id = $store_id;
	             $row->save();
	          }
	          catch (Exception $e) {
	              throw $e;
	          }
	
	      //Close Form If Editing Successfully
	      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	    }
	    //Output
	    $this->renderScript('my-store/set-form.tpl');
  	}
  	
  	public function manageAttributeSetAction() {
  		Zend_Registry::set('STOREMINIMENU_ACTIVE','attribute-set');
		$store =  $this->getMyStore();
    	$store_id = $store->store_id;
    	$set_id = $this->_getParam('set_id');
    	$setTable = new Socialstore_Model_DbTable_AttributesSets;
    	$set = $setTable->getSetById($set_id);
    	$this->view->set = $set;
    	$typeTable = new Socialstore_Model_DbTable_AttributesTypes;
    	$this->view->types = $typeTable->getTypes($set_id);
  	}
  	
	public function attributeCreateAction() {
		//parent::fieldCreateAction();
	    // remove stuff only relavent to profile questions
	    $form  = $this->view->form = new Socialstore_Form_Attribute_Create();
	    $form->removeElement("content");
	    if( !$this->getRequest()->isPost() ) {
	      $form->populate($this->_getAllParams());
	      return;
	    }	
	    if( !$form->isValid($this->getRequest()->getPost()) ) {
      		return;
	    }
	

	    $value = $form->getValues();
	    $Types = new Socialstore_Model_DbTable_AttributesTypes;
	    $type = $Types->createRow();
	    $type->set_id = $this->_getParam('set_id');
	    $type->setFromArray($value);
	    $type->save();
	    // Should get linked in field creation
	    //$fieldMap = Engine_Api::_()->fields()->createMap($field, $option);
	
	    $this->view->status = true;
	    $this->view->field = $type->toArray();
	    $this->view->option = array('option_id' => '0');
	    $this->view->form = null;
	
	    // Re-render all maps that have this field as a parent or child
	    
	    $html = array();
	    //foreach( $maps as $meta ) {
	    	$key = '0_0_'.$type->type_id;
	      $html[$key] = $this->view->ynStoreAttribute($type);
	    //}
	    $this->view->htmlArr = $html;
	}
	public function attributeEditAction() {
		$type_id = $this->_getParam('field_id');
		$Values = new Socialstore_Model_DbTable_AttributesValues;
		$Types = new Socialstore_Model_DbTable_AttributesTypes;
		$select = $Types->select()->where('type_id = ?', $type_id);
		$type = $Types->fetchRow($select);
		$form  = $this->view->form = new Socialstore_Form_Attribute_Edit();
		$product_id = $this->_getParam('pro_id');
		if ($product_id == 0) {
			$form->removeElement("content");
		}
		else {
			$params = array('product_id' => $product_id, 'type_id' => $type_id);
			$con = $Values->getValue($params);
			if (is_object($con)) {
				$content = $con->value;
			}
			else {
				$content = '';
			}
		}
		if( !$this->getRequest()->isPost() ) {
      		$form->populate($type->toArray());
      		$form->populate(array('content' => $content));
      		$form->populate($this->_getAllParams());
			return;
	    }
	
	    if( !$form->isValid($this->getRequest()->getPost()) ) {
	    	return;
	    }
		$type->setFromArray($form->getValues());
		$type->save();
		if ($product_id != 0) {
			$content = $form->getValue('content');
			$params = array('product_id' => $product_id, 'type_id' => $type_id, 'content' => $content);
			$Values->addValue($params);
			$this->view->pro_id = $product_id;
			$this->view->productAttr = 1;
			
		}
	    $this->view->status = true;
	    $this->view->field = $type->toArray();
	    $this->view->form = null;
	    $html = array();
	    //foreach( $maps as $meta ) {
	    	$key = '0_0_'.$type->type_id;
	      $html[$key] = $this->view->ynStoreAttribute($type);
	    //}
	    $this->view->htmlArr = $html;
      		
	}
	
	public function attributeDeleteAction() {
		$type_id = $this->_getParam('field_id');
		$Types = new Socialstore_Model_DbTable_AttributesTypes;
		$select = $Types->select()->where('type_id = ?', $type_id);
		$type = $Types->fetchRow($select);
		$product_id = $this->_getParam('pro_id');
		$this->view->form = $form = new Engine_Form(array(
	      'method' => 'post',
	      'action' => $_SERVER['REQUEST_URI'],
	      'elements' => array(
	        array(
	          'type' => 'submit',
	          'name' => 'submit',
	        )
	      )
	    ));
	
	    if( !$this->getRequest()->isPost() ) {
	      return;
	    }
		if ($product_id == 0) {
			$this->view->status = true;	
			$Types->deleteAttribute($type);
		}
	    else {
		    $this->view->status = true;
		    $this->view->pro_id = $product_id;
			$this->view->productAttr = 1;
			$Values = new Socialstore_Model_DbTable_AttributesValues;
			$params = array('product_id' => $product_id, 'type_id' => $type_id);
			$Values->removeValue($params);
	    }
	    
	}
	
	public function optionCreateAction() {
		$values = $this->_getAllParams();
		if ($values['label'] == '') {
			return;
		}
		$type_id = $this->_getParam('field_id');
		$Types = new Socialstore_Model_DbTable_AttributesTypes;
		$select = $Types->select()->where('type_id = ?', $type_id);
		$type = $Types->fetchRow($select);
		$Options = new Socialstore_Model_DbTable_AttributesOptions;
		$option = $Options->createRow();
		$option->type_id = $values['field_id'];
		$option->product_id = $values['product_id'];
		$option->label = $values['label']; 
		$option->save();
		$this->view->status = true;
    	$this->view->option = $option->toArray();
    	$this->view->field = $type->toArray();
    	$this->view->pro_id = $values['product_id'];
		$this->view->productAttr = 1;
    	 $html = array();
	    //foreach( $maps as $meta ) {
	    	$key = '0_0_'.$type->type_id;
	      $html[$key] = $this->view->ynStoreAttribute($type);
	    //}
	    $this->view->htmlArr = $html;
	}
	
	public function optionEditAction() {
		$values = $this->_getAllParams();
		$Options = new Socialstore_Model_DbTable_AttributesOptions;
		$option_select = $Options->select()->where('option_id = ?', $values['option_id']);
		$option = $Options->fetchRow($option_select);
		$product = Engine_Api::_()->getItem('social_product', $option->product_id);
		$type_id = $option->type_id;
		$Types = new Socialstore_Model_DbTable_AttributesTypes;
		$type_select = $Types->select()->where('type_id = ?', $type_id);
		$type = $Types->fetchRow($type_select);
		$this->view->form = $form = new Socialstore_Form_Attribute_Option();
	    if( !$this->getRequest()->isPost() ) {
	    	$form->populate($option->toArray());
	    	return;
	    }
	
	    if( !$form->isValid($this->getRequest()->getPost()) ) {
	    	return;
	    }
	    $form_values = $form->getValues();
	    if ($product->pretax_price + $form_values['adjust_price'] <= 0) {
	    	return $form->getElement('adjust_price')->addError('Adjust Price must be lower than Product\'s Pretax Price!');	
	    } 
	    $option->setFromArray($form_values);
	    $option->save();
	    $this->view->status = true;
	    $this->view->form = null;
	    $this->view->pro_id = $option->product_id;
		$this->view->productAttr = 1;
	    $this->view->option = $option->toArray();
	    $this->view->field = $type->toArray();
	     $html = array();
	    //foreach( $maps as $meta ) {
	    	$key = '0_0_'.$type->type_id;
	      $html[$key] = $this->view->ynStoreAttribute($type);
	    //}
	    $this->view->htmlArr = $html;
	    
	}
	
	public function optionDeleteAction() {
		$Options = new Socialstore_Model_DbTable_AttributesOptions;
		$option_select = $Options->select()->where('option_id = ?', $this->_getParam('option_id'));
		$option = $Options->fetchRow($option_select);		
		if( !$this->getRequest()->isPost() ) {
      		return;
    	}
		$Options->deleteOption($option);
	}
	
	public function productAttributeAction() {
		Zend_Registry::set('STOREMINIMENU_ACTIVE','my-products');
		$store =  $this->getMyStore();
		if(!is_object($store)){
				// return to notify that you have no store to accept.
				return $this->_forward('no-store');	
		}
		$Sets = new Socialstore_Model_DbTable_AttributesSets;
		$sets = $Sets->getSetsByStoreId($store->store_id);
		$this->view->sets = $sets;
		$product_id = $this->_getParam('product_id', 0);
		if (Zend_Registry::isRegistered('product_id') || $product_id == 0) {
			$product_id = Zend_Registry::get('product_id');
		}
		$product = Engine_Api::_()->getItem('social_product', $product_id);
		$this->view->product = $product;
		$this->view->pro_id = $product_id;
		$this->view->productAttr = 1;
		if ($product->attributeset_id != 0) {
			$set = $Sets->getSetById($product->attributeset_id);
			$this->view->attrSetName = $set->name;
    		$this->view->set = $set;
    		$typeTable = new Socialstore_Model_DbTable_AttributesTypes;
    		$this->view->types = $typeTable->getTypes($set->set_id);
		}
		else {
			$Presets = new Socialstore_Model_DbTable_AttributePresets;
			$store_presets = $Presets->getPresetsByStore($store->store_id);
			$this->view->presets = $store_presets;
		}
	}
	
	public function addProductSetAction() {
		$store = $this->getMyStore();
		$store_id = $store->store_id;
		Zend_Registry::set('store_id', $store_id);
		$product_id = $this->_getParam('product_id');
		$form = $this->view->form = new Socialstore_Form_Product_Attribute_Add();
    	$form->populate(array('product_id' => $product_id));
	    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	          //Get Form Values And Create Database Connection
	          $values = $form->getValues();
	          $db = Engine_Db_Table::getDefaultAdapter();
	          $db->beginTransaction();
	          try{
	          	$product = Engine_Api::_()->getItem('social_product', $form->getValue('product_id'));
	             //Insert Values Into A Row.
	            $product->attributeset_id = $form->getValue('set_id'); 
	            $product->save();
	          	$db->commit();
	          }
	          catch (Exception $e) {
	              $db->rollBack();
	              throw $e;
	          }
		  Zend_Registry::set('product_id', $product->product_id);
	      //Close Form If Editing Successfully
	      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	    }
	    //Output
	    $this->renderScript('my-store/taxes-form.tpl');
	}
	public function loadAttributePresetAction() {
		$store = $this->getMyStore();
		$store_id = $store->store_id;
		Zend_Registry::set('store_id', $store_id);
		$product_id = $this->_getParam('product_id');
		$form = $this->view->form = new Socialstore_Form_Product_Attribute_Preset_Load();
    	$form->populate(array('product_id' => $product_id));
	    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	          //Get Form Values And Create Database Connection
	          $values = $form->getValues();
	          $db = Engine_Db_Table::getDefaultAdapter();
	          $db->beginTransaction();
	          try {
	          	$product = Engine_Api::_()->getItem('social_product', $form->getValue('product_id'));
	             //Insert Values Into A Row.
	            $preset_id = $form->getValue('attributepreset_id');
	            $Presets = new Socialstore_Model_DbTable_AttributePresets;
	            $select = $Presets->select()->where('attributepreset_id = ?', $preset_id);
	            $preset = $Presets->fetchRow($select);
	            $product->attributeset_id = $preset->attributeset_id;
	            $product->save();
	            if ($preset->options != null && $preset->options != '') {
	            	$options = Zend_Json::decode($preset->options);
	            	$Options = new Socialstore_Model_DbTable_AttributesOptions;
	            	foreach ($options as $option_id) {
	            		$option_select = $Options->select()->where('option_id = ?', $option_id);
	            		$option = $Options->fetchRow($option_select);
	            		if (is_object($option)) {
	            			$new_option = $Options->createRow();
		            		$new_option->type_id = $option->type_id;
		            		$new_option->product_id = $product->product_id;
		            		$new_option->label = $option->label;
		            		$new_option->adjust_price = $option->adjust_price;
		            		$new_option->save();
	            		}
	            	}
	            }
	            if ($preset->values != null && $preset->values != '') {
	            	$values = Zend_Json::decode($preset->values);
	            	$Values = new Socialstore_Model_DbTable_AttributesValues;
	            	foreach ($values as $value_id) {
	            		$value_select = $Values->select()->where('value_id = ?', $value_id);
	            		$value = $Values->fetchRow($value_select);
	            		if (is_object($value)) {
		            		$new_value = $Values->createRow();
		            		$new_value->product_id = $product->product_id;
		            		$new_value->type_id = $value->type_id;
		            		$new_value->value = $value->value;
		            		$new_value->save();
	            		}
	            	}
	            }
	          	$db->commit();
	          }
	          catch (Exception $e) {
	              $db->rollBack();
	              throw $e;
	          }
		  Zend_Registry::set('product_id', $product->product_id);
	      //Close Form If Editing Successfully
	      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	    }
	    //Output
	    $this->renderScript('my-store/taxes-form.tpl');
	}
	
	public function editProductSetAction() {
		$store = $this->getMyStore();
		$store_id = $store->store_id;
		Zend_Registry::set('store_id', $store_id);
		$form = $this->view->form = new Socialstore_Form_Product_Attribute_Edit();
		$product_id = $this->_getParam('product_id');
    	$product = Engine_Api::_()->getItem('social_product', $product_id);
	    //Check Post Method
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	
	      $values = $form->getValues();
	      $db = Engine_Db_Table::getDefaultAdapter();
	      $db->beginTransaction();
	      try {
			$product->attributeset_id = $values['set_id'];
	        $product->save();
			$Options = new Socialstore_Model_DbTable_AttributesOptions;
			$Options->deleteOptionsByProId($product_id);
	        $db->commit();
	      } catch (Exception $e) {
	        $db->rollBack();
	        throw $e;
	      }
	      Zend_Registry::set('product_id', $product->product_id);
	      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	    }
	    
	    $form->submit->setLabel('Save Changes');
	    $form->populate(array('set_id' => $product->attributeset_id));
	    
	    //Output
	    $this->renderScript('my-store/taxes-form.tpl');
	}
	
	public function saveAttributePresetAction() {
		$product_id = $this->_getParam('product_id');
		$store = $this->getMyStore();
		$form = $this->view->form = new Socialstore_Form_Product_Attribute_Preset_Create();
		$form->populate(array('store_id' => $store->store_id, 'product_id' => $product_id));
		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$Presets = new Socialstore_Model_DbTable_AttributePresets;
			$preset = $Presets->createRow();
			$preset->store_id = $store->store_id;
			$preset->preset_name = $form->getValue('preset_name');
			$product_id = $form->getValue('product_id');
			$product = Engine_Api::_()->getItem('social_product', $product_id);
			$preset->attributeset_id = $product->attributeset_id;
			$Options = new Socialstore_Model_DbTable_AttributesOptions;
			$option_select = $Options->select()->where('product_id = ?', $product_id);
			$options = $Options->fetchAll($option_select);
			if (count($options) > 0) {
				$preset_options = array();
				foreach ($options as $option) {
					$preset_options[] = $option->option_id;
				}
			}
			$Values = new Socialstore_Model_DbTable_AttributesValues;
			$value_select = $Values->select()->where('product_id = ?', $product_id);
			$values = $Values->fetchAll($value_select);
			if (count($values) > 0) {
				$preset_values = array();
				foreach ($values as $value) {
					$preset_values[] = $value->value_id;
				}
			}
			if ($preset_options && count($preset_options) > 0) {
				$preset->options = Zend_Json::encode($preset_options);
			}
			if ($preset_values && count($preset_values) > 0) {
				$preset->values = Zend_Json::encode($preset_values);
			}
			$preset->save();
			$this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}
		$this->renderScript('my-store/taxes-form.tpl');
	}
	
	public function attributePresetListAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		$store = $this->getMyStore();
		$Presets = new Socialstore_Model_DbTable_AttributePresets;
		$select = $Presets->select()->where('store_id = ?', $store->store_id);
		$store_presets = $Presets->fetchAll($select);
		$this->view->presets = $store_presets;
		$params = $this -> _getAllParams();
		if(isset($params['submit'])) {
			$id_array = $params['preset'];
			foreach ($id_array as $id) {
				$select = $Presets->select()->where('attributepreset_id = ?', $id);
				$preset = $Presets->fetchRow($select);
				$preset->delete();
			}
			$this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'messages' => array('')));
		}
		$this->renderScript('my-store/attribute-preset-list.tpl');
	}
	
	public function removeProductSetAction() {
		
	}
	
	public function getAdjustPriceAction() {
		$params = $this->_getAllParams();
		$Options = new Socialstore_Model_DbTable_AttributesOptions;
		$option = $Options->getOption($params);
		if (($option) && $option->adjust_price != 0) {
			$this->view->adjust = 1;
			$this->view->price = $this->view->currency($option->adjust_price);
		}
		else {
			$this->view->adjust = 0;
		}
	}
	
	/**
	 * End Manage Attributes
	 */
	
}
