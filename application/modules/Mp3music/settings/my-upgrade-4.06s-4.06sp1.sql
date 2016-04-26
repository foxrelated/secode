UPDATE `engine4_core_modules` SET `version` = '4.06sp1' WHERE `engine4_core_modules`.`name` = 'mp3music' LIMIT 1 ;
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album_song' as `type`,
    'play' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels`;
  
INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'mp3music_album_song' as `resource_type`,
    `song_id` as `resource_id`,
    'play' as `action`,
    'everyone' as `role`,
    0 as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_mp3music_album_songs`;