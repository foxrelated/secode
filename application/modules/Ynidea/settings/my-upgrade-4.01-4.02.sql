UPDATE `engine4_core_modules` SET `version` = '4.02' WHERE `engine4_core_modules`.`name` = 'ynidea' LIMIT 1 ;
ALTER TABLE `engine4_ynidea_ideas` ADD `allow_campaign` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `follow_count`;
ALTER TABLE `engine4_ynidea_trophies` ADD `allow_campaign` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `modified_date` ;
ALTER TABLE `engine4_ynidea_versions` ADD `allow_campaign` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `modified_date`;