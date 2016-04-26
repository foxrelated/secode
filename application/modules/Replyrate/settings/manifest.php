<?php
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'replyrate',
    'version' => '4.0.0',
    'revision' => '',
    'path' => 'application/modules/Replyrate',
    'repository' => 'spur-i-t.com',
    'title' => 'Reply Rate',
    'description' => 'Reply rate shows the ratio of incoming messages and answers to them.',
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
      'application/modules/Replyrate',
    ),
    'files' => array(
      'application/languages/en/replyrate.csv',
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
    // Admin
	'replyrate_extended_level' => array(
		'route' => 'admin/replyrate/settings/level/:level_id',
		'defaults' => array(
			'module' => 'replyrate',
            'controller' => 'admin-settings',
            'action' => 'level',	
			'level_id' => 4 // default selected value
		)
	),  
  )
); ?>