/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: my.sql 2010-09-13 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
-- --------------------------------------------------------
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('dbbackup', 'Backup and Restore Plugin', 'Backup and Restore Plugin', '4.8.10', 1, 'extra');

-- --------------------------------------------------------

DROP TABLE IF EXISTS `engine4_dbbackup_backuplogs`;
CREATE TABLE IF NOT EXISTS `engine4_dbbackup_backuplogs` ( `backuplog_id` int(11) NOT NULL AUTO_INCREMENT, `type` varchar(63) NOT NULL, `method` varchar(63) NOT NULL, `destination_name` varchar(63) NOT NULL, `destination_method` varchar(63) NOT NULL, `filename` varchar(63) NOT NULL, `size` varchar(63) DEFAULT "0", `start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', `end_time` datetime DEFAULT NULL, `status` varchar(63) DEFAULT NULL, PRIMARY KEY (`backuplog_id`) ) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1;