<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manifest.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
$videosRoute = "videos";
$chanelsRoute = "chanels";
$module = null;
$controller = null;
$action = null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName();
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {
  $setting = Engine_Api::_()->getApi('settings', 'core');
  $videosRoute = $setting->getSetting('video.videos.manifest', 'videos');
  $videoRoute = $setting->getSetting('video.video.manifest', 'video');
  $chanelsRoute = $setting->getSetting('video.chanels.manifest');
	$chanelRoute = $setting->getSetting('video.chanel.manifest');
}
return array(
    'package' => array(
        'type' => 'module',
        'name' => 'sesvideo',
        'version' => '4.8.9p7',
        'path' => 'application/modules/Sesvideo',
        'title' => 'Advanced Videos & Channels Plugin',
        'description' => 'Advanced Videos & Channels Plugin',
        'author' => '<a href="http://www.socialenginesolutions.com" style="text-decoration:underline;" target="_blank">SocialEngineSolutions</a>',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sesvideo/settings/install.php',
            'class' => 'Sesvideo_Installer',
        ),
        'directories' => array(
            'application/modules/Sesvideo',
        ),
        'files' => array(
            'application/languages/en/sesvideo.csv',
        ),
    ),
    // Compose
    'composer' => array(
        'video' => array(
            'script' => array('_composeVideo.tpl', 'sesvideo'),
            'plugin' => 'Sesvideo_Plugin_Composer',
            'auth' => array('video', 'create'),
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'video', 'sesvideo_video', 'sesvideo_category', 'sesvideo_chanel', 'sesvideo_playlist', 'watchlater', 'sesvideo_chanelphoto', 'sesvideo_playlistvideo', 'sesvideo_chanelvideo', 'sesvideo_artist', 'sesvideo_artists', 'sesvideo_gallery', 'sesvideo_slide'
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onStatistics',
            'resource' => 'Sesvideo_Plugin_Core'
        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Sesvideo_Plugin_Core',
        ),
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Sesvideo_Plugin_Core'
        ),
				array(
            'event' => 'onRenderLayoutDefaultSimple',
            'resource' => 'Sesvideo_Plugin_Core'
        )
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sesvideo_general' => array(
            'route' => $videosRoute . '/:action/*',
            'defaults' => array(
                'module' => 'sesvideo',
                'controller' => 'index',
                'action' => 'welcome',
            ),
            'reqs' => array(
                'action' => '(home|index|browse|create|list|manage|view|share|tags|locations|edit|delete|browse-pinboard)',
            )
        ),
        //lightbox all videos
        'sesvideo_allvideos' => array(
            'route' => $videoRoute.'/all-videos/:user_id/:video_id/:slug/*',
            'defaults' => array(
                'module' => 'sesvideo',
                'controller' => 'index',
                'action' => 'all-videos',
                'slug' => '',
            )
        ),
        //lightbox video
        'sesvideo_lightbox' => array(
            'route' => $videoRoute.'/imageviewerdetail/:user_id/:video_id/:slug/*',
            'defaults' => array(
                'module' => 'sesvideo',
                'controller' => 'index',
                'action' => 'imageviewerdetail',
                'slug' => '',
            )
        ),
        'sesvideo_playlist' => array(
            'route' => $videosRoute.'/playlist/:action',
            'defaults' => array(
                'module' => 'sesvideo',
                'controller' => 'playlist',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(add)',
            )
        ),
        'sesvideo_playlist_view' => array(
            'route' => $videosRoute.'/playlist/:playlist_id/:slug/:action/*',
            'defaults' => array(
                'module' => 'sesvideo',
                'controller' => 'playlist',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'playlist_id' => '\d+',
                'action' => '(edit|delete)',
            )
        ),
        'sesvideo_view' => array(
            'route' => $videoRoute.'/:user_id/:video_id/:slug/*',
            'defaults' => array(
                'module' => 'sesvideo',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            )
        ),
        'sesvideo_category' => array(
            'route' => $videosRoute . '/categories/:action/*',
            'defaults' => array(
                'module' => 'sesvideo',
                'controller' => 'category',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(index|browse)',
            )
        ),
        'sesvideo_watchlater' => array(
            'route' => $videosRoute . '/watchlater/:action/*',
            'defaults' => array(
                'module' => 'sesvideo',
                'controller' => 'watchlater',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(index|browse|add)',
            )
        ),
        'sesvideo_category_view' => array(
            'route' => $videosRoute . '/category/:category_id/*',
            'defaults' => array(
                'module' => 'sesvideo',
                'controller' => 'category',
                'action' => 'index',
            )
        ),
        'sesvideo_chanel_category' => array(
            'route' => $videosRoute . '/' . $chanelsRoute . '/category/*',
            'defaults' => array(
                'module' => 'sesvideo',
                'controller' => 'chanel',
                'action' => 'category',
            ),
        ),
        'sesvideo_chanel' => array(
            'route' => $videosRoute . '/' . $chanelsRoute . '/:action/*',
            'defaults' => array(
                'module' => 'sesvideo',
                'controller' => 'chanel',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(index|browse|create|list|manage|edit|delete|invite|photos|edit-photo|save-information|like|image-viewer-detail|last-element-data|all-photos|last-element-data|delete-photo|download|view|location|overview)',
            )
        ),
        'sesvideo_chanel_view' => array(
            'route' => $videoRoute . '/'.$chanelRoute.'/:chanel_id/*',
            'defaults' => array(
                'module' => 'sesvideo',
                'controller' => 'chanel',
                'action' => 'index',
            )
        ),
        'sesvideo_artists' => array(
            'route' => $videosRoute.'/artists/:action/*',
            'defaults' => array(
                'module' => 'sesvideo',
                'controller' => 'artist',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(browse|favourite-artists)',
            ),
        ),
        'sesvideo_artist' => array(
            'route' => $videosRoute.'/artist/:artist_id/:slug/*',
            'defaults' => array(
                'module' => 'sesvideo',
                'controller' => 'artist',
                'action' => 'view',
            ),
            'reqs' => array(
                'artist_id' => '\d+'
            )
        ),
    ),
);
?>