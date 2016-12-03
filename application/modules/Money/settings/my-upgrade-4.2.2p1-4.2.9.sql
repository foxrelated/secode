ALTER TABLE `engine4_money_money`
	CHANGE COLUMN `money_id` `money_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
	ADD INDEX `money_id` (`money_id`);