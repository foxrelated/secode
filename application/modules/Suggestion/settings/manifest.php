<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    // Package -------------------------------------------------------------------

    'package' => array(
        'type' => 'module',
        'name' => 'suggestion',
        'version' => '4.8.5p1',
        'path' => 'application/modules/Suggestion',
        'repository' => 'null',
        'title' => 'Suggestions',
        'description' => 'This plugin provides you the best tools to increase user engagement on your Social Network. This highly customizable plugin enables your site to recommend various content and friends to users, just like Facebook does, and is arguably the most useful social graph feature for your Social Network. The algorithms behind the suggestions are based on user relevance, and highlight content and people that the users might actually be interested in.',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Tuesday, 17 Aug 2010 18:33:08 +0000',
        'copyright' => 'Copyright 2009-2010 BigStep Technologies Pvt. Ltd.',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Suggestion/settings/install.php',
            'class' => 'Suggestion_Installer',
        ),
        'directories' => array(
            'application/modules/Suggestion',
        ),
        'files' => array(
            'application/languages/en/suggestion.csv',
        ),
    ),
    'sitemobile_compatible' => true,
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Suggestion_Plugin_Core',
        ),
        array(
            'event' => 'addActivity',
            'resource' => 'Suggestion_Plugin_Core',
        ),
        array(
            'event' => 'onUserCreateAfter',
            'resource' => 'Suggestion_Plugin_Core',
        ),
        array(
            'event' => 'onItemDeleteAfter',
            'resource' => 'Suggestion_Plugin_Core',
        ),
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Suggestion_Plugin_Core',
        )
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'suggestion',
        'suggestion_rejected',
        'suggestion_photo',
        'suggestion_album',
        'suggestion_introduction',
        'suggestion_modinfo'
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'friends_suggestions_viewall' => array(
            'route' => 'suggestions/friends_suggestions',
            'defaults' => array(
                'module' => 'suggestion',
                'controller' => 'index',
                'action' => 'viewfriendsuggestion'
            )
        ),
        'suggestion_invite_statistics' => array(
            'route' => 'suggestions/friends_statistics',
            'defaults' => array(
                'module' => 'suggestion',
                'controller' => 'index',
                'action' => 'viewstatistics'
            )
        ),
        'suggestion_admin_setting_invite_statistics' => array(
            'route' => 'admin/suggestion/settings/invite-statistics/:category',
            'defaults' => array(
                'module' => 'suggestion',
                'controller' => 'admin-settings',
                'action' => 'invite-statistics',
                'category' => 'listview'
            )
        ),
        'suggestions_display' => array(
            'route' => 'suggestions/viewall',
            'defaults' => array(
                'module' => 'suggestion',
                'controller' => 'index',
                'action' => 'viewall'
            )
        ),
        'received_suggestion' => array(
            'route' => 'suggestions/view/:sugg_id',
            'defaults' => array(
                'module' => 'suggestion',
                'controller' => 'index',
                'action' => 'view'
            )
        ),
        'suggestion_admin_widget_setting' => array(
            'route' => 'admin/suggestion/settings/id/:sugg_select',
            'defaults' => array(
                'module' => 'suggestion',
                'controller' => 'admin-settings',
                'action' => 'index'
            )
        ),
        'sugg_explore_friend' => array(
            'route' => 'suggestions/explore',
            'defaults' => array(
                'module' => 'suggestion',
                'controller' => 'index',
                'action' => 'explore'
            )
        ),
        'suggest_to_friend_link' => array(
            'route' => 'suggestion/index/suggestions/*',
            'defaults' => array(
                'module' => 'suggestion',
                'controller' => 'index',
                'action' => 'switch-popup',
            ),
        ),
        'suggestion_app_config' => array(
            'route' => 'admin/suggestion/global/appconfigs',
            'defaults' => array(
                'module' => 'suggestion',
                'controller' => 'admin-global',
                'action' => 'appconfigs'
            )
        ),
        'suggestion_global_global' => array(
            'route' => 'admin/suggestion/global/global',
            'defaults' => array(
                'module' => 'suggestion',
                'controller' => 'admin-global',
                'action' => 'global'
            )
        ), //Route for sitemobile suggestion plugin.
        'suggestion_friend_request' => array(
            'route' => 'suggestions/friends-request/:tab',
            'defaults' => array(
                'module' => 'suggestion',
                'controller' => 'index-sitemobile',
                'action' => 'request',
                'tab' => 0
            )
        ),
        'suggestion_explore_suggestions' => array(
            'route' => 'suggestions/explore-suggestions/:tab',
            'defaults' => array(
                'module' => 'suggestion',
                'controller' => 'index-sitemobile',
                'action' => 'suggestions',
                'tab' => 0
            )
        )
    )
        )
?>
