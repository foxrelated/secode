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

return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'goal',
    'version' => '4.8.8',
    'path' => 'application/modules/Goal',
    'title' => 'Goals',
    'description' => 'Goal creation and task management.',
    'author' => 'StarsDeveloper.com',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.2.0',
      ),
     array(
       'type' => 'module',
       'name' => 'sdcore',
       'minVersion' => '4.8.6',
     ),
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
   'callback' => array(
      'path' => 'application/modules/Goal/settings/install.php',
      'class' => 'Goal_Installer',
    ),  
    'directories' => 
    array (
      0 => 'application/modules/Goal',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/goal.csv',
    ),
  ),
   // Items ---------------------------------------------------------------------
  'items' => array(
    'goal',
    'goal_category',
    'goal_photo',
    'goal_task',
  ),  
    
 // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Goal_Plugin_Core',
    ),
  ),
  
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'goal_extended' => array(
      'route' => 'goals/:controller/:action/*',
      'defaults' => array(
        'module' => 'goal',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
    'goal_general' => array(
      'route' => 'goals/:action/*',
      'defaults' => array(
        'module' => 'goal',
        'controller' => 'index',
        'action' => 'browse',
      ),
      'reqs' => array(
        'action' => '(browse|create|manage|createtemp|temptasks|createt)',
      )
    ),
    'goal_specific' => array(
      'route' => 'goals/:action/:goal_id/*',
      'defaults' => array(
        'module' => 'goal',
        'controller' => 'goal',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(edit|delete)',
        'goal_id' => '\d+',
      )
    ),
    'goal_profile' => array(
      'route' => 'goal/:id/*',
      'defaults' => array(
        'module' => 'goal',
        'controller' => 'profile',
        'action' => 'index',
      ),
      'reqs' => array(
        'id' => '\d+',
      )
    ),   
    'task_general' => array(
      'route' => 'goal/tasks/:action/*',
      'defaults' => array(
        'module' => 'goal',
        'controller' => 'task',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(add)',
        'goal_id' => '\d+',
      )
    ),  
    'task_specific' => array(
      'route' => 'goal/task/:action/*',
      'defaults' => array(
        'module' => 'goal',
        'controller' => 'task',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(edit|delete|complete)',
        'task_id' => '\d+',
      )
    ),       
)
    
); ?>