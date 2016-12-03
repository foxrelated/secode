<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */

$order_options = array(
  'views' => 'Most Popular',
  'likes' => 'Most Liked',
  'comments' => 'Most Commented',
  'date' => 'Recently Created',
);

return array(
  array(
    'title' => 'Profile Goals',
    'description' => 'Displays a member\'s goals on their profile.',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.profile-goals',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Goals',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Completed Tasks',
    'description' => 'Displays a goal\'s completed tasks. Place this widget on goal profile page',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.tasks-completed',
    'requirements' => array(
      'subject' => 'goal',
    ),
  ),    
  array(
    'title' => 'Tasks Due Soon',
    'description' => 'Displays a goal\'s due tasks on goal manage page.',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.tasks-due-soon',
    'requirements' => array(
      'subject' => 'goal',
    ),
  ),
  array(
    'title' => 'Goal Profile Info',
    'description' => 'Displays a goal\'s info (creation date, view count, owner, etc) on its profile.',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.profile-info',
    'requirements' => array(
      'subject' => 'goal',
    ),
  ),
  array(
    'title' => 'Tasks Due',
    'description' => 'Displays a goal\'s due tasks . Place this widget on goal profile page',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.tasks-due',
    'requirements' => array(
      'subject' => 'goal',
    ),
  ),
  array(
    'title' => 'Goal Profile Options',
    'description' => 'Displays a menu of actions (edit, delete, etc) that can be performed on a goal on its profile.',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.profile-options',
    'requirements' => array(
      'subject' => 'goal',
    ),
  ),
  array(
    'title' => 'Goal Profile Photo',
    'description' => 'Displays a goal\'s photo on its profile.',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.profile-photo',
    'requirements' => array(
      'subject' => 'goal',
    ),
  ),
  array(
    'title' => 'Goal Profile Status',
    'description' => 'Displays a goal\'s title on its profile.',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.goal-status',
    'requirements' => array(
      'subject' => 'goal',
    ),
  ),
  array(
    'title' => 'Most liked/Commented/Popular Goals',
    'description' => 'Displays goals with custom ordering',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.goals',
    'autoEdit' => true,
    'adminForm' => array(
      'elements' => array(
        array(
          'Select',
          'order_type',
          array(
            'label' => 'Goals Order by',
            'multiOptions' => $order_options,
          )
        )
      )
    ),
  ),
  array(
    'title' => 'Goal Browse Search',
    'description' => 'Displays a search form in the goal browse page.',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.browse-search',
    'requirements' => array(
      'no-subject',
    ),
  ),
 array(
    'title' => 'Recent Goals',
    'description' => 'Displays recent goals.',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.recent-goals',
    'requirements' => array(
      'no-subject',
    ),
   'defaultParams' => array(
      'title' => 'Recent Goals',
    ),
    'adminForm' => array(
      'elements' => array(
        
      )
    ),
  ),
 array(
    'title' => 'Goal Browse Menu',
    'description' => 'Displays Goal main menu create/manage/browse links.',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.browse-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Goal Browse Categories',
    'description' => 'Displays all categories on goal browse page.',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.goal-categories',
    'requirements' => array(
      'no-subject',
    ),
  ), 
    array(
    'title' => 'Goals Active',
    'description' => 'Displays active goals on manage page/my goals',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.goals-active',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Goals Completed',
    'description' => 'Displays completed goals on manage page/my goals',
    'category' => 'Goals',
    'type' => 'widget',
    'name' => 'goal.goals-completed',
    'requirements' => array(
      'no-subject',
    ),
  ),
) ?>