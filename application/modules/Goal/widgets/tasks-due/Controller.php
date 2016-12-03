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

class Goal_Widget_TasksDueController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('goal');


    //get completed tasks
    $table = Engine_Api::_()->getDbtable('tasks','goal');
    $select = $table->select()
            ->where('goal_id = ?',$subject->getIdentity())
            ->where('complete = ?', 0);
    $completedTasks = $table->fetchAll($select);
    $this->view->completedTasks = $completedTasks;
   }
}