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

class Goal_Widget_GoalsActiveController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

     $viewer = Engine_Api::_()->user()->getViewer();
    
    $table = Engine_Api::_()->getDbtable('goals','goal');
    $select = $table->select()
            ->where('achieved = ?', 0)
            ->where('user_id = ?', $viewer->getIdentity())
            ->order('goal_id DESC')
            ;
    $goals = $table->fetchAll($select);
    $this->view->paginator = $goals;
  }
}