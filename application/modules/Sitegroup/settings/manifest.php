<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStartP = "groupitems";
$routeStartS = "groupitem";
$module=null;$controller=null;$action=null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {
  $routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manifestUrlP', "groupitems");
  $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manifestUrlS', "groupitem");
}
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitegroup',
        'version' => '4.8.12p3',
        'path' => 'application/modules/Sitegroup',
        'title' => 'Groups / Communities',
        'description' => 'Groups / Communities',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thursday, 05 May 2011 18:33:08 +0000',
        'copyright' => 'Copyright 2010-2011 BigStep Technologies Pvt. Ltd.',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sitegroup/settings/install.php',
            'class' => 'Sitegroup_Installer',
        ),
        'directories' => array(
            'application/modules/Sitegroup',
            'application/modules/Sitegroupalbum',
            'application/modules/Sitegroupmember',
        ),
        'files' => array(
            'application/languages/en/sitegroup.csv',
            'application/languages/en/sitegroupalbum.csv',
            'application/languages/en/sitegroupmember.csv',
        ),
    ),
    'sitemobile_compatible' => true,
// Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            // 'event' => 'addActivity',
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Sitegroup_Plugin_Core'
        ),
        array(
            'event' => 'onStatistics',
            'resource' => 'Sitegroup_Plugin_Core'
        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Sitegroup_Plugin_Core',
        ),
        array(
            'event' => 'getActivity',
            'resource' => 'Sitegroup_Plugin_Core',
        ),
        array(
            'event' => 'addActivity',
            'resource' => 'Sitegroup_Plugin_Core',
        ),
        array(
					'event' => 'onActivityActionCreateAfter',
					'resource' => 'Sitegroup_Plugin_Core',
			  ),
// 			  array(
// 					'event' => 'onActivityCommentCreateAfter',
// 					'resource' => 'Sitegroup_Plugin_Core',
// 			  ),
    ),
// Items ---------------------------------------------------------------------
    'items' => array(
        'sitegroup_group',
        'sitegroup_album',
        'sitegroup_photo','sitegroup_category',
        'sitegroup_topic',
        'sitegroup_post',
        'sitegroup_profilemap',
        'sitegroup_claim',
        'sitegroup_package',
        'sitegroup_gateway', 
        'sitegroup_location',
        'sitegroup_listmemberclaims',
        'sitegroup_gateway',
        'sitegroup_itemofthedays',
        'sitegroup_transaction',
				'sitegroup_import',
				'sitegroup_membership',
				'sitegroup_announcements','sitegroup_announcements','sitegroup_list',
			  'sitegroup_membership',
				'sitegroup_importfile'
    ),
