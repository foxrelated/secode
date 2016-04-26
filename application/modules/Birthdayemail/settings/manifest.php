<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthdayemail
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: manifest.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/

return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'birthdayemail',
    'version' => '4.8.7p1',
    'path' => 'application/modules/Birthdayemail',
    'title' => 'Birthdayemail',
    'description' => 'Birthday Email',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
    'date' => 'Thursday, 17 Nov 2010 18:33:08 +0000',
    'copyright' => 'Copyright 2009-2010 BigStep Technologies Pvt. Ltd.',
    'callback' => 
    array (
      'path' => 'application/modules/Birthdayemail/settings/install.php',
      'class' => 'Birthdayemail_Installer',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Birthdayemail',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/birthdayemail.csv',
    ),
  ),

// items
 'items' => array(
    
  ),

// routes
'routes' => array(
    // Public
   
    'birthdayemail_general' => array(
      'route' => 'birthdayemails/:action/*',
      'defaults' => array(
        'module' => 'birthdayemail',
        'controller' => 'index',
        'action' => 'index',
      ),
    ),
  ),
); ?>
