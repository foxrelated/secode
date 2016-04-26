UPDATE `engine4_core_modules` SET `version` = '4.04sp1' WHERE `engine4_core_modules`.`name` = 'mp3music' LIMIT 1 ;
ALTER TABLE `engine4_mp3music_albums` ADD `type` TINYINT( 1 ) NOT NULL DEFAULT '2' AFTER `modified_date`;
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('mp3music_track_new', 'mp3music', '{item:$subject} has uploaded a track: {item:$object}', '1', '5', '1', '3', '1', 1);
