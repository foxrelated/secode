--
-- Database: `products_store`
--

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('socialstore', 'Store', 'Store Description.', '4.01', 1, 'extra');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('social_product_new', 'socialstore', '{item:$subject} created a new product:', 1, 5, 1, 3, 1, 1),
('social_store_new', 'socialstore', '{item:$subject} created a new store:', 1, 5, 1, 3, 1, 1);


-- --------------------------------------------------------


--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('socialstore_main', 'standard', 'Store Main Navigation Menu', 999);
INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_admin_main_plugins_socialstore', 'socialstore', 'Store', '', '{"route":"admin_default","module":"socialstore","controller":"manage-store"}', 'core_admin_main_plugins', '', 1, 0, 999),
('core_main_socialstore', 'socialstore', 'Store', '', '{"route":"socialstore_general"}', 'core_main', '', 1, 0, 5),
('socialstore_admin_main_managestore', 'socialstore', 'Stores', '', '{"route":"admin_default","module":"socialstore","controller":"manage-store"}', 'socialstore_admin_main', '', 1, 0, 1),
('socialstore_admin_main_settings', 'socialstore', 'Global Settings', '', '{"route":"admin_default","module":"socialstore","controller":"settings"}', 'socialstore_admin_main', '', 1, 0, 3),
('socialstore_admin_main_level', 'socialstore', 'Member Level Settings', '', '{"route":"admin_default","module":"socialstore","controller":"level"}', 'socialstore_admin_main', '', 1, 0, 4),
('socialstore_admin_main_localtions', 'socialstore', 'Locations', '', '{"route":"admin_default","module":"socialstore","controller":"location"}', 'socialstore_admin_main', '', 1, 0, 6),
('socialstore_admin_main_categories', 'socialstore', 'Categories', '', '{"route":"admin_default","module":"socialstore","controller":"category"}', 'socialstore_admin_main', '', 1, 0, 5),
('socialstore_admin_main_accounts', 'socialstore', 'Accounts', '', '{"route":"admin_default","module":"socialstore","controller":"account"}', 'socialstore_admin_main', '', 0, 0, 9),
('socialstore_admin_main_transactions', 'socialstore', 'Transactions', '', '{"route":"admin_default","module":"socialstore","controller":"transaction"}', 'socialstore_admin_main', '', 1, 0, 11),
('socialstore_admin_main_request', 'socialstore', 'Requests', '', '{"route":"admin_default","module":"socialstore","controller":"request"}', 'socialstore_admin_main', '', 1, 0, 12),
('socialstore_admin_main_emailtemplates', 'socialstore', 'Email Templates', '', '{"route":"admin_default","module":"socialstore","controller":"mail", "action": "templates"}', 'socialstore_admin_main', '', 1, 0, 14),
('socialstore_admin_main_statistics', 'socialstore', 'Statistics', '', '{"route":"admin_default","module":"socialstore","controller":"statistic"}', 'socialstore_admin_main', '', 1, 0, 11),
('socialstore_admin_main_gateway', 'socialstore', 'Gateway', '', '{"route":"admin_default","module":"socialstore","controller":"gateway"}', 'socialstore_admin_main', '', 1, 0, 10),
('socialstore_admin_main_currency', 'socialstore', 'Currencies', '', '{"route":"admin_default","module":"socialstore","controller":"currency"}', 'socialstore_admin_main', '', 1, 0, 8),
('socialstore_admin_main_taxes', 'socialstore', 'Taxes', '', '{"route":"admin_default","module":"socialstore","controller":"taxes"}', 'socialstore_admin_main', '', 1, 0, 7),
('socialstore_admin_main_helps', 'socialstore', 'Helps', '', '{"route":"admin_default","module":"socialstore","controller":"helps"}', 'socialstore_admin_main', '', 1, 0, 15),
('socialstore_main_mystore', 'socialstore', 'My Store', 'Socialstore_Plugin_Menus::canMyStore', '{"route":"socialstore_extended","controller":"my-store"}', 'socialstore_main', '', 1, 0, 3),
('socialstore_main_myfavouriteproduct', 'socialstore', 'Favourite Products', 'Socialstore_Plugin_Menus::canMyFavourite', '{"route":"socialstore_extended","controller":"my-favourite-product"}', 'socialstore_main', '', 1, 0, 4),
('socialstore_main_myfollowstore', 'socialstore', 'Following Stores', 'Socialstore_Plugin_Menus::canMyFollowing', '{"route":"socialstore_extended","controller":"my-follow-store"}', 'socialstore_main', '', 1, 0, 5),
('socialstore_main_mycart', 'socialstore', 'Shopping Cart', 'Socialstore_Plugin_Menus::canMyCart', '{"route":"socialstore_extended","controller":"my-cart"}', 'socialstore_main', '', 1, 0, 6),
('socialstore_main_myorders', 'socialstore', 'My Orders', 'Socialstore_Plugin_Menus::canMyOrders', '{"route":"socialstore_extended","controller":"my-orders"}', 'socialstore_main', '', 1, 0, 7),
('socialstore_main_faqs', 'socialstore', 'FAQs', 'Socialstore_Plugin_Menus::canFaqs', '{"route":"socialstore_extended","controller":"faqs"}', 'socialstore_main', '', 1, 0, 9),
('socialstore_main_store', 'socialstore', 'Stores', 'Socialstore_Plugin_Menus::canStore', '{"route":"socialstore_extended","controller":"index"}', 'socialstore_main', '', 1, 0, 1),
('socialstore_main_product', 'socialstore', 'Products', 'Socialstore_Plugin_Menus::canProduct', '{"route":"socialstore_extended","controller":"product"}', 'socialstore_main', '', 1, 0, 2),
('socialstore_admin_main_manageproduct', 'socialstore', 'Products', '', '{"route":"admin_default","module":"socialstore","controller":"manage-product"}', 'socialstore_admin_main', '', 1, 0, 2),
('socialstore_admin_main_refund', 'socialstore', 'Refunds', '', '{"route":"admin_default","module":"socialstore","controller":"refund"}', 'socialstore_admin_main', '', 1, 0, 13),
('socialstore_admin_main_faqs', 'socialstore', 'FAQs', '', '{"route":"admin_default","module":"socialstore","controller":"faqs"}', 'socialstore_admin_main', '', 1, 0, 16),
('socialstore_main_helps', 'socialstore', 'Help', 'Socialstore_Plugin_Menus::canHelp', '{"route":"socialstore_extended","controller":"help"}', 'socialstore_main', '', 1, 0, 8),
('socialstore_admin_main_order', 'socialstore', 'Orders', NULL, '{"route":"admin_default","module":"socialstore","controller":"order"}', 'socialstore_admin_main', NULL, 1, 0, 11);

-- --------------------------------------------------------





