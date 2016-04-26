UPDATE `engine4_core_modules` SET `version` = '4.09' where 'name' = 'ynblog';
ALTER TABLE  `engine4_blog_blogs` ADD  `photo_id` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `owner_id` ;