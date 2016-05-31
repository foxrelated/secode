<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'siteadvsearch',
        'version' => '4.8.10p4',
        'path' => 'application/modules/Siteadvsearch',
        'title' => 'Advanced Search Plugin',
        'description' => 'Advanced Search Plugin',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' => array(
            'path' => 'application/modules/Siteadvsearch/settings/install.php',
            'class' => 'Siteadvsearch_Installer',
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
            0 => 'application/modules/Siteadvsearch',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/siteadvsearch.csv',
        ),
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onSitereviewListingtypeUpdateAfter',
            'resource' => 'Siteadvsearch_Plugin_Core',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'siteadvsearch_content',
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'siteadvsearch_admin_general' => array(
            'route' => 'siteadvsearch/:action/*',
            'defaults' => array(
                'module' => 'siteadvsearch',
                'controller' => 'admin-manage',
                'action' => 'show-tab',
            ),
            'reqs' => array(
                'action' => '(show-content-search|show-tab|edit-content|delete-content|add-icon|edit-icon|enable-search)'
            ),
        ),
        'siteadvsearch_general' => array(
            'route' => 'search/:action/*',
            'defaults' => array(
                'module' => 'siteadvsearch',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index)'
            ),
        ),
    )
);