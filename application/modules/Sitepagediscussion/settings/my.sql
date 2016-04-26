/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagediscussion
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitepagediscussion', 'Page Discussions', 'Sitepagediscussion', '4.2.3', 1, 'extra') ;

-- ---------------------------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepage_discussion_reply', 'sitepagediscussion', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::page topic} you posted on.', 0, ''),
('sitepage_discussion_response', 'sitepagediscussion', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::page topic} you created.', 0, '');

-- ---------------------------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('sitepage_topic_create', 'sitepagediscussion', '{item:$subject} posted a new discussion topic in the page {item:$object}:', 1, 3, 2, 1, 1, 1),
('sitepage_topic_reply', 'sitepagediscussion', '{item:$subject} replied to a discussion in the page {item:$object}:', 1, 3, 2, 1, 1, 1);

-- ---------------------------------------------------------------------------

--
-- Table structure for table `engine4_sitepage_posts`
--

DROP TABLE IF EXISTS `engine4_sitepage_posts`;
CREATE TABLE `engine4_sitepage_posts` (
  `post_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) unsigned NOT NULL,
  `page_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `topic_id` (`topic_id`),
  KEY `page_id` (`page_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- ---------------------------------------------------------------------------

--
-- Table structure for table `engine4_sitepage_topics`
--

DROP TABLE IF EXISTS `engine4_sitepage_topics`;
CREATE TABLE `engine4_sitepage_topics` (
  `topic_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `sticky` tinyint(1) NOT NULL DEFAULT '0',
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `post_count` int(11) unsigned NOT NULL DEFAULT '0',
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `lastpost_id` int(11) unsigned NOT NULL DEFAULT '0',
  `lastposter_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`topic_id`),
  KEY `page_id` (`page_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- ---------------------------------------------------------------------------

--
-- Table structure for table `engine4_sitepage_topicwatches`
--

DROP TABLE IF EXISTS `engine4_sitepage_topicwatches`;
CREATE TABLE IF NOT EXISTS `engine4_sitepage_topicwatches` (
  `resource_id` int(10) unsigned NOT NULL,
  `topic_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `watch` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `page_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`resource_id`,`topic_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
