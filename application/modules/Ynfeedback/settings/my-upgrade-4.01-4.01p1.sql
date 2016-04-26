UPDATE `engine4_core_modules` SET `version` = '4.01p1' WHERE `engine4_core_modules`.`name` = 'ynfeedback' LIMIT 1 ;

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'max_feedback' as `name`,
	3 as `value`,
	0 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');