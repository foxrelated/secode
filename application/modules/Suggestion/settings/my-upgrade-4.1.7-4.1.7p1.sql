INSERT IGNORE INTO `engine4_user_signup` (`class`, `order`, `enable`) VALUES
('Suggestion_Plugin_Signup_Invite', 5, 1);

ALTER TABLE  `engine4_suggestion_rejected` ADD INDEX (  `entity` );
ALTER TABLE  `engine4_suggestion_suggestions` ADD INDEX (  `owner_id` );
ALTER TABLE  `engine4_suggestion_suggestions` ADD INDEX (  `entity` );