UPDATE `engine4_core_modules` SET `version` = '4.05p7' WHERE `name` = 'ynevent';

-- Alter action of allow table to contain 'ynultimatevideo_video'
ALTER TABLE `engine4_authorization_allow` MODIFY `action` VARCHAR(64);
ALTER TABLE `engine4_authorization_permissions` MODIFY `name` VARCHAR(64);

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'event' as `type`,
    'ynultimatevideo_video' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
