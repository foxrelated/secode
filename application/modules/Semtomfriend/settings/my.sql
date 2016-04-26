
/**
 * SocialEngine - SocialEngineMods
 *
 */


INSERT INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('semtomfriend', 'Tom Friend', 'Tom Friend plugin.', '4.0.0', 1, 'extra');


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('core_admin_main_plugins_semtomfriend', 'semtomfriend', 'Tom Friend', '', '{"route":"admin_default","module":"semtomfriend","controller":"settings","action":"index"}', 'core_admin_main_plugins', '', 0, 999),

('semtomfriend_admin_main_tomfriend', 'semtomfriend', 'Tom Friend', '', '{"route":"admin_default","module":"semtomfriend","controller":"tomfriend"}', 'semtomfriend_admin_main', '', 0, 2),
('semtomfriend_admin_main_welcome', 'semtomfriend', 'Welcome Message', '', '{"route":"admin_default","module":"semtomfriend","controller":"welcome"}', 'semtomfriend_admin_main', '', 0, 3),
('semtomfriend_admin_main_help', 'semtomfriend', 'Help', '', '{"route":"admin_default","module":"semtomfriend","controller":"help"}', 'semtomfriend_admin_main', '', 0, 10);


CREATE TABLE IF NOT EXISTS `engine4_semtomfriend_semtomfriend` (
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

