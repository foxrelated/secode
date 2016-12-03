<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
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
$routeStart = "event-coupons";
$slug_plural = 'event-items';
if (!empty($request)) {
    $module = $request->getModuleName();
    $action = $request->getActionName();
    $controller = $request->getControllerName();
}

if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {

    $db = Engine_Db_Table::getDefaultAdapter();
    $slug_plural = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.slugplural', 'event-items');
    $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.couponmanifesturl', "event-coupons");
}


return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'siteeventticket',
        'version' => '4.8.10p2',
        'path' => 'application/modules/Siteeventticket',
        'title' => 'Advanced Events - Events Booking, Tickets Selling & Paid Events Extension',
        'description' => 'Advanced Events - Events Booking, Tickets Selling & Paid Events Extension',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' =>
        array(
            'path' => 'application/modules/Siteeventticket/settings/install.php',
            'class' => 'Siteeventticket_Installer',
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
            'application/modules/Siteeventticket',
            'application/modules/Siteeventpaid',
        ),
        'files' =>
        array(
            'application/languages/en/siteeventticket.csv',
            'application/languages/en/siteeventpaid.csv',
        ),
    ),
//Items
    'items' => array(
        'siteeventticket_ticket',
        'siteeventticket_order',
        'siteeventticket_usergateway',
        'siteeventticket_sellergateway',
        'siteeventticket_gateway',
        'siteeventticket_paymentrequest',
        'siteeventticket_eventpaypalbill',
        'siteeventticket_eventbill',
        'siteeventticket_transaction',
        'siteeventticket_paymentreq',
        'siteeventticket_coupon',
        'siteeventticket_couponphoto',
        'siteeventticket_claim',
        'siteeventticket_couponalbum'
    ),
    'routes' => array(
        'siteeventticket_ticket' => array(
            'route' => $slug_plural . '/ticket/:action/*',
            'defaults' => array(
                'module' => 'siteeventticket',
                'controller' => 'ticket',
                'action' => 'manage'
            ),
            'reqs' => array(
                'action' => '(manage|add|detail|edit|delete|buy|terms-of-use)',
            ),
        ),
        'siteeventticket_order' => array(
            'route' => $slug_plural . '/order/:action/:event_id/*',
            'defaults' => array(
                'module' => 'siteeventticket',
                'controller' => 'order',
                'action' => 'view',
                'event_id' => '',
            ),
            'reqs' => array(
                'action' => '(print-ticket|buyer-details|checkout|view|print-invoice|my-tickets|payment|success|detail|manage|transaction|event-transaction|payment-approve|order-cancel|payment-info|payment-to-me|payment-request|set-event-gateway-info|your-bill|bill-details|bill-process|view-transaction-detail|send-email)',
            ),
        ),
        'siteeventticket_entry_view' => array(
            'route' => $slug_plural . '/:ticket_id/:slug/*',
            'defaults' => array(
                'module' => 'siteeventticket',
                'controller' => 'index',
                'action' => 'view',
                'slug' => ''
            ),
            'reqs' => array(
                'ticket_id' => '\d+'
            )
        ),
        'siteeventticket_report_general' => array(
            'route' => $slug_plural . '/report/:action/:event_id/*',
            'defaults' => array(
                'module' => 'siteeventticket',
                'controller' => 'report',
                'action' => 'index',
               'event_id' => '',
            ),
            'reqs' => array(
                'action' => '(index|export-webpage|export-excel|sales-statistics)',
            ),
        ),
        'siteeventticket_coupon' => array(
            'route' => $routeStart . '/coupon/:action/:event_id/:coupon_id/*',
            'defaults' => array(
                'module' => 'siteeventticket',
                'controller' => 'coupon',
                'action' => 'index',
                'event_id' => '',
                'coupon_id' => ''
            ),
            'reqs' => array(
                'action' => '(index|manage|create|detail|edit|delete|buy|print|resend-coupon|coupon-code-validation)',
            ),
        ),
        'siteeventticketcoupon_view' => array(
            'route' => $routeStart . '/:user_id/:coupon_id/:slug/*',
            'defaults' => array(
                'module' => 'siteeventticket',
                'controller' => 'coupon',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            )
        ),
        'siteeventticket_tax_general' => array(
            'route' => $slug_plural . '/tax/:action/:event_id/*',
            'defaults' => array(
                'module' => 'siteeventticket',
                'controller' => 'tax',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index)',
            ),
        )
    )
);
