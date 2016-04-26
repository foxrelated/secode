UPDATE `engine4_core_modules` SET `version` = '4.04p2' WHERE `engine4_core_modules`.`name` = 'groupbuy' LIMIT 1 ;
ALTER TABLE  `engine4_groupbuy_deals` CHANGE  `company_name`  `company_name` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
DROP TABLE `engine4_groupbuy_params`;
ALTER TABLE  `engine4_groupbuy_deals` CHANGE  `title`  `title` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;
ALTER TABLE  `engine4_groupbuy_deals` CHANGE  `address`  `address` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;