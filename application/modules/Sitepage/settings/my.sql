/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('sitepage', 'Pages', 'Pages', '4.8.12p5', 1, 'extra');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('mobi_browse_sitepage', 'sitepage', 'Pages', 'Sitepage_Plugin_Menus::canViewSitepages', '{"route":"sitepage_general","action":"home"}', 'mobi_browse', '', 1, 0, 5);

-- --------------------------------------------------------
--
-- Dumping data for table `engine4_activity_actiontypes`
--
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES
('sitepage_post_self', 'sitepage', '{item:$object}\n{body:$body}', 1, 6, 1, 1, 1, 0, 1);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('sitepage_tagged', 'sitepage', '{item:$subject} tagged your page in a {item:$object:$label}.', 0, '', 1);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES
('sitepage_post', 'sitepage', '{actors:$subject:$object}:\r\n{body:$body}', 1, 3, 1, 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_mailtemplates`
--
 INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES
("sitepage_page_recurrence", "sitepage", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[page_title],[page_description],[object_link]");



INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`module`, `name`, `display`, `order`, `label`) VALUES
('sitepage', 'show', 1, 1, 'Show'),
('sitepage', 'closed', 1, 2, 'Status'),
('sitepage', 'orderby', 1, 3, 'Browse By'),
('sitepage', 'badge_id', 1, 4, 'Badge'),
('sitepage', 'search', 1, 5, 'Search Pages'),
('sitepage', 'location', 1, 6, 'Location'),
('sitepage', 'street', 1, 7, 'Street'),
('sitepage', 'city', 1, 8, 'City'),
('sitepage', 'state', 1, 9, 'State'),
('Sitepage', 'country', 1, 10, 'Country'),
('sitepage', 'locationmiles', 1, 11, 'Within Miles / Within Kilometers'),
('sitepage', 'price', 1, 12, 'Price'),
-- ('sitepage', 'profile_type', 1, 13, 'Page Profile Type'),
('sitepage', 'category_id', 0, 14, 'Category'),
('sitepage', 'has_photo', 1, 10000009, 'Only Pages With Photos');



INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGE_POSTNOTIFICATION_EMAIL", "sitepage", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepage_notificationpost', 'sitepage', '{item:$subject} posted in {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepage_activitycomment', 'sitepage', '{item:$subject} has commented on {var:$eventname}.', 0, '');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepage_activitylike', 'sitepage', '{item:$subject} has liked {var:$eventname}.', 0, '');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepage_contentlike', 'sitepage', '{item:$subject} has liked {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepage_contentcomment', 'sitepage', '{item:$subject} has commented on {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('follow_sitepage_page', 'sitepage', '{item:$subject} is following {item:$object}:', '0', '');

ALTER TABLE `engine4_sitepage_albums` CHANGE `type` `type` ENUM( 'note', 'overview','wall', 'announcements', 'discussions','cover' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL; 

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('sitepage_cover_update', 'sitepage', '{item:$subject} updated cover photo of the page {item:$object}:', 1, 3, 2, 1, 1, 1);
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`,`is_object_thumb`) VALUES
('sitepage_admin_cover_update', 'sitepage', '{item:$object} updated a new cover photo.', 1, 3, 2, 1, 1, 1, 1);


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepage_subpage_gutter_create", "sitepage", "Create New Sub Page", "Sitepage_Plugin_Menus", "", "sitepage_gutter", "", 1, 0, 999);


INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitepage_page' as `type`,
    'auth_sspcreate' as `name`,
    5 as `value`,
    '["registered","owner_network","owner_member_member","owner_member","owner", "member", "like_member"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitepage_page' as `type`,
    'sspcreate' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGE_EMAILME_EMAIL", "sitepage", "[host],[sender_email],[sender_name],[page_title],[message],[object_link]");



INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('sitepage_dashboard', 'standard', 'Page Dashboard Menu', '999');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitepage_dashboard_getstarted', 'sitepage', 'Get Started', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard","action":"get-started"}', 'sitepage_dashboard', '', 1, 0, 1),
('sitepage_dashboard_editinfo', 'sitepage', 'Edit Info', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_edit"}', 'sitepage_dashboard', '', 1, 0, 2),
('sitepage_dashboard_profilepicture', 'sitepage', 'Profile Picture', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"profile-picture"}', 'sitepage_dashboard', '', 1, 0, 3),
('sitepage_dashboard_overview', 'sitepage', 'Overview', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"overview"}', 'sitepage_dashboard', '', 1, 0, 4),
('sitepage_dashboard_contact', 'sitepage', 'Contact Details', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"contact"}', 'sitepage_dashboard', '', 1, 0, 5),
('sitepage_dashboard_managememberroles', 'sitepage', 'Manage Member Roles', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"manage-member-category"}', 'sitepage_dashboard', '', 1, 0, 6),
('sitepage_dashboard_announcements', 'sitepage', 'Manage Announcements', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"announcements"}', 'sitepage_dashboard', '', 1, 0, 7),
('sitepage_dashboard_alllocation', 'sitepage', 'Location', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"all-location"}', 'sitepage_dashboard', '', 1, 0, 8),
('sitepage_dashboard_editlocation', 'sitepage', 'Location', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"edit-location"}', 'sitepage_dashboard', '', 1, 0, 9),
('sitepage_dashboard_profiletype', 'sitepage', 'Profile Info', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"profile-type"}', 'sitepage_dashboard', '', 1, 0, 10),
('sitepage_dashboard_apps', 'sitepage', 'Apps', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"app"}', 'sitepage_dashboard', '', 1, 0, 11),
('sitepage_dashboard_marketing', 'sitepage', 'Marketing', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"marketing"}', 'sitepage_dashboard', '', 1, 0, 12),
('sitepage_dashboard_badge', 'sitepage', 'Badge', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepagebadge_request"}', 'sitepage_dashboard', '', 1, 0, 13),
('sitepage_dashboard_notificationsettings', 'sitepage', 'Manage Notifications', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"notification-settings"}', 'sitepage_dashboard', '', 1, 0, 14),
('sitepage_dashboard_insights', 'sitepage', 'Insights', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_insights"}', 'sitepage_dashboard', '', 1, 0, 15),
('sitepage_dashboard_reports', 'sitepage', 'Reports', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_reports"}', 'sitepage_dashboard', '', 1, 0, 16),
('sitepage_dashboard_manageadmins', 'sitepage', 'Manage Admins', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_manageadmins", "action":"index"}', 'sitepage_dashboard', '', 1, 0, 17),
('sitepage_dashboard_featuredowners', 'sitepage', 'Featured Admins', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"featured-owners"}', 'sitepage_dashboard', '', 1, 0, 18),
('sitepage_dashboard_editstyle', 'sitepage', 'Edit Style', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"edit-style"}', 'sitepage_dashboard', '', 1, 0, 19),
('sitepage_dashboard_editlayout', 'sitepage', 'Edit Layout', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_layout"}', 'sitepage_dashboard', '', 1, 0, 20),
('sitepage_dashboard_updatepackages', 'sitepage', 'Packages', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_packages", "action":"update-package"}', 'sitepage_dashboard', '', 1, 0, 21);