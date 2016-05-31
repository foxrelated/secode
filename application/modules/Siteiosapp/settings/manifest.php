<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    manifest.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'siteiosapp',
        'version' => '4.8.10p7',
        'path' => 'application/modules/Siteiosapp',
        'title' => 'iOS Mobile Application - iPhone and iPad',
        'description' => 'iOS Mobile Application - iPhone and iPad',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' =>
        array(
            'path' => 'application/modules/Siteiosapp/settings/install.php',
            'class' => 'Siteiosapp_Installer',
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
            0 => 'application/modules/Siteiosapp',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/siteiosapp.csv',
        ),
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onActivityNotificationCreateAfter',
            'resource' => 'Siteiosapp_Plugin_Pushnotification',
        )
    ),
    'items' => array(
        'siteiosapp_menus'
    ),
);
?>