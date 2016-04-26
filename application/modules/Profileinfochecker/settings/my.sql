INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES ('profileinfochecker', 'Profile Info Checker', 'Profile Info Checker', '4.0.0', 1, 'extra');

SET @iMaxOrder=(SELECT MAX(`order`) + 1 FROM `engine4_core_menuitems` WHERE `menu` LIKE "core_admin_main_plugins");

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('core_admin_main_plugins_profileinfochecker', 'profileinfochecker', 'Profile Info Checker', '', '{"route":"admin_default","module":"profileinfochecker","controller":"settings"}', 'core_admin_main_plugins', '', 1, 0, @iMaxOrder);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('profileinfochecker_admin_main_settings', 'profileinfochecker', 'Main Settings', '', '{"route":"admin_default","module":"profileinfochecker","controller":"settings"}', 'profileinfochecker_admin_main', '', 1, 0, 1);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('profileinfochecker_admin_main_more', 'profileinfochecker', 'More Plugins', '', '{"route":"admin_default","module":"profileinfochecker","controller":"settings","action":"more"}', 'profileinfochecker_admin_main', '', 1, 0, 2);