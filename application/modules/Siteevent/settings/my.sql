
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('siteevent', 'Advanced Events', 'Advanced Events', '4.8.10p4', 1, 'extra');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('siteevent_main_categories', 'siteevent', 'Categories', 'Siteevent_Plugin_Menus::canViewCategories', '{"route":"siteevent_review_categories","action":"categories"}', 'siteevent_main', '', 1, 0, 8),
('siteevent_main_reviews', 'siteevent', 'Browse Reviews', 'Siteevent_Plugin_Menus::canViewBrosweReview', '{"route":"siteevent_review_browse", "action":"browse"}', 'siteevent_main', '', 1, 0, 10),
('siteevent_main_diaries', 'siteevent', 'Diaries', 'Siteevent_Plugin_Menus::canViewDiary', '{"route":"siteevent_diary_general","action":"browse"}', 'siteevent_main', '', 1, 0, 11),
('siteevent_main_editors', 'siteevent', 'Editors', 'Siteevent_Plugin_Menus::canViewEditors', '{"route":"siteevent_review_editor","action":"home"}', 'siteevent_main', '', 1, 0, 10),
('siteevent_main_home', 'siteevent', 'Events Home', 'Siteevent_Plugin_Menus::canViewSiteevents', '{"route":"siteevent_general","action":"home"}', 'siteevent_main', '', 1, 0, 1),
('siteevent_main_browse', 'siteevent', 'Browse Events', 'Siteevent_Plugin_Menus::canViewSiteevents', '{"route":"siteevent_general","action":"index"}', 'siteevent_main', '', 1, 0, 2),
('siteevent_main_pinboard', 'siteevent', 'Events Pinboard', 'Siteevent_Plugin_Menus::canViewSiteevents', '{"route":"siteevent_general","action":"pinboard"}', 'siteevent_main', '', 1, 0,4),
('siteevent_main_browse_location', 'siteevent', 'Locations', 'Siteevent_Plugin_Menus::canViewSiteevents', '{"route":"siteevent_general","action":"map"}', 'siteevent_main', '', 1, 0, 3),
('siteevent_main_manage', 'siteevent', 'My Events', 'Siteevent_Plugin_Menus::canCreateSiteevents', '{"route":"siteevent_general","action":"manage"}', 'siteevent_main', '', 1, 0, 6),
('siteevent_main_create', 'siteevent', 'Create New Event', 'Siteevent_Plugin_Menus::canCreateSiteevents', '{"route":"siteevent_general","action":"create"}', 'siteevent_main', '', 1, 0, 7),
('user_profile_diary', 'siteevent', 'Diaries', 'Siteevent_Plugin_Menus::userProfileDiary', '', 'user_profile', '', '1', '0', 999),
('mobi_browse_siteevent', 'siteevent', 'Events', 'Siteevent_Plugin_Menus::canViewSiteevents', '{"route":"siteevent_general","action":"home"}', 'mobi_browse', '', 1, 0, 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('siteevent_gutter_invite', 'siteevent', 'Invite Guests', 'Siteevent_Plugin_Menus::siteeventGutterInvite', '', 'siteevent_gutter', '', 1, 0, 1),
('siteevent_gutter_member', 'siteevent', 'Join Button', 'Siteevent_Plugin_Menus::siteeventGutterMember', '', 'siteevent_gutter', '', 1, 0, 2),
('siteevent_gutter_diary', 'siteevent', 'Add to Diary', 'Siteevent_Plugin_Menus::siteeventGutterDiary', '', 'siteevent_gutter', '', 1, 0, 2),
('siteevent_gutter_messageowner', 'siteevent', 'Message Owner', 'Siteevent_Plugin_Menus::siteeventGutterMessageowner', '', 'siteevent_gutter', '', 0, 0, 2),
('siteevent_gutter_print', 'siteevent', 'Print This Event', 'Siteevent_Plugin_Menus::siteeventGutterPrint', '', 'siteevent_gutter', '', 0, 0, 3),
('siteevent_gutter_share', 'siteevent', 'Share This Event', 'Siteevent_Plugin_Menus::siteeventGutterShare', '', 'siteevent_gutter', '', 0, 0, 4),
('siteevent_gutter_tfriend', 'siteevent', 'Tell a Friend', 'Siteevent_Plugin_Menus::siteeventGutterTfriend', '', 'siteevent_gutter', '', 0, 0, 5),
('siteevent_gutter_report', 'siteevent', 'Report This Event', 'Siteevent_Plugin_Menus::siteeventGutterReport', '', 'siteevent_gutter', '', 0, 0, 6),
('siteevent_gutter_edit', 'siteevent', 'Dashboard', 'Siteevent_Plugin_Menus::siteeventGutterEdit', '', 'siteevent_gutter', '', 1, 0, 7),
('siteevent_gutter_editoverview', 'siteevent', 'Edit Overview', 'Siteevent_Plugin_Menus::siteeventGutterEditoverview', '', 'siteevent_gutter', '', 0, 0, 8),
('siteevent_gutter_editstyle', 'siteevent', 'Edit Style', 'Siteevent_Plugin_Menus::siteeventGutterEditstyle', '', 'siteevent_gutter', '', 0, 0, 9),
('siteevent_gutter_close', 'siteevent', 'Re-publish / Cancel', 'Siteevent_Plugin_Menus::siteeventGutterClose', '', 'siteevent_gutter', '', 1, 0, 10),
('siteevent_gutter_publish', 'siteevent', 'Publish Event', 'Siteevent_Plugin_Menus::siteeventGutterPublish', '', 'siteevent_gutter', '', 1, 0, 11),
('siteevent_gutter_delete', 'siteevent', 'Delete Event', 'Siteevent_Plugin_Menus::siteeventGutterDelete', '', 'siteevent_gutter', '', 1, 0, 12),
('siteevent_gutter_review', 'siteevent', 'Write / Edit a Editor Review', 'Siteevent_Plugin_Menus::siteeventGutterReview', '', 'siteevent_gutter', '', 1, 0, 14);

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('siteevent_gutter_notifications', 'siteevent', 'Notification Settings', 'Siteevent_Plugin_Menus::siteeventGutterNotificationSettings', '', 'siteevent_gutter', NULL, '1', '0', '999');


INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES
('siteevent_review_write', 'siteevent', '{item:$subject} has written a {item:$object:review} for the {itemParent:$object::event}.', '');


INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('siteevent_userreview_add', 'siteevent', '{item:$subject} rated and wrote a review for {var:$username} in event {item:$object}:', 1, 7, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_siteevent_userreviews`
--

DROP TABLE IF EXISTS `engine4_siteevent_userreviews`;
CREATE TABLE IF NOT EXISTS `engine4_siteevent_userreviews` (
  `userreview_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `viewer_id` int(10) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `rating` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`userreview_id`),
  UNIQUE KEY `event_id` (`event_id`,`user_id`,`viewer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('siteevent_page_invite', 'siteevent', '{item:$subject} has invited you to the page event {item:$object}.', 0, '', 1);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEEVENT_PAGE_INVITE_EMAIL", "siteevent", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('siteevent_business_invite', 'siteevent', '{item:$subject} has invited you to the business event {item:$object}.', 0, '', 1);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEEVENT_BUSINESS_INVITE_EMAIL", "siteevent", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('siteevent_group_invite', 'siteevent', '{item:$subject} has invited you to the group event {item:$object}.', 0, '', 1);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEEVENT_GROUP_INVITE_EMAIL", "siteevent", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");

  
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 

("siteevent_dashboard_editinfo", "siteevent", "Edit Info", "Siteevent_Plugin_Dashboardmenus", '{"route":"siteevent_specific", "action":"edit"}', "siteevent_dashboard_content", NULL, "1", "0", "10"),

("siteevent_dashboard_overview", "siteevent", "Overview", "Siteevent_Plugin_Dashboardmenus", '{"route":"siteevent_specific", "action":"overview"}', "siteevent_dashboard_content", NULL, "1", "0", "20"),
 
("siteevent_dashboard_waitlist", "siteevent", "Capacity & Waitlist", "Siteevent_Plugin_Dashboardmenus", '', "siteevent_dashboard_content", NULL, 1, 0, 30),

("siteevent_dashboard_profilepicture", "siteevent", "Profile Picture", "Siteevent_Plugin_Dashboardmenus", '{"route":"siteevent_dashboard", "action":"change-photo"}', "siteevent_dashboard_content", NULL, "1", "0", "40"),

("siteevent_dashboard_contact", "siteevent", "Contact Details", "Siteevent_Plugin_Dashboardmenus", '{"route":"siteevent_dashboard", "action":"contact"}', "siteevent_dashboard_content", NULL, "1", "0", "50"),

("siteevent_dashboard_editlocation", "siteevent", "Location", "Siteevent_Plugin_Dashboardmenus", '{"route":"siteevent_specific", "action":"editlocation"}', "siteevent_dashboard_content", NULL, "1", "0", "60"),

("siteevent_dashboard_editphoto", "siteevent", "Photos", "Siteevent_Plugin_Dashboardmenus", '{"route":"siteevent_albumspecific"}', "siteevent_dashboard_content", NULL, "1", "0", "70"),

("siteevent_dashboard_editvideo", "siteevent", "Videos", "Siteevent_Plugin_Dashboardmenus", '{"route":"siteevent_videospecific"}', "siteevent_dashboard_content", NULL, "1", "0", "80"),

("siteevent_dashboard_announcements", "siteevent", "Announcements", "Siteevent_Plugin_Dashboardmenus", '{"route":"siteevent_extended", "action":"manage", "controller":"announcement"}', "siteevent_dashboard_content", NULL, "1", "0", "90"),

("siteevent_dashboard_editmetakeyword", "siteevent", "Meta Keywords", "Siteevent_Plugin_Dashboardmenus", '{"route":"siteevent_dashboard", "action":"meta-detail"}', "siteevent_dashboard_content", NULL, "1", "0", "95"), 

("siteevent_dashboard_editstyle", "siteevent", "Edit Style", "Siteevent_Plugin_Dashboardmenus", '{"route":"siteevent_specific", "action":"editstyle"}', "siteevent_dashboard_content", NULL, "1", "0", "100"),

("siteevent_dashboard_manageleaders", "siteevent", "Manage Leaders", "Siteevent_Plugin_Dashboardmenus", '{"route":"siteevent_extended", "action":"manage-leaders", "controller":"member"}', "siteevent_dashboard_admin", NULL, "1", "0", "120"),

("siteevent_dashboard_notificationsettings", "siteevent", "Notification Settings", "Siteevent_Plugin_Dashboardmenus", '{"route":"siteevent_dashboard", "action":"notification-settings"}', "siteevent_dashboard_admin", NULL, "1", "0", "130");

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('siteevent_dashboard_content', 'standard', 'Advanced Events - Dashboard Navigation (Content)'),
('siteevent_dashboard_admin', 'standard', 'Advanced Events - Dashboard Navigation (Admin)');
