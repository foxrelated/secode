<?php

class Ynaffiliate_AdminCommissionRuleController extends Core_Controller_Action_Admin {

   protected $_fieldType = 'user';
   protected $_requireProfileType = true;

   // Level of commissions
   public function init() {
      $this->_MAX_COMMISSION_LEVEL = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.max.commission.level', 5);
   }

   public function indexAction() {
      Zend_Registry::set('admin_active_menu', 'ynaffiliate_admin_main_rule');
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('ynaffiliate_admin_main', array(), 'ynaffiliate_admin_main_rule');

      // Get level id
      if( null !== ($id = $this->_getParam('level_id')) ) {
         $level = Engine_Api::_()->getItem('authorization_level', $id);
      } else {
         $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
      }

      if( !$level instanceof Authorization_Model_Level ) {
         throw new Engine_Exception('missing level');
      }

      $level_id = $level->level_id;
      $this->view->level_id = $level_id;

      // Prepare user levels
      $levelOptions = array();
      $authorizationTable = Engine_Api::_()->getDbtable('levels', 'authorization');
      $select = $authorizationTable->select()->where('type != ?', 'public');
      $levels = $authorizationTable->fetchAll($select);
      foreach( $levels as $level ) {
         $levelOptions[$level->level_id] = $level->getTitle();
      }

      $this->view->levelOptions = $levelOptions;

      $page = $this->_getParam('page', 1);

      $rule_table = Engine_Api::_()->getDbTable('rules', 'ynaffiliate');
      $db = $rule_table->getAdapter();

      // get all commission rule maps detail, they may have duplicate rule_id, may consider implement level value to another table
      $select = Engine_Api::_()->ynaffiliate()->getCommissionRulesSelect($level_id);

      $data = $db->fetchAll($select);
      $data2 = null;

      // now merger duplicated rule maps with same rule_id, create level_x for each level value
      $j = 0;
      for ($i = 0, $max = count($data); $i < $max; $i++) {
         $j = $data[$i]['ruleid'];
         $data2[$j]['rulemap_id'] = $data[$i]['rulemap_id'];
         $data2[$j]['rule_title'] = $data[$i]['rule_title'];
         $data2[$j]['rule_id'] = $data[$i]['ruleid'];
         $data2[$j]['enabled'] = $data[$i]['enabled'];

         // only load a number of max level level larger than max is ignored
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

   public function editAction() {
      $level_id = $this->getRequest()->getParam('level_id');
      $rule_id = $this->getRequest()->getParam('rule_id');
      $rulemaps_table = Engine_Api::_()->getDbTable('rulemaps', 'ynaffiliate');
      $rulemapdetails_table = Engine_Api::_()->getDbTable('rulemapdetails', 'ynaffiliate');

      $this->view->form = $form = new Ynaffiliate_Form_Admin_Commission_Rule();

      $check = $rulemaps_table->checkRuleMap($rule_id, $level_id);
      if (count($check) > 0) {
         //the rule is set, dump data to form
         $dt = $rulemapdetails_table->getRuleMapDetailById($check['rulemap_id']);
         foreach ($dt as $row) {
            if ($row->level <= $this->_MAX_COMMISSION_LEVEL) {
               $form->{"level_$row->level"}->setValue((float) ($row->rule_value));
            }
         }
      }

      if (!$this->getRequest()->isPost()) {
         return;
      }

      if (!$form->isValid($this->getRequest()->getPost())) {
         return;
      }

      $values = $form->getValues();

      if (count($check) > 0) {
         //the rule is set => Edit rule
         $rulemap_id = $check['rulemap_id'];
         $db = $rulemapdetails_table->getAdapter();
         $db->beginTransaction();
         try {
            // admin has increased max level, create remain rules
            for ($i = 1; $i <= $this->_MAX_COMMISSION_LEVEL; $i++) {
               $select = $rulemapdetails_table->select()->where('rule_map = ?', $rulemap_id)->where('level = ?', $i)->limit(1);
               $row = $rulemapdetails_table->fetchRow($select);
               if ($row) {
                  $row->rule_value = round($values["level_$i"], 2);
                  $row->save();
               } else {
                  $row = $rulemapdetails_table->createRow();
                  $row->rule_map = $rulemap_id;
                  $row->level = $i;
                  $row->rule_value = round($values["level_$i"], 2);
                  $row->save();
               }
            }
            $db->commit();
         } catch (Exception $e) {
            $db->rollBack();
            throw $e;
         }

      } else {
         //create new rule detail
         $db = $rulemaps_table->getAdapter();
         $db->beginTransaction();
         try {
            $row = $rulemaps_table->createRow();
            $row->rule_id = $rule_id;
            $row->level_id = $level_id;
            $row->save();
            $db->commit();
         } catch (Exception $e) {
            $db->rollBack();
            throw $e;
         }
         $rulemap_id = $row->rulemap_id;

         $db = $rulemapdetails_table->getAdapter();
         $db->beginTransaction();
         try {
            for ($i = 1; $i <= $this->_MAX_COMMISSION_LEVEL; $i++) {
               $row = $rulemapdetails_table->createRow();
               $row->rule_map = $rulemap_id;
               $row->level = $i;
               $row->rule_value = round($values["level_$i"], 2);
               $row->save();
            }
         } catch (Exception $e) {
            $db->rollBack();
            throw $e;
         }
      }

      $this->view->form = null;
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_("Changes Saved"))
      ));
   }

}
