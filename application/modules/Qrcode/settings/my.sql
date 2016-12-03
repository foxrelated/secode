INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('qrcode', 'QRCode', 'Having values from user profile.', '4.2.2', 1, 'extra') ;
DROP TABLE IF EXISTS `engine4_qrcode_qrcodes`;



CREATE TABLE IF NOT EXISTS `engine4_qrcode_qrcodes` (
  `qrcode_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `field_type` smallint(6) DEFAULT NULL,
  `image_url` varchar(70) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `display` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`qrcode_id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `qrcode_id` (`qrcode_id`)
) ENGINE=InnoDB;

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
( 'user_settings_qrcode', 'core', 'QR Code', '', '{"route":"qrcode_route","module":"qrcode","controller":"index","action":"index"}', 'user_settings', '', 1, 1, 999),
('qrcode_admin_manage','qrcode','QR Code','','{"route":"admin_default","module":"qrcode","controller":"qrmapping","action":"setting"}','core_admin_main_plugins','','1','1','999');


INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('qrcode_admin_main_setting','qrcode','QR Code','','{"route":"admin_default","module":"qrcode","controller":"qrmapping","action":"setting"}','qrcode_admin_main','','1','1','999');




INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('qrcode_admin_main_updates', 'qrcode', 'iPragmatech Plugin', '', '{"route":"admin_default","module":"qrcode","controller":"Qrmapping","action":"updates"}', 'qrcode_admin_main', '', 1, 0, 999);


INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
( 'qrcode_main', 'standard', 'QR Code Main Navigation Menu', 999);


ALTER IGNORE TABLE `engine4_qrcode_qrcodes` ADD `custom_url` VARCHAR(70) AFTER `image_url`;