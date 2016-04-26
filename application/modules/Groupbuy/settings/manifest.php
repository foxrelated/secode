<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'groupbuy',
    'version' => '4.04p4',
    'path' => 'application/modules/Groupbuy',
    'title' => 'YN - Group Buy',
    'description' => 'This is Group Buy module.',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.2',
      ),
      array(
         'type' => 'module',
         'name' => 'younet-core',
         'minVersion' => '4.02',
      ),
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
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
      0 => 'application/modules/Groupbuy'
    ),
    'files' => 
    array (
      0 => 'application/languages/en/groupbuy.csv',
    ),
  ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onStatistics',
            'resource' => 'Groupbuy_Plugin_Core'
        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Groupbuy_Plugin_Core'
        ),
         array(
            'event' => 'onUserCreateAfter',
            'resource' => 'Groupbuy_Plugin_Core'
        )
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'groupbuy_deal',
        'groupbuy_category',
        'groupbuy_location',
        'groupbuy_param',
        'deal',
        'groupbuy_album',
        'groupbuy_buy_deal',
        'groupbuy_photo',
        'groupbuy_mail_template',
        'groupbuy_order',
    ),
   // Routes --------------------------------------------------------------------
    'routes' => array(
        'groupbuy_extended' => array(
          'route' => 'group-buy/:controller/:action/*',
          'defaults' => array(
            'module' => 'groupbuy',
            'controller' => 'index',
            'action' => 'browse',
          ),
        ),
        'groupbuy_general' => array(
            'route' => 'group-buy/:action/*',
            'defaults' => array(
                'module' => 'groupbuy',
                'controller' => 'index',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(update-order|browse|create|success|edit|admin-edit|delete|detail|rate|publish|publish-free|manage-selling|manage-buying|stop|start|transaction|history|userdeal|approve|deny|listing|email|request|request-refund|delete-buy|contract|buy-deal|delivery|statistic|reopen|buydeals|buygift|editgift|deletegift|editcoupon|accountmoney|publishmoney)',
            ),
        ),
        'groupbuy_cron' => array(
          'route' => 'group-buy/cron/',
          'defaults' => array(
            'module' => 'groupbuy',
            'controller' => 'cron',
            'action' => 'index',
          ),
        ),
        'groupbuy_help' => array(
           'route'    => 'group-buy/help/*',
           'defaults' => array(
               'module'     => 'groupbuy',
               'controller' => 'help',
               'action'     => 'detail',
           ),
        ),
        'groupbuy_faqs' => array(
           'route'    => 'group-buy/faqs/*',
           'defaults' => array(
               'module'     => 'groupbuy',
               'controller' => 'faqs',
               'action'     => 'index',
           ),
        ),
        'groupbuy_account' => array(
            'route' => 'group-buy/account/:action/*',
            'defaults' => array(
                'module' => 'groupbuy',
                'controller' => 'account',
                'action' => 'index',
            ),
        ),
         'groupbuy_viewtransaction' => array(
         'route' => 'group-buy/viewtransaction/:id/:username',
          'defaults' => array(
            'module' => 'groupbuy',
            'controller' => 'index',
            'action' => 'view-transaction',
          ),
          ),
        'groupbuy_payment_threshold' => array(
          'route' => 'group-buy/account/threshold/*',
          'defaults' => array(
            'module' => 'groupbuy',
            'controller' => 'account',
            'action' => 'threshold',
          ),
        ),
        
		'groupbuy_transaction' => array(
	      'route' => 'group-buy/transaction-process/:action/*',
	      'defaults' => array(
	        'module' => 'groupbuy',
	        'controller' => 'transaction',
	        'action' => 'index'
	      )
    	),
    	
         'groupbuy_admin_main_request' => array(
          'route' => 'admin/groupbuy/request/*',
          'defaults' => array(
            'module' => 'groupbuy',
            'controller' => 'admin-request',
            'action' => 'index',
         
           ),
          'reqs' => array(
            'page' => '\d+' ,
            'action' => '(index|update)' ,
          ),
        ), 
        'groupbuy_admin_request-payment'  => array( 
        'route' => 'admin/groupbuy/request/request-payment/*',
          'defaults' => array(
            'module' => 'groupbuy',
            'controller' => 'admin-request',
            'action' => 'request-payment',
          ),
        ),
        'groupbuy_admin_main_refund' => array(
          'route' => 'admin/groupbuy/refund/:page/*',
          'defaults' => array(
            'module' => 'groupbuy',
            'controller' => 'admin-refund',
            'action' => 'index',
         'page' => 1,
           ),
          'reqs' => array(
            'page' => '\d+'
          ),
        ), 
        'groupbuy_admin_refund-payment'  => array( 
        'route' => 'admin/groupbuy/refund/refund-payment/*',
          'defaults' => array(
            'module' => 'groupbuy',
            'controller' => 'admin-refund',
            'action' => 'refund-payment',
          ),
        ),
        
		'groupbuy_mytransaction' => array(
	      'route' => 'group-buy/my-transaction/*',
	      'defaults' => array(
	        'module' => 'groupbuy',
	        'controller' => 'index',
	        'action' => 'transaction'
	      )
    	),
    	
        ),
); ?>