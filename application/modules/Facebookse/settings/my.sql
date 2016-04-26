/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

INSERT INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('facebookse', 'Facebook for SocialEngine', 'Facebook for SocialEngine', '4.8.10p2', 1, 'extra');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('facebooksepage_friend_home', 'facebookse', 'My Facebook', 'Facebookse_Plugin_Menus', '{"route":"facebookse_index_settings"}', 'user_home', '', 999);

DROP TABLE IF EXISTS `engine4_facebookse_statistics`;
CREATE TABLE IF NOT EXISTS `engine4_facebookse_statistics` ( 
  `statistic_id` int(11) unsigned NOT NULL auto_increment, 
  `url` text NOT NULL, 
  `updated` timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP, 
  `url_scrape` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0',
  `url_type` varchar(50) NOT NULL,
  `content_id` int(11) unsigned NOT NULL,
  `resource_type` varchar(50) NOT NULL,
PRIMARY KEY (`statistic_id`),
UNIQUE KEY `content_id` (`content_id`,`resource_type`) ) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_facebookse_feedsettings` (
  `feedsetting_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL, 
  `feedpublish_types` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`feedsetting_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;