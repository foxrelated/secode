<?php
class Mp3music_Model_DbTable_SellingSettings extends Engine_Db_Table
{
   protected $_name     = 'mp3music_selling_settings';
  protected $_primary  = 'sellingsetting_id'; 
  protected $_rowClass = "Mp3music_Model_SellingSetting";

}