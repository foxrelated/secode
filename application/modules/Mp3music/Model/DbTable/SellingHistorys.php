<?php
class Mp3music_Model_DbTable_SellingHistorys extends Engine_Db_Table
{
  protected $_name     = 'mp3music_selling_historys';
  protected $_primary  = 'sellinghistory_id';   
  protected $_rowClass = "Mp3music_Model_SellingHistory";

}