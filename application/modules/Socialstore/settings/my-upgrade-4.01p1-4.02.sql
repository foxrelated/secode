UPDATE `engine4_core_modules` SET `version` = '4.02' where 'name' = 'socialstore';

ALTER TABLE `engine4_socialstore_products` ADD `product_type` ENUM( 'default', 'downloadable' ) NOT NULL DEFAULT 'default' AFTER `favourite_count` ;

ALTER TABLE `engine4_socialstore_products` ADD `download_url` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `product_type` ;

ALTER TABLE `engine4_socialstore_products` ADD `discount_price` decimal(16,2) NOT NULL DEFAULT '0.00' AFTER `max_qty_purchase` ;

ALTER TABLE `engine4_socialstore_carts` CHANGE COLUMN `owner_id` `owner_id` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `cart_id`;

ALTER TABLE `engine4_socialstore_carts` ADD COLUMN `guest_id` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `owner_id`;

ALTER TABLE `engine4_socialstore_cartitems` CHANGE COLUMN `owner_id` `owner_id` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `cart_id`;

ALTER TABLE `engine4_socialstore_cartitems` ADD COLUMN `guest_id` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `owner_id`;

ALTER TABLE `engine4_socialstore_orders` CHANGE COLUMN `owner_id` `owner_id` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `paytype_id`;

ALTER TABLE `engine4_socialstore_orders` ADD COLUMN `guest_id` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `owner_id`;

ALTER TABLE `engine4_socialstore_paytrans` CHANGE COLUMN `owner_id` `owner_id` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `gateway`;

ALTER TABLE `engine4_socialstore_paytrans` ADD COLUMN `guest_id` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `owner_id`;

INSERT INTO `engine4_socialstore_gateways` (`gateway_id`, `title`, `description`) VALUES ('Authorizenet', 'Authorize.net', 'Authorize.Net');
UPDATE `engine4_socialstore_gateways` SET `plugin`='Socialstore_Payment_Plugin_Authorizenet' WHERE `gateway_id`='Authorizenet' LIMIT 1;
UPDATE `engine4_socialstore_gateways` SET `admin_form`='Socialstore_Form_Admin_Gateway_Authorizenet' WHERE `gateway_id`='Authorizenet' LIMIT 1;
UPDATE `engine4_socialstore_gateways` SET `config`='{"login":"7cD42Z9gH","key":"42a5ya7e8wC4GXRr"}' WHERE `gateway_id`='Authorizenet' LIMIT 1;

ALTER TABLE `engine4_socialstore_products` ADD `available_quantity` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `price`;

ALTER TABLE `engine4_socialstore_products` ADD `shipping_option` ENUM( 'local', 'international' ) NOT NULL DEFAULT 'local' AFTER `body`;

INSERT INTO `engine4_socialstore_gateways` (`gateway_id`,`title`,`description`,`enabled`,`plugin`,`admin_form`,`config`)
VALUES ('google', 'GoogleCheckout', 'Google Checkout', '0', 'Socialstore_Payment_Plugin_Google', 'Socialstore_Form_Admin_Gateway_Google', NULL);

INSERT INTO `engine4_socialstore_gateways` (`gateway_id`,`title`,`description`,`enabled`,`plugin`,`admin_form`,`config`)
VALUES (
'2checkout', '2Checkout', '2Checkout', '0', 'Socialstore_Payment_Plugin_2Checkout', 'Socialstore_Form_Admin_Gateway_2Checkout', NULL
);

CREATE TABLE IF NOT EXISTS `engine4_socialstore_discounts` (
  `discount_id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `quantity` int(4) NOT NULL DEFAULT '0',
  `price` decimal(16,2) NOT NULL default '0.00',
  `date_start` datetime NULL,
  `date_end` datetime NULL,
  PRIMARY KEY (`discount_id`)
) ENGINE=InnoDB;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_social_store_fields_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_social_store_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`),
  KEY `order` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `engine4_social_store_fields_maps`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_social_store_fields_meta`
--

CREATE TABLE IF NOT EXISTS `engine4_social_store_fields_meta` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(32) NOT NULL DEFAULT '',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `display` tinyint(1) unsigned NOT NULL,
  `search` tinyint(1) unsigned NULL DEFAULT '0',
  `show` tinyint(1) unsigned NULL DEFAULT '1',
  `order` smallint(3) unsigned NOT NULL DEFAULT '999',
  `config` text NOT NULL,
  `validators` text COLLATE utf8_unicode_ci,
  `filters` text COLLATE utf8_unicode_ci,
  `style` text COLLATE utf8_unicode_ci,
  `error` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `engine4_social_store_fields_meta`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_social_store_fields_options`
--

CREATE TABLE IF NOT EXISTS `engine4_social_store_fields_options` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `engine4_social_store_fields_options`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_social_store_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_social_store_fields_search` (
  `item_id` int(11) NOT NULL,
  `price` double DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `price` (`price`),
  KEY `location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `engine4_social_store_fields_search`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_social_store_fields_values`
--

CREATE TABLE IF NOT EXISTS `engine4_social_store_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('socialstore_admin_main_fields', 'socialstore', 'Store Questions', '', '{"route":"admin_default","module":"socialstore","controller":"fields"}', 'socialstore_admin_main', '', 17);
