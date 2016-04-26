<?php

class Socialstore_AdminManageProductController extends Core_Controller_Action_Admin {
	public function init() {
		parent::init();
		Zend_Registry::set('admin_active_menu', 'socialstore_admin_main_manageproduct');
	}

	public function indexAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('socialstore_admin_main', array(), 'socialstore_admin_main_settings');

		$page = $this -> _getParam('page', 1);
		$this -> view -> form = $form = new Socialstore_Form_Admin_Product_Search();
		$values = array();
		if($form -> isValid($this -> _getAllParams())) {
			$values = $form -> getValues();
			if(empty($values['order'])) {
				$values['order'] = 'product_id';
			}
			if(empty($values['direction'])) {
				$values['direction'] = 'DESC';
			}
			$this -> view -> filterValues = $values;
			$this -> view -> order = $values['order'];
			$this -> view -> direction = $values['direction'];
			$table = new Socialstore_Model_DbTable_Products();
			$products = $table -> fetchAll(Engine_Api::_() -> getApi('product', 'Socialstore') -> getProductsSelect($values)) -> toArray();
			$this -> view -> count = count($products);
		}
		$limit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('store.product.page', 10);
		$values['limit'] = $limit;
		$this -> view -> viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> paginator = Engine_Api::_() -> getApi('product', 'Socialstore') -> getProductsPaginator($values);
		$this -> view -> paginator -> setCurrentPageNumber($page);
		$this -> view -> formValues = $values;
	}

	public function deleteSelectedAction() {
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		$this -> view -> count = count(explode(",", $ids));
		// Save values
		if($this -> getRequest() -> isPost() && $confirm == true) {
			$ids_array = explode(",", $ids);
			foreach($ids_array as $id) {
				$product = Engine_Api::_() -> getItem('social_product', $id);
				if($product) {
					$product -> deleted = 1;
					$product->save();
					
					//delete actions and attachments
					$streamTbl = Engine_Api::_()->getDbTable('stream', 'activity');
					$streamTbl->delete('(`object_id` = '.$id.' AND `object_type` = "social_product")');
                    $activityTbl = Engine_Api::_()->getDbTable('actions', 'activity');
                    $activityTbl->delete('(`object_id` = '.$id.' AND `object_type` = "social_product")');
                    $attachmentTbl = Engine_Api::_()->getDbTable('attachments', 'activity');
                    $attachmentTbl->delete('(`id` = '.$id.' AND `type` = "social_product")');
					
					$params = $product -> toArray();
					$sendTo = Engine_Api::_()->getItem('user', $product->owner_id)->email;
				    // send mail to the seller
				    try
				    {
						Engine_Api::_()->getApi('mail','Socialstore')->send($sendTo, 'store_productdelete',$params);
						$Favourite = new Socialstore_Model_DbTable_Favourites;
						$select = $Favourite->select()->where('product_id = ?', $product->product_id);
						$results = $Favourite->fetchAll($select);
						if (count($results) > 0) 
						{
							  // send mail to favouriter
							$store = Engine_Api::_()->getItem('social_store', $product->store_id);
							$params['product_title'] =  $product->title;
							$params['store_title'] =  $store->title;
							$params['store_link'] =  $store->getHref();
							foreach($results as $result){
								if ($result->user_id !=0) {
								$useremail = Engine_Api::_()->getItem('user', $result->user_id)->email;
								Engine_Api::_()->getApi('mail','Socialstore')->send($useremail, 'store_productdelfav',$params);
							   }
							}
						}
					}
					catch(Exception $e)
					{
					}
				}
			}

			$this -> _helper -> redirector -> gotoRoute(array('action' => 'index'));
		}

	}

	public function approveSelectedAction() {
		$this -> view -> ids = $ids = $this -> _getParam('ids1', null);
		$confirm = $this -> _getParam('confirm', false);
		$this -> view -> count = count(explode(",", $ids));
		// Save values
		if($this -> getRequest() -> isPost() && $confirm == true) {
			$ids_array = explode(",", $ids);
			foreach($ids_array as $id) {
				$product = Engine_Api::_() -> getItem('social_product', $id);
				if($product && $product -> approve_status != 'approved' && $product -> approve_status != 'denied') {
						$plugin =  new Socialstore_Plugin_Process_Product;
						$plugin->setProduct($product)->process('accept');
				}
			}
			$this -> _helper -> redirector -> gotoRoute(array('action' => 'index'));
		}

	}

	public function featuredAction() {
		$product_id = $this -> _getParam('product');
		$product_good = $this -> _getParam('good');
		$product = Engine_Api::_() -> getItem('social_product', $product_id);
		if($product) {
			$product -> featured = $product_good;
			$product -> save();
		}
	}
	public function gdaAction() {
        $product_id = $this -> _getParam('product');
        $product_good = $this -> _getParam('good');
        $product = Engine_Api::_() -> getItem('social_product', $product_id);
        if($product) {
            $product -> gda = $product_good;
            $product -> save();
        }
    }
	public function showAction() {
		$product_id = $this -> _getParam('product');
		$product_show = $this -> _getParam('show');
		$product = Engine_Api::_() -> getItem('social_product', $product_id);
		if($product) {
			$store = $product->getStore();
			if ($store->view_status == 'show') {
				$product -> view_status = ($product_show == 1) ? 'show' : 'hide';
				$product -> save();
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => false, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changed Successfully.'))));
			}
			else {
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => false, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array(Zend_Registry::get('Zend_Translate')->_('Product cannot be shown. Store of this product is not showing.'))));
			}
		}
	}

	public function approveProductAction() {
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$product_id = $this -> _getParam('product');
		$product = Engine_Api::_() -> getItem('social_product', $product_id);

		if($product && $product -> approve_status != 'approved' && $product -> approve_status != 'denied') {
			$plugin =  new Socialstore_Plugin_Process_Product;
			$plugin->setProduct($product)->process('accept');
			
		}
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array(Zend_Registry::get('Zend_Translate')->_('Approve product successfully.'))));
	}

	public function denyProductAction() {
		$product_id = $this -> _getParam('product');
		$product = Engine_Api::_() -> getItem('social_product', $product_id);
		if($product) {
			$plugin =  new Socialstore_Plugin_Process_Product;
			$plugin->setProduct($product)->process('denied');
		}
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array(Zend_Registry::get('Zend_Translate')->_('Deny product successfully.'))));
	}

	public function deleteProductAction()
  	{
	    $form = $this->view->form = new Socialstore_Form_Admin_Product_Delete();
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
          	$values = $form->getValues();
	  		$product_id = $values['product_id'];
	  		$product = Engine_Api::_()->getItem('social_product', $product_id);
		      $this->view->product_id = $product->getIdentity();
		    // This is a smoothbox by default
		    if( null === $this->_helper->ajaxContext->getCurrentContext() )
		      $this->_helper->layout->setLayout('default-simple');
		    else // Otherwise no layout
		      $this->_helper->layout->disableLayout(true);
		    
			$product->deleted = 1;
		    $product->save();
			
			//delete actions and attachments
			$streamTbl = Engine_Api::_()->getDbTable('stream', 'activity');
			$streamTbl->delete('(`object_id` = '.$product_id.' AND `object_type` = "social_product")');
			$activityTbl = Engine_Api::_()->getDbTable('actions', 'activity');
			$activityTbl->delete('(`object_id` = '.$product_id.' AND `object_type` = "social_product")');
			$attachmentTbl = Engine_Api::_()->getDbTable('attachments', 'activity');
			$attachmentTbl->delete('(`id` = '.$product_id.' AND `type` = "social_product")');
					
		    Engine_Api::_()->getApi('search', 'core')->unindex($product);
      		$sendTo = Engine_Api::_()->getItem('user', $product->owner_id)->email;
		    $params = $product->toArray();
				   
		   // send mail to the seller
		   try
		   {
			Engine_Api::_()->getApi('mail','Socialstore')->send($sendTo, 'store_productdelete',$params);
			$Favourite = new Socialstore_Model_DbTable_Favourites;
			$select = $Favourite->select()->where('product_id = ?', $product_id);
			$results = $Favourite->fetchAll($select);
			if (count($results) > 0) {
				   // send mail to favouriter
				$store = Engine_Api::_()->getItem('social_store', $product->store_id);
				$params['product_title'] =  $product->title;
				$params['store_title'] =  $store->title;
				$params['store_link'] =  $store->getHref();
				foreach($results as $result){
					if ($result->user_id != 0) {
						$useremail = Engine_Api::_()->getItem('user', $result->user_id)->email;
						Engine_Api::_()->getApi('mail','Socialstore')->send($useremail, 'store_productdelfav',$params);
					}
				}
			}

			// send mail to buyers
		   }
		   catch(Exception $e)
		   {}
		    $this->view->success = true;
		    $this->_forward('success', 'utility', 'core', array(
						'smoothboxClose' => 10, 
						'parentRefresh' => 10, 
						'messages' => array('')));
	    }
	    if (!($product_id = $this->_getParam('product_id'))) {
      		throw new Zend_Exception('No Product specified');
	    }

	    //Generate form
	    $form->populate(array('product_id' => $product_id));
	    
	    //Output
	    $this->renderScript('admin-manage-product/form.tpl');
  	}
	public function editProductAction() {
	    
	    $product = Engine_Api::_()->getItem('social_product', $this->_getParam('product_id'));
	    
	   /* if( !$this->_helper->requireAuth()->setAuthParams($product, $viewer, 'product_edit')->isValid() ) 
	        return;
	    */// Prepare form
	    Zend_Registry::set('store_id', $product->store_id);
	        
	    $this->view->form = $form = new Socialstore_Form_Admin_Product_EditProduct(array(
	      'item' => $product
	    ));
		$form->removeElement('thumbnail');
	    $this->view->product = $product;
	    if ($product->product_type == 'downloadable') {
	    	$this->view->downloadable = '1';
	    }
		$form->removeElement('downloadable_file');
	    $form->removeElement('preview_file');
	    // Populate form
	   // date_default_timezone_set($viewer->timezone); 
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
	
	
	      if( empty($values['product_authcom']) ) {
	        $values['product_authcom'] = 'everyone';
	      }
	
	      $viewMax = array_search($values['product_authcom'], $roles);
	      $commentMax = array_search($values['product_authcom'], $roles);
	
	      foreach( $roles as $i => $role ) {
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
	    
		$this -> _helper -> redirector -> gotoRoute(array('module'=>'socialstore', 'controller'=>'manage-product', 'action' => 'index'), 'admin_default', true);
	}
}
