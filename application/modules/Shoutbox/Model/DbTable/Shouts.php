<?php   
/**
 * @author     George Coca
 * @website    geodeveloper.net <info@geodeveloper.net>   
 */
class Shoutbox_Model_DbTable_Shouts extends Engine_Db_Table
{
  protected $_rowClass = 'Shoutbox_Model_Shout';


  public function getShouts($identity)
  {
    $select = $this->select()
      ->where('page = ?', $identity)
      ->order('creation_date DESC')
      ;
    
    return Zend_Paginator::factory($select);
  }

  public function addShout(User_Model_User $user, $body, $creation_date, $identity)
  {
      $viewer = Engine_Api::_()->user()->getViewer();      
      $row = $this->createRow();
      $row->setFromArray(array(
        'user_id' => $viewer->getIdentity(),
        'body' => $body,
        'creation_date' => $creation_date,
        'page' => $identity,
      ));
      $row->save();
      return $row;
  }
}