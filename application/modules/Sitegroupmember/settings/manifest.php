<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


$routeStart = "groupmembers";
$module = null;
$controller = null;
$action = null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupmember.manifestUrl', "group-members");
}
return array (
  'package' => array (
    'type' => 'module',
    'name' => 'sitegroupmember',
    'version' => '-',
    'path' => 'application/modules/Sitegroupmember',
    'title' => '<i><span style="color:#999999">Groups / Communities - Group Members Extension</span></i>',
    'description' => '<i><span style="color:#999999">Groups / Communities - Group Members Extension</span></i>',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
		'callback' => array(
			'path' => 'application/modules/Sitegroupmember/settings/install.php',
			'class' => 'Sitegroupmember_Installer',
    ),
    'actions' => array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => array (
      0 => 'application/modules/Sitegroupmember',
    ),
    'files' =>array (
      0 => 'application/languages/en/sitegroupmember.csv',
    ),
  ),
	// Hooks ---------------------------------------------------------------------
	'hooks' => array(
		array(
			'event' => 'onActivityActionCreateAfter',
			'resource' => 'Sitegroupmember_Plugin_Core',
		),
	),
  // Items ---------------------------------------------------------------------
  'items' => array (
    'sitegroupmember_roles'
  ),
  'sitemobile_compatible' => true, 
  // Route--------------------------------------------------------------------
	'routes' => array(
		'sitegroup_profilegroupmember' => array(
			'route' => $routeStart.'/member/:action/*',
			'defaults' => array(
					'module' => 'sitegroupmember',
					'controller' => 'member',
					//'action' => 'index',
			),
			'reqs' => array(
					'action' => '(join|leave|request|cancel|invite|reject|accept|invite-members|joined-more-groups|respond)',
			),
		),

	  'sitegroupmember_approve' => array(
			'route' => $routeStart.'/index/:action/*',
			'defaults' => array(
					'module' => 'sitegroupmember',
					'controller' => 'index',
					//'action' => 'index',
			),
			'reqs' => array(
					'action' => '(approve|remove|featured|highlighted|reject|edit|request-member|group-join|get-item|member-join|edittitle|create-announcement|delete-announcement|edit-announcement|notification-settings)',
			),
		),
		'sitegroupmember_browse' => array(
			'route' => $routeStart.'/browse/*',
			'defaults' => array(
				'module' => 'sitegroupmember',
				'controller' => 'index',
				'action' => 'browse',
			),
		),
		'sitegroupmember_home' => array(
			'route' => $routeStart.'/home/*',
			'defaults' => array(
					'module' => 'sitegroupmember',
					'controller' => 'index',
					'action' => 'home',
			),
		),
	),
);