<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventinvite
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStart = "eventinvites";
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
    $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventinvite.manifestUrl', "event-invites");
}
return array(
    // Package -------------------------------------------------------------------
    'package' => array(
        'type' => 'module',
        'name' => 'siteeventinvite',
        'version' => '4.8.10',
        'path' => 'application/modules/Siteeventinvite',
        'repository' => 'null',
        'title' => 'Advanced Events - Inviter and Promotion Extension',
        'description' => 'Advanced Events - Inviter and Promotion Extension',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thrusday, 2014-01-02 00:00:00Z',
        'copyright' => 'Copyright 2010-2011 BigStep Technologies Pvt. Ltd.',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Siteeventinvite/settings/install.php',
            'class' => 'Siteeventinvite_Installer',
        ),
        'directories' => array(
            'application/modules/Siteeventinvite',
        ),
        'files' => array(
            'application/languages/en/siteeventinvite.csv',
        ),
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'siteeventinvite_invite' => array(
            'route' => $routeStart . '/invitefriends/:user_id/:siteevent_id/:occurrence_id',
            'defaults' => array(
                'module' => 'siteeventinvite',
                'controller' => 'index',
                'action' => 'friendseventinvite',
                'occurrence_id' => ''
            ),
            'reqs' => array(
                'user_id' => '\d+',
                'siteevent_id' => '\d+'
            )
        ),
        'siteeventinvite_inviteusers' => array(
            'route' => $routeStart . '/inviteusers/:user_id/:siteevent_id/:occurrence_id/',
            'defaults' => array(
                'module' => 'siteeventinvite',
                'controller' => 'index',
                'action' => 'inviteusers',
                'occurrence_id' => ''
            ),
            'reqs' => array(
                'user_id' => '\d+',
                'siteevent_id' => '\d+'
            )
        ),
        'siteeventinvite_invitefriends' => array(
            'route' => $routeStart . '/friends/:action/:siteevent_id/:occurrence_id/',
            'defaults' => array(
                'module' => 'siteeventinvite',
                'controller' => 'index',
                'action' => 'invite-friends',
                'occurrence_id' => ''
            ),
            'reqs' => array(
                'siteevent_id' => '\d+',
                'action' => '(invite-friends|sendinvite)',
            )
        ),
        'siteeventinvite_app_config' => array(
            'route' => 'admin/siteeventinvite/global/appconfigs',
            'defaults' => array(
                'module' => 'siteeventinvite',
                'controller' => 'admin-global',
                'action' => 'appconfigs'
            )
        ),
        'siteeventinvite_global_global' => array(
            'route' => 'admin/siteeventinvite/global/global',
            'defaults' => array(
                'module' => 'siteeventinvite',
                'controller' => 'admin-global',
                'action' => 'global'
            )
        )
    )
);
?>