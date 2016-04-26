UPDATE `engine4_core_modules` SET `version` = '4.04p5' WHERE `engine4_core_modules`.`name` = 'groupbuy' LIMIT 1 ;

ALTER TABLE `engine4_groupbuy_deals` ADD COLUMN `location` text COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `engine4_groupbuy_deals` ADD COLUMN `longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL;
ALTER TABLE `engine4_groupbuy_deals` ADD COLUMN `latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL;

ALTER TABLE `engine4_groupbuy_subscription_conditions` ADD COLUMN `within` int(11) unsigned NOT NULL default '0';
ALTER TABLE `engine4_groupbuy_subscription_conditions` ADD COLUMN `long` varchar(64) CHARACTER SET utf8 default '0';
ALTER TABLE `engine4_groupbuy_subscription_conditions` ADD COLUMN `lat` varchar(64) CHARACTER SET utf8 default '0';
