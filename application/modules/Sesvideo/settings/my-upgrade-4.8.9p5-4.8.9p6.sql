INSERT IGNORE INTO `engine4_authorization_permissions`
	  SELECT
    level_id as `level_id`,
    "video" as `type`,
    "video_approve_type" as `name`,
    5 as `value`,
    '["youtube","youtubePlaylist","vimeo","dailymotion","url","embedcode"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    "video" as `type`,
    "video_approve" as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN("user");

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    "video" as `type`,
    "video_approve" as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN("public");