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

class Goal_Widget_GoalStatusController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('goal');

    $this->view->goal = $subject;
    
    //get total tasks
    $taskTable = Engine_Api::_()->getDbtable('tasks','goal');
    $tSel = $taskTable->select()
            ->where('goal_id = ?', $subject->getIdentity())
            ->where('user_id = ?', $subject->getOwner()->getIdentity())
            ;
    $total_tasks = $taskTable->fetchAll($tSel);
    $this->view->total_tasks = $total_tasks;
    
    //get completed tasks
    $tCompleteSel = $taskTable->select()
            ->where('goal_id = ?', $subject->getIdentity())
            ->where('user_id = ?', $subject->getOwner()->getIdentity())
            ->where('complete = ?', 1)
            ;
    $tasks_completed = $taskTable->fetchAll($tCompleteSel);
    $this->view->tasks_completed = $tasks_completed;
    
    //get inComplete tasks
    $tInCompleteSel = $taskTable->select()
            ->where('goal_id = ?', $subject->getIdentity())
            ->where('user_id = ?', $subject->getOwner()->getIdentity())
            ->where('complete = ?', 0)
            ;
    $tasks_inComplete = $taskTable->fetchAll($tInCompleteSel);
    $this->view->tasks_inComplete = $tasks_inComplete;
    
  }
}