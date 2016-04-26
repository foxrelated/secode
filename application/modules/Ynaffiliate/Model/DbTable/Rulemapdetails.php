<?php

class Ynaffiliate_Model_DbTable_Rulemapdetails extends Engine_Db_Table {

    protected $_rowClass = "Ynaffiliate_Model_Rulemapdetail";

    public function getRuleMapDetail($rule_name = null, $user_id = null, $rule_id = null, $rulemap_id = null, $level = null) {
        if ($rulemap_id) {
            $rulemap_detail = $this->select()->where('rule_map = ?', $rulemap_id);
            return $this->fetchRow($rulemap_detail);
        }
        if (!$rule_id) {
            $Rules = new Ynaffiliate_Model_DbTable_Rules;
            $rule = $Rules->getRuleByName($rule_name);
            if ($rule) {
                $rule_id = $rule->rule_id;
            } else {
                return;
            }
        }
        $RuleMaps = new Ynaffiliate_Model_DbTable_Rulemaps;
        $rulemap = $RuleMaps->getRuleMap($rule_id, $user_id);
        if ($rulemap) {
            $rulemap_id = $rulemap->rulemap_id;
            $rulemap_detail = $this->select()->where('rule_map = ?', $rulemap_id);
            // get only one row by level
            if ($level) {
                $rulemap_detail = $rulemap_detail->where('level = ?', $level);
                return $this->fetchRow($rulemap_detail);
            } else {
                return $this->fetchAll($rulemap_detail);
            }
        } else {
            return;
        }
    }

    public function getRuleMapDetailById($rulemap_id) {
        $rulemap_detail = $this->select()->where('rule_map = ?', $rulemap_id)->order('level');
        return $this->fetchAll($rulemap_detail);
    }

}