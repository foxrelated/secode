<?php

class Socialstore_AdminRefundController extends Core_Controller_Action_Admin{
	
	public function init() {
		Zend_Registry::set('admin_active_menu', 'socialstore_admin_main_refund');
	}
	
	public function indexAction(){
		$page = $this->_getParam('page',1);
    	//$this->view->form = $form = new Socialstore_Form_Admin_Order_Search();
		$values = array();  
    	//if ($form->isValid($this->_getAllParams())) {
    	//	$values = $form->getValues();
    	
    	//}
    	$limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.page', 10);
    	$values['limit'] = $limit;
    	$values['refund_status'] = 1;
    	$this->view->paginator = Socialstore_Api_Order::getInstance()->getOrderItemsPaginator($values); 
    	
    	$this->view->paginator->setCurrentPageNumber($page);
    	$this->view->formValues = $values; 
  	
	}
}
