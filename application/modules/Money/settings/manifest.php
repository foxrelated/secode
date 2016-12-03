<?php

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'money',
        'version' => '4.3.0',
        'path' => 'application/modules/Money',
        'title' => 'Emoney',
        'description' => '',
        'author' => 'SocialEnginePro',
        'changeLog' => 'settings/changelog.php',
        'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'sepcore',
                'minVersion' => '4.2.0',
            ),
        ),
        'callback' =>
        array(
            'path' => 'application/modules/Money/settings/install.php',
            'class' => 'Money_Installer',
        ),
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Money',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/money.csv',
            2 => 'application/libraries/Engine/Payment/Gateway/Webmoney.php',
            3 => 'application/libraries/Engine/Service/Webmoney.php',
            4 => 'application/libraries/Engine/Payment/Gateway/LiqPay.php',
            5 => 'application/libraries/Engine/Service/LiqPay.php',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'money_order',
        'money_gateway',
        'money_subscription',
        'money_issue',
        'money_transaction',
        'money_package'
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'money_general' => array(
            'route' => 'e-money/:action/*',
            'defaults' => array(
                'module' => 'money',
                'controller' => 'index',
                'action' => 'transaction',
            ),
            'reqs' => array(
                'action' => '(recharge|send|transaction|issue|paid)',
            ),
        ),
        'money_subscription' => array(
            'route' => 'e-money/subscription/:action/*',
            'defaults' => array(
                'module' => 'money',
                'controller' => 'subscription',
                'action' => 'choose'
            ),
            'regs' => array(
                'action' => '(gateway|result|choose)',
            )
        ),
        'money_order' => array(
            'route' => 'e-money/order/:action/:type/:id/*',
            'defaults' => array(
                'module' => 'money',
                'controller' => 'order',
                'action' => 'paid'
            ),
            'regs' => array(
                'action' => '(recharge|paid)',
                'id' => '\d+',
                'type' => '\D+')
        ),
        'money_connect' => array(
            'route' => 'e-money/order/:action/:type/:id/:plugin_type/*',
            'defaults' => array(
                'module' => 'money',
                'controller' => 'order',
                'action' => 'paid-subject'
            ),
            'regs' => array(
                'action' => '(paid-subject)',
                'id' => '\d+',
                'type' => '\D+',
                'plugin_type' => '\D+'
            )
        ),
    )
);
?>