<?php return array (
    'package' =>
        array (
            'type' => 'module',
            'name' => 'ynfullslider',
            'version' => '4.01',
            'path' => 'application/modules/Ynfullslider',
            'title' => 'YN - Full Slider',
            'description' => '',
            'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
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
                    0 => 'application/modules/Ynfullslider',
                ),
            'files' =>
                array (
                    0 => 'application/languages/en/ynfullslider.csv',
                ),
        ),
    'items' =>
        array(
            'ynfullslider_slider',
            'ynfullslider_slide',
//            'ynfullslider_element',
        )
); ?>