--
-- Table structure for table `engine4_socialstore_stores`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_stores` (
  `store_id` int(11) unsigned NOT NULL auto_increment,
  `owner_id` int(11) unsigned NOT NULL,
  `photo_id` int(10) unsigned NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `search` tinyint(1) unsigned NOT NULL default '0',
  `slug` varchar(256) NOT NULL,
  `location_id` int(11) unsigned NOT NULL default '0',
  `view_status` enum('show','hide') NOT NULL default 'hide',
  `approve_status` enum('new','waiting','approved','denied') NOT NULL default 'new',
  `sponsored` tinyint(1) unsigned NOT NULL default '0',
  `sold_products` int(11) unsigned NOT NULL default '0',
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `follow_count` int(11) unsigned NOT NULL default '0',
  `rate_ave` smallint(5) unsigned NOT NULL default '0',
  `featured` tinyint(1) unsigned NOT NULL default '0',
  `title` varchar(128) NOT NULL,
  `contact_name` varchar(255) default NULL,
  `contact_email` varchar(255) default NULL,
  `contact_phone` varchar(255) default NULL,
  `contact_address` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `contact_fax` varchar(255) default NULL,
  `contact_website` varchar(255) default NULL,
  `description` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `approved_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY  (`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_accounts`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_accounts` (
  `account_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` int(11) unsigned NOT NULL default '0',
  `owner_id` int(11) unsigned NOT NULL default '0',
  `gateway_id` varchar(32) NOT NULL default 'paypal',
  `name` varchar(128) NOT NULL,
  `account_username` varchar(128) default NULL,
  `account_password` varchar(128) default NULL,
  `config` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY  (`account_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_addresses`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_addresses` (
  `address_id` bigint(20) NOT NULL auto_increment,
  `address_type` enum('billing','shipping') NOT NULL,
  `order_id` varchar(32) NOT NULL,
  `firstname` varchar(64) NOT NULL,
  `lastname` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `company` varchar(256) default NULL,
  `street` varchar(256) NOT NULL,
  `street2` varchar(256) NOT NULL,
  `phone` varchar(64) NOT NULL,
  `fax` varchar(64) default NULL,
  `postcode` varchar(64) NOT NULL,
  `country` varchar(64) NOT NULL,
  `city` varchar(64) NOT NULL,
  `region` varchar(64) NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`address_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_cartitems`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_cartitems` (
  `cartitem_id` bigint(20) unsigned NOT NULL auto_increment,
  `cart_id` bigint(20) unsigned NOT NULL default '0',
  `owner_id` int(11) unsigned NOT NULL default '0',
  `item_id` bigint(20) unsigned NOT NULL default '0',
  `item_qty` int(11) unsigned NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`cartitem_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_carts`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_carts` (
  `cart_id` bigint(20) unsigned NOT NULL auto_increment,
  `owner_id` int(11) unsigned NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`cart_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `engine4_socialstore_carts`
--

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_categories`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `level` smallint(5) unsigned NOT NULL default '0',
  `ordering` smallint(5) unsigned NOT NULL default '0',
  `pid` int(11) unsigned NOT NULL default '0',
  `p0` int(11) unsigned NOT NULL default '0',
  `p1` int(11) unsigned NOT NULL default '0',
  `p2` int(11) unsigned NOT NULL default '0',
  `p3` int(11) unsigned NOT NULL default '0',
  `p4` int(11) unsigned NOT NULL default '0',
  `p5` int(11) unsigned NOT NULL default '0',
  `p6` int(11) unsigned NOT NULL default '0',
  `p7` int(11) unsigned NOT NULL default '0',
  `p8` int(11) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=93 ;

--
-- Dumping data for table `engine4_socialstore_categories`
--

INSERT IGNORE INTO `engine4_socialstore_categories` (`category_id`, `level`, `ordering`, `pid`, `p0`, `p1`, `p2`, `p3`, `p4`, `p5`, `p6`, `p7`, `p8`, `name`) VALUES
(1, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 'Books'),
(2, 1, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 'Movies, Music & Games'),
(3, 1, 0, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 'Electronics & Computers'),
(14, 2, 0, 1, 0, 1, 14, 0, 0, 0, 0, 0, 0, 'Books'),
(16, 2, 0, 1, 0, 1, 16, 0, 0, 0, 0, 0, 0, 'Audiobooks'),
(17, 2, 0, 1, 0, 1, 17, 0, 0, 0, 0, 0, 0, 'Magazines'),
(18, 2, 0, 1, 0, 1, 18, 0, 0, 0, 0, 0, 0, 'eBooks'),
(19, 2, 0, 2, 0, 2, 19, 0, 0, 0, 0, 0, 0, 'Movies & TV'),
(20, 2, 0, 2, 0, 2, 20, 0, 0, 0, 0, 0, 0, 'Music'),
(21, 2, 0, 2, 0, 2, 21, 0, 0, 0, 0, 0, 0, 'MP3 Downloads'),
(24, 2, 0, 2, 0, 2, 24, 0, 0, 0, 0, 0, 0, 'Game Downloads'),
(25, 2, 0, 3, 0, 3, 25, 0, 0, 0, 0, 0, 0, 'TV & Video'),
(26, 2, 0, 3, 0, 3, 26, 0, 0, 0, 0, 0, 0, 'Home Audio & Theater'),
(27, 2, 0, 3, 0, 3, 27, 0, 0, 0, 0, 0, 0, 'Cell Phones & Accessories'),
(39, 2, 0, 3, 0, 3, 39, 0, 0, 0, 0, 0, 0, 'PC Games');
-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_countries`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_countries` (
  `code` char(2) default NULL,
  `country` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `engine4_socialstore_countries`
--

INSERT IGNORE INTO `engine4_socialstore_countries` (`code`, `country`) VALUES
('AF', 'Afghanistan'),
('AL', 'Albania'),
('DZ', 'Algeria'),
('AS', 'American Samoa'),
('AD', 'Andorra'),
('AO', 'Angola'),
('AI', 'Anguilla'),
('AQ', 'Antarctica'),
('AG', 'Antigua and Barbuda'),
('AR', 'Argentina'),
('AM', 'Armenia'),
('AW', 'Aruba'),
('AU', 'Australia'),
('AT', 'Austria'),
('AZ', 'Azerbaidjan'),
('BS', 'Bahamas'),
('BH', 'Bahrain'),
('BD', 'Banglades'),
('BB', 'Barbados'),
('BY', 'Belarus'),
('BE', 'Belgium'),
('BZ', 'Belize'),
('BJ', 'Benin'),
('BM', 'Bermuda'),
('BO', 'Bolivia'),
('BA', 'Bosnia-Herzegovina'),
('BW', 'Botswana'),
('BV', 'Bouvet Island'),
('BR', 'Brazil'),
('IO', 'British Indian O. Terr.'),
('BN', 'Brunei Darussalam'),
('BG', 'Bulgaria'),
('BF', 'Burkina Faso'),
('BI', 'Burundi'),
('BT', 'Buthan'),
('KH', 'Cambodia'),
('CM', 'Cameroon'),
('CA', 'Canada'),
('CV', 'Cape Verde'),
('KY', 'Cayman Islands'),
('CF', 'Central African Rep.'),
('TD', 'Chad'),
('CL', 'Chile'),
('CN', 'China'),
('CX', 'Christmas Island'),
('CC', 'Cocos (Keeling) Isl.'),
('CO', 'Colombia'),
('KM', 'Comoros'),
('CG', 'Congo'),
('CK', 'Cook Islands'),
('CR', 'Costa Rica'),
('HR', 'Croatia'),
('CU', 'Cuba'),
('CY', 'Cyprus'),
('CZ', 'Czech Republic'),
('CS', 'Czechoslovakia'),
('DK', 'Denmark'),
('DJ', 'Djibouti'),
('DM', 'Dominica'),
('DO', 'Dominican Republic'),
('TP', 'East Timor'),
('EC', 'Ecuador'),
('EG', 'Egypt'),
('SV', 'El Salvador'),
('GQ', 'Equatorial Guinea'),
('EE', 'Estonia'),
('ET', 'Ethiopia'),
('FK', 'Falkland Isl.(UK)'),
('FO', 'Faroe Islands'),
('FJ', 'Fiji'),
('FI', 'Finland'),
('FR', 'France'),
('FX', 'France (European Ter.)'),
('TF', 'French Southern Terr.'),
('GA', 'Gabon'),
('GM', 'Gambia'),
('GE', 'Georgia'),
('DE', 'Germany'),
('GH', 'Ghana'),
('GI', 'Gibraltar'),
('GB', 'Great Britain (UK)'),
('GR', 'Greece'),
('GL', 'Greenland'),
('GD', 'Grenada'),
('GP', 'Guadeloupe (Fr.)'),
('GU', 'Guam (US)'),
('GT', 'Guatemala'),
('GN', 'Guinea'),
('GW', 'Guinea Bissau'),
('GY', 'Guyana'),
('GF', 'Guyana (Fr.)'),
('HT', 'Haiti'),
('HM', 'Heard & McDonald Isl.'),
('HN', 'Honduras'),
('HK', 'Hong Kong'),
('HU', 'Hungary'),
('IS', 'Iceland'),
('IN', 'India'),
('ID', 'Indonesia'),
('IR', 'Iran'),
('IQ', 'Iraq'),
('IE', 'Ireland'),
('IL', 'Israel'),
('IT', 'Italy'),
('CI', 'Ivory Coast'),
('JM', 'Jamaica'),
('JP', 'Japan'),
('JO', 'Jordan'),
('KZ', 'Kazachstan'),
('KE', 'Kenya'),
('KG', 'Kirgistan'),
('KI', 'Kiribati'),
('KP', 'Korea (North)'),
('KR', 'Korea (South)'),
('KW', 'Kuwait'),
('LA', 'Laos'),
('LV', 'Latvia'),
('LB', 'Lebanon'),
('LS', 'Lesotho'),
('LR', 'Liberia'),
('LY', 'Libya'),
('LI', 'Liechtenstein'),
('LT', 'Lithuania'),
('LU', 'Luxembourg'),
('MO', 'Macau'),
('MG', 'Madagascar'),
('MW', 'Malawi'),
('MY', 'Malaysia'),
('MV', 'Maldives'),
('ML', 'Mali'),
('MT', 'Malta'),
('MH', 'Marshall Islands'),
('MQ', 'Martinique (Fr.)'),
('MR', 'Mauritania'),
('MU', 'Mauritius'),
('MX', 'Mexico'),
('FM', 'Micronesia'),
('MD', 'Moldavia'),
('MC', 'Monaco'),
('MN', 'Mongolia'),
('MS', 'Montserrat'),
('MA', 'Morocco'),
('MZ', 'Mozambique'),
('MM', 'Myanmar'),
('NA', 'Namibia'),
('NR', 'Nauru'),
('NP', 'Nepal'),
('AN', 'Netherland Antilles'),
('NL', 'Netherlands'),
('NT', 'Neutral Zone'),
('NC', 'New Caledonia (Fr.)'),
('NZ', 'New Zealand'),
('NI', 'Nicaragua'),
('NE', 'Niger'),
('NG', 'Nigeria'),
('NU', 'Niue'),
('NF', 'Norfolk Island'),
('MP', 'Northern Mariana Isl.'),
('NO', 'Norway'),
('OM', 'Oman'),
('PK', 'Pakistan'),
('PW', 'Palau'),
('PA', 'Panama'),
('PG', 'Papua New'),
('PY', 'Paraguay'),
('PE', 'Peru'),
('PH', 'Philippines'),
('PN', 'Pitcairn'),
('PL', 'Poland'),
('PF', 'Polynesia (Fr.)'),
('PT', 'Portugal'),
('PR', 'Puerto Rico (US)'),
('QA', 'Qatar'),
('RE', 'Reunion (Fr.)'),
('RO', 'Romania'),
('RU', 'Russian Federation'),
('RW', 'Rwanda'),
('LC', 'Saint Lucia'),
('WS', 'Samoa'),
('SM', 'San Marino'),
('SA', 'Saudi Arabia'),
('SN', 'Senegal'),
('SC', 'Seychelles'),
('SL', 'Sierra Leone'),
('SG', 'Singapore'),
('SK', 'Slovak Republic'),
('SI', 'Slovenia'),
('SB', 'Solomon Islands'),
('SO', 'Somalia'),
('ZA', 'South Africa'),
('SU', 'Soviet Union'),
('ES', 'Spain'),
('LK', 'Sri Lanka'),
('SH', 'St. Helena'),
('PM', 'St. Pierre & Miquelon'),
('ST', 'St. Tome and Principe'),
('KN', 'St.Kitts Nevis Anguilla'),
('VC', 'St.Vincent & Grenadines'),
('SD', 'Sudan'),
('SR', 'Suriname'),
('SJ', 'Svalbard & Jan Mayen Is'),
('SZ', 'Swaziland'),
('SE', 'Sweden'),
('CH', 'Switzerland'),
('SY', 'Syria'),
('TJ', 'Tadjikistan'),
('TW', 'Taiwan'),
('TZ', 'Tanzania'),
('TH', 'Thailand'),
('TG', 'Togo'),
('TK', 'Tokelau'),
('TO', 'Tonga'),
('TT', 'Trinidad & Tobago'),
('TN', 'Tunisia'),
('TR', 'Turkey'),
('TM', 'Turkmenistan'),
('TC', 'Turks & Caicos Islands'),
('TV', 'Tuvalu'),
('UG', 'Uganda'),
('UA', 'Ukraine'),
('AE', 'United Arab Emirates'),
('UK', 'United Kingdom'),
('US', 'United States'),
('UY', 'Uruguay'),
('UM', 'US Minor outlying Isl.'),
('UZ', 'Uzbekistan'),
('VU', 'Vanuatu'),
('VA', 'Vatican City State'),
('VE', 'Venezuela'),
('VN', 'Vietnam'),
('VG', 'Virgin Islands (British)'),
('VI', 'Virgin Islands (US)'),
('WF', 'Wallis & Futuna Islands'),
('EH', 'Western Sahara'),
('YE', 'Yemen'),
('YU', 'Yugoslavia'),
('ZR', 'Zaire'),
('ZM', 'Zambia'),
('ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_currencies`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_currencies` (
  `code` varchar(10) NOT NULL,
  `name` varchar(64) NOT NULL,
  `symbol` varchar(50) NOT NULL,
  `status` enum('Enable','Disable') NOT NULL default 'Enable',
  `position` enum('Standard','Left','Right') NOT NULL default 'Standard',
  `precision` tinyint(4) unsigned NOT NULL default '2',
  `script` tinyint(64) default NULL,
  `format` varchar(64) default NULL,
  `display` enum('No Symbol','Use Symbol','Use Shortname','Use Name') NOT NULL default 'Use Symbol',
  PRIMARY KEY  (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `engine4_socialstore_currencies`
--

INSERT IGNORE INTO `engine4_socialstore_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES
('AUD', 'Australian Dollar', 'A$', 'Enable', 'Standard', 2, NULL, NULL, 'No Symbol'),
('BRL', 'Brazilian Real	', 'BRL', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('CAD', 'Canadian Dollar', 'C$', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('CHF', 'Swiss Franc', 'CHF', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('CZK', 'Czech Koruna', 'CZK', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('DKK', 'Danish Krone', 'DKK', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('EUR', 'Euro', '&euro;', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('GBP', 'British Pound', '&pound;', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('HKD', 'Hong Kong Dollar', 'H$', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('HUF', 'Hungarian Forint', 'HUF', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('ILS', 'Israeli New Shekel', 'ILS', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('JPY', 'Japanese Yen', '&yen;', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('MXN', 'Mexican Peso', 'MXN', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('MYR', 'Malaysian Ringgit', 'MYR', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('NOK', 'Norwegian Krone', 'NOK', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('NZD', 'New Zealand Dollar', '$', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('PHP', 'Philippine Peso', 'PHP', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('PLN', 'Polish Zloty', 'PLN', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('SEK', 'Swedish Krona', 'SEK', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('SGD', 'Singapore Dollar', '$', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('THB', 'Thai Baht', 'THB', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('TRY', 'Turkish Lira', 'TRY', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('TWD', 'New Taiwan Dollar', 'TWD', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('USD', 'U.S. Dollar', '$', 'Enable', 'Standard', 1, NULL, NULL, 'Use Symbol');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_emails`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_emails` (
  `email_id` int(11) unsigned NOT NULL auto_increment,
  `sended` tinyint(1) NOT NULL default '0',
  `priority` tinyint(3) unsigned NOT NULL default '0',
  `type` varchar(128) NOT NULL default '',
  `creation_date` datetime NOT NULL,
  `send_from` varchar(128) NOT NULL default '',
  `from_name` varchar(128) NOT NULL default '',
  `subject` varchar(256) NOT NULL,
  `send_to` varchar(128) NOT NULL,
  `to_name` varchar(128) NOT NULL,
  `body_text` text,
  `body_html` text,
  PRIMARY KEY  (`email_id`),
  KEY `sended` (`sended`),
  KEY `priority` (`priority`),
  KEY `type` (`type`),
  KEY `creation_date` (`creation_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_faqs`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_faqs` (
  `faq_id` int(11) unsigned NOT NULL auto_increment,
  `status` enum('show','hide') NOT NULL default 'hide',
  `ordering` int(11) unsigned NOT NULL default '0',
  `owner_id` int(11) unsigned NOT NULL default '0',
  `category_id` int(11) unsigned NOT NULL default '0',
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`faq_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_favourites`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_favourites` (
  `favourite_id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`favourite_id`),
  UNIQUE KEY `user_id_product_id` (`user_id`,`product_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_follows`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_follows` (
  `follow_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `store_id` int(11) unsigned NOT NULL,
  `creation_date` datetime default NULL,
  PRIMARY KEY  (`follow_id`),
  UNIQUE KEY `user_id_store_id` (`user_id`,`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_gateways`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_gateways` (
  `gateway_id` varchar(32) NOT NULL,
  `title` varchar(128) default NULL,
  `description` text,
  `enabled` tinyint(1) unsigned NOT NULL default '0',
  `plugin` varchar(128) NOT NULL,
  `admin_form` varchar(128) NOT NULL,
  `config` text,
  PRIMARY KEY  (`gateway_id`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `engine4_socialstore_gateways`
--

INSERT INTO `engine4_socialstore_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `admin_form`, `config`) VALUES
('paypal', 'PayPal', 'PayPal', 1, 'Socialstore_Payment_Plugin_Paypal', 'Socialstore_Form_Admin_Gateway_PayPal', '');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_gatewaytransactions`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_gatewaytransactions` (
  `gatewaytransaction_id` varchar(128) NOT NULL,
  `parent_id` varchar(128) default NULL,
  `request` text NOT NULL,
  PRIMARY KEY  (`gatewaytransaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_helppages`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_helppages` (
  `helppage_id` int(11) unsigned NOT NULL auto_increment,
  `status` enum('show','hide') NOT NULL,
  `ordering` smallint(5) unsigned NOT NULL default '999',
  `category_id` int(11) unsigned NOT NULL default '0',
  `owner_id` int(11) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`helppage_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `engine4_socialstore_helppages`
--

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_locations`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_locations` (
  `location_id` int(11) unsigned NOT NULL auto_increment,
  `level` smallint(5) unsigned NOT NULL default '0',
  `ordering` smallint(5) unsigned NOT NULL default '0',
  `pid` int(11) unsigned NOT NULL default '0',
  `p0` int(11) unsigned NOT NULL default '0',
  `p1` int(11) unsigned NOT NULL default '0',
  `p2` int(11) unsigned NOT NULL default '0',
  `p3` int(11) unsigned NOT NULL default '0',
  `p4` int(11) unsigned NOT NULL default '0',
  `p5` int(11) unsigned NOT NULL default '0',
  `p6` int(11) unsigned NOT NULL default '0',
  `p7` int(11) unsigned NOT NULL default '0',
  `p8` int(11) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY  (`location_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=70 ;

--
-- Dumping data for table `engine4_socialstore_locations`
--

INSERT INTO `engine4_socialstore_locations` (`location_id`, `level`, `ordering`, `pid`, `p0`, `p1`, `p2`, `p3`, `p4`, `p5`, `p6`, `p7`, `p8`, `name`) VALUES
(9, 1, 0, 0, 0, 9, 0, 0, 0, 0, 0, 0, 0, 'United States'),
(11, 1, 0, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 'France'),
(12, 2, 0, 11, 0, 11, 12, 0, 0, 0, 0, 0, 0, 'Sub France'),
(20, 1, 0, 0, 0, 20, 0, 0, 0, 0, 0, 0, 0, 'United of Kingdom'),
(21, 2, 0, 9, 0, 9, 21, 0, 0, 0, 0, 0, 0, 'Alabama'),
(22, 2, 0, 9, 0, 9, 22, 0, 0, 0, 0, 0, 0, 'Alaska'),
(23, 2, 0, 9, 0, 9, 23, 0, 0, 0, 0, 0, 0, 'Arizona'),
(24, 2, 0, 9, 0, 9, 24, 0, 0, 0, 0, 0, 0, 'Arkansas'),
(25, 2, 0, 9, 0, 9, 25, 0, 0, 0, 0, 0, 0, 'California'),
(26, 2, 0, 9, 0, 9, 26, 0, 0, 0, 0, 0, 0, 'Colorado'),
(27, 2, 0, 9, 0, 9, 27, 0, 0, 0, 0, 0, 0, 'Connecticut'),
(28, 2, 0, 9, 0, 9, 28, 0, 0, 0, 0, 0, 0, 'Delaware'),
(29, 2, 0, 9, 0, 9, 29, 0, 0, 0, 0, 0, 0, 'Florida'),
(30, 2, 0, 9, 0, 9, 30, 0, 0, 0, 0, 0, 0, 'Georgia'),
(31, 2, 0, 9, 0, 9, 31, 0, 0, 0, 0, 0, 0, 'Hawaii'),
(32, 2, 0, 9, 0, 9, 32, 0, 0, 0, 0, 0, 0, 'Idaho'),
(33, 2, 0, 9, 0, 9, 33, 0, 0, 0, 0, 0, 0, 'Illinois'),
(34, 2, 0, 9, 0, 9, 34, 0, 0, 0, 0, 0, 0, 'Indiana'),
(35, 2, 0, 9, 0, 9, 35, 0, 0, 0, 0, 0, 0, 'Iowa'),
(36, 2, 0, 9, 0, 9, 36, 0, 0, 0, 0, 0, 0, 'Kansas'),
(37, 2, 0, 9, 0, 9, 37, 0, 0, 0, 0, 0, 0, 'Kentucky'),
(38, 2, 0, 9, 0, 9, 38, 0, 0, 0, 0, 0, 0, 'Louisiana'),
(39, 2, 0, 9, 0, 9, 39, 0, 0, 0, 0, 0, 0, 'Maine'),
(40, 2, 0, 9, 0, 9, 40, 0, 0, 0, 0, 0, 0, 'Maryland'),
(41, 2, 0, 9, 0, 9, 41, 0, 0, 0, 0, 0, 0, 'Massachusetts'),
(42, 2, 0, 9, 0, 9, 42, 0, 0, 0, 0, 0, 0, 'Michigan'),
(43, 2, 0, 9, 0, 9, 43, 0, 0, 0, 0, 0, 0, 'Minnesota'),
(44, 2, 0, 9, 0, 9, 44, 0, 0, 0, 0, 0, 0, 'Mississippi'),
(45, 2, 0, 9, 0, 9, 45, 0, 0, 0, 0, 0, 0, 'Missouri'),
(46, 2, 0, 9, 0, 9, 46, 0, 0, 0, 0, 0, 0, 'Montana'),
(47, 2, 0, 9, 0, 9, 47, 0, 0, 0, 0, 0, 0, 'Nebraska'),
(48, 2, 0, 9, 0, 9, 48, 0, 0, 0, 0, 0, 0, 'Nevada'),
(49, 2, 0, 9, 0, 9, 49, 0, 0, 0, 0, 0, 0, 'New Hampshire'),
(50, 2, 0, 9, 0, 9, 50, 0, 0, 0, 0, 0, 0, 'New Jersey'),
(51, 2, 0, 9, 0, 9, 51, 0, 0, 0, 0, 0, 0, 'New Mexico'),
(52, 2, 0, 9, 0, 9, 52, 0, 0, 0, 0, 0, 0, 'New York'),
(53, 2, 0, 9, 0, 9, 53, 0, 0, 0, 0, 0, 0, 'North Carolina'),
(54, 2, 0, 9, 0, 9, 54, 0, 0, 0, 0, 0, 0, 'North Dakota'),
(55, 2, 0, 9, 0, 9, 55, 0, 0, 0, 0, 0, 0, 'Ohio'),
(56, 2, 0, 9, 0, 9, 56, 0, 0, 0, 0, 0, 0, 'Oklahoma'),
(57, 2, 0, 9, 0, 9, 57, 0, 0, 0, 0, 0, 0, 'Oregon'),
(58, 2, 0, 9, 0, 9, 58, 0, 0, 0, 0, 0, 0, 'Pennsylvania'),
(59, 2, 0, 9, 0, 9, 59, 0, 0, 0, 0, 0, 0, 'Rhode Island'),
(60, 2, 0, 9, 0, 9, 60, 0, 0, 0, 0, 0, 0, 'South Carolina'),
(61, 2, 0, 9, 0, 9, 61, 0, 0, 0, 0, 0, 0, 'South Dakota'),
(62, 2, 0, 9, 0, 9, 62, 0, 0, 0, 0, 0, 0, 'Tennessee'),
(63, 2, 0, 9, 0, 9, 63, 0, 0, 0, 0, 0, 0, 'Texas'),
(64, 2, 0, 9, 0, 9, 64, 0, 0, 0, 0, 0, 0, 'Utah'),
(65, 2, 0, 9, 0, 9, 65, 0, 0, 0, 0, 0, 0, 'Vermont'),
(66, 2, 0, 9, 0, 9, 66, 0, 0, 0, 0, 0, 0, 'Virginia'),
(67, 2, 0, 9, 0, 9, 67, 0, 0, 0, 0, 0, 0, 'Washington'),
(68, 2, 0, 9, 0, 9, 68, 0, 0, 0, 0, 0, 0, 'West Virginia'),
(69, 2, 0, 9, 0, 9, 69, 0, 0, 0, 0, 0, 0, 'Wisconsin');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_mailtemplates`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_mailtemplates` (
  `mailtemplate_id` int(11) unsigned NOT NULL auto_increment,
  `type` varchar(255) NOT NULL,
  `vars` varchar(255) NOT NULL,
  PRIMARY KEY  (`mailtemplate_id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Dumping data for table `engine4_socialstore_mailtemplates`
--

INSERT INTO `engine4_socialstore_mailtemplates` (`mailtemplate_id`, `type`, `vars`) VALUES
(2, 'store_header', ''),
(3, 'store_footer', ''),
(4, 'store_headermember', ''),
(5, 'store_footermember', ''),
(9, 'store_approvestore', ''),
(10, 'store_approveproduct', ''),
(14, 'store_purchasebuyer', ''),
(15, 'store_purchaseseller', ''),
(18, 'store_requestaccept', ''),
(20, 'store_requestdeny', ''),
(23, 'store_productdelete', ''),
(24, 'store_productdelfav', ''),
(25, 'store_follownotice', ''),
(35, 'store_refundbuyer', ''),
(36, 'store_refundseller', '');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_orderitems`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_orderitems` (
  `orderitem_id` bigint(20) unsigned NOT NULL auto_increment,
  `store_id` bigint(20) unsigned NOT NULL default '0',
  `order_id` varchar(20) NOT NULL,
  `object_id` int(11) unsigned NOT NULL,
  `object_type` varchar(32) default NULL,
  `pretax_price` decimal(16,2) NOT NULL,
  `price` decimal(16,2) NOT NULL,
  `sku` varchar(256) NOT NULL,
  `quantity` int(11) NOT NULL default '1',
  `item_tax_amount` decimal(16,2) NOT NULL default '0.00',
  `item_commission_amount` decimal(16,2) NOT NULL default '0.00',
  `tax_amount` decimal(16,2) NOT NULL default '0.00',
  `shipping_amount` decimal(16,2) NOT NULL default '0.00',
  `handling_amount` decimal(16,2) NOT NULL default '0.00',
  `discount_amount` decimal(16,2) NOT NULL default '0.00',
  `commission_amount` decimal(16,2) NOT NULL default '0.00',
  `sub_amount` decimal(16,2) NOT NULL default '0.00',
  `delivery_status` enum('processing','shipping','delivered') NOT NULL default 'processing',
  `payment_status` varchar(50) NOT NULL default 'processing',
  `total_amount` decimal(16,2) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(128) NOT NULL,
  `refund_status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`orderitem_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_orders`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_orders` (
  `order_id` varchar(20) NOT NULL,
  `paytype_id` varchar(32) NOT NULL,
  `owner_id` int(11) unsigned default NULL,
  `payment_status` enum('initial','pending','failure','completed') NOT NULL default 'initial',
  `order_status` enum('initial','processing','shipping','deliveried') NOT NULL default 'initial',
  `quantity` int(10) unsigned NOT NULL default '0',
  `tax_amount` decimal(16,2) unsigned NOT NULL default '0.00',
  `shipping_amount` decimal(16,2) unsigned NOT NULL default '0.00',
  `handling_amount` decimal(16,2) unsigned NOT NULL default '0.00',
  `discount_amount` decimal(16,2) unsigned NOT NULL default '0.00',
  `insurance_amount` decimal(16,2) unsigned NOT NULL default '0.00',
  `commission_amount` decimal(16,2) unsigned NOT NULL default '0.00',
  `sub_amount` decimal(16,2) unsigned NOT NULL default '0.00',
  `total_amount` decimal(16,2) unsigned NOT NULL default '0.00',
  `currency` char(3) NOT NULL default 'USD',
  `name` varchar(255) NOT NULL,
  `description` tinytext NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_pages`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_pages` (
  `page_id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `body` longtext NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY  (`page_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_paytrans`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_paytrans` (
  `paytran_id` bigint(20) unsigned NOT NULL auto_increment,
  `gateway` varchar(32) NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `transaction_id` varchar(64) NOT NULL,
  `transaction_type` varchar(64) NOT NULL,
  `payment_status` varchar(64) NOT NULL,
  `gateway_fee` decimal(16,2) NOT NULL default '0.00',
  `currency` char(3) NOT NULL,
  `amount` decimal(16,4) unsigned NOT NULL default '0.0000',
  `payment_type` varchar(64) default NULL,
  `gateway_token` varchar(64) default NULL,
  `pending_reason` varchar(256) default NULL,
  `error_code` varchar(256) default NULL,
  `order_id` varchar(20) NOT NULL,
  `timestamp` varchar(64) NOT NULL,
  `order_time` varchar(64) NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`paytran_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_paytypes`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_paytypes` (
  `paytype_id` varchar(32) NOT NULL,
  `plugin_class` varchar(128) NOT NULL,
  PRIMARY KEY  (`paytype_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `engine4_socialstore_paytypes`
--

INSERT INTO `engine4_socialstore_paytypes` (`paytype_id`, `plugin_class`) VALUES
('publish-store', 'Socialstore_Plugin_Payment_PublishStore'),
('shopping-cart', 'Socialstore_Plugin_Payment_ShoppingCart'),
('publish-product', 'Socialstore_Plugin_Payment_PublishProduct'),
('pay-request', 'Socialstore_Plugin_Payment_PayRequest');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_productalbums`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_productalbums` (
  `productalbum_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) unsigned NOT NULL,
  `title` varchar(128) character set utf8 collate utf8_unicode_ci NOT NULL,
  `description` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `photo_id` int(11) unsigned NOT NULL default '0',
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `collectible_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`productalbum_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_productphotos`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_productphotos` (
  `productphoto_id` int(11) unsigned NOT NULL auto_increment,
  `album_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `slideshow` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`productphoto_id`),
  KEY `collection_id` (`collection_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_products`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_products` (
  `product_id` int(11) unsigned NOT NULL auto_increment,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `sponsored` tinyint(1) unsigned NOT NULL default '0',
  `store_id` int(11) unsigned NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `rate_ave` int(11) unsigned NOT NULL default '0',
  `sold_qty` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `favourite_count` int(11) unsigned NOT NULL default '0',
  `category_id` int(11) unsigned NOT NULL,
  `view_status` enum('show','hide') NOT NULL default 'hide',
  `approve_status` enum('new','waiting','approved','denied') NOT NULL default 'new',
  `sku` varchar(64) NOT NULL,
  `photo_id` int(10) unsigned NOT NULL default '0',
  `featured` tinyint(1) unsigned NOT NULL default '0',
  `tax_id` float NOT NULL default '0',
  `pretax_price` decimal(16,2) NOT NULL,
  `tax_percentage` double(10,2) NOT NULL default '0.00',
  `item_tax_amount` decimal(16,2) NOT NULL default '0.00',
  `item_commission_amount` decimal(16,2) NOT NULL default '0.00',
  `price` decimal(16,2) NOT NULL default '0.00',
  `min_qty_purchase` int(11) unsigned NOT NULL default '0',
  `max_qty_purchase` int(11) unsigned NOT NULL default '0',
  `available_date` datetime NOT NULL,
  `currency` char(3) NOT NULL default 'USD',
  `expire_date` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `body` text NOT NULL,
  `deliver_days` int(11) unsigned NOT NULL default '7',
  `creation_date` datetime NOT NULL,
  `approved_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY  (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_rates`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_rates` (
  `rate_id` bigint(20) unsigned NOT NULL auto_increment,
  `item_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `rate_number` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`rate_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_refunditems`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_refunditems` (
  `refunditem_id` int(11) unsigned NOT NULL auto_increment,
  `refund_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `item_qty` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`refunditem_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_refunds`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_refunds` (
  `refund_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` int(11) unsigned NOT NULL default '0',
  `owner_id` int(11) unsigned NOT NULL,
  `message` text NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`refund_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_reqtrans`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_reqtrans` (
  `reqtran_id` bigint(20) unsigned NOT NULL auto_increment,
  `gateway` varchar(32) NOT NULL,
  `request_id` int(11) unsigned NOT NULL,
  `transaction_id` varchar(64) NOT NULL,
  `transaction_type` varchar(64) NOT NULL,
  `payment_status` varchar(64) NOT NULL,
  `gateway_fee` decimal(16,2) NOT NULL default '0.00',
  `currency` char(3) NOT NULL,
  `amount` decimal(16,4) unsigned NOT NULL default '0.0000',
  `payment_type` varchar(64) default NULL,
  `gateway_token` varchar(64) default NULL,
  `pending_reason` varchar(256) default NULL,
  `error_code` varchar(256) default NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`reqtran_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_requests`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_requests` (
  `request_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` int(11) unsigned NOT NULL default '0',
  `owner_id` int(11) unsigned NOT NULL default '0',
  `request_amount` decimal(16,2) unsigned NOT NULL default '0.00',
  `request_status` enum('completed','denied','pending','waiting') NOT NULL default 'waiting',
  `currency` char(3) NOT NULL,
  `request_message` text NOT NULL,
  `response_message` text NOT NULL,
  `request_date` datetime default NULL,
  `response_date` datetime default NULL,
  PRIMARY KEY  (`request_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_states`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_states` (
  `code` char(2) default NULL,
  `state` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `engine4_socialstore_states`
--

INSERT INTO `engine4_socialstore_states` (`code`, `state`) VALUES
('AL', 'Alabama'),
('AK', 'Alaska'),
('AZ', 'Arizona'),
('AR', 'Arkansas'),
('CA', 'California'),
('CO', 'Colorado'),
('CT', 'Connecticut'),
('DE', 'Delaware'),
('DC', 'District of Columbia'),
('FL', 'Florida'),
('GA', 'Goergia'),
('HI', 'Hawaii'),
('ID', 'Idaho'),
('IL', 'Illinois'),
('IN', 'Indiana'),
('IA', 'Iowa'),
('KS', 'Kansas'),
('KY', 'Kentucky'),
('LA', 'Louisiana'),
('ME', 'Maine'),
('MD', 'Maryland'),
('MA', 'Massachusetts'),
('MI', 'Michigan'),
('MN', 'Minnesota'),
('MS', 'Mississippi'),
('MO', 'Missouri'),
('MT', 'Montana'),
('NE', 'Nebraska'),
('NV', 'Nevada'),
('NH', 'New Hampshire'),
('NJ', 'New Jersey'),
('NM', 'New Mexico'),
('NY', 'New York'),
('NC', 'North Carolina'),
('ND', 'North Dakota'),
('OH', 'Ohio'),
('OK', 'Oklahoma'),
('OR', 'Oregon'),
('PA', 'Pennsylvania'),
('RI', 'Rhode Islands'),
('SC', 'South Carolina'),
('SD', 'South Dakota'),
('TN', 'Tennessee'),
('TX', 'Texas'),
('UT', 'Utah'),
('VT', 'Vermont'),
('VA', 'Virginia'),
('WA', 'Washington'),
('WV', 'West Virginia'),
('WI', 'Wisconsin'),
('WY', 'Wyoming');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_storealbums`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_storealbums` (
  `storealbum_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` int(11) unsigned NOT NULL,
  `title` varchar(128) character set utf8 collate utf8_unicode_ci NOT NULL,
  `description` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `photo_id` int(11) unsigned NOT NULL default '0',
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `collectible_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`storealbum_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_storephotos`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_storephotos` (
  `storephoto_id` int(11) unsigned NOT NULL auto_increment,
  `album_id` int(11) unsigned NOT NULL,
  `store_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `slideshow` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`storephoto_id`),
  KEY `collection_id` (`collection_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_transactions`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_transactions` (
  `transaction_id` bigint(20) unsigned NOT NULL auto_increment,
  `gatewaytransaction_id` varchar(128) NOT NULL,
  `gateway_status` enum('sending','pending','success','failure') NOT NULL,
  `object_id` int(11) unsigned NOT NULL,
  `object_type` enum('store','product','request') NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `gateway_id` varchar(32) NOT NULL,
  `amount` decimal(16,2) unsigned NOT NULL,
  `currency` char(3) NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_vats`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_vats` (
  `vat_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `value` decimal(10,2) NOT NULL default '0.00',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY  (`vat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `engine4_socialstore_vats`
--

INSERT INTO `engine4_socialstore_vats` (`vat_id`, `name`, `value`, `creation_date`, `modified_date`) VALUES
(2, 'American VATs', '10.00', '2011-09-17 02:58:21', '2011-09-28 02:40:55'),
(3, 'Testing ', '50.00', '2011-10-04 02:28:53', '2011-10-04 02:28:53'),
(4, 'Testing 20', '20.00', '2011-10-04 02:29:06', '2011-10-04 02:29:06'),
(5, 'Testing ', '30.00', '2011-10-04 02:29:17', '2011-10-04 02:29:17'),
(6, 'Testing 40', '40.00', '2011-10-04 02:29:30', '2011-10-04 02:29:30');




INSERT INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES
(1, 'social_product', 'comment', 2, NULL),
(1, 'social_product', 'product_approve', 0, NULL),
(1, 'social_product', 'product_authcom', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),
(1, 'social_product', 'product_buy', 1, NULL),
(1, 'social_product', 'product_com', 5, '5'),
(1, 'social_product', 'product_comment', 2, NULL),
(1, 'social_product', 'product_create', 1, NULL),
(1, 'social_product', 'product_delete', 1, NULL),
(1, 'social_product', 'product_edit', 1, NULL),
(1, 'social_product', 'product_feature', 0, NULL),
(1, 'social_product', 'product_ftedfee', 0, NULL),
(1, 'social_product', 'product_pubfee', 0, NULL),
(1, 'social_product', 'product_publish', 0, NULL),
(1, 'social_product', 'product_view', 2, NULL),
(1, 'social_store', 'comment', 2, NULL),
(1, 'social_store', 'store_approve', 0, NULL),
(1, 'social_store', 'store_authcom', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),
(1, 'social_store', 'store_comment', 2, NULL),
(1, 'social_store', 'store_create', 1, NULL),
(1, 'social_store', 'store_edit', 1, NULL),
(1, 'social_store', 'store_feature', 0, NULL),
(1, 'social_store', 'store_ftedfee', 0, NULL),
(1, 'social_store', 'store_pubfee', 0, NULL),
(1, 'social_store', 'store_publish', 0, NULL),
(1, 'social_store', 'store_view', 2, NULL),
(2, 'social_product', 'comment', 2, NULL),
(2, 'social_product', 'product_approve', 1, NULL),
(2, 'social_product', 'product_authcom', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),
(2, 'social_product', 'product_buy', 1, NULL),
(2, 'social_product', 'product_com', 5, '5'),
(2, 'social_product', 'product_create', 1, NULL),
(2, 'social_product', 'product_delete', 1, NULL),
(2, 'social_product', 'product_edit', 1, NULL),
(2, 'social_product', 'product_feature', 0, NULL),
(2, 'social_product', 'product_ftedfee', 5, '5'),
(2, 'social_product', 'product_pubfee', 5, '5'),
(2, 'social_product', 'product_publish', 0, NULL),
(2, 'social_product', 'product_view', 2, NULL),
(2, 'social_store', 'comment', 2, NULL),
(2, 'social_store', 'store_approve', 1, NULL),
(2, 'social_store', 'store_authcom', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),
(2, 'social_store', 'store_create', 1, NULL),
(2, 'social_store', 'store_edit', 1, NULL),
(2, 'social_store', 'store_feature', 0, NULL),
(2, 'social_store', 'store_ftedfee', 5, '5'),
(2, 'social_store', 'store_pubfee', 5, '5'),
(2, 'social_store', 'store_publish', 0, NULL),
(2, 'social_store', 'store_view', 2, NULL),
(3, 'social_product', 'comment', 2, NULL),
(3, 'social_product', 'product_approve', 1, NULL),
(3, 'social_product', 'product_authcom', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),
(3, 'social_product', 'product_buy', 1, NULL),
(3, 'social_product', 'product_com', 3, '10'),
(3, 'social_product', 'product_create', 1, NULL),
(3, 'social_product', 'product_delete', 1, NULL),
(3, 'social_product', 'product_edit', 1, NULL),
(3, 'social_product', 'product_feature', 0, NULL),
(3, 'social_product', 'product_ftedfee', 5, '5'),
(3, 'social_product', 'product_pubfee', 5, '5'),
(3, 'social_product', 'product_publish', 0, NULL),
(3, 'social_product', 'product_view', 2, NULL),
(3, 'social_store', 'comment', 2, NULL),
(3, 'social_store', 'store_approve', 1, NULL),
(3, 'social_store', 'store_authcom', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),
(3, 'social_store', 'store_create', 1, NULL),
(3, 'social_store', 'store_edit', 1, NULL),
(3, 'social_store', 'store_feature', 0, NULL),
(3, 'social_store', 'store_ftedfee', 5, '5'),
(3, 'social_store', 'store_pubfee', 5, '5'),
(3, 'social_store', 'store_publish', 0, NULL),
(3, 'social_store', 'store_view', 2, NULL),
(4, 'social_product', 'comment', 1, NULL),
(4, 'social_product', 'product_approve', 1, NULL),
(4, 'social_product', 'product_authcom', 5, '["everyone"]'),
(4, 'social_product', 'product_authview', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),
(4, 'social_product', 'product_buy', 1, NULL),
(4, 'social_product', 'product_com', 3, '15'),
(4, 'social_product', 'product_comment', 1, NULL),
(4, 'social_product', 'product_create', 1, NULL),
(4, 'social_product', 'product_delete', 1, NULL),
(4, 'social_product', 'product_edit', 1, NULL),
(4, 'social_product', 'product_feature', 0, NULL),
(4, 'social_product', 'product_ftedfee', 5, '5'),
(4, 'social_product', 'product_pubfee', 5, '5'),
(4, 'social_product', 'product_publish', 0, NULL),
(4, 'social_product', 'product_view', 1, NULL),
(4, 'social_store', 'comment', 1, NULL),
(4, 'social_store', 'store_approve', 1, NULL),
(4, 'social_store', 'store_authcom', 5, '["everyone"]'),
(4, 'social_store', 'store_authview', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),
(4, 'social_store', 'store_comment', 1, NULL),
(4, 'social_store', 'store_create', 1, NULL),
(4, 'social_store', 'store_edit', 1, NULL),
(4, 'social_store', 'store_feature', 0, NULL),
(4, 'social_store', 'store_ftedfee', 5, '5'),
(4, 'social_store', 'store_pubfee', 5, '5'),
(4, 'social_store', 'store_publish', 0, NULL),
(4, 'social_store', 'store_view', 1, NULL),
(5, 'social_product', 'product_view', 1, NULL),
(5, 'social_store', 'store_view', 1, NULL);
INSERT IGNORE INTO `engine4_younetcore_license` (`name`, `title`, `descriptions`, `type`, `current_version`, `lasted_version`, `is_active`, `date_active`, `params`, `download_link`, `demo_link`) VALUES ('socialstore', 'Store', '', 'module', '4.02', '4.02', '0', NULL, NULL, NULL, NULL);

UPDATE `engine4_core_modules` SET `version` = '4.02' where `name` = 'socialstore';

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
  `store_category_id` int(11) unsigned NOT NULL default '0',
  `store_id` int(11) unsigned NOT NULL,
  `parent_category_id` int(11) unsigned NOT NULL default '0',
  `level` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY  (`customcategory_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


ALTER TABLE `engine4_socialstore_products` ADD COLUMN `storecategory_id` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `category_id`;
ALTER TABLE `engine4_socialstore_products` ADD COLUMN `video_url` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `storecategory_id` ;
INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('socialstore_admin_main_categories', 'socialstore', 'Store Categories', '', '{"route":"admin_default","module":"socialstore","controller":"store-category"}', 'socialstore_admin_main', '', 1, 0, 5);


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
ALTER TABLE `engine4_socialstore_attributes_options` ADD `adjust_price` DECIMAL( 16, 2 ) NOT NULL DEFAULT '0.00' AFTER `label`;
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

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
( 'user_home_socialstore', 'socialstore', 'My Deal Requests', 'Socialstore_Plugin_Menus::canViewGDAs', '{"route":"socialstore_extended","controller":"gda","action":"requests","module":"socialstore","icon":"application/modules/Socialstore/externals/images/gda_icon.png"}', 'user_home', '', 1, 0, 6),
( 'user_profile_socialstore', 'socialstore', 'My Deal Requests', 'Socialstore_Plugin_Menus', '', 'user_profile', '', 1, 0, 4);
UPDATE `engine4_core_modules` SET `version` = '4.03p2' where 'name' = 'socialstore';