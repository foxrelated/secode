/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Social Publisher
 * @copyright  Copyright 2013-2014 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @author     trunglt
 */

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('socialpublisher', 'Social Publisher', '', '4.03p4', 1, 'extra') ;

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('socialpublisher.activityaction', '{"types":["activity_action"],"title":"socialpublisher_activity_action","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.album', '{"types":["album","advalbum_album"],"title":"socialpublisher_album","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.blog', '{"types":["blog"],"title":"socialpublisher_blog","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.event', '{"types":["event"],"title":"socialpublisher_event","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.forumtopic', '{"types":["forum_topic"],"title":"socialpublisher_forum_topic","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.group', '{"types":["group"],"title":"socialpublisher_group","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.mp3musicalbum', '{"types":["mp3music"],"title":"socialpublisher_mp3music_album","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.musicplaylist', '{"types":["music"],"title":"socialpublisher_music_playlist","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.video', '{"types":["video"],"title":"socialpublisher_video","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.classified', '{"types":["classified"],"title":"socialpublisher_classified","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.contest', '{"types":["contest"],"title":"socialpublisher_contest","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.ynfunraisingcampaign', '{"types":["ynfunraising_campaign"],"title":"socialpublisher_ynfunraising_campaign","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.ynauctionproduct', '{"types":["ynauction_product"],"title":"socialpublisher_ynauction_product","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.groupbuydeal', '{"types":["deal"],"title":"socialpublisher_groupbuydeal","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.socialstore', '{"types":["social_store"],"title":"socialpublisher_socialstore","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.socialproduct', '{"types":["social_product"],"title":"socialpublisher_socialproduct","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.ynwikipage', '{"types":["ynwiki_page"],"title":"socialpublisher_ynwikipage","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.file', '{"types":["file"],"title":"socialpublisher_file","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.ynbusinesspagesbusiness', '{"types":["ynbusinesspages_business"],"title":"socialpublisher_business","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.ynlistingslisting', '{"types":["ynlistings_listing"],"title":"socialpublisher_listing","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.ynjobpostingcompany', '{"types":["ynjobposting_company"],"title":"socialpublisher_company","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.ynjobpostingjob', '{"types":["ynjobposting_job"],"title":"socialpublisher_job","active":"1","providers":["facebook","twitter","linkedin"]}'),
('socialpublisher.ynultimatevideovideo', '{"types":["ynultimatevideo_video"],"title":"socialpublisher_ynultimatevideo_video","active":"1","providers":["facebook","twitter","linkedin"]}');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialpublisher_settings`
--

CREATE TABLE `engine4_socialpublisher_settings` (
  `setting_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `type` varchar(128) NOT NULL,
  `option` tinyint(1) NOT NULL default '0',
  `privacy` int(1) default '7',
  `providers` text NOT NULL,
  PRIMARY KEY  (`setting_id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
