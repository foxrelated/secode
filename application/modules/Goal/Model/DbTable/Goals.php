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

class Goal_Model_DbTable_Goals extends Engine_Db_Table
{
  protected $_rowClass = 'Goal_Model_Goal';
  
  public function getGoalPaginator($params = array())
  {
    return Zend_Paginator::factory($this->getGoalSelect($params));
  }
  
  public function getGoalSelect($params = array())
  {
      
    $table = Engine_Api::_()->getItemTable('goal');
    $select = $table->select();
    
    // Search
    if( isset($params['search']) ) {
      $select->where('search = ?', (bool) $params['search']);
    }
    
    // User-based
    if( !empty($params['owner']) && $params['owner'] instanceof Core_Model_Item_Abstract ) {
      $select->where('user_id = ?', $params['owner']->getIdentity());
    } else if( !empty($params['user_id']) ) {
      $select->where('user_id = ?', $params['user_id']);
    } else if( !empty($params['users']) && is_array($params['users']) ) {
      foreach( $params['users'] as &$id ) if( !is_numeric($id) ) $id = 0;
      $params['users'] = array_filter($params['users']);
      $select->where('user_id IN(\''.join("', '", $params['users']).'\')');
    }
    
    // Category
    if( !empty($params['category_id']) ) {
      $select->where('category_id = ?', $params['category_id']);
    }
    
    // status
    if( isset($params['status']) && $params['status'] == 1 ) {
      $select->where('achieved = ?', 1);
    }elseif( isset($params['status']) && $params['status'] == 2 ) {
      $select->where('achieved = ?', 0);
    }
    
    //Full Text
    $search_text = $params['search_text'];
    if( !empty($params['search_text']) ) {
      $select->where("title LIKE '%$search_text%'");
    }
    
    // Order
    if( !empty($params['order']) ) {
      $select->order($params['order']);
    } else {
      $select->order('goal_id DESC');
    }    
    
    return $select;
  }
  
  public function saveGoalTasks($values,$goal){
    
         // step 0: array for all task data 
       
      $taskData = array();
       // step 1: getting last inserted Goal Id
      $goalid = $goal->getIdentity();
      
      // step 2: getting last inserted goal data
      //Get goal detail by id
        $goalTable = Engine_Api::_()->getDbtable('goals','goal');
        $goal_sel = $goalTable->select()
              ->where('goal_id = ?', $goalid);
        $goalData = Engine_Api::_()->getDbtable('goals', 'goal')->fetchAll($goal_sel);
        
        foreach ($goalData as $mygoal) {
            
          $taskData['goal_id'] = $mygoal->goal_id;
          $taskData['user_id'] = $mygoal->user_id;
          $taskData['creation_date'] = $mygoal->creation_date;
          $taskData['modified_date'] = $mygoal->modified_date;
          $taskData['starttime'] = $mygoal->creation_date;
          $taskData['duedate'] = $mygoal->creation_date;
        
           
        }
       
   // getting tasks from vlaues array
   $tasks = $values['task'];
   foreach($tasks as $temptask_id):
       // $temptask_id 
   
       //Get task detail by id
        $taskTable = Engine_Api::_()->getDbtable('temptasks','goal');
        $task_sel = $taskTable->select()
              ->where('temptask_id = ?', $temptask_id);
        $eachtask = Engine_Api::_()->getDbtable('temptasks', 'goal')->fetchAll($task_sel);
        
      
        //loop through selected tasks to get detail of each
        foreach ($eachtask as $task) {
            
            $taskData['title'] = $task->title;
            $taskData['description'] = $task->description;
            
            
            
            // setting task due date
            $days = $task->duration;
            if($days!=0){
               $taskData['duedate'] = date('Y-m-d H:i:s',strtotime($taskData['duedate'].' +'.$days.' days'));   
            }
          
            
        
            
            //Save date in goal_tasks table
            $table = Engine_Api::_()->getItemTable('goal_task');
            $db = $table->getAdapter();
            $db->beginTransaction();
             try {
                 // Create goal
                 $task = $table->createRow();
                 $task->setFromArray($taskData);
                 $task->save();

                 // Commit
                 $db->commit(); 
               } catch( Exception $e ){
                 $db->rollBack();
                 throw $e;
               }
        }
      
  
   endforeach;
  }
  
}