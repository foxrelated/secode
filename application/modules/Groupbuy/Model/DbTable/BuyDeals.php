<?php
 /**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Params.php
 * @author     Minh Nguyen
 */
class Groupbuy_Model_DbTable_BuyDeals extends Engine_Db_Table
{
   protected $_name     = 'groupbuy_buy_deals';
   protected $_primary  = 'buydeal_id';
   protected $_rowClass = 'Groupbuy_Model_BuyDeal';
}