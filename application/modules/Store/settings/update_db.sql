ALTER TABLE `engine4_store_products` ADD `item_condition` VARCHAR(16) NOT NULL;

ALTER TABLE `engine4_store_orderitems` CHANGE `status` `status` ENUM('initial','processing','shipping','delivered','cancelled','completed','pending') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'initial';
ALTER TABLE `engine4_store_orders` CHANGE `status` `status` ENUM('initial','processing','shipping','delivered','cancelled','completed','pending') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'initial';


CREATE TABLE IF NOT EXISTS `engine4_store_brequests` (
  `brequest_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `order_id` int(11) unsigned NOT NULL,
  `order_item_id` int(11) unsigned NOT NULL,
  `credit` tinyint(1) DEFAULT NULL,
  `product_ids` text NULL,
  `status` varchar(128) NULL DEFAULT 'pending',
  `creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`brequest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `engine4_store_orders` CHANGE `item_type` `item_type` ENUM('store_cart','store_request','store_product') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE `engine4_store_orders` DROP INDEX item_type_item_id;

ALTER TABLE `engine4_store_brequests` ADD `credit_value` decimal(16,2) NOT NULL DEFAULT '0';

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('store_request_approved', 'store', '{item:$subject} has accepted your business request for item: {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`, `template_id`, `enable_template`, `email_signature_en`, `show_signature`) VALUES
('store_cart_complete', 'store', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[order_details]', 0, 1, '', 0);