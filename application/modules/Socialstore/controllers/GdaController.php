<?php

class Socialstore_GdaController extends Core_Controller_Action_Standard {
    const MYSTORE_ID = 'MYSTORE_ID';
	public function addGdaAction() 
    {
        if(!Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection()) return $this->_helper->requireAuth->forward();
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $product = Engine_Api::_()->getItem('social_product', $this->_getParam('productId'));     
                                   
		if(!$product->gda || $product->approve_status != "approved" || !$viewer->getIdentity())
        {
            return $this->_helper->requireAuth->forward();
        }
        $this->view->form = $form = new Socialstore_Form_Gda_RequestGda();
         // If not post or form not valid, return
        if( !$this->getRequest()->isPost() ) {
            return;
        }
        $post = $this->getRequest()->getPost();
        if(!$form->isValid($post))
            return;

        // Process
        $table = Engine_Api::_()->getDbtable('gdarequests','Socialstore');
        $db = $table->getAdapter();
        $db->beginTransaction();
        $values = $form->getValues();
        try
        {
             if($values['gda'] == 0)
             {
                $form->getElement('link')->addError('Please complete this field - it is required. You must agree to the Term of Use and Privacy Statement to continue.'); 
                return;
            }
            // Create Wiki page
            $values = array_merge($form->getValues(), array(
                'user_id' => $viewer->getIdentity(),
                'store_id' => $product->store_id ,
                'product_id' => $product->product_id ,
            ));
            
            $gda = $table->createRow();
            $gda->setFromArray($values);
            $gda->save();
            
            //Send message to owner store
            $message = $values['org_message'];
            if( $product->getOwner()->getIdentity() != $viewer->getIdentity() ) {
            // Create conversation
              $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
                $viewer,
                $product->getOwner(),
                'Deal Request',
                $message,
                null
              );

              // Send notifications
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                  $product->getOwner(),
                  $viewer,
                  $conversation,
                  'message_new'
                );
              
              // Increment messages counter
              Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
            }
                    
            // Commit
            $db->commit();
            
