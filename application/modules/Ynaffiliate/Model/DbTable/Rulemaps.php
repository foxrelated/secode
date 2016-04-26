<?php

class Ynaffiliate_Model_DbTable_Rulemaps extends Engine_Db_Table {

   protected $_rowClass = "Ynaffiliate_Model_Rulemap";

   public function getRuleMap($rule_id, $user_id) {
      $level_id = Engine_Api::_()->ynaffiliate()->getLevelId($user_id);
      $rulemap = $this->select()->where('rule_id = ?', $rule_id)->where('level_id = ?', $level_id);
      return $this->fetchRow($rulemap);
   }

   public function checkRuleMap($rule_id, $level_id) {
      $rulemap = $this->select()->where('rule_id = ?', $rule_id)->where('level_id = ?', $level_id);
      return $this->fetchRow($rulemap);
   }

}