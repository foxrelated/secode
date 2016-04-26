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