<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2015-05-11 00:00:00Z SocialEngineAddOns $
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
        'siteevent_package' => array(
            'route' => $slug_plural . '/package/:action/*',
            'defaults' => array(
                'module' => 'siteeventpaid',
                'controller' => 'package',
                'action' => 'index',
                'package' => 1,
            ),
            'reqs' => array(
                'action' => '(index|detail|update-package|update-confirmation|cancel)',
            ),
        ),
        'siteevent_all_package' => array(
            'route' => $slug_plural . '/packages/*',
            'defaults' => array(
                'module' => 'siteeventpaid',
                'controller' => 'package',
                'action' => 'index',
                'package' => 2,
            ),
        ),
        'siteevent_payment' => array(
            'route' => $slug_plural . '/payment/',
            'defaults' => array(
                'module' => 'siteeventpaid',
                'controller' => 'payment',
                'action' => 'index',
            ),
        ),
        'siteevent_process_payment' => array(
            'route' => $slug_plural . '/payment/process',
            'defaults' => array(
                'module' => 'siteeventpaid',
                'controller' => 'payment',
                'action' => 'process',
            ),
        ),
        'siteevent_session_payment' => array(
            'route' => $slug_plural . '/payment/sessionpayment/',
            'defaults' => array(
                'module' => 'siteeventpaid',
                'controller' => 'package',
                'action' => 'payment',
            ),
        ),
        'siteeventpaid_extended' => array(
            'route' => $slug_plural . '/success/:controller/:action/*',
            'defaults' => array(
                'module' => 'siteeventpaid',
                'controller' => 'payment',
                'action' => 'finish',
            ),
        ),
    );
}

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'siteeventpaid',
        'version' => '-',
        'path' => 'application/modules/Siteeventpaid',
        'title' => '<i><span style="color:#999999">Advanced Events - Paid Extension</span></i>',
        'description' => '<i><span style="color:#999999">Advanced Events - Paid Extension</span></i>',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' => array(
            'path' => 'application/modules/Siteeventpaid/settings/install.php',
        //'class' => 'Siteeventpaid_Installer',
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
            0 => 'application/modules/Siteeventpaid',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/siteeventpaid.csv',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'siteeventpaid_package',
        'siteeventpaid_transaction',
        'siteeventpaid_gateway',
    ),
    //Route--------------------------------------------------------------------
    'routes' => $routes,
);
