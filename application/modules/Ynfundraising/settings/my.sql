INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynfundraising', 'Fundraising', '', '4.03p1', 1, 'extra') ;

ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 256 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_main_ynfundraising', 'ynfundraising', 'Fundraising', '', '{"route":"ynfundraising_general","action":"browse"}', 'core_main', '', 1, 0, 999),
('ynfundraising_main_browse', 'ynfundraising', 'Browse Campaigns', '', '{"route":"ynfundraising_general","action":"browse"}', 'ynfundraising_main', '', 1, 0, 1),
('ynfundraising_main_pastcampaigns', 'ynfundraising', 'Past Campaigns', 'Ynfundraising_Plugin_Menus::showPastCampaigns', '{"route":"ynfundraising_general","action":"past-campaigns"}', 'ynfundraising_main', '', 1, 0, 2),
('ynfundraising_main_mycampaigns', 'ynfundraising', 'My Campaigns', 'Ynfundraising_Plugin_Menus::showMyCampaigns', '{"route":"ynfundraising_extended","controller":"campaign"}', 'ynfundraising_main', '', 1, 0, 3),
('ynfundraising_main_myrequests', 'ynfundraising', 'My Requests', 'Ynfundraising_Plugin_Menus::showMyRequests', '{"route":"ynfundraising_extended","controller":"request"}', 'ynfundraising_main', '', 1, 0, 4),
('ynfundraising_main_managerequests', 'ynfundraising', 'Manage Requests', 'Ynfundraising_Plugin_Menus::showManageRequests', '{"route":"ynfundraising_general","action":"manage-requests"}', 'ynfundraising_main', '', 1, 0, 5),
('ynfundraising_main_create', 'ynfundraising', 'Create Campaign', 'Ynfundraising_Plugin_Menus::showCreateCampaigns', '{"route":"ynfundraising_general","action":"create"}', 'ynfundraising_main', '', 1, 0, 6),
('core_admin_main_plugins_ynfundraising', 'ynfundraising', 'YN - Fundraising', '', '{"route":"admin_default","module":"ynfundraising","controller":"manage"}', 'core_admin_main_plugins', '', 1, 0, 999),
('ynfundraising_admin_main_manage', 'ynfundraising', 'Manage Campaigns', '', '{"route":"admin_default","module":"ynfundraising","controller":"manage"}', 'ynfundraising_admin_main', '', 1, 0, 1),
('ynfundraising_admin_main_statistics', 'ynfundraising', 'Statistics', '', '{"route":"admin_default","module":"ynfundraising","controller":"statistics"}', 'ynfundraising_admin_main', '', 1, 0, 2),
('ynfundraising_admin_main_settings', 'ynfundraising', 'Global Settings', '', '{"route":"admin_default","module":"ynfundraising","controller":"settings"}', 'ynfundraising_admin_main', '', 1, 0, 3),
('ynfundraising_admin_main_level', 'ynfundraising', 'Member Level Settings', '', '{"route":"admin_default","module":"ynfundraising","controller":"level"}', 'ynfundraising_admin_main', '', 1, 0, 4),
('ynfundraising_admin_main_currency', 'ynfundraising', 'Currencies', '', '{"route":"admin_default","module":"ynfundraising","controller":"currency"}', 'ynfundraising_admin_main', '', 1, 0, 5),
('ynfundraising_quick_create', 'ynfundraising', 'Create Campaign', 'Ynfundraising_Plugin_Menus::canCreateCampaigns', '{"route":"ynfundraising_general","action":"create","class":"buttonlink icon_create_new"}', 'ynfundraising_quick', '', 1, 0, 1),
('ynfundraising_admin_main_emailtemplates', 'ynfundraising', 'Email Templates', '', '{"route":"admin_default","module":"ynfundraising","controller":"mail", "action": "templates"}', 'ynfundraising_admin_main', '', 1, 0, 5);



