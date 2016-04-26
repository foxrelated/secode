ALTER TABLE `engine4_sesmusic_albums` ADD `offtheday` TINYINT( 1 ) NOT NULL;
ALTER TABLE `engine4_sesmusic_albums` ADD `starttime` DATE NOT NULL;
ALTER TABLE `engine4_sesmusic_albums` ADD `endtime` DATE NOT NULL;

ALTER TABLE `engine4_sesmusic_albumsongs` ADD `offtheday` TINYINT( 1 ) NOT NULL;
ALTER TABLE `engine4_sesmusic_albumsongs` ADD `starttime` DATE NOT NULL;
ALTER TABLE `engine4_sesmusic_albumsongs` ADD `endtime` DATE NOT NULL;

ALTER TABLE `engine4_sesmusic_playlists` ADD `offtheday` TINYINT( 1 ) NOT NULL;
ALTER TABLE `engine4_sesmusic_playlists` ADD `starttime` DATE NOT NULL;
ALTER TABLE `engine4_sesmusic_playlists` ADD `endtime` DATE NOT NULL;

ALTER TABLE `engine4_sesmusic_artists` ADD `offtheday` TINYINT( 1 ) NOT NULL;
ALTER TABLE `engine4_sesmusic_artists` ADD `starttime` DATE NOT NULL;
ALTER TABLE `engine4_sesmusic_artists` ADD `endtime` DATE NOT NULL;