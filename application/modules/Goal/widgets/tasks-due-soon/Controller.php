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

class Goal_Widget_TasksDueSoonController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    //get tasks due soon
    $table = Engine_Api::_()->getDbtable('tasks','goal');
    $select = $table->select()
            ->where('complete = ?', 0)
            ->where('user_id = ?', $viewer->getIdentity())
            ->order('duedate ASC')
            ->limit(10)
            ;
    $duetasks = $table->fetchAll($select);
    $this->view->duetasks = $duetasks;
    
   }
}