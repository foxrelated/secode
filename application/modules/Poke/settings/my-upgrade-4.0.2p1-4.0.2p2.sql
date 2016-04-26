
-- -----------------------------------------------------------------------

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('poke_home_connection', 'poke', 'Poke Settings', 'Poke_Plugin_Menus', '{"route":"default", "icon":"application/modules/Poke/externals/images/poke_icon.png"}', 'user_home', '', '999');

-- -----------------------------------------------------------------------


INSERT IGNORE  INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES (NULL, 'Poke_main_connectionse', 'poke', 'Poke Settings', NULL, '{"route":"default","module":"poke","controller":"index","action":"pokesettings"}', 'poke_main', NULL, '0', '0', '1');
		
-- -----------------------------------------------------------------------

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('poke.conn.setting', '0');

-- -----------------------------------------------------------------------
		
DROP TABLE IF EXISTS `engine4_poke_settings`;
CREATE TABLE `engine4_poke_settings` (`setting_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,`user_id` INT( 11 ) NOT NULL ,`connection` INT( 11 ) NOT NULL ,PRIMARY KEY ( `setting_id` )) ENGINE = MYISAM ;

-- -----------------------------------------------------------------------