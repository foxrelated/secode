<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: widgetSettings.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
$check_name = $check_table->info('name');
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'core_main_siteevent');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'core_main_siteevent';
    $menu_item->module = 'siteevent';
    $menu_item->label = 'Events';
    $menu_item->plugin = 'Siteevent_Plugin_Menus::canViewSiteevents';
    $menu_item->params = '{"route":"siteevent_general","action":"home"}';
    $menu_item->menu = 'core_main';
    $menu_item->submenu = '';
    $menu_item->order = 999;
    $menu_item->save();
}

$db = Zend_Db_Table_Abstract::getDefaultAdapter();

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("siteevent_admin_main_general", "siteevent", "General Settings", "", \'{"route":"admin_default","module":"siteevent","controller":"settings"}\', "siteevent_admin_main_settings", "", 1, 0, 1),
("siteevent_admin_main_createedit", "siteevent", "Miscellaneous Settings", "", \'{"route":"admin_default","module":"siteevent","controller":"settings", "action":"create-edit"}\', "siteevent_admin_main_settings", "", 1, 0, 2);');

$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
$db->query("INSERT IGNORE INTO `engine4_siteevent_editors` (`user_id`, `designation`, `details`, `about`, `badge_id`, `super_editor`) VALUES ($viewer_id,'Super Editor','','',0, 1)");

$db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('SITEEVENT_EMAIL_FRIEND', 'siteevent', '[host],[email],[recipient_title],[recipient_link],[review_title],[review_title_with_link],[user_email],[userComment]'),
('SITEEVENT_EVENT_CANCELED', 'siteevent', '[host],[email],[event_title],[event_message],[event_link]'),
('SITEEVENT_EVENT_PUBLISHED', 'siteevent', '[host],[email],[event_title],[event_message],[event_link]'),
('SITEEVENT_EVENT_CREATION_EDITOR', 'siteevent', '[host],[object_title],[sender],[object_link],[object_description]'),
('SITEEVENT_EDITOR_EMAIL', 'siteevent', '[host],[email],[sender],[message]'),
('SITEEVENT_EDITOR_ASSIGN_EMAIL', 'siteevent', '[sender],[editor_page_url]'),
('SITEEVENT_EDITORREVIEW_CREATION', 'siteevent', '[host],[editor_name],[editor],[object_title],[object_parent_with_link],[object_link], [object_parent_title],[object_description]'),
('SITEEVENT_APPROVED_EMAIL_NOTIFICATION', 'siteevent', '[host],[email],[subject],[title],[message][object_link]'),
('SITEEVENT_TELLAFRIEND_EMAIL', 'siteevent', '[host],[email],[sender],[message][object_link]');");

$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES 
  
("comment_siteevent_photo", "siteevent", \'{item:$subject} commented on {item:$owner}\'\'s {item:$object:photo}: {body:$body}\', 1, 1, 1, 1, 1, 0),
("comment_siteevent_video", "siteevent", \'{item:$subject} commented on {item:$owner}\'\'s {item:$object:video}: {body:$body}\', 1, 1, 1, 1, 1, 0),
("comment_siteevent_event", "siteevent", \'{item:$subject} commented on {item:$owner}\'\'s event {item:$object:$title}: {body:$body}\', "1", "1", "1", "1", "1", 1),
("comment_siteevent_review", "siteevent", \'{item:$subject} commented on {item:$owner}\'\'s review {item:$object:$title}: {body:$body}\', "1", "1", "1", "1", "1", 1),
("nestedcomment_siteevent_event", "siteevent", \'{item:$subject} replied to a comment on {item:$owner}\'\'s event {item:$object:$title}: {body:$body}\', "1", "1", "1", "1", "1", 1),
("nestedcomment_siteevent_review", "siteevent", \'{item:$subject} replied to a comment on {item:$owner}\'\'s review {item:$object:$title}: {body:$body}\', "1", "1", "1", "1", "1", 1)');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("siteevent_discussion_reply", "siteevent", \'{item:$subject} has {item:$object:posted} on an {itemParent:$object::event topic} you posted on.\', 0, ""),
("siteevent_discussion_response", "siteevent", \'{item:$subject} has {item:$object:posted} on an {itemParent:$object::event topic} you created.\', 0, ""),
("siteevent_video_processed", "siteevent", \'Your {item:$object:event video} is ready to be viewed.\', 0, ""),
("siteevent_video_processed_failed", "siteevent", \'Your {item:$object:event video} has failed to process.\', 0, ""),
("siteevent_write_review", "siteevent", \'{item:$subject} has written a {item:$object:review} for the {itemParent:$object::event}.\', "0", ""),
("siteevent_editorreview", "siteevent", \'{item:$subject} has written a {item:$object:review} for the {itemParent:$object::event}.\', "0", "")');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("siteeventvideo_main_browse", "siteevent", "Browse Videos", "", \'{"route":"siteevent_video_general"}\', "siteeventvideo_main", "", 1);');

$db->query('INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
("siteeventvideo_main", "standard", "Advanced Events - Video Main Navigation Menu");');

$contentTable = Engine_Api::_()->getDbtable('content', 'core');
$contentTableName = $contentTable->info('name');

//EVENT MY CALENDAR PAGE

$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_index_manage_calendar")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (empty($page_id)) {

    $containerCount = 0;
    $widgetCount = 0;

    //CREATE PAGE
    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_index_manage_calendar",
        'displayname' => 'Advanced Events - My Events Calendar',
        'title' => 'My Calendar',
        'description' => 'This page shows the user’s events (joined and created) in a calendar.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $main_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => $containerCount++,
    ));
    $top_middle_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $main_middle_id = $db->lastInsertId();

    //INSERT THE NAVIGATION WIDGET
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.navigation-siteevent',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    //INSERT THE MY EVENTS CALENDAR WIDGET

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.mycalendar-siteevent',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));
}




//EVENT MANAGE PAGE CREATION
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_index_manage")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (empty($page_id)) {

    $containerCount = 0;
    $widgetCount = 0;

    //CREATE PAGE
    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_index_manage",
        'displayname' => 'Advanced Events - Event Manage Page',
        'title' => 'My Events',
        'description' => 'This is the event manage page.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $main_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => $containerCount++,
    ));
    $top_middle_id = $db->lastInsertId();


    //RIGHT CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $right_container_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $main_middle_id = $db->lastInsertId();

    //INSERT THE NAVIGATION WIDGET
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.navigation-siteevent',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    //INSERT THE CALENDAR WIDGET WIDGET IN RIGHT CONTAINER
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.calendarview-siteevent',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"0":"","title":"","titleCount":true}',
    ));

    //INSERT THE MY EVENTS LIST WIDGET
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.manage-events-siteevent',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"title":"","titleCount":true,"layouts_order":1,"statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"180","postedby":"1","eventInfo":["featuredLabel","sponsoredLabel","newLabel"],"orderby":"event_id","itemCount":"20","truncation":"100","nomobile":"0","name":"siteevent.manage-events-siteevent"}',
    ));
}

//EVENT PINBOARD PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_index_pinboard")
        ->limit(1)
        ->query()
        ->fetchColumn();
$containerCount = 0;
$widgetCount = 0;
if (empty($page_id)) {
    //CREATE PAGE
    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_index_pinboard",
        'displayname' => 'Advanced Events - Browse Events’ Pinboard View',
        'title' => '',
        'description' => 'This is the browse events pinboard view page.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();


    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $main_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => $containerCount++,
    ));
    $top_middle_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $main_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.navigation-siteevent',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'seaocore.scroll-top',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.search-siteevent',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"title":"","titleCount":true,"viewType":"horizontal","showAllCategories":"1","resultsAction":"pinboard","locationDetection":"1","whatWhereWithinmile":"0","advancedSearch":"0","priceFieldType":"slider","minPrice":"0","maxPrice":"999","nomobile":"0","name":"siteevent.search-siteevent"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.pinboard-browse',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"title":"","statistics":["likeCount","memberCount"],"show_buttons":["membership","comment","like","share","facebook","pinit"],"eventType":null,"category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["hostName","startDate","directionLink"],"showEventType":"upcoming","userComment":"1","autoload":"1","defaultLoadingImage":"1","itemWidth":"274","withoutStretch":"0","itemCount":"16","truncationLocation":"35","truncationDescription":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.pinboard-browse"}',
    ));
}

