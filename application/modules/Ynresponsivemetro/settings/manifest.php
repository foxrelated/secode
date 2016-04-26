<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynresponsivemetro',
    'version' => '4.01p2',
    'path' => 'application/modules/Ynresponsivemetro',
    'title' => 'YN - Responsive Metro Template',
    'description' => 'Responsive Metro Template',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
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
      0 => 'application/modules/Ynresponsivemetro',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynresponsivemetro.csv',
    ),
    'dependencies' => 
    array (
      0 => 
      array (
        'type' => 'module',
        'name' => 'younet-core',
        'minVersion' => '4.02p7',
      ),
    ),
  ),
  'items' => 
  array (
    0 => 'ynresponsivemetro_metroblock',
   ),
); ?>