INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynresponsivemetro', 'YN - Responsive Metro Template', 'Responsive Metro Template', '4.01p2', 1, 'extra') ;

UPDATE `engine4_core_content` SET `name` = 'ynresponsivemetro.metro-mini-menu' where `name` = 'ynresponsive1.metro-mini-menu';
UPDATE `engine4_core_content` SET `name` = 'ynresponsivemetro.metro-main-menu' where `name` = 'ynresponsive1.metro-main-menu';
UPDATE `engine4_core_content` SET `name` = 'ynresponsivemetro.metro-blocks' where `name` = 'ynresponsive1.metro-blocks';
UPDATE `engine4_core_content` SET `name` = 'ynresponsivemetro.metro-introduction' where `name` = 'ynresponsive1.metro-introduction';
UPDATE `engine4_core_content` SET `name` = 'ynresponsivemetro.metro-members' where `name` = 'ynresponsive1.metro-members';
UPDATE `engine4_core_content` SET `name` = 'ynresponsivemetro.metro-featured-photos' where `name` = 'ynresponsive1.metro-featured-photos';
UPDATE `engine4_core_content` SET `name` = 'ynresponsivemetro.metro-va-info' where `name` = 'ynresponsive1.metro-va-info';
UPDATE `engine4_core_content` SET `name` = 'ynresponsivemetro.metro-groups' where `name` = 'ynresponsive1.metro-groups';
UPDATE `engine4_core_content` SET `name` = 'ynresponsivemetro.metro-blogs' where `name` = 'ynresponsive1.metro-blogs';
UPDATE `engine4_core_content` SET `name` = 'ynresponsivemetro.metro-events' where `name` = 'ynresponsive1.metro-events';
UPDATE `engine4_core_content` SET `name` = 'ynresponsivemetro.metro-footer-menu' where `name` = 'ynresponsive1.metro-footer-menu';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynresponsivemetro', 'ynresponsivemetro', 'YN - Responsive Metro', '', '{"route":"admin_default","module":"ynresponsivemetro","controller":"menus"}', 'core_admin_main_plugins', '', 999);

DELETE FROM `engine4_core_menuitems` WHERE `name` = 'ynresponsive1_admin_main_manage_menus';
DELETE FROM `engine4_core_menuitems` WHERE `name` = 'ynresponsive1_admin_main_manage_blocks';
DELETE FROM `engine4_core_menuitems` WHERE `name` = 'ynresponsive1_admin_main_manage_introduction';
DELETE FROM `engine4_core_menuitems` WHERE `name` = 'ynresponsive1_admin_main_manage_featured_photos';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('ynresponsivemetro_admin_main_manage_menus', 'ynresponsivemetro', 'Manage Menus', '', '{"route":"admin_default","module":"ynresponsivemetro","controller":"menus"}', 'ynresponsivemetro_admin_main', '', 1, 0, 1),
('ynresponsivemetro_admin_main_manage_blocks', 'ynresponsivemetro', 'Manage Blocks', '', '{"route":"admin_default","module":"ynresponsivemetro","controller":"manage-blocks"}', 'ynresponsivemetro_admin_main', '', 1, 0, 2),
('ynresponsivemetro_admin_main_manage_introduction', 'ynresponsivemetro', 'Manage Introduction', '', '{"route":"admin_default","module":"ynresponsivemetro","controller":"manage-introduction"}', 'ynresponsivemetro_admin_main', '', 1, 0, 3),
('ynresponsivemetro_admin_main_manage_featured_photos', 'ynresponsivemetro', 'Manage Featured Photos', '', '{"route":"admin_default","module":"ynresponsivemetro","controller":"manage-featured-photos"}', 'ynresponsivemetro_admin_main', '', 1, 0, 4);

CREATE TABLE IF NOT EXISTS `engine4_ynresponsive1_metroblocks` (
  `metroblock_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `icon` varchar(256) DEFAULT NULL,
  `link` varchar(256) DEFAULT NULL,
  `photo_id` int(11) NOT NULL DEFAULT '0',
  `block` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`metroblock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;