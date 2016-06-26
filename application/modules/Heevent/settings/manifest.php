<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
return array(
  'package' =>
  array(
    'type' => 'module',
    'name' => 'heevent',
    'version' => '4.5.4p1',
    'path' => 'application/modules/Heevent',
    'title' => 'HE - Advanced  Events',
    'description' => 'Advanced Events Plugin',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'callback' =>
    array(
      'path' => 'application/modules/Heevent/settings/install.php',
      'class' => 'Heevent_Installer',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'event',
        'minVersion' => '4.2.0',
      ),
      array(
        'type' => 'module',
        'name' => 'hecore',
        'minVersion' => '4.2.0p9',
      ),
    ),

    'actions' =>
    array(
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' =>
    array(
      0 => 'application/modules/Heevent',
      1 => 'public/heevent_categoryphoto',
    ),
    'files' =>
    array(
      0 => 'application/languages/en/heevent.csv',
    ),
  ),
  'items' => array(
    'event',
    'heevent_categoryphoto',
    'heevent_gateway',
    'order',
    'transaction',
    'subscription',
  ),
  'wall_composer' => array(
    array(
      'script' => array('_composeHeevent.tpl', 'heevent'),
      'plugin' => 'Heevent_Plugin_Composer',
      'auth' => array('event', 'create'),
      'subjects' => array('user'),
      'module' => 'heevent',
      'type' => 'heevent',
      'can_disable' => true,
      'composer' => true
    )
  ),

  // Routes --------------------------------------------------------------------
  'routes' => array(
    'heevent_extended' => array(
      'route' => 'heevents/:controller/:action/*',
      'defaults' => array(
        'module' => 'heevent',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
    'heevent_general' => array(
      'route' => 'he-events/:action/:type/*',
      'defaults' => array(
        'module' => 'heevent',
        'controller' => 'index',
        'action' => 'browse',
        'type' => 'upcoming',
      ),
      'reqs' => array(
        'action' => '(index|browse|create|delete|list|manage|edit|tickets|moretickets)',
      )
    ),
    'heevent_print' => array(
      'route' => 'he-events/print/:id/*',
      'defaults' => array(
        'module' => 'heevent',
        'controller' => 'index',
        'action' => 'print',
      ),
    ),
    'heevent_payment' => array(
      'route' => 'he-events/:action/:event_id/*',
      'defaults' => array(
        'module' => 'heevent',
        'controller' => 'subscription',
        'action' => 'choose',
      ),
      'reqs' => array(
        'action' => '(choose|gateway|process|finish|return)',
        'event_id' => '\d+',
      )
    ),
    'heevent_specific' => array(
      'route' => 'he-events/:action/:event_id/*',
      'defaults' => array(
        'module' => 'heevent',
        'controller' => 'event',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(edit|delete|join|leave|invite|accept|style|reject)',
        'heevent_id' => '\d+',
      )
    ),
    'heevent_profile' => array(
      'route' => 'he-event/:id/*',
      'defaults' => array(
        'module' => 'heevent',
        'controller' => 'profile',
        'action' => 'index',
      ),
      'reqs' => array(
        'id' => '\d+',
      )
    ),
    'heevent_mine' => array(
      'route' => 'he-event/manage/*',
      'defaults' => array(
        'module' => 'heevent',
        'controller' => 'index',
        'action' => 'browse',
      ),

    ),
    'heevent_upcoming' => array(
      'route' => 'he-events/upcoming/*',
      'defaults' => array(
        'module' => 'heevent',
        'controller' => 'index',
        'action' => 'browse',
        'filter' => 'future'
      )
    ),
    'heevent_past' => array(
      'route' => 'he-events/past/*',
      'defaults' => array(
        'module' => 'heevent',
        'controller' => 'index',
        'action' => 'browse',
        'filter' => 'past'
      )
    ),
  )
); ?>


