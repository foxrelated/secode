
-- --------------------------------------------------------

--
-- Table structure for table `engine4_spamcontrol_warn`
--

DROP TABLE IF EXISTS `engine4_spamcontrol_warn`;
CREATE TABLE `engine4_spamcontrol_warn` (
	`warn_id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) UNSIGNED NOT NULL,
	`resource_id` INT(11) UNSIGNED NOT NULL,
	`resource_type` VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci',
	`body` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`count` INT(11) NOT NULL,
	PRIMARY KEY (`warn_id`),
	UNIQUE INDEX `Index 2` (`resource_type`, `resource_id`, `warn_id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;
-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('user_warn', 'spamcontrol', 'You was warn. {item:$object}', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_user_warn', 'spamcontrol', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `order`) VALUES
('core_admin_main_plugins_spamcontrol', 'spamcontrol', 'Spam control', '', '{"route":"admin_default","module":"spamcontrol", "controller":"settings","action":"comment"}', 'core_admin_main_plugins', '', 1, 999),

('spamcontrol_admin_main_commentcontrol', 'spamcontrol', 'Comment control', '', '{"route":"admin_default","module":"spamcontrol","controller":"settings", "action":"comment"}', 'spamcontrol_admin_main', '', 1, 1),

('spamcontrol_admin_main_privatemessagecontrol', 'spamcontrol', 'Private message control', '', '{"route":"admin_default","module":"spamcontrol","controller":"settings", "action":"message"}', 'spamcontrol_admin_main', '', 1, 2),
('spamcontrol_admin_main_post', 'spamcontrol', 'Post Control', '', '{"route":"admin_default","module":"spamcontrol","controller":"settings", "action":"post"}', 'spamcontrol_admin_main', '', 1, 3),
('spamcontrol_admin_main_blog', 'spamcontrol', 'Blog Control', 'Spamcontrol_Plugin_Menus', '{"route":"admin_default","module":"spamcontrol","controller":"settings", "action":"blog"}', 'spamcontrol_admin_main', '', 1, 4),
('spamcontrol_admin_main_photo', 'spamcontrol', 'Photo Control', 'Spamcontrol_Plugin_Menus', '{"route":"admin_default","module":"spamcontrol","controller":"settings", "action":"photo"}', 'spamcontrol_admin_main', '', 1, 5),
('spamcontrol_admin_main_user', 'spamcontrol', 'Potential Spamer', '', '{"route":"admin_default","module":"spamcontrol","controller":"settings", "action":"user"}', 'spamcontrol_admin_main', '', 1, 6),
('spamcontrol_admin_main_recaptcha', 'spamcontrol', 'Recaptcha', '', '{"route":"admin_default","module":"spamcontrol","controller":"settings", "action":"recaptcha"}', 'spamcontrol_admin_main', '', 1, 7),
('spamcontrol_admin_main_settings', 'spamcontrol', 'Settings', '', '{"route":"admin_default","module":"spamcontrol","controller":"settings", "action":"settings"}', 'spamcontrol_admin_main', '', 1, 8);







