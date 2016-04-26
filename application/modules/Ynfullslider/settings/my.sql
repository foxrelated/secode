--
-- Module insertion
--
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynfullslider', 'YN - Full Slider', '', '4.01', 1, 'extra') ;

--
-- Admin menu items
--
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynfullslider', 'ynfullslider', 'YN - Full Slider', '', '{"route":"admin_default","module":"ynfullslider","controller":"sliders"}', 'core_admin_main_plugins', '', 999),
('ynfullslider_admin_main_sliders', 'ynfullslider', 'Manage Sliders', '', '{"route":"admin_default","module":"ynfullslider","controller":"sliders"}', 'ynfullslider_admin_main', '', 1),
 ('ynfullslider_admin_main_settings', 'ynfullslider', 'Global Settings', '', '{"route":"admin_default","module":"ynfullslider","controller":"settings", "action":"index"}', 'ynfullslider_admin_main', '', 2);

--
-- Table structure for `engine4_ynfullslider_sliders`
--
CREATE TABLE IF NOT EXISTS `engine4_ynfullslider_sliders` (
`slider_id` int(11) unsigned NOT NULL auto_increment,
`user_id` int(11) unsigned NOT NULL,
`creation_date` datetime NOT NULL,
`title` varchar(128) NOT NULL,
`photo_id` int(11) NOT NULL DEFAULT '0',
`slide_count` int(11) NOT NULL default '0',
`valid_from` datetime default NULL,
`valid_to` datetime default NULL,
`unlimited` boolean NOT NULL default '1',
`params` text NULL,
PRIMARY KEY (`slider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for `engine4_ynfullslider_slides`
--
CREATE TABLE IF NOT EXISTS `engine4_ynfullslider_slides` (
`slide_id` int(11) unsigned NOT NULL auto_increment,
`title` varchar(128) default NULL,
`slider_id` int(11) unsigned NOT NULL,
`photo_id` int(11) NOT NULL DEFAULT '0',
`show_slide` boolean NOT NULL default '1',
`slide_order` int(11) DEFAULT 0,
`params` text NULL,
`slide_elements` text NULL,
`elements_order` text NULL,
`elements_count` text NULL,
`animation_order` text NULL,
PRIMARY KEY (`slide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for `engine4_ynfullslider_elements`
--
CREATE TABLE IF NOT EXISTS `engine4_ynfullslider_elements` (
`element_id` int(11) unsigned NOT NULL auto_increment,
`slide_id` int(11) unsigned NOT NULL,
`creation_date` datetime NOT NULL,
`params` text NULL,
PRIMARY KEY (`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
