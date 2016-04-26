<?php
class Groupbuy_Model_DbTable_BuyCods extends Engine_Db_Table
{
   protected $_name     = 'groupbuy_buy_cods';
   protected $_primary  = 'buycod_id';
   protected $_rowClass = 'Groupbuy_Model_BuyCod';
}