<?php
return array(
		'package' => array(
			'type' => 'module',
			'name' => 'ynmobile',
			'version' => '4.13p1',
			'path' => 'application/modules/Ynmobile',
			'title' => 'YouNet Mobile SocialEngine',
			'description' => '',
			'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
			'callback' => array('class' => 'Engine_Package_Installer_Module', ),
			'actions' => array(
					0 => 'install',
					1 => 'upgrade',
					2 => 'refresh',
					3 => 'enable',
					4 => 'disable',
			),

			'directories' => array(

				 0 => 'application/modules/Ynmobile',
				 1 => 'wideimage',
                 2 => 'public/ynmobile',
			 ),
			'files' => array(
			     0 => 'application/languages/en/ynmobile.csv',
			     1 => 'cometchat/cometchat_api_mysqli.php',
			     2 => 'cometchat/cometchat_api.php', 
            ),
		),
		// Hooks ---------------------------------------------------------------------
		'hooks' => array(
						array(
							'event' => 'onActivityNotificationCreateAfter',
							'resource' => 'Ynmobile_Plugin_Core',
						),
					),
		'items' => array(
				'ynmobile_map'
		),
    'routes'=>array(
        'ynmobile_file_css' => array(
            'route' => 'public/ynmobile/css/:filename',
            'defaults' => array(
                'module' => 'ynmobile',
                'controller' => 'index',
                'action' => 'index',
            )
        ),
    )
);

?>
