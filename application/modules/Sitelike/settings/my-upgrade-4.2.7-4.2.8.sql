INSERT IGNORE INTO `engine4_sitelike_settings` (`content_type`, `tab1_show`, `tab1_duration`, `tab1_name`, `tab1_entries`, `tab2_show`, `tab2_duration`, `tab2_name`, `tab2_entries`, `tab3_show`, `tab3_duration`, `tab3_name`, `tab3_entries`, `view_layout`) VALUES
('sitereview_listing', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitereview_photo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
('sitereview_video', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1');

INSERT IGNORE INTO `engine4_sitelike_mixsettings` ( `module`, `resource_type`, `resource_id`, `item_title`,
`title_items`, `value`, `default`, `enabled`) VALUES
('sitereview', 'sitereview_listing', 'listing_id', 'Listings', 'Listing', 1, 1, 1),
('sitereview', 'sitereview_photo', 'photo_id', 'Listing Photos', 'Listing Photos', 1, 1, 1),
('sitereview', 'sitereview_video', 'video_id', 'Listing Videos', 'Listing Video', 1, 1, 1);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`,
`attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitereview_listing', 'sitereview', '{item:$subject} likes the {var:$listing_name} {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitereview_photo', 'sitereview', '{item:$subject} likes the {var:$listing_name} photo {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitereview_video', 'sitereview', '{item:$subject} likes the {var:$listing_name} video {item:$object}:', 0, 5, 1, 1, 1, 1);