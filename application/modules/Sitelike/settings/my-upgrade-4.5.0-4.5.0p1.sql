ALTER TABLE `engine4_sitelike_mixsettings` DROP INDEX `resource_type`;
ALTER TABLE `engine4_sitelike_mixsettings` ADD UNIQUE (`module` ,`resource_type`);


INSERT IGNORE INTO `engine4_sitelike_mixsettings` ( `module`, `resource_type`, `resource_id`, `item_title`, `title_items`, `value`, `default`, `enabled`) VALUES
('ynevent', 'event', 'event_id', 'Events', 'Event', 1, 0, 1),
('ynevent_photo', 'event_photo', 'photo_id', 'Event Photos', 'Event Photo', 1, 0, 1),
('advgroup', 'group', 'group_id', 'Groups', 'Group', 1, 0, 1),
('advgroup_photo', 'group_photo', 'photo_id', 'Group Photos', 'Group Photo', 1, 0, 1),
('ynblog', 'blog', 'blog_id', 'Blogs', 'Blog', 1, 0, 1),
('ynforum', 'forum_topic', 'forum_id', 'Forums', 'Forum', 1, 0, 1),
('ynvideo', 'video', 'video_id', 'Videos', 'Video', 1, 0, 1);