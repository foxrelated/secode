-- --------------------------------------------------------
--
-- Dumping data for table `engine4_like_settings`
--
INSERT IGNORE INTO `engine4_sitelike_settings` (`content_type`, `tab1_show`, `tab1_duration`, `tab1_name`, `tab1_entries`, `tab2_show`, `tab2_duration`, `tab2_name`, `tab2_entries`, `tab3_show`, `tab3_duration`, `tab3_name`, `tab3_entries`, `view_layout`) VALUES
( 'sitepagemusic', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'sitepage_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1');
-- --------------------------------------------------------
INSERT IGNORE INTO `engine4_sitelike_mixsettings` (`name`, `value`) VALUES
('sitepagemusic', 1),
('sitepage_photo', 1);



DELETE FROM `engine4_activity_actiontypes` WHERE `engine4_activity_actiontypes`.`type` = 'like_sitelike' LIMIT 1;

DELETE FROM `engine4_activity_actiontypes` WHERE `engine4_activity_actiontypes`.`type` = 'like_item' LIMIT 1;

-- LISTING
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_list_listing', 'list', '{item:$subject} likes the listing {item:$object}:', 1, 5, 1, 1, 1, 0);

-- SITEPAGE
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitepage_page', 'sitepage', '{item:$subject} likes  the page {item:$object}:', 1, 5, 1, 1, 1, 0);


-- ALBUM
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_album', 'album', '{item:$subject} likes the album {item:$object}:', 1, 5, 1, 1, 1, 0);

-- ALBUM PHOTO
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_album_photo', 'album', '{item:$subject} likes the photo {item:$object}:', 1, 5, 1, 1, 1, 0);

-- BLOG
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_blog', 'blog', '{item:$subject} likes the blog {item:$object}:', 1, 5, 1, 1, 1, 0);

-- CLASSIFIED
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_classified', 'classified', '{item:$subject} likes the classified listing {item:$object}:', 1, 5, 1, 1, 1, 0);


-- PLAYLIST
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_music_playlist', 'music', '{item:$subject} likes the playlist {item:$object}:', 1, 5, 1, 1, 1, 0);

-- VIDEO
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_video', 'video', '{item:$subject} likes the video {item:$object}:', 1, 5, 1, 1, 1, 0);
-- EVENT
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_event', 'event', '{item:$subject} likes the event {item:$object}:', 1, 5, 1, 1, 1, 0);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_event_photo', 'event', '{item:$subject} likes the events’  photo {item:$object}:', 1, 5, 1, 1, 1, 0);


-- GROUP
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_group', 'group', '{item:$subject} likes the group {item:$object}:', 1, 5, 1, 1, 1, 0);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_group_photo', 'group', '{item:$subject} likes the groups’ photo {item:$object}:', 1, 5, 1, 1, 1, 0);

-- DOCUMENT

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_document', 'document', '{item:$subject} likes the document {item:$object}:', 1, 5, 1, 1, 1, 0);

-- FORUM TOPIC
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_forum_topic', 'forum', '{item:$subject} likes the forum topic {item:$object}:', 1, 5, 1, 1, 1, 0);




-- PAGE ALBUM
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitepage_album', 'sitepagealbum', '{item:$subject} likes the page album {item:$object}:', 1, 5, 1,1 , 1, 0);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitepage_photo', 'sitepagealbum', '{item:$subject} likes the page  photo {item:$object}:', 1, 5, 1, 1, 1, 0);

