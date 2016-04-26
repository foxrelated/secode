INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('shoutbox', 'Shoutbox', 'Shoutbox', '4.0.0', 1, 'extra');

CREATE TABLE IF NOT EXISTS `engine4_shoutbox_shouts` (
  `shout_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `page` varchar(128) NOT NULL,
  PRIMARY KEY (`shout_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_admin_main_plugins_shoutbox', 'shoutbox', 'Shoutbox', '', '{"route":"admin_default","module":"shoutbox","controller":"settings","action":"index"}', 
'core_admin_main_plugins', '', 1, 0, 999),
('shoutbox_admin_main_settings', 'Shoutbox', 'Global Settings', NULL, '{"route":"admin_default","module":"shoutbox","controller":"settings"}', 'shoutbox_admin_main', 
NULL, 1, 0, 1),
('shoutbox_admin_main_level', 'shoutbox', 'Member Level Setting', NULL, '{"route":"admin_default","module":"shoutbox","controller":"level"}', 'shoutbox_admin_main', 
NULL, 1, 0, 2);
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES 
('shoutbox.shouts', '10'),
('shoutbox.autorefresh', '1'),
('shoutbox.timer', '5000');
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES
(1, 'shoutbox', 'view', 1, NULL),
(2, 'shoutbox', 'view', 1, NULL),
(3, 'shoutbox', 'view', 1, NULL),
(4, 'shoutbox', 'view', 1, NULL),
(5, 'shoutbox', 'view', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES
(1, 'shoutbox', 'create', 1, NULL),
(2, 'shoutbox', 'create', 1, NULL),
(3, 'shoutbox', 'create', 1, NULL),
(4, 'shoutbox', 'create', 1, NULL),
(5, 'shoutbox', 'create', 0, NULL);