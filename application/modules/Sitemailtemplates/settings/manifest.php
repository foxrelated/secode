<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'sitemailtemplates',
    'version' => '4.8.10p1',
    'path' => 'application/modules/Sitemailtemplates',
    'title' => 'Email Templates',
    'description' => 'Email Templates Plugin',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
    ),
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sitemailtemplates/settings/install.php',
            'class' => 'Sitemailtemplates_Installer',
        ),
        'directories' => array(
            'application/modules/Sitemailtemplates',
        ),
        'files' => array(
            'application/languages/en/sitemailtemplates.csv',
        )
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sitemailtemplates_templates',
    ),
);