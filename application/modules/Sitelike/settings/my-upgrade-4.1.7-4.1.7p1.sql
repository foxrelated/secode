UPDATE `engine4_core_likes` SET `resource_type` = 'user' WHERE `engine4_core_likes`.`resource_type` = 'member' ;


UPDATE `engine4_sitelike_settings` SET `content_type` = 'user' WHERE `engine4_sitelike_settings`.`content_type` = 'member' LIMIT 1 ;

UPDATE `engine4_sitelike_mixsettings` SET `name` = 'user' WHERE `engine4_sitelike_mixsettings`.`name` = 'member' LIMIT 1 ;