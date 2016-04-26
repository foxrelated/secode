/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules` 
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES ('sitetagcheckin', 'Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin', 'Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin', '4.8.6p3', 1, 'extra');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitetagcheckin_main_location', 'sitetagcheckin', 'By Locations', '', '{"route":"sitetagcheckin_bylocation"}', 'event_main', '', 1, 0, 3);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitetagcheckin_main_bylocation', 'sitetagcheckin', 'By Locations', '', '{"route":"sitetagcheckin_bylocation"}', 'ynevent_main', '', 1, 0, 3);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('sitetagcheckin_lct_add_to_map', 'sitetagcheckin', '{item:$subject} was {var:$prefixadd} {var:$location} {var:$event_date}.<br/>{body:$body}', 1, 7, 1, 1, 1, 1);


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitetagcheckin_gutter_editlocation', 'sitetagcheckin', 'Edit Event Location', 'Sitetagcheckin_Plugin_Menus', '', 'event_profile', '', 1, 0, 999);

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

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ('sitetagcheckin_page_tagged', 'sitetagcheckin', '{item:$subject} mentioned your page with a {item:$object:$label}.', '0', '', '1');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ('sitetagcheckin_business_tagged', 'sitetagcheckin', '{item:$subject} mentioned your business with a {item:$object:$label}.', '0', '', '1');

DROP TABLE IF EXISTS `engine4_sitetagcheckin_profilemaps`;
CREATE TABLE IF NOT EXISTS `engine4_sitetagcheckin_profilemaps` (
  `profilemap_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `option_id` int(11) NOT NULL,
  `profile_type` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`profilemap_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitetagcheckin_main_videolocation', 'sitetagcheckin', 'By Locations', '', '{"route":"sitetagcheckin_videobylocation"}', 'video_main', '', 0, 0, 2);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitetagcheckin_main_albumlocation', 'sitetagcheckin', 'By Locations', '', '{"route":"sitetagcheckin_albumbylocation"}', 'album_main', '', 0, 0, 2);
