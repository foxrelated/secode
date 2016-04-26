<?php

class Socialstore_AdminManageStoreController extends Core_Controller_Action_Admin {

	public function init() {
		parent::init();
		Zend_Registry::set('admin_active_menu', 'socialstore_admin_main_managestore');
	}

	public function indexAction() {

		$page = $this -> _getParam('page', 1);
		$this -> view -> form = $form = new Socialstore_Form_Admin_Search();
		$values = array();
		if($form -> isValid($this -> _getAllParams())) {
			$values = $form -> getValues();
			if(empty($values['order'])) {
				$values['order'] = 'store_id';
			}
			if(empty($values['direction'])) {
				$values['direction'] = 'DESC';
			}
			$this -> view -> filterValues = $values;
			$this -> view -> order = $values['order'];
			$this -> view -> direction = $values['direction'];
			$table = new Socialstore_Model_DbTable_SocialStores();
			$stores = $table -> fetchAll(Engine_Api::_() -> getApi('store','Socialstore') -> getStoresSelect($values)) -> toArray();
			$this -> view -> count = count($stores);
		}
		$limit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('store.page', 10);
		$values['limit'] = $limit;
		$this -> view -> viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> paginator = Engine_Api::_() -> getApi('store','Socialstore') -> getStoresPaginator($values);
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
				$store = Engine_Api::_() -> getItem('social_store', $id);
				if($store) {
					$store -> deleted = 1;
					$store -> save();
					$products = $store->getProductsOfStore();
					foreach($products as $product) {
						$product -> deleted = 1;
						$product -> save();
					}
					$sendTo = $store -> getOwner() -> email;
					$params = $store -> toArray();
					/*
					 // send to seller
					 Engine_Api::_()->getApi('mail','groupbuy')->send($sendTo, 'groupbuy_sellerdealdel', $params);

					 // send to buyer
					 foreach($deal->getBuyerEmails() as $buyerEmail){
					 Engine_Api::_()->getApi('mail','groupbuy')->send($buyerEmail, 'groupbuy_buyerdealdel', $params,1);
					 }	*/
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
				$store = Engine_Api::_() -> getItem('social_store', $id);
				if($store && $store -> approve_status != 'approved' && $store -> approve_status != 'denied') {
					$plugin =  new Socialstore_Plugin_Process_Store;
					$plugin->setStore($store)->process('accept');
				}
			}
			$this -> _helper -> redirector -> gotoRoute(array('action' => 'index'));
		}

	}

	public function featuredAction() {
		$store_id = $this -> _getParam('socialstore');
		$store_good = $this -> _getParam('good');
		$store = Engine_Api::_() -> getItem('social_store', $store_id);
		if($store) {
			$store -> featured = $store_good;
			$store -> save();
		}
	}

	public function showAction() {
		$store_id = $this -> _getParam('socialstore');
		$store_show = $this -> _getParam('show');
		$store = Engine_Api::_() -> getItem('social_store', $store_id);
		if($store) {
			$store -> view_status = ($store_show == 1) ? 'show' : 'hide';
			$store -> save();
		}
		if ($store_show == '0') {
			$products = $store->getProductsOfStore();
			foreach ($products as $product) {
				$product -> view_status = 'hide';
				$product -> save();
			}
		}
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array(Zend_Registry::get('Zend_Translate')->_('Change successfully.'))));
	}

	public function approveStoreAction() {
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$store_id = $this -> _getParam('socialstore');
		$store = Engine_Api::_() -> getItem('social_store', $store_id);

		if($store && $store -> approve_status != 'approved' && $store -> approve_status != 'denied') {
			$plugin =  new Socialstore_Plugin_Process_Store;
			$plugin->setStore($store)->process('accept');
			//$store->view_status = 'show';
			//$store->save();
		}

		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array(Zend_Registry::get('Zend_Translate')->_('Approve store successfully.'))));
	}

	public function denyStoreAction() {
		$store_id = $this -> _getParam('socialstore');
		$store = Engine_Api::_() -> getItem('social_store', $store_id);
		if($store) {
			$plugin =  new Socialstore_Plugin_Process_Store;
			$plugin->setStore($store)->process('denied');
		}
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array(Zend_Registry::get('Zend_Translate')->_('Deny store successfully.'))));
	}
	
	public function deleteStoreAction()
  	{
	    $form = $this->view->form = new Socialstore_Form_Admin_Delete();
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
          	$values = $form->getValues();
	  		$store_id = $values['store_id'];
          	$store = Engine_Api::_()->getItem('social_store', $store_id);
		      $this->view->store_id = $store->getIdentity();
		    // This is a smoothbox by default
		    if( null === $this->_helper->ajaxContext->getCurrentContext() )
		      $this->_helper->layout->setLayout('default-simple');
		    else // Otherwise no layout
		      $this->_helper->layout->disableLayout(true);
		    
		      	/*	$sendTo = $deal->getOwner()->email;
		           $params = $deal->toArray();
				   
				   // send mail to the seller
				   Engine_Api::_()->getApi('mail','groupbuy')->send($sendTo, 'groupbuy_sellerdealdel',$params);
				   
				   // send mail to all buyers
				   foreach($deal->getBuyerEmails() as $buyerEmail){
					   	$params['total_amount'] =  $buyerEmail['total_amount'];
						$params['total_number'] =  $buyerEmail['total_number'];
					   	Engine_Api::_()->getApi('mail','groupbuy')->send($buyerEmail['email'], 'groupbuy_buyerdealdel',$params);
				   }*/
									   
		      $store->deleted = 1;
		      $store->save();
		      
		      $products = $store->getProductsOfStore();
		      foreach ($products as $product) {
			  		$product->deleted = 1;
			  		$product->save();
					Engine_Api::_()->getApi('search', 'core')->unindex($product);
		      }
		      
			$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => 10, 
					'parentRefresh' => 10, 
					'messages' => array('')));
		      
	    }
	    if (!($store_id = $this->_getParam('store_id'))) {
      		throw new Zend_Exception('No Store specified');
    	}

	    //Generate form
	    $form->populate(array('store_id' => $store_id));
	    
	    //Output
	    $this->renderScript('admin-manage-store/form.tpl');
  	}

  	public function editStoreAction() {
  		$store = Engine_Api::_() -> getItem('social_store', $this->_getParam('store_id'));
		
		
		/*if(!$this -> _helper -> requireAuth() -> setAuthParams('social_store', $viewer, 'store_edit') -> isValid()) {
			return ;
		}*/
		
		// Prepare form
		$this -> view -> form = $form = new Socialstore_Form_Admin_EditStore( array('item' => $store));
		$form -> removeElement('thumbnail');
		$form -> removeElement('cancel');
		$this -> view -> store = $store;
		// Populate form

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
			$store -> save();

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
		
		$this -> _helper -> redirector -> gotoRoute(array('action' => 'index'));
		
  	}
  	
  	public function statisticAction() {
  		$store_id = $this->_getParam('store_id');
  		$store = Engine_Api::_()->getItem('social_store', $store_id);
  		$this->view->store = $store;
  	}
  	
  	public function productStatisticAction() {
  		$store_id = $this->_getParam('store_id');
  		$store = Engine_Api::_()->getItem('social_store', $store_id);
  		$this->view->store = $store;
		$Product = new Socialstore_Model_DbTable_Products;
		$select = $Product->select()->from($Product->info('name'), array('*', 'product'=>new Zend_Db_Expr('sold_qty * price')))->where('store_id = ?', $store->store_id);
  		$select->where('deleted = 0');
		$this -> view -> form = $form = new Socialstore_Form_Admin_SearchProductStatistic();
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
			if ($values['order'] != 'total_amount') {
				$select->order($values['order']." ".$values['direction']);
			}
			else {
				$select->order('product ' .$values['direction']);
			}
			if (!empty($values['search']) && $search = trim($values['search'])) {
				$select->where('title LIKE ?', '%'.$search.'%');
			}
			if (isset($values['feature']) && $values['feature'] != ' ') {
				$select->where('featured = ?', $values['feature']);
			}
		}
		$paginator = $this -> view -> paginator = Zend_Paginator::factory($select);
		$page = $this->_getParam('page', 1);
		$paginator -> setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(10);
  	}
  	
	public function transactionHistoryAction(){
		$product_id = $this->_getParam('product_id');
		$product = Engine_Api::_()->getItem('social_product',$product_id);
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
  	
}
