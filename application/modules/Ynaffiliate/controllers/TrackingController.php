<?php

class Ynaffiliate_TrackingController extends Core_Controller_Action_Standard {

   public function init() {
      if(!$this -> _helper -> requireUser() -> isValid()){
         return ;
      }
      $affiliate = new Ynaffiliate_Plugin_Menus;
      if(!$affiliate->canView())
      {
         $this->_redirect('/affiliate/index');

      }
      $this->view->headScript()->appendFile($this->view->baseUrl() . '/application/modules/Ynaffiliate/externals/scripts/datepicker.js');
      $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/application/modules/Ynaffiliate/externals/styles/datepicker_jqui/datepicker_jqui.css');
      $this->view->headScript()->appendFile($this->view->baseUrl() . '/application/modules/Ynaffiliate/externals/scripts/ynaffiliate_date.js');
   }

   public function indexAction() {

   }

   public function purchaseAction() {
      $this -> _helper -> content -> setEnabled();
      $page = $this -> _getParam('page', 1);
      $this->view->form = $form = new Ynaffiliate_Form_Tracking_Purchase();
      $values = array();
      if($form -> isValid($this->_getAllParams())) {
         $values = $form -> getValues();
      }
	  $viewer = Engine_Api::_()->user()->getViewer();
      $values['user_id'] = $viewer -> getIdentity();
      $this->view->formValues = $values;

      $commissionsTable = Engine_Api::_()->getDbTable('commissions', 'ynaffiliate');
      $this->view->approved_commission_count = $commissionsTable->countCommission('approved', $viewer -> getIdentity());
      $this->view->waiting_commission_count = $commissionsTable->countCommission('waiting', $viewer -> getIdentity());
      $this->view->delaying_commission_count = $commissionsTable->countCommission('delaying', $viewer -> getIdentity());
	  $this -> view -> paginator = Engine_Api::_()->ynaffiliate()->getCommissionsPaginator($values);

	  $this->view->paginator->setCurrentPageNumber($page);
      $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.page', 10);
      $this->view->paginator->setItemCountPerPage($limit);
   }

   public function clickAction() {
      $this -> _helper -> content -> setEnabled();
      $this->view->form = $form = new Ynaffiliate_Form_Tracking_Click();

      $page = $this->_getParam('page', 1);
      $values = array();
      $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();

      if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
         $values = $form->getValues();

      }
      $values['user_id'] = $user_id;
      $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.page', 10);
      $values['limit'] = $limit;
      $this->view->viewer = Engine_Api::_()->user()->getViewer();
      $this->view->paginator = $paginator = Engine_Api::_()->ynaffiliate()->getLinksPaginator($values);
      $this->view->paginator->setCurrentPageNumber($page);
      $this->view->formValues = $values;

   }

   public function registrationAction() {
      $this->view->form = $form = new Ynaffiliate_Form_Tracking_Registration();
   }

}
