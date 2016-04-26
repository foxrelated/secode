<?php
class Mp3music_Model_DbTable_PaymentAccounts extends Engine_Db_Table
{
  protected $_name     = 'mp3music_payment_accounts';
  protected $_primary  = 'paymentaccount_id';
  protected $_rowClass = "Mp3music_Model_PaymentAccount";
}