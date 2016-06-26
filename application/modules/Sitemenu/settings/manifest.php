<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitemenu',
        'version' => '4.8.10p4',
        'path' => 'application/modules/Sitemenu',
        'title' => 'Advanced Menus Plugin - Interactive and Attractive Navigation',
        'description' => 'Advanced Menus Plugin - Interactive and Attractive Navigation',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' =>
        array(
            'path' => 'application/modules/Sitemenu/settings/install.php',
            'class' => 'Sitemenu_Installer',
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
            0 => 'application/modules/Sitemenu',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitemenu.csv',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sitemenu_module',
    ),
    //Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Sitemenu_Plugin_Core'
        ),
    ),
);