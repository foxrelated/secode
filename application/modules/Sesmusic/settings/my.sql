INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sesmusic', 'sesmusic', 'sesmusic', '4.8.9p2', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_sesmusic', 'sesmusic', 'SES - Advanced Music Albums, Songs & Playlists', '', '{"route":"admin_default","module":"sesmusic","controller":"settings"}', 'core_admin_main_plugins', '', 999),
('sesmusic_admin_main_settings', 'sesmusic', 'Settings', '', '{"route":"admin_default","module":"sesmusic","controller":"settings"}', 'sesmusic_admin_main', '', 1),
('sesmusic_admin_main_subglobalsettings', 'sesmusic', 'Global Settings', '', '{"route":"admin_default","module":"sesmusic","controller":"settings"}', 'sesmusic_admin_main_globalsettings', '', 1);