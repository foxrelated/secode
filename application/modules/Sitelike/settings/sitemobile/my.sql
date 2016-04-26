INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitelike_main_browse', 'sitelike', 'Liked Items', '','{"route":"like_general","module":"sitelike","controller":"index","action":"browse"}', 'sitelike_main', '', 0, 1,1,1),
('sitelike_main_myfriendslike', 'sitelike', 'My Friends'' Likes', 'Sitelike_Plugin_Menus::canMyfriendsLike',
'{"route":"like_myfriendslike","module":"sitelike","controller":"index","action":"myfriendslike"}','sitelike_main', '', 0, 2,1,1),
('sitelike_main_mycontent', 'sitelike', 'My Content', 'Sitelike_Plugin_Menus::canMycontentLikes',
'{"route":"like_mycontent","module":"sitelike","controller":"index","action":"mycontent"}', 'sitelike_main', '', 0, 3,1,1),
('sitelike_main_memberlike', 'sitelike', 'Who Likes Me', 'Sitelike_Plugin_Menus::canMemberLikes',
'{"route":"like_memberlike","module":"sitelike","controller":"index","action":"memberlike"}', 'sitelike_main', '', 0, 4,1,1),
('sitelike_main_mylikes', 'sitelike', 'My Likes', 'Sitelike_Plugin_Menus::canMyLikes',
'{"route":"like_mylikes","module":"sitelike","controller":"index","action":"mylikes"}', 'sitelike_main', '', 0, 5,1,1),
('sitelike_main_likesettings', 'sitelike', 'Like Settings', 'Sitelike_Plugin_Menus::canLikesettings',
'{"route":"like_settings","module":"sitelike","controller":"index","action":"likesettings"}', 'user_settings', '', 0, 6,1,1),
('core_main_sitelike', 'sitelike', 'Likes', '', '{"route":"like_general"}', 'core_main', '', 0, 16,1,1);


INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES ('message_all', 'standard', 'Message All Members', '999');
INSERT IGNORE INTO `engine4_sitemobile_navigation` (`name`, `menu`, `subject_type`) VALUES ('message_all', 'message_all', '');
INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('message_all', 'sitelike', 'Message All Members', 'Sitelike_Plugin_Menus::canSendMessages','', 'message_all', '', 0, 1,1,1);