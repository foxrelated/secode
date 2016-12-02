/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
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
('sitegroup', 'Groups / Communities', 'Groups / Communities', '4.8.12p3', 1, 'extra');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('mobi_browse_sitegroup', 'sitegroup', 'Groups', 'Sitegroup_Plugin_Menus::canViewSitegroups', '{"route":"sitegroup_general","action":"home"}', 'mobi_browse', '', 1, 0, 5);

-- --------------------------------------------------------
--
-- Dumping data for table `engine4_activity_actiontypes`
--
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES
('sitegroup_post_self', 'sitegroup', '{item:$object}\n{body:$body}', 1, 6, 1, 1, 1, 0, 1);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('sitegroup_tagged', 'sitegroup', '{item:$subject} tagged your group in a {item:$object:$label}.', 0, '', 1);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES
('sitegroup_post', 'sitegroup', '{actors:$subject:$object}:\r\n{body:$body}', 1, 3, 1, 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_mailtemplates`
--
 INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES
("sitegroup_group_recurrence", "sitegroup", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[group_title],[group_description],[object_link]");



INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`module`, `name`, `display`, `order`, `label`) VALUES
('sitegroup', 'show', 1, 1, 'Show'),
('sitegroup', 'closed', 1, 2, 'Status'),
('sitegroup', 'orderby', 1, 3, 'Browse By'),
('sitegroup', 'badge_id', 1, 4, 'Badge'),
('sitegroup', 'search', 1, 5, 'Search Groups'),
('sitegroup', 'location', 1, 6, 'Location'),
('sitegroup', 'street', 1, 7, 'Street'),
('sitegroup', 'city', 1, 8, 'City'),
('sitegroup', 'state', 1, 9, 'State'),
('Sitegroup', 'country', 1, 10, 'Country'),
('sitegroup', 'locationmiles', 1, 11, 'Within Miles / Within Kilometers'),
('sitegroup', 'price', 1, 12, 'Price'),
-- ('sitegroup', 'profile_type', 1, 13, 'Group Profile Type'),
('sitegroup', 'category_id', 0, 14, 'Category'),
('sitegroup', 'has_photo', 1, 10000009, 'Only Groups With Photos');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEGROUP_POSTNOTIFICATION_EMAIL", "sitegroup", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitegroup_notificationpost', 'sitegroup', '{item:$subject} posted in {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitegroup_activitycomment', 'sitegroup', '{item:$subject} has commented on {var:$eventname}.', 0, '');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitegroup_activitylike', 'sitegroup', '{item:$subject} has liked {var:$eventname}.', 0, '');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitegroup_contentlike', 'sitegroup', '{item:$subject} has liked {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitegroup_contentcomment', 'sitegroup', '{item:$subject} has commented on {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('follow_sitegroup_group', 'sitegroup', '{item:$subject} is following {item:$object}:', '0', '');


ALTER TABLE `engine4_sitegroup_albums` CHANGE `type` `type` ENUM( 'note', 'overview','wall', 'announcements', 'discussions','cover' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL; 

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('sitegroup_cover_update', 'sitegroup', '{item:$subject} updated cover photo of the group {item:$object}:', 1, 3, 2, 1, 1, 1);
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`,`is_object_thumb`) VALUES
('sitegroup_admin_cover_update', 'sitegroup', '{item:$object} updated a new cover photo.', 1, 3, 2, 1, 1, 1, 1);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitegroup_main_pinboardbrowse', 'sitegroup', 'Pinboard', 'Sitegroup_Plugin_Menus::canViewSitegroups', '{"route":"sitegroup_general","action":"pinboard-browse"}', 'sitegroup_main', '', 1, 0, 3);



INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroup_subgroup_gutter_create", "sitegroup", "Create New Sub Group", "Sitegroup_Plugin_Menus", "", "sitegroup_gutter", "", 1, 0, 999);


INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitegroup_group' as `type`,
    'auth_sspcreate' as `name`,
    5 as `value`,
    '["registered","owner_network","owner_member_member","owner_member","owner", "member", "like_member"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitegroup_group' as `type`,
    'sspcreate' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');


INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitegroupalbum', 'Groups / Communities - Albums', 'Groups / Communities - Albums', '4.8.9p2', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `order` )VALUES
 ('sitegroup_main_album', 'sitegroupalbum', 'Albums', 'Sitegroupalbum_Plugin_Menus::canViewAlbums', '{"route":"sitegroupalbum_home","action":"home"}', 'sitegroup_main', '', 1, '19');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_seaocore_tabs`
--

INSERT IGNORE INTO `engine4_seaocore_tabs` (`module` ,`type` ,`name` ,`title` ,`enabled` ,`order` ,`limit`)VALUES
('sitegroupalbum', 'albums', 'recent_groupalbums', 'Recent', '1', '1', '24'),
('sitegroupalbum', 'albums', 'liked_groupalbums', 'Most Liked', '1', '2', '24'),
('sitegroupalbum', 'albums', 'viewed_groupalbums', 'Most Viewed', '1', '3', '24'),
('sitegroupalbum', 'albums', 'commented_groupalbums', 'Most Commented', '0', '4', '24'),
('sitegroupalbum', 'albums', 'featured_groupalbums', 'Featured', '0', '5', '24'),
('sitegroupalbum', 'albums', 'random_groupalbums', 'Random', '0', '6', '24'),
('sitegroupalbum', 'photos', 'recent_groupphotos', 'Recent', '1', '1', '24'),
('sitegroupalbum', 'photos', 'liked_groupphotos', 'Most Liked', '1', '2', '24'),
('sitegroupalbum', 'photos', 'viewed_groupphotos', 'Most Viewed', '1', '3', '24'),
('sitegroupalbum', 'photos', 'commented_groupphotos', 'Most Commented', '0', '4', '24'),
('sitegroupalbum', 'photos', 'featured_groupphotos', 'Featured', '0', '5', '24'),
('sitegroupalbum', 'photos', 'random_groupphotos', 'Random', '0', '6', '24');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitegroupalbum_create', 'sitegroupalbum', '{item:$subject} has created a group album {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitegroupalbum_admin_main_settings', 'sitegroupalbum', 'General Settings', '', '{"route":"admin_default","module":"sitegroupalbum","controller":"settings"}', 'sitegroupalbum_admin_main', '', 1, 0, 1);

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('sitegroup_album', 10),
('sitegroup_photos',100),
('sitegroup.mostrecent.photos', 7),
('sitegroup.mostcommented.photos', 4),
('sitegroup.mostliked.photos',4);

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitegroup_album' as `type`,
    'auth_tag' as `name`,
    5 as `value`,
    '["everyone", "registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'sitegroup_album' as `type`,
  'tag' as `name`,
  1 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'sitegroup_album' as `type`,
  'tag' as `name`,
  2 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');


INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'sitegroup_album' as `type`,
  'tag' as `name`,
  0 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitegroup_album' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitegroup_album' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('sitegroupalbum_photo_new', 'sitegroupalbum', '{item:$subject} added {var:$count} photo(s) to the album {var:$linked_album_title} of group {item:$object}:', 1, 3, 2, 1, 1, 1);

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitegroupmember', 'Groups / Communities - Group Members', 'Groups / Communities - Group Members', '4.8.9p2', 1, 'extra') ;


DROP TABLE IF EXISTS `engine4_sitegroupmember_roles`;
CREATE TABLE IF NOT EXISTS `engine4_sitegroupmember_roles` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `is_admincreated` tinyint(1) NOT NULL,
  `group_id` int(11) NOT NULL,
  `group_category_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `engine4_sitegroup_membership`;
