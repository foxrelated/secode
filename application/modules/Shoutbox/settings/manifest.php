<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'shoutbox',
    'version' => '4.0.0',
    'path' => 'application/modules/Shoutbox',
    'title' => 'Shoutbox',
    'description' => 'Shoutbox',
    'author' => 'GeoDeveloper.net',
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
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
      0 => 'application/modules/Shoutbox',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/shoutbox.csv',
    ),
  ),
); ?>