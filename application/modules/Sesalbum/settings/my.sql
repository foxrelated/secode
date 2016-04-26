INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sesalbum', 'Advanced Photos & Albums Plugin', '', '4.8.9p10', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_sesalbum', 'sesalbum', 'SES - Advanced Photos & Albums', '', '{"route":"admin_default","module":"sesalbum","controller":"settings","action":"index"}', 'core_admin_main_plugins', '', 999),
('sesalbum_admin_main_settings', 'sesalbum', 'Global Settings', '', '{"route":"admin_default","module":"sesalbum","controller":"settings"}', 'sesalbum_admin_main', '', 1);