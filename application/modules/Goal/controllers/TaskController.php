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

class Goal_TaskController extends Core_Controller_Action_Standard
{
  public function addAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');
    //get goal
    $this->view->goal = Engine_Api::_()->getItem('goal', $this->_getParam('goal_id'));
    // Create form
    $this->view->form = $form = new Goal_Form_Task_Create(array('goalid'=>$this->_getParam('goal_id')));

    $form->goal_id->setValue($this->_getParam('goal_id'));
    
    // Check method/data validitiy
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $values = $form->getValues();
  
    $viewer = Engine_Api::_()->user()->getViewer();
    $values['user_id'] = $viewer->getIdentity();
    
  
    $table = Engine_Api::_()->getItemTable('goal_task');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      // Create goal
      $task = $table->createRow();
      $task->setFromArray($values);
      $task->save();

      // Commit
      $db->commit(); 
    } catch( Exception $e ){
      $db->rollBack();
      throw $e;
    }
    
    //$this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('New Task added.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRefresh' => true,
      'messages' => Array($this->view->message)
    ));
    
  }

  public function editAction()
  {
     if($this->_getParam('task_id')){
        $task_id = $this->_getParam('task_id');
         $task = Engine_Api::_()->getItem('goal_task', $task_id);
     } 
    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');
     
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $this->view->form = $form = new Goal_Form_Task_Edit(array('goalid'=>$task->getParent()->getIdentity()));
    
    if( !$this->getRequest()->isPost() )
    {
      $form->populate($task->toArray());
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    //get task with form
     $values = $form->getValues();
    
    if($values['task_id']){
        $task = Engine_Api::_()->getItem('goal_task', $values['task_id']);
    }
    
    // Process
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      // Set group info
      $task->setFromArray($values);
      $task->save();
   
      // Commit
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected task has been updated.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRefresh' => true,
      'messages' => Array($this->view->message)
    ));
  }

  public function completeAction()
  {
    $task_id = $this->_getParam('task_id');
     
    //task object
    $task = Engine_Api::_()->getItem('goal_task', $task_id);

    $tasks_sel = $task->getTable()->select()
            ->where('goal_id = ?', $task->getOwner()->getIdentity());
    
    $total_tasks = $task->getTable()->fetchAll($tasks_sel);
    $total_tasks = count($total_tasks);

    //goal table
    $goalTable = Engine_Api::_()->getDbtable('goals','goal');
    $viewer = Engine_Api::_()->user()->getViewer();
    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');
    // Make form
    $this->view->form = $form = new Goal_Form_Task_Complete();
    
    if( !$task || $viewer->getIdentity() != $task->user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Task doesn't exists or not authorized to complete");
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    //completed date
    // Convert times
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($viewer->timezone);
    date_default_timezone_set($oldTz);
    $completed_date =  date('Y-m-d H:i:s', strtotime('now'));
    
    $db = $task->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $task->getTable()->update(
              array('complete' => 1,'completed_date' => $completed_date),
              array('task_id = ?'=> $task->getIdentity())
        );

      //get total completed tasks
       $tasks_completed_sel = $task->getTable()->select()
            ->where('goal_id = ?', $task->getOwner()->getIdentity())
            ->where('complete = ?', 1)   
            ;
       $tasks_completed = $task->getTable()->fetchAll($tasks_completed_sel);
       if($tasks_completed){
        $tasks_completed = count($tasks_completed);
       }
       if($tasks_completed == $total_tasks){
           $goalTable->update(
              array('achieved'=> 1,'ach_date' => $completed_date),
              array('goal_id = ?' => $task->getOwner()->getIdentity())     
        );
           
        // Add action && get goal item
        $goal = Engine_Api::_()->getItem('goal',$task->getOwner()->getIdentity());
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity($viewer, $goal, 'goal_complete');
        if( $action ) {
            $activityApi->attachActivity($action, $goal);
        }
        
      //sending completed notification to all members who liked this goal
       $coreLikeTbl = Engine_Api::_()->getDbtable('likes','core');
       $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
       $sAuL = $coreLikeTbl->select()
               ->where('resource_type = ?', 'goal')
               ->where('resource_id = ?', $goal->getIdentity())
               ->where('poster_type = ?', 'user')
               ;
       $allLikeUsers = $coreLikeTbl->fetchAll($sAuL);
       if(count($allLikeUsers) > 0){
           foreach ($allLikeUsers as $poster){
               $user = Engine_Api::_()->getItem('user', $poster->poster_id);
                // Add notification
                $notifyApi->addNotification($user, $goal, $viewer, 'goal_completed');
           }
       }
    }
       
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected task has been updated.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRefresh' => true,
      'messages' => Array($this->view->message)
    ));
    
  }

  public function deleteAction()
  {
      
     $task_id = $this->_getParam('task_id');
     $viewer = Engine_Api::_()->user()->getViewer();
     $task = Engine_Api::_()->getItem('goal_task', $task_id);

    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');
    
    // Make form
    $this->view->form = $form = new Goal_Form_Task_Delete();
    
    if( !$task )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Task doesn't exists or not authorized to delete");
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $task->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $task->delete();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected task has been deleted.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRefresh' => true,
      'messages' => Array($this->view->message)
    ));
  }
}