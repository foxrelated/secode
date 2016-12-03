<?php
class Money_Model_DbTable_Subscriptions extends Engine_Db_Table
{
  protected $_rowClass = 'Money_Model_Subscription';
 
  public function cancelAll(User_Model_User $user, $note = null,
      Money_Model_Subscription $except = null)
  {
    $select = $this->select()
      ->where('user_id = ?', $user->getIdentity())
      ->where('active = ?', true)
      ;
    
    if( $except ) {
      $select->where('subscription_id != ?', $except->subscription_id);
    }
    
    foreach( $this->fetchAll($select) as $subscription ) {
      try {
        $subscription->cancel();
      } catch( Exception $e ) {
        $subscription->getGateway()->getPlugin()->getLog()
          ->log($e->__toString(), Zend_Log::ERR);
      }
    }

    return $this;
  }
}