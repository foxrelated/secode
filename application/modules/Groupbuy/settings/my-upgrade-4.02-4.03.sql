DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'groupbuy_admin_main_page';
DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'groupbuy_main_page';
DROP TABLE `engine4_groupbuy_pages`;

CREATE TABLE `engine4_groupbuy_faqs` (
    `faq_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `status` ENUM('show','hide') NOT NULL DEFAULT 'hide',
    `ordering` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `owner_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `category_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `question` VARCHAR(255) NOT NULL,
    `answer` TEXT NOT NULL,
    `creation_date` DATETIME NOT NULL,
    PRIMARY KEY (`faq_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
AUTO_INCREMENT=24;


CREATE TABLE `engine4_groupbuy_helppages` (
    `helppage_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `status` ENUM('show','hide') NOT NULL,
    `ordering` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '999',
    `category_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `owner_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `creation_date` DATETIME NOT NULL,
    PRIMARY KEY (`helppage_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
AUTO_INCREMENT=33;


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_main_faqs', 'groupbuy', 'FAQs', 'Groupbuy_Plugin_Menus::canFaqs', '{"route":"groupbuy_extended","controller":"faqs"}', 'groupbuy_main', '', 1, 0, 9);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_main_helps', 'groupbuy', 'Helps', 'Groupbuy_Plugin_Menus::canHelp', '{"route":"groupbuy_extended","controller":"help"}', 'groupbuy_main', '', 1, 0, 8);
INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_helps', 'groupbuy', 'Helps', '', '{"route":"admin_default","module":"groupbuy","controller":"helps"}', 'groupbuy_admin_main', '', 1, 0, 17);
INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_faqs', 'groupbuy', 'FAQs', '', '{"route":"admin_default","module":"groupbuy","controller":"faqs"}', 'groupbuy_admin_main', '', 1, 0, 18);

UPDATE `engine4_core_modules` SET `version` = '4.03' WHERE `engine4_core_modules`.`name` = 'groupbuy' LIMIT 1 ;

ALTER TABLE `engine4_groupbuy_transaction_trackings` ADD `commission_fee` DECIMAL( 10, 2 ) NOT NULL AFTER `amount`;

ALTER TABLE `engine4_groupbuy_payment_accounts` ADD `currency` VARCHAR( 10 ) NOT NULL AFTER `total_price_amount`;
ALTER TABLE `engine4_groupbuy_transaction_trackings` ADD `currency` VARCHAR( 10 ) NOT NULL AFTER `commission_fee` ; 

ALTER TABLE `engine4_groupbuy_payment_accounts` ADD `gateway_id` INT( 11 ) NOT NULL DEFAULT '1' AFTER `account_username`;
ALTER TABLE `engine4_groupbuy_gateways` ADD `currency` VARCHAR( 10 ) NOT NULL AFTER `admin_account` ;
INSERT INTO `engine4_groupbuy_gateways` (`gateway_id`, `gateway_name`, `admin_account`,`currency`, `is_active`, `params`) VALUES
(1, 'Paypal', '', '', 1, ''),
(2, '2Checkout', '', 'USD', 0, '');


DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'groupbuy_admin_main_refunds' LIMIT 1;
DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'groupbuy_admin_main_statistics' LIMIT 1;

INSERT IGNORE INTO `engine4_groupbuy_mailtemplates` (`mailtemplate_id`, `type`, `vars`) VALUES
(28, 'groupbuy_buygiftbuyer', ''),
(29, 'groupbuy_giftconfirm', ''),
(30, 'groupbuy_buygiftseller', '');