//EVENT CREATE PAGE CREATION
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_index_create")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (empty($page_id)) {

    $containerCount = 0;

    //CREATE PAGE
    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_index_create",
        'displayname' => 'Advanced Events - Event Create Page',
        'title' => 'Event Create Page',
        'description' => 'This page allows users to create events.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $main_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => $containerCount++,
    ));
    $top_middle_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $main_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.navigation-siteevent',
        'parent_content_id' => $top_middle_id,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
    ));
}

//EVENT EDIT PAGE CREATION
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_index_edit")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (empty($page_id)) {

    $containerCount = 0;

    //CREATE PAGE
    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_index_edit",
        'displayname' => 'Advanced Events - Event Edit Page',
        'title' => 'Event Edit Page',
        'description' => 'This page allows users to edit events.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $main_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => $containerCount++,
    ));
    $top_middle_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $main_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.navigation-siteevent',
        'parent_content_id' => $top_middle_id,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
    ));
}

//HOME PAGE CREATE
Engine_Api::_()->getApi('template', 'siteevent')->defaultHome();

//BROWSE EVENT PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_index_index")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {

    $containerCount = 0;
    $widgetCount = 0;

    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_index_index",
        'displayname' => 'Advanced Events - Browse Events',
        'title' => '',
        'description' => 'This is the event browse page.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $main_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => $containerCount++,
    ));
    $top_middle_id = $db->lastInsertId();

    //LEFT CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $left_container_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $main_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.navigation-siteevent',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.browse-breadcrumb-siteevent',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '{"nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.listtypes-categories',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '{"viewDisplayHR":"0","title":"","nomobile":"0","name":"siteevent.listtypes-categories"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.search-siteevent',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.newevent-siteevent',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '{"nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.tagcloud-siteevent',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '{"title": "Popular Tags (%s)","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'seaocore.change-my-location',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Select your Location","showSeperateLink":"1","nomobile":"0","name":"seaocore.change-my-location"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.categories-banner-siteevent',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.browse-events-siteevent',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"title":"","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"100","eventType":"All","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","columnHeight":"260","eventInfo":["hostName","startDate","location","directionLink"],"titlePosition":"1","orderby":"event_id","itemCount":"20","truncation":"100","ratingType":"rating_both","detactLocation":"1","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.browse-events-siteevent"}',
    ));
}

