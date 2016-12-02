<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'dbbackup',
    'version' => '4.2.3',
    'path' => 'application/modules/Dbbackup',
		'title' => 'Backup and Restore',
		'description' => 'Backup and Restore',
		'author' => 'SocialEngineAddOns',
    'callback' => 
    array (
      'path' => 'application/modules/Dbbackup/settings/install.php',
      'class' => 'Dbbackup_Installer',
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
      0 => 'application/modules/Dbbackup',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/dbbackup.csv',
    ),
      
    // Items ---------------------------------------------------------------------
  	
  ),
  'items' => array(
     'dbbackup',
     'destinations',
     'backuplog'
  ),
); ?>