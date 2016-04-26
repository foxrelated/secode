<?php
class Mp3music_Model_DbTable_TransactionTrackings extends Engine_Db_Table
{
   protected $_name     = 'mp3music_transaction_trackings';
   protected $_primary  = 'transactiontracking_id';   
  protected $_rowClass = "Mp3music_Model_TransactionTracking";

}