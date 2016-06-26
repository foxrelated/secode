<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
  array(
    'title' => 'Upcoming HE - Event',
    'description' => 'Displays upcoming events for logged in members.',
    'category' => 'HE - Event',
    'type' => 'widget',
    'name' => 'heevent.home-upcoming',
    'isPaginated' => true,
    'requirements' => array(
      'viewer',
      'no-subject',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title'
          )
        ),
        array(
          'Radio',
          'type',
          array(
            'label' => 'Show',
            'multiOptions' => array(
              '1' => 'Any upcoming events.',
              '2' => 'Current member\'s upcoming events.',
              '0' => 'Any upcoming events when member is logged out, that member\'s events when logged in.',
            ),
            'value' => '0',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Event Profile Info',
    'description' => 'Displays an event\'s info (creation date, member count, etc) on it\'s profile.',
    'category' => 'HE - Event',
    'type' => 'widget',
    'name' => 'heevent.profile-info',
    'requirements' => array(
      'subject' => 'event',
    ),
  ),
  array(
    'title' => 'Event Profile Members',
    'description' => 'Displays a event\'s members on it\'s profile.',
    'category' => 'HE - Event',
    'type' => 'widget',
    'name' => 'heevent.profile-members',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Guests',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'event',
    ),
  ),
  array(
    'title' => 'Event Owner Tickets',
    'description' => 'Displays an event\'s buy tickets on it\'s profile.',
    'category' => 'HE - Event',
    'type' => 'widget',
    'defaultParams' => array(
      'title' => 'Tickets',
      'titleCount' => true,
    ),
    'name' => 'heevent.profile-owner-tickets',
    'requirements' => array(
      'subject' => 'event',
    ),
  ),
  array(
    'title' => 'Event Profile Guests',
    'description' => 'Displays an event\'s guests on it\'s profile.',
    'category' => 'HE - Event',
    'type' => 'widget',
    'name' => 'heevent.profile-guests',
    'requirements' => array(
      'subject' => 'event',
    ),
  ),
  array(
    'title' => 'Event Profile Options',
    'description' => 'Displays a menu of actions (edit, report, invite, etc) that can be performed on an event on it\'s profile.',
    'category' => 'HE - Event',
    'type' => 'widget',
    'name' => 'heevent.profile-options',
    'requirements' => array(
      'subject' => 'event',
    ),
  ),
  array(
    'title' => 'Event Profile Cover',
    'description' => 'Displays an event\'s cover on it\'s profile.',
    'category' => 'HE - Event',
    'type' => 'widget',
    'name' => 'heevent.profile-cover',
    'requirements' => array(
      'subject' => 'event',
    ),
  ),
  array(
    'title' => 'Event Profile RSVP',
    'description' => 'Displays options for RSVP\'ing to an event on it\'s profile.',
    'category' => 'HE - Event',
    'type' => 'widget',
    'name' => 'heevent.profile-rsvp',
    'requirements' => array(
      'subject' => 'event',
    ),
  ),
  array(
    'title' => 'Popular Events',
    'description' => 'Displays a list of the most viewed events.',
    'category' => 'HE - Event',
    'type' => 'widget',
    'name' => 'heevent.list-popular-events',
    'defaultParams' => array(
      'title' => 'Popular HE - Event',
    ),
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Radio',
          'popularType',
          array(
            'label' => 'Popular Type',
            'multiOptions' => array(
              'view' => 'Views',
              'member' => 'Members',
            ),
            'value' => 'view',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Event Browse Search',
    'description' => 'Displays a search form in the event browse page.',
    'category' => 'HE - Event',
    'type' => 'widget',
    'name' => 'heevent.browse-search',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Event Calendar',
    'description' => 'Witget shows active events on calendar.',
    'category' => 'HE - Event',
    'type' => 'widget',
    'name' => 'heevent.calendar',
    'requirements' => array(
      'no-subject',
    ),
  )
) ?>