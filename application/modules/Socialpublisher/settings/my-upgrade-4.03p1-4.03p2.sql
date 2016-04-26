INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('socialpublisher.file', '{"types":["file"],"title":"socialpublisher_file","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.ynbusinesspagesbusiness', '{"types":["ynbusinesspages_business"],"title":"socialpublisher_business","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.ynlistingslisting', '{"types":["ynlistings_listing"],"title":"socialpublisher_listing","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.ynjobpostingjob', '{"types":["ynjobposting_job"],"title":"socialpublisher_job","active":"1","providers":["facebook","twitter","linkedin"]}');

UPDATE `engine4_core_modules` SET `version` = '4.03p2' where 'name' = 'socialpublisher';