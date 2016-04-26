INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_currency', 'groupbuy', 'Currencies', '', '{"route":"admin_default","module":"groupbuy","controller":"currency"}', 'groupbuy_admin_main', '', 1, 0, 11);
UPDATE `engine4_core_menuitems` SET `order` = 6 WHERE `name` = 'groupbuy_main_create';


/* Alter table in target */
ALTER TABLE `engine4_groupbuy_bills` 
	ADD COLUMN `item_price` decimal(10,2) unsigned   NOT NULL DEFAULT '0.00' after `invoice`, 
	ADD COLUMN `item_final_price` decimal(10,2) unsigned   NOT NULL DEFAULT '0.00' after `item_price`, 
	CHANGE `finance_account_id` `finance_account_id` int(11)   NULL after `sercurity`, 
	CHANGE `emal_receiver` `emal_receiver` varchar(255)  COLLATE utf8_general_ci NOT NULL after `finance_account_id`, 
	CHANGE `payment_receiver_id` `payment_receiver_id` int(11)   NOT NULL after `emal_receiver`, 
	ADD COLUMN `commission_fee` decimal(10,2)   NOT NULL DEFAULT '0.00' after `payment_receiver_id`, 
	CHANGE `date_bill` `date_bill` datetime   NOT NULL after `commission_fee`, 
	CHANGE `bill_status` `bill_status` int(3)   NOT NULL DEFAULT '0' after `date_bill`, 
	CHANGE `amount` `amount` double(10,2)   NOT NULL after `bill_status`, 
	CHANGE `number` `number` int(11)   NOT NULL DEFAULT '1' after `amount`, COMMENT='', DEFAULT CHARSET='utf8';
	
ALTER TABLE  `engine4_groupbuy_bills` ADD  `currency` VARCHAR( 10 ) NULL AFTER  `number`;

/* Alter table in target */
ALTER TABLE `engine4_groupbuy_buy_cods` 
	CHANGE `deal_id` `deal_id` int(11) unsigned   NOT NULL after `buycod_id`, 
	CHANGE `user_id` `user_id` int(11) unsigned   NOT NULL after `deal_id`, 
	CHANGE `verified` `verified` tinyint(3) unsigned   NOT NULL after `user_id`, 
	CHANGE `status` `status` tinyint(8) unsigned   NOT NULL DEFAULT '0' after `verified`, 
	CHANGE `creation_date` `creation_date` datetime   NOT NULL after `note`, 
	CHANGE `modified_date` `modified_date` datetime   NOT NULL after `creation_date`, 
	CHANGE `tran_id` `tran_id` int(11)   NULL after `modified_date`;

/* Create table in target */
CREATE TABLE `engine4_groupbuy_coupons`(
	`coupon_id` int(11) unsigned NOT NULL  auto_increment , 
	`deal_id` int(11) unsigned NOT NULL  , 
	`user_id` int(11) unsigned NOT NULL  , 
	`trans_id` int(11) unsigned NOT NULL  , 
	`bill_id` int(11) NULL  , 
	`cod_id` int(11) NULL  , 
	`status` enum('Unused','Used','Expired') COLLATE latin1_swedish_ci NOT NULL  DEFAULT 'Unused' , 
	`code` varchar(128) COLLATE latin1_swedish_ci NOT NULL  , 
	`creation_date` datetime NOT NULL  , 
	`used_date` datetime NULL  , 
	PRIMARY KEY (`coupon_id`) , 
	KEY `code`(`code`) , 
	KEY `cod_id`(`cod_id`) 
) ENGINE=InnoDB DEFAULT CHARSET='latin1';


/* Create table in target */
CREATE TABLE `engine4_groupbuy_currencies`(
	`code` varchar(10) COLLATE utf8_general_ci NOT NULL  , 
	`name` varchar(64) COLLATE utf8_general_ci NOT NULL  , 
	`symbol` varchar(50) COLLATE utf8_general_ci NOT NULL  , 
	`status` enum('Enable','Disable') COLLATE utf8_general_ci NOT NULL  DEFAULT 'Enable' , 
	`position` enum('Standard','Left','Right') COLLATE utf8_general_ci NOT NULL  DEFAULT 'Standard' , 
	`precision` tinyint(4) unsigned NOT NULL  DEFAULT '2' , 
	`script` tinyint(64) NULL  , 
	`format` varchar(64) COLLATE utf8_general_ci NULL  , 
	`display` enum('No Symbol','Use Symbol','Use Shortname','Use Name') COLLATE utf8_general_ci NOT NULL  DEFAULT 'Use Symbol' , 
	PRIMARY KEY (`code`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8';


/* Alter table in target */
ALTER TABLE `engine4_groupbuy_deals` 
	ADD COLUMN `max_bought` int(10) unsigned   NOT NULL after `max_sold`, 
	ADD COLUMN `method` int(10) unsigned   NOT NULL DEFAULT '0' after `max_bought`, 
	CHANGE `comment_count` `comment_count` int(11) unsigned   NOT NULL DEFAULT '0' after `method`, 
	ADD COLUMN `currency` varchar(10)  COLLATE utf8_general_ci NOT NULL DEFAULT 'USD' after `comment_count`, 
	CHANGE `min_sold` `min_sold` int(11) unsigned   NOT NULL after `currency`, 
	CHANGE `value_deal` `value_deal` double(10,2) unsigned   NOT NULL DEFAULT '0.00' after `title`, 
	CHANGE `discount` `discount` int(11) unsigned   NOT NULL DEFAULT '0' after `value_deal`, 
	CHANGE `total_fee` `total_fee` decimal(11,2)   NOT NULL DEFAULT '0.00' after `discount`, 
	CHANGE `price` `price` double(10,2) unsigned   NOT NULL DEFAULT '0.00' after `total_fee`, 
	ADD COLUMN `vat` double(10,2) unsigned   NOT NULL DEFAULT '0.00' after `price`, 
	ADD COLUMN `vat_id` int(11) unsigned   NULL DEFAULT '0' after `vat`, 
	ADD COLUMN `vat_value` double(10,2)   NOT NULL after `vat_id`, 
	ADD COLUMN `final_price` double(10,2) unsigned   NOT NULL DEFAULT '0.00' after `vat_value`, 
	ADD COLUMN `deal_href` varchar(256)  COLLATE utf8_general_ci NOT NULL after `modified_date`, COMMENT='', DEFAULT CHARSET='utf8';


/* Create table in target */
CREATE TABLE `engine4_groupbuy_gifts`(
	`gift_id` int(11) NOT NULL  auto_increment , 
	`user_id` int(11) NOT NULL  , 
	`bill_id` int(11) NOT NULL  , 
	`friend_name` varchar(128) COLLATE latin1_swedish_ci NULL  , 
	`friend_email` varchar(128) COLLATE latin1_swedish_ci NULL  , 
	`friend_address` varchar(256) COLLATE latin1_swedish_ci NOT NULL  , 
	`friend_phone` varchar(64) COLLATE latin1_swedish_ci NOT NULL  , 
	`note` longtext COLLATE latin1_swedish_ci NOT NULL  , 
	`creation_date` datetime NOT NULL  , 
	`modified_date` datetime NULL  , 
	PRIMARY KEY (`gift_id`) , 
	KEY `user_id`(`user_id`) 
) ENGINE=MyISAM DEFAULT CHARSET='latin1';


/* Create table in target */
CREATE TABLE `engine4_groupbuy_pages`(
	`page_id` int(11) NOT NULL  auto_increment , 
	`name` varchar(128) COLLATE latin1_swedish_ci NOT NULL  , 
	`title` varchar(128) COLLATE latin1_swedish_ci NOT NULL  , 
	`description` text COLLATE latin1_swedish_ci NOT NULL  , 
	`body` longtext COLLATE latin1_swedish_ci NOT NULL  , 
	`created_date` datetime NOT NULL  , 
	`modified_date` datetime NOT NULL  , 
	PRIMARY KEY (`page_id`) , 
	UNIQUE KEY `name`(`name`) 
) ENGINE=InnoDB DEFAULT CHARSET='latin1';

INSERT IGNORE INTO `engine4_groupbuy_pages`(`name`,`title`,`body`,`created_date`,`modified_date`) VALUES
('groupbuy_page_terms','Terms','Add terms of use you want to inform to the users','2011-08-19 08:00:00','2011-08-19 08:00:00'),
('groupbuy_page_privacy','Privacy','Add privacy you want to inform to the users','2011-08-19 08:00:00','2011-08-19 08:00:00');
/* Alter table in target */
ALTER TABLE `engine4_groupbuy_payment_accounts` 
	CHANGE `total_amount` `total_amount` decimal(11,2)   NOT NULL DEFAULT '0.00' after `last_check_out`, 
	ADD COLUMN `total_price_amount` decimal(11,2)   NOT NULL DEFAULT '0.00' after `total_amount`, COMMENT='';

/* Alter table in target */
ALTER TABLE `engine4_groupbuy_payment_requests` 
	CHANGE `dealbuy_id` `dealbuy_id` int(11) unsigned   NULL after `paymentrequest_id`, 
	ADD COLUMN `commission` double(10,2)   NOT NULL DEFAULT '0.00' after `request_status`, 
	ADD COLUMN `commission_fee` double(10,2)   NOT NULL DEFAULT '0.00' after `commission`, 
	ADD COLUMN `send_amount` decimal(11,2)   NOT NULL DEFAULT '0.00' after `commission_fee`, 
	ADD COLUMN `request_currency` varchar(10)  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'USD' after `send_amount`, 
	CHANGE `request_type` `request_type` tinyint(1)   NOT NULL DEFAULT '1' after `request_currency`, 
	CHANGE `request_date` `request_date` datetime   NOT NULL after `request_type`;
	
/* Create table in target */
CREATE TABLE `engine4_groupbuy_vats`(
	`vat_id` int(10) unsigned NOT NULL  auto_increment , 
	`name` varchar(64) COLLATE utf8_general_ci NOT NULL  , 
	`value` decimal(10,2) NOT NULL  DEFAULT '0.00' , 
	`creation_date` datetime NOT NULL  , 
	`modified_date` datetime NOT NULL  , 
	PRIMARY KEY (`vat_id`) 
) ENGINE=MyISAM DEFAULT CHARSET='utf8';


update engine4_groupbuy_deals set final_price =  price;
update engine4_groupbuy_payment_accounts set total_price_amount = total_amount;
update engine4_groupbuy_payment_requests set send_amount = request_amount;


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('core_main_groupbuy', 'groupbuy', 'GroupBuy', '', '{"route":"groupbuy_general"}', 'core_main', '', 1, 0, 5);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_main_browse', 'groupbuy', 'Browse Deals', '', '{"route":"groupbuy_general","action":"browse"}', 'groupbuy_main', '', 1, 0, 1);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_main_manage-selling', 'groupbuy', 'My Posted Deals', 'Groupbuy_Plugin_Menus::canManageDeals', '{"route":"groupbuy_general","action":"manage-selling"}', 'groupbuy_main', '', 1, 0, 2);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_main_account', 'groupbuy', 'My Account', 'Groupbuy_Plugin_Menus::canCreateAccounts', '{"route":"groupbuy_account"}', 'groupbuy_main', '', 1, 0, 4);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_main_create', 'groupbuy', 'Post A New Deal', 'Groupbuy_Plugin_Menus::canCreateDeals', '{"route":"groupbuy_general","action":"create"}', 'groupbuy_main', '', 1, 0, 5);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('core_admin_main_plugins_groupbuy', 'groupbuy', 'GroupBuy', '', '{"route":"admin_default","module":"groupbuy","controller":"manage"}', 'core_admin_main_plugins', '', 1, 0, 999);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_manage', 'groupbuy', 'View Deals', '', '{"route":"admin_default","module":"groupbuy","controller":"manage"}', 'groupbuy_admin_main', '', 1, 0, 1);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_settings', 'groupbuy', 'Global Settings', '', '{"route":"admin_default","module":"groupbuy","controller":"settings"}', 'groupbuy_admin_main', '', 1, 0, 2);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_level', 'groupbuy', 'Member Level Settings', '', '{"route":"admin_default","module":"groupbuy","controller":"level"}', 'groupbuy_admin_main', '', 1, 0, 3);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_localtions', 'groupbuy', 'Locations', '', '{"route":"admin_default","module":"groupbuy","controller":"location"}', 'groupbuy_admin_main', '', 1, 0, 4);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_categories', 'groupbuy', 'Categories', '', '{"route":"admin_default","module":"groupbuy","controller":"category"}', 'groupbuy_admin_main', '', 1, 0, 5);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_accounts', 'groupbuy', 'Accounts', '', '{"route":"admin_default","module":"groupbuy","controller":"account"}', 'groupbuy_admin_main', '', 1, 0, 7);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_transactions', 'groupbuy', 'Transactions', '', '{"route":"admin_default","module":"groupbuy","controller":"transaction"}', 'groupbuy_admin_main', '', 1, 0, 8);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_requests', 'groupbuy', 'Requests', '', '{"route":"admin_default","module":"groupbuy","controller":"request"}', 'groupbuy_admin_main', '', 1, 0, 9);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_emailtemplates', 'groupbuy', 'Email Templates', '', '{"route":"admin_default","module":"groupbuy","controller":"mail", "action": "templates"}', 'groupbuy_admin_main', '', 1, 0, 10);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_paymentsettings', 'groupbuy', 'Gateway', '', '{"route":"admin_default","module":"groupbuy","controller":"payment"}', 'groupbuy_admin_main', '', 1, 0, 12);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_fields', 'groupbuy', 'Questions', '', '{"route":"admin_default","module":"groupbuy","controller":"fields"}', 'groupbuy_admin_main', '', 1, 0, 13);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_main_manage-buying', 'groupbuy', 'My Bought Deals', 'Groupbuy_Plugin_Menus::canManageDeals', '{"route":"groupbuy_general","action":"manage-buying"}', 'groupbuy_main', '', 1, 0, 3);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_currency', 'groupbuy', 'Currencies', '', '{"route":"admin_default","module":"groupbuy","controller":"currency"}', 'groupbuy_admin_main', '', 1, 0, 11);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_vats', 'groupbuy', 'VATs', '', '{"route":"admin_default","module":"groupbuy","controller":"vat"}', 'groupbuy_admin_main', '', 1, 0, 13);


INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('USD', 'U.S. Dollar', '$', 'Enable', 'Standard', 1, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('TRY', 'Turkish Lira', 'TRY', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('THB', 'Thai Baht', 'THB', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('CHF', 'Swiss Franc', 'CHF', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('SEK', 'Swedish Krona', 'SEK', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('SGD', 'Singapore Dollar', '$', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('PLN', 'Polish Zloty', 'PLN', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('PHP', 'Philippine Peso', 'PHP', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('NOK', 'Norwegian Krone', 'NOK', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('NZD', 'New Zealand Dollar', '$', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('TWD', 'New Taiwan Dollar', 'TWD', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('MXN', 'Mexican Peso', 'MXN', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('MYR', 'Malaysian Ringgit', 'MYR', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('JPY', 'Japanese Yen', '¥', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('ILS', 'Israeli New Shekel', 'ILS', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('HUF', 'Hungarian Forint', 'HUF', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('HKD', 'Hong Kong Dollar', '$', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('EUR', 'Euro', '€', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('DKK', 'Danish Krone', 'DKK', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('CZK', 'Czech Koruna', 'CZK', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('CAD', 'Canadian Dollar', 'C $', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('GBP', 'British Pound', '£', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('BRL', 'Brazilian Real	', 'BRL', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('AUD', 'Australian Dollar', 'A $', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');

UPDATE `engine4_core_modules` SET `version` = '4.02' WHERE `engine4_core_modules`.`name` = 'groupbuy' LIMIT 1 ;

DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'groupbuy_admin_main_refunds' LIMIT 1;
DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'groupbuy_admin_main_statistics' LIMIT 1;

INSERT IGNORE INTO `engine4_groupbuy_mailtemplates` (`mailtemplate_id`, `type`, `vars`) VALUES
(28, 'groupbuy_buygiftbuyer', ''),
(29, 'groupbuy_giftconfirm', ''),
(30, 'groupbuy_buygiftseller', '');