-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfundraising_albums`
--

CREATE TABLE `engine4_ynfundraising_albums` (
  `album_id` int(11) unsigned NOT NULL auto_increment,
  `campaign_id` int(11) unsigned NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `title` varchar(128) NOT NULL,
  `description` mediumtext NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `photo_id` int(11) unsigned NOT NULL default '0',
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `collectible_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`album_id`),
  KEY `FK_engine4_ynfundraising_albums_engine4_ynfundraising_campaigns` (`campaign_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfundraising_campaigns`
--

CREATE TABLE `engine4_ynfundraising_campaigns` (
  `campaign_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `owner_type` varchar(64) collate utf8_unicode_ci NOT NULL,
  `parent_id` int(11) NOT NULL,
  `parent_type` varchar(64) collate utf8_unicode_ci NOT NULL,
  `request_id` int(11) default '0',
  `status` enum('draft','ongoing','closed','reached','expired') collate utf8_unicode_ci NOT NULL,
  `is_featured` tinyint(1) NOT NULL default '0',
  `view_count` int(11) NOT NULL default '0',
  `comment_count` int(11) NOT NULL default '0',
  `like_count` int(11) NOT NULL default '0',
  `share_count` int(11) NOT NULL default '0',
  `click_count` int(11) NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `title` varchar(256) collate utf8_unicode_ci NOT NULL,
  `short_description` text collate utf8_unicode_ci NOT NULL,
  `main_description` text collate utf8_unicode_ci NOT NULL,
  `paypal_account` varchar(256) collate utf8_unicode_ci NOT NULL,
  `goal` decimal(10,2) NOT NULL default '0.00',
  `total_amount` float(10,2) NOT NULL default '0.00',
  `currency` varchar(64) collate utf8_unicode_ci NOT NULL,
  `expiry_date` datetime default NULL,
  `predefined` varchar(256) collate utf8_unicode_ci default NULL,
  `minimum_donate` decimal(10,2) NOT NULL default '0.00',
  `allow_anonymous` tinyint(1) NOT NULL default '0',
  `address` varchar(256) collate utf8_unicode_ci default NULL,
  `location` varchar(256) collate utf8_unicode_ci default NULL,
  `photo_id` int(11) NOT NULL default '0',
  `video_url` varchar(256) collate utf8_unicode_ci default NULL,
  `name` varchar(256) collate utf8_unicode_ci default NULL,
  `phone` varchar(256) collate utf8_unicode_ci default NULL,
  `email` varchar(256) collate utf8_unicode_ci default NULL,
  `country` varchar(64) collate utf8_unicode_ci NOT NULL,
  `state` varchar(256) collate utf8_unicode_ci default NULL,
  `city` varchar(256) collate utf8_unicode_ci default NULL,
  `street` varchar(256) collate utf8_unicode_ci default NULL,
  `about_me` text collate utf8_unicode_ci,
  `email_sub` varchar(256) collate utf8_unicode_ci default NULL,
  `email_message` text collate utf8_unicode_ci NOT NULL,
  `terms_conditions` text collate utf8_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfundraising_campaign_ratings`
--

CREATE TABLE `engine4_ynfundraising_campaign_ratings` (
  `campaignrating_id` int(11) unsigned NOT NULL auto_increment,
  `campaign_id` int(11) unsigned NOT NULL,
  `poster_id` int(11) unsigned NOT NULL,
  `rate_number` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`campaignrating_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfundraising_countries`
--

CREATE TABLE `engine4_ynfundraising_countries` (
  `country_id` int(11) NOT NULL,
  `name` varchar(128) character set utf8 collate utf8_bin NOT NULL,
  `iso_code_2` varchar(2) character set utf8 collate utf8_bin NOT NULL default '',
  `iso_code_3` varchar(3) character set utf8 collate utf8_bin NOT NULL default '',
  `address_format` text character set utf8 collate utf8_bin NOT NULL,
  `postcode_required` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynfundraising_countries`
--
INSERT INTO `engine4_ynfundraising_countries` (`country_id`, `name`, `iso_code_2`, `iso_code_3`, `address_format`, `postcode_required`, `status`) VALUES
(1, 'Afghanistan', 'AF', 'AFG', '', 0, 1),
(2, 'Albania', 'AL', 'ALB', '', 0, 1),
(3, 'Algeria', 'DZ', 'DZA', '', 0, 1),
(4, 'American Samoa', 'AS', 'ASM', '', 0, 1),
(5, 'Andorra', 'AD', 'AND', '', 0, 1),
(6, 'Angola', 'AO', 'AGO', '', 0, 1),
(7, 'Anguilla', 'AI', 'AIA', '', 0, 1),
(8, 'Antarctica', 'AQ', 'ATA', '', 0, 1),
(9, 'Antigua and Barbuda', 'AG', 'ATG', '', 0, 1),
(10, 'Argentina', 'AR', 'ARG', '', 0, 1),
(11, 'Armenia', 'AM', 'ARM', '', 0, 1),
(12, 'Aruba', 'AW', 'ABW', '', 0, 1),
(13, 'Australia', 'AU', 'AUS', '', 0, 1),
(14, 'Austria', 'AT', 'AUT', '', 0, 1),
(15, 'Azerbaijan', 'AZ', 'AZE', '', 0, 1),
(16, 'Bahamas', 'BS', 'BHS', '', 0, 1),
(17, 'Bahrain', 'BH', 'BHR', '', 0, 1),
(18, 'Bangladesh', 'BD', 'BGD', '', 0, 1),
(19, 'Barbados', 'BB', 'BRB', '', 0, 1),
(20, 'Belarus', 'BY', 'BLR', '', 0, 1),
(21, 'Belgium', 'BE', 'BEL', '', 0, 1),
(22, 'Belize', 'BZ', 'BLZ', '', 0, 1),
(23, 'Benin', 'BJ', 'BEN', '', 0, 1),
(24, 'Bermuda', 'BM', 'BMU', '', 0, 1),
(25, 'Bhutan', 'BT', 'BTN', '', 0, 1),
(26, 'Bolivia', 'BO', 'BOL', '', 0, 1),
(27, 'Bosnia and Herzegowina', 'BA', 'BIH', '', 0, 1),
(28, 'Botswana', 'BW', 'BWA', '', 0, 1),
(29, 'Bouvet Island', 'BV', 'BVT', '', 0, 1),
(30, 'Brazil', 'BR', 'BRA', '', 0, 1),
(31, 'British Indian Ocean Territory', 'IO', 'IOT', '', 0, 1),
(32, 'Brunei Darussalam', 'BN', 'BRN', '', 0, 1),
(33, 'Bulgaria', 'BG', 'BGR', '', 0, 1),
(34, 'Burkina Faso', 'BF', 'BFA', '', 0, 1),
(35, 'Burundi', 'BI', 'BDI', '', 0, 1),
(36, 'Cambodia', 'KH', 'KHM', '', 0, 1),
(37, 'Cameroon', 'CM', 'CMR', '', 0, 1),
(38, 'Canada', 'CA', 'CAN', '', 0, 1),
(39, 'Cape Verde', 'CV', 'CPV', '', 0, 1),
(40, 'Cayman Islands', 'KY', 'CYM', '', 0, 1),
(41, 'Central African Republic', 'CF', 'CAF', '', 0, 1),
(42, 'Chad', 'TD', 'TCD', '', 0, 1),
(43, 'Chile', 'CL', 'CHL', '', 0, 1),
(44, 'China', 'CN', 'CHN', '', 0, 1),
(45, 'Christmas Island', 'CX', 'CXR', '', 0, 1),
(46, 'Cocos (Keeling) Islands', 'CC', 'CCK', '', 0, 1),
(47, 'Colombia', 'CO', 'COL', '', 0, 1),
(48, 'Comoros', 'KM', 'COM', '', 0, 1),
(49, 'Congo', 'CG', 'COG', '', 0, 1),
(50, 'Cook Islands', 'CK', 'COK', '', 0, 1),
(51, 'Costa Rica', 'CR', 'CRI', '', 0, 1),
(52, 'Cote D''Ivoire', 'CI', 'CIV', '', 0, 1),
(53, 'Croatia', 'HR', 'HRV', '', 0, 1),
(54, 'Cuba', 'CU', 'CUB', '', 0, 1),
(55, 'Cyprus', 'CY', 'CYP', '', 0, 1),
(56, 'Czech Republic', 'CZ', 'CZE', '', 0, 1),
(57, 'Denmark', 'DK', 'DNK', '', 0, 1),
(58, 'Djibouti', 'DJ', 'DJI', '', 0, 1),
(59, 'Dominica', 'DM', 'DMA', '', 0, 1),
(60, 'Dominican Republic', 'DO', 'DOM', '', 0, 1),
(61, 'East Timor', 'TP', 'TMP', '', 0, 1),
(62, 'Ecuador', 'EC', 'ECU', '', 0, 1),
(63, 'Egypt', 'EG', 'EGY', '', 0, 1),
(64, 'El Salvador', 'SV', 'SLV', '', 0, 1),
(65, 'Equatorial Guinea', 'GQ', 'GNQ', '', 0, 1),
(66, 'Eritrea', 'ER', 'ERI', '', 0, 1),
(67, 'Estonia', 'EE', 'EST', '', 0, 1),
(68, 'Ethiopia', 'ET', 'ETH', '', 0, 1),
(69, 'Falkland Islands (Malvinas)', 'FK', 'FLK', '', 0, 1),
(70, 'Faroe Islands', 'FO', 'FRO', '', 0, 1),
(71, 'Fiji', 'FJ', 'FJI', '', 0, 1),
(72, 'Finland', 'FI', 'FIN', '', 0, 1),
(73, 'France', 'FR', 'FRA', '', 0, 1),
(74, 'France, Metropolitan', 'FX', 'FXX', '', 0, 1),
(75, 'French Guiana', 'GF', 'GUF', '', 0, 1),
(76, 'French Polynesia', 'PF', 'PYF', '', 0, 1),
(77, 'French Southern Territories', 'TF', 'ATF', '', 0, 1),
(78, 'Gabon', 'GA', 'GAB', '', 0, 1),
(79, 'Gambia', 'GM', 'GMB', '', 0, 1),
(80, 'Georgia', 'GE', 'GEO', '', 0, 1),
(81, 'Germany', 'DE', 'DEU', '{company}\r\n{firstname} {lastname}\r\n{address_1}\r\n{address_2}\r\n{postcode} {city}\r\n{country}', 1, 1),
(82, 'Ghana', 'GH', 'GHA', '', 0, 1),
(83, 'Gibraltar', 'GI', 'GIB', '', 0, 1),
(84, 'Greece', 'GR', 'GRC', '', 0, 1),
(85, 'Greenland', 'GL', 'GRL', '', 0, 1),
(86, 'Grenada', 'GD', 'GRD', '', 0, 1),
(87, 'Guadeloupe', 'GP', 'GLP', '', 0, 1),
(88, 'Guam', 'GU', 'GUM', '', 0, 1),
(89, 'Guatemala', 'GT', 'GTM', '', 0, 1),
(90, 'Guinea', 'GN', 'GIN', '', 0, 1),
(91, 'Guinea-bissau', 'GW', 'GNB', '', 0, 1),
(92, 'Guyana', 'GY', 'GUY', '', 0, 1),
(93, 'Haiti', 'HT', 'HTI', '', 0, 1),
(94, 'Heard and Mc Donald Islands', 'HM', 'HMD', '', 0, 1),
(95, 'Honduras', 'HN', 'HND', '', 0, 1),
(96, 'Hong Kong', 'HK', 'HKG', '', 0, 1),
(97, 'Hungary', 'HU', 'HUN', '', 0, 1),
(98, 'Iceland', 'IS', 'ISL', '', 0, 1),
(99, 'India', 'IN', 'IND', '', 0, 1),
(100, 'Indonesia', 'ID', 'IDN', '', 0, 1),
(101, 'Iran (Islamic Republic of)', 'IR', 'IRN', '', 0, 1),
(102, 'Iraq', 'IQ', 'IRQ', '', 0, 1),
(103, 'Ireland', 'IE', 'IRL', '', 0, 1),
(104, 'Israel', 'IL', 'ISR', '', 0, 1),
(105, 'Italy', 'IT', 'ITA', '', 0, 1),
(106, 'Jamaica', 'JM', 'JAM', '', 0, 1),
(107, 'Japan', 'JP', 'JPN', '', 0, 1),
(108, 'Jordan', 'JO', 'JOR', '', 0, 1),
(109, 'Kazakhstan', 'KZ', 'KAZ', '', 0, 1),
(110, 'Kenya', 'KE', 'KEN', '', 0, 1),
(111, 'Kiribati', 'KI', 'KIR', '', 0, 1),
(112, 'North Korea', 'KP', 'PRK', '', 0, 1),
(113, 'Korea, Republic of', 'KR', 'KOR', '', 0, 1),
(114, 'Kuwait', 'KW', 'KWT', '', 0, 1),
(115, 'Kyrgyzstan', 'KG', 'KGZ', '', 0, 1),
(116, 'Lao People''s Democratic Republic', 'LA', 'LAO', '', 0, 1),
(117, 'Latvia', 'LV', 'LVA', '', 0, 1),
(118, 'Lebanon', 'LB', 'LBN', '', 0, 1),
(119, 'Lesotho', 'LS', 'LSO', '', 0, 1),
(120, 'Liberia', 'LR', 'LBR', '', 0, 1),
(121, 'Libyan Arab Jamahiriya', 'LY', 'LBY', '', 0, 1),
(122, 'Liechtenstein', 'LI', 'LIE', '', 0, 1),
(123, 'Lithuania', 'LT', 'LTU', '', 0, 1),
(124, 'Luxembourg', 'LU', 'LUX', '', 0, 1),
(125, 'Macau', 'MO', 'MAC', '', 0, 1),
(126, 'FYROM', 'MK', 'MKD', '', 0, 1),
(127, 'Madagascar', 'MG', 'MDG', '', 0, 1),
(128, 'Malawi', 'MW', 'MWI', '', 0, 1),
(129, 'Malaysia', 'MY', 'MYS', '', 0, 1),
(130, 'Maldives', 'MV', 'MDV', '', 0, 1),
(131, 'Mali', 'ML', 'MLI', '', 0, 1),
(132, 'Malta', 'MT', 'MLT', '', 0, 1),
(133, 'Marshall Islands', 'MH', 'MHL', '', 0, 1),
(134, 'Martinique', 'MQ', 'MTQ', '', 0, 1),
(135, 'Mauritania', 'MR', 'MRT', '', 0, 1),
(136, 'Mauritius', 'MU', 'MUS', '', 0, 1),
(137, 'Mayotte', 'YT', 'MYT', '', 0, 1),
(138, 'Mexico', 'MX', 'MEX', '', 0, 1),
(139, 'Micronesia, Federated States of', 'FM', 'FSM', '', 0, 1),
(140, 'Moldova, Republic of', 'MD', 'MDA', '', 0, 1),
(141, 'Monaco', 'MC', 'MCO', '', 0, 1),
(142, 'Mongolia', 'MN', 'MNG', '', 0, 1),
(143, 'Montserrat', 'MS', 'MSR', '', 0, 1),
(144, 'Morocco', 'MA', 'MAR', '', 0, 1),
(145, 'Mozambique', 'MZ', 'MOZ', '', 0, 1),
(146, 'Myanmar', 'MM', 'MMR', '', 0, 1),
(147, 'Namibia', 'NA', 'NAM', '', 0, 1),
(148, 'Nauru', 'NR', 'NRU', '', 0, 1),
(149, 'Nepal', 'NP', 'NPL', '', 0, 1),
(150, 'Netherlands', 'NL', 'NLD', '', 0, 1),
(151, 'Netherlands Antilles', 'AN', 'ANT', '', 0, 1),
(152, 'New Caledonia', 'NC', 'NCL', '', 0, 1),
(153, 'New Zealand', 'NZ', 'NZL', '', 0, 1),
(154, 'Nicaragua', 'NI', 'NIC', '', 0, 1),
(155, 'Niger', 'NE', 'NER', '', 0, 1),
(156, 'Nigeria', 'NG', 'NGA', '', 0, 1),
(157, 'Niue', 'NU', 'NIU', '', 0, 1),
(158, 'Norfolk Island', 'NF', 'NFK', '', 0, 1),
(159, 'Northern Mariana Islands', 'MP', 'MNP', '', 0, 1),
(160, 'Norway', 'NO', 'NOR', '', 0, 1),
(161, 'Oman', 'OM', 'OMN', '', 0, 1),
(162, 'Pakistan', 'PK', 'PAK', '', 0, 1),
(163, 'Palau', 'PW', 'PLW', '', 0, 1),
(164, 'Panama', 'PA', 'PAN', '', 0, 1),
(165, 'Papua New Guinea', 'PG', 'PNG', '', 0, 1),
(166, 'Paraguay', 'PY', 'PRY', '', 0, 1),
(167, 'Peru', 'PE', 'PER', '', 0, 1),
(168, 'Philippines', 'PH', 'PHL', '', 0, 1),
(169, 'Pitcairn', 'PN', 'PCN', '', 0, 1),
(170, 'Poland', 'PL', 'POL', '', 0, 1),
(171, 'Portugal', 'PT', 'PRT', '', 0, 1),
(172, 'Puerto Rico', 'PR', 'PRI', '', 0, 1),
(173, 'Qatar', 'QA', 'QAT', '', 0, 1),
(174, 'Reunion', 'RE', 'REU', '', 0, 1),
(175, 'Romania', 'RO', 'ROM', '', 0, 1),
(176, 'Russian Federation', 'RU', 'RUS', '', 0, 1),
(177, 'Rwanda', 'RW', 'RWA', '', 0, 1),
(178, 'Saint Kitts and Nevis', 'KN', 'KNA', '', 0, 1),
(179, 'Saint Lucia', 'LC', 'LCA', '', 0, 1),
(180, 'Saint Vincent and the Grenadines', 'VC', 'VCT', '', 0, 1),
(181, 'Samoa', 'WS', 'WSM', '', 0, 1),
(182, 'San Marino', 'SM', 'SMR', '', 0, 1),
(183, 'Sao Tome and Principe', 'ST', 'STP', '', 0, 1),
(184, 'Saudi Arabia', 'SA', 'SAU', '', 0, 1),
(185, 'Senegal', 'SN', 'SEN', '', 0, 1),
(186, 'Seychelles', 'SC', 'SYC', '', 0, 1),
(187, 'Sierra Leone', 'SL', 'SLE', '', 0, 1),
(188, 'Singapore', 'SG', 'SGP', '', 0, 1),
(189, 'Slovak Republic', 'SK', 'SVK', '{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city} {postcode}\r\n{zone}\r\n{country}', 0, 1),
(190, 'Slovenia', 'SI', 'SVN', '', 0, 1),
(191, 'Solomon Islands', 'SB', 'SLB', '', 0, 1),
(192, 'Somalia', 'SO', 'SOM', '', 0, 1),
(193, 'South Africa', 'ZA', 'ZAF', '', 0, 1),
(194, 'South Georgia &amp; South Sandwich Islands', 'GS', 'SGS', '', 0, 1),
(195, 'Spain', 'ES', 'ESP', '', 0, 1),
(196, 'Sri Lanka', 'LK', 'LKA', '', 0, 1),
(197, 'St. Helena', 'SH', 'SHN', '', 0, 1),
(198, 'St. Pierre and Miquelon', 'PM', 'SPM', '', 0, 1),
(199, 'Sudan', 'SD', 'SDN', '', 0, 1),
(200, 'Suriname', 'SR', 'SUR', '', 0, 1),
(201, 'Svalbard and Jan Mayen Islands', 'SJ', 'SJM', '', 0, 1),
(202, 'Swaziland', 'SZ', 'SWZ', '', 0, 1),
(203, 'Sweden', 'SE', 'SWE', '', 0, 1),
(204, 'Switzerland', 'CH', 'CHE', '', 0, 1),
(205, 'Syrian Arab Republic', 'SY', 'SYR', '', 0, 1),
(206, 'Taiwan', 'TW', 'TWN', '', 0, 1),
(207, 'Tajikistan', 'TJ', 'TJK', '', 0, 1),
(208, 'Tanzania, United Republic of', 'TZ', 'TZA', '', 0, 1),
(209, 'Thailand', 'TH', 'THA', '', 0, 1),
(210, 'Togo', 'TG', 'TGO', '', 0, 1),
(211, 'Tokelau', 'TK', 'TKL', '', 0, 1),
(212, 'Tonga', 'TO', 'TON', '', 0, 1),
(213, 'Trinidad and Tobago', 'TT', 'TTO', '', 0, 1),
(214, 'Tunisia', 'TN', 'TUN', '', 0, 1),
(215, 'Turkey', 'TR', 'TUR', '', 0, 1),
(216, 'Turkmenistan', 'TM', 'TKM', '', 0, 1),
(217, 'Turks and Caicos Islands', 'TC', 'TCA', '', 0, 1),
(218, 'Tuvalu', 'TV', 'TUV', '', 0, 1),
(219, 'Uganda', 'UG', 'UGA', '', 0, 1),
(220, 'Ukraine', 'UA', 'UKR', '', 0, 1),
(221, 'United Arab Emirates', 'AE', 'ARE', '', 0, 1),
(222, 'United Kingdom', 'GB', 'GBR', '', 1, 1),
(223, 'United States', 'US', 'USA', '{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}', 0, 1),
(224, 'United States Minor Outlying Islands', 'UM', 'UMI', '', 0, 1),
(225, 'Uruguay', 'UY', 'URY', '', 0, 1),
(226, 'Uzbekistan', 'UZ', 'UZB', '', 0, 1),
(227, 'Vanuatu', 'VU', 'VUT', '', 0, 1),
(228, 'Vatican City State (Holy See)', 'VA', 'VAT', '', 0, 1),
(229, 'Venezuela', 'VE', 'VEN', '', 0, 1),
(230, 'Viet Nam', 'VN', 'VNM', '', 0, 1),
(231, 'Virgin Islands (British)', 'VG', 'VGB', '', 0, 1),
(232, 'Virgin Islands (U.S.)', 'VI', 'VIR', '', 0, 1),
(233, 'Wallis and Futuna Islands', 'WF', 'WLF', '', 0, 1),
(234, 'Western Sahara', 'EH', 'ESH', '', 0, 1),
(235, 'Yemen', 'YE', 'YEM', '', 0, 1),
(236, 'Yugoslavia', 'YU', 'YUG', '', 0, 1),
(237, 'Democratic Republic of Congo', 'CD', 'COD', '', 0, 1),
(238, 'Zambia', 'ZM', 'ZMB', '', 0, 1),
(239, 'Zimbabwe', 'ZW', 'ZWE', '', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfundraising_currencies`
--

CREATE TABLE `engine4_ynfundraising_currencies` (
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
-- Dumping data for table `engine4_ynfundraising_currencies`
--
INSERT INTO `engine4_ynfundraising_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES
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
-- Table structure for table `engine4_ynfundraising_donations`
--

CREATE TABLE `engine4_ynfundraising_donations` (
  `donation_id` int(10) NOT NULL auto_increment,
  `campaign_id` int(10) NOT NULL default '0',
  `user_id` int(10) NOT NULL default '0',
  `message` varchar(256) collate utf8_unicode_ci default NULL,
  `is_anonymous` tinyint(1) NOT NULL default '0',
  `guest_name` varchar(256) collate utf8_unicode_ci default NULL,
  `guest_email` varchar(256) collate utf8_unicode_ci default NULL,
  `donation_date` datetime default NULL,
  `amount` decimal(10,2) default '0.00',
  `status` tinyint(1) default '0',
  `commission_fee` decimal(10,2) default NULL,
  `currency` varchar(10) collate utf8_unicode_ci NOT NULL,
  `payer_email` varchar(64) collate utf8_unicode_ci NOT NULL,
  `payer_status` varchar(128) collate utf8_unicode_ci default NULL,
  `text` text collate utf8_unicode_ci,
  `transaction_id` varchar(20) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`donation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfundraising_follows`
--

CREATE TABLE `engine4_ynfundraising_follows` (
  `follow_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL default '0',
  `campaign_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`follow_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfundraising_mailtemplates`
--

CREATE TABLE `engine4_ynfundraising_mailtemplates` (
  `mailtemplate_id` int(11) unsigned NOT NULL auto_increment,
  `type` varchar(255) NOT NULL,
  `vars` varchar(255) NOT NULL,
  PRIMARY KEY  (`mailtemplate_id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynfundraising_mailtemplates`
--

INSERT INTO `engine4_ynfundraising_mailtemplates` (`type`, `vars`) VALUES
('fundraising_requestApproved', ''),
('fundraising_requestTimeoutRequester', ''),
('fundraising_requestTimeoutOwner', ''),
('fundraising_createCampaignToRequester', ''),
('fundraising_createCampaignToOtherRequester', ''),
('fundraising_thanksDonor', ''),
('fundraising_updatingDonor', ''),
('fundraising_campaignExpiredToOwner', ''),
('fundraising_campaignExpiredToParent', ''),
('fundraising_campaignExpiredToDonor', ''),
('fundraising_campaignGoalToOwner', ''),
('fundraising_campaignGoalToParent', ''),
('fundraising_campaignGoalToDonor', ''),
('fundraising_campaignClosedToOwner', ''),
('fundraising_campaignClosedToParent', ''),
('fundraising_campaignClosedToDonor', ''),
('fundraising_inviteFriends', ''),
('fundraising_emailToDonors', '');


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfundraising_news`
--

CREATE TABLE `engine4_ynfundraising_news` (
  `new_id` int(11) NOT NULL auto_increment,
  `campaign_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `title` varchar(256) NOT NULL,
  `content` text NOT NULL,
  `link` varchar(256) default NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY  (`new_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfundraising_photos`
--

CREATE TABLE `engine4_ynfundraising_photos` (
  `photo_id` int(11) unsigned NOT NULL auto_increment,
  `campaign_id` int(11) unsigned NOT NULL,
  `album_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `image_title` varchar(128) NOT NULL,
  `image_description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY  (`photo_id`),
  KEY `FK_engine4_ynfundraising_photos_engine4_ynfundraising_campaigns` (`campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfundraising_requests`
--

CREATE TABLE `engine4_ynfundraising_requests` (
  `request_id` int(11) NOT NULL auto_increment,
  `requester_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `parent_type` varchar(64) NOT NULL,
  `request_date` datetime NOT NULL,
  `status` enum('waiting','approved','denied') NOT NULL,
  `visible` tinyint(1) default '1',
  `reason` text NOT NULL,
  `approved_date` datetime default NULL,
  `is_completed` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`request_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfundraising_sponsor_levels`
--

CREATE TABLE `engine4_ynfundraising_sponsor_levels` (
  `sponsorlevel_id` int(11) NOT NULL auto_increment,
  `campaign_id` int(11) NOT NULL,
  `amount` float(10,2) NOT NULL default '0.00',
  `description` text NOT NULL,
  PRIMARY KEY  (`sponsorlevel_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfundraising_supporters`
--

CREATE TABLE `engine4_ynfundraising_supporters` (
  `supporter_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL default '0',
  `click_count` int(11) NOT NULL,
  PRIMARY KEY  (`supporter_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--
INSERT INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynfundraising_anonymous', 'ynfundraising', '{var:$donor} has donated {var:$amount}:', 1, 7, 1, 1, 1, 1),
('ynfundraising_anonymous_msg', 'ynfundraising', '{var:$donor} has donated {var:$amount} and leave a message: {var:$message}', 1, 7, 1, 1, 1, 1),
('ynfundraising_donate', 'ynfundraising', '{item:$subject} has donated {var:$amount}:', 1, 7, 1, 1, 1, 1),
('ynfundraising_donate_msg', 'ynfundraising', '{item:$subject} has donated {var:$amount} and leave a message: {var:$message}', 1, 7, 1, 1, 1, 1),
('ynfundraising_new', 'ynfundraising', '{item:$subject} has created a new campaign:', 1, 7, 1, 1, 1, 1);

--
-- Dumping data for table `engine4_activity_notificationtypes`
--
INSERT INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('ynfundraising_notify_close', 'ynfundraising', 'Campaign {item:$object} has been closed.', 0, '', 1),
('ynfundraising_notify_donate', 'ynfundraising', '{item:$subject} has donated {var:$amount} to {item:$object}.', 0, '', 1),
('ynfundraising_notify_invi', 'ynfundraising', '{var:$donor} has donated {var:$amount} to {item:$object}.', 0, '', 1),
('ynfundraising_notify_reached', 'ynfundraising', 'Campaign {item:$object} has been reached.', 0, '', 1),
('ynfundraising_notify_expired', 'ynfundraising', 'Campaign {item:$object} has been expired.', 0, '', 1),
('ynfundraising_notify_news', 'ynfundraising', '{item:$subject} has updated news in {item:$object}.', 0, '', 1),
('ynfundraising_notify_request', 'ynfundraising', '{item:$subject} has sent a request to {item:$object}.', 0, '', 1);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
-- auth_view, auth_comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'auth_donate' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');


-- ADMIN, MODERATOR
-- create, delete, edit, view, comment, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'close' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'donate' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'close' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'donate' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynfundraising_campaign' as `type`,
    'donate' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');
  
  
 ALTER TABLE  `engine4_ynfundraising_campaigns` ADD  `activated` TINYINT( 1 ) NOT NULL DEFAULT  '1';