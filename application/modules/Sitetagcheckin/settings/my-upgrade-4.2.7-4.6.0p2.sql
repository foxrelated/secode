INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ('sitetagcheckin_group_tagged', 'sitetagcheckin', '{item:$subject} mentioned your group with a {item:$object:$label}.', '0', '', '1');

INSERT IGNORE INTO `engine4_sitetagcheckin_contents` (`module`, `resource_type`, `resource_id`, `value`, `default`, `enabled`) VALUES
("sitegroup", "sitegroup_group", "group_id", 1, 1, 1),
("sitegroupalbum", "sitegroup_album", "album_id", 1, 1, 1),
("sitegroupnote", "sitegroupnote_note", "note_id", 1, 1, 1),
("sitegroupevent", "sitegroupevent_event", "event_id", 1, 1, 1),
("sitegroupmusic", "sitegroupmusic_playlist", "playlist_id", 1, 1, 1),
("sitegroupdiscussion", "sitegroup_topic", "topic_id", 1, 1, 1),
("sitegroupvideo", "sitegroupvideo_video", "video_id", 1, 1, 1),
("sitegrouppoll", "sitegrouppoll_poll", "poll_id", 1, 1, 1),
("sitegroupdocument", "sitegroupdocument_document", "document_id", 1, 1, 1),
("sitegroupreview", "sitegroupreview_review", "review_id", 1, 1, 1);