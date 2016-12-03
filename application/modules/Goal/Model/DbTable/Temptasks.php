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

class Goal_Model_DbTable_Temptasks extends Engine_Db_Table
{
  protected $_rowClass = 'Goal_Model_Temptask';
  
  public function getTemptasksAssoc()
  {
    $stmt = $this->select()
        ->from($this, array('task_id', 'title'))
        ->order('title ASC')
        ->query();
    
    $data = array();
    foreach( $stmt->fetchAll() as $temptasks ) {
      $data[$temptasks['task_id']] = $temptasks['title'];
    }
    return $data;
  }
}