<?php
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'profileinfochecker',
    'version' => '4.0.0',
    'revision' => '',
    'path' => 'application/modules/Profileinfochecker',
    'repository' => 'spur-i-t.com',
    'title' => 'Profile Info Checker',
    'description' => 'This plugin calculates the number of filled profile fields. The useful widget because it reminds the user to fill out the information on his profile.',
    'author' => '<a href="http://spur-i-t.com" style="text-decoration:underline;">SpurIT</a>',
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),		 
    'callback' => array(
      'class' => 'Engine_Package_Installer_Module',
    ),
    'directories' => array(
      'application/modules/Profileinfochecker',
    ),
    'files' => array(
      'application/languages/en/profileinfochecker.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
  )
); ?>