UPDATE `engine4_core_modules` SET `version` = '4.03p2' WHERE `engine4_core_modules`.`name` = 'ynidea' LIMIT 1 ;

-- support categories  
--
-- Table structure for table `engine4_ynidea_categories`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_categories` (
`category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`parent_id` int(11) unsigned DEFAULT NULL,
`pleft` int(11) unsigned NOT NULL,
`pright` int(11) unsigned NOT NULL,
`level` int(11) unsigned NOT NULL DEFAULT '0',
`title` varchar(64) NOT NULL,
`order` smallint(6) NOT NULL DEFAULT '0',
`option_id` int(11) NOT NULL,
PRIMARY KEY (`category_id`),
KEY `user_id` (`user_id`),
KEY `parent_id` (`parent_id`),
KEY `pleft` (`pleft`),
KEY `pright` (`pright`),
KEY `level` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_ynidea_categories`
--

INSERT IGNORE INTO `engine4_ynidea_categories` (`category_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`) VALUES
(1, 0, NULL, 1, 4, 0, 'All Categories');  

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynidea_admin_main_categories', 'ynidea', 'Categories', '', '{"route":"admin_default","module":"ynidea","controller":"category", "action":"index"}', 'ynidea_admin_main', '', 15);