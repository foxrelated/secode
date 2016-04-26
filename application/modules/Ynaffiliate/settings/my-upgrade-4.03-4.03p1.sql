UPDATE `engine4_core_modules` SET `version` = '4.03p1' where 'name' = 'ynaffiliate';

ALTER TABLE `engine4_ynaffiliate_accounts` ADD COLUMN `selected_currency` varchar(3) NOT NULL default 'USD';
