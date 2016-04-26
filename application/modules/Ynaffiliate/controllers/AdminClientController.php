<?php

class Ynaffiliate_AdminClientController extends Core_Controller_Action_Admin {

   	public function init() {
    	Zend_Registry::set('admin_active_menu', 'ynaffiliate_admin_main_client');      
    	$this->view->headScript()->appendFile($this->view->baseUrl() . '/application/modules/Ynaffiliate/externals/scripts/datepicker.js');
    	$this->view->headScript()->appendFile($this->view->baseUrl() . '/application/modules/Ynaffiliate/externals/scripts/ynaffiliate_date.js');
    	$this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/application/modules/Ynaffiliate/externals/styles/datepicker_jqui/datepicker_jqui.css');
		$this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/application/modules/Ynaffiliate/externals/styles/main.css');
   	}

	public function indexAction() {
		
		$this->view->form = $form = new Ynaffiliate_Form_Admin_Manage_AffiliateClient();
		$page = $this->_getParam('page', 1);
      	$values = array();
      	if ($form->isValid($this->_getAllParams())) {
      		$values = $form->getValues();
      	}
  	    $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.page', 10);
  	    $values['limit'] = $limit;
      	$this->view->viewer = Engine_Api::_()->user()->getViewer();
      	$this->view->paginator = $paginator = Engine_Api::_()->ynaffiliate()->getMyAffiliatesPaginator($values);
      	$this->view->paginator->setCurrentPageNumber($page);
      	$this->view->formValues = $values;
   	}	


}