////TOP RATED EVENTS WIDGETIZED PAGE
//$page_id = $db->select()
//        ->from('engine4_core_pages', 'page_id')
//        ->where('name = ?', "siteevent_index_top-rated")
//        ->limit(1)
//        ->query()
//        ->fetchColumn();
//
//if (!$page_id) {
//
//    $containerCount = 0;
//    $widgetCount = 0;
//
//    $db->insert('engine4_core_pages', array(
//        'name' => "siteevent_index_top-rated",
//        'displayname' => 'Advanced Events - Browse Top Rated Events',
//        'title' => '',
//        'description' => '',
//        'custom' => 0,
//    ));
//    $page_id = $db->lastInsertId();
//
//    //TOP CONTAINER
//    $db->insert('engine4_core_content', array(
//        'type' => 'container',
//        'name' => 'top',
//        'page_id' => $page_id,
//        'order' => $containerCount++,
//    ));
//    $top_container_id = $db->lastInsertId();
//
//    //MAIN CONTAINER
//    $db->insert('engine4_core_content', array(
//        'type' => 'container',
//        'name' => 'main',
//        'page_id' => $page_id,
//        'order' => $containerCount++,
//    ));
//    $main_container_id = $db->lastInsertId();
//
//    //INSERT TOP-MIDDLE
//    $db->insert('engine4_core_content', array(
//        'type' => 'container',
//        'name' => 'middle',
//        'page_id' => $page_id,
//        'parent_content_id' => $top_container_id,
//        'order' => $containerCount++,
//    ));
//    $top_middle_id = $db->lastInsertId();
//
//    //RIGHT CONTAINER
//    $db->insert('engine4_core_content', array(
//        'type' => 'container',
//        'name' => 'right',
//        'page_id' => $page_id,
//        'parent_content_id' => $main_container_id,
//        'order' => $containerCount++,
//    ));
//    $right_container_id = $db->lastInsertId();
//
//    //MAIN-MIDDLE CONTAINER
//    $db->insert('engine4_core_content', array(
//        'type' => 'container',
//        'name' => 'middle',
//        'page_id' => $page_id,
//        'parent_content_id' => $main_container_id,
//        'order' => $containerCount++,
//    ));
//    $main_middle_id = $db->lastInsertId();
//
//    $db->insert('engine4_core_content', array(
//        'page_id' => $page_id,
//        'type' => 'widget',
//        'name' => 'siteevent.navigation-siteevent',
//        'parent_content_id' => $top_middle_id,
//        'order' => $widgetCount++,
//        'params' => '',
//    ));
//
//    $db->insert('engine4_core_content', array(
//        'page_id' => $page_id,
//        'type' => 'widget',
//        'name' => 'siteevent.browse-breadcrumb-siteevent',
//        'parent_content_id' => $top_middle_id,
//        'order' => $widgetCount++,
//        'params' => '{"nomobile":"1"}',
//    ));
//
//    $db->insert('engine4_core_content', array(
//        'page_id' => $page_id,
//        'type' => 'widget',
//        'name' => 'siteevent.categories-sidebar-siteevent',
//        'parent_content_id' => $right_container_id,
//        'order' => $widgetCount++,
//        'params' => '{"title":"Categories","titleCount":"true","nomobile":"1"}',
//    ));
//
//    $db->insert('engine4_core_content', array(
//        'page_id' => $page_id,
//        'type' => 'widget',
//        'name' => 'siteevent.search-siteevent',
//        'parent_content_id' => $right_container_id,
//        'order' => $widgetCount++,
//        'params' => '',
//    ));
//
//    $db->insert('engine4_core_content', array(
//        'page_id' => $page_id,
//        'type' => 'widget',
//        'name' => 'siteevent.newevent-siteevent',
//        'parent_content_id' => $right_container_id,
//        'order' => $widgetCount++,
//        'params' => '{"nomobile":"1"}',
//    ));
//
//    $db->insert('engine4_core_content', array(
//        'page_id' => $page_id,
//        'type' => 'widget',
//        'name' => 'siteevent.tagcloud-siteevent',
//        'parent_content_id' => $right_container_id,
//        'order' => $widgetCount++,
//        'params' => '{"title": "Popular Tags (%s)","nomobile":"1"}',
//    ));
//
//    $db->insert('engine4_core_content', array(
//        'page_id' => $page_id,
//        'type' => 'widget',
//        'name' => 'siteevent.categories-banner-siteevent',
//        'parent_content_id' => $main_middle_id,
//        'order' => $widgetCount++,
//        'params' => '{"nomobile":"1"}',
//    ));
//
//    $db->insert('engine4_core_content', array(
//        'page_id' => $page_id,
//        'type' => 'widget',
//        'name' => 'siteevent.rated-events-siteevent',
//        'parent_content_id' => $main_middle_id,
//        'order' => $widgetCount++,
//        'params' => '{"title":"","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":1,"statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"180","truncationGrid":100,"ratingType":"rating_both"}',
//    ));
//}
//START THE WORK FOR MAKE WIDGETIZE PAGE OF Locatio or map.
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', "siteevent_index_map")
        ->limit(1);
$info = $select->query()->fetch();

if (empty($info)) {

    $containerCount = 0;
    $widgetCount = 0;

    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_index_map",
        'displayname' => "Advanced Events - Browse Events' Locations",
        'title' => "Browse Events' Locations",
        'description' => 'This is the event browse locations page.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId('engine4_core_pages');

    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $main_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => $containerCount++,
    ));
    $top_middle_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $main_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.navigation-siteevent',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.browselocation-siteevent',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"title":"","titleCount":true,"showEventType":"upcoming","eventInfo":["hostName","categoryLink","featuredLabel","sponsoredLabel","newLabel","startDate","price","venueName","location","directionLink","likeCount","memberCount","reviewCount","ratingStar"],"truncationLocation":"100","ratingType":"rating_avg","showAllCategories":"1","priceFieldType":"text","minPrice":"0","maxPrice":"999","nomobile":"0","name":"siteevent.browselocation-siteevent"}',
    ));
}

Engine_Api::_()->getApi('template', 'siteevent')->defaultProfile();

//Check if it's already been placed
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'siteevent_video_view')
        ->limit(1);

$info = $select->query()->fetch();

if (empty($info)) {
    $db->insert('engine4_core_pages', array(
        'name' => 'siteevent_video_view',
        'displayname' => 'Advanced Events - Video View Page',
        'title' => 'Video Profile',
        'description' => 'This is the video view page.',
        'custom' => 0,
        'provides' => 'subject=siteevent',
    ));
    $page_id = $db->lastInsertId('engine4_core_pages');

    //containers
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'order' => 1,
        'params' => '',
    ));
    $container_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $container_id,
        'order' => 1,
        'params' => '',
    ));
    $right_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 3,
        'params' => '',
    ));
    $middle_id = $db->lastInsertId('engine4_core_content');

    //middle column content
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.video-content',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '',
    ));

    //right column
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.show-same-tags',
        'parent_content_id' => $right_id,
        'order' => 1,
        'params' => '{"title":"Similar Videos","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.show-also-liked',
        'parent_content_id' => $right_id,
        'order' => 2,
        'params' => '{"title":"People Also Liked","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.show-same-poster',
        'parent_content_id' => $right_id,
        'order' => 3,
        'params' => '{"title":"Other Videos From Event","nomobile":"1"}',
    ));
}

