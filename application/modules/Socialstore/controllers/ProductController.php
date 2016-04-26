<?php

// public page
class Socialstore_ProductController extends Core_Controller_Action_Standard{
	public function init(){
		
		Zend_Registry::set('active_menu','socialstore_main_product');
		$viewer = Engine_Api::_()->user()->getViewer();
		if( (!$this -> _helper -> requireAuth() -> setAuthParams('social_store', $viewer, 'store_view')) || (!$this -> _helper -> requireAuth() -> setAuthParams('social_product', $viewer, 'product_view'))) 
		{
      		return false;
    	}
		$this->view->headScript()
    	->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places');
	}
	
	/**
	 * product home page should display as landing page.
	 * allow admin configure this page.
	 */
	public function indexAction(){
		$this->_helper->content
         ->setNoRender()
           ->setEnabled()
            ;
		
	}
	public function index2Action(){
		//$viewer = Engine_Api::_()->user()->getViewer();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.product.page', 10);
		$this->view->items_per_page = $items_per_page;
		$params['page'] = $request -> getParam('page');
		//$this -> view -> user_id = $user_id = $viewer -> getIdentity();
		
		//$params['store_id'] = Zend_Registry::get('store_id');
		
		$this -> view -> paginator = $paginator = Engine_Api::_()->getApi('product','Socialstore')->getProductsPaginator($params);
		$paginator->setItemCountPerPage($items_per_page);
		
	}
	
  	public function deleteProductAction()
  	{
	    $form = $this->view->form = new Socialstore_Form_Product_Delete();
	    $user = Engine_Api::_()->user()->getViewer();
		
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
          	$values = $form->getValues();
	  		$product_id = $values['product_id'];
	  		$product = Engine_Api::_()->getItem('social_product', $product_id);
	    	if( !$this->_helper->requireAuth()->setAuthParams($product, $viewer, 'product_delete')->isValid() ) {
				return;
	    	}
			$viewer = Engine_Api::_()->user()->getViewer();
			if ($viewer->getIdentity() != $product->owner_id || (!is_object($product))) {
				$this->view->success = true;
				return $this->_forward('success', 'utility', 'core', array(
						'smoothboxClose' => 10, 
						'parentRefresh' => 10, 
						'messages' => array(Zend_Registry::get('Zend_Translate')->_('You cannot delete this product!'))));
			}
			else {
	  		$this->view->product_id = $product->getIdentity();
		    // This is a smoothbox by default
		    if( null === $this->_helper->ajaxContext->getCurrentContext() )
		      $this->_helper->layout->setLayout('default-simple');
		    else // Otherwise no layout
		      $this->_helper->layout->disableLayout(true);
		    
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
		      $this->view->success = true;
		      $this->_forward('success', 'utility', 'core', array(
						'smoothboxClose' => 10, 
						'parentRefresh' => 10, 
						'messages' => array('')));
			}
	    }
	    if (!($product_id = $this->_getParam('product_id'))) {
      		throw new Zend_Exception('No Product specified');
	    }

	    //Generate form
	    $form->populate(array('product_id' => $product_id));
	    
	    //Output
	    $this->renderScript('my-store/form.tpl');
  		
  	}
  	
  	public function rateProductAction()
	{
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        if( !$this->_helper->requireUser()->isValid() ) return;

        $product_id = (int) $this->_getParam('product_id');
        $rates = (int)  $this->_getParam('rates');

        $viewer = Engine_Api::_()->user()->getViewer();

        if ($rates == 0 || $product_id == 0)
        {
            return;
        }
        
        $product = Engine_Api::_()->getItem('social_product', $product_id);
        $can_rate = Engine_Api::_()->getApi('product','Socialstore')->canRate($product,$viewer->getIdentity());
        // Check user rated
        if (!$can_rate)
        {
            return;
        }            
        $rateTable = Engine_Api::_()->getDbtable('rates', 'Socialstore');
        $db = $rateTable->getAdapter();
        $db->beginTransaction();
        try
        {
            $rate = $rateTable->createRow();
            $rate->user_id = $viewer->getIdentity();
            $rate->item_id = $product_id;
            $rate->rate_number  = $rates;
            $rate->save();
            $rates = Engine_Api::_()->getApi('product','Socialstore')->getAVGrate($product_id);
            $product->rate_ave = $rates;
            $product->save();
            // Commit
            $db->commit();
        }

        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }
        $route=Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
        return $this->_redirect($route."/product/detail/product_id/".$product_id);
	}
  	
	public function listingAction() {
		$values = $this->_getAllParams();
		Zend_Registry::set('product_search_params', $values);
		$this->_helper->content
         ->setNoRender()
           ->setEnabled()
            ;
	}
	
	public function storeListProductAction() {
		$values = $this->_getAllParams();
		Zend_Registry::set('product_search_params', $values);
		$this->_helper->content
         ->setNoRender()
           ->setEnabled()
            ;
		/*$viewer = Engine_Api::_() -> user() -> getViewer();		
		$items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.product.page', 10);
		$this->view->items_per_page = $items_per_page;
		$values['page'] = $this->_getParam('page', 1);
		$values['view_status'] = 'show';
		$values['approve_status'] = 'approved';
		$this -> view -> user_id = $user_id = $viewer -> getIdentity();
		$this->view->paginator = $paginator = Engine_Api::_()->getApi('product','Socialstore')->getProductsPaginator($values);
		$paginator->setItemCountPerPage($items_per_page);*/
	}
	public function detailAction() {
		$product_id = $this->_getParam('product_id');
		$product = Engine_Api::_()->getItem('social_product', $product_id);
		if (!$product) {
			return;
		}
		$store = $product->getStore();
		$store_id = $store->store_id;
		Zend_Registry::set('store_info_id', $store_id);
		Zend_Registry::set('store_detail_id', $store_id);
		Zend_Registry::set('product_detail_id',$product_id);
		
		$this->_helper->content
        ->setNoRender()
        ->setEnabled();
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
	
}