<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WidgetSettings.php 6590 2010-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
$check_name = $check_table->info('name');
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroupalbum_admin_main_manage');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroupalbum_admin_main_manage';
    $menu_item->module = 'sitegroupalbum';
    $menu_item->label = 'Manage Group Albums';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroupalbum","controller":"manage","action":"index"}';
    $menu_item->menu = 'sitegroupalbum_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 2;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroupalbum_admin_main_level');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroupalbum_admin_main_level';
    $menu_item->module = 'sitegroupalbum';
    $menu_item->label = 'Member Level Settings';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroupalbum","controller":"level"}';
    $menu_item->menu = 'sitegroupalbum_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 3;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_main_claim');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_main_claim';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Claim a Group';
    $menu_item->plugin = 'Sitegroup_Plugin_Menus::canViewClaims';
    $menu_item->params = '{"route":"sitegroup_claimgroups"}';
    $menu_item->menu = 'sitegroup_main';
    $menu_item->submenu = '';
    $menu_item->order = 17;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_package');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_package';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Manage Packages';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"package","action":"index"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 2;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_profilemaps');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_profilemaps';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Category-Group Profile Mapping';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"profilemaps", "action":"manage"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 6;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_claim');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_claim';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Manage Claims';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"claim"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 9;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_layoutdefault');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_layoutdefault';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Group Layout';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"defaultlayout"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 13;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_adsettings');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_adsettings';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Ad Settings';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"settings","action":"adsettings"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 14;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_transactions');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_transactions';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Transactions';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"payment"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 15;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_import');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_import';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Import';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"importlisting"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 15;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_extension');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_extension';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Extensions';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"extension","action":"upgrade"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 25;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_form_search');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_form_search';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Search Form Settings';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"settings","action":"form-search"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->enabled = 1;
    $menu_item->custom = 0;
    $menu_item->order = 14;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_main_home');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_main_home';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Groups Home';
    $menu_item->plugin = 'Sitegroup_Plugin_Menus::canViewSitegroups';
    $menu_item->params = '{"route":"sitegroup_general","action":"home"}';
    $menu_item->menu = 'sitegroup_main';
    $menu_item->submenu = '';
    $menu_item->order = 1;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_main_browse');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_main_browse';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Browse Groups';
    $menu_item->plugin = 'Sitegroup_Plugin_Menus::canViewSitegroups';
    $menu_item->params = '{"route":"sitegroup_general","action":"index"}';
    $menu_item->menu = 'sitegroup_main';
    $menu_item->submenu = '';
    $menu_item->order = 2;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_main_manage');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_main_manage';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'My Groups';
    $menu_item->plugin = 'Sitegroup_Plugin_Menus::canCreateSitegroups';
    $menu_item->params = '{"route":"sitegroup_general","action":"manage"}';
    $menu_item->menu = 'sitegroup_main';
    $menu_item->submenu = '';
    $menu_item->order = 3;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_main_create');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_main_create';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Create New Group';
    $menu_item->plugin = 'Sitegroup_Plugin_Menus::canCreateSitegroups';
    $menu_item->params = '{"route":"sitegroup_packages"}';
    $menu_item->menu = 'sitegroup_main';
    $menu_item->submenu = '';
    $menu_item->order = 4;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_quick_create');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_quick_create';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Create New Group';
    $menu_item->plugin = 'Sitegroup_Plugin_Menus::canCreateSitegroups';
    $menu_item->params = '{"route":"sitegroup_packages","class":"buttonlink icon_sitegroup_new"}';
    $menu_item->menu = 'sitegroup_quick';
    $menu_item->submenu = '';
    $menu_item->order = 1;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_level');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_level';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Member Level Settings';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"level"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 3;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_viewsitegroup');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_viewsitegroup';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Manage Groups';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"viewsitegroup"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 8;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_widget');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_widget';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Group of the Day';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"items","action":"day"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 7;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_fields');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_fields';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Profile Fields';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"fields"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 5;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_sitegroupcategories');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_sitegroupcategories';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Categories';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"settings","action":"sitegroupcategories"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 4;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_statistic');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_statistic';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Statistics';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"settings","action":"statistic"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 12;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_email');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_email';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Insights Email Settings';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"settings","action":"email"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 10;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_admin_main_graph');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_admin_main_graph';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Insights Graph Settings';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"settings","action":"graph"}';
    $menu_item->menu = 'sitegroup_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 11;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'core_main_sitegroup');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'core_main_sitegroup';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Groups';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"sitegroup_general","action":"home"}';
    $menu_item->menu = 'core_main';
    $menu_item->submenu = '';
    $menu_item->order = 4;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'core_sitemap_sitegroup');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'core_sitemap_sitegroup';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Groups';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"sitegroup_general","action":"home"}';
    $menu_item->menu = 'core_sitemap';
    $menu_item->submenu = '';
    $menu_item->order = 4;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'authorization_admin_level_sitegroup');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'authorization_admin_level_sitegroup';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Groups';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitegroup","controller":"level","action":"index"}';
    $menu_item->menu = 'authorization_admin_level';
    $menu_item->submenu = '';
    $menu_item->order = 999;
    $menu_item->save();
}





