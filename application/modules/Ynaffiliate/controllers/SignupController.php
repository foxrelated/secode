<?php

class Ynaffiliate_SignupController extends Core_Controller_Action_Standard {

   public function init() {
      if (!$this->_helper->requireUser()->isValid()) {
         return;
      }
      Zend_Registry::set('active_menu', 'ynaffiliate_main_index');
   }

   public function indexAction() {

      $this->view->success = false;

      $account = Engine_Api::_()->getApi('Core', 'Ynaffiliate')->getAccount();

      if (is_object($account)) {
         $url = $this->getFrontController()->getRouter()->assemble(array(), 'ynaffiliate_memberhome', true);
         $this->_helper->redirector->setPrependBase(false)->gotoUrl($url);
      }
      $user = Engine_Api::_()->user()->getViewer();
      $this->view->form = $form = new Ynaffiliate_Form_Signup( array('item' => $user));   
     
      if (!$this->getRequest()->isPost()) {
         return;
      }

      if (!$form->isValid($this->getRequest()->getPost())) {
         return;
      }

      $values = $form->getValues();
      $values['contact_name'] = $user->getTitle();
      $id = $user->level_id;

//      $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
//      $auto_approve = $permissionsTable->getAllowed('ynaffiliate', $id, array(0 => 'auto_approve'));
//      $auto_approve = $auto_approve['auto_approve'];
      $auto_approve= Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.autoapprove', 1);

//      echo $auto_approve;
//      die('jay');
      $table = Engine_Api::_()->getDbTable('accounts', 'ynaffiliate');
      $db = $table->getAdapter();
      $db->beginTransaction();

      try {
         $acc = $table->createRow();
         $acc->setFromArray($values);       
         $acc->user_id = $user->user_id;
         $acc->approved = $auto_approve;
         $acc->creation_date = date('Y-m-d H:i:s');        
         $acc->save();
         $db->commit();
         $this->view->success = true;
      } catch (Exception $e) {
         $db->rollBack();
         throw $e;
      }
       $this->_redirect('/affiliate/index');
   }

   public function successAction() {
      
   }

}
