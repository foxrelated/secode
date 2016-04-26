<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Ynaffiliate_CommissionRuleController extends Core_Controller_Action_Standard {

   protected $_fieldType = 'user';
   protected $_requireProfileType = true;


   public function init() {
      if (!$this->_helper->requireUser()->isValid()) {
         return;
      }

      $this->_MAX_COMMISSION_LEVEL = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.max.commission.level', 5);
   }

   public function indexAction() {
      $this -> _helper -> content -> setEnabled();

      // Get level id of viewing user
      if( null !== ($id = $this->_getParam('level_id')) ) {
         $level = Engine_Api::_()->getItem('authorization_level', $id);
      } else {
         $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
      }

      $viewer = Engine_Api::_()->user()->getViewer();

      if( !$level instanceof Authorization_Model_Level ) {
         throw new Engine_Exception('missing level');
      }

      $level_id = $level->level_id;

      if ($this->getRequest()->isPost()) {
         $level_id = $_POST['kitty'];
      }

      $this->view->level_id = $level_id;

      // Prepare user levels
      $levelOptions = array();
      $authorizationTable = Engine_Api::_()->getDbtable('levels', 'authorization');
      $select = $authorizationTable->select()->where('type != ?', 'public');
      foreach( $authorizationTable->fetchAll($select) as $level ) {
         $leveloption = array();
         $leveloption['level_id'] = $level->level_id;
         $leveloption['label'] = $level->getTitle();
         $levelOptions[] = $leveloption;
      }

      $this->view->levelOptions = $levelOptions;

      $page = $this->_getParam('page', 1);

      $rule_table = Engine_Api::_()->getDbTable('rules', 'ynaffiliate');
      $db = $rule_table->getAdapter();
      $select = Engine_Api::_()->ynaffiliate()->getCommissionRulesSelect($level_id);

      $data = $db->fetchAll($select);

      $data2 = null;
      $j = 0;
      for ($i = 0; $i < count($data); $i++) {

         $j = $data[$i]['ruleid'];
         $data2[$j]['rulemap_id'] = $data[$i]['rulemap_id'];

         $data2[$j]['rule_title'] = $data[$i]['rule_title'];
         $data2[$j]['rule_id'] = $data[$i]['ruleid'];
         $data2[$j]['enabled'] = $data[$i]['enabled'];

         $commission_level = $data[$i]['level'];
         if ($commission_level <= $this->_MAX_COMMISSION_LEVEL) {
            if (isset($data[$i]['rule_value'])) {
               $data2[$j]["level_$commission_level"] = $data[$i]['rule_value'];
            } else {
               $data2[$j]["level_$commission_level"] = null;
            }
         }
      }

      $this->view->MAX_COMMISSION_LEVEL = $this->_MAX_COMMISSION_LEVEL;
      $this->view->paginator = $paginator = Zend_Paginator::factory($data2);
      $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.page', 10);
      $this->view->paginator->setItemCountPerPage($limit);
      $this->view->paginator->setCurrentPageNumber($page);
      $this -> view -> formValues = array('level_id' => $level_id);
   }
}
?>
