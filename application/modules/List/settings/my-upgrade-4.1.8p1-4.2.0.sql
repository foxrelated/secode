INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('list_change_photo', 'list', '{item:$subject} changed their listing profile picture:', 1, 3, 2, 1, 1, 1);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('list_admin_main_import', 'list', 'Import', '', '{"route":"admin_default","module":"list","controller":"importlisting"}', 'list_admin_main', '', 1, 0, 7);

DROP TABLE IF EXISTS `engine4_list_importfiles`;
CREATE TABLE IF NOT EXISTS `engine4_list_importfiles` (
  `importfile_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `first_import_id` int(11) unsigned NOT NULL,
  `last_import_id` int(11) unsigned NOT NULL,
  `current_import_id` int(11) unsigned NOT NULL,
  `first_listing_id` int(11) unsigned NOT NULL,
  `last_listing_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `view_privacy` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `comment_privacy` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`importfile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `engine4_list_imports`;
CREATE TABLE IF NOT EXISTS `engine4_list_imports` (
  `import_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `sub_category` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8_unicode_ci NOT NULL,
  `price` int(11) NOT NULL DEFAULT '0',
  `location` text COLLATE utf8_unicode_ci,
  `overview` text COLLATE utf8_unicode_ci,
  `tags` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`import_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;