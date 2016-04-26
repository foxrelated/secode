<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$module = null;
$controller = null;
$action = null;
$request = Zend_Controller_Front::getInstance()->getRequest();
$routes = array();
if (!empty($request)) {
    $module = $request->getModuleName();
    $action = $request->getActionName();
    $controller = $request->getControllerName();
}

if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {

    $db = Engine_Db_Table::getDefaultAdapter();

    $slug_plural = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.slugplural', 'event-items');
    $slug_singular = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.slugsingular', 'event-item');

    $routes = array(

        'siteevent_user_review' => array(
            'route' => $slug_plural . '/:controller/:action/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'userreview',
                'action' => 'view',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '(view|create)',
            )
        ),

        'siteevent_extended' => array(
            'route' => $slug_plural . '/:controller/:action/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'index',
                'action' => 'home',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
        'siteevent_diary_view' => array(
            'route' => $slug_plural . '/diary/:diary_id/:slug/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'diary',
                'action' => 'profile',
                'slug' => '',
            ),
            'reqs' => array(
                'diary_id' => '\d+'
            )
        ),
        'siteevent_diary_general' => array(
            'route' => $slug_plural . '/diaries/:action/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'diary',
                'action' => 'browse'
            ),
            'reqs' => array(
                'action' => '(browse|create|edit|add|cover-photo|delete|remove|print|tell-a-friend|message-owner)',
            ),
        ),
        'siteevent_review_editor' => array(
            'route' => $slug_plural . '/editors/:action/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'editor',
                'action' => 'home',
            ),
        ),
        'siteevent_general' => array(
            'route' => $slug_plural . '/:action/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'index',
                'action' => 'home',
            ),
            'reqs' => array(
                'action' => '(home|categories|index|top-rated|manage|create|ajaxhomesiteevent|tagscloud|get-search-events|sub-category|subsub-category|map|upload-photo|pinboard|calendar|show-radius-tip)',
            ),
        ),
        'siteevent_editor_general' => array(
            'route' => $slug_plural . '/editor/:action/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'editor',
                'action' => 'home',
            ),
            'reqs' => array(
                'action' => '(home|similar-items|add-items|categories)',
            ),
        ),
        'siteevent_review_editor_profile' => array(
            'route' => $slug_plural . '/editor/profile/:username/:user_id',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'editor',
                'action' => 'profile',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            )
        ),
        'siteevent_organizer_profile' => array(
            'route' => $slug_plural . '/host/profile/:organizer_id/:slug',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'organizer',
                'action' => 'view',
            ),
            'reqs' => array(
                'organizer_id' => '\d+'
            )
        ),
        'siteevent_priceinfo' => array(
            'route' => $slug_singular . '/priceinfo/:action/:id/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'price-info',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index|add|edit|delete|redirect)',
            ),
        ),
        'siteevent_entry_view' => array(
            'route' => $slug_singular . '/:slug/:event_id/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'index',
                'action' => 'view',
                'slug' => ''
            ),
            'reqs' => array(
                'event_id' => '\d+'
            )
        ),
        'siteevent_entry_view_occurrence' => array(
            'route' => $slug_singular . '/:slug/:event_id/:occurrence_id/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
                'occurrence_id' => ''
            ),
            'reqs' => array(
                'event_id' => '\d+',
                'occurrence_id' => '\d+'
            )
        ),
        'siteevent_specific' => array(
            'route' => $slug_singular . '/:action/:event_id/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'index',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(messageowner|tellafriend|print|delete|publish|close|edit|overview|editstyle|editlocation|editaddress|add-to-my-calendar|messageLeader|notifications)',
                'event_id' => '\d+',
            )
        ),
        'siteevent_dashboard' => array(
            'route' => $slug_singular . '/:action/:event_id/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'dashboard',
            ),
            'reqs' => array(
                'action' => '(contact|change-photo|remove-photo|meta-detail|notification-settings|ical-outlook)',
                'event_id' => '\d+',
            )
        ),
        'siteevent_image_specific' => array(
            'route' => $slug_singular . '/photo/view/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'photo',
                'action' => 'view',
            ),
            'reqs' => array(
                'action' => '(view|remove)',
            ),
        ),
        'siteevent_photo_extended' => array(
            'route' => $slug_singular . '/photo/:action/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'photo',
                'action' => 'edit',
            ),
            'reqs' => array(
                'action' => '\D+',
            )
        ),
        'siteevent_photoalbumupload' => array(
            'route' => $slug_plural . '/photo/:event_id/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'photo',
                'action' => 'upload',
                'event_id' => '0',
            )
        ),
        'siteevent_albumspecific' => array(
            'route' => $slug_plural . '/album/:action/:event_id/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'album',
                'action' => 'editphotos',
            ),
            'reqs' => array(
                'action' => '(compose-upload|delete|edit|editphotos|upload|view)',
            ),
        ),
        'siteevent_videospecific' => array(
            'route' => $slug_plural . '/videos/:action/:event_id/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'videoedit',
                'action' => 'edit',
            ),
            'reqs' => array(
                'action' => '(compose-upload|delete|edit|editphotos|upload|view)',
            ),
        ),
        'siteevent_video_upload' => array(
            'route' => $slug_singular . '/video/:action/:event_id/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'video',
                'action' => 'index',
                'event_id' => '0',
            ),
        ),
        'siteevent_general_category' => array(
            'route' => $slug_plural . '/category/:categoryname/:category_id/browse',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+',
            ),
        ),
        'siteevent_category_home' => array(
            'route' => $slug_plural . '/category/:categoryname/:category_id',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'index',
                'action' => 'category-home',
            ),
            'reqs' => array(
                'category_id' => '\d+',
            ),
        ),
        'siteevent_general_subcategory' => array(
            'route' => $slug_plural . '/category/:categoryname/:category_id/:subcategoryname/:subcategory_id',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+',
                'subcategory_id' => '\d+',
            ),
        ),
        'siteevent_general_subsubcategory' => array(
            'route' => $slug_plural . '/category/:categoryname/:category_id/:subcategoryname/:subcategory_id/:subsubcategoryname/:subsubcategory_id',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+',
                'subcategory_id' => '\d+',
                'subsubcategory_id' => '\d+',
            ),
        ),
        'siteevent_review_browse' => array(
            'route' => $slug_plural . '/reviews/browse/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'review',
                'action' => 'browse',
            ),
        ),
        'siteevent_review_categories' => array(
            'route' => $slug_plural . '/categories/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'index',
                'action' => 'categories',
            ),
        ),
        'siteevent_user_general' => array(
            'route' => $slug_plural . '/review/:action/event_id/:event_id/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'review',
            ),
            'reqs' => array(
                'event_id' => '\d+',
                'action' => '(create|edit|update|reply|helpful|email|delete)'
            ),
        ),
        'siteevent_view_review' => array(
            'route' => $slug_plural . '/review/:action/:review_id/:event_id/:slug/:tab/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'review',
                'action' => 'view',
                'slug' => '',
                'tab' => ''
            ),
            'reqs' => array(
                'review_id' => '\d+',
                'event_id' => '\d+'
            ),
        ),
        'siteevent_video_general' => array(
            'route' => $slug_plural . '/video/:action/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'video',
                'action' => 'view',
            ),
            'reqs' => array(
                'action' => '(index|create)',
            )
        ),
        'siteevent_video_view' => array(
            'route' => $slug_plural . '/video/:event_id/:user_id/:video_id/:slug/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'video',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            )
        ),
        'siteevent_video_create' => array(
            'route' => $slug_plural . '/video/create/:event_id/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'video',
                'action' => 'create',
            ),
            'reqs' => array(
                'event_id' => '\d+'
            )
        ),
        'siteevent_video_edit' => array(
            'route' => $slug_plural . '/video/edit/:event_id/:video_id/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'video',
                'action' => 'edit',
            )
        ),
        'siteevent_video_embed' => array(
            'route' => $slug_plural . '/videos/embed/:id/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'video',
                'action' => 'embed',
            )
        ),
        'siteevent_video_delete' => array(
            'route' => $slug_plural . '/video/delete/:event_id/:video_id/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'video',
                'action' => 'delete',
            ),
            'reqs' => array(
                'video_id' => '\d+',
                'event_id' => '\d+'
            )
        ),
        'siteevent_video_tags' => array(
            'route' => $slug_plural . '/video/tagscloud/:event/',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'index',
                'action' => 'tags-cloud',
                'event' => 1,
            )
        ),
        'siteevent_video_general' => array(
            'route' => 'review-videos/:action/*',
            'defaults' => array(
                'module' => 'siteevent',
                'controller' => 'video',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(index|browse)',
            )
        ),
    );
}

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'siteevent',
        'version' => '4.8.10p4',
        'path' => 'application/modules/Siteevent',
        'title' => 'Advanced Events',
        'description' => 'Advanced Events Plugin',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thursday, 2014-01-02 00:00:00Z',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Siteevent/settings/install.php',
            'class' => 'Siteevent_Installer',
        ),
        'directories' => array(
            'application/modules/Siteevent',
        ),
        'files' => array(
            'application/languages/en/siteevent.csv',
            'application/modules/Activity/Model/Helper/ItemSeaoChild.php',
            'application/modules/Activity/Model/Helper/ItemDate.php'
        ),
    ),
    // Mobile / Tablet Plugin Compatible
    'sitemobile_compatible' => true,
    //Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onStatistics',
            'resource' => 'Siteevent_Plugin_Core'
        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Siteevent_Plugin_Core',
        ),
        array(
            'event' => 'onItemDeleteBefore',
            'resource' => 'Siteevent_Plugin_Core',
        ),
        array(
            'event' => 'onActivityActionCreateAfter',
            'resource' => 'Siteevent_Plugin_Core',
        ),
        array(
            'event' => 'getActivity',
            'resource' => 'Siteevent_Plugin_Core',
        ),
        array(
            'event' => 'addActivity',
            'resource' => 'Siteevent_Plugin_Core',
        ),
        array(
            'event' => 'onSitereviewListingtypeCreateAfter',
            'resource' => 'Siteevent_Plugin_Core',
        ),
    ),
    // Compose
  'composer' => array(
    'event' => array(
      'script' => array('_composeEvent.tpl', 'siteevent'),
    ),
  ),
    //Items ---------------------------------------------------------------------
    'items' => array(
        'siteevent_clasfvideo',
        'siteevent_event',
        'siteevent_album',
        'siteevent_photo',
        'siteevent_review',
        'siteevent_userreview',
        'siteevent_topic',
        'siteevent_post',
        'siteevent_category',
        'siteevent_profilemap',
        'siteevent_ratingparam',
        'siteevent_diary',
        'siteevent_badge',
        'siteevent_editor',
        'siteevent_priceinfo',
        'siteevent_wheretobuy',
        'siteevent_video',
        'siteevent_announcement',
        'siteevent_list',
        'siteevent_list_item',
        'siteevent_occurrence',
        'siteevent_organizer',
        'siteevent_waitlist',
    ),
    //Route--------------------------------------------------------------------
    'routes' => $routes,
);
