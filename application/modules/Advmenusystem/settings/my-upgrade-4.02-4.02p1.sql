UPDATE `engine4_core_modules` SET `version` = '4.02p1' WHERE `engine4_core_modules`.`name` = 'advmenusystem' LIMIT 1 ;
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES  
('advmenusystem_admin_main_menus', 'advmenusystem', 'Menu Settings', '', '{"route":"admin_default","module":"advmenusystem","controller":"menus"}', 'advmenusystem_admin_main', '', 2),
('advmenusystem_admin_main_styles', 'advmenusystem', 'Style Settings', '', '{"route":"admin_default","module":"advmenusystem","controller":"styles"}', 'advmenusystem_admin_main', '', 3);
