INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES ('winkgreeting', 'Wink and Greeting', 'Wink and Greeting', '4.0.2', 1, 'extra');

SET @iMaxOrder=(SELECT MAX(`order`) + 1 FROM `engine4_core_menuitems` WHERE `menu` LIKE "core_admin_main_plugins");

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('core_admin_main_plugins_winkgreeting', 'winkgreeting', 'Wink and Greeting', '', '{"route":"admin_default","module":"winkgreeting","controller":"settings"}', 'core_admin_main_plugins', '', 1, 0, @iMaxOrder);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('winkgreeting_admin_main_settings', 'winkgreeting', 'Main Settings', '', '{"route":"admin_default","module":"winkgreeting","controller":"settings"}', 'winkgreeting_admin_main', '', 1, 0, 1);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('winkgreeting_admin_main_level', 'winkgreeting', 'Member Level Settings', '', '{"route":"admin_default","module":"winkgreeting","controller":"settings","action":"level"}', 'winkgreeting_admin_main', '', 1, 0, 2);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('winkgreeting_admin_main_more', 'winkgreeting', 'More Plugins', '', '{"route":"admin_default","module":"winkgreeting","controller":"settings","action":"more"}', 'winkgreeting_admin_main', '', 1, 0, 3);

INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (1, 'winkgreeting', 'wink', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (2, 'winkgreeting', 'wink', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (4, 'winkgreeting', 'wink', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (3, 'winkgreeting', 'wink', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (5, 'winkgreeting', 'wink', 0, NULL);

INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (1, 'winkgreeting', 'greeting', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (2, 'winkgreeting', 'greeting', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (4, 'winkgreeting', 'greeting', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (3, 'winkgreeting', 'greeting', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` VALUES (5, 'winkgreeting', 'greeting', 0, NULL);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('notify_wink_new', 'winkgreeting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_link]');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('notify_greeting_new', 'winkgreeting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_link]');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ('wink_new', 'winkgreeting', '{item:$subject} has sent you a {item:$object:wink}.', 0, '', 1);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ('greeting_new', 'winkgreeting', '{item:$subject} has sent you a {item:$object:greeting}.', 0, '', 1);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('winkgreeting_wink', 'Winkgreeting', 'Wink', 'Winkgreeting_Plugin_Menus', '', 'user_profile', '', 1, 0, 0);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('winkgreeting_greeting', 'Winkgreeting', 'Greeting', 'Winkgreeting_Plugin_Menus', '', 'user_profile', '', 1, 0, 0);