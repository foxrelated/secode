UPDATE `engine4_core_modules` SET `version` = '4.03p4' where 'name' = 'socialpublisher';

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('socialpublisher.ynultimatevideovideo', '{"types":["ynultimatevideo_video"],"title":"socialpublisher_ynultimatevideo_video","active":"1","providers":["facebook","twitter","linkedin"]}');