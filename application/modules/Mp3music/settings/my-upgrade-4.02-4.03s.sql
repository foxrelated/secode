CREATE TABLE IF NOT EXISTS `engine4_mp3music_artists` (
  `artist_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `photo_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`artist_id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('mp3music_admin_main_import', 'mp3music', 'Import Database', '', '{"route":"admin_default","module":"mp3-music","controller":"import"}', 'mp3music_admin_main', '', 13);


INSERT IGNORE INTO `engine4_mp3music_artists` (`artist_id`, `title`, `photo_id`) VALUES
(1, 'Black Eyed Peas', 0),
(2, 'Aaron Carter', 0),
(3, 'Avril Lavigne', 0),
(4, 'Celine Dion', 0);

UPDATE `engine4_core_modules` SET `version` = '4.04sp1' WHERE `engine4_core_modules`.`name` = 'mp3music' LIMIT 1 ;