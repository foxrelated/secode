DROP TABLE IF EXISTS `engine4_sitelike_mixsettings`;
CREATE TABLE IF NOT EXISTS `engine4_sitelike_mixsettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
-- --------------------------------------------------------
--
-- Dumping data for table `engine4_like_settings`
--
INSERT IGNORE INTO `engine4_sitelike_settings` (`content_type`, `tab1_show`, `tab1_duration`, `tab1_name`, `tab1_entries`, `tab2_show`, `tab2_duration`, `tab2_name`, `tab2_entries`, `tab3_show`, `tab3_duration`, `tab3_name`, `tab3_entries`, `view_layout`) VALUES
( 'sitepagealbum', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'sitepagevideo', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'sitepagepoll', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'sitepagenote', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'sitepagedocument', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'sitepagereview', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'recipe', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1'),
( 'sitepageevent', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1');
-- --------------------------------------------------------
INSERT IGNORE INTO `engine4_sitelike_mixsettings` (`name`, `value`) VALUES
('member', 1),
('blog', 1),
('classified', 1),
('poll', 1),
('album', 1),
('document', 1),
('album_photo', 1),
('video', 1),
('music_playlist', 1),
('group', 1),
('group_photo', 1),
('event', 1),
('event_photo', 1),
('forum_topic', 1),
('list', 1),
('sitepage', 1),
('recipe', 1),
('sitepagenote', 1),
('sitepagevideo', 1),
('sitepagepoll', 1),
('sitepagereview', 1),
('sitepagedocument', 1),
('sitepageevent', 1),
('sitepagealbum', 1);

-- --------------------------------------------------------
--
-- Dumping data for table `engine4_activity_actiontypes`
--
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitelike', 'sitelike', '{item:$subject} likes {item:$object}{var:$content}', 1, 1, 1, 1, 1, 0);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_item', 'sitelike', '{item:$subject} likes {item:$object}:', 1, 5, 1, 1, 1, 1);
