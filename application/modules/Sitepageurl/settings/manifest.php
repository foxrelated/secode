<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'sitepageurl',
    'version' => '4.2.3',
    'path' => 'application/modules/Sitepageurl',
    'title' => 'Directory / Pages - Short Page URL Extension',
    'description' => 'Directory / Pages - Short Page URL Extension',
    'author' => 'SocialEngineAddOns',
     'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sitepageurl/settings/install.php',
            'class' => 'Sitepageurl_Installer'
        ),
        'directories' => array(
            'application/modules/Sitepageurl'
        ),
        'files' => array(
            'application/languages/en/sitepageurl.csv'
        )
    )
); ?>