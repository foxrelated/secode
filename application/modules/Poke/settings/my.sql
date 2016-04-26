/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: my.sql 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */

-- -----------------------------------------------------------------------
		
DROP TABLE IF EXISTS `engine4_poke_settings`;
CREATE TABLE `engine4_poke_settings` (`setting_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,`user_id` INT( 11 ) NOT NULL ,`connection` INT( 11 ) NOT NULL ,PRIMARY KEY ( `setting_id` )) ENGINE = MYISAM ;

-- -----------------------------------------------------------------------

DROP TABLE IF EXISTS `engine4_poke_pokeusers`; 
CREATE TABLE IF NOT EXISTS `engine4_poke_pokeusers` ( `pokeuser_id` int(11) NOT NULL AUTO_INCREMENT, `created` varchar(50) NOT NULL, `resourceid` int(11) NOT NULL, `userid` int(11) NOT NULL, `isdeleted` tinyint(1) NOT NULL, `isexpire` tinyint(1) NOT NULL, PRIMARY KEY (`pokeuser_id`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; 

-- -----------------------------------------------------------------------

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('poke', 'Pokes', 'Pokes', '4.8.5', 1, 'extra');

-- -----------------------------------------------------------------------

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('poke_home_connection', 'poke', 'Poke Settings', 'Poke_Plugin_Menus', '{"route":"default", "icon":"application/modules/Poke/externals/images/poke_icon.png"}', 'user_home', '', '999');

-- -----------------------------------------------------------------------


INSERT IGNORE  INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES (NULL, 'Poke_main_connectionse', 'poke', 'Poke Settings', NULL, '{"route":"default","module":"poke","controller":"index","action":"pokesettings"}', 'poke_main', NULL, '0', '0', '1');
		
-- -----------------------------------------------------------------------

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('poke.conn.setting', '0');

-- -----------------------------------------------------------------------

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ('Poke', 'poke', '{item:$subject} has poked you.', 0, 'poke.widget.userpoke'); 

-- -----------------------------------------------------------------------

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ('Poke', 'poke', '{item:$subject} poked {item:$object}.', 1,3,0, 1, 1, 1); 

-- -----------------------------------------------------------------------

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('Poke_User_Email', 'poke', '[host],[email],[sender_title],[site_title],[date],[object_link]'); 

-- -----------------------------------------------------------------------

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('user_profile_poke', 'poke', 'Poke', 'Poke_Plugin_Menus', '', 'user_profile', '', 1, 0, 4), 
('core_admin_main_plugins_pokesettings', 'poke', 'SEAO - Pokes', '', '{"route":"admin_default","module":"poke","controller":"pokesettings"}', 'core_admin_main_plugins', '', 1, 0, 999), 
('poke_admin_main_pokesettings', 'poke', 'Global Settings', '', '{"route":"admin_default","module":"poke","controller":"pokesettings"}', 'poke_admin_main', '', 1, 0, 1), 
('poke_admin_main_level', 'poke', 'Member Level Settings', '', '{"route":"admin_default","module":"poke","controller":"level"}', 'poke_admin_main', '', 1, 0, 2),
('poke_admin_main_pokehistory', 'poke', 'View Pokes', '', '{"route":"admin_default","module":"poke","controller":"pokehistory"}', 'poke_admin_main', '', 1, 0, 3),
('poke_admin_main_faq', 'poke', 'FAQ', '', '{"route":"admin_default","module":"poke","controller":"pokesettings","action":"faq"}', 'poke_admin_main', '', 1, 0, 4);

-- -----------------------------------------------------------------------

INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES 
(1, 'poke', 'auth_view', 3, 'everyone'), 
(1, 'poke', 'send', 1, NULL), 
(2, 'poke', 'auth_view', 3, 'everyone'), 
(2, 'poke', 'send', 1, NULL), 
(3, 'poke', 'auth_view', 3, 'everyone'), 
(3, 'poke', 'send', 1, NULL), 
(4, 'poke', 'auth_view', 3, 'everyone'), 
(4, 'poke', 'send', 1, NULL), 
(5, 'poke', 'auth_view', 3, 'everyone'), 
(5, 'poke', 'send', 1, NULL);