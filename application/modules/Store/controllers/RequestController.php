<?php
class Store_RequestController extends Store_Controller_Action_Standard {
	public function indexAction() {
		$this->view->product = $product = Engine_Api::_()->getItem('store_product', $this->_getParam('id', 0));
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$product) {
			return $this->_helper->requireSubject()->forward();
		}
		if (!$product->isOwner($viewer)) {
			return $this->_helper->requireAuth()->forward();
		}
		$this->view->paginator = $paginator = $product->getRequests();
		$paginator->setItemCountPerPage(10);
      	$paginator->setCurrentPageNumber($this->_getParam('page', 1));
	}
	
	public function dismissAction() {
		$this->_helper->layout->setLayout('default-simple');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$request = $this->view->request = Engine_Api::_()->getItem('store_brequest', $this->_getParam('id'), 0);
		if (!$request) {
			$this->_forward('success', 'utility', 'core', array(
	            'parentRefresh' => false,
	            'smoothboxClose' => true,
	            'messages' => Array($this->view->translate('We are sorry, the request cannot be found.'))
	        ));
		}
		
		$this->view->form = $form = new Store_Form_Request_Dismiss();
		
		if(!$this->getRequest()->isPost()) {
	      	return;
	    }
		
		$request->dismiss();
		$this->_forward('success', 'utility', 'core', array(
            'parentRefresh' => true,
            'smoothboxClose' => true,
            'messages' => $this->view->translate('This request has been dismissed!')
        ));
	}
	
	public function viewAction() {
		$this->_helper->layout->setLayout('default-simple');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$request = $this->view->request = Engine_Api::_()->getItem('store_brequest', $this->_getParam('id'), 0);
		if (!$request) {
			$this->_forward('success', 'utility', 'core', array(
	            'parentRefresh' => false,
	            'smoothboxClose' => true,
	            'messages' => Array($this->view->translate('We are sorry, the request cannot be found.'))
	        ));
		}
		
		if ($request->status != 'pending') {
			$this->_forward('success', 'utility', 'core', array(
	            'parentRefresh' => true,
	            'smoothboxClose' => true,
	            'messages' => ''
	        ));
		}
		
		if(!$this->getRequest()->isPost()) {
	      	return;
	    }
		
		$this->_forward('success', 'utility', 'core', array(
            'parentRedirect' => $this->view->url(array('action'=>'summary','id'=>$request->getIdentity()),'store_request',true),
            'messages' => $this->view->translate('Please wait...')
        ));
	}
	
	public function summaryAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$request = $this->view->request = Engine_Api::_()->getItem('store_brequest', $this->_getParam('id'), 0);
		if (!$request) {
			return $this->_helper->requireSubject()->forward();
		}
		$product = $request->getProduct();
		$owner = $product->getOwner();
		
		// Shipping Details
	    $detailsTbl = Engine_Api::_()->getDbTable('details', 'store');
	    $locationsTbl = Engine_Api::_()->getDbTable('locations', 'store');
	    $this->view->details = $details = $detailsTbl->getDetails($owner);
	    if ($details['c_location']) {
	      $this->view->country = $details['c_country'];
	      $this->view->region = $details['c_state'];
	    } else {
	      $this->view->country = $locationsTbl->fetchRow(array('location_id = ?' => (int)$details['location_id_1']));
	      $this->view->region = $locationsTbl->fetchRow(array('location_id = ?' => (int)$details['location_id_2']));
	    }
		
		$totalTaxAmt = 0;
		$totalShippingAmt = 0;
		if (!$request->credit) {
			$products = $request->getProducts();
			foreach ($products as $item) {
				$totalTaxAmt += (double)$item->getTax();
			}
			if (null == ($location_id = $detailsTbl->getDetail($owner, 'state'))) {
		      	$location_id = $detailsTbl->getDetail($owner, 'country');
		    }
			if (!empty($location_id)) {
				foreach ($products as $item) {
					$totalShippingAmt += (double)$product->getShippingPrice($location_id);
				}
			}
		}
		$this->view->totalPrice = $product->price; // Added by Alvaro but is wrong
		$this->view->shippingPrice = $totalShippingAmt;
	    	$this->view->taxesPrice = $totalTaxAmt;
	}

	public function confirmAction() {
		$this->_helper->layout->setLayout('default-simple');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$request = $this->view->request = Engine_Api::_()->getItem('store_brequest', $this->_getParam('id'), 0);
		if (!$request) {
			$this->_forward('success', 'utility', 'core', array(
	            'parentRefresh' => false,
	            'smoothboxClose' => true,
	            'messages' => Array($this->view->translate('We are sorry, the request cannot be found.'))
	        ));
		}
		
		$this->view->form = $form = new Store_Form_Request_Confirm();
		
		if(!$this->getRequest()->isPost()) {
	      	return;
	    }
		
		$request->approve();
		$this->_forward('success', 'utility', 'core', array(
            'parentRedirect' => $this->view->url(array('action'=>'complete','id'=>$request->getIdentity()),'store_request',true),
            'messages' => $this->view->translate('Please wait...')
        ));
	}
	
	public function completeAction() {
		$request = $this->view->request = Engine_Api::_()->getItem('store_brequest', $this->_getParam('id'), 0);
		$this->view->user = $request->getProduct()->getOwner();
	}
}
