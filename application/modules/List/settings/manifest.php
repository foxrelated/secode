<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: manifest.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/

$routeStartP = "listingitems";
$routeStartS = "listingitem";
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
  $routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.manifestUrlP', "listingitems");
  $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.manifestUrlS', "listingitem");
}

return array(
	'package' =>
    array(
        'type' => 'module',
        'name' => 'list',
        'version' => '4.8.8',
        'path' => 'application/modules/List',
        'title' => 'Listing',
        'description' => 'Listing / Catalog Showcase',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/List/settings/install.php',
            'class' => 'List_Installer',
        ),
        'directories' => array(
            'application/modules/List',
        ),
        'files' => array(
            'application/languages/en/list.csv',
        ),
    ),
		//Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'List_Plugin_Core'
        ),
        array(
            'event' => 'onStatistics',
            'resource' => 'List_Plugin_Core'
        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'List_Plugin_Core',
        ),
        array(
            'event' => 'onItemDeleteBefore',
            'resource' => 'List_Plugin_Core',
        ),
    ),
		//Items ---------------------------------------------------------------------
    'items' => array(
        'list_clasfvideo',
        'list_listing',
        'list_album',
        'list_photo',
        'list_reviews',
        'list_vieweds',
        'list_review',
        'list_itemofthedays',
        'list_topic',
        'list_post',
				'list_import',
				'list_importfile',
				'list_category',
				'list_profilemap'
    ),
		//Route--------------------------------------------------------------------
    'routes' => array(
        'list_extended' => array(
            'route' => $routeStartP . '/:controller/:action/*',
            'defaults' => array(
                'module' => 'list',
                'controller' => 'index',
                'action' => 'home',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),

        'list_general' => array(
            'route' => $routeStartP . '/:action/*',
            'defaults' => array(
                'module' => 'list',
                'controller' => 'index',
                'action' => 'home',
            ),
            'reqs' => array(
                'action' => '(home|index|manage|create|ajaxhomelist|tagscloud|get-search-listings|sub-category|subsub-category|upload-photo|map)',
            ),
        ),

				'list_specific' => array(
					'route' => $routeStartS . '/:action/:listing_id/*',
					'defaults' => array(
							'module' => 'list',
							'controller' => 'index',
						  'action' => 'view',
					),
					'reqs' => array(
						'action' => '(messageowner|tellafriend|print|delete|publish|close|edit|change-photo|remove-photo|overview|editstyle|editlocation|editaddress)',
						'listing_id' => '\d+',
					)
				),

        'list_entry_view' => array(
            'route' => $routeStartP . '/:user_id/:listing_id/:slug/*',
            'defaults' => array(
                'module' => 'list',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+',
                'listing_id' => '\d+'
            )
        ),

        'list_image_specific' => array(
            'route' => $routeStartS . '/photo/view/*',
            'defaults' => array(
                'module' => 'list',
                'controller' => 'photo',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(view|remove)',
            ),
        ),

        'list_photo_extended' => array(
            'route' => $routeStartS . '/photo/:action/*',
            'defaults' => array(
                'module' => 'list',
                'controller' => 'photo',
                'action' => 'edit',
            ),
            'reqs' => array(
                'action' => '\D+',
            )
        ),

        'list_photoalbumupload' => array(
            'route' => $routeStartP . '/photo/:listing_id/*',
            'defaults' => array(
                'module' => 'list',
                'controller' => 'photo',
                'action' => 'upload',
                'listing_id' => '0',
            )
        ),

        'list_albumspecific' => array(
            'route' => $routeStartP . '/album/:action/:listing_id/*',
            'defaults' => array(
                'module' => 'list',
                'controller' => 'album',
                'action' => 'editphotos'
            ),
            'reqs' => array(
                'action' => '(compose-upload|delete|edit|editphotos|upload|view)',
            ),
        ),

        'list_videospecific' => array(
            'route' => $routeStartP . '/videoedit/:action/:listing_id/*',
            'defaults' => array(
                'module' => 'list',
                'controller' => 'videoedit',
                'action' => 'edit'
            ),
            'reqs' => array(
                'action' => '(compose-upload|delete|edit|editphotos|upload|view)',
            ),
        ),

        'list_video_upload' => array(
            'route' => $routeStartS . '/video/:action/:listing_id/*',
            'defaults' => array(
                'module' => 'list',
                'controller' => 'video',
                'action' => 'index',
                'listing_id' => '0',
            ),
//             'reqs' => array(
//                 'action' => '\+D',
//             ),
        ),

        'list_general_category' => array(
            'route' => $routeStartP . '/:category/:categoryname',
            'defaults' => array(
                'module' => 'list',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category' => '\d+',
            ),
        ),

        'list_general_subcategory' => array(
            'route' => $routeStartP . '/:category/:categoryname/:subcategory/:subcategoryname',
            'defaults' => array(
                'module' => 'list',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category' => '\d+',
                'subcategory' => '\d+',
            ),
        ),

        'list_general_subsubcategory' => array(
            'route' => $routeStartP . '/:category/:categoryname/:subcategory/:subcategoryname/:subsubcategory/:subsubcategoryname',
            'defaults' => array(
                'module' => 'list',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category' => '\d+',
                'subcategory' => '\d+',
								'subsubcategory' => '\d+',
            ),
        ),
    ),
);