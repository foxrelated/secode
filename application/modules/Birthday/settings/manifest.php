<?php

/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: manifest.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
return array (
    'package' => array(
    'type' => 'module',
    'name' => 'birthday',
    'version' => '4.8.7p1',
    'path' => 'application/modules/Birthday',
    'repository' => 'null',
      'title' => 'Birthdays',
      'description' => 'Birthdays Plugin - Listing, Wishes, Reminder Emails and Widgets',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
      'date' => 'Thursday, 17 Nov 2010 18:33:08 +0000',
      'copyright' => 'Copyright 2009-2010 BigStep Technologies Pvt. Ltd.',
    'actions' => array(
    	'install',
      'upgrade',
      'refresh',
      'enable',
      'disable',
    ),
    'callback' => array(
      'path' => 'application/modules/Birthday/settings/install.php',
      'class' => 'Birthday_Installer',
    ),
    'directories' => array(
    	'application/modules/Birthday',
      'application/modules/Birthdayemail'
    ),
    'files' => array(
      'application/languages/en/birthday.csv',
      'application/languages/en/birthdayemail.csv'
    ),
  ),
   // Items ---------------------------------------------------------------------
  'items' => array(
    'meta', 'value', 'search'
  ),

 'routes' => array(
    // Public
       'birthday_extended' => array(
      'route' => 'birthdays/:action/*',
      'defaults' => array(
        'module' => 'birthday',
        'controller' => 'index',
        'action' => 'index',
      ),
    ),

    'birthday_general' => array(
      'route' => 'birthdays/:action/*',
      'defaults' => array(
        'module' => 'birthday',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|create|manage|style|tag)',
      ),
    ),
  ),
); ?>
