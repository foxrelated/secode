<?php
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'winkgreeting',
    'version' => '4.0.3',
    'revision' => '',
    'path' => 'application/modules/Winkgreeting',
    'repository' => 'spur-i-t.com',
    'title' => 'Wink and Greeting',
    'description' => 'Plugin provides two useful options (wink and greeting) which help in establishing contact between users. When you click on one of these options the profile owner gets a message what you like his profile and your are interested in communicating with him.',
    'author' => '<a href="http://spur-i-t.com" style="text-decoration:underline;">SpurIT</a>',
    'changeLog' => 'settings/changelog.php',
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
      'application/modules/Winkgreeting',
    ),
    'files' => array(
      'application/languages/en/winkgreeting.csv',
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
    // User - General
    'winkgreeting_extended' => array(
      'route' => 'winkgreeting/:controller/:action/:id',
      'defaults' => array(
        'module' => 'winkgreeting',
        'controller' => 'index',
        'action' => 'index'
      ),
    ),  
    // Admin
	'winkgreeting_extended_level' => array(
		'route' => 'admin/winkgreeting/settings/level/:level_id',
		'defaults' => array(
			'module' => 'winkgreeting',
            'controller' => 'admin-settings',
            'action' => 'level',	
			'level_id' => 4 // default selected value
		)
	),  
  )
); ?>