INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES ('replyrate', 'Reply Rate', 'Reply Rate', '4.0.0', 1, 'extra');

SET @iMaxOrder=(SELECT MAX(`order`) + 1 FROM `engine4_core_menuitems` WHERE `menu` LIKE "core_admin_main_plugins");

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('core_admin_main_plugins_replyrate', 'replyrate', 'Reply Rate', '', '{"route":"admin_default","module":"replyrate","controller":"settings"}', 'core_admin_main_plugins', '', 1, 0, @iMaxOrder);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('replyrate_admin_main_settings', 'replyrate', 'Main Settings', '', '{"route":"admin_default","module":"replyrate","controller":"settings"}', 'replyrate_admin_main', '', 1, 0, 1);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('replyrate_admin_main_level', 'replyrate', 'Member Level Settings', '', '{"route":"admin_default","module":"replyrate","controller":"settings","action":"level"}', 'replyrate_admin_main', '', 1, 0, 2);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('replyrate_admin_main_more', 'replyrate', 'More Plugins', '', '{"route":"admin_default","module":"replyrate","controller":"settings","action":"more"}', 'replyrate_admin_main', '', 1, 0, 3);

INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (1, 'replyrate', 'view', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (2, 'replyrate', 'view', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (4, 'replyrate', 'view', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (3, 'replyrate', 'view', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (5, 'replyrate', 'view', 1, NULL);