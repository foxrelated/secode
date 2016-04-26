INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('ynidea_main', 'standard', 'Ideas Main Navigation Menu');
UPDATE `engine4_core_modules` SET `version` = '4.02p2' WHERE `engine4_core_modules`.`name` = 'ynidea' LIMIT 1 ;