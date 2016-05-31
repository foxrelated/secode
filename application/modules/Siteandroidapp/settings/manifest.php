<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    manifest.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'siteandroidapp',
        'version' => '4.8.10p7',
        'path' => 'application/modules/Siteandroidapp',
        'title' => 'Android Mobile Application',
        'description' => 'Android Mobile Application',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' =>
        array(
            'path' => 'application/modules/Siteandroidapp/settings/install.php',
            'class' => 'Siteandroidapp_Installer',
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
            0 => 'application/modules/Siteandroidapp',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/siteandroidapp.csv',
        ),
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onActivityNotificationCreateAfter',
            'resource' => 'Siteandroidapp_Plugin_Pushnotification',
        )
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'siteandroidapp_menus'
    ),
);
?>