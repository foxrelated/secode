<?php return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'ynresponsive-metro',
    'version' => '4.01p2',
    'path' => 'application/themes/ynresponsive-metro',
    'repository' => 'younetco.com',
    'title' => 'YN - Responsive Metro Template',
    'thumb' => 'theme.png',
    'author' => 'YouNet Company',
    'dependencies' => array(
      array(
         'type' => 'module',
         'name' => 'ynresponsive1',
         'minVersion' => '4.03',
      ),
       array(
         'type' => 'module',
         'name' => 'ynresponsivemetro',
         'minVersion' => '4.01p2',
      ),
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => 
    array (
      0 => 'application/themes/ynresponsive-metro',
      1 => 'application/themes/configure/default',
      2 => 'application/themes/configure/ynresponsive-metro',
    ),
    'description' => 'YouNet Responsive Metro Template',
  ),
  'files' => 
  array (
    0 => 'theme.css',
    1 => 'constants.css',
  ),
); ?>