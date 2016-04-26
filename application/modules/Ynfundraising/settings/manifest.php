<?php return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'ynfundraising',
    'version' => '4.03p3',
    'path' => 'application/modules/Ynfundraising',
    'title' => 'YN - Fundraising',
    'description' => '',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
    'dependencies' => array(
      array(
         'type' => 'module',
         'name' => 'younet-core',
         'minVersion' => '4.02',
      ),
    ),
    'callback' =>
    array (
      'path' => 'application/modules/Ynfundraising/settings/install.php',
      'class' => 'Ynfundraising_Installer',
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
      0 => 'application/modules/Ynfundraising',
    ),
    'files' =>
    array (
      0 => 'application/languages/en/ynfundraising.csv',
    ),
  ),
   // Items ---------------------------------------------------------------------
    'items' => array(
        'ynfundraising_campaign',
        'ynfundraising_request',
        'ynfundraising_supporter',
        'ynfundraising_photo',
        'ynfundraising_album',
        'ynfundraising_new',
        'ynfundraising_mail_template',
        'ynfundraising_follow',
        'ynfundraising_sponsor_level',
    	'ynfundraising_donation',
    	'ynfundraising_transaction',
    	'ynfundraising_follow'
    ),
   // Routes --------------------------------------------------------------------
    'routes' => array(
        'ynfundraising_extended' => array(
          'route' => 'fundraising/:controller/:action/*',
          'defaults' => array(
            'module' => 'ynfundraising',
            'controller' => 'index',
            'action' => 'index',
          ),
          'reqs' => array(
                'action' => 'delete|view-statistics-detail|edit|summary|notify|index|upload|view-statistics-chart|view-statistics-list|share|delete-news|edit-news|view-all-supporters|view-all-donors|approve|deny|delete|cancel|view-reason|chart-data',
          ),
        ),
        'ynfundraising_general' => array(
            'route' => 'fundraising/:action/*',
            'defaults' => array(
                'module' => 'ynfundraising',
                'controller' => 'index',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(manage-requests|delete|past-campaigns|list|browse|create|close|confirm-create|request-create|cancel-request|create-step-one|edit-step-one|create-step-two|create-step-three|add-sponsor-level|remove-sponsor-level|edit-sponsor-level|create-step-four|create-step-five|create-step-six|create-step-seven|add-location|edit|delete|view|invite-friends|email-donors|promote|send|user-rate|campaign-rate|owner-close|campaign-badge)',
            ),
        ),

    ),
); ?>