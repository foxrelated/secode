INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
( 'poke_home_connection', 'poke', 'Poke Settings', 'Poke_Plugin_Menus', '{"route":"default", "icon":"application/modules/Poke/externals/images/poke_icon.png"}', 'user_home', '',  0, 999, 0,0),
('Poke_main_connectionse', 'poke', 'Poke Settings', NULL, '{"route":"default","module":"poke","controller":"index","action":"pokesettings"}', 'poke_main', NULL, 0, 1, 0, 0),
('user_profile_poke', 'poke', 'Poke', 'Poke_Plugin_Menus', '', 'user_profile', '', 0, 4, 1,1);

INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES
('poke', 1, 0, 0, 0);

INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES ('core_main_pokes', 'poke', 'Pokes', NULL, '{"route":"poke_general"}', 'core_main', NULL, '0', '15', '1', '1');