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

class Goal_Plugin_Core
{
 public function onStatistics($event)
  {
    $table = Engine_Api::_()->getItemTable('goal');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'goal');
  }
  
  public function onUserDeleteBefore($goal)
  {
    $payload = $goal->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete goals and tasks
      $goalTable = Engine_Api::_()->getDbtable('goals', 'goal');
      $tasksTable = Engine_Api::_()->getDbtable('tasks', 'goal');
      $goalSelect = $goalTable->select()->where('user_id = ?', $payload->getIdentity());
      $taskSelect = $tasksTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $goalTable->fetchAll($goalSelect) as $goal ) {
        $goal->delete();
      }
      foreach( $tasksTable->fetchAll($taskSelect) as $task ) {
        $task->delete();
      }
    }
  }
}