//DIARY PROFILE PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_diary_profile")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {

    $containerCount = 0;
    $widgetCount = 0;

    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_diary_profile",
        'displayname' => 'Advanced Events - Diary Profile',
        'title' => 'Diary Profile',
        'description' => 'This is the diary profile page.',
        'custom' => 0,
    ));

    $page_id = $db->lastInsertId();
    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => $containerCount++,
    ));
    $top_middle_id = $db->lastInsertId();
    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $main_container_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $main_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.diary-profile-items',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"shareOptions":["siteShare","friend","report","print","socialShare"],"viewTypes":["list","pin"],"statistics":["likeCount","memberCount"],"statisticsDiary":["entryCount","viewCount"],"show_buttons":["diary","comment","like","share","facebook","pinit"]}',
    ));
}

//DIARY HOME PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_diary_browse")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {

    $containerCount = 0;
    $widgetCount = 0;

    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_diary_browse",
        'displayname' => 'Advanced Events - Browse Diaries',
        'title' => 'Browse Diaries',
        'description' => 'This is the diary browse page.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $main_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => $containerCount++,
    ));
    $top_middle_id = $db->lastInsertId();

    //RIGHT CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $right_container_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $main_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.navigation-siteevent',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.diary-browse-search',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '{"title":"","viewTypes":["list","grid"],"statisticsDiary":["entryCount","viewCount"],"viewTypeDefault":"grid","listThumbsValue":"2","itemCount":"20"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.diary-creation-link',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.diary-events',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"My Firends\' Event Diaries","statisticsDiary":["viewCount","entryCount"],"type":"friends","orderby":"creation_date","limit":"3","truncation":"25","nomobile":"1","name":"siteevent.diary-events"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.diary-events',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Diaries Having Maximum Events","statisticsDiary":["entryCount"],"type":"none","orderby":"total_item","limit":"3","truncation":"25","nomobile":"1","name":"siteevent.diary-events"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.diary-events',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Most Popular Event Diaries","statisticsDiary":["viewCount","entryCount"],"type":"none","orderby":"view_count","limit":"3","truncation":"25","nomobile":"1","name":"siteevent.diary-events"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.diary-events',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Most Viewed Event Diaries","statisticsDiary":["viewCount"],"type":"none","orderby":"view_count","limit":"3","truncation":"25","nomobile":"1","name":"siteevent.diary-events"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.diary-browse',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"title":"","viewTypes":["list","grid"],"statisticsDiary":["entryCount","viewCount"],"viewTypeDefault":"grid","listThumbsValue":"2","itemCount":"20"}',
    ));
}

//REVIEW PROFILE PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_review_view")
        ->limit(1)
        ->query()
        ->fetchColumn();

//CREATE PAGE IF NOT EXIST
if (!$page_id) {

    $containerCount = 0;
    $widgetCount = 0;

    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_review_view",
        'displayname' => 'Advanced Events - Review Profile',
        'title' => 'Review Profile',
        'description' => 'This is the review profile page.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $main_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => $containerCount++,
    ));
    $top_middle_id = $db->lastInsertId();

    //RIGHT CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $right_container_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $main_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'seaocore.scroll-top',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.profile-review-breadcrumb-siteevent',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '{"nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.quick-specification-siteevent',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Quick Information","titleCount":true,"nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.socialshare-siteevent',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Social Share","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.related-events-view-siteevent',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Related Events","statistics":["likeCount","memberCount"],"nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.ownerreviews-siteevent',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"statistics":["likeCount","replyCount","commentCount"],"nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.profile-review-siteevent',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"title":"","titleCount":true,"loaded_by_ajax":"1","name":"siteevent.profile-review-siteevent"}',
    ));
}

//CATEGORIES HOME PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_index_categories")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {

    $containerCount = 0;
    $widgetCount = 0;

    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_index_categories",
        'displayname' => 'Advanced Events - Categories Home',
        'title' => 'Categories Home',
        'description' => 'This is the categories home page.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();


    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $main_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => $containerCount++,
    ));
    $top_middle_id = $db->lastInsertId();

    //LEFT CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $left_container_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $main_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.navigation-siteevent',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.listtypes-categories',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '{"viewDisplayHR":"0","title":"All Categories","nomobile":"0","name":"siteevent.listtypes-categories"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.events-siteevent',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Most Liked Events","titleCount":true,"statistics":["likeCount","memberCount"],"viewType":"gridview","columnWidth":"215","eventType":null,"fea_spo":"","showEventType":"upcoming","titlePosition":"1","columnHeight":"287","popularity":"like_count","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["featuredLabel","sponsoredLabel","newLabel","startDate","location","directionLink","likeCount"],"itemCount":"2","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.events-siteevent"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.recently-viewed-siteevent',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Recently Viewed By Your Friends","titleCount":true,"statistics":["likeCount","memberCount"],"eventType":null,"fea_spo":"featured","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","show":"1","viewType":"gridview","columnWidth":"215","columnHeight":"267","eventInfo":["startDate","location"],"titlePosition":"1","truncationLocation":"35","truncation":"100","count":"2","ratingType":"rating_avg","nomobile":"0","name":"siteevent.recently-viewed-siteevent"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.recently-viewed-siteevent',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"You Recently Viewed","titleCount":true,"statistics":["likeCount","memberCount"],"eventType":null,"fea_spo":"featured","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","show":"0","viewType":"gridview","columnWidth":"215","columnHeight":"267","eventInfo":["categoryLink","startDate","location","directionLink"],"titlePosition":"1","truncationLocation":"35","truncation":"100","count":"2","ratingType":"rating_avg","nomobile":"0","name":"siteevent.recently-viewed-siteevent"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.searchbox-siteevent',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"title":"","titleCount":"","locationDetection":"1","formElements":["textElement","categoryElement"],"categoriesLevel":["category","subcategory","subsubcategory"],"showAllCategories":"0","textWidth":"500","locationWidth":"250","locationmilesWidth":"125","categoryWidth":"150","nomobile":"0","name":"siteevent.searchbox-siteevent"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.categories-grid-view',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Categories","titleCount":true,"showSubCategoriesCount":"5","showCount":"0","columnWidth":"278","columnHeight":"260","nomobile":"0","name":"siteevent.categories-grid-view"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.recently-popular-random-siteevent',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"title":"","titleCount":"","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"layouts_views":["listZZZview","gridZZZview","mapZZZview"],"ajaxTabs":["upcoming","mostZZZreviewed","mostZZZjoined","thisZZZmonth","thisZZZweek","thisZZZweekend","today"],"showContent":["price","location"],"upcoming_order":"1","reviews_order":"9","popular_order":"8","featured_order":"7","sponosred_order":"6","joined_order":"6","columnWidth":"199","titleLink":"","eventType":"0","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["hostName","categoryLink","startDate","location","directionLink"],"showEventType":"upcoming","defaultOrder":"gridZZZview","columnHeight":"277","month_order":"2","week_order":"3","weekend_order":"4","today_order":"5","titlePosition":"1","showViewMore":"1","limit":"8","truncationLocation":"35","truncationList":"600","truncationGrid":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.recently-popular-random-siteevent"}',
    ));
}

