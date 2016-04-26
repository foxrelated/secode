<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStart = "groupalbums";
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
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.manifestUrl', "group-albums");
}
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitegroupalbum',
        'version' => '-',
        'path' => 'application/modules/Sitegroupalbum',
        'title' => '<i><span style="color:#999999">Groups / Communities - Photo Albums Extension</span></i>',
        'description' => '<i><span style="color:#999999">Groups / Communities - Photo Albums Extension</span></i>',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thrusday, 05 May 2011 18:33:08 +0000',
        'copyright' => 'Copyright 2010-2011 BigStep Technologies Pvt. Ltd.',
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'callback' =>
        array(
            'path' => 'application/modules/Sitegroupalbum/settings/install.php',
            'class' => 'Sitegroupalbum_Installer',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Sitegroupalbum',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitegroupalbum.csv',
        ),
    ),
    // Compose -------------------------------------------------------------------
    'composer' => array(
        'sitegroupphoto' => array(
            'script' => array('_composeSitegroupPhoto.tpl', 'sitegroupalbum'),
            'plugin' => 'Sitegroupalbum_Plugin_Composer',
            'auth' => array('sitegroup_group', 'spcreate'),
        ),
    ),
    'sitemobile_compatible' => true,
    'routes' => array(
        'sitegroupalbumadmin_delete' => array(
            'route' => $routeStart.'/admin/delete/:id/*',
            'defaults' => array(
                'module' => 'sitegroupalbum',
                'controller' => 'admin-manage',
                'action' => 'delete'
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),

        'sitegroupalbum_featuredalbum' => array(
            'route' => $routeStart.'/admin/featured/:id/*',
            'defaults' => array(
                'module' => 'sitegroupalbum',
                'controller' => 'admin-manage',
                'action' => 'featured'
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),

        'sitegroupalbum_extended' => array(
            'route' => $routeStart.'/:controller/:action/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'photo',
                'action' => 'index'
            ),
        ),

         'sitegroupalbum_browse' => array(
            'route' => $routeStart.'/browse/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'album',
                'action' => 'browse',
            ),
        ),

        'sitegroupalbum_home' => array(
           'route' => $routeStart.'/home/*',
            'defaults' => array(
                'module' => 'sitegroup',
                'controller' => 'album',
                'action' => 'home',
            ),
        ),
    ),

);
?>