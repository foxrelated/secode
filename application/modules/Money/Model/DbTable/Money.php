<?php

class Money_Model_DbTable_Money extends Engine_Db_Table {

    protected $_rowClass = 'Money_Model_Money';

    public function getBalance(User_Model_User $user)
    {
        $select = $this->select()
                        ->where('user_id = ?', $user->getIdentity())
                        ;
        $row = $this->fetchRow($select);

        return $row;
    }

    public function updateMoneyPayPal(User_Model_User $user, $amount) {
        $commission = Engine_Api::_()->getApi('settings', 'core')->getSetting('money.commission');
        $siteCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('money.site.currency');
        $paidCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('money.currency');
        $conversion = Engine_Api::_()->getApi('settings', 'core')->getSetting('money.conversion', 1);

        if($paidCurrency != $siteCurrency){
            if($conversion != 0){
                $amount = $conversion * $amount;
            }
        }

        $new_amount = $amount - ($amount * $commission) / 100;

        $row = $this->fetchRow($this->select()->where('user_id = ?', $user->getIdentity()));
        $row->money = $row->money + $new_amount;
        $row->save();
    }

   
    
    public function setMoney($user_id, $amount){
        $row = $this->fetchRow($this->select()->where('user_id = ?', $user_id));
       
        if(!$row){
            $row = $this->createRow();
            $row->user_id = $user_id;
            $row->save();
        }
        
        $row->money = $row->money + $amount;
        $row->save();
    }
    
    

    

}