$db = Zend_Db_Table_Abstract::getDefaultAdapter();
$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`,`order`) VALUES ("sitegroup_admin_main_album", "sitegroup", "Photo Albums", "", \'{"route":"admin_default","module":"sitegroupalbum","controller":"settings","action":"index"}\', "sitegroup_admin_main", "", 10)');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`,`order`) VALUES ("sitegroup_admin_main_member", "sitegroup", "Group Members", "", \'{"route":"admin_default","module":"sitegroupmember","controller":"settings","action":"index"}\', "sitegroup_admin_main", "", 10)');

$sitegroup_layout_cover_photo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layout.cover.photo', 1);
$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroup_admin_main_activity_feed", "sitegroup", "Activity Feed", "",\'{"route":"admin_default","module":"sitegroup","controller":"settings","action":"activity-feed"}\', "sitegroup_admin_main", "", 1, 0, 998);');

$check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
$check_name = $check_table->info('name');
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_main_location');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_main_location';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Browse Locations';
    $menu_item->plugin = 'Sitegroup_Plugin_Menus::canViewSitegroups';
    $menu_item->params = '{"route":"sitegroup_general","action":"map"}';
    $menu_item->menu = 'sitegroup_main';
    $menu_item->submenu = '';
    $menu_item->order = 4;
    $menu_item->save();
}

$check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
$check_name = $check_table->info('name');
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitegroup_main_pinboardbrowse');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitegroup_main_pinboardbrowse';
    $menu_item->module = 'sitegroup';
    $menu_item->label = 'Pinboard';
    $menu_item->plugin = 'Sitegroup_Plugin_Menus::canViewGroups';
    $menu_item->params = '{"route":"sitegroup_general","action":"pinboard-browse"}';
    $menu_item->menu = 'sitegroup_main';
    $menu_item->submenu = '';
    $menu_item->order = 3;
    $menu_item->save();
}

$menuitemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
$menuitemsTableName = $menuitemsTable->info('name');

$selectmenuitems = $menuitemsTable->select()
        ->from($menuitemsTableName, array('name'))
        ->where('name =?', 'core_admin_main_plugins_sitegroupextensions')
        ->where('module =?', 'sitegroup')
        ->limit(1);
$fetchmenuitems = $selectmenuitems->query()->fetchAll();
if (empty($fetchmenuitems)) {
    $menuitems = $menuitemsTable->createRow();
    $menuitems->name = 'core_admin_main_plugins_sitegroupextensions';
    $menuitems->module = 'sitegroup';
    $menuitems->label = 'SEAO - Groups - Extensions';
    $menuitems->plugin = Null;
    $menuitems->params = '{"route":"admin_default","module":"sitegroup","controller":"extension", "action": "index"}';
    $menuitems->menu = 'core_admin_main_plugins';
    $menuitems->submenu = Null;
    $menuitems->enabled = '1';
    $menuitems->custom = '0';
    $menuitems->order = '999';
    $menuitems->save();
}
$contentTable = Engine_Api::_()->getDbtable('content', 'core');
$contentTableName = $contentTable->info('name');
$groupTable = Engine_Api::_()->getDbtable('pages', 'core');
$groupTableName = $groupTable->info('name');
$selectGroup = $groupTable->select()
        ->from($groupTableName, array('page_id'))
        ->where('name =?', 'sitegroup_index_home')
        ->limit(1);
$fetchGroupId = $selectGroup->query()->fetchAll();
if (empty($fetchGroupId)) {
    $groupCreate = $groupTable->createRow();
    $groupCreate->name = 'sitegroup_index_home';
    $groupCreate->displayname = 'Groups Home';
    $groupCreate->title = 'Groups Home';
    $groupCreate->description = 'This is the group home group.';
    $groupCreate->custom = 0;
    $groupCreate->save();
    $group_id = $groupCreate->page_id;

    // INSERT MAIN CONTAINER
    $mainContainer = $contentTable->createRow();
    $mainContainer->page_id = $group_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->content_id;

    // INSERT MAIN-MIDDLE CONTAINER
    $mainMiddleContainer = $contentTable->createRow();
    $mainMiddleContainer->page_id = $group_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->content_id;

    // INSERT MAIN-LEFT CONTAINER
    $mainLeftContainer = $contentTable->createRow();
    $mainLeftContainer->page_id = $group_id;
    $mainLeftContainer->type = 'container';
    $mainLeftContainer->name = 'left';
    $mainLeftContainer->parent_content_id = $container_id;
    $mainLeftContainer->order = 4;
    $mainLeftContainer->save();
    $left_id = $mainLeftContainer->content_id;

    // INSERT MAIN-RIGHT CONTAINER
    $mainRightContainer = $contentTable->createRow();
    $mainRightContainer->page_id = $group_id;
    $mainRightContainer->type = 'container';
    $mainRightContainer->name = 'right';
    $mainRightContainer->parent_content_id = $container_id;
    $mainRightContainer->order = 5;
    $mainRightContainer->save();
    $right_id = $mainRightContainer->content_id;

    // INSERT TOP CONTAINER
    $topContainer = $contentTable->createRow();
    $topContainer->page_id = $group_id;
    $topContainer->type = 'container';
    $topContainer->name = 'top';
    $topContainer->order = 1;
    $topContainer->save();
    $top_id = $topContainer->content_id;

    // INSERT TOP-MIDDLE CONTAINER
    $topMiddleContainer = $contentTable->createRow();
    $topMiddleContainer->page_id = $group_id;
    $topMiddleContainer->type = 'container';
    $topMiddleContainer->name = 'middle';
    $topMiddleContainer->parent_content_id = $top_id;
    $topMiddleContainer->order = 1;
    $topMiddleContainer->save();
    $top_middle_id = $topMiddleContainer->content_id;

    // INSERT "Group of the day" WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.item-sitegroup', $left_id, 1, "Group of the day", "true");

    // INSERT NAVIGATION WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.browsenevigation-sitegroup', $top_middle_id, 2, '', 'true');

    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.horizontal-searchbox-sitegroup', $top_middle_id, 3, '', 'true');

    // INSERT ZERO GROUP WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.zerogroup-sitegroup', $middle_id, 3, '', 'true');

    // INSERT "Featured Groups" WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.slideshow-sitegroup', $middle_id, 4, "Featured Groups", "true");

    // INSERT "Categories" WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.categories', $middle_id, 5, "Categories", "true");

    // INSERT RANDOM GROUPS WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.recently-popular-random-sitegroup', $middle_id, 6, '', 'true');

    // INSERT SEARCH WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.search-sitegroup', $right_id, 7, '', 'true');

    // INSERT NEW GROUP WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.newgroup-sitegroup', $right_id, 8, '', 'true');

    // INSERT "Sponsored Groups" WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.sponsored-sitegroup', $right_id, 9, "Sponsored Groups", "true");

    // INSERT TAG CLOUD WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.tagcloud-sitegroup', $right_id, 10, '', 'true');

    // INSERT "Recommended Groups" WIDGET
    $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    if (!empty($isModEnabled)) {
        Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'suggestion.common-suggestion', $right_id, 999, '', 'true', '{"title":"Recommended Group","resource_type":"sitegroup","getWidAjaxEnabled":"1","getWidLimit":"5","nomobile":"0","name":"suggestion.common-suggestion"}');
    }

    // INSERT "Most Liked Groups" WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.mostlikes-sitegroup', $left_id, 11, "Most Liked Groups", "true");

    //INSERT "Most Followed Pages" WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.mostfollowers-sitegroup', $left_id, 12, "Most Followed Groups", "true");

    // INSERT "Most Commented Groups" WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.mostcommented-sitegroup', $left_id, 13, "Most Commented Groups", "true");

    // INSERT "Recently Viewed" WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.recentview-sitegroup', $left_id, 14, "Recently Viewed", "true");

    // INSERT "Recently Viewed By Friends" WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.recentfriend-sitegroup', $left_id, 15, "Recently Viewed By Friends", "true");
}

$selectGroup = $groupTable->select()
        ->from($groupTableName, array('page_id'))
        ->where('name =?', 'sitegroup_index_index')
        ->limit(1);
$group_id = $selectGroup->query()->fetchAll();
if (empty($group_id)) {
    $groupCreate = $groupTable->createRow();
    $groupCreate->name = 'sitegroup_index_index';
    $groupCreate->displayname = 'Browse Groups';
    $groupCreate->title = 'Browse Groups';
    $groupCreate->description = 'This is the group browse group.';
    $groupCreate->custom = 0;
    $groupCreate->save();
    $group_id = $groupCreate->page_id;

    // INSERT MAIN CONTAINER
    $mainContainer = $contentTable->createRow();
    $mainContainer->page_id = $group_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->content_id;

    // INSERT MAIN - MIDDLE CONTAINER
    $mainMiddleContainer = $contentTable->createRow();
    $mainMiddleContainer->page_id = $group_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->content_id;

    // INSERT MAIN - RIGHT CONTAINER
    $mainRightContainer = $contentTable->createRow();
    $mainRightContainer->page_id = $group_id;
    $mainRightContainer->type = 'container';
    $mainRightContainer->name = 'right';
    $mainRightContainer->parent_content_id = $container_id;
    $mainRightContainer->order = 5;
    $mainRightContainer->save();
    $right_id = $mainRightContainer->content_id;

    // INSERT TOP CONTAINER
    $topContainer = $contentTable->createRow();
    $topContainer->page_id = $group_id;
    $topContainer->type = 'container';
    $topContainer->name = 'top';
    $topContainer->order = 1;
    $topContainer->save();
    $top_id = $topContainer->content_id;

    // INSERT TOP- MIDDLE CONTAINER
    $topMiddleContainer = $contentTable->createRow();
    $topMiddleContainer->page_id = $group_id;
    $topMiddleContainer->type = 'container';
    $topMiddleContainer->name = 'middle';
    $topMiddleContainer->parent_content_id = $top_id;
    $topMiddleContainer->order = 6;
    $topMiddleContainer->save();
    $top_middle_id = $topMiddleContainer->content_id;

    // INSERT NAVIGATION WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.browsenevigation-sitegroup', $top_middle_id, 1, '', 'true');

    //INSERT NAVIGATION WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.alphabeticsearch-sitegroup', $top_middle_id, 2, '', 'true');

    // INSERT GROUPS WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.groups-sitegroup', $middle_id, 2, '', 'true');

    // INSERT "Categories" WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.categories-sitegroup', $right_id, 3, "Categories", "true");

    // INSERT SEARCH GROUP WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.search-sitegroup', $right_id, 4, '', 'true');

    // INSERT NEW GROUP WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.newgroup-sitegroup', $right_id, 5, '', 'true');

    // INSERT "Popular Locations" WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.popularlocations-sitegroup', $right_id, 6, "Popular Locations", 'true');

    // INSERT TAG CLOUD WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.tagcloud-sitegroup', $right_id, 7, '', 'true');
}

include_once APPLICATION_PATH . '/application/modules/Sitegroup/controllers/AdminviewgroupwidgetController.php';
$selectGroup = $groupTable->select()
        ->from($groupTableName, array('page_id'))
        ->where('name =?', 'user_profile_index')
        ->limit(1);
$group_id = $selectGroup->query()->fetchAll();
if (!empty($group_id)) {
    $group_id = $group_id[0]['page_id'];
    $selectWidgetId = $contentTable->select()
            ->from($contentTableName, array('content_id'))
            ->where('page_id =?', $group_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'core.container-tabs')
            ->limit(1);
    $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
    if (!empty($fetchWidgetContentId)) {
        $tab_id = $fetchWidgetContentId[0]['content_id'];
        $contentWidget = $contentTable->createRow();
        $contentWidget->page_id = $group_id;
        $contentWidget->type = 'widget';
        $contentWidget->name = 'sitegroup.profile-sitegroup';
        $contentWidget->parent_content_id = $tab_id;
        $contentWidget->order = 999;
        $contentWidget->params = '{"title":"Groups","titleCount":true}';
        $contentWidget->save();
    }
}

$tableContent = Engine_Api::_()->getDbtable('admincontent', 'sitegroup');
$tableContentName = $tableContent->info('name');
$select = new Zend_Db_Select($db);
$select_group = $select
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sitegroup_index_view')
        ->limit(1);
$group = $select_group->query()->fetchAll();
if (!empty($group)) {
    $group_id = $group[0]['page_id'];
    // INSERT MAIN CONTAINER
    $mainContainer = $tableContent->createRow();
    $mainContainer->group_id = $group_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->admincontent_id;

    // INSERT MAIN-MIDDLE CONTAINER
    $mainMiddleContainer = $tableContent->createRow();
    $mainMiddleContainer->group_id = $group_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->admincontent_id;

    // INSERT MAIN-LEFT CONTAINER
    $mainLeftContainer = $tableContent->createRow();
    $mainLeftContainer->group_id = $group_id;
    $mainLeftContainer->type = 'container';
    $mainLeftContainer->name = 'right';
    $mainLeftContainer->parent_content_id = $container_id;
    $mainLeftContainer->order = 4;
    $mainLeftContainer->save();
    $left_id = $mainLeftContainer->admincontent_id;
    $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.showmore', 8);

    //INSERT MAIN-MIDDLE TAB CONTAINER
    //if(Engine_Api::_()->getApi("settings", "core")->getSetting('sitegroup.layout.setting', 1)){
    $middleTabContainer = $tableContent->createRow();
    $middleTabContainer->group_id = $group_id;
    $middleTabContainer->type = 'widget';
    $middleTabContainer->name = 'core.container-tabs';
    $middleTabContainer->parent_content_id = $middle_id;
    $middleTabContainer->order = 10;
    $middleTabContainer->params = "{\"max\":\"$showmaxtab\"}";
    $middleTabContainer->save();
    $middle_tab = $middleTabContainer->admincontent_id;
    // }      
    //INSERTING THUMB PHOTO WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.thumbphoto-sitegroup', $middle_id, 3, '', 'true');

    if (empty($sitegroup_layout_cover_photo)) {
        Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id, 1, '', 'true');
        //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroupmember.groupcover-photo-sitegroupmembers', $middle_id, 2, '', 'true');
        }



        //INSERTING TITLE WIDGET
        Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.title-sitegroup', $middle_id, 4, '', 'true');

        //INSERTING LIKE WIDGET
        Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'seaocore.like-button', $middle_id, 5, '', 'true');

        //INSERTING FOLLOW WIDGET
        Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'seaocore.seaocore-follow', $middle_id, 6, '', 'true');

        //INSERTING FACEBOOK LIKE WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'Facebookse.facebookse-commonlike', $middle_id, 7, '', 'true');
        }

        //INSERTING MAIN PHOTO WIDGET 
        Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.mainphoto-sitegroup', $left_id, 10, '', 'true');
    } else {
        Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.group-cover-information-sitegroup', $middle_id, 1, '', 'true');
    }

    //INSERTING CONTACT DETAIL WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.contactdetails-sitegroup', $middle_id, 8, '', 'true');

    //INSERTING OPTIONS WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.options-sitegroup', $left_id, 12, '', 'true');

    //INSERTING WIDGET LINK WIDGET 
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.widgetlinks-sitegroup', $left_id, 11, '', 'true');

    //INSERTING INFORMATION WIDGET 
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.information-sitegroup', $left_id, 10, 'Information', 'true');

    //INSERTING WRITE SOMETHING ABOUT WIDGET 
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

    //INSERTING RATING WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
        Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroupreview.ratings-sitegroupreviews', $left_id, 16, 'Ratings', 'true');
    }

    //INSERTING BADGE WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge')) {
        Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroupbadge.badge-sitegroupbadge', $left_id, 17, 'Badge', 'true');
    }

    //INSERTING YOU MAY ALSO LIKE WIDGET 
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.suggestedgroup-sitegroup', $left_id, 18, 'You May Also Like', 'true');

    $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitegroup.socialshare-sitegroup"}';

    //INSERTING SOCIAL SHARE WIDGET 
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.socialshare-sitegroup', $left_id, 19, 'Social Share', 'true', $social_share_default_code);

//  //INSERTING FOUR SQUARE WIDGET 
//  Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.foursquare-sitegroup', $left_id, 20, '', 'true');
    //INSERTING INSIGHTS WIDGET 
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.insights-sitegroup', $left_id, 21, 'Insights', 'true');

    //INSERTING FEATURED OWNER WIDGET 
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.featuredowner-sitegroup', $left_id, 22, 'Owners', 'true');

    //INSERTING ALBUM WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
        Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.albums-sitegroup', $left_id, 23, 'Albums', 'true');
    }

    //INSERTING GROUP PROFILE PLAYER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
        Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroupmusic.profile-player', $left_id, 24, '', 'true');
    }

    //INSERTING 'Linked Groups' WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.favourite-group', $left_id, 25, 'Linked Groups', '', 'true');

    //INSERTING ACTIVITY FEED WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
        $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
        Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 2, 'Updates', 'true', $advanced_activity_params);
    } else {
        Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'seaocore.feed', $middle_tab, 2, 'Updates', 'true');
    }

    //INSERTING INFORAMTION WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.info-sitegroup', $middle_tab, 3, 'Info', 'true');

    //INSERTING OVERVIEW WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.overview-sitegroup', $middle_tab, 4, 'Overview', 'true');

    //INSERTING LOCATION WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.location-sitegroup', $middle_tab, 5, 'Map', 'true');

    //INSERTING LINKS WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($tableContent, $tableContentName, $group_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');

    // Work for advancedactivity feed plugin(feed widget place by default).
    $aafModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled(
            'advancedactivity');
    //Quary for update widget of seaocore activity feed.
    if (!empty($aafModuleEnabled)) {
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_content')->where('name = ?', 'seaocore.feed');
        $results = $select->query()->fetchAll();
        if (!empty($results)) {
            foreach ($results as $result) {
                $params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
                $db->query('UPDATE  `engine4_core_content` SET  `name` =  "advancedactivity.home-feeds", `params`=\'' . $params . '\' WHERE `engine4_core_content`.`name` ="seaocore.feed";');
            }
        }
    }
}


include APPLICATION_PATH . '/application/modules/Sitegroupmember/settings/widgetSettings.php';
include APPLICATION_PATH . '/application/modules/Sitegroupalbum/settings/widgetSettings.php';

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'siteevent')
        ->where('enabled = ?', 1);
$is_siteevent_object = $select->query()->fetchObject();
if ($is_siteevent_object) {

    $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES("siteevent_group_host", "siteevent", \'{item:$subject} has made your group {var:$group} host of the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.\', "");');

    $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES("SITEEVENT_GROUP_HOST", "siteevent", "[host],[email],[sender],[event_title_with_link],[event_url],[group_title_with_link]");');

    $db->query("INSERT IGNORE INTO `engine4_siteevent_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`) VALUES ('sitegroup_group', 'group_id', 'sitegroup', '0', '0', 'Group Events')");

    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitegroup_admin_main_manage", "siteevent", "Manage Events", "", \'{"uri":"admin/siteevent/manage/index/contentType/sitegroup_group/contentModule/sitegroup"}\', "sitegroup_admin_main", "", 1, 0, 24);');
    $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "siteevent.event.leader.owner.sitegroup.group", "0");');
}

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitevideointegration')
        ->where('enabled = ?', 1);
$is_sitevideointegration_object = $select->query()->fetchObject();
if ($is_sitevideointegration_object) {
    $db->query("INSERT IGNORE INTO `engine4_sitevideo_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitegroup_group', 'group_id', 'sitegroup', '0', '0', 'Group Videos', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitegroup_admin_main_managevideo", "sitevideointegration", "Manage Videos", "", \'{"uri":"admin/sitevideo/manage-video/index/contentType/sitegroup_group/contentModule/sitegroup"}\', "sitegroup_admin_main", "", 0, 0, 24);');
    $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitevideo.video.leader.owner.sitegroup.group", "1");');
}
$this->makeWidgitizeManageGroup('sitegroup_index_manage', 'Groups / Communities - Manage Groups', 'My Groups', 'This group lists a user\'s Groups\'s.');

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', "sitegroup_index_manage")
        ->limit(1);
$info = $select->query()->fetch();

if (empty($info)) {

    $db->insert('engine4_core_pages', array(
        'name' => "sitegroup_index_manage",
        'displayname' => "Groups / Communities - Manage Groups",
        'title' => "My Groups",
        'description' => 'This group lists a user\'s Groups\'s.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    // Insert main
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
    ));
    $top_id = $db->lastInsertId();

    // Insert top-middle
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
        'order' => 6,
    ));
    $top_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitegroup.browsenevigation-sitegroup',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 3,
    ));

    // Insert main
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
    ));
    $main_id = $db->lastInsertId();

    // Insert main-middle
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 6,
    ));
    $main_middle_id = $db->lastInsertId();

    // Insert main-middle
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 5,
    ));
    $right_id = $db->lastInsertId();

    // Insert content
    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitegroup.manage-sitegroup',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 5,
    ));

    // Insert content
    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitegroup.links-sitegroup',
        'page_id' => $page_id,
        'parent_content_id' => $right_id,
        'order' => 1,
    ));

    // Insert content
    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitegroup.manage-search-sitegroup',
        'page_id' => $page_id,
        'parent_content_id' => $right_id,
        'order' => 2,
    ));

    // Insert content
    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitegroup.newgroup-sitegroup',
        'page_id' => $page_id,
        'parent_content_id' => $right_id,
        'order' => 3,
    ));
    //  }
}

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'documentintegration')
        ->where('enabled = ?', 1);
$is_documentintegration_object = $select->query()->fetchObject();
if ($is_documentintegration_object) {
    $db->query("INSERT IGNORE INTO `engine4_document_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`) VALUES ('sitegroup_group', 'group_id', 'sitegroup', '0', '0', 'Group Documents')");
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitegroup_admin_main_managedocument", "documentintegration", "Manage Documents", "", \'{"uri":"admin/document/manage-document/index/contentType/sitegroup_group/contentModule/sitegroup"}\', "sitegroup_admin_main", "", 0, 0, 25);');
    $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "document.leader.owner.sitegroup.group", "1");');
}
?>