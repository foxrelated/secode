<?php 
  /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
	array(
    'title' => $view->translate('Group Profile Polls'),
    'description' => $view->translate('Displays a group’s polls on its profile.'),
    'category' => $view->translate('Groups'),
    'type' => 'widget',
    'name' => 'grouppoll.profile-grouppolls',
    'defaultParams' => array(
      'title' => $view->translate('Group Polls at Group-profile page'),
    ),
  ),
  array(
    'title' => $view->translate('Group Profile Most Commented Polls'),
    'description' => $view->translate('Displays list of group’s most commented polls. Setting for this widget available in widget settings tab of Group - Polls admin.'),
    'category' => $view->translate('Groups'),
    'type' => 'widget',
    'name' => 'grouppoll.comment-grouppolls',
    'defaultParams' => array(
      'title' => $view->translate('Most Commented Group Polls'),
      'titleCount' => true,
    ),
  ),
  array(
    'title' => $view->translate('Group Profile Most Viewed Polls'),
    'description' => $view->translate('Displays list of group’s most viewed polls. Setting for this widget available in widget settings tab of Group - Polls admin.'),
    'category' => $view->translate('Groups'),
    'type' => 'widget',
    'name' => 'grouppoll.view-grouppolls',
    'defaultParams' => array(
      'title' => $view->translate('Most Viewed Group Polls'),
      'titleCount' => true,
    ),
  ),
  array(
    'title' => $view->translate('Group Profile Most Voted Polls'),
    'description' => $view->translate('Displays list of group’s most voted polls. Setting for this widget available in widget settings tab of Group - Polls admin.'),
    'category' => $view->translate('Groups'),
    'type' => 'widget',
    'name' => 'grouppoll.vote-grouppolls',
    'defaultParams' => array(
      'title' => $view->translate('Most Voted Group Polls'),
      'titleCount' => true,
    ),
  ),
  array(
    'title' => $view->translate('Group Profile Most Recent Polls'),
    'description' => $view->translate('Displays list of group’s most recentd polls. Setting for this widget available in widget settings tab of Group - Polls admin.'),
    'category' => $view->translate('Groups'),
    'type' => 'widget',
    'name' => 'grouppoll.recent-grouppolls',
    'defaultParams' => array(
      'title' => $view->translate('Most Recent Group Polls'),
      'titleCount' => true,
    ),
  ),
  array(
    'title' => $view->translate('Group Profile Most Liked Polls'),
    'description' => $view->translate('Displays list of group’s most liked polls. Setting for this widget available in widget settings tab of Group - Polls admin.'),
    'category' => $view->translate('Groups'),
    'type' => 'widget',
    'name' => 'grouppoll.like-grouppolls',
    'defaultParams' => array(
      'title' => $view->translate('Most Liked Group Polls'),
      'titleCount' => true,
    ),
  ),
)
?>