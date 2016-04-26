INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('siteevent_userreview_add', 'siteevent', '{item:$subject} rated and wrote a review for {var:$username} in event {item:$object}:', 1, 7, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_siteevent_userreviews`
--

DROP TABLE IF EXISTS `engine4_siteevent_userreviews`;
CREATE TABLE IF NOT EXISTS `engine4_siteevent_userreviews` (
  `userreview_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `viewer_id` int(10) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `rating` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`userreview_id`),
  UNIQUE KEY `event_id` (`event_id`,`user_id`,`viewer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;