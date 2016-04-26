-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('grouppoll', 'Group Polls', 'Group Polls', '4.2.7', 1, 'extra');

-- --------------------------------------------------------


DROP TABLE IF EXISTS `engine4_grouppoll_polls`;
CREATE TABLE IF NOT EXISTS `engine4_grouppoll_polls` (
  `poll_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) unsigned NOT NULL,
  `group_id` int(10) NOT NULL,
  `parent_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) unsigned NOT NULL,
  `group_owner_id` int(10) NOT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `end_settings` tinyint(4) NOT NULL,
  `end_time` datetime NOT NULL,
  `views` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `vote_count` int(11) unsigned NOT NULL DEFAULT '0',
  `search` tinyint(1) NOT NULL DEFAULT '1',
  `gp_auth_vote` tinyint(2) NOT NULL DEFAULT '2',
  `approved` tinyint(2) NOT NULL DEFAULT '1',
  `closed` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`poll_id`),
  KEY `owner_id` (`owner_id`),
  KEY `parent_type` (`parent_type`,`parent_id`),
  KEY `is_closed` (`is_closed`),
  KEY `creation_date` (`creation_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `engine4_grouppoll_options`; 
CREATE TABLE IF NOT EXISTS `engine4_grouppoll_options` ( `poll_option_id` int(11) unsigned NOT NULL auto_increment, `poll_id` int(11) unsigned NOT NULL, `grouppoll_option` text NOT NULL, `votes` smallint(4) unsigned NOT NULL, PRIMARY KEY (`poll_option_id`), KEY `poll_id` (`poll_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ; 

-- --------------------------------------------------------

DROP TABLE IF EXISTS `engine4_grouppoll_votes`; 
CREATE TABLE IF NOT EXISTS `engine4_grouppoll_votes` ( `poll_id` int(11) unsigned NOT NULL, `owner_id` int(11) unsigned NOT NULL, `poll_option_id` int(11) unsigned NOT NULL, `creation_date` datetime NOT NULL, `modified_date` datetime NOT NULL, PRIMARY KEY (`poll_id`,`owner_id`), KEY `poll_option_id` (`poll_option_id`), KEY `owner_id` (`owner_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ; 

-- --------------------------------------------------------

DROP TABLE IF EXISTS `engine4_grouppoll_ratings`; 
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES ('core_admin_main_plugins_grouppoll', 'grouppoll', 'Group Polls', '', '{"route":"admin_default","module":"grouppoll","controller":"settings"}', 'core_admin_main_plugins', '', 999), 
('grouppoll_admin_main_settings', 'grouppoll', 'Global Settings', '', '{"route":"admin_default","module":"grouppoll","controller":"settings"}', 'grouppoll_admin_main', '', 1), 
('grouppoll_admin_main_manage', 'grouppoll', 'View Group Polls', '', '{"route":"admin_default","module":"grouppoll","controller":"manage"}', 'grouppoll_admin_main', '', 2),
('grouppoll_admin_main_level', 'grouppoll', 'Member Level Settings', '', '{"route":"admin_default","module":"grouppoll","controller":"level"}', 'grouppoll_admin_main', '', 3),
('grouppoll_admin_widget_settings', 'grouppoll', 'Widget Settings', '', '{"route":"admin_default","module":"grouppoll","controller":"widgets"}', 'grouppoll_admin_main', '', 5),
('grouppoll_admin_main_faq', 'grouppoll', 'FAQ', '', '{"route":"admin_default","module":"grouppoll","controller":"settings","action":"faq"}', 'grouppoll_admin_main', '', 6); 

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES 
('grouppoll_new', 'group', '{item:$subject} created a new poll:', 1, 2, 2, 1, 1, 1); 

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES 
(1, 'group', 'auth_gpcreate', 5, '["registered", "member", "officer"]'), 
(1, 'group', 'gpcreate', 2, NULL), 
(1, 'grouppoll_poll', 'gp_auth_vote', 5, '["1", "2", "3"]'), 
(2, 'group', 'auth_gpcreate', 5, '["registered", "member", "officer"]'), 
(2, 'group', 'gpcreate', 1, NULL), 
(2, 'grouppoll_poll', 'gp_auth_vote', 5, '["1", "2", "3"]'), 
(3, 'group', 'auth_gpcreate', 5, '["registered", "member", "officer"]'), 
(3, 'group', 'gpcreate', 1, NULL), 
(3, 'grouppoll_poll', 'gp_auth_vote', 5, '["1", "2", "3"]'), 
(4, 'group', 'auth_gpcreate', 5, '["registered", "member", "officer"]'), 
(4, 'group', 'gpcreate', 1, NULL), 
(4, 'grouppoll_poll', 'gp_auth_vote', 5, '["1", "2", "3"]'); 

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES 
('grouppoll.maxoptions', 4), 
('grouppoll.canchangevote', false), 
('grouppoll.comment.widgets', 3), 
('grouppoll.recent.widgets', 3), 
('grouppoll.vote.widgets', 3), 
('grouppoll.like.widgets', 3), 
('grouppoll.view.widgets', 3), 
('grouppoll.isActivate', 1),
('grouppoll.title.turncation', 16);
