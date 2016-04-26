UPDATE `engine4_core_modules` SET `version` = '4.04p3' WHERE `engine4_core_modules`.`name` = 'groupbuy' LIMIT 1 ;

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

DROP TABLE IF EXISTS `engine4_groupbuy_gateways`;

DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'groupbuy_admin_main_paymentsettings';