<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitetagcheckin',
        'version' => '4.8.6p3',
        'path' => 'application/modules/Sitetagcheckin',
        'title' => 'Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin',
        'description' => 'Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' =>
        array(
            'path' => 'application/modules/Sitetagcheckin/settings/install.php',
            'class' => 'Sitetagcheckin_Installer',
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
            0 => 'application/modules/Sitetagcheckin',
        ),
       
        'files' => array(
						'application/languages/en/sitetagcheckin.csv',
            'application/modules/Activity/Model/Helper/VarCheckin.php',
        ),
    ),
    //HOOKS ---------------------------------------------------------------------
    'hooks' => array(
				array(
					'event' => 'onRenderLayoutDefault',
					'resource' => 'Sitetagcheckin_Plugin_Core'
				),
        array(
            'event' => 'onActivityActionDeleteBefore',
            'resource' => 'Sitetagcheckin_Plugin_Core',
        ),
        array(
            'event' => 'onCoreTagMapDeleteBefore',
            'resource' => 'Sitetagcheckin_Plugin_Core',
        ),
        array(
            'event' => 'onCoreTagMapCreateAfter',
            'resource' => 'Sitetagcheckin_Plugin_Core',
        ),
        array(
            'event' => 'addActivity',
            'resource' => 'Sitetagcheckin_Plugin_Core'
        ),
        array(
            'event' => 'onEventCreateAfter',
            'resource' => 'Sitetagcheckin_Plugin_Core',
        ),
        array(
            'event' => 'onUserCreateAfter',
            'resource' => 'Sitetagcheckin_Plugin_Core',
        ),
        array(
            'event' => 'onUserUpdateAfter',
            'resource' => 'Sitetagcheckin_Plugin_Core',
        ),
        array(
            'event' => 'onEventUpdateAfter',
            'resource' => 'Sitetagcheckin_Plugin_Core',
        ),
        array(
          'event' => 'onItemDeleteBefore',
          'resource' => 'Sitetagcheckin_Plugin_Core',
        )
    ),
    //COMPOSE -------------------------------------------------------------------
    'composer' => array(
        'checkin' => array(
            'script' => array('_composeCheckin.tpl', 'sitetagcheckin'),
            'plugin' => 'Sitetagcheckin_Plugin_Composer',
        ),
    ),
    //ITEMS ---------------------------------------------------------------------
    'items' => array(
        'sitetagcheckin_contents',
        'sitetagcheckin_addlocation',
        'sitetagcheckin_profilemap'
    ),
    'routes' => array(
        'sitetagcheckin_general' => array(
            'route' => 'sitetagcheckin' . '/:action/*',
            'defaults' => array(
                'module' => 'sitetagcheckin',
                'controller' => 'index',
            ),
            'reqs' => array(
                'action' => '(get-search-locations|save-location|get-location-photos|get-feed-items|get-content-feed-items|get-albums-photos)',
            ),
        ),
        'sitetagcheckin_viewmap' => array(
            'route' => 'sitetagcheckin/index/view-map/:guid/*',
            'defaults' => array(
                'module' => 'sitetagcheckin',
                'controller' => 'index',
                'action' => 'view-map'
            )
        ),
        
        'sitetagcheckin_groupspecific' => array(
					'route' => 'group/:action/:group_id/*',
						'defaults' => array(
						'module' => 'sitetagcheckin',
						'controller' => 'location',
						'action' => 'edit-location',
					),
					'reqs' => array(
						'action' => '(edit-location|edit-address)',
						'group_id' => '\d+',
					)
				),
        
				'sitetagcheckin_specific' => array(
					'route' => 'event/:action/:event_id/*',
						'defaults' => array(
						'module' => 'sitetagcheckin',
						'controller' => 'index',
						'action' => 'edit-location',
					),
					'reqs' => array(
						'action' => '(edit-location|edit-address)',
						'event_id' => '\d+',
					)
				),
				
			  'sitetagcheckin_userspecific' => array(
					'route' => 'member/:action/:user_id/*',
						'defaults' => array(
						'module' => 'sitetagcheckin',
						'controller' => 'location',
						'action' => 'edit-location',
					),
					'reqs' => array(
						'action' => '(edit-location|edit-address)',
						'event_id' => '\d+',
					)
				),
				
        'sitetagcheckin_bylocation' => array(
            'route' => 'event/by-locations',
            'defaults' => array(
                'module' => 'sitetagcheckin',
                'controller' => 'index',
                'action' => 'by-locations',
            ),
        ),
        
        'sitetagcheckin_groupbylocation' => array(
            'route' => 'group/by-locations',
            'defaults' => array(
                'module' => 'sitetagcheckin',
                'controller' => 'location',
                'action' => 'by-locations',
            ),
        ),
        
        'sitetagcheckin_videobylocation' => array(
            'route' => 'video/by-locations',
            'defaults' => array(
                'module' => 'sitetagcheckin',
                'controller' => 'location',
                'action' => 'videoby-locations',
            ),
        ),
        
        'sitetagcheckin_albumbylocation' => array(
            'route' => 'album/by-locations',
            'defaults' => array(
                'module' => 'sitetagcheckin',
                'controller' => 'location',
                'action' => 'albumby-locations',
            ),
        ),
        
        'sitetagcheckin_userbylocation' => array(
            'route' => 'member/userby-locations',
            'defaults' => array(
                'module' => 'sitetagcheckin',
                'controller' => 'location',
                'action' => 'userby-locations',
            ),
        ),
    )
);
?>