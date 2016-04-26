INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sesvideo', 'Advanced Videos & Channels Plugin', 'Advanced Videos & Channels Plugin', '4.8.9p7', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("core_admin_main_plugins_sesvideo", "sesvideo", "SES - Advanced Videos & Channels", "", '{"route":"admin_default","module":"sesvideo","controller":"settings"}', "core_admin_main_plugins", "", 1),
("sesvideo_admin_main_settings", "sesvideo", "Global Settings", "", '{"route":"admin_default","module":"sesvideo","controller":"settings"}', "sesvideo_admin_main", "", 1);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`,  `body`,  `enabled`,  `displayable`,  `attachable`,  `commentable`,  `shareable`, `is_generated`) VALUES
('video_new', 'sesvideo', '{item:$subject} posted a new video:', '1', '5', '1', '3', '1', 0),
('comment_video', 'sesvideo', '{item:$subject} commented on {item:$owner}''s {item:$object:video}: {body:$body}', 1, 1, 1, 1, 1, 0);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('sesvideo_channel_subscription_email', 'sesvideo', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[content][title]');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('video_approved', 'sesvideo', 'Your {item:$object:video} is approved by the administrator and is ready to be viewed.', 0, ''),
('video_disapproved', 'sesvideo', 'Your {item:$object:video} is disapproved by administrator.', 0, '');