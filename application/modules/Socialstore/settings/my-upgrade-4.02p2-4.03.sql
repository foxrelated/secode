UPDATE `engine4_core_modules` SET `version` = '4.03' where `name` = 'socialstore';

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_taxes`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_taxes` (
  `tax_id` int(10) unsigned NOT NULL auto_increment,
  `store_id` int(11) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL,
  `value` decimal(10,2) NOT NULL default '0.00',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY  (`tax_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Direct Payment
--


ALTER TABLE `engine4_socialstore_orderitems` ADD `seller_amount` DECIMAL( 16, 2 ) NOT NULL AFTER `payment_status`; 

ALTER TABLE `engine4_socialstore_orders` ADD `paypal_paykey` VARCHAR( 255 ) NULL DEFAULT 'none' AFTER `description`;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_downloadurls`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_downloadurls` (
  `downloadurl_id` int(11) unsigned NOT NULL auto_increment,
  `download_url` mediumtext NOT NULL,
  `file_url` mediumtext NOT NULL,
  `used_time` int(11) unsigned NOT NULL default '0',
  `expire_time` int(11) unsigned NOT NULL default '0',
  `last_click` datetime NULL,
  `creation_date` datetime NULL,
  PRIMARY KEY  (`downloadurl_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `engine4_socialstore_products` ADD COLUMN `file_id` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `product_type`;
ALTER TABLE `engine4_socialstore_products` ADD COLUMN `previewfile_id` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `file_id`;

DELETE FROM `engine4_core_menuitems` WHERE `name` = 'socialstore_admin_main_categories';
DELETE FROM `engine4_core_menuitems` WHERE `name` = 'socialstore_admin_main_taxes';

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_storecategories`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_storecategories` (
  `storecategory_id` int(11) unsigned NOT NULL auto_increment,
  `parent_category_id` int(11) unsigned NOT NULL default '0',
  `level` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY  (`storecategory_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `engine4_socialstore_storecategories` (`storecategory_id`, `parent_category_id`, `level`, `name`) VALUES
(1, 0, 1, 'Default Category');
ALTER TABLE `engine4_socialstore_stores` ADD `category_id` int(11) unsigned NOT NULL AFTER `location_id` ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_customcategories`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_customcategories` (
  `customcategory_id` int(11) unsigned NOT NULL auto_increment,
  `store_category_id` int(11) unsigned NOT NULL,
  `store_id` int(11) unsigned NOT NULL,
  `parent_category_id` int(11) unsigned NOT NULL default '0',
  `level` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY  (`customcategory_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


ALTER TABLE `engine4_socialstore_products` ADD COLUMN `storecategory_id` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `category_id`;


ALTER TABLE `engine4_socialstore_products` ADD COLUMN `video_url` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `storecategory_id` ;

INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('socialstore_admin_main_categories', 'socialstore', 'Store Categories', 'Socialstore_Plugin_Menus::canMyAddressBook', '{"route":"admin_default","module":"socialstore","controller":"store-category"}', 'socialstore_admin_main', '', 1, 0, 5);


-- --------------------------------------------------------

--
-- Address book
--

INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('socialstore_main_myaddressbook', 'socialstore', 'Address Book', 'Socialstore_Plugin_Menus::canMyAddressBook', '{"route":"socialstore_extended","controller":"my-address-book"}', 'socialstore_main', '', 1, 0, 10);

CREATE TABLE IF NOT EXISTS `engine4_socialstore_addressbooks` (
	`addressbook_id` int(11) unsigned NOT NULL auto_increment,
	`user_id` int(11) unsigned NOT NULL,
	`value` text NOT NULL,
	PRIMARY KEY (`addressbook_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Shipping Method
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_shippingmethods` (
	`shippingmethod_id` int(11) unsigned NOT NULL auto_increment,
	`store_id` int(11) unsigned NOT NULL,
	`name` varchar(256) NOT NULL,
	`description` text NOT NULL,
	`free_shipping` tinyint(1) unsigned NOT NULL default '0',
	PRIMARY KEY (`shippingmethod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `engine4_socialstore_shippingrules` (
	`shippingrule_id` int(11) unsigned NOT NULL auto_increment,
	`shippingmethod_id` int(11) unsigned NOT NULL,
	`enabled` tinyint(1) unsigned NOT NULL default '1',
	`category_ids` varchar(256) NULL,
	`countries` varchar(256) NULL,
	`order_minimum` decimal(16,2) NOT NULL default '0.00',
	`order_cost` decimal(16,2) NOT NULL default '0.00',
	`cal_type` enum('item','weight') NOT NULL default 'item',
	`type_amount` decimal(16,2) NOT NULL default '0.00',
	`handling_type` enum('none','order','item') NOT NULL default 'none',
	`handling_fee_type` enum('fixed','percent') NOT NULL default 'fixed',
	`handling_fee` decimal(16,2) NOT NULL default '0.00',
	PRIMARY KEY (`shippingrule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `engine4_socialstore_shippingcats` (
	`shippingcat_id` int(11) unsigned NOT NULL auto_increment,
	`shippingrule_id` int(11) unsigned NOT NULL,
	`shippingmethod_id` int(11) unsigned NOT NULL,
	`category_id` int(11) unsigned NOT NULL,
	PRIMARY KEY (`shippingcat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `engine4_socialstore_shippingcountries` (
	`shippingcountry_id` int(11) unsigned NOT NULL auto_increment,
	`shippingrule_id` int(11) unsigned NOT NULL,
	`shippingmethod_id` int(11) unsigned NOT NULL,
	`country_id` varchar(64) NOT NULL,
	PRIMARY KEY (`shippingcountry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `engine4_socialstore_shippingaddresses` (
	`shippingaddress_id` int(11) unsigned NOT NULL auto_increment,
	`order_id` varchar(32) NOT NULL,
	`value` text NOT NULL,
	`creation_date` datetime NULL,
	`addressbook_id` int(11) unsigned NULL default 0,
	`is_form` tinyint (1) NULL default 0,
	PRIMARY KEY (`shippingaddress_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `engine4_socialstore_billingaddresses` (
	`billingaddress_id` int(11) unsigned NOT NULL auto_increment,
	`order_id` varchar(32) NOT NULL,
	`value` text NOT NULL,
	PRIMARY KEY (`billingaddress_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `engine4_socialstore_orderitems` ADD COLUMN `shippingaddress_id` int(11) unsigned NOT NULL default '0' AFTER `object_id`;
ALTER TABLE `engine4_socialstore_orderitems` ADD COLUMN `order_shipping_amount` decimal(16,2) NOT NULL default '0.00' AFTER `shipping_amount`;
ALTER TABLE `engine4_socialstore_orderitems` ADD COLUMN `order_handling_amount` decimal(16,2) NOT NULL default '0.00' AFTER `handling_amount`;
ALTER TABLE `engine4_socialstore_orderitems` ADD COLUMN `shippingrule_id` int(11) unsigned NOT NULL default '0' AFTER `shippingaddress_id`;

CREATE TABLE IF NOT EXISTS `engine4_socialstore_shippingpackages` (
	`shippingpackage_id` int(11) unsigned NOT NULL auto_increment,
	`order_id` varchar(32) NOT NULL,
	`store_id` int(11) unsigned NOT NULL,
	`shippingaddress_id` int(11) unsigned NOT NULL,
	`shipping_cost` decimal(16,2) NOT NULL default '0.00',
	`handling_cost` decimal(16,2) NOT NULL default '0.00',
	PRIMARY KEY (`shippingpackage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `engine4_socialstore_orders` ADD `pdf_id` int(11) DEFAULT NULL AFTER `description`;
ALTER TABLE `engine4_socialstore_products` ADD `weight` decimal(16,2) unsigned NOT NULL default '0.00' AFTER `sku`;
ALTER TABLE `engine4_socialstore_products` ADD `weight_unit` enum('kg','lb') NOT NULL default 'kg' AFTER `weight`;

UPDATE `engine4_core_menuitems` SET `menu` = 'socialstore_main_more' where `name` = 'socialstore_main_myorders';
UPDATE `engine4_core_menuitems` SET `menu` = 'socialstore_main_more' where `name` = 'socialstore_main_faqs';
UPDATE `engine4_core_menuitems` SET `menu` = 'socialstore_main_more' where `name` = 'socialstore_main_helps';
UPDATE `engine4_core_menuitems` SET `menu` = 'socialstore_main_more' where `name` = 'socialstore_main_myaddressbook';
INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('socialstore_main_more', 'socialstore', 'More', '', '{"uri":"javascript:void(0);"}', 'socialstore_main', 'socialstore_main_more', 1, 0, 7);

CREATE TABLE IF NOT EXISTS `engine4_socialstore_attributes_sets` (
  `set_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `store_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_attributes_types`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_attributes_types` (
  `type_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `set_id` int(11) unsigned NOT NULL,
  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `order` smallint(3) unsigned NOT NULL DEFAULT '999',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_attributes_options`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_attributes_options` (
  `option_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `set_id` int(11) unsigned NOT NULL,
  `type_id` int(11) unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_attributes_values`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_attributes_values` (
  `value_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `type_id` int(11) unsigned NOT NULL,
  `index` smallint(3) unsigned NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`value_id`,`product_id`,`type_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `engine4_socialstore_products` ADD COLUMN `attributeset_id` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `storecategory_id`;

--
-- MinhNC add table Gdarequest + add Link to member home page and profile home page.
-- Remember add column 'gda' to socialstore_products
--

--
-- Structure de la table `engine4_socialstore_gdarequests`
--
CREATE TABLE IF NOT EXISTS `engine4_socialstore_gdarequests` (
  `gdarequest_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) unsigned NOT NULL DEFAULT '0',
  `product_id` int(11) unsigned NOT NULL DEFAULT '0',
  `deal_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'gda requester',
  `org_qty` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'gda requester expect qty',
  `status` enum('waiting','approved','refused') NOT NULL DEFAULT 'waiting' COMMENT 'gda requester expect discount',
  `org_discount` float(10,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT 'gda requester expect discount in percentage',
  `org_message` text NOT NULL COMMENT 'requester message',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`gdarequest_id`),
  KEY `store_id` (`store_id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `engine4_socialstore_attributes_options` ADD `product_id` INT( 11 ) NOT NULL DEFAULT '0' AFTER `type_id` ;
ALTER TABLE `engine4_socialstore_attributes_options` ADD `adjust_price` DECIMAL( 16, 2 ) NOT NULL DEFAULT '0.00' AFTER `label` ;
ALTER TABLE `engine4_socialstore_products` ADD COLUMN `gda` tinyint(1) unsigned NOT NULL default '0' AFTER `deliver_days`;
ALTER TABLE `engine4_socialstore_cartitems` ADD COLUMN `options` text NULL AFTER `item_qty`; 
ALTER TABLE `engine4_socialstore_orderitems` ADD COLUMN `options` text NULL AFTER `description`; 
ALTER TABLE `engine4_socialstore_orderitems` ADD COLUMN `options_jsons` text NULL AFTER `options`; 
ALTER TABLE `engine4_socialstore_attributes_values` CHANGE `value_id` `value_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `engine4_socialstore_productoptions` (
	`productoption_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`product_id` int(11) unsigned NOT NULL DEFAULT '0',
	`cart_id` int(11) unsigned NOT NULL DEFAULT '0',
	`cartitem_id` int(11) unsigned NOT NULL DEFAULT '0',
	`order_id` varchar(32) NULL,
	`orderitem_id` int(11) unsigned NOT NULL DEFAULT '0',
	`options` text NULL,
	PRIMARY KEY (`productoption_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8; 
ALTER TABLE `engine4_socialstore_shippingpackages` ADD COLUMN `paypal_paykey` varchar(255) default 'none' AFTER `order_id`;

CREATE TABLE IF NOT EXISTS `engine4_socialstore_attributepresets` (
  `attributepreset_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) unsigned NOT NULL,
  `preset_name` varchar(256) NOT NULL,
  `attributeset_id` int(11) unsigned NOT NULL DEFAULT '0',
  `options` text NULL,
  `values` text NULL,
  PRIMARY KEY (`attributepreset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;