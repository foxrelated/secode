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

class Goal_Plugin_Menus
{
  public function canCreateGroups()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    // Must be able to create events
    if( !Engine_Api::_()->authorization()->isAllowed('goal', $viewer, 'create') ) {
      return false;
    }
    return true;
  }

  public function onMenuInitialize_GoalMainManage()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() )
    {
      return false;
    }
    // Must be able to create goals
    if( !Engine_Api::_()->authorization()->isAllowed('goal', $viewer, 'view') ) {
      return false;
    }
    return true;
  }

  public function onMenuInitialize_GoalMainCreate()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if( !$viewer->getIdentity() )
    {
      return false;
    }

    if( !Engine_Api::_()->authorization()->isAllowed('goal', null, 'create') )
    {
      return false;
    }

    return true;
  }
  
  //custom code tausif 1-2-16
  public function onMenuInitialize_GoalMainCreatetemp()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if( !$viewer->getIdentity() )
    {
      return false;
    }

    if( !Engine_Api::_()->authorization()->isAllowed('goal', null, 'create') )
    {
      return false;
    }

    return true;
  }
  
  
  public function onMenuInitialize_GoalProfileEdit()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject->getType() !== 'goal' )
    {
      throw new Group_Model_Exception('Whoops, not a goal!');
    }
    if( !$viewer->getIdentity() )
    {
      return false;
    }
    if( $subject->getOwner()->getIdentity() != $viewer->getIdentity() && $viewer->level_id != 1 && $viewer->level_id != 2 && $viewer->level_id != 3 )
    {
      return false;
    }
  if( $subject->achieved == 1 )
    {
      return false;
    }
    
    return array(
      'label' => 'Edit Goal',
      'icon' => 'application/modules/Goal/externals/images/edit_goal_ico.png',
      'route' => 'goal_specific',
      'params' => array(
        'controller' => 'goal',
        'action' => 'edit',
        'goal_id' => $subject->getIdentity(),
        'ref' => 'profile'
      )
    );
  }
  
public function onMenuInitialize_GoalProfileDelete()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject->getType() !== 'goal' ) {
      throw new Event_Model_Exception('This goal does not exist.');
    } 
    
if( $subject->getOwner()->getIdentity() != $viewer->getIdentity() && $viewer->level_id != 1 && $viewer->level_id != 2 && $viewer->level_id != 3 )
    {
      return false;
    }

    return array(
      'label' => 'Delete Goal',
      'icon' => 'application/modules/Goal/externals/images/delete_goal_ico.png',
      'class' => 'smoothbox',
      'route' => 'goal_specific',
      'params' => array(
        'action' => 'delete',
        'goal_id' => $subject->getIdentity(),
      ),
    );
  }
 
  
public function onMenuInitialize_GoalProfileTask()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject->getType() !== 'goal' )
    {
      throw new Group_Model_Exception('Whoops, not a goal!');
    }

    if( $subject->getOwner()->getIdentity() != $viewer->getIdentity() )
    {
      return false;
    }
   if( $subject->achieved == 1 )
    {
      return false;
    }
    return array(
      'label' => 'Add Task',
      'icon' => 'application/modules/Goal/externals/images/add_goal_ico.png',
      'class' => 'smoothbox',
      'route' => 'task_general',
      'params' => array(
        'controller' => 'task',
        'action' => 'add',
        'goal_id' => $subject->getIdentity(),
        'ref' => 'profile'
      )
    );
  } 

}