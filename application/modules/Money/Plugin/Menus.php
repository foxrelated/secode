<?php
/**
 * SocialEnginePro
 *
 * @category   Application_Extensions
 * @package    E-money
 * @author     Azim
 */

/**
 * @category   Application_Extensions
 * @package    E-money
 */
class Money_Plugin_Menus {

    public function onMenuInitialize_MoneyAdminMainBanktransfer($row) {
        $orderTable = Engine_Api::_()->getDbtable('orders', 'money');
        $select = $orderTable->fetchAll($orderTable->select()->where('gateway_id = 3 Or gateway_id = 4')->where('state = ?', 'pending'));
       
        if(sizeof($select) > 0){
            $translate = Zend_Registry::get ('Zend_Translate');
            $label = sprintf($translate->translate('BankTransfer ticket(%s)'), sizeof($select));
            $row->label = $label;
            return $row;
        }
        else{
            return $row;
        }
        
    }
    public function onMenuInitialize_MoneyAdminMainIssue($row){
        $table = Engine_Api::_()->getDbtable('issues', 'money');
        $select = $table->fetchAll($table->select()->where('enable = ?', 1));
        if(sizeof($select) > 0){
        $translate = Zend_Registry::get ('Zend_Translate');
        $label = sprintf($translate->translate('Issue an invoice(%s)'), sizeof($select));
        $row->label= $label;
        return $row;
        }
        else{
            return $row;
        }
    }

}