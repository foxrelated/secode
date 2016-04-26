<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

return array(
  // Package -------------------------------------------------------------------
    'package' => array(
    'type' => 'module',
    'name' => 'facebookse',
     'version' => '4.8.10p2',
    'path' => 'application/modules/Facebookse',
    'repository' => 'null',
     'title' => 'Advanced Facebook Integration / Likes, Social Plugins and Open Graph',
      'description' => 'Advanced Facebook Integration / Likes, Social Plugins and Open Graph',
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
      'path' => 'application/modules/Facebookse/settings/install.php',
      'class' => 'Facebookse_Installer',
    ),
    'directories' => array(
    	'application/modules/Facebookse',
    ),
    'files' => array(
      'application/languages/en/facebookse.csv',
    ),
  ),
  
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Facebookse_Plugin_Core',
    ),
    array(
      'event' => 'onRenderLayoutDefaultSimple',
      'resource' => 'Facebookse_Plugin_Core',
    ),
    array(
        'event' => 'onItemDeleteBefore',
        'resource' => 'Facebookse_Plugin_Core',
    ),  
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'facebookse',
    'facebookse_mixsetting',
    'facebookse_widgetsettings',
    'facebookse_feedsetting', 'facebookse_mixsettings'
  ),
   // Routes --------------------------------------------------------------------
  'routes' => array(
    'facebookse_like' => array(
      'route' => '#'
    ),

    'facebookse_admin' => array(
      'route' => 'admin/facebookse/settings',
      'defaults' => array(
        'module' => 'facebookse',
        'controller' => 'admin-settings',
        'action' => 'index'
      )
    ),
    
    'facebookse_admin_like_settings' => array(
      'route' => 'admin/facebookse/settings/likes/:level_id',
      'defaults' => array(
        'module' => 'facebookse',
        'controller' => 'admin-settings',
        'action' => 'likesettings',
		'level_id' => ''
      ),
    ),
      
    'facebookse_admin_like_view' => array(
        'route' => 'admin/facebookse/settings/likeview/',
        'defaults' => array(
            'module' => 'facebookse',
            'controller' => 'admin-settings',
            'action' => 'likeview',            
        ),
    ),
    
    
    'facebookse_admin_comment_settings' => array(
      'route' => 'admin/facebookse/settings/comments/:level_id',
      'defaults' => array(
        'module' => 'facebookse',
        'controller' => 'admin-settings',
        'action' => 'commentsettings',
		'level_id' => ''
      ),
    ),
    
   
    'facebookse_admin_manage_opengraph' => array(
      'route' => 'admin/facebookse/settings/opengraph/:level_id',
      'defaults' => array(
        'module' => 'facebookse',
        'controller' => 'admin-settings',
        'action' => 'opengraph',
        'level_id' => ''
      ),
    ),

		'facebookse_admin_widget_settings' => array(
      'route' => 'admin/facebookse/settings/widgetsettings/:level_id',
      'defaults' => array(
        'module' => 'facebookse',
        'controller' => 'admin-settings',
        'action' => 'widgetsettings',
        'level_id' => ''
      ),
    ),

		'facebookse_admin_faq' => array(
      'route' => 'admin/facebookse/settings/faq/',
      'defaults' => array(
        'module' => 'facebookse',
        'controller' => 'admin-settings',
        'action' => 'faq',
      ),
    ),
    
		'facebookse_admin_like_init_settings' => array(
    'route' => 'admin/facebookse/settings/fblikeint',
    'defaults' => array(
       'module' => 'facebookse',
       'controller' => 'admin-settings',
       'action' => 'likeintsettings'
      )
    ),

		'facebookse_admin_manage_statistics' => array(
    'route' => 'admin/facebookse/settings/statistics/:page',
    'defaults' => array(
       'module' => 'facebookse',
       'controller' => 'admin-settings',
       'action' => 'statistics',
			 'page' => 1
       
       
      )
    ),

		'facebookse_admin_manage_statistics_general' => array(
    'route' => 'admin/facebookse/settings/statistics/general/:category/:duration',
    'defaults' => array(
       'module' => 'facebookse',
       'controller' => 'admin-settings',
       'action' => 'statistics',
			 'category' => 'general',
       'duration' => 1
       
      )
    ),

		'facebookse_admin_manage_statistics_contentlikes' => array(
    'route' => 'admin/facebookse/settings/statistics/contentlikes/:category/:page',
    'defaults' => array(
       'module' => 'facebookse',
       'controller' => 'admin-settings',
       'action' => 'statistics',
			 'page' => 1,
       'category' => 'contentlikes'
       
      )
    ),

		'facebookse_admin_manage_modules' => array(
			'route' => 'admin/facebookse/manage/index/:page',
			'defaults' => array(
				'module' => 'facebookse',
				'controller' => 'admin-manage',
				'action' => 'index',
				'page' => 1
		  )
    ),


    
    'facebookse_index_settings' => array(
  		'route' => 'facebookse/settings',
  		'defaults' => array(
  			'module' => 'facebookse',
      	'controller' => 'index',
      	'action' => 'mysettings'
  		)
  	),

   'facebookse_app_configure' => array(
  		'route' => 'admin/facebookse/settings/fbconfig',
  		'defaults' => array(
  			'module' => 'facebookse',
      	'controller' => 'admin-settings',
      	'action' => 'fbconfig'
  		)
  	)
  ), 
) ?>