// Route--------------------------------------------------------------------
    'routes' => array(
        'sitegroup_extended' => array(
            'route' => $routeStartP.'/:controller/:action/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'index',
                'action' => 'home',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
        'sitegroup_general' => array(
            'route' => $routeStartP.'/:action/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'index',
                'action' => 'home',
            ),
            'reqs' => array(
                'action' => '(home|index|manage|create|subcategory|groupurlvalidation|get-search-groups|subsubcategory|map|pinboard-browse)',
            ),
        ),

				'sitegroup_claimgroups' => array(
				            'route' => $routeStartP.'/claim/:action/*',
				            'defaults' => array(
				                'module' => 'sitegroup',
				                'controller' => 'claim',
				                'action' => 'index',
				            ),
				            'reqs' => array(
				                'action' => '(claim-group|get-groups|terms|my-groups|delete)',

				            ),
				        ),
			'sitegroup_dashboard' => array(
			            'route' => $routeStartP.'/dashboard/:action/*',
			            'defaults' => array(
			                'module' => 'sitegroup',
			                'controller' => 'dashboard',
			                //'action' => 'index',
			            ),
			            'reqs' => array(
			                'action' => '(get-started|edit-style|edit-location|overview|edit-address|profile-type|marketing|foursquare-help|contact|featured-owners|profile-picture|remove-photo|unhide-photo|app|foursquare|favourite|favourite-delete|upload-photo|wishlist|twitter|announcements|notification-settings|add-location|delete-location|all-location|manage-member-category|delete-member-category|reset-position-cover-photo|upload-cover-photo|get-albums-photos|remove-cover-photo)',
			            ),
			        ),
			'sitegroup_profilegroup' => array(
			            'route' => $routeStartP.'/profilegroup/:action/*',
			            'defaults' => array(
			                'module' => 'sitegroup',
			                'controller' => 'profile',
			                //'action' => 'index',
			            ),
			            'reqs' => array(
			                'action' => '(message-owner|tell-a-friend|print|contact-detail|get-cover-photo|email-me)',	
			            ),
			        ),
			'sitegroup_like' => array(
          'route' => $routeStartP.'/like/:action/*',
          'defaults' => array(
              'module' => 'sitegroup',
              'controller' => 'like',
              //'action' => 'index',
          ),
          'reqs' => array(
              'action' => '(global-likes|like-groups|send-update|mylikes|my-joined)',
          ),
      ),

			'sitegroup_packages' => array(
          'route' => $routeStartP.'/package/:action/*',
          'defaults' => array(
              'module' => 'sitegroup',
              'controller' => 'package',
              'action' => 'index',
          ),
          'reqs' => array(
              'action' => '(detail|update-package|update-confirmation|cancel)',
          ),
      ),

			'sitegroup_manageadmins' => array(
          'route' => $routeStartP.'/manageadmin/:action/*',
          'defaults' => array(
              'module' => 'sitegroup',
              'controller' => 'manageadmin',
              'action' => 'index',
          ),
          'reqs' => array(
              'action' => '(my-groups|manage-auto-suggest|list|delete)',
          ),
      ),


        // Public
        'sitegroup_entry_view' => array(
            'route' => $routeStartS.'/:group_url/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'index',
                'action' => 'view',
            ),
        ),
        // User

        'sitegroup_delete' => array(
            'route' => $routeStartP.'/delete/:group_id/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'index',
                'action' => 'delete'
            ),
            'reqs' => array(
                'group_id' => '\d+',
            )
        ),
        'sitegroup_publish' => array(
            'route' => $routeStartP.'/publish/:group_id/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'index',
                'action' => 'publish'
            ),
            'reqs' => array(
                'group_id' => '\d+'
            )
        ),
        'sitegroup_close' => array(
            'route' => $routeStartP.'/close/:group_id/:closed/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'index',
                'action' => 'close'
            )
        ),
        'sitegroup_edit' => array(
            'route' => $routeStartP.'/edit/:group_id/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'index',
                'action' => 'edit',
                'group_id' => '0',
            )
        ),
        'sitegroup_session_payment' => array(
            'route' => $routeStartP.'/payment/sessionpayment/',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'package',
                'action' => 'payment',
            ),
        ),
        'sitegroup_payment' => array(
            'route' => $routeStartP.'/payment/',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'payment',
                'action' => 'index',
            ),
        ),
        'sitegroup_process_payment' => array(
            'route' => $routeStartP.'/payment/process',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'payment',
                'action' => 'process',
            ),
        ),
        'sitegroup_insights' => array(
            'route' => $routeStartP.'/insights/:action/:group_id/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'insights',
                'action' => 'index',
                'group_id' => '0',
            )
        ),
        'sitegroup_tags' => array(
            'route' => $routeStartP.'/tagscloud/:group/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'index',
                'action' => 'tags-cloud',
                'group' => 1
            )
        ),
        'sitegroup_photoalbumupload' => array(
            'route' => $routeStartP.'/photo/:group_id/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'photo',
                'action' => 'upload-album',
                'group_id' => '1'
            )
        ),
        'sitegroup_imagephoto_specific' => array(
            'route' => $routeStartP.'/photo/:action/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'photo',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(view|photo-edit|remove|make-group-profile-photo)',
            ),
        ),
        'sitegroup_albumphoto_general' => array(
            'route' => $routeStartP.'/album/:action/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'album',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(edit|delete|edit-photos|view-album|view|album-order)',
            ),
        ),
        'sitegroup_general_category' => array(
            'route' => $routeStartP.'/:category_id/:categoryname/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+'
            ),
        ),
        'sitegroup_general_subcategory' => array(
            'route' => $routeStartP.'/:category_id/:categoryname/:subcategory_id/:subcategoryname/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+',
                'subcategory_id' => '\d+'

            ),
        ),

        'sitegroup_general_subsubcategory' => array(
            'route' => $routeStartP.'/:category_id/:categoryname/:subcategory_id/:subcategoryname/:subsubcategory_id/:subsubcategoryname/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+',
                'subcategory_id' => '\d+'

            ),
         ),

        'sitegroup_layout' => array(
            'route' => $routeStartP.'/layout/:group_id/',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'layout',
                'action' => 'layout',
            ),
            'reqs' => array(
                'group_id' => '\d+',
            )
        ),
        'sitegroup_ajaxhomelist' => array(
            'route' => $routeStartP.'/ajaxhomelist/',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'index',
                'action' => 'ajax-home-list'
            )
        ),
        'sitegroup_reports' => array(
            'route' => $routeStartP.'/insights/:action/:group_id/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'insights',
                'action' => 'export-report',
                'group_id' => '0',
            )
        ),
        'sitegroup_webgroupreport' => array(
            'route' => $routeStartP.'/insights/:action/:group_id/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'insights',
                'action' => 'export-webgroup',
                'group_id' => '0',
            )
        ),
        'sitegroup_homesponsored' => array(
            'route' => $routeStartP.'/homesponsored',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'index',
                'action' => 'home-sponsored'
            )
        ),
        // User
        'sitegroup_widget' => array(
            'route' => 'admin/groupitem/widgets/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'admin-widgets',
                'action' => 'index',
            )
        ),
        // User
        'sitegroup_itemofday' => array(
            'route' => 'admin/groupitem/items/day/:group/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'admin-items',
                'action' => 'day',
                'group' => 1
            )
        ),
				'sitegroup_profilegroupmobile' => array(
            'route' => $routeStartS . '/profile/:action/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'profile',
            ),
            'reqs' => array(
                'action' => '(upload-cover-photo|get-albums-photos|remove-cover-photo)'
            ),
        ),
        'sitegroup_viewmap' => array(
            'route' => $routeStartS.'/index/view-map/:id',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'index',
                'action' => 'view-map'
            )
        ),
    ),
);
?>
