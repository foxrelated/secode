<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'qrcode',
    'version' => '4.6.0p1',
    'path' => 'application/modules/Qrcode',
    'title' => 'QRCode',
    'description' => 'Having values from user\'s profile.',
    'author' => '<a href="www.ipragmatech.com">iPragmatech</a>',
    'callback' => 
    array (
       'path' => 'application/modules/Qrcode/settings/install.php',
       'class' => 'Qrcode_Installer',
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
      0 => 'application/modules/Qrcode',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/qrcode.csv',
    ),
  ),
  'routes' => array(
    // User - General
    'qrcode_route' => array(
      'route' => 'qrcode/:controller/:action',
      'defaults' => array(
        'module' => 'qrcode',
        'controller' => 'index',
        'action' => 'index'
      ),
      ),
      )
  
); ?>