            $this->_forward('success', 'utility', 'core', array(
                          'smoothboxClose' => true,
                          'parentRefresh' => false,
                          'format'=> 'smoothbox',
                          'messages' => array($this->view->translate('Request successfully.'))
                          ));
        }
        catch( Exception $e )
        {
          $db->rollBack();
          throw $e;
        }
	}
    public function manageGdaAction()
    {
        if(!Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection()) return $this->_helper->requireAuth->forward();
        
        if( !$this->_helper->requireUser()->isValid() ) return;    
        // get get current viewer
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this->view->viewer =  $viewer;
        $store = Engine_Api::_() -> getDbTable('SocialStores', 'Socialstore') -> getStoreByOwnerId($viewer -> getIdentity());
        Zend_Registry::set('active_menu','socialstore_main_mystore');
        
        // check if store is exists.
        if(is_object($store)){
            Zend_Registry::set(self::MYSTORE_ID, $store->getIdentity());
        }
        Zend_Registry::set('STOREMINIMENU_ACTIVE','manage-gda');
        $this->view->form = $form = new Socialstore_Form_Gda_GDASearch();
       
        $params = array();  
        if ($form->isValid($this->_getAllParams())) {
            $params = $form->getValues();
            $this->view->formValues = array_filter($params);  
             if($params['product_title'] || $params['status'] || $params['owner_name']) {
                $this->view->search = true;
             }
        }
        $params['store_id'] = $store->store_id;
        $params['page'] = $this->_getParam('page');
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.product.page', 10);
        $this -> view -> paginator = $paginator = Engine_Api::_()->getApi('product','Socialstore')->getGDARequestsPaginator($params);
        $paginator->setItemCountPerPage($items_per_page);
    }
    public function approveRequestAction()
    {
        if(!Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection()) return $this->_helper->requireAuth->forward();
        
        if( !$this->_helper->requireUser()->isValid() ) return;    
         $viewer = Engine_Api::_() -> user() -> getViewer();
         $store = Engine_Api::_() -> getDbTable('SocialStores', 'Socialstore') -> getStoreByOwnerId($viewer -> getIdentity());
         $gdaRequest = Engine_Api::_()->getItem('socialstore_gdarequest', $this->_getParam('gdaId'));  
         if(!$gdaRequest || $gdaRequest->store_id != $store->store_id)
            return $this->_helper->requireAuth->forward();
         // Options to select 1 deal exist.
         /*
         $this->view->form = $form = new Socialstore_Form_Gda_ApproveGda();
         // If not post or form not valid, return
        if( !$this->getRequest()->isPost() ) {
            return;
        }
        $post = $this->getRequest()->getPost();
        if(!$form->isValid($post))
            return;
        */
         // Redirect
        return $this->_helper->redirector->gotoRoute(array('controller'=>'gda','action' => 'create','gdaId'=>$gdaRequest->gdarequest_id), 'groupbuy_extended', true);
    }
    public function refuseRequestAction()
    {
        if(!Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection()) return $this->_helper->requireAuth->forward();
        
        if( !$this->_helper->requireUser()->isValid() ) return;     
        $viewer = Engine_Api::_() -> user() -> getViewer();
         $store = Engine_Api::_() -> getDbTable('SocialStores', 'Socialstore') -> getStoreByOwnerId($viewer -> getIdentity());
         $gdaRequest = Engine_Api::_()->getItem('socialstore_gdarequest', $this->_getParam('gdaId'));  
         if(!$gdaRequest || $gdaRequest->store_id != $store->store_id)
            return $this->_helper->requireAuth->forward(); 
        $gdaRequest->status = 'refused';
        $gdaRequest->save();
        $requester = Engine_Api::_()->getItem('user', $gdaRequest->user_id); 
        if($requester->getIdentity() != $viewer->getIdentity()) {
            // Create conversation
              $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
                $viewer,
                $requester,
                'Deal Request',
                "Your request has be refused.",
                null
              );

              // Send notifications
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                  $requester,
                  $viewer,
                  $conversation,
                  'message_new'
                );
              
              // Increment messages counter
              Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
         }
        $this->_forward('success', 'utility', 'core', array(
                          'smoothboxClose' => true,
                          'parentRefresh' => true,
                          'format'=> 'smoothbox',
                          'messages' => array($this->view->translate('Refuse successfully.'))
                          ));
    }
    public function deleteRequestAction()
    {
        if(!Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection()) return $this->_helper->requireAuth->forward();
        
        if( !$this->_helper->requireUser()->isValid() ) return;     
        $viewer = Engine_Api::_() -> user() -> getViewer();
         $store = Engine_Api::_() -> getDbTable('SocialStores', 'Socialstore') -> getStoreByOwnerId($viewer -> getIdentity());
         $gdaRequest = Engine_Api::_()->getItem('socialstore_gdarequest', $this->_getParam('gdaId'));  
         if(!$gdaRequest || $gdaRequest->store_id != $store->store_id)
            return $this->_helper->requireAuth->forward(); 
        $requester = Engine_Api::_()->getItem('user', $gdaRequest->user_id); 
        $gdaRequest->delete();
        
        if($requester->getIdentity() != $viewer->getIdentity()) {
            // Create conversation
              $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
                $viewer,
                $requester,
                'Deal Request',
                "Your request has be deleted.",
                null
              );

              // Send notifications
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                  $requester,
                  $viewer,
                  $conversation,
                  'message_new'
                );
              
              // Increment messages counter
              Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
         }
        $this->_forward('success', 'utility', 'core', array(
                          'smoothboxClose' => true,
                          'parentRefresh' => true,
                          'format'=> 'smoothbox',
                          'messages' => array($this->view->translate('Delete successfully.'))
                          ));
    }
    public function requestsAction()
    {
        if(!Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection()) return $this->_helper->requireAuth->forward();
        
        // get get current viewer
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.product.page', 10);
        $this->view->form = $form = new Socialstore_Form_Gda_RequestSearch();
        $params = array();  
        if ($form->isValid($this->_getAllParams())) {
            $params = $form->getValues();
            $this->view->formValues = array_filter($params);  
            if($params['product_title'] || $params['status'] || $params['deal_title']) {
                $this->view->search = true;
             }
        }
        $user_id = $this->_getParam('userId');
        if($user_id && $user_id != $viewer->user_id)
        {
            $params['user_id'] = $user_id;
            $params['status'] = 'approved';
            $form->removeElement('status');    
        }
        else
            $params['user_id'] = $viewer->user_id;
        $params['page'] = $this->_getParam('page');
        $this -> view -> paginator = $paginator = Engine_Api::_()->getApi('product','Socialstore')->getGDARequestsPaginator($params);
        $paginator->setItemCountPerPage($items_per_page);
    }
    public function storeRequestsAction()
    {
        if(!Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection()) return $this->_helper->requireAuth->forward();
        
        // get get current viewer
        if( !$this->_helper->requireUser()->isValid() ) return;
        $store_id = $this->_getParam('storeId');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.product.page', 10);
        $this->view->form = $form = new Socialstore_Form_Gda_RequestSearch();
        $form->removeElement('status');
        $params = array();  
        if ($form->isValid($this->_getAllParams())) {
            $params = $form->getValues();
            $this->view->formValues = array_filter($params);  
            if($params['product_title'] || $params['deal_title']) {
                $this->view->search = true;
             }
        }
        $params['store_id'] = $store_id;
        $params['status'] = 'approved';
        $params['page'] = $this->_getParam('page');
        $this -> view -> paginator = $paginator = Engine_Api::_()->getApi('product','Socialstore')->getGDARequestsPaginator($params);
        $paginator->setItemCountPerPage($items_per_page);
    }
}