//MEMBER PROFILE PAGE WIDGETS
$page_id = $db->select()
        ->from('engine4_core_pages', array('page_id'))
        ->where('name =?', 'user_profile_index')
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!empty($page_id)) {

    $tab_id = $db->select()
            ->from('engine4_core_content', array('content_id'))
            ->where('page_id =?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'core.container-tabs')
            ->limit(1)
            ->query()
            ->fetchColumn();

    if (!empty($tab_id)) {

        $content_id = $db->select()
                ->from('engine4_core_content', array('content_id'))
                ->where('page_id =?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'siteevent.profile-siteevent')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (empty($content_id)) {
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.profile-siteevent',
                'parent_content_id' => $tab_id,
                'order' => 999,
                'params' => '{"title":"Events","titleCount":true,"statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"eventType":"0","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["hostName","categoryLink","featuredLabel","sponsoredLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"showEventType":"all","showEventFilter":"1","eventFilterTypes":["joined","ledOwner","host","liked"],"typesOfViews":["listview","gridview","mapview"],"layoutViewType":"gridview","titlePosition":"1","columnWidth":"199","columnHeight":"330","truncationLocation":"35","truncation":"100","itemCount":"12","ratingType":"rating_avg","nomobile":"0","name":"siteevent.profile-siteevent"}',
            ));
        }

        $content_id = $db->select()
                ->from('engine4_core_content', array('content_id'))
                ->where('page_id =?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'siteevent.host-events')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (empty($content_id)) {
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.host-events',
                'parent_content_id' => $tab_id,
                'order' => 999,
                'params' => '{"title":"Hosted Events","titleCount":true,"statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"eventType":"0","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["startDate","ledBy","venueName","location","directionLink","viewCount","memberCount"],"typesOfViews":["listview","gridview","mapview"],"viewType":"gridview","titlePosition":"1","columnWidth":"199","columnHeight":"295","eventFilterTypes":["upcoming","past"],"truncationLocation":"35","truncation":"100","truncationGrid":"100","itemCount":"10","ratingType":"rating_avg","nomobile":"0","name":"siteevent.host-events"}',
            ));
        }

        $content_id = $db->select()
                ->from('engine4_core_content', array('content_id'))
                ->where('page_id =?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'siteevent.editor-profile-reviews-siteevent')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (empty($content_id)) {

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.editor-profile-reviews-siteevent',
                'parent_content_id' => $tab_id,
                'order' => 999,
                'params' => '{"title":"Reviews As Editor","type":"editor"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.editor-profile-reviews-siteevent',
                'parent_content_id' => $tab_id,
                'order' => 999,
                'params' => '{"title":"Reviews As User","type":"user"}',
            ));
        }
    }
}

//REVIEW BROWSE PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_review_browse")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (empty($page_id)) {

    $containerCount = 0;
    $widgetCount = 0;

    $db->insert('engine4_core_pages', array(
        'name' => 'siteevent_review_browse',
        'displayname' => 'Advanced Events - Browse Reviews',
        'title' => 'Browse Reviews',
        'description' => 'This is the review browse page.',
        'custom' => 0
    ));

    $page_id = $db->lastInsertId('engine4_core_pages');

    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => $containerCount++,
    ));
    $top_middle_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'order' => $containerCount++,
        'params' => '',
    ));
    $main_container_id = $db->lastInsertId('engine4_core_content');

    //RIGHT CONTAINER
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
        'params' => '',
    ));
    $right_container_id = $db->lastInsertId('engine4_core_content');

    //MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
        'params' => '',
    ));
    $main_middle_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.navigation-siteevent',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'seaocore.scroll-top',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.review-browse-search',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.review-of-the-day',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Review of the Day","titleCount":"true","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.popular-reviews-siteevent',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Popular Reviews","statistics":["helpfulCount"],"type":"user","status":"0","popularity":"helpful_count","interval":"overall","groupby":"1","itemCount":"3","truncation":"25","nomobile":"0","name":"siteevent.popular-reviews-siteevent"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.top-reviewers-siteevent',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Top Reviewers","type":"user","itemCount":"3","nomobile":"0","name":"siteevent.top-reviewers-siteevent"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.popular-reviews-siteevent',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Most Liked Reviews","statistics":["likeCount"],"type":"overall","status":"0","popularity":"like_count","interval":"overall","groupby":"1","itemCount":"3","truncation":"25","nomobile":"0","name":"siteevent.popular-reviews-siteevent"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.popular-reviews-siteevent',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Most Viewed Reviews","statistics":["viewCount"],"type":"user","status":"0","popularity":"view_count","interval":"overall","groupby":"1","itemCount":"3","truncation":"25","nomobile":"0","name":"siteevent.popular-reviews-siteevent"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.reviews-statistics',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Reviews Statistics","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.content',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));
}