CREATE TABLE IF NOT EXISTS `engine4_sitegroup_membership` (
	`member_id` int(11) NOT NULL AUTO_INCREMENT,
	`resource_id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	`active` tinyint(1) NOT NULL DEFAULT '1',
	`resource_approved` tinyint(1) NOT NULL DEFAULT '1',
	`user_approved` tinyint(1) NOT NULL DEFAULT '1',
	`title` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
	`role_id` VARCHAR( 255 ) NOT NULL,
	`date` date DEFAULT NULL,
	`highlighted` tinyint(1) NOT NULL DEFAULT '0',
	`featured` tinyint(1) NOT NULL DEFAULT '0',
	`join_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`group_id` int(11) NOT NULL,
	`notification` tinyint(1) NOT NULL DEFAULT '1',
	`action_email` VARCHAR( 255 ) NULL,
	`action_notification` VARCHAR( 255 ) NULL,
	`email` TINYINT( 1 ) NOT NULL DEFAULT '1',
	PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `engine4_sitegroup_announcements`;
CREATE TABLE IF NOT EXISTS `engine4_sitegroup_announcements` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `startdate` date NOT NULL,
  `expirydate` date NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`announcement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitegroup_group' as `type`,
    'smecreate' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels`;
  
INSERT IGNORE INTO `engine4_seaocore_tabs` (`module` ,`type` ,`name` ,`title` ,`enabled` ,`order` ,`limit`)VALUES
('sitegroupmember', 'member', 'recent_groupmembers', 'Recent Group Members', '1', '1', '10'),
('sitegroupmember', 'member', 'featured_groupmembers', 'Featured Group Members', '1', '2', '10');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`,`order`) VALUES
('sitegroupmember_admin_settings', 'sitegroupmember', 'General Settings', '', '{"route":"admin_default","module":"sitegroupmember","controller":"settings","action":"index"}', 'sitegroupmember_admin_main', '', 1);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitegroup_gutter_manage_joined_members', 'sitegroupmember', 'Manage Joined Groups', 'Sitegroupmember_Plugin_Menus', '', 'user_home', '', 0, 0, 999);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEGROUP_EMAILME_EMAIL", "sitegroup", "[host],[sender_email],[sender_name],[group_title],[message],[object_link]");

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('sitegroup_dashboard', 'standard', 'Group Dashboard Menu', '999');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitegroup_dashboard_getstarted', 'sitegroup', 'Get Started', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard","action":"get-started"}', 'sitegroup_dashboard', '', 1, 0, 1),
('sitegroup_dashboard_editinfo', 'sitegroup', 'Edit Info', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_edit"}', 'sitegroup_dashboard', '', 1, 0, 2),
('sitegroup_dashboard_profilepicture', 'sitegroup', 'Profile Picture', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"profile-picture"}', 'sitegroup_dashboard', '', 1, 0, 3),
('sitegroup_dashboard_overview', 'sitegroup', 'Overview', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"overview"}', 'sitegroup_dashboard', '', 1, 0, 4),
('sitegroup_dashboard_contact', 'sitegroup', 'Contact Details', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"contact"}', 'sitegroup_dashboard', '', 1, 0, 5),
('sitegroup_dashboard_managememberroles', 'sitegroup', 'Manage Member Roles', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"manage-member-category"}', 'sitegroup_dashboard', '', 1, 0, 6),
('sitegroup_dashboard_announcements', 'sitegroup', 'Manage Announcements', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"announcements"}', 'sitegroup_dashboard', '', 1, 0, 7),
('sitegroup_dashboard_alllocation', 'sitegroup', 'Location', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"all-location"}', 'sitegroup_dashboard', '', 1, 0, 8),
('sitegroup_dashboard_editlocation', 'sitegroup', 'Location', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"edit-location"}', 'sitegroup_dashboard', '', 1, 0, 9),
('sitegroup_dashboard_profiletype', 'sitegroup', 'Profile Info', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"profile-type"}', 'sitegroup_dashboard', '', 1, 0, 10),
('sitegroup_dashboard_apps', 'sitegroup', 'Apps', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"app"}', 'sitegroup_dashboard', '', 1, 0, 11),
('sitegroup_dashboard_marketing', 'sitegroup', 'Marketing', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"marketing"}', 'sitegroup_dashboard', '', 1, 0, 12),
('sitegroup_dashboard_badge', 'sitegroup', 'Badge', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroupbadge_request"}', 'sitegroup_dashboard', '', 1, 0, 13),
('sitegroup_dashboard_notificationsettings', 'sitegroup', 'Manage Notifications', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"notification-settings"}', 'sitegroup_dashboard', '', 1, 0, 14),
('sitegroup_dashboard_insights', 'sitegroup', 'Insights', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_insights"}', 'sitegroup_dashboard', '', 1, 0, 15),
('sitegroup_dashboard_reports', 'sitegroup', 'Reports', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_reports"}', 'sitegroup_dashboard', '', 1, 0, 16),
('sitegroup_dashboard_manageadmins', 'sitegroup', 'Manage Admins', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_manageadmins", "action":"index"}', 'sitegroup_dashboard', '', 1, 0, 17),
('sitegroup_dashboard_featuredowners', 'sitegroup', 'Featured Admins', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"featured-owners"}', 'sitegroup_dashboard', '', 1, 0, 18),
('sitegroup_dashboard_editstyle', 'sitegroup', 'Edit Style', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"edit-style"}', 'sitegroup_dashboard', '', 1, 0, 19),
('sitegroup_dashboard_editlayout', 'sitegroup', 'Edit Layout', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_layout"}', 'sitegroup_dashboard', '', 1, 0, 20),
('sitegroup_dashboard_updatepackages', 'sitegroup', 'Packages', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_packages", "action":"update-package"}', 'sitegroup_dashboard', '', 1, 0, 21);
