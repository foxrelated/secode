INSERT IGNORE INTO `engine4_sitelike_settings` (`content_type`, `tab1_show`, `tab1_duration`, `tab1_name`, `tab1_entries`, `tab2_show`, `tab2_duration`, `tab2_name`, `tab2_entries`, `tab3_show`,
`tab3_duration`, `tab3_name`, `tab3_entries`, `view_layout`) VALUES
('sitebusiness_business', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitebusiness_album', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitebusinessvideo_video', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitebusinesspoll_poll', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitebusinessnote_note', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitebusinessreview_review', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitebusinessmusic_playlist', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitebusiness_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitebusinessevent_event', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitebusinessnote_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1');


-- --------------------------------------------------------
INSERT IGNORE INTO `engine4_sitelike_mixsettings` ( `module`, `resource_type`, `resource_id`, `item_title`, `title_items`, `value`, `default`, `enabled`) VALUES
( 'sitebusiness', 'sitebusiness_business', 'business_id', 'Businesses', 'Business', 1, 1, 1),
( 'sitebusinessalbum', 'sitebusiness_photo', 'photo_id', 'Business Album Photos', 'Business Album Photo', 1, 1, 1),
( 'sitebusinessalbum', 'sitebusiness_album', 'album_id', 'Business Albums', 'Business Album', 1, 1, 1),
( 'sitebusinessdocument', 'sitebusinessdocument_document', 'document_id', 'Business Documents', 'Business Document', 1, 1, 1),
( 'sitebusinessevent', 'sitebusinessevent_event', 'event_id', 'Business Events', 'Business Event', 1, 1, 1),
( 'sitebusinessmusic', 'sitebusinessmusic_playlist', 'playlist_id', 'Business Music', 'Business Music', 1, 1, 1),
( 'sitebusinessnote', 'sitebusinessnote_photo', 'photo_id', 'Business Note Photos', 'Business Note Photo', 1, 1, 1),
( 'sitebusinessnote', 'sitebusinessnote_note', 'note_id', 'Business Notes', 'Business Note', 1, 1, 1),
( 'sitebusinesspoll', 'sitebusinesspoll_poll', 'poll_id', 'Business Polls', 'Business Poll', 1, 1, 1),
( 'sitebusinessreview', 'sitebusinessreview_review', 'review_id', 'Business Reviews', 'Business Review', 1, 1, 1),
( 'sitebusinessvideo', 'sitebusinessvideo_video', 'video_id', 'Business Videos', 'Business Video', 1, 1, 1);


-- --------------------------------------------------------

-- SITEBUSINESS
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitebusiness_business', 'sitebusiness', '{item:$subject} likes the businesses {item:$object}:', 1, 5,
1, 1, 1, 1);

-- SITEBUSINESS ALBUM PHOTO
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitebusiness_photo', 'sitebusinessalbum', '{item:$subject} likes the business album photo
{item:$object}:', 1, 5, 1, 1, 1, 1);

-- SITEBUSINESS ALBUM
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitebusiness_album', 'sitebusinessalbum', '{item:$subject} likes the business album {item:$object}:',
1, 5, 1, 1, 1, 1);

-- SITEBUSINESS DOCUMENT
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitebusinessdocument_document', 'sitebusinessdocument', '{item:$subject} likes the business document
{item:$object}:', 1, 5, 1, 1, 1, 1);

-- SITEBUSINESS EVENT
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitebusinessevent_event', 'sitebusinessevent', '{item:$subject} likes the business event
{item:$object}:', 1, 5, 1, 1, 1, 1);

-- SITEBUSINESS MUSIC
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitebusinessmusic_playlist', 'sitebusinessmusic', '{item:$subject} likes the business music {item:$object}:', 1, 5, 1, 1, 1, 0);

-- SITEBUSINESS NOTE PHOTO
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitebusinessnote_photo', 'sitebusinessnote', '{item:$subject} likes the business note photo
{item:$object}:', 1, 5, 1, 1, 1, 1);

-- SITEBUSINESS NOTE
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitebusinessnote_note', 'sitebusinessnote', '{item:$subject} likes the business note {item:$object}:',
1, 5, 1, 1, 1, 1);


-- SITEBUSINESS POLL
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitebusinesspoll_poll', 'sitebusinesspoll', '{item:$subject} likes the business poll {item:$object}:',
1, 5, 1, 1, 1, 1);


-- SITEBUSINESS REVIEW
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitebusinessreview_review', 'sitebusinessreview', '{item:$subject} likes the business review
{item:$object}:', 1, 5, 1, 1, 1, 1);

-- SITEBUSINESS VIDEO
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitebusinessvideo_video', 'sitebusinessvideo', '{item:$subject} likes the business video
{item:$object}:', 1, 5, 1, 1, 1, 1);

-- Recipe
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_recipe', 'recipe', '{item:$subject} likes the recipe {item:$object}:', 1, 5, 1, 1, 1, 1);

UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_album' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_album_photo' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_document' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_user' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_blog' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_classified' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_group' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_group_photo' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_event' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_event_photo' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_forum_topic' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_playlist' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_poll' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_video' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_sitepage_album' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_sitepage_page' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_sitepage_photo' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_sitepagedocument_document' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_sitepageevent_event' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_sitepageevent_photo' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_sitepagemusic_playlist' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_sitepagenote_note' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_sitepagenote_photo' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_sitepagepoll_poll' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_sitepagereview_review' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_sitepagevideo_video' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_list_listing' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_list_photo' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_recipe' LIMIT 1 ;
UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE
`engine4_activity_actiontypes`.`type` = 'like_recipe_photo' LIMIT 1 ;