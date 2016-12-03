DROP TABLE IF EXISTS `engine4_money_gateways`;
CREATE TABLE `engine4_money_gateways` (
	`gateway_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`description` TEXT NULL COLLATE 'utf8_unicode_ci',
	`enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`plugin` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`config` MEDIUMBLOB NULL,
	`test_mode` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`gateway_id`),
	INDEX `enabled` (`enabled`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;

INSERT INTO `engine4_money_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `test_mode`) VALUES
(1, 'Paypal', NULL, 0, 'Money_Plugin_Gateway_PayPal', 0),
(2, 'Web money', '', 0, 'Money_Plugin_Gateway_Webmoney', 0),
(3, '2Checkout', '', 0, 'Money_Plugin_Gateway_2Checkout',  0),
(4, 'LiqPay', '', 0, 'Money_Plugin_Gateway_LiqPay', 0);

DROP TABLE IF EXISTS `engine4_money_products`;
CREATE TABLE `engine4_money_products` (
	`product_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`extension_type` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`extension_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	`sku` BIGINT(20) UNSIGNED NOT NULL,
	`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`description` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`price` DECIMAL(16,2) UNSIGNED NOT NULL,
	PRIMARY KEY (`product_id`),
	UNIQUE INDEX `sku` (`sku`),
	INDEX `extension_type` (`extension_type`, `extension_id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;

DROP TABLE IF EXISTS `engine4_money_packages`;
CREATE TABLE `engine4_money_packages` (
	`package_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`description` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`price` DECIMAL(16,2) UNSIGNED NOT NULL,
	`enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	PRIMARY KEY (`package_id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;

DROP TABLE IF EXISTS `engine4_money_money`;
CREATE TABLE `engine4_money_money` (
	`money_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) UNSIGNED NOT NULL,
	`money` DECIMAL(16,2) NOT NULL,
	`spent` INT(11) NOT NULL,
	`input` INT(11) NOT NULL,
	`output` INT(11) NOT NULL,
	`total` INT(11) NOT NULL,
	PRIMARY KEY (`user_id`),
	INDEX `money_id` (`money_id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;

DROP TABLE IF EXISTS `engine4_money_orders`;
CREATE TABLE `engine4_money_orders` (
	`order_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(10) UNSIGNED NOT NULL,
	`gateway_id` INT(10) UNSIGNED NOT NULL,
	`gateway_order_id` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`gateway_transaction_id` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`state` ENUM('pending','cancelled','failed','incomplete','complete','refuse') NOT NULL DEFAULT 'pending' COLLATE 'latin1_general_ci',
	`creation_date` DATETIME NOT NULL,
	`source_type` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`source_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	`body` TEXT NOT NULL,
	PRIMARY KEY (`order_id`),
	INDEX `user_id` (`user_id`),
	INDEX `gateway_id` (`gateway_id`, `gateway_order_id`),
	INDEX `state` (`state`),
	INDEX `source_type` (`source_type`, `source_id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;

DROP TABLE IF EXISTS `engine4_money_subscriptions`;
CREATE TABLE `engine4_money_subscriptions` (
	`subscription_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) UNSIGNED NOT NULL,
	`package_id` INT(11) UNSIGNED NOT NULL,
	`status` ENUM('initial','trial','pending','active','cancelled','expired','overdue','refunded') NOT NULL DEFAULT 'initial' COLLATE 'utf8_unicode_ci',
	`active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`creation_date` DATETIME NOT NULL,
	`modified_date` DATETIME NULL DEFAULT NULL,
	`payment_date` DATETIME NULL DEFAULT NULL,
	`expiration_date` DATETIME NULL DEFAULT NULL,
	`notes` TEXT NULL COLLATE 'utf8_unicode_ci',
	`gateway_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	`gateway_profile_id` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`subscription_id`),
	UNIQUE INDEX `gateway_id` (`gateway_id`, `gateway_profile_id`),
	INDEX `user_id` (`user_id`),
	INDEX `package_id` (`package_id`),
	INDEX `status` (`status`),
	INDEX `active` (`active`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;

DROP TABLE IF EXISTS `engine4_money_transactions`;
CREATE TABLE `engine4_money_transactions` (
	`transaction_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`gateway_id` INT(10) UNSIGNED NOT NULL,
	`timestamp` DATETIME NOT NULL,
	`order_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`type` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`state` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`body` TEXT NOT NULL,
	`gateway_transaction_id` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`gateway_parent_transaction_id` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`gateway_order_id` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`amount` DECIMAL(16,2) NOT NULL,
	`currency` CHAR(3) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`transaction_id`),
	INDEX `user_id` (`user_id`),
	INDEX `gateway_id` (`gateway_id`),
	INDEX `type` (`type`),
	INDEX `state` (`state`),
	INDEX `gateway_transaction_id` (`gateway_transaction_id`),
	INDEX `gateway_parent_transaction_id` (`gateway_parent_transaction_id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;


DROP TABLE IF EXISTS `engine4_money_issues`;
CREATE TABLE `engine4_money_issues` (
	`issue_id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) NOT NULL,
	`gateway_id` INT(11) NOT NULL,
	`amount` DECIMAL(16,2) NOT NULL,
	`creation_date` DATETIME NOT NULL,
	`type` INT(11) NOT NULL,
	`purse` VARCHAR(50) NOT NULL,
	`enable` TINYINT(4) NOT NULL DEFAULT '1',
	PRIMARY KEY (`issue_id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;



INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
('money_main_browse', 'money', 'Add funds', '', '{"route":"money_subscription","action":"choose"}', 'money_main', '', 1, 0, 1),
('money_main_send', 'money', 'Send to a friend', NULL, '{"route":"money_general", "action":"send"}', 'money_main', '', 1, 0, 2),
('money_main_transaction', 'money', 'Transactions', '', '{"route":"money_general", "action":"transaction"}', 'money_main', '', 1, 0, 3),
('core_admin_main_plugins_money', 'money', 'E-Money', NULL, '{"route":"admin_default","module":"money","controller":"manage","action":"index"}', 'core_admin_main_plugins', '', 1, 0, 999),
('money_admin_main_manage', 'money', 'Manage', '', '{"route":"admin_default","module":"money","controller":"manage"}', 'money_admin_main', '', 1, 0, 1),
('money_admin_main_transaction', 'money', 'Transactions', '', '{"route":"admin_default","module":"money","controller":"manage", "action":"transaction"}', 'money_admin_main', '', 1, 0, 2),
('money_admin_main_gateway', 'money', 'Gateway', '', '{"route":"admin_default","module":"money","controller":"gateway", "action":"gateway"}', 'money_admin_main', NULL, 1, 0, 3),
('money_admin_main_settings', 'money', 'Global Settings', '', '{"route":"admin_default","module":"money","controller":"manage", "action":"settings"}', 'money_admin_main', '', 1, 0, 4),
('money_main_issue', 'money', 'Withdraw', NULL, '{"route":"money_general","action":"issue"}', 'money_main', NULL, 1, 0, 2),
('core_main_money', 'money', 'E-Money', NULL, '{"route":"money_general"}', 'core_main', '', 1, 0, 999),
('money_admin_main_plans', 'money', 'Plan', NULL, '{"route":"admin_default","module":"money","controller":"package", "action":"index"}', 'money_admin_main', NULL, 1, 0, 5),
('money_admin_main_issue', 'money', 'Issue an invoice', 'Money_Plugin_Menus', '{"route":"admin_default","module":"money","controller":"issue", "action":"issue"}', 'money_admin_main', '', 1, 0, 5)
;


INSERT INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('send_money', 'money', '{item:$subject} send money {item:$object:$count}.', 0, '', 1),
('refuse_money', 'money', '{item:$object} withdraw denied.', 0, '', 1),
('approve_money', 'money', '{item:$object} Withdraw approval.', 0, '', 1)
;

INSERT INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('notify_cart_approve_money', 'money', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_refuse_money', 'money', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_send_money', 'money', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('money.commission', '0.8'),
('money.commissionissue', '0.8'),
('money.page', '30')
;




 
