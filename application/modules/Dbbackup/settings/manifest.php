<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'dbbackup',
    'version' => '4.8.10',
    'path' => 'application/modules/Dbbackup',
		'title' => 'Backup and Restore',
		'description' => 'Backup and Restore',
'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
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