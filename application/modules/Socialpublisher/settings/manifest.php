<?php defined("_ENGINE") or die("access denied"); return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'socialpublisher',
    'version' => '4.03p4',
    'path' => 'application/modules/Socialpublisher',
    'title' => 'YN - Social Publisher',
    'description' => '',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
    'dependencies' => 
    array (
      0 => 
      array (
        'type' => 'module',
        'name' => 'socialbridge',
        'minVersion' => '4.04p6',
      ),
      1 => 
      array (
        'type' => 'module',
        'name' => 'younet-core',
        'minVersion' => '4.02',
      ),
    ),
    'callback' => 
    array (
      'path' => 'application/modules/Socialpublisher/settings/install.php',
      'class' => 'Socialpublisher_Package_Installer',
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
      0 => 'application/modules/Socialpublisher',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/socialpublisher.csv',
    ),
  ),
  'hooks' => 
  array (
    0 => 
    array (
      'event' => 'onItemCreateAfter',
      'resource' => 'Socialpublisher_Plugin_Core',
    ),
  ),
  'routes' => 
  array (
    'socialpublisher_general' => 
    array (
      'route' => 'socialpublisher/:action/*',
      'defaults' => 
      array (
        'module' => 'socialpublisher',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(index|settings|share)',
      ),
    ),
  ),
);?>