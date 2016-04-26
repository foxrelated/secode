INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES ('sitelike', 'Likes', 'Likes', '4.8.6p1', 1, 'extra') ;
-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitelike_mixsettings`
--

DROP TABLE IF EXISTS `engine4_sitelike_mixsettings`;
CREATE TABLE IF NOT EXISTS `engine4_sitelike_mixsettings` (
  `mixsetting_id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `resource_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `resource_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `item_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title_items` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `value` int(11) NOT NULL DEFAULT '1',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`mixsetting_id`),
  UNIQUE KEY `module` (`module`,`resource_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
-- --------------------------------------------------------
DROP TABLE IF EXISTS `engine4_sitelike_settings`;
CREATE TABLE IF NOT EXISTS `engine4_sitelike_settings` ( 
	`setting_id` int(255) NOT NULL AUTO_INCREMENT,
	`content_type` varchar(255) NOT NULL,
	`tab1_show` varchar(255) NOT NULL, 
	`tab1_duration` varchar(255) NOT NULL, 
	`tab1_name` varchar(255) NOT NULL, 
	`tab1_entries` int(255) NOT NULL, 
	`tab2_show` varchar(255) NOT NULL,
	`tab2_duration` varchar(255) NOT NULL, 
	`tab2_name` varchar(255) NOT NULL, 
	`tab2_entries` int(255) NOT NULL,
	`tab3_show` varchar(255) NOT NULL, 
	`tab3_duration` varchar(255) NOT NULL, 
	`tab3_name` varchar(255) NOT NULL,
	`tab3_entries` int(255) NOT NULL, 
	`view_layout` varchar(255) NOT NULL, 
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `category_id` (`setting_id`),
  UNIQUE KEY `content_type` (`content_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
-- --------------------------------------------------------
DROP TABLE IF EXISTS `engine4_sitelike_mysettings`;
CREATE TABLE `engine4_sitelike_mysettings` ( 
	`mysetting_id` INT( 255 ) NOT NULL AUTO_INCREMENT , 
	`user_id` INT( 255 ) NOT NULL , 
	`like` INT( 255 ) NOT NULL , 
	PRIMARY KEY ( `mysetting_id` ) , 
	UNIQUE ( `mysetting_id` )
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
-- --------------------------------------------------------
INSERT IGNORE INTO `engine4_sitelike_settings` (`content_type`, `tab1_show`, `tab1_duration`, `tab1_name`, `tab1_entries`, `tab2_show`, `tab2_duration`, `tab2_name`, `tab2_entries`, `tab3_show`, `tab3_duration`, `tab3_name`, `tab3_entries`, `view_layout`) VALUES
( 'group', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'user', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'classified', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'document', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'video', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'mixed', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'blog', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'event', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'album', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'music', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'poll', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'forum_topic', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'group_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'album_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'event_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'sitebusiness_business', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall',
   'Overall', 3,'1'),
( 'sitebusiness_album', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall',
3, '1'),
('sitebusinessvideo_video', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall',
'Overall', 3, '1'),
('sitebusinesspoll_poll', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall',
3, '1'),
('sitebusinessnote_note', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall',
3, '1'),
('sitebusinessreview_review', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall',
'Overall', 3, '1'),
('sitebusinessmusic_playlist', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall',
'Overall', 3, '1'),
('sitebusiness_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3,
'1'),
('sitebusinessevent_event', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall',
'Overall', 3, '1'),
('sitebusinessnote_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall',
'Overall', 3, '1'),
('list_listing', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitepage_page', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitepage_album', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitepagevideo_video', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3,
'1'),
('sitepagepoll_poll', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3,
'1'),
('sitepagenote_note', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3,
'1'),
('sitepagedocument_document', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitepagereview_review', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3,
'1'),
('recipe', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitepagemusic_playlist', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitepage_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitepageevent_event', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3,
'1'),
('sitepagenote_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3,
'1'),
('list_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('recipe_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('siteestore_product', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitereview_listing', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitereview_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitereview_video', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),

( 'sitegroup_group', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall',
   'Overall', 3,'1'),
( 'sitegroup_album', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall',
3, '1'),
('sitegroupvideo_video', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall',
'Overall', 3, '1'),
('sitegrouppoll_poll', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall',
3, '1'),
('sitegroupnote_note', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall',
3, '1'),
('sitegroupreview_review', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall',
'Overall', 3, '1'),
('sitegroupmusic_playlist', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall',
'Overall', 3, '1'),
('sitegroup_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3,
'1'),
('sitegroupevent_event', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall',
'Overall', 3, '1'),
('sitegroupnote_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall',
'Overall', 3, '1');

INSERT IGNORE INTO `engine4_sitelike_settings` (`content_type`, `tab1_show`, `tab1_duration`, `tab1_name`, `tab1_entries`, `tab2_show`, `tab2_duration`, `tab2_name`, `tab2_entries`, `tab3_show`, `tab3_duration`, `tab3_name`, `tab3_entries`, `view_layout`) VALUES
( 'sitestore_store', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall',
   'Overall', 3,'1'),
('sitestoreproduct_product', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1');



-- --------------------------------------------------------
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES 
('core_admin_main_plugins_sitelike', 'sitelike', 'SEAO - Likes Plugin & Widgets', '', '{"route":"admin_default","module":"sitelike","controller":"global"}', 'core_admin_main_plugins', '', 999),
('core_main_sitelike', 'sitelike', 'Likes', '', '{"route":"like_general"}', 'core_main', '', 4),
('mobi_browse_sitelike', 'sitelike', 'Likes', '', '{"route":"like_general"}', 'mobi_browse', '', 4);

-- --------------------------------------------------------
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES (
'sitelike_admin_global_settings', 'sitelike', 'Global Settings', '', '{"route":"admin_default","module":"sitelike","controller":"global"}', 'sitelike_admin_main', '', '1', '0',
1),
('sitelike_admin_main_settings', 'sitelike', 'Tabbed Widgets', '', '{"route":"admin_default","module":"sitelike","controller":"settings"}', 'sitelike_admin_main', '', '1', '0', 2),
('sitelike_admin_mix_settings', 'sitelike', 'Mixed Content Widgets', '', '{"route":"admin_default","module":"sitelike","controller":"mix"}', 'sitelike_admin_main', '', '1', '0', 3),('sitelike_admin_widget_settings', 'sitelike', 'Other Widgets', '', '{"route":"admin_default","module":"sitelike","controller":"widgets"}', 'sitelike_admin_main', '', '1', '0', 4),
('sitelike_admin_like_settings', 'sitelike', 'Like Button View', '', '{"route":"admin_default","module":"sitelike","controller":"settings", "action":"likesettings"}', 'sitelike_admin_main', '', '1', '0', 5),
('sitelike_admin_faqs', 'sitelike', 'FAQ', '', '{"route":"admin_default","module":"sitelike","controller":"global", "action":"faq"}', 'sitelike_admin_main', '', '1', '0', 998),
( 'sitelike_admin_manage_modules', 'sitelike', 'Manage Modules', '', '{"route":"admin_default","module":"sitelike","controller":"manage","action":"index"}', 'sitelike_admin_main', '', 1, 0, 8);

-- --------------------------------------------------------
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('like.link.position', '3'),
('like.browse.auth', '1'),
('like.profile.show', '1'),
('browse.likes.entries','10'),
('profile.owner.entries','3'),
('like.setting.button','1'),
('like.title.turncation','16'),
('like.forum.show', '1'),
('like.setting.show', '1'),
('like.mix.wid', '5');
-- --------------------------------------------------------
--
-- Dumping data for table `engine4_sitelike_mixsettings`
--

INSERT IGNORE INTO `engine4_sitelike_mixsettings` ( `module`, `resource_type`, `resource_id`, `item_title`, `title_items`, `value`, `default`, `enabled`) VALUES
( 'user', 'user', 'user_id', 'Members', 'Member', 1, 1, 1),
( 'album', 'album', 'album_id', 'Albums', 'Album', 1, 1, 1),
( 'album', 'album_photo', 'photo_id', 'Album Photos', 'Album Photo', 1, 1, 1),
( 'blog', 'blog', 'blog_id', 'Blogs', 'Blog', 1, 1, 1),
( 'classified', 'classified', 'classified_id', 'Classifieds', 'Classified', 1, 1, 1),
( 'event', 'event', 'event_id', 'Events', 'Event', 1, 1, 1),
( 'event', 'event_photo', 'event_id', 'Event Photos', 'Event Photo', 1, 1, 1),
( 'group', 'group', 'group_id', 'Groups', 'Group', 1, 1, 1),
( 'group', 'group_photo', 'photo_id', 'Group Photos', 'Group Photo', 1, 1, 1),
( 'forum', 'forum_topic', 'forum_id', 'Forums', 'Forum', 1, 1, 1),
( 'music', 'music_playlist', 'playlist_id', 'Music', 'Music', 1, 1, 1),
( 'poll', 'poll', 'poll_id', 'Polls', 'Poll', 1, 1, 1),
( 'video', 'video', 'video_id', 'Videos', 'Video', 1, 1, 1),
( 'document', 'document', 'document_id', 'Documents', 'Document', 1, 1, 1),
( 'list', 'list_listing', 'listing_id', 'Listings', 'Listing', 1, 1, 1),
( 'list', 'list_photo', 'photo_id', 'Listing Photos', 'Listing Photo', 1, 1, 1),
( 'recipe', 'recipe', 'recipe_id', 'Recipes', 'Recipe', 1, 1, 1),
( 'recipe', 'recipe_photo', 'photo_id', 'Recipe Photos', 'Recipe Photo', 1, 1, 1),
( 'sitepage', 'sitepage_page', 'page_id', 'Pages', 'Page', 1, 1, 1),
( 'sitepagealbum', 'sitepage_photo', 'photo_id', 'Page Album Photos', 'Page Album Photo', 1, 1, 1),
( 'sitepagealbum', 'sitepage_album', 'album_id', 'Page Albums', 'Page Album', 1, 1, 1),
( 'sitepagedocument', 'sitepagedocument_document', 'document_id', 'Page Documents', 'Page Document', 1, 1, 1),
( 'sitepageevent', 'sitepageevent_event', 'event_id', 'Page Events', 'Page Event', 1, 1, 1),
( 'sitepagemusic', 'sitepagemusic_playlist', 'playlist_id', 'Page Music', 'Page Music', 1, 1, 1),
( 'sitepagenote', 'sitepagenote_photo', 'photo_id', 'Page Note Photos', 'Page Note Photo', 1, 1, 1),
( 'sitepagenote', 'sitepagenote_note', 'note_id', 'Page Notes', 'Page Note', 1, 1, 1),
( 'sitepagepoll', 'sitepagepoll_poll', 'poll_id', 'Page Polls', 'Page Poll', 1, 1, 1),
( 'sitepagereview', 'sitepagereview_review', 'review_id', 'Page Reviews', 'Page Review', 1, 1, 1),
( 'sitepagevideo', 'sitepagevideo_video', 'video_id', 'Page Videos', 'Page Video', 1, 1, 1),
( 'sitebusiness', 'sitebusiness_business', 'business_id', 'Businesses', 'Business', 1, 1, 1),
( 'sitebusinessalbum', 'sitebusiness_photo', 'photo_id', 'Business Album Photos', 'Business Album Photo', 1, 1, 1),
( 'sitebusinessalbum', 'sitebusiness_album', 'album_id', 'Business Albums', 'Business Album', 1, 1, 1),
( 'sitebusinessdocument', 'sitebusinessdocument_document', 'document_id', 'Business Documents', 'Business Document', 1, 1, 1),
( 'sitebusinessevent', 'sitebusinessevent_event', 'event_id', 'Business Events', 'Business Event', 1, 1, 1),
( 'sitebusinessmusic', 'sitebusinessmusic_playlist', 'playlist_id', 'Business Music', 'Business Music', 1, 1, 1),
( 'sitebusinessnote', 'sitebusinessnote_photo', 'photo_id', 'Business Note Photos', 'Business Note Photo', 1, 1, 1),
( 'sitebusinessnote', 'sitebusinessnote_note', 'note_id', 'Business Notes', 'Business Note', 1, 1, 1),
( 'sitebusinesspoll', 'sitebusinesspoll_poll', 'poll_id', 'Business Polls', 'Business Poll', 1, 1, 1),
( 'sitebusinessreview', 'sitebusinessreview_review', 'review_id', 'Business Reviews', 'Business Review', 1, 1,1),
( 'sitebusinessvideo', 'sitebusinessvideo_video', 'video_id', 'Business Videos', 'Business Video', 1, 1, 1),
('siteestore', 'siteestore_product', 'product_id', 'Products', 'Product', 1, 1, 1),
('sitereview', 'sitereview_listing', 'listing_id', 'Listings', 'Listing', 1, 1, 1),
('sitereview', 'sitereview_photo', 'photo_id', 'Listing Photos', 'Listing Photos', 1, 1, 1),
('sitereview', 'sitereview_video', 'video_id', 'Listing Videos', 'Listing Video', 1, 1, 1),
('ynevent', 'event', 'event_id', 'Events', 'Event', 1, 0, 1),
('ynevent_photo', 'event_photo', 'photo_id', 'Event Photos', 'Event Photo', 1, 0, 1),
('advgroup', 'group', 'group_id', 'Groups', 'Group', 1, 0, 1),
('advgroup_photo', 'group_photo', 'photo_id', 'Group Photos', 'Group Photo', 1, 0, 1),
('ynblog', 'blog', 'blog_id', 'Blogs', 'Blog', 1, 0, 1),
('ynforum', 'forum_topic', 'forum_id', 'Forums', 'Forum', 1, 0, 1),
('ynvideo', 'video', 'video_id', 'Videos', 'Video', 1, 0, 1),

( 'sitegroup', 'sitegroup_group', 'group_id', 'Groupes', 'Group', 1, 1, 1),
( 'sitegroupalbum', 'sitegroup_photo', 'photo_id', 'Group Album Photos', 'Group Album Photo', 1, 1, 1),
( 'sitegroupalbum', 'sitegroup_album', 'album_id', 'Group Albums', 'Group Album', 1, 1, 1),
( 'sitegroupdocument', 'sitegroupdocument_document', 'document_id', 'Group Documents', 'Group Document', 1, 1, 1),
( 'sitegroupevent', 'sitegroupevent_event', 'event_id', 'Group Events', 'Group Event', 1, 1, 1),
( 'sitegroupmusic', 'sitegroupmusic_playlist', 'playlist_id', 'Group Music', 'Group Music', 1, 1, 1),
( 'sitegroupnote', 'sitegroupnote_photo', 'photo_id', 'Group Note Photos', 'Group Note Photo', 1, 1, 1),
( 'sitegroupnote', 'sitegroupnote_note', 'note_id', 'Group Notes', 'Group Note', 1, 1, 1),
( 'sitegrouppoll', 'sitegrouppoll_poll', 'poll_id', 'Group Polls', 'Group Poll', 1, 1, 1),
( 'sitegroupreview', 'sitegroupreview_review', 'review_id', 'Group Reviews', 'Group Review', 1, 1,1),
( 'sitegroupvideo', 'sitegroupvideo_video', 'video_id', 'Group Videos', 'Group Video', 1, 1, 1);


INSERT IGNORE INTO `engine4_sitelike_mixsettings` ( `module`, `resource_type`, `resource_id`, `item_title`, `title_items`, `value`, `default`, `enabled`) VALUES
( 'sitestore', 'sitestore_store', 'store_id', 'Stores', 'Store', 1, 1, 1),
('sitestoreproduct', 'sitestoreproduct_product', 'product_id', 'Products', 'Product', 1, 1, 1);




-- ------------------------------------------------------------------------
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('sitelike_main', 'standard', 'Like Main Navigation Menu', '999');

-- --------------------------------------------------------
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitelike_main_browse', 'sitelike', 'Liked Items', '',
'{"route":"like_general","module":"sitelike","controller":"index","action":"browse"}', 'sitelike_main', '', 1,
0, 1),
('sitelike_main_myfriendslike', 'sitelike', 'My Friends'' Likes', 'Sitelike_Plugin_Menus::canMyfriendsLike',
'{"route":"like_myfriendslike","module":"sitelike","controller":"index","action":"myfriendslike"}',
'sitelike_main', '', 1, 0, 2),
('sitelike_main_mycontent', 'sitelike', 'My Content', 'Sitelike_Plugin_Menus::canMycontentLikes',
'{"route":"like_mycontent","module":"sitelike","controller":"index","action":"mycontent"}', 'sitelike_main',
'', 1, 0, 3),
('sitelike_main_memberlike', 'sitelike', 'Who Likes Me', 'Sitelike_Plugin_Menus::canMemberLikes',
'{"route":"like_memberlike","module":"sitelike","controller":"index","action":"memberlike"}', 'sitelike_main',
'', 1, 0, 4),
('sitelike_main_mylikes', 'sitelike', 'My Likes', 'Sitelike_Plugin_Menus::canMyLikes',
'{"route":"like_mylikes","module":"sitelike","controller":"index","action":"mylikes"}', 'sitelike_main', '',
1, 0, 5),
('sitelike_main_likesettings', 'sitelike', 'Like Settings', 'Sitelike_Plugin_Menus::canLikesettings',
'{"route":"like_settings","module":"sitelike","controller":"index","action":"likesettings"}', 'sitelike_main',
'', 1, 0, 6),
('core_main_sitelike', 'sitelike', 'Likes', '', '{"route":"like_general"}', 'core_main', '', 1, 0, 999);

-- ------------------------------------------------------------------------
--
-- Dumping data for table `engine4_activity_actiontypes`
--
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`,
`attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_user', 'user', '{item:$subject} likes {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_list_listing', 'list', '{item:$subject} likes the listing {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_sitepage_page', 'sitepage', '{item:$subject} likes  the page {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_album', 'album', '{item:$subject} likes the album {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_album_photo', 'album', '{item:$subject} likes the photo {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_poll', 'poll', '{item:$subject} likes the poll {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_blog', 'blog', '{item:$subject} likes the blog {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_classified', 'classified', '{item:$subject} likes the classified listing {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_music_playlist', 'music', '{item:$subject} likes the playlist {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_video', 'video', '{item:$subject} likes the video {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_event', 'event', '{item:$subject} likes the event {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_event_photo', 'event', '{item:$subject} likes the events’  photo {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_group', 'group', '{item:$subject} likes the group {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_group_photo', 'group', '{item:$subject} likes the groups’ photo {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_document', 'document', '{item:$subject} likes the document {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_forum_topic', 'forum', '{item:$subject} likes the forum topic {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_sitepage_album', 'sitepagealbum', '{item:$subject} likes the page album {item:$object}:', 1, 5, 1,1, 1, 1),
('like_sitepage_photo', 'sitepagealbum', '{item:$subject} likes the page  photo {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_recipe_photo', 'recipe', '{item:$subject} likes the recipe photo {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_list_photo', 'list', '{item:$subject} likes the list photo {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_list_photo', 'list', '{item:$subject} likes the list photo {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_sitepagenote_note', 'sitepagenote', '{item:$subject} likes the page note {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_sitepagepoll_poll', 'sitepagepoll', '{item:$subject} likes the page poll {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_sitepagereview_review', 'sitepagereview', '{item:$subject} likes the page review {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_sitepagevideo_video', 'sitepagevideo', '{item:$subject} likes the page video {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_sitepagenote_photo', 'sitepagenote', '{item:$subject} likes the page note photo {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_sitepageevent_event', 'sitepageevent', '{item:$subject} likes the page event {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_sitepageevent_photo', 'sitepageevent', '{item:$subject} likes the page event photo {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_sitepagemusic_playlist', 'sitepagemusic', '{item:$subject} likes the page playlist {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_sitepagedocument_document', 'sitepagedocument', '{item:$subject} likes the page document {item:$object}:', 1, 5, 1, 1, 1, 1),
('like_sitebusiness_business', 'sitebusiness', '{item:$subject} likes the businesses {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitebusiness_photo', 'sitebusinessalbum', '{item:$subject} likes the business album photo {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitebusiness_album', 'sitebusinessalbum', '{item:$subject} likes the business album {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitebusinessdocument_document', 'sitebusinessdocument', '{item:$subject} likes the business document {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitebusinessevent_event', 'sitebusinessevent', '{item:$subject} likes the business event {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitebusinessmusic_playlist', 'sitebusinessmusic', '{item:$subject} likes the business music {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitebusinessnote_photo', 'sitebusinessnote', '{item:$subject} likes the business note photo {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitebusinessnote_note', 'sitebusinessnote', '{item:$subject} likes the business note {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitebusinesspoll_poll', 'sitebusinesspoll', '{item:$subject} likes the business poll {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitebusinessreview_review', 'sitebusinessreview', '{item:$subject} likes the business review {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitebusinessvideo_video', 'sitebusinessvideo', '{item:$subject} likes the business video {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_recipe', 'recipe', '{item:$subject} likes the recipe {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_siteestore_product', 'siteestore', '{item:$subject} likes the Product {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitereview_listing', 'sitereview', '{item:$subject} likes the {var:$listing_name} {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitereview_photo', 'sitereview', '{item:$subject} likes the {var:$listing_name} photo {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitereview_video', 'sitereview', '{item:$subject} likes the {var:$listing_name} video {item:$object}:', 0, 5, 1, 1, 1, 1),

('like_sitegroup_group', 'sitegroup', '{item:$subject} likes the groups {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitegroup_photo', 'sitegroupalbum', '{item:$subject} likes the group album photo {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitegroup_album', 'sitegroupalbum', '{item:$subject} likes the group album {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitegroupdocument_document', 'sitegroupdocument', '{item:$subject} likes the group document {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitegroupevent_event', 'sitegroupevent', '{item:$subject} likes the group event {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitegroupmusic_playlist', 'sitegroupmusic', '{item:$subject} likes the group music {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitegroupnote_photo', 'sitegroupnote', '{item:$subject} likes the group note photo {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitegroupnote_note', 'sitegroupnote', '{item:$subject} likes the group note {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitegrouppoll_poll', 'sitegrouppoll', '{item:$subject} likes the group poll {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitegroupreview_review', 'sitegroupreview', '{item:$subject} likes the group review {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitegroupvideo_video', 'sitegroupvideo', '{item:$subject} likes the group video {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitestore_store', 'sitestore', '{item:$subject} likes the stores {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitestoreproduct_product', 'sitestoreproduct', '{item:$subject} likes the product {item:$object}:', 0, 5, 1, 1, 1, 1);

INSERT IGNORE INTO `engine4_sitelike_settings` (`content_type`, `tab1_show`, `tab1_duration`, `tab1_name`, `tab1_entries`, `tab2_show`, `tab2_duration`, `tab2_name`, `tab2_entries`, `tab3_show`, `tab3_duration`, `tab3_name`, `tab3_entries`, `view_layout`) VALUES
('siteevent_event', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3,'1'),
('siteevent_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('siteeventdocument_document', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3,'1'),
('siteevent_video', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1');


INSERT IGNORE INTO `engine4_sitelike_mixsettings` ( `module`, `resource_type`, `resource_id`, `item_title`, `title_items`, `value`, `default`, `enabled`) VALUES
('siteevent', 'siteevent_event', 'event_id', 'Events', 'Event', 1, 1, 1),
('siteevent', 'siteevent_photo', 'photo_id', 'Event Photos', 'Event Photo', 1, 1, 1),
('siteeventdocument', 'siteeventdocument_document', 'document_id', 'Event Documents', 'Event Document', 1, 1, 1),
('siteevent', 'siteevent_video', 'video_id', 'Event Videos', 'Event Video', 1, 1, 1);


INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`,
`attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_siteevent_event', 'siteevent', '{item:$subject} likes the event {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_siteevent_photo', 'siteevent', '{item:$subject} likes the event photo {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_siteeventdocument_document', 'siteeventdocument', '{item:$subject} likes the event document {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_siteevent_video', 'siteevent', '{item:$subject} likes the event video {item:$object}:', 0, 5, 1, 1, 1, 1);

UPDATE `engine4_activity_actiontypes` SET `is_grouped` = '1' WHERE `engine4_activity_actiontypes`.`type` LIKE '%like_%';

