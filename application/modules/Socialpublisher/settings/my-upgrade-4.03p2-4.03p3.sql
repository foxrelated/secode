UPDATE `engine4_core_modules` SET `version` = '4.03p3' where 'name' = 'socialpublisher';
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('socialpublisher.poll', '{"types":["poll"],"title":"socialpublisher_poll","active":"1","providers":["facebook","twitter","linkedin"]}');