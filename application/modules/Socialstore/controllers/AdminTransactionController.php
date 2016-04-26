<?php
class Socialstore_AdminTransactionController extends Core_Controller_Action_Admin 
{
	public function init(){
		parent::init();
		Zend_Registry::set('admin_active_menu', 'socialstore_admin_main_transactions');
	}

	public function indexAction(){
      	$page = $this->_getParam('page',1);
    	$this->view->form = $form = new Socialstore_Form_Admin_Transaction_Search();
		$values = array();  
    	if ($form->isValid($this->_getAllParams())) {
    		$values = $form->getValues();
    	}
    	$limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.page', 10);
    	$values['limit'] = $limit;
    	$viewer = Engine_Api::_()->user()->getViewer();
    	$this->view->viewer = $viewer;
    	$this->view->paginator = Socialstore_Api_Transaction::getInstance()->getTransactionsPaginator($values); 
    	
    	$this->view->paginator->setCurrentPageNumber($page);
    	$this->view->formValues = $values; 
  	
	}
}