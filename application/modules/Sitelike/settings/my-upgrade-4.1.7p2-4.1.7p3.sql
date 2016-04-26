--
-- Dumping data for table `engine4_core_menus`
--
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES ('sitelike_main', 'standard', 'Like Main Navigation Menu', '999');

--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitelike_main_browse', 'sitelike', 'Liked Items', '', '{"route":"like_general","module":"sitelike","controller":"index","action":"browse"}', 'sitelike_main', '', 1, 0, 1),
('sitelike_main_myfriendslike', 'sitelike', 'My Friends'' Likes', 'Sitelike_Plugin_Menus::canMyfriendsLike', '{"route":"like_myfriendslike","module":"sitelike","controller":"index","action":"myfriendslike"}', 'sitelike_main', '', 1, 0, 1),
('sitelike_main_mycontent', 'sitelike', 'My Content', 'Sitelike_Plugin_Menus::canMycontentLikes', '{"route":"like_mycontent","module":"sitelike","controller":"index","action":"mycontent"}', 'sitelike_main', '', 1, 0, 1),
('sitelike_main_memberlike', 'sitelike', 'Who Likes Me', 'Sitelike_Plugin_Menus::canMemberLikes', '{"route":"like_memberlike","module":"sitelike","controller":"index","action":"memberlike"}', 'sitelike_main', '', 1, 0, 1),
('sitelike_main_mylikes', 'sitelike', 'My Likes', 'Sitelike_Plugin_Menus::canMyLikes', '{"route":"like_mylikes","module":"sitelike","controller":"index","action":"mylikes"}', 'sitelike_main', '', 1, 0, 1),
('sitelike_main_likesettings', 'sitelike', 'Like Settings', 'Sitelike_Plugin_Menus::canLikesettings', '{"route":"like_settings","module":"sitelike","controller":"index","action":"likesettings"}', 'sitelike_main', '', 1, 0, 1);
