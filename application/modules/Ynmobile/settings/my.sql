INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynmobile', 'Mobile SocialEngine', '', '4.12', 1, 'extra') ;

CREATE TABLE IF NOT EXISTS `engine4_ynmobile_tokens` (
	`token_id` VARCHAR(64) NOT NULL,
	`user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`created_at` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`token_id`)
);

CREATE TABLE IF NOT EXISTS `engine4_ynmobile_userdevices` (
  `userdevice_id` varchar(45) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `token` varchar(64) DEFAULT NULL,
  `timestamp` int(10) NOT NULL,
  `platform` varchar(50) DEFAULT NULL,
  `device_id` tinytext NOT NULL,
  PRIMARY KEY (`userdevice_id`),
  KEY `user_id` (`user_id`),
  KEY `token` (`token`)
);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynmobile', 'ynmobile', ' YouNet Mobile', '', '{"route":"admin_default","module":"ynmobile","controller":"menus"}', 'core_admin_main_plugins', '', 999),
('ynmobile_admin_main_menus', 'ynmobile', 'Manage Menus','', '{"route":"admin_default","module":"ynmobile","controller":"menus"}', 'ynmobile_admin_main', '', 1),
('ynmobile_admin_main_settings', 'ynmobile', 'Global Settings','', '{"route":"admin_default","module":"ynmobile","controller":"settings"}', 'ynmobile_admin_main', '', 2),
('ynmobile_admin_main_notifications', 'ynmobile', 'Manage Notifications','', '{"route":"admin_default","module":"ynmobile","controller":"notifications"}', 'ynmobile_admin_main', '', 3),
('ynmobile_admin_main_subscription', 'ynmobile', 'Subscription Products','', '{"route":"admin_default","module":"ynmobile","controller":"subscription"}', 'ynmobile_admin_main', '', 4),
('ynmobile_admin_main_themes', 'ynmobile', 'Manage Themes','', '{"route":"admin_default","module":"ynmobile","controller":"themes"}', 'ynmobile_admin_main', '', 5);

--
-- Dumping data for table `engine4_ynmobile_menuitems`
--

DROP TABLE IF EXISTS `engine4_ynmobile_menuitems`;
CREATE TABLE IF NOT EXISTS `engine4_ynmobile_menuitems` (
  `menuitem_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `module` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'Core',
  `module_alt` varchar(32) NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `order` smallint(6) NOT NULL DEFAULT '999',
  `label` varchar(50) NOT NULL,
  `layout` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `uri` varchar(50) NOT NULL,
  `menu` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`menuitem_id`),
  UNIQUE KEY `name` (`name`),
  KEY `LOOKUP` (`name`,`order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=55 ;

--
-- Dumping data for table `engine4_ynmobile_menuitems`
--

INSERT INTO `engine4_ynmobile_menuitems` (`menuitem_id`, `name`, `module`, `module_alt`, `enabled`, `order`, `label`, `layout`, `icon`, `uri`, `menu`) VALUES
(29, 'activity', 'core', '', 1, 1, 'News Feed', 'activity', 'ion-card', '/app/newsfeed', 1),
(30, 'event', 'event', 'ynevent', 1, 999, 'Events', 'event', 'ion-ios-calendar-outline', '/app/events', 1),
(32, 'friends', 'core', '', 1, 3, 'Friends', 'friend', 'ion-ios-people-outline', '/app/friends', 1),
(33, 'photo', 'album', 'advalbum', 1, 999, 'Photos', 'photo', 'ion-ios-photos-outline', '/app/photos', 1),
(35, 'music', 'music', 'mp3music', 1, 4, 'Music', 'music', 'ion-ios-musical-notes', '/app/music_playlists', 1),
(36, 'video', 'video', 'ynvideo', 1, 999, 'Videos', 'video', 'ion-ios-film-outline', '/app/videos', 1),
(38, 'message', 'core', '', 1, 9, 'Messages', 'message', 'ion-ios-email-outline', '/app/messages', 1),
(41, 'forum', 'forum', 'ynforum', 1, 999, 'Forums', 'forum', 'ion-ios-bookmarks-outline', '/app/forums', 1),
(42, 'poll', 'poll', '', 1, 10, 'Polls', 'poll', 'ion-ios-analytics-outline', '/app/polls', 1),
(43, 'group', 'group', 'advgroup', 1, 999, 'Groups', 'group', 'ion-ios-circle-outline', '/app/groups', 1),
(44, 'blog', 'blog', 'ynblog', 1, 11, 'Blogs', 'blog', 'ion-ios-paper-outline', '/app/blogs', 1),
(45, 'classified', 'classified', '', 1, 12, 'Classifieds', 'classified', 'ion-ios-briefcase-outline', '/app/classifieds', 1),
(46, 'subscription', 'core', '', 1, 13, 'Memberships', 'subscription', 'ion-ribbon-b', '/app/subscriptions', 1),
(47, 'search', 'core', '', 1, 2, 'Search', 'search', 'ion-ios-search', '/app/search', 1),
(48, 'ynjobposting', 'ynjobposting', '', 1, 7, 'Job Posting', 'ynjobposting', 'ion-ios-glasses-outline', '/app/ynjobposting_jobs', 1),
(49, 'ynlisting', 'ynjobposting', '', 1, 6, 'Listings', 'ynlisting', 'ion-ios-pricetags-outline', '/app/listings', 1),
(50, 'ultimatenews', 'ultimatenews', '', 1, 5, 'Ultimate News', 'ultimatenews', 'ion-social-rss-outline', '/app/ultimatenews', 1),
(51, 'setting', 'core', '', 1, 14, 'Settings', 'setting', 'ion-ios-gear-outline', '/app/settings', 1),
(52, 'member', 'core', '', 1, 8, 'Members', 'member', 'ion-ios-personadd-outline', '/app/members', 1),
(53, 'ynbusinesspages', 'ynbusinesspages', '', 1, 15, 'Business Pages', 'ynbusinesspages', 'ion-cash', '/app/ynbusinesspages', 1),
(54, 'ynresume', 'ynresume', '', 1, 999, 'Resume', 'ynresume', 'ion-clipboard', '/app/ynresume', 1);

INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `form`, `enabled`, `priority`, `multi`) VALUES
('SE Mobile Video Encode', 'ynmobile_encode', 'ynmobile', 'Ynmobile_Plugin_Job_Encode', NULL, 1, 1, 1);

CREATE TABLE IF NOT EXISTS `engine4_ynmobile_maps` (
  `map_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci,
  `latitude` varchar(64),
  `longitude` varchar(64),
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`map_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES 
('ynmobile_checkin', 'ynmobile', '{item:$subject} - {var:$status} at {var:$location}', 1, 5, 1, 1, 1, 1);

-- ALL
-- auth_view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmobile_map' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmobile_map' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
 
-- USER
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmobile_map' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');


-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmobile_map' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

CREATE TABLE IF NOT EXISTS `engine4_ynmobile_storekitpurchases` (
    `storekitpurchase_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`storekitpurchase_key` VARCHAR(75) COMMENT 'which has been created manually on Apple Dev' ,
	`storekitpurchase_module_id` VARCHAR(75),
	`storekitpurchase_type` VARCHAR(255) DEFAULT 'purchase_product' COMMENT 'purchase product/sponsor/feature/...',
	`storekitpurchase_item_id` INT(10) UNSIGNED DEFAULT NULL COMMENT 'some modules need item id' ,
	PRIMARY KEY (`storekitpurchase_id`) 
);

--
-- profile cover table
--
CREATE TABLE IF NOT EXISTS `engine4_ynmobile_profilecovers` (
  `profilecover_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) DEFAULT 'iphone',
  `owner_type` varchar(64) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `photo_id` int(11) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`profilecover_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

UPDATE `engine4_core_menuitems` SET  `enabled` =  '1' WHERE  `engine4_core_menuitems`.`name` ='ynmobile_admin_main_menus';

CREATE TABLE IF NOT EXISTS `engine4_ynmobile_themes` (
  `theme_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `is_publish` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_test` tinyint(1) unsigned NOT NULL,
  `build_number` int(11) NOT NULL DEFAULT '0',
  `variables_text` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- default theme
--

INSERT IGNORE INTO `engine4_core_settings` (`name`,`value`) VALUES ('ynmobile.theme.number','0');

INSERT IGNORE INTO `engine4_ynmobile_themes` (`theme_id`, `name`, `is_publish`, `is_test`, `build_number`, `variables_text`, `creation_date`, `modified_date`) VALUES
(1, 'Default', 1, 0, 0, '{"positive":"#01a0db"}', '2015-06-11 06:41:46', '2015-06-11 06:41:46');
