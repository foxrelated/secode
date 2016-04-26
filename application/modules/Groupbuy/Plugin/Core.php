<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Core.php
 * @author     Minh Nguyen
 */
class Groupbuy_Plugin_Core
{
  public function onStatistics($event)
  {
    $table  = Engine_Api::_()->getDbTable('deals', 'groupbuy');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count')->where('is_delete = 0');
    $event->addResponse($select->query()->fetchColumn(0), 'deals');
  }
  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    /*if( $payload instanceof User_Model_User ) {
      // Delete deal
      $dealTable = Engine_Api::_()->getDbtable('deals', 'groupbuy');
      $dealSelect = $dealTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $dealTable->fetchAll($dealSelect) as $deal ) {
        $deal->is_delete = 1;
        
        $deal->save();
      } 
    }  */
  }
  public function onUserCreateAfter($event)
  {
       $payload = $event->getPayload();
       if( $payload instanceof User_Model_User ) 
       {
            $user_id = $payload->getIdentity();
            Groupbuy_Api_Account::addAccount($user_id);
       }
  }
}