//EDITOR HOME PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_editor_home")
        ->limit(1)
        ->query()
        ->fetchColumn();

//CREATE PAGE IF NOT EXIST
if (!$page_id) {

    $containerCount = 0;
    $widgetCount = 0;

    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_editor_home",
        'displayname' => 'Advanced Events - Editors Home',
        'title' => 'Editors Home',
        'description' => 'This is the editors home page.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $main_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => $containerCount++,
    ));
    $top_middle_id = $db->lastInsertId();

    //RIGHT CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $right_container_id = $db->lastInsertId();

    //LEFT CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $left_container_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $main_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.navigation-siteevent',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.popular-reviews-siteevent',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Most Recent Reviews","groupby":"0","type":"editor","popularity":"review_id","titleCount":"true","itemCount":"5","statistics":"","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.popular-reviews-siteevent',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Most Viewed Reviews","groupby":"0","type":"editor","popularity":"view_count","titleCount":"true","itemCount":"5","statistics":"","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.editor-featured-siteevent',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Featured Editor","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.editors-home-statistics-siteevent',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Statistics","titleCount":"true","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.top-reviewers-siteevent',
        'parent_content_id' => $right_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Top Reviewers","type":"editor","titleCount":"true","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.editors-home',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Review Editors","titleCount":"true"}',
    ));
}

//EDITOR PROFILE PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_editor_profile")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {

    $containerCount = 0;
    $widgetCount = 0;

    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_editor_profile",
        'displayname' => 'Advanced Events - Editor Profile',
        'title' => 'Editor Profile',
        'description' => 'This is the editor profile page.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'order' => $containerCount++,
        'params' => '',
    ));
    $main_container_id = $db->lastInsertId('engine4_core_content');

    //RIGHT CONTAINER
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'left',
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
        'params' => '',
    ));
    $left_container_id = $db->lastInsertId('engine4_core_content');

    //MIDDLE CONTAINER  
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
        'params' => '',
    ));
    $main_middle_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.editor-photo-siteevent',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.editor-profile-info',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"About Editor","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.editor-profile-statistics',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Statistics","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.socialshare-siteevent',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Social Share","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'seaocore.scroll-top',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.editor-profile-title',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"max":"6"}',
    ));
    $tab_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.editor-profile-reviews-siteevent',
        'parent_content_id' => $tab_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Reviews As Editor","type":"editor"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.editor-profile-reviews-siteevent',
        'parent_content_id' => $tab_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Reviews As User","type":"user"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.editor-replies-siteevent',
        'parent_content_id' => $tab_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Comments"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.editors-siteevent',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Similar Editors","nomobile":"1"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.content',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));
}

$db->query('
  
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES

("siteevent_admin_main_level", "siteevent", "Member Level Settings", "", \'{"route":"admin_default","module":"siteevent","controller":"settings","action":"level"}\', "siteevent_admin_main", "", 1, 0, 10),

("siteevent_admin_main_categories", "siteevent", "Categories", "", \'{"route":"admin_default","module":"siteevent","controller":"settings","action":"categories"}\', "siteevent_admin_main", "", 1, 0, 15),

("siteevent_admin_main_fields", "siteevent", "Profile Fields", "", \'{"route":"admin_default","module":"siteevent","controller":"fields"}\', "siteevent_admin_main", "", 1, 0, 20),

("siteevent_admin_main_profilemaps", "siteevent", "Category-Event Profile Mapping", "", \'{"route":"admin_default","module":"siteevent","controller":"profilemaps","action":"manage"}\', "siteevent_admin_main", "", 1, 0, 25),

("siteevent_admin_main_diary", "siteevent", "Manage Event Diaries", "", \'{"route":"admin_default","module":"siteevent","controller":"diary","action":"manage"}\', "siteevent_admin_main", "", 1, 0, 30),

("siteevent_admin_main_formsearch", "siteevent", "Search Form Settings", "", \'{"route":"admin_default","module":"siteevent","controller":"settings","action":"form-search"}\', "siteevent_admin_main", "", 1, 0, 35),

("siteevent_admin_main_manage", "siteevent", "Manage Events", "", \'{"route":"admin_default","module":"siteevent","controller":"manage"}\', "siteevent_admin_main", "", 1, 0, 40),

("siteevent_admin_main_video", "siteevent", "Video Settings", "", \'{"route":"admin_default","module":"siteevent","controller":"settings","action":"show-video"}\', "siteevent_admin_main", "", 1, 0, 45),

("siteevent_admin_submain_general_tab", "siteevent", "Video Settings", "", \'{"route":"admin_default","module":"siteevent","controller":"settings","action":"show-video"}\', "siteevent_admin_submain", "", 1, 0, 1),

("siteevent_admin_submain_manage_tab", "siteevent", "Manage Videos", "", \'{"route":"admin_default","module":"siteevent","controller":"video","action": "manage"}\', "siteevent_admin_submain", "", 1, 0, 2),

("siteevent_admin_submain_utilities_tab", "siteevent", "Video Utilities", "", \'{"route":"admin_default","module":"siteevent","controller":"video", "action": "utility"}\', "siteevent_admin_submain", "", 1, 0, 3),

("siteevent_admin_main_statistic", "siteevent", "Statistics", "", \'{"route":"admin_default","module":"siteevent","controller":"settings","action":"statistic"}\', "siteevent_admin_main", "", 1, 0, 50),

("siteevent_admin_main_import", "siteevent", "Import", "", \'{"route":"admin_default","module":"siteevent","controller":"importevent"}\', "siteevent_admin_main", "", 1, 0, 55),

("siteevent_admin_main_editors", "siteevent", "Manage Editors", "", \'{"route":"admin_default","module":"siteevent","controller":"editors", "action":"manage"}\', "siteevent_admin_main", "", 1, 0, 60),

("siteevent_admin_main_review", "siteevent", "Reviews & Ratings", "", \'{"route":"admin_default","module":"siteevent","controller":"review"}\', "siteevent_admin_main", "", 1, 0, 65),

("siteevent_admin_reviewmain_general", "siteevent", "Review Settings", "", \'{"route":"admin_default","module":"siteevent","controller":"review"}\', "siteevent_admin_reviewmain", "", 1, 0, 1),

("siteevent_admin_reviewmain_manage", "siteevent", "Manage Reviews & Ratings", "", \'{"route":"admin_default","module":"siteevent","controller":"review", "action":"manage"}\', "siteevent_admin_reviewmain", "", 1, 0, 2),

("siteevent_admin_reviewmain_fields", "siteevent", "Review Profile Fields", "", \'{"route":"admin_default","module":"siteevent","controller":"fields-review"}\', "siteevent_admin_reviewmain", "", 1, 0, 3),

("siteevent_admin_reviewmain_profilemaps", "siteevent", "Category-Review Profile Mapping", "", \'{"route":"admin_default","module":"siteevent","controller":"profilemaps-review","action":"manage"}\', "siteevent_admin_reviewmain", "", 1, 0, 4),

("siteevent_admin_reviewmain_ratingparams", "siteevent", "Rating Parameters", "", \'{"route":"admin_default","module":"siteevent","controller":"ratingparameters","action":"manage"}\', "siteevent_admin_reviewmain", "", 1, 0, 5),

("siteevent_admin_main_modules", "siteevent", "Manage Modules", "", \'{"route":"admin_default","module":"siteevent","controller":"modules"}\', "siteevent_admin_main", "", "1", 0, 70),

("siteevent_admin_main_ads", "siteevent", "Ad Settings", "", \'{"route":"admin_default","module":"siteevent","controller":"settings","action":"adsettings"}\', "siteevent_admin_main", "", 1, 0, 75),

("siteevent_admin_main_template", "siteevent", "Layout Templates", "", \'{"route":"admin_default","module":"siteevent","controller":"general", "action":"set-template"}\', "siteevent_admin_main", "", 1, 0, 77),

("siteevent_admin_main_integrations", "siteevent", "Plugin Integrations", "", \'{"route":"admin_default","module":"siteevent","controller":"settings","action":"integrations"}\', "siteevent_admin_main", "", 1, 0, 80),

("siteevent_admin_main_extensions", "siteevent", " Extensions", "", \'{"route":"admin_default","module":"siteevent","controller":"extension","action":"upgrade"}\', "siteevent_admin_main", "", 1, 0, 81)

 ');

//$db->query('
//  
//INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
//
//("siteevent_admin_main_wheretobuy", "siteevent", "Where To Buy", "", \'{"route":"admin_default","module":"siteevent","controller":"where-to-buy"}\', "siteevent_admin_main", "", 1, 0, 60)
//
// ');
//$wheretobuyIcon = array(
//    "2" => 'amazon.gif',
//    "3" => 'ebuy.gif',
//    "4" => 'target.gif',
//    "5" => 'tesco.png',
//    "6" => 'best_buy.gif',
//    "7" => 'comet.png',
//    "8" => 'data_vision_computer_video.gif',
//    "9" => 'newegg.gif',
//    "10" => 'sears.gif',
//    "11" => 'tiger_direct.gif',
//    "12" => 'pc_connectiorr.gif',
//    "13" => 'next_warehouse.gif',
//    "14" => 'amazon_marketplace.gif',
//    "15" => 'beachcamera.gif',
//    "16" => "buydig.gif",
//    "17" => "pcrush.gif"
//);
//$wheretobuyList = Engine_Api::_()->getItemTable('siteevent_wheretobuy')->getList();
//$defaultPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . "application/modules/Siteevent/externals/images/wheretobuy/";
//foreach ($wheretobuyList as $item):
//    if (isset($wheretobuyIcon[$item->getIdentity()]) && $item->getIdentity() != 1 && empty($item->photo_id)) {
//        $item->setPhoto($defaultPath . $wheretobuyIcon[$item->getIdentity()]);
//    }
//endforeach;

$db->query("UPDATE `engine4_activity_actiontypes` SET `enabled` = '0' WHERE `engine4_activity_actiontypes`.`type` = 'video_siteevent'");

//ADD DEFAULT SUPER EDITOR
$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
$db->query("INSERT IGNORE INTO `engine4_siteevent_editors` (`user_id`, `designation`, `details`, `about`, `badge_id`, `super_editor`) VALUES ($viewer_id,'Super Editor','','',0, 1)");

// CREATE CATEGORIES DEFAULT PAGES
$categoryIds = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategoriesArray(array('cat_dependency' => 0, 'subcat_dependency' => 0));
Engine_Api::_()->siteevent()->categoriesPageCreate($categoryIds);

$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_organizer_view")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (empty($page_id)) {

    $containerCount = 0;
    $widgetCount = 0;

    //CREATE PAGE
    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_organizer_view",
        'displayname' => 'Advanced Events - Host Profile',
        'title' => 'Host Profile',
        'description' => 'This is the host profile page.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $main_container_id = $db->lastInsertId();

    //LEFT CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $left_container_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $main_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.organizer-info',
        'parent_content_id' => $left_container_id,
        'order' => $widgetCount++,
        'params' => '{"showInfo":["links","photo","creator","options","totalevent","totalrating"],"title":"","nomobile":"0","name":"siteevent.organizer-info"}',
    ));

    //INSERT THE MY EVENTS LIST WIDGET
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.organizer-info',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"showInfo":["title","description"],"title":"","nomobile":"0","name":"siteevent.organizer-info"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '{"max":6}',
    ));
    $tab_id = $db->lastInsertId();


    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.host-events',
        'parent_content_id' => $tab_id,
        'order' => $widgetCount++,
        'params' => '{"title":"Events","titleCount":true,"statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"eventType":null,"category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["startDate","location","directionLink","viewCount","memberCount","reviewCount"],"typesOfViews":["listview","gridview","mapview"],"viewType":"gridview","titlePosition":"1","columnWidth":"215","columnHeight":"263","truncationLocation":"35","truncation":"100","truncationGrid":"100","itemCount":"12","ratingType":"rating_avg","nomobile":"0","name":"siteevent.host-events"}',
    ));
}

$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteevent_index_tagscloud")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (empty($page_id)) {

    $containerCount = 0;
    $widgetCount = 0;

    //CREATE PAGE
    $db->insert('engine4_core_pages', array(
        'name' => "siteevent_index_tagscloud",
        'displayname' => 'Advanced Events - Event Tags',
        'title' => 'Popular Tags',
        'description' => 'This is the event tags page.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $top_container_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $containerCount++,
    ));
    $main_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => $containerCount++,
    ));
    $top_middle_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => $containerCount++,
    ));
    $main_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.navigation-siteevent',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'seaocore.scroll-top',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.tagcloud-siteevent',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));
}

$aafModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
//Quary for update widget of seaocore activity feed.
if (!empty($aafModuleEnabled)) {
    $db->query("INSERT IGNORE INTO `engine4_advancedactivity_contents` ( `module_name`, `filter_type`, `resource_title`, `content_tab`, `order`, `default`) VALUES ('siteevent', 'siteevent', 'Event', '1', '999', '1');");
    $db->query("INSERT IGNORE INTO `engine4_advancedactivity_customtypes` ( `module_name`, `resource_type`, `resource_title`, `enabled`, `order`, `default`) VALUES ('siteevent', 'siteevent_event', 'Events', '1', '999', '1')");

    //NOT ADDING THE ELSE CASE BECAUSE WE ALREDY PASS VALUES ARRAY WHEN WE FETCH SETTING IN application/modules/Seaocore/views/scripts/feed/show-tooltip-info.tpl.
    $seaocoreActionLinks = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.action.link');
    if (!empty($seaocoreActionLinks)) {
        $seaocoreActionLinks = array_merge($seaocoreActionLinks, array("joinevent", "editevent", "inviteevent"));
        Engine_Api::_()->getApi('settings', 'core')->setSetting('seaocore.action.link', $seaocoreActionLinks);
    }
}

// Check if it's already been placed
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'siteevent_topic_view')
        ->limit(1);
;
$info = $select->query()->fetch();

if (empty($info)) {
    $db->insert('engine4_core_pages', array(
        'name' => 'siteevent_topic_view',
        'displayname' => 'Advanced Event - Discussion Topic View Page',
        'title' => 'View Event Discussion Topic',
        'description' => 'This is the view page for a event discussion.',
        'custom' => 0,
        'provides' => 'subject=siteevent_topic',
    ));
    $page_id = $db->lastInsertId('engine4_core_pages');

    // containers
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
    ));
    $container_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 3,
        'params' => '',
    ));
    $middle_id = $db->lastInsertId('engine4_core_content');

    // middle column content
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteevent.discussion-content',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '',
    ));
}

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteadvsearch'))
    $db->query("UPDATE `engine4_siteadvsearch_contents` SET `content_tab` = '0', `main_search` = '0'  WHERE `engine4_siteadvsearch_contents`.`module_name` ='event' LIMIT 1");

//START: PUT CALENDER WIDGET AUTOMATICALLY AT SITEEVENT PROFILE PAGE
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'siteevent_index_view')
        ->limit(1);
$page_id = $select->query()->fetchObject()->page_id;
if (!empty($page_id)) {

    $select = new Zend_Db_Select($db);
    $select_content = $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'siteevent.add-to-my-calendar-siteevent')
            ->limit(1);
    $content_id = $select_content->query()->fetchObject()->content_id;
    if (empty($content_id)) {
        $select = new Zend_Db_Select($db);
        $select_right = $select
                ->from('engine4_core_content')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'container')
                ->where('name = ?', 'right')
                ->limit(1);
        $right_id = $select_right->query()->fetchObject()->content_id;
        if (!empty($right_id)) {
            $select = new Zend_Db_Select($db);
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.add-to-my-calendar-siteevent',
                'parent_content_id' => $right_id,
                'order' => 5,
                'params' => '{"title":"","calendarOptions":["google","iCal","outlook","yahoo"],"nomobile":"0","name":"siteevent.add-to-my-calendar-siteevent"}',
            ));
        }
    }
}
//END: PUT CALENDER WIDGET AUTOMATICALLY AT SITEEVENT PROFILE PAGE 

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitevideointegration')
        ->where('enabled = ?', 1);
$is_sitevideointegration_object = $select->query()->fetchObject();
if ($is_sitevideointegration_object) {
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'siteevent.isActivate')
            ->where('value = ?', 1);
    $siteevent_isActivate_object = $select->query()->fetchObject();
    if ($siteevent_isActivate_object) {

        $db->query("INSERT IGNORE INTO `engine4_sitevideo_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('siteevent_event', 'event_id', 'siteevent', '0', '0', 'Events Videos', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
        $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("siteevent_admin_main_managevideo", "sitevideointegration", "Manage Videos", "", \'{"uri":"admin/sitevideo/manage-video/index/contentType/siteevent_event/contentModule/siteevent"}\', "siteevent_admin_main", "", 0, 0, 45);');
        $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitevideo.video.leader.owner.siteevent.event", "1");');

        $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '0' WHERE `engine4_core_menuitems`.`name` = 'siteevent_admin_submain_general_tab'");
    }
}

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'documentintegration')
        ->where('enabled = ?', 1);
$is_documentintegration_object = $select->query()->fetchObject();
if ($is_documentintegration_object) {
    $db->query("INSERT IGNORE INTO `engine4_document_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`) VALUES ('siteevent_event', 'event_id', 'siteevent', '0', '0', 'Event Documents')");
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("siteevent_admin_main_managedocument", "documentintegration", "Manage Documents", "", \'{"uri":"admin/document/manage-document/index/contentType/siteevent_event/contentModule/siteevent"}\', "siteevent_admin_main", "", 0, 0, 25);');
    $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "document.leader.owner.siteevent.event", "1");');
}