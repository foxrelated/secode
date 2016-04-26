INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('groupbuy', 'Group Buy', 'This is Group Buy module.', '4.04p1', 1, 'extra');

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('groupbuy_main', 'standard', 'Group Buy Main Navigation Menu', 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_groupbuy', 'groupbuy', 'GroupBuy', '', '{"route":"groupbuy_general"}', 'core_main', '', 5),

('groupbuy_main_browse', 'groupbuy', 'Browse Deals', '', '{"route":"groupbuy_general","action":"browse"}', 'groupbuy_main', '', 1),
('groupbuy_main_manage-selling', 'groupbuy', 'My Posted Deals', 'Groupbuy_Plugin_Menus::canManageDeals', '{"route":"groupbuy_general","action":"manage-selling"}', 'groupbuy_main', '', 2),
('groupbuy_main_account', 'groupbuy', 'My Account', 'Groupbuy_Plugin_Menus::canCreateAccounts', '{"route":"groupbuy_account"}', 'groupbuy_main', '', 4),
('groupbuy_main_create', 'groupbuy', 'Post A New Deal', 'Groupbuy_Plugin_Menus::canCreateDeals', '{"route":"groupbuy_general","action":"create"}', 'groupbuy_main', '', 5),

('core_admin_main_plugins_groupbuy', 'groupbuy', 'GroupBuy', '', '{"route":"admin_default","module":"groupbuy","controller":"manage"}', 'core_admin_main_plugins', '', 999),
('groupbuy_admin_main_manage', 'groupbuy', 'View Deals', '', '{"route":"admin_default","module":"groupbuy","controller":"manage"}', 'groupbuy_admin_main', '', 1),
('groupbuy_admin_main_settings', 'groupbuy', 'Global Settings', '', '{"route":"admin_default","module":"groupbuy","controller":"settings"}', 'groupbuy_admin_main', '', 2),
('groupbuy_admin_main_level', 'groupbuy', 'Member Level Settings', '', '{"route":"admin_default","module":"groupbuy","controller":"level"}', 'groupbuy_admin_main', '', 3),
('groupbuy_admin_main_localtions', 'groupbuy', 'Locations', '', '{"route":"admin_default","module":"groupbuy","controller":"location"}', 'groupbuy_admin_main', '', 4),
('groupbuy_admin_main_categories', 'groupbuy', 'Categories', '', '{"route":"admin_default","module":"groupbuy","controller":"category"}', 'groupbuy_admin_main', '', 5),
('groupbuy_admin_main_accounts', 'groupbuy', 'Accounts', '', '{"route":"admin_default","module":"groupbuy","controller":"account"}', 'groupbuy_admin_main', '', 7),
('groupbuy_admin_main_transactions', 'groupbuy', 'Transactions', '', '{"route":"admin_default","module":"groupbuy","controller":"transaction"}', 'groupbuy_admin_main', '', 8),
('groupbuy_admin_main_requests', 'groupbuy', 'Requests', '', '{"route":"admin_default","module":"groupbuy","controller":"request"}', 'groupbuy_admin_main', '', 9),
('groupbuy_admin_main_emailtemplates', 'groupbuy', 'Email Templates', '', '{"route":"admin_default","module":"groupbuy","controller":"mail", "action": "templates"}', 'groupbuy_admin_main', '', 10),
('groupbuy_admin_main_fields', 'groupbuy', 'Questions', '', '{"route":"admin_default","module":"groupbuy","controller":"fields"}', 'groupbuy_admin_main', '', 12),
('groupbuy_main_manage-buying', 'groupbuy', 'My Bought Deals', 'Groupbuy_Plugin_Menus::canManageDeals', '{"route":"groupbuy_general","action":"manage-buying"}', 'groupbuy_main', '', 3);

--
-- Table structure for table `engine4_groupbuy_orders`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_orders` (
`order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`gateway_id` int(11) unsigned NOT NULL,
`gateway_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
`status` enum('pending','completed','cancelled','failed') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'pending',
`creation_date` datetime NOT NULL,
`payment_date` datetime DEFAULT NULL,
`item_id` int(11) unsigned NOT NULL DEFAULT '0',
`price` decimal(16,2) NOT NULL DEFAULT '0',
`currency` char(3),
`security_code` text NOT NULL,
`invoice_code` text NOT NULL,
`params` text NULL,
PRIMARY KEY (`order_id`),
KEY `user_id` (`user_id`),
KEY `gateway_id` (`gateway_id`),
KEY `state` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_groupbuy_albums`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_albums` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) unsigned NOT NULL,
  `search` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(128) NOT NULL,
  `description` mediumtext NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `collectible_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`album_id`),
  KEY `FK_engine4_groupbuy_albums_engine4_groupbuy_deals` (`deal_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB;

--
-- Dumping data for table `engine4_groupbuy_albums`
--

-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_bills`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_bills` (
  `bill_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `invoice` varchar(70) NOT NULL,
  `sercurity` varchar(100) NOT NULL,
  `finance_account_id` int(11) DEFAULT NULL,
  `emal_receiver` varchar(255) NOT NULL,
  `payment_receiver_id` int(11) NOT NULL,
  `date_bill` datetime NOT NULL,
  `bill_status` int(3) NOT NULL DEFAULT '0',
  `amount` double(10,2) NOT NULL,
  `number` int(11) NOT NULL DEFAULT '1',
  `currency` VARCHAR( 10 ) NULL,
  PRIMARY KEY (`bill_id`),
  KEY `FK_engine4_groupbuy_bills_engine4_users` (`user_id`),
  KEY `FK_engine4_groupbuy_bills_engine4_users_2` (`owner_id`),
  KEY `FK_engine4_groupbuy_bills_engine4_groupbuy_deals` (`item_id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `engine4_groupbuy_bills`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_buy_cods`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_buy_cods` (
  `buycod_id` int(11) NOT NULL AUTO_INCREMENT,
  `tran_id` INT( 11 ) NOT NULL,
  `deal_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `verified` tinyint(3) unsigned NOT NULL,
  `status` tinyint(8) unsigned NOT NULL DEFAULT '0',
  `email` varchar(128) DEFAULT NULL,
  `buyer_name` varchar(256) NOT NULL,
  `address` varchar(256) NOT NULL,
  `phone` varchar(64) NOT NULL,
  `note` longtext NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`buycod_id`),
  KEY `FK_engine4_groupbuy_buy_cods_engine4_groupbuy_deals` (`deal_id`),
  KEY `FK_engine4_groupbuy_buy_cods_engine4_users` (`user_id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `engine4_groupbuy_buy_cods`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_buy_deals`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_buy_deals` (
  `buydeal_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `buy_date` datetime NOT NULL,
  `amount` float(10,2) NOT NULL DEFAULT '0.00',
  `number` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`buydeal_id`),
  KEY `FK_engine4_groupbuy_buy_deals_engine4_groupbuy_deals` (`item_id`),
  KEY `FK_engine4_groupbuy_buy_deals_engine4_users` (`owner_id`),
  KEY `FK_engine4_groupbuy_buy_deals_engine4_users_2` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB;

--
-- Dumping data for table `engine4_groupbuy_buy_deals`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_categories`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_categories` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `pleft` int(11) unsigned NOT NULL,
  `pright` int(11) unsigned NOT NULL,
  `level` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `pleft` (`pleft`),
  KEY `pright` (`pright`),
  KEY `level` (`level`)
) ENGINE=InnoDB;

INSERT IGNORE INTO `engine4_groupbuy_categories` (`category_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`) VALUES (1, 0, NULL, 1, 4, 0, 'All Categories');
INSERT INTO `engine4_groupbuy_categories` (`category_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`) VALUES (2, 0, 1, 2, 3, 1, 'Category 1');


--
-- Dumping data for table `engine4_groupbuy_categories`
--

--
-- Table structure for table `engine4_groupbuy_deals`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_deals` (
  `deal_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  `location_id` int(11) unsigned NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(8) NOT NULL DEFAULT '0',
  `stop` tinyint(1) NOT NULL DEFAULT '0',
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `rates` float NOT NULL DEFAULT '0',
  `current_sold` int(11) unsigned NOT NULL,
  `max_sold` int(11) unsigned NOT NULL,
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `min_sold` int(11) unsigned NOT NULL,
  `title` varchar(256) NOT NULL,
  `value_deal` double(10,2) unsigned NOT NULL DEFAULT '0.00',
  `discount` int(11) unsigned NOT NULL DEFAULT '0',
  `total_fee` decimal(11,2) NOT NULL DEFAULT '0.00',
  `price` double(10,2) unsigned NOT NULL DEFAULT '0.00',
  `latitude` double NOT NULL DEFAULT '0',
  `longitude` double NOT NULL DEFAULT '0',
  `published` tinyint(8) NOT NULL DEFAULT '0',
  `company_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(60) NOT NULL,
  `website` varchar(128) NOT NULL,
  `photo_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fine_print` longtext NOT NULL,
  `features` longtext NOT NULL,
  `description` longtext NOT NULL,
  `timezone` varchar(100) DEFAULT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`deal_id`),
  KEY `category_id` (`category_id`),
  KEY `location_id` (`location_id`),
  KEY `FK_engine4_groupbuy_deals_engine4_users` (`user_id`),
  KEY `is_delete` (`is_delete`),
  KEY `status` (`status`),
  KEY `stop` (`stop`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`),
  KEY `featured` (`featured`),
  KEY `creation_date` (`creation_date`),
  KEY `view_count` (`view_count`),
  KEY `rates` (`rates`),
  KEY `current_sold_max_sold` (`current_sold`,`max_sold`)
) ENGINE=InnoDB;

--
-- Dumping data for table `engine4_groupbuy_deals`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_deal_fields_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_deal_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`),
  KEY `order` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `engine4_groupbuy_deal_fields_maps`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_deal_fields_meta`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_deal_fields_meta` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(32) NOT NULL DEFAULT '',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `display` tinyint(1) unsigned NOT NULL,
  `search` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `order` smallint(3) unsigned NOT NULL DEFAULT '999',
  `config` text NOT NULL,
  `validators` text COLLATE utf8_unicode_ci,
  `filters` text COLLATE utf8_unicode_ci,
  `style` text COLLATE utf8_unicode_ci,
  `error` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `engine4_groupbuy_deal_fields_meta`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_deal_fields_options`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_deal_fields_options` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `engine4_groupbuy_deal_fields_options`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_deal_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_deal_fields_search` (
  `item_id` int(11) NOT NULL,
  `price` double DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `price` (`price`),
  KEY `location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `engine4_groupbuy_deal_fields_search`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_deal_fields_values`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_deal_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `engine4_groupbuy_deal_fields_values`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_emails`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_emails` (
  `email_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sended` tinyint(1) NOT NULL DEFAULT '0',
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` varchar(128) NOT NULL DEFAULT '',
  `creation_date` datetime NOT NULL,
  `send_from` varchar(128) NOT NULL DEFAULT '',
  `from_name` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(256) NOT NULL,
  `send_to` varchar(128) NOT NULL,
  `to_name` varchar(128) NOT NULL,
  `body_text` text,
  `body_html` text,
  PRIMARY KEY (`email_id`),
  KEY `sended` (`sended`),
  KEY `priority` (`priority`),
  KEY `type` (`type`),
  KEY `creation_date` (`creation_date`)
) ENGINE=InnoDB;

--
-- Dumping data for table `engine4_groupbuy_emails`
--

-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_locations`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_locations` (
  `location_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `pleft` int(11) NOT NULL,
  `pright` int(11) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL,
  PRIMARY KEY (`location_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `pleft` (`pleft`),
  KEY `pright` (`pright`),
  KEY `level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `engine4_groupbuy_locations` (`location_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`) VALUES (1, 0, NULL, 1, 4, 0, 'All Locations');
INSERT INTO `engine4_groupbuy_locations` (`location_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`) VALUES (2, 0, 1, 2, 3, 1, 'USA');


--
-- Dumping data for table `engine4_groupbuy_locations`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_mailtemplates`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_mailtemplates` (
  `mailtemplate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `vars` varchar(255) NOT NULL,
  PRIMARY KEY (`mailtemplate_id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

--
-- Dumping data for table `engine4_groupbuy_mailtemplates`
--

INSERT IGNORE INTO `engine4_groupbuy_mailtemplates` (`mailtemplate_id`, `type`, `vars`) VALUES
(2, 'groupbuy_header', ''),
(3, 'groupbuy_footer', ''),
(4, 'groupbuy_headermember', ''),
(5, 'groupbuy_footermember', ''),
(9, 'groupbuy_dealday', ''),
(10, 'groupbuy_dealsubscribed', '[website_name],[website_link],[verify_link],[verify_code]'),
(14, 'groupbuy_approvedeal', ''),
(15, 'groupbuy_codseller', ''),
(17, 'groupbuy_deletebuyToBuyer', ''),
(18, 'groupbuy_deletebuyToSeller', ''),
(20, 'groupbuy_codbuy', ''),
(21, 'groupbuy_buyerdealdel', ''),
(22, 'groupbuy_sellerdealdel', ''),
(23, 'groupbuy_buyerdealclosed', ''),
(24, 'groupbuy_sellerdealclosed', ''),
(25, 'groupbuy_dealrunning', ''),
(26, 'groupbuy_buydealbuyer', ''),
(27, 'groupbuy_buydealseller', ''),
(28, 'groupbuy_buygiftbuyer', ''),
(29, 'groupbuy_giftconfirm', ''),
(30, 'groupbuy_buygiftseller', '')
;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_payment_accounts`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_payment_accounts` (
  `paymentaccount_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `account_username` varchar(64) NOT NULL,
  `payment_type` int(11) NOT NULL,
  `gateway_id` INT( 11 ) NOT NULL DEFAULT '1',
  `account_status` int(11) NOT NULL DEFAULT '1',
  `last_check_out` bigint(11) DEFAULT NULL,
  `total_amount` decimal(11,2) DEFAULT NULL,
  `total_price_amount` decimal(11,2)   NOT NULL DEFAULT '0.00',
  `currency` VARCHAR( 10 ) NOT NULL,
  PRIMARY KEY (`paymentaccount_id`),
  KEY `FK_engine4_groupbuy_payment_accounts_engine4_users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Store account payment.' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `engine4_groupbuy_payment_accounts`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_payment_requests`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_payment_requests` (
  `paymentrequest_id` int(11) NOT NULL AUTO_INCREMENT,
  `dealbuy_id` int(11) unsigned NOT NULL,
  `request_user_id` int(11) unsigned NOT NULL,
  `request_payment_acount_id` int(11) NOT NULL,
  `request_amount` decimal(11,2) NOT NULL,
  `request_status` int(11) NOT NULL DEFAULT '0',
  `request_type` tinyint(1) NOT NULL DEFAULT '1',
  `request_date` datetime NOT NULL,
  `request_reason` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `request_answer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY (`paymentrequest_id`),
  KEY `FK_engine4_groupbuy_payment_requests_engine4_users` (`request_user_id`),
  KEY `FK_engine4_groupbuy_payment_requests_engine4_accounts` (`request_payment_acount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='engine4_groupbuy_payment_request' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `engine4_groupbuy_payment_requests`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_photos`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_photos` (
  `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) unsigned NOT NULL,
  `album_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `image_title` varchar(128) NOT NULL,
  `image_description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`photo_id`),
  KEY `FK_engine4_groupbuy_photos_engine4_groupbuy_deals` (`deal_id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `engine4_groupbuy_photos`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_rates`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_rates` (
  `rate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) unsigned NOT NULL,
  `poster_id` int(11) unsigned NOT NULL,
  `rate_number` int(11) unsigned NOT NULL,
  PRIMARY KEY (`rate_id`),
  KEY `FK_engine4_groupbuy_rates_engine4_users` (`deal_id`),
  KEY `FK_engine4_groupbuy_rates_engine4_users_2` (`poster_id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `engine4_groupbuy_rates`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_subscription_conditions`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_subscription_conditions` (
  `subscriptioncondition_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) unsigned NOT NULL DEFAULT '0',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `location_id` int(11) unsigned NOT NULL DEFAULT '0',
  `lat` int(11) `long` varchar(64) CHARACTER SET utf8 default '0',
  `long` int(11) `long` varchar(64) CHARACTER SET utf8 default '0',
  `within` int(11) unsigned NOT NULL DEFAULT '0',
  `age` int(11) unsigned NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`subscriptioncondition_id`),
  KEY `FK_engine4_groupbuy_subscription_groupbuy_categories` (`category_id`),
  KEY `FK_engine4_conditions_engine4_groupbuy_locations` (`location_id`),
  KEY `FK_engine4_groupbuy_subscription_groupbuy_subscription_contacts` (`contact_id`),
  KEY `age` (`age`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `engine4_groupbuy_subscription_conditions`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_subscription_contacts`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_subscription_contacts` (
  `subscriptioncontact_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `verified` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `verify_code` varchar(64) NOT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `email` varchar(128) NOT NULL,
  `verified_date` datetime NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`subscriptioncontact_id`),
  UNIQUE KEY `verified_code` (`verify_code`),
  KEY `verified` (`verified`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `engine4_groupbuy_subscription_contacts`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_subscription_relations`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_subscription_relations` (
  `subscriptionrelation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) unsigned NOT NULL,
  `contact_id` int(11) unsigned NOT NULL,
  `sended` tinyint(1) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `sended_date` datetime NOT NULL,
  PRIMARY KEY (`subscriptionrelation_id`),
  UNIQUE KEY `deal_id_contact_id` (`deal_id`,`contact_id`),
  KEY `FK_engine4_groupbuy_subscription_subscription_contacts` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `engine4_groupbuy_subscription_relations`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_transaction_trackings`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_transaction_trackings` (
  `transactiontracking_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned DEFAULT NULL,
  `user_buyer` int(11) DEFAULT NULL,
  `user_seller` int(11) DEFAULT NULL,
  `account_seller_id` int(11) DEFAULT NULL,
  `account_buyer_id` int(11) DEFAULT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `amount` decimal(11,2) DEFAULT NULL,
  `commission_fee` DECIMAL( 10, 2 ) NOT NULL,
  `currency` VARCHAR( 10 ) NOT NULL,
  `number` int(11) NOT NULL DEFAULT '1',
  `transaction_status` int(11) DEFAULT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`transactiontracking_id`),
  KEY `user_buyer` (`user_buyer`),
  KEY `user_seller` (`user_seller`),
  KEY `account_seller_id` (`account_seller_id`),
  KEY `account_buyer_id` (`account_buyer_id`),
  KEY `transaction_date` (`transaction_date`)
) ENGINE=InnoDB;

--
-- Dumping data for table `engine4_groupbuy_transaction_trackings`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_groupbuy_verifications`
--

CREATE TABLE IF NOT EXISTS `engine4_groupbuy_verifications` (
  `verification_id` int(11) NOT NULL AUTO_INCREMENT,
  `verify_code` varchar(64) NOT NULL,
  `verify_action` varchar(64) NOT NULL,
  `item_id` int(11) NOT NULL,
  `expired_date` datetime NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`verification_id`),
  KEY `verify_code` (`verify_code`),
  KEY `verify_action` (`verify_action`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `engine4_groupbuy_verifications`
--




--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
-- auth_view, auth_comment, auth_html
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, delete, edit, view, comment, css, style, max, photo
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'max' as `name`,
    3 as `value`,
    1000 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'max' as `name`,
    3 as `value`,
    50 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'groupbuy_deal' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('groupbuy_new', 'groupbuy', '{item:$subject} posted a new deal:', 1, 5, 1, 3, 1, 1),
('comment_groupbuy', 'groupbuy', '{item:$subject} commented on {item:$owner}''s {item:$object:deal}: {body:$body}', 1, 1, 1, 1, 1, 0);

INSERT INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('buy', 'groupbuy', '{item:$subject} has buy your {item:$object:$label}.', 0, '', 1)
;
-- Dumping data for table `engine4_core_pages`
INSERT IGNORE INTO `engine4_core_content` (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`) VALUES
(5,'widget','groupbuy.profile-sell-deals',531,10,'{"title":"Posted Deals","titleCount":true}'),
(5,'widget','groupbuy.profile-buy-deals',531,10,'{"title":"Bought Deals","titleCount":true}');

--
-- Dumping data for table `engine4_core_pages`

INSERT IGNORE  INTO `engine4_core_pages`(`name`,displayname,`url`,`title`,`description`,`keywords`,`custom`,fragment,layout,view_count) VALUES 
('groupbuy_index_browse','Group Buy Home',NULL,'Group Buy Home','This is the page deals','',1,0,'',0),
('groupbuy_index_listing','Group Buy Listing',NULL,'Group Buy Listing','This is the page listing deals','',1,0,'',0);

--
-- Dumping data for table `engine4_core_content`
--
INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_browse'), 'container', 'top', NULL, '1', '[""]', NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_browse'), 'container', 'middle', (SELECT LAST_INSERT_ID()) , '6', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_browse'),'widget','groupbuy.menu-deals',(SELECT LAST_INSERT_ID() + 1),3,NULL,NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_browse'),'widget','groupbuy.search-deals',(SELECT LAST_INSERT_ID() + 1),4,'{"title":"Search Deals"}',NULL);


INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_browse'), 'container', 'main', NULL, '2', '[""]', NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES 
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_browse'), 'container', 'middle', (SELECT LAST_INSERT_ID()) , '6', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_browse'), 'container', 'right', (SELECT LAST_INSERT_ID()) , '5', '[""]', NULL),

((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_browse'),'widget','groupbuy.featured-deals',(SELECT LAST_INSERT_ID() + 1),3,NULL,NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_browse'),'widget','groupbuy.listing-deals',(SELECT LAST_INSERT_ID() + 1),4,NULL,NULL),

((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_browse'),'widget','groupbuy.email-form',(SELECT LAST_INSERT_ID() + 2),5,NULL,NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_browse'),'widget','groupbuy.latest-deals',(SELECT LAST_INSERT_ID() + 2),5,'{"title":"Most Latest Deals"}',NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_listing'), 'container', 'top', NULL, '1', '[""]', NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_listing'), 'container', 'main', NULL, '2', '[""]', NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_listing'), 'container', 'middle', (SELECT LAST_INSERT_ID()) , '6', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_listing'),'widget','groupbuy.menu-deals',(SELECT LAST_INSERT_ID() + 1),5,NULL,NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_listing'),'widget','groupbuy.search-deals',(SELECT LAST_INSERT_ID() + 1),6,NULL,NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='groupbuy_index_listing'),'widget','groupbuy.search-listing-deals',(SELECT LAST_INSERT_ID() + 1),7,NULL,NULL);


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
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_fields', 'groupbuy', 'Questions', '', '{"route":"admin_default","module":"groupbuy","controller":"fields"}', 'groupbuy_admin_main', '', 1, 0, 13);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_main_manage-buying', 'groupbuy', 'My Bought Deals', 'Groupbuy_Plugin_Menus::canManageDeals', '{"route":"groupbuy_general","action":"manage-buying"}', 'groupbuy_main', '', 1, 0, 3);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_currency', 'groupbuy', 'Currencies', '', '{"route":"admin_default","module":"groupbuy","controller":"currency"}', 'groupbuy_admin_main', '', 1, 0, 11);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('groupbuy_admin_main_vats', 'groupbuy', 'VATs', '', '{"route":"admin_default","module":"groupbuy","controller":"vat"}', 'groupbuy_admin_main', '', 1, 0, 13);


INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('USD', 'U.S. Dollar', '$', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
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
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('JPY', 'Japanese Yen', '�', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('ILS', 'Israeli New Shekel', 'ILS', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('HUF', 'Hungarian Forint', 'HUF', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('HKD', 'Hong Kong Dollar', '$', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('EUR', 'Euro', '&euro;', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('DKK', 'Danish Krone', 'DKK', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('CZK', 'Czech Koruna', 'CZK', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('CAD', 'Canadian Dollar', 'C $', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('GBP', 'British Pound', '�', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('BRL', 'Brazilian Real	', 'BRL', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');
INSERT IGNORE INTO `engine4_groupbuy_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES ('AUD', 'Australian Dollar', 'A $', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol');


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