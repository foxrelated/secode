<?php
 /**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: PaymentAccounts.php
 * @author     Minh Nguyen
 */
class Groupbuy_Model_DbTable_PaymentAccounts extends Engine_Db_Table
{
  protected $_name     = 'groupbuy_payment_accounts';
  protected $_primary  = 'paymentaccount_id';
  protected $_rowClass = "Groupbuy_Model_PaymentAccount";
}