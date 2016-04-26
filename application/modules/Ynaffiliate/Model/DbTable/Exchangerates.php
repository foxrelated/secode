<?php

class Ynaffiliate_Model_DbTable_Exchangerates extends Engine_Db_Table {

   protected $_rowClass = "Ynaffiliate_Model_Exchangerate";
   protected $_name = 'ynpayment_exchangerates';

   public function calculatePoints($purchase_currency, $commission_amount) {
    try {
   	  $rate_select = $this->select()->where('exchangerate_id like ?', $purchase_currency);
      $rate_result = $this->fetchRow($rate_select);
      if ($rate_result) {
         $exchange_rate = $rate_result->exchange_rate;
         $new_amount = $commission_amount / $exchange_rate;
         return $new_amount;
      }
      else {
      	return 0;
      }
    }
    catch (Exception $e) {
    	throw $e;
    }
   }

   public function getExchangerate() {
      $select = $this->select();

      $curr = $this->fetchAll($select);
      return $curr;
   }

   public function getExchangerateById($id) {
      $select = $this->select();
      $select->where("exchangerate_id =?", $id);
      $curr = $this->fetchRow($select);
      return $curr;
   }

}