DROP TABLE `engine4_sitelike_mixsettings`;
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
  UNIQUE KEY `resource_type` (`resource_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_sitelike_mixsettings`
--

INSERT IGNORE INTO `engine4_sitelike_mixsettings` ( `module`, `resource_type`, `resource_id`, `item_title`, `title_items`, `value`, `default`, `enabled`) VALUES
( 'user', 'user', 'user_id', 'Members', 'Member', 1, 1, 1),
('blog', 'blog', 'blog_id', 'Blogs', 'Blog', 1, 1, 1),
( 'classified', 'classified', 'classified_id', 'Classifieds', 'Classified', 1, 1, 1),
( 'poll', 'poll', 'poll_id', 'Polls', 'Poll', 1, 1, 1),
( 'document', 'document', 'document_id', 'Documents', 'Document', 1, 1, 1),
( 'album', 'album', 'album_id', 'Albums', 'Album', 1, 1, 1),
( 'album', 'album_photo', 'photo_id', 'Album Photos', 'Album Photo', 1, 1, 1),
( 'video', 'video', 'video_id', 'Videos', 'Video', 1, 1, 1),
( 'music', 'music_playlist', 'playlist_id', 'Music', 'Music', 1, 1, 1),
( 'group', 'group', 'group_id', 'Groups', 'Group', 1, 1, 1),
( 'group', 'group_photo', 'photo_id', 'Group Photos', 'Group Photo', 1, 1, 1),
( 'event', 'event', 'event_id', 'Events', 'Event', 1, 1, 1),
( 'event', 'event_photo', 'event_id', 'Event Photos', 'Event Photo', 1, 1, 1),
( 'forum', 'forum_topic', 'forum_id', 'Forums', 'Forum', 1, 1, 1),
( 'list', 'list_listing', 'listing_id', 'Listings', 'Listing', 1, 1, 1),
( 'recipe', 'recipe', 'recipe_id', 'Recipes', 'Recipe', 1, 1, 1),
( 'sitepage', 'sitepage_page', 'page_id', 'Pages', 'Page', 1, 1, 1),
( 'sitepagenote', 'sitepagenote_note', 'note_id', 'Page Notes', 'Page Note', 1, 1, 1),
( 'sitepagevideo', 'sitepagevideo_video', 'video_id', 'Page Videos', 'Page Video', 1, 1, 1),
( 'sitepagepoll', 'sitepagepoll_poll', 'poll_id', 'Page Polls', 'Page Poll', 1, 1, 1),
( 'sitepagereview', 'sitepagereview_review', 'review_id', 'Page Reviews', 'Page Review', 1, 1, 1),
( 'sitepagedocument', 'sitepagedocument_document', 'document_id', 'Page Documents', 'Page Document', 1, 1, 1),
( 'sitepageevent', 'sitepageevent_event', 'event_id', 'Page Events', 'Page Event', 1, 1, 1),
( 'sitepagemusic', 'sitepagemusic_playlist', 'playlist_id', 'Page Music', 'Page Music', 1, 1, 1),
( 'sitepagealbum', 'sitepage_photo', 'photo_id', 'Page Album Photos', 'Page Album Photo', 1, 1, 1),
( 'sitepagealbum', 'sitepage_album', 'album_id', 'Page Albums', 'Page Album', 1, 1, 1),
( 'sitepagenote', 'sitepagenote_photo', 'photo_id', 'Page Note Photos', 'Page Note Photo', 1, 1, 1),
( 'list', 'list_photo', 'photo_id', 'Listing Photos', 'Listing Photo', 1, 1, 1),
( 'recipe', 'recipe_photo', 'photo_id', 'Recipe Photos', 'Recipe Photo', 1, 1, 1);


INSERT IGNORE INTO `engine4_sitelike_settings` (`content_type`, `tab1_show`, `tab1_duration`, `tab1_name`, `tab1_entries`, `tab2_show`, `tab2_duration`, `tab2_name`, `tab2_entries`, `tab3_show`, `tab3_duration`, `tab3_name`, `tab3_entries`, `view_layout`) VALUES
('sitepagenote_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('list_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('recipe_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1');


UPDATE  `engine4_sitelike_settings` SET  `content_type` =  'forum_topic' WHERE  `engine4_sitelike_settings`.`content_type` =  'forum' LIMIT 1 ;
UPDATE  `engine4_sitelike_settings` SET  `content_type` =  'sitepage_page' WHERE  `engine4_sitelike_settings`.`content_type` =  'sitepage' LIMIT 1 ;
UPDATE  `engine4_sitelike_settings` SET  `content_type` =  'sitepagenote_note' WHERE  `engine4_sitelike_settings`.`content_type` =  'sitepagenote' LIMIT 1 ;
UPDATE  `engine4_sitelike_settings` SET  `content_type` =  'sitepagevideo_video' WHERE  `engine4_sitelike_settings`.`content_type` =  'sitepagevideo' LIMIT 1 ;
UPDATE  `engine4_sitelike_settings` SET  `content_type` =  'sitepagepoll_poll' WHERE  `engine4_sitelike_settings`.`content_type` =  'sitepagepoll' LIMIT 1 ;
UPDATE  `engine4_sitelike_settings` SET  `content_type` =  'sitepagereview_review' WHERE  `engine4_sitelike_settings`.`content_type` =  'sitepagereview' LIMIT 1 ;
UPDATE  `engine4_sitelike_settings` SET  `content_type` =  'sitepagedocument_document' WHERE  `engine4_sitelike_settings`.`content_type` =  'sitepagedocument' LIMIT 1 ;
UPDATE  `engine4_sitelike_settings` SET  `content_type` =  'sitepageevent_event' WHERE  `engine4_sitelike_settings`.`content_type` =  'sitepageevent' LIMIT 1 ;
UPDATE  `engine4_sitelike_settings` SET  `content_type` =  'sitepagemusic_playlist' WHERE  `engine4_sitelike_settings`.`content_type` =  'sitepagemusic' LIMIT 1 ;
UPDATE  `engine4_sitelike_settings` SET  `content_type` =  'sitepage_album' WHERE  `engine4_sitelike_settings`.`content_type` =  'sitepagealbum' LIMIT 1 ;
UPDATE  `engine4_sitelike_settings` SET  `content_type` =  'list_listing' WHERE  `engine4_sitelike_settings`.`content_type` =  'list' LIMIT 1 ;


UPDATE  `engine4_core_menuitems` SET  `order` =  '998' WHERE  `engine4_core_menuitems`.`name` = 'sitelike_admin_faqs' LIMIT 1 ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
( 'sitelike_admin_manage_modules', 'sitelike', 'Manage Modules', '', '{"route":"admin_default","module":"sitelike","controller":"manage","action":"index"}', 'sitelike_admin_main', '', 1, 0, 8);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_user', 'user', '{item:$subject} likes {item:$object}:', 1, 5, 1, 1, 1, 0);
