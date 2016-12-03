UPDATE `engine4_core_modules` SET `version` = '4.6.0'  WHERE `name` = 'Phone';


INSERT INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('qrcode_admin_main_setting','qrcode','QR Code','','{"route":"admin_default","module":"qrcode","controller":"qrmapping","action":"setting"}','qrcode_admin_main','','1','1','999');



INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('qrcode_admin_main_updates', 'qrcode', 'iPragmatech Plugin', '', '{"route":"admin_default","module":"qrcode","controller":"Qrmapping","action":"updates"}', 'qrcode_admin_main', '', 1, 0, 999);


INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
( 'qrcode_main', 'standard', 'QR Code Main Navigation Menu', 999);


ALTER IGNORE TABLE `engine4_qrcode_qrcodes` ADD `custom_url` VARCHAR(70) AFTER `image_url`;

