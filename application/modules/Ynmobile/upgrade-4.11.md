#SQL
 
CREATE TABLE `engine4_ynmobile_themes` (
  `theme_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `is_publish` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_test` tinyint(1) unsigned NOT NULL,
  `build_number` int(11) NOT NULL DEFAULT '0',
  `variables_text` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

UPDATE `engine4_core_modules` SET `version` = '4.11' WHERE `name` = 'ynmobile';


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynmobile_admin_main_themes', 'ynmobile', 'Manage Themes','', '{"route":"admin_default","module":"ynmobile","controller":"themes"}', 'ynmobile_admin_main', '', 6);