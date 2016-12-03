<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */

class Goal_Model_Category extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;
  
  public function getTable()
  {
    if( null === $this->_table ) {
      $this->_table = Engine_Api::_()->getDbtable('categories', 'goal');
    }
    
    return $this->_table;
  }

  public function getUsedCount()
  {
    $eventTable = Engine_Api::_()->getItemTable('goal');
    return $eventTable->select()
        ->from($eventTable, new Zend_Db_Expr('COUNT(goal_id)'))
        ->where('category_id = ?', $this->category_id)
        ->query()
        ->fetchColumn();
  }

  public function isOwner($owner)
  {
    return false;
  }

  public function getOwner()
  {
    return $this;
  }
}
