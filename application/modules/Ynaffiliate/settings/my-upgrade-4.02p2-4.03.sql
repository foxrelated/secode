UPDATE `engine4_core_modules` SET `version` = '4.03' where 'name' = 'ynaffiliate';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('ynaffiliate_menu_commissiontracking', 'ynaffiliate', 'Commission Tracking', 'Ynaffiliate_Plugin_Menus::canView', '{"module":"ynaffiliate","route":"ynaffiliate_extended", "controller":"tracking", "action":"purchase"}', 'ynaffiliate_main', '', 1, 0, 3),
('ynaffiliate_menu_linktracking', 'ynaffiliate', 'Links Tracking', 'Ynaffiliate_Plugin_Menus::canView', '{"module":"ynaffiliate","route":"ynaffiliate_extended", "controller":"tracking", "action":"click"}', 'ynaffiliate_main', '', 1, 0, 4),
('ynaffiliate_menu_dynamic', 'ynaffiliate', 'Dynamic Links', 'Ynaffiliate_Plugin_Menus::canView', '{"module":"ynaffiliate","route":"ynaffiliate_extended", "controller": "sources", "action":"dynamic"}', 'ynaffiliate_main', '', 1, 0, 6),
('ynaffiliate_menu_statistics', 'ynaffiliate', 'Statistic', 'Ynaffiliate_Plugin_Menus::canView', '{"module":"ynaffiliate","route":"ynaffiliate_extended", "controller": "statistic"}', 'ynaffiliate_main_more', '', 1, 0, 8),
('ynaffiliate_menu_requests', 'ynaffiliate', 'My Requests', 'Ynaffiliate_Plugin_Menus::canView', '{"module":"ynaffiliate","route":"ynaffiliate_extended", "controller": "my-request"}', 'ynaffiliate_main_more', '', 1, 0, 9);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('ynaffiliate_main_more', 'ynaffiliate', 'More +', 'Ynaffiliate_Plugin_Menus::canView', '{"uri":"javascript:void(0);"}', 'ynaffiliate_main', 'ynaffiliate_main_more', 1, 0, 7);

UPDATE `engine4_core_menuitems` SET `label` = 'Network Clients' where `name` = 'ynaffiliate_menu_manage';
UPDATE `engine4_core_menuitems` SET `order` = 5 where `name` = 'ynaffiliate_menu_sources';
UPDATE `engine4_core_menuitems` SET `order` = 10 where `name` = 'ynaffiliate_menu_help';
UPDATE `engine4_core_menuitems` SET `order` = 11 where `name` = 'ynaffiliate_menu_faqs';
UPDATE `engine4_core_menuitems` SET `label` = 'YN - Affiliates' where `name` = 'core_admin_main_plugins_ynaffiliate';

DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'ynaffiliate_menu_account';

UPDATE `engine4_core_menuitems` SET `menu` = 'ynaffiliate_main_more' where `name` = 'ynaffiliate_menu_help';
UPDATE `engine4_core_menuitems` SET `menu` = 'ynaffiliate_main_more' where `name` = 'ynaffiliate_menu_faqs';

UPDATE  `engine4_core_pages` SET  `displayname` = 'YN - Affiliate Network Clients Page', `title` = 'Affiliate Network Clients Page', `description` = 'Affiliate Network Clients Page' WHERE  `engine4_core_pages`.`name` = 'ynaffiliate_my-affiliate_index';
DELETE FROM `engine4_core_pages` WHERE `engine4_core_pages`.`name` = 'ynaffiliate_my-account_index';

INSERT IGNORE INTO `engine4_ynaffiliate_rules` (`enabled`, `module`, `rule_name`, `rule_title`) VALUES 
('1', 'ynbusinesspages', 'publish_business', 'Publish a business'),
('1', 'ynbusinesspages', 'feature_business', 'Feature a business'),
('1', 'ynlistings', 'publish_listing', 'Publish a listing'),
('1', 'ynjobposting', 'publish_job', 'Publish a job'),
('1', 'ynjobposting', 'sponsor_company', 'Sponsor a company'),
('1', 'ynmultilisting', 'publish_multilisting', 'Publish a listing'),
('1', 'ynmultilisting', 'feature_multilisting', 'Feature a listing'),
('1', 'ynsocialads', 'publish_ad', 'Publish an ad'),
('1', 'ynmember', 'feature_member', 'Feature member'),
('1', 'yncontest', 'publish_contest', 'Publish a contest'),
('1', 'ynresume', 'feature_resume', 'Feature resume'),
('1', 'ynresume', 'who_view_me', 'Use "Who View Me" service');

INSERT IGNORE INTO `engine4_ynaffiliate_suggests` (`module`, `priority`, `enabled`, `suggest_title`, `plugin`, `href`) VALUES
('ynbusinesspages', 999, 1, 'Business Home Page', '', ''),
('ynlistings', 999, 1, 'Listing Home Page', '', ''),
('ynjobposting', 999, 1, 'Job Posting Home Page', '', ''),
('ynmultilisting', 999, 1, 'Multiple Listing Home Page', '', ''),
('ynsocialads', 999, 1, 'Social Ads Home Page', '', ''),
('ynmember', 999, 1, 'Browse Member Page', '', ''),
('yncontest', 999, 1, 'Contest Home Page', '', ''),
('ynresume', 999, 1, 'Resume Home Page', '', '');

-- Add delay status
ALTER TABLE `engine4_ynaffiliate_commissions` CHANGE `approve_stat` `approve_stat` ENUM('waiting','approved','denied','delaying') NOT NULL default 'waiting';

-- Add reason column
ALTER TABLE `engine4_ynaffiliate_commissions` ADD COLUMN `reason` varchar(128);

-- Add task to auto approve commission when delay period is over, interval 12 hours
INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`) VALUES
('Ynaffiliate Approve Delaying Commissions', 'ynaffiliate', 'Ynaffiliate_Plugin_Task_ApproveDelayingCommissions', 43200);

-- Add notification
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynaffiliate_commission_approved', 'ynaffiliate', 'Your commission from {item:$subject} was approved.{item:$object}', 0, ''),
('ynaffiliate_commission_denied', 'ynaffiliate', 'Your commission from {item:$subject} was declined.{item:$object}', 0, ''),
('ynaffiliate_request_approved', 'ynaffiliate', 'Your requested points were approved.{item:$object}', 0, ''),
('ynaffiliate_request_denied', 'ynaffiliate', 'Your requested points were declined.{item:$object}', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynaffiliate_commission_approved', 'ynaffiliate', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynaffiliate_commission_denied', 'ynaffiliate', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynaffiliate_request_approved', 'ynaffiliate', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynaffiliate_request_denied', 'ynaffiliate', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');