<?php

class Ynaffiliate_Model_DbTable_Currencies extends Engine_Db_Table {

   protected $_rowClass = "Ynaffiliate_Model_Currency";
   protected $_name = 'ynpayment_currencies';
   
    public function getCurrencies() {
      $select = $this->select();
      $curr = $this->fetchAll($select);
      return $curr;
   }
   
}