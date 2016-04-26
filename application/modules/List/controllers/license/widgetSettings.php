<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: WidgetSettings.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
$check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
$check_name = $check_table->info('name');

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_main_home');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_main_home';
	$menu_item->module = 'list';
	$menu_item->label = 'Listings Home';
	$menu_item->plugin = 'List_Plugin_Menus::canViewLists';
	$menu_item->params = '{"route":"list_general","action":"home"}';
	$menu_item->menu = 'list_main';
	$menu_item->submenu = '';
	$menu_item->order = 1;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'mobi_browse_list');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'mobi_browse_list';
	$menu_item->module = 'list';
	$menu_item->label = 'Listings';
	$menu_item->plugin = 'List_Plugin_Menus::canViewLists';
	$menu_item->params = '{"route":"list_general","action":"home"}';
	$menu_item->menu = 'mobi_browse';
	$menu_item->submenu = '';
	$menu_item->order = 4;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_main_browse');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_main_browse';
	$menu_item->module = 'list';
	$menu_item->label = 'Browse Listings';
	$menu_item->plugin = 'List_Plugin_Menus::canViewLists';
	$menu_item->params = '{"route":"list_general","action":"index"}';
	$menu_item->menu = 'list_main';
	$menu_item->submenu = '';
	$menu_item->order = 2;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_main_location');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_main_location';
	$menu_item->module = 'list';
	$menu_item->label = 'Browse Locations';
	$menu_item->plugin = 'List_Plugin_Menus::canCreateLists';
	$menu_item->params = '{"route":"list_general","action":"map"}';
	$menu_item->menu = 'list_main';
	$menu_item->submenu = '';
	$menu_item->order = 3;
	$menu_item->save();
}


$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_main_manage');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_main_manage';
	$menu_item->module = 'list';
	$menu_item->label = 'My Listings';
	$menu_item->plugin = 'List_Plugin_Menus::canCreateLists';
	$menu_item->params = '{"route":"list_general","action":"manage"}';
	$menu_item->menu = 'list_main';
	$menu_item->submenu = '';
	$menu_item->order = 4;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_main_create');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_main_create';
	$menu_item->module = 'list';
	$menu_item->label = 'Post a New Listing';
	$menu_item->plugin = 'List_Plugin_Menus::canCreateLists';
	$menu_item->params = '{"route":"list_general","action":"create"}';
	$menu_item->menu = 'list_main';
	$menu_item->submenu = '';
	$menu_item->order = 5;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_quick_create');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_quick_create';
	$menu_item->module = 'list';
	$menu_item->label = 'Post a New Listing';
	$menu_item->plugin = 'List_Plugin_Menus::canCreateLists';
	$menu_item->params = '{"route":"list_general","action":"create","class":"buttonlink icon_list_new"}';
	$menu_item->menu = 'list_quick';
	$menu_item->submenu = '';
	$menu_item->order = 1;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_admin_main_level');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_admin_main_level';
	$menu_item->module = 'list';
	$menu_item->label = 'Member Level Settings';
	$menu_item->plugin = '';
	$menu_item->params = '{"route":"admin_default","module":"list","controller":"settings","action":"level"}';
	$menu_item->menu = 'list_admin_main';
	$menu_item->submenu = '';
	$menu_item->order = 2;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_admin_main_viewlist');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_admin_main_viewlist';
	$menu_item->module = 'list';
	$menu_item->label = 'Manage Listings';
	$menu_item->plugin = '';
	$menu_item->params = '{"route":"admin_default","module":"list","controller":"viewlist"}';
	$menu_item->menu = 'list_admin_main';
	$menu_item->submenu = '';
	$menu_item->order = 3;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_admin_main_widget');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_admin_main_widget';
	$menu_item->module = 'list';
	$menu_item->label = 'Widget Settings';
	$menu_item->plugin = '';
	$menu_item->params = '{"route":"admin_default","module":"list","controller":"settings","action":"widget-settings"}';
	$menu_item->menu = 'list_admin_main';
	$menu_item->submenu = '';
	$menu_item->order = 4;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_admin_main_fields');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_admin_main_fields';
	$menu_item->module = 'list';
	$menu_item->label = 'Profile Fields';
	$menu_item->plugin = '';
	$menu_item->params = '{"route":"admin_default","module":"list","controller":"fields"}';
	$menu_item->menu = 'list_admin_main';
	$menu_item->submenu = '';
	$menu_item->order = 7;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_admin_main_form_search');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_admin_main_form_search';
	$menu_item->module = 'list';
	$menu_item->label = 'Search Form Settings';
	$menu_item->plugin = '';
	$menu_item->params = '{"route":"admin_default","module":"list","controller":"settings","action":"form-search"}';
	$menu_item->menu = 'list_admin_main';
	$menu_item->submenu = '';
	$menu_item->order = 5;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_admin_main_profilemaps');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_admin_main_profilemaps';
	$menu_item->module = 'list';
	$menu_item->label = 'Category-Listing Profile Mapping';
	$menu_item->plugin = '';
	$menu_item->params = '{"route":"admin_default","module":"list","controller":"profilemaps","action":"manage"}';
	$menu_item->menu = 'list_admin_main';
	$menu_item->submenu = '';
	$menu_item->order = 6;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_admin_main_categories');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_admin_main_categories';
	$menu_item->module = 'list';
	$menu_item->label = 'Categories';
	$menu_item->plugin = '';
	$menu_item->params = '{"route":"admin_default","module":"list","controller":"settings","action":"categories"}';
	$menu_item->menu = 'list_admin_main';
	$menu_item->submenu = '';
	$menu_item->order = 8;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_admin_main_import');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_admin_main_import';
	$menu_item->module = 'list';
	$menu_item->label = 'Import';
	$menu_item->plugin = '';
	$menu_item->params = '{"route":"admin_default","module":"list","controller":"importlisting"}';
	$menu_item->menu = 'list_admin_main';
	$menu_item->submenu = '';
	$menu_item->order = 8;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'list_admin_main_statistic');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'list_admin_main_statistic';
	$menu_item->module = 'list';
	$menu_item->label = 'Statistics';
	$menu_item->plugin = '';
	$menu_item->params = '{"route":"admin_default","module":"list","controller":"settings","action":"statistic"}';
	$menu_item->menu = 'list_admin_main';
	$menu_item->submenu = '';
	$menu_item->order = 9;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'core_main_list');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'core_main_list';
	$menu_item->module = 'list';
	$menu_item->label = 'Listings';
	$menu_item->plugin = '';
	$menu_item->params = '{"route":"list_general","action":"home"}';
	$menu_item->menu = 'core_main';
	$menu_item->submenu = '';
	$menu_item->order = 4;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'core_sitemap_list');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'core_sitemap_list';
	$menu_item->module = 'list';
	$menu_item->label = 'Listings';
	$menu_item->plugin = '';
	$menu_item->params = '{"route":"list_general","action":"home"}';
	$menu_item->menu = 'core_sitemap';
	$menu_item->submenu = '';
	$menu_item->order = 4;
	$menu_item->save();
}

$select = $check_table->select()
				->from($check_name, array('id'))
				->where('name = ?', 'authorization_admin_level_list');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
	$menu_item = $check_table->createRow();
	$menu_item->name = 'authorization_admin_level_list';
	$menu_item->module = 'list';
	$menu_item->label = 'Listings';
	$menu_item->plugin = '';
	$menu_item->params = '{"route":"admin_default","module":"list","controller":"level","action":"index"}';
	$menu_item->menu = 'authorization_admin_level';
	$menu_item->submenu = '';
	$menu_item->order = 999;
	$menu_item->save();
}

$contentTable = Engine_Api::_()->getDbtable('content', 'core');
$contentTableName = $contentTable->info('name');
$pageTable = Engine_Api::_()->getDbtable('pages', 'core');
$pageTableName = $pageTable->info('name');


//Make a Widgitized Page (list_index_home) & widgets.

$selectPage = $pageTable->select()
				->from($pageTableName, array('page_id'))
				->where('name =?', 'list_index_home')
				->limit(1);
$fetchPageId = $selectPage->query()->fetchAll();
if (empty($fetchPageId)) {
	$pageCreate = $pageTable->createRow();
	$pageCreate->name = 'list_index_home';
	$pageCreate->displayname = 'Listings Home';
	$pageCreate->title = 'Listings Home';
	$pageCreate->description = 'This is the listing home page.';
	$pageCreate->custom = 1;
	$pageCreate->save();
	$page_id = $pageCreate->page_id;

	//Insert Main Container.
	$mainContainer = $contentTable->createRow();
	$mainContainer->page_id = $page_id;
	$mainContainer->type = 'container';
	$mainContainer->name = 'main';
	$mainContainer->order = 2;
	$mainContainer->save();
	$container_id = $mainContainer->content_id;

	//Insert Main-Middle Container.
	$mainMiddleContainer = $contentTable->createRow();
	$mainMiddleContainer->page_id = $page_id;
	$mainMiddleContainer->type = 'container';
	$mainMiddleContainer->name = 'middle';
	$mainMiddleContainer->parent_content_id = $container_id;
	$mainMiddleContainer->order = 6;
	$mainMiddleContainer->save();
	$middle_id = $mainMiddleContainer->content_id;


	//Insert Main-Left Container.
	$mainLeftContainer = $contentTable->createRow();
	$mainLeftContainer->page_id = $page_id;
	$mainLeftContainer->type = 'container';
	$mainLeftContainer->name = 'left';
	$mainLeftContainer->parent_content_id = $container_id;
	$mainLeftContainer->order = 4;
	$mainLeftContainer->save();
	$left_id = $mainLeftContainer->content_id;

	//Insert Main-Right Container.
	$mainRightContainer = $contentTable->createRow();
	$mainRightContainer->page_id = $page_id;
	$mainRightContainer->type = 'container';
	$mainRightContainer->name = 'right';
	$mainRightContainer->parent_content_id = $container_id;
	$mainRightContainer->order = 5;
	$mainRightContainer->save();
	$right_id = $mainRightContainer->content_id;

	//Insert Top Container.
	$topContainer = $contentTable->createRow();
	$topContainer->page_id = $page_id;
	$topContainer->type = 'container';
	$topContainer->name = 'top';
	$topContainer->order = 1;
	$topContainer->save();
	$top_id = $topContainer->content_id;

	//Insert Top-Middle Container.
	$topMiddleContainer = $contentTable->createRow();
	$topMiddleContainer->page_id = $page_id;
	$topMiddleContainer->type = 'container';
	$topMiddleContainer->name = 'middle';
	$topMiddleContainer->parent_content_id = $top_id;
	$topMiddleContainer->order = 1;
	$topMiddleContainer->save();
	$top_middle_id = $topMiddleContainer->content_id;

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.browsenevigation-list')
					->where('parent_content_id = ?', $top_middle_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.browsenevigation-list';
	$contentWidget->parent_content_id = $top_middle_id;
	$contentWidget->order = 1;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.zerolisiting-list')
					->where('parent_content_id = ?', $middle_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.zerolisiting-list';
	$contentWidget->parent_content_id = $middle_id;
	$contentWidget->order = 2;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.slideshow-list')
					->where('parent_content_id = ?', $middle_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.slideshow-list';
	$contentWidget->parent_content_id = $middle_id;
	$contentWidget->order = 3;
	$contentWidget->params = '{"title":"Featured Listings","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.categories')
					->where('parent_content_id = ?', $middle_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.categories';
	$contentWidget->parent_content_id = $middle_id;
	$contentWidget->order = 4;
	$contentWidget->params = '{"title":"Categories","titleCount":"true"}';
	$contentWidget->save();
	}


	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.recently-popular-random-list')
					->where('parent_content_id = ?', $middle_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.recently-popular-random-list';
	$contentWidget->parent_content_id = $middle_id;
	$contentWidget->order = 5;
	$contentWidget->params = '{"title":"","titleCount":""}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.search-list')
					->where('parent_content_id = ?', $right_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.search-list';
	$contentWidget->parent_content_id = $right_id;
	$contentWidget->order = 9;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.newlisting-list')
					->where('parent_content_id = ?', $right_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.newlisting-list';
	$contentWidget->parent_content_id = $right_id;
	$contentWidget->order = 10;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.sponsored-list')
					->where('parent_content_id = ?', $right_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.sponsored-list';
	$contentWidget->parent_content_id = $right_id;
	$contentWidget->order = 11;
	$contentWidget->params = '{"title":"Sponsored Listings","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.tagcloud-list')
					->where('parent_content_id = ?', $right_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.tagcloud-list';
	$contentWidget->parent_content_id = $right_id;
	$contentWidget->order = 12;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.mostdiscussion-list')
					->where('parent_content_id = ?', $right_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.mostdiscussion-list';
	$contentWidget->parent_content_id = $right_id;
	$contentWidget->order = 14;
	$contentWidget->params = '{"title":"Most Discussed Listings","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.mostrated-list')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.mostrated-list';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 15;
	$contentWidget->params = '{"title":"Top Rated Listings","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.mostlikes-list')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.mostlikes-list';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 16;
	$contentWidget->params = '{"title":"Most Liked Listings","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.mostcommented-list')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.mostcommented-list';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 17;
	$contentWidget->params = '{"title":"Most Commented Listings","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.item-list')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.item-list';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 18;
	$contentWidget->params = '{"title":"Listing of the day","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.recentview-list')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.recentview-list';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 19;
	$contentWidget->params = '{"title":"Recently Viewed","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.recentfriend-list')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.recentfriend-list';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 20;
	$contentWidget->params = '{"title":"Recently Viewed By Friends","titleCount":"true"}';
	$contentWidget->save();
	}
}

//Make a Widgitized Page (list_index_index) & widgets.

$selectPage = $pageTable->select()
				->from($pageTableName, array('page_id'))
				->where('name =?', 'list_index_index')
				->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (empty($page_id)) {
	$pageCreate = $pageTable->createRow();
	$pageCreate->name = 'list_index_index';
	$pageCreate->displayname = 'Browse Listings';
	$pageCreate->title = 'Browse Listings';
	$pageCreate->description = 'This is the listing browse page.';
	$pageCreate->custom = 1;
	$pageCreate->save();
	$page_id = $pageCreate->page_id;

	//Insert Main Container.
	$mainContainer = $contentTable->createRow();
	$mainContainer->page_id = $page_id;
	$mainContainer->type = 'container';
	$mainContainer->name = 'main';
	$mainContainer->order = 2;
	$mainContainer->save();
	$container_id = $mainContainer->content_id;

	//Insert Main-Middle Container.
	$mainMiddleContainer = $contentTable->createRow();
	$mainMiddleContainer->page_id = $page_id;
	$mainMiddleContainer->type = 'container';
	$mainMiddleContainer->name = 'middle';
	$mainMiddleContainer->parent_content_id = $container_id;
	$mainMiddleContainer->order = 6;
	$mainMiddleContainer->save();
	$middle_id = $mainMiddleContainer->content_id;

	//Insert Main-Right Container.
	$mainRightContainer = $contentTable->createRow();
	$mainRightContainer->page_id = $page_id;
	$mainRightContainer->type = 'container';
	$mainRightContainer->name = 'right';
	$mainRightContainer->parent_content_id = $container_id;
	$mainRightContainer->order = 5;
	$mainRightContainer->save();
	$right_id = $mainRightContainer->content_id;

	//Insert Top Container.
	$topContainer = $contentTable->createRow();
	$topContainer->page_id = $page_id;
	$topContainer->type = 'container';
	$topContainer->name = 'top';
	$topContainer->order = 1;
	$topContainer->save();
	$top_id = $topContainer->content_id;

	//Insert Top-Middle Container.
	$topMiddleContainer = $contentTable->createRow();
	$topMiddleContainer->page_id = $page_id;
	$topMiddleContainer->type = 'container';
	$topMiddleContainer->name = 'middle';
	$topMiddleContainer->parent_content_id = $top_id;
	$topMiddleContainer->order = 6;
	$topMiddleContainer->save();
	$top_middle_id = $topMiddleContainer->content_id;

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.browsenevigation-list')
					->where('parent_content_id = ?', $top_middle_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.browsenevigation-list';
	$contentWidget->parent_content_id = $top_middle_id;
	$contentWidget->order = 1;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.listings-list')
					->where('parent_content_id = ?', $middle_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.listings-list';
	$contentWidget->parent_content_id = $middle_id;
	$contentWidget->order = 2;
	$contentWidget->params = '{"title":"","titleCount":"true","statistics":["likeCount","reviewCount","commentCount","viewCount"]}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.categories-list')
					->where('parent_content_id = ?', $right_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.categories-list';
	$contentWidget->parent_content_id = $right_id;
	$contentWidget->order = 3;
	$contentWidget->params = '{"title":"Categories","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.search-list')
					->where('parent_content_id = ?', $right_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.search-list';
	$contentWidget->parent_content_id = $right_id;
	$contentWidget->order = 4;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.newlisting-list')
					->where('parent_content_id = ?', $right_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.newlisting-list';
	$contentWidget->parent_content_id = $right_id;
	$contentWidget->order = 5;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.mostrated-list')
					->where('parent_content_id = ?', $right_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.mostrated-list';
	$contentWidget->parent_content_id = $right_id;
	$contentWidget->order = 6;
	$contentWidget->params = '{"title":"Top Rated Listings","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.tagcloud-list')
					->where('parent_content_id = ?', $right_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.tagcloud-list';
	$contentWidget->parent_content_id = $right_id;
	$contentWidget->order = 7;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}
}

//Make a Widgitized Page (list_index_view) & widgets.

$selectPage = $pageTable->select()
				->from($pageTableName, array('page_id'))
				->where('name =?', 'list_index_view')
				->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (empty($page_id)) {
	$pageCreate = $pageTable->createRow();
	$pageCreate->name = 'list_index_view';
	$pageCreate->displayname = 'Listing Profile';
	$pageCreate->title = 'Listing Profile';
	$pageCreate->description = 'This is the listing view  page.';
	$pageCreate->custom = 1;
	$pageCreate->save();
	$page_id = $pageCreate->page_id;

	//Insert Main Container.
	$mainContainer = $contentTable->createRow();
	$mainContainer->page_id = $page_id;
	$mainContainer->type = 'container';
	$mainContainer->name = 'main';
	$mainContainer->order = 2;
	$mainContainer->save();
	$container_id = $mainContainer->content_id;

	//Insert Main-Middle Container.
	$mainMiddleContainer = $contentTable->createRow();
	$mainMiddleContainer->page_id = $page_id;
	$mainMiddleContainer->type = 'container';
	$mainMiddleContainer->name = 'middle';
	$mainMiddleContainer->parent_content_id = $container_id;
	$mainMiddleContainer->order = 6;
	$mainMiddleContainer->save();
	$middle_id = $mainMiddleContainer->content_id;

	//Insert Main-Left Container.
	$mainLeftContainer = $contentTable->createRow();
	$mainLeftContainer->page_id = $page_id;
	$mainLeftContainer->type = 'container';
	$mainLeftContainer->name = 'left';
	$mainLeftContainer->parent_content_id = $container_id;
	$mainLeftContainer->order = 4;
	$mainLeftContainer->save();
	$left_id = $mainLeftContainer->content_id;

	//Insert Main-Middle-Tab Container.
	$middleTabContainer = $contentTable->createRow();
	$middleTabContainer->page_id = $page_id;
	$middleTabContainer->type = 'widget';
	$middleTabContainer->name = 'core.container-tabs';
	$middleTabContainer->parent_content_id = $middle_id;
	$middleTabContainer->order = 7;
	$middleTabContainer->params = '{"max":"6"}';
	$middleTabContainer->save();
	$middle_tab = $middleTabContainer->content_id;

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.title-list')
					->where('parent_content_id = ?', $middle_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.title-list';
	$contentWidget->parent_content_id = $middle_id;
	$contentWidget->order = 1;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'seaocore.like-button')
					->where('parent_content_id = ?', $middle_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'seaocore.like-button';
	$contentWidget->parent_content_id = $middle_id;
	$contentWidget->order = 2;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.mainphoto-list')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.mainphoto-list';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 10;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.options-list')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.options-list';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 11;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.write-page')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.write-page';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 12;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.information-list')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.information-list';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 13;
	$contentWidget->params = '{"title":"Information","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'seaocore.people-like')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'seaocore.people-like';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 14;
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.ratings-list')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.ratings-list';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 15;
	$contentWidget->params = '{"title":"Rating","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.suggestedlist-list')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.suggestedlist-list';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 16;
	$contentWidget->params = '{"title":"You May Also Like","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.userlisting-list')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.userlisting-list';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 17;
	$contentWidget->params = '{"title":"","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.socialshare-list')
					->where('parent_content_id = ?', $left_id)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {

	$social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"list.socialshare-list"}';

	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.socialshare-list';
	$contentWidget->parent_content_id = $left_id;
	$contentWidget->order = 18;
	$contentWidget->params = $social_share_default_code;
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'activity.feed')
					->where('parent_content_id = ?', $middle_tab)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'activity.feed';
	$contentWidget->parent_content_id = $middle_tab;
	$contentWidget->order = 1;
	$contentWidget->params = '{"title":"Updates","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.info-list')
					->where('parent_content_id = ?', $middle_tab)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.info-list';
	$contentWidget->parent_content_id = $middle_tab;
	$contentWidget->order = 2;
	$contentWidget->params = '{"title":"Info","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.overview-list')
					->where('parent_content_id = ?', $middle_tab)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.overview-list';
	$contentWidget->parent_content_id = $middle_tab;
	$contentWidget->order = 3;
	$contentWidget->params = '{"title":"Overview","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.location-list')
					->where('parent_content_id = ?', $middle_tab)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.location-list';
	$contentWidget->parent_content_id = $middle_tab;
	$contentWidget->order = 4;
	$contentWidget->params = '{"title":"Map","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.photos-list')
					->where('parent_content_id = ?', $middle_tab)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.photos-list';
	$contentWidget->parent_content_id = $middle_tab;
	$contentWidget->order = 5;
	$contentWidget->params = '{"title":"Photos","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.video-list')
					->where('parent_content_id = ?', $middle_tab)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.video-list';
	$contentWidget->parent_content_id = $middle_tab;
	$contentWidget->order = 6;
	$contentWidget->params = '{"title":"Videos","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.review-list')
					->where('parent_content_id = ?', $middle_tab)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.review-list';
	$contentWidget->parent_content_id = $middle_tab;
	$contentWidget->order = 8;
	$contentWidget->params = '{"title":"Reviews","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'list.discussion-list')
					->where('parent_content_id = ?', $middle_tab)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.discussion-list';
	$contentWidget->parent_content_id = $middle_tab;
	$contentWidget->order = 9;
	$contentWidget->params = '{"title":"Discussions","titleCount":"true"}';
	$contentWidget->save();
	}

	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'core.profile-links')
					->where('parent_content_id = ?', $middle_tab)
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (empty($fetchWidgetContentId)) {
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'core.profile-links';
	$contentWidget->parent_content_id = $middle_tab;
	$contentWidget->order = 10;
	$contentWidget->params = '{"title":"Links","titleCount":"true"}';
	$contentWidget->save();
	}
}


//Widgets: Member profile page.
$selectPage = $pageTable->select()
				->from($pageTableName, array('page_id'))
				->where('name =?', 'user_profile_index')
				->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (!empty($page_id)) {
	$page_id = $page_id[0]['page_id'];
	$selectWidgetId = $contentTable->select()
					->from($contentTableName, array('content_id'))
					->where('page_id =?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'core.container-tabs')
					->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
	if (!empty($fetchWidgetContentId)) {
	$tab_id = $fetchWidgetContentId[0]['content_id'];
	$contentWidget = $contentTable->createRow();
	$contentWidget->page_id = $page_id;
	$contentWidget->type = 'widget';
	$contentWidget->name = 'list.profile-list';
	$contentWidget->parent_content_id = $tab_id;
	$contentWidget->order = 999;
	$contentWidget->params = '{"title":"Listings","titleCount":true}';
	$contentWidget->save();
	}
}
