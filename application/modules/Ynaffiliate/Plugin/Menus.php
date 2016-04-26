<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Ynaffiliate_Plugin_Menus {

   public function canSignup() {

      // Must be logged in
      $viewer = Engine_Api::_()->user()->getViewer();
      if (!$viewer || !$viewer->getIdentity()) {
         return false;
      }

      //the user have not affiliate account
      $model = Engine_Api::_()->getDbTable('accounts', 'ynaffiliate');
      $select = $model->select()->where('user_id=?', $viewer->user_id);
      $item = $model->fetchAll($select);

      if (count($item)) {
         return false;
      }
      return true;
      // return false;
   }

   public function canManage() {
      // Must be logged in
      $viewer = Engine_Api::_()->user()->getViewer();
      if (!$viewer || !$viewer->getIdentity()) {
         return false;
      }

      //the user have not affiliate account
      $model = Engine_Api::_()->getDbTable('accounts', 'ynaffiliate');
      $select = $model->select()->where('user_id=?', $viewer->user_id);
      $item = $model->fetchAll($select);

      if (is_object($item)) {
         return true;
      }
      return false;
      // return false;
   }

   public function canView() {

      $viewer = Engine_Api::_()->user()->getViewer();
      if (!$viewer || !$viewer->getIdentity()) {
         return false;
      }
      
      $account = Engine_Api::_()->getApi('Core', 'Ynaffiliate')->getAccount();

      if (!is_object($account)) {
         return false;
       
      }

      if (!$account->isApproved()) {
         return false;
      }
        if ($account->isApproved()==2) {
         return false;
      }
        
      return true;
   }

}

?>
