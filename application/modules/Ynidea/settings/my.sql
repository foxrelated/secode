INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynidea', 'Ideas Plugin', 'Ideas Plugin', '4.03p1', 1, 'extra') ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_attachments`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_attachments` (
  `attachment_id` int(11) unsigned NOT NULL auto_increment,
  `idea_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL default '0',
  `file_id` int(11) unsigned NOT NULL default '0',
  `title` varchar(256) NOT NULL default '0',
  `description` varchar(256) NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY  (`attachment_id`),
  KEY `fk__ynideal_attachments__ynideal_ideals` (`idea_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_awards`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_awards` (
  `award_id` int(11) unsigned NOT NULL auto_increment,
  `trophy_id` int(11) unsigned NOT NULL,
  `idea_id` int(10) unsigned NOT NULL,
  `award` tinyint(1) unsigned NOT NULL,
  `comment` text,
  PRIMARY KEY  (`award_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_coauthors`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_coauthors` (
  `coauthor_id` int(11) NOT NULL auto_increment,
  `idea_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `creation_date` datetime default NULL,
  PRIMARY KEY  (`coauthor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_faqs`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_faqs` (
  `faq_id` int(11) unsigned NOT NULL auto_increment,
  `status` enum('show','hide') NOT NULL default 'hide',
  `ordering` int(11) unsigned NOT NULL default '0',
  `owner_id` int(11) unsigned NOT NULL default '0',
  `category_id` int(11) unsigned NOT NULL default '0',
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`faq_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_favourites`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_favourites` (
  `favourite_id` int(11) unsigned NOT NULL auto_increment,
  `idea_id` int(11) unsigned NOT NULL default '0',
  `user_id` int(11) unsigned NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`favourite_id`),
  KEY `fk__ynideal_favourites__ynideal_ideals` (`idea_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_follows`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_follows` (
  `follow_id` int(11) unsigned NOT NULL auto_increment,
  `idea_id` int(11) unsigned NOT NULL default '0',
  `user_id` int(11) unsigned NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`follow_id`),
  KEY `fk__ynideal_follows__ynideal_ideals` (`idea_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_helppages`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_helppages` (
  `helppage_id` int(11) unsigned NOT NULL auto_increment,
  `status` enum('show','hide') NOT NULL,
  `ordering` smallint(5) unsigned NOT NULL default '999',
  `category_id` int(11) unsigned NOT NULL default '0',
  `owner_id` int(11) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`helppage_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_ideas`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_ideas` (
  `idea_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL COMMENT 'who created',
  `category_id` INT(11) NOT NULL,
  `version` int(11) unsigned default NULL,
  `version_id` int(11) unsigned default NULL,
  `version_date` datetime default NULL,
  `publish_status` enum('draft','publish','unpublish') NOT NULL default 'draft',
  `ideal_score` double unsigned NOT NULL default '0.5' COMMENT 'ideascore = 2 * (averagevote – 0.5) * totalvoters',
  `vote_ave` double unsigned NOT NULL default '0.5' COMMENT 'averagevote = (2*potential + 2*feasibility + 1*innovation)/5',
  `potential_ave` double unsigned NOT NULL default '0.5',
  `innovation_ave` double unsigned NOT NULL default '0.5',
  `feasibility_ave` double unsigned NOT NULL default '0.5',
  `vote_count` int(11) unsigned NOT NULL default '0',
  `search` tinyint(1) unsigned NOT NULL default '1',
  `slug` varchar(256) NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `decision` enum('selected','realized','') NOT NULL default '',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) NOT NULL default '0',
  `activity_count` int(11) NOT NULL default '0',
  `follow_count` int(11) unsigned NOT NULL default '0',
  `allow_campaign` TINYINT( 1 ) NOT NULL DEFAULT '0',
  `tags` tinytext NOT NULL,
  `award` tinyint(1) NOT NULL default '0',
  `title` varchar(256) NOT NULL,
  `photo_id` int(11) NOT NULL,
  `cost` text NOT NULL,
  `description` text NOT NULL,
  `feasibility` text NOT NULL,
  `reproducible` text NOT NULL,
  `body` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `active_date` datetime NOT NULL,
  PRIMARY KEY  (`idea_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_ideavotes`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_ideavotes` (
  `ideavote_id` int(11) unsigned NOT NULL auto_increment,
  `idea_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `version_id` int(11) unsigned NOT NULL COMMENT 'current version when voted',
  `potential` float unsigned NOT NULL default '0',
  `innovation` float unsigned NOT NULL default '0',
  `feasibility` float unsigned NOT NULL default '0',
  `potential_plus` smallint(5) unsigned NOT NULL default '0',
  `potential_minus` smallint(5) unsigned NOT NULL default '0',
  `feasibility_plus` smallint(5) unsigned NOT NULL default '0',
  `feasibility_minus` smallint(5) unsigned NOT NULL default '0',
  `inovation_plus` smallint(5) unsigned NOT NULL default '0',
  `inovation_minus` smallint(5) unsigned NOT NULL default '0',
  `value` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ideavote_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_judges`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_judges` (
  `judge_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `trophy_id` int(11) default NULL,
  PRIMARY KEY  (`judge_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_nominees`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_nominees` (
  `nominee_id` int(11) unsigned NOT NULL auto_increment,
  `idea_id` int(11) unsigned NOT NULL,
  `trophy_score_ave` int(11) unsigned NOT NULL default '0',
  `trophy_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`nominee_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_reports`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_reports` (
  `report_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `idea_id` int(11) NOT NULL,
  `type` varchar(256) character set utf8 collate utf8_unicode_ci NOT NULL,
  `content` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY  (`report_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_trophies`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_trophies` (
  `trophy_id` int(11) unsigned NOT NULL auto_increment,
  `status` enum('pending','voting','finished') NOT NULL default 'pending',
  `photo_id` int(11) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `allow_campaign` TINYINT( 1 ) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`trophy_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_trophyfavourites`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_trophyfavourites` (
  `trophyfavourite_id` int(11) unsigned NOT NULL auto_increment,
  `trophy_id` int(11) unsigned NOT NULL default '0',
  `user_id` int(11) unsigned NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`trophyfavourite_id`),
  KEY `fk__ynideal_trophyfavourites__ynideal_ideals` (`trophyfavourite_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_trophyvotes`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_trophyvotes` (
  `trophyvote_id` int(11) unsigned NOT NULL auto_increment,
  `trophy_id` int(11) unsigned NOT NULL,
  `idea_id` int(10) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `value` FLOAT( 10, 2 ) UNSIGNED NOT NULL DEFAULT '5.00',
  PRIMARY KEY  (`trophyvote_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_versions`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_versions` (
  `version_id` int(11) unsigned NOT NULL auto_increment,
  `idea_id` int(11) unsigned NOT NULL,
  `idea_version` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(256) NOT NULL,
  `body` text NOT NULL,
  `description` text NOT NULL,
  `vote_ave` double unsigned NOT NULL default '0.5' COMMENT 'averagevote = (2*potential + 2*feasibility + 1*innovation)/5',
  `potential_ave` double unsigned NOT NULL default '0.5',
  `innovation_ave` double unsigned NOT NULL default '0.5',
  `feasibility_ave` double unsigned NOT NULL default '0.5',
  `vote_count` int(11) unsigned NOT NULL default '0',
  `cost` text NOT NULL,
  `feasibility` text NOT NULL,
  `reproducible` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `allow_campaign` TINYINT( 1 ) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`version_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynidea_views`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_views` (
  `view_id` bigint(20) unsigned NOT NULL auto_increment,
  `idea_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`view_id`),
  KEY `fk__ynideal_views__ynideal_ideals` (`idea_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('ynidea_main', 'standard', 'Ideas Main Navigation Menu');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynidea', 'ynidea', 'Ideas', '', '{"route":"admin_default","module":"ynidea","controller":"manage"}', 'core_admin_main_plugins', '', 999),
('ynidea_admin_main_manage', 'ynidea', 'Manage Ideas', '', '{"route":"admin_default","module":"ynidea","controller":"manage"}', 'ynidea_admin_main', '', 14),
('ynidea_admin_main_categories', 'ynidea', 'Categories', '', '{"route":"admin_default","module":"ynidea","controller":"category", "action":"index"}', 'ynidea_admin_main', '', 15),
('ynidea_admin_main_trophy', 'ynidea', 'Manage Trophies', '', '{"route":"admin_default","module":"ynidea","controller":"trophy"}', 'ynidea_admin_main', '', 15),
('ynidea_admin_main_settings', 'ynidea', 'Global Settings', '', '{"route":"admin_default","module":"ynidea","controller":"settings"}', 'ynidea_admin_main', '', 16),
('ynidea_admin_main_level', 'ynidea', 'Member Level Settings', '', '{"route":"admin_default","module":"ynidea","controller":"level"}', 'ynidea_admin_main', '', 17),
('ynidea_admin_main_report', 'ynidea', 'Reports', '', '{"route":"admin_default","module":"ynidea","controller":"report"}', 'ynidea_admin_main', '', 19),
('ynidea_admin_main_helps', 'ynidea', 'Help', '', '{"route":"admin_default","module":"ynidea","controller":"helps"}', 'ynidea_admin_main', '', 20),
('ynidea_admin_main_faqs', 'ynidea', 'FAQs', '', '{"route":"admin_default","module":"ynidea","controller":"faqs"}', 'ynidea_admin_main', '', 21),

('core_main_ynidea', 'ynidea', 'Ideas', '', '{"route":"ynidea_general"}', 'core_main', '', 10),

('ynidea_main_ideas', 'ynidea', 'Browse Ideas', '', '{"route":"ynidea_general"}', 'ynidea_main', '', 1),
('ynidea_main_myideas', 'ynidea', 'My Ideas', 'Ynidea_Plugin_Menus::canMyIdeas', '{"route":"ynidea_myideas"}', 'ynidea_main', '', 3),
('ynidea_main_mytrophies', 'ynidea', 'My Trophies', 'Ynidea_Plugin_Menus::canMyTrophies', '{"route":"ynidea_mytrophies"}', 'ynidea_main', '',4),
('ynidea_main_trophies', 'ynidea', 'Browse Trophies', 'Ynidea_Plugin_Menus::canMyIdeas', '{"route":"ynidea_trophies"}', 'ynidea_main', '', 2),
('ynidea_main_create_idea', 'ynidea', 'Create New Idea', 'Ynidea_Plugin_Menus::canCreateIdea', '{"route":"ynidea_general","action":"create"}', 'ynidea_main', '', 5),
('ynidea_main_create_trophy', 'ynidea', 'Create New Trophy', 'Ynidea_Plugin_Menus::canCreateTrophy', '{"route":"ynidea_trophies","action":"create"}', 'ynidea_main', '', 6),
('ynidea_main_faqs', 'ynidea', 'FAQs', '', '{"route":"ynidea_extended","controller":"faqs"}', 'ynidea_main', '', 7),
('ynidea_main_helps', 'ynidea', 'Help', '', '{"route":"ynidea_extended","controller":"help"}', 'ynidea_main', '', 8)
;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynidea_trophy_new', 'ynidea', '{item:$subject} has created new {item:$object:trophy}.', 1, 7, 0, 1, 1, 1),
('ynidea_trophy_edit', 'ynidea', '{item:$subject} has updated a {item:$object:trophy}.', 1, 7, 0, 1, 1, 1),
('ynidea_trophy_vote', 'ynidea', '{item:$subject} has voted idea in {item:$object:trophy}.', 1, 7, 0, 1, 1, 1),
('ynidea_idea_new', 'ynidea', '{item:$subject} has created new {item:$object:idea}.', 1, 7, 0, 1, 1, 1),
('ynidea_idea_edit', 'ynidea', '{item:$subject} has updated {item:$object:idea}.', 1, 7, 0, 1, 1, 1),
('ynidea_idea_publish', 'ynidea', '{item:$subject} has published {item:$object:idea}.', 1, 7, 0, 1, 1, 1),
('ynidea_idea_vote', 'ynidea', '{item:$subject} has voted {item:$object:idea}.', 1, 7, 0, 1, 1, 1),
('ynidea_idea_award', 'ynidea', '{item:$subject} has given award {item:$object:idea}.', 1, 7, 0, 1, 1, 1),
('ynidea_version_publish', 'ynidea', '{item:$subject} has published a new version of {item:$object:idea}.', 1, 7, 0, 1, 1, 1),
('comment_ynidea_idea', 'ynidea', '{item:$subject} commented on {item:$owner}''s {item:$object:idea}: {body:$body}', 1, 1, 1, 1, 1, 0),
('comment_ynidea_trophy', 'ynidea', '{item:$subject} commented on {item:$owner}''s {item:$object:trophy}: {body:$body}', 1, 1, 1, 1, 1, 0);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynidea_version_publish', 'ynidea', '{item:$subject} has published new version on a idea {item:$object}.', 0, ''),
('ynidea_idea_vote', 'ynidea', '{item:$subject} has voted on a idea {item:$object}.', 0, ''),
('ynidea_idea_award', 'ynidea', '{item:$subject} has given award on a idea {item:$object}.', 0, ''),
('ynidea_judges_enablevoting', 'ynidea', '{item:$subject} has enabled voting a trophy {item:$object}.', 0, ''),
('ynidea_judges_disablevoting', 'ynidea', '{item:$subject} has disabled voting a trophy {item:$object}.', 0, ''),
('ynidea_voter_publishnewversion', 'ynidea', '{item:$subject} has published new version you had voted on previous version {item:$object}.', 0, '');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--
--
-- Ideas
--
-- ALL
-- auth_view, auth_comment, auth_html
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'auth_edit' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'auth_delete' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'auth_vote' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'vote' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'vote' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_idea' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');
  
--
-- Trophies
--
-- ALL
-- auth_view, auth_comment, auth_html
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'auth_vote' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'auth_edit' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
 INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'auth_delete' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'vote' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'vote' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynidea_trophy' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- support categories  
--
-- Table structure for table `engine4_ynidea_categories`
--

CREATE TABLE IF NOT EXISTS `engine4_ynidea_categories` (
`category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`parent_id` int(11) unsigned DEFAULT NULL,
`pleft` int(11) unsigned NOT NULL,
`pright` int(11) unsigned NOT NULL,
`level` int(11) unsigned NOT NULL DEFAULT '0',
`title` varchar(64) NOT NULL,
`order` smallint(6) NOT NULL DEFAULT '0',
`option_id` int(11) NOT NULL,
PRIMARY KEY (`category_id`),
KEY `user_id` (`user_id`),
KEY `parent_id` (`parent_id`),
KEY `pleft` (`pleft`),
KEY `pright` (`pright`),
KEY `level` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_ynidea_categories`
--

INSERT IGNORE INTO `engine4_ynidea_categories` (`category_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`) VALUES
(1, 0, NULL, 1, 4, 0, 'All Categories');  