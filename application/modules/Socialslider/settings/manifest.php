<?php

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'socialslider',
        'version' => '4.2.8',
        'path' => 'application/modules/Socialslider',
        'title' => 'Social Slider',
        'description' => '',
        'author' => 'SocialEnginePro',
        'callback' =>
        array(
            'path' => 'application/modules/Socialslider/settings/install.php',
            'class' => 'Socialslider_Installer',
        ),
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'sepcore',
                'minVersion' => '4.2.0',
            ),
        ),
        'directories' =>
        array(
            0 => 'application/modules/Socialslider',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/socialslider.csv',
        ),
    ),
    'widgets' => array(
        array(
            'page' => 'footer',
            'name' => 'socialslider.slider',
            'parent_content_name' => 'main',
            'order' => 999,
        ),
    ),
    'menu' => array(
        'socialslider_admin_main' => array(
            'title' => '',
            'menuitems' => array(
                array(
                    'name' => 'socialslider_admin_main_buttons',
                    'label' => 'Social Buttons',
                    'plugin' => '',
                    'params' => '{"route":"admin_default", "module":"socialslider", "controller": "settings", "action":"index"}'
                ),
                array(
                    'name' => 'socialslider_admin_main_addbutton',
                    'label' => 'Custom Buttons',
                    'plugin' => '',
                    'params' => '{"route":"admin_default","module":"socialslider","controller":"settings","action":"add"}'
                ),
                array(
                    'name' => 'socialslider_admin_main_settings',
                    'label' => 'Global Settings',
                    'plugin' => '',
                    'params' => '{"route":"admin_default", "module":"socialslider", "controller": "settings", "action":"manage"}'
                ),
            ),
        ),
        'core_admin_main_plugins' => array(
            'title' => '',
            'menuitems' => array(
                array(
                    'name' => 'core_admin_main_plugins_socialslider',
                    'label' => 'Social Slider',
                    'plugin' => '',
                    'params' => '{"route":"admin_default","module":"socialslider","controller":"settings","action":"index"}'
                ),
            ),
        ),
    ),
);
?>
