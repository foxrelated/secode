<?php

$db = Engine_Db_Table::getDefaultAdapter();
$menuTable = Engine_Api::_()->getDbTable('menuItems', 'core');


$isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteandroidapp_admin_app_create\' LIMIT 1')->fetch();
if (empty($isRowExist)) {
    $row = $menuTable->createRow();
    $row->name = 'siteandroidapp_admin_app_create';
    $row->module = 'siteandroidapp';
    $row->label = 'Android App Setup';
    $row->plugin = '';
    $row->params = '{"route":"admin_default","module":"siteandroidapp","controller":"app-builder"}';
    $row->menu = 'siteandroidapp_admin_main';
    $row->submenu = '';
    $row->order = 10;
    $row->save();
}

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("siteandroidapp_admin_api_menus", "siteandroidapp", "App Dashboard Menus", NULL, \'{"route":"admin_default","module":"siteandroidapp","controller":"menus", "action":"manage"}\', "siteandroidapp_admin_main", NULL, 1, 0, 5);');

$isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteandroidapp_admin_notifications\' LIMIT 1')->fetch();
if(empty($isRowExist)) {
    $row = $menuTable->createRow();
    $row->name = 'siteandroidapp_admin_notifications';
    $row->module = 'siteandroidapp';
    $row->label = 'Push Notification Settings';
    $row->plugin = '';
    $row->params = '{"route":"admin_default","module":"siteandroidapp","controller":"push-notification"}';
    $row->menu = 'siteandroidapp_admin_main';
    $row->submenu = '';
    $row->order = 15;
    $row->save();
}

$isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteandroidapp_admin_send_notifications\' LIMIT 1')->fetch();
if(empty($isRowExist)) {
    $row = $menuTable->createRow();
    $row->name = 'siteandroidapp_admin_send_notifications';
    $row->module = 'siteandroidapp';
    $row->label = 'Send Push Notifications';
    $row->plugin = '';
    $row->params = '{"route":"admin_default","module":"siteandroidapp","controller":"push-notification","action":"send"}';
    $row->menu = 'siteandroidapp_admin_main';
    $row->submenu = '';
    $row->order = 20;
    $row->save();
}

$isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteandroidapp_admin_api_management\' LIMIT 1')->fetch();
if(empty($isRowExist)) {
    $row = $menuTable->createRow();
    $row->name = 'siteandroidapp_admin_api_management';
    $row->module = 'siteandroidapp';
    $row->label = 'API Management';
    $row->plugin = '';
    $row->params = '{"route":"admin_default","module":"siteapi","controller":"settings"}';
    $row->menu = 'siteandroidapp_admin_main';
    $row->submenu = '';
    $row->order = 25;
    $row->save();
}

$isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteapi_admin_api_toAndroid\' LIMIT 1')->fetch();
if (empty($isRowExist)) {
    $row = $menuTable->createRow();
    $row->name = 'siteapi_admin_api_toAndroid';
    $row->module = 'siteandroidapp';
    $row->label = 'Android Mobile Application';
    $row->plugin = '';
    $row->params = '{"route":"admin_default","module":"siteandroidapp","controller":"settings"}';
    $row->menu = 'siteapi_admin_main';
    $row->submenu = '';
    $row->order = 50;
    $row->save();
}