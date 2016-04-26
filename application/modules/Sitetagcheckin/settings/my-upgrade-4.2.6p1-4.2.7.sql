INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitetagcheckin_gutter_groupeditlocation', 'sitetagcheckin', 'Edit Group Location', 'Sitetagcheckin_Plugin_Menus', '', 'group_profile', '', 1, 0, 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitetagcheckin_gutter_advgroupeditlocation', 'sitetagcheckin', 'Edit Group Location', 'Sitetagcheckin_Plugin_Menus', '', 'advgroup_profile', '', 1, 0, 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitetagcheckin_main_grouplocation', 'sitetagcheckin', 'By Locations', '', '{"route":"sitetagcheckin_groupbylocation"}', 'group_main', '', 1, 0, 2);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitetagcheckin_main_groupbylocation', 'sitetagcheckin', 'By Locations', '', '{"route":"sitetagcheckin_groupbylocation"}', 'advgroup_main', '', 1, 0, 2);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitetagcheckin_gutter_usereditlocation', 'sitetagcheckin', 'Edit My Location', 'Sitetagcheckin_Plugin_Menus', '', 'user_profile', '', 1, 0, 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitetagcheckin_admin_main_userlocations','sitetagcheckin', 'Member Locations', '','{"route":"admin_default","module":"sitetagcheckin","controller":"settings","action":"userlocations"}', 'sitetagcheckin_admin_main', '', '1', '0', '6');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ('sitetagcheckin_page_tagged', 'sitetagcheckin', '{item:$subject} mentioned your page with a {item:$object:$label}.', '0', '', '1');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ('sitetagcheckin_business_tagged', 'sitetagcheckin', '{item:$subject} mentioned your business with a {item:$object:$label}.', '0', '', '1');

DROP TABLE IF EXISTS `engine4_sitetagcheckin_profilemaps`;
CREATE TABLE IF NOT EXISTS `engine4_sitetagcheckin_profilemaps` (
  `profilemap_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `option_id` int(11) NOT NULL,
  `profile_type` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`profilemap_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;