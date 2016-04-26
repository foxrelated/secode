<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'spamcontrol',
    'version' => '4.2.3',
    'path' => 'application/modules/Spamcontrol',
    'title' => 'Mega Spam Control',
    'description' => 'Photo, Blog, Comment Control',
    'changeLog' => 'settings/changelog.php',
    'author' => 'Nadri',
	'dependencies' => array(
		array(
			'type' => 'module',
			'name' => 'core',
			'minVersion' => '4.0.3',
		),
	),

    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'callback' => array(
      'path' => 'application/modules/Spamcontrol/settings/install.php',
      'class' => 'Spamcontrol_Installer',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Spamcontrol',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/spamcontrol.csv',
    ),
  ),
); ?>