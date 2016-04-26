
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Seo
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */


-- --------------------------------------------------------

--
-- Table structure for table `engine4_seo_seos`
--

DROP TABLE IF EXISTS `engine4_seo_pages`;
CREATE TABLE IF NOT EXISTS `engine4_seo_pages` (
  `page_id` int(11) unsigned NOT NULL auto_increment,
  
  `page_module` varchar(32) NOT NULL,
  `page_controller` varchar(32) NOT NULL,
  `page_action` varchar(32) NOT NULL,
  
  `title` varchar(255),
  `description` TEXT,
  `keywords` TEXT,
  `extra_headers` TEXT,
  
  
  `title_mode` varchar(16) NOT NULL default 'default',
  `description_mode` varchar(16) NOT NULL default 'default',
  `keywords_mode` varchar(16) NOT NULL default 'default',
  
  `enabled` tinyint(1) NOT NULL default '1',
  
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,  
  
  PRIMARY KEY  (`page_id`),
  UNIQUE KEY `mca` (`page_module`,`page_controller`,`page_action`),
  KEY `page_module` (`page_module`),
  KEY `page_controller` (`page_controller`),
  KEY `page_action` (`page_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- END
-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Table structure for table `engine4_seo_channels`
--

DROP TABLE IF EXISTS `engine4_seo_channels`;
CREATE TABLE IF NOT EXISTS `engine4_seo_channels` (
  `name` varchar(64) NOT NULL,

  `plugin` varchar(128),

  `title` varchar(128) NOT NULL,
  `description` TEXT,
  
  `changefreq` varchar(16) NOT NULL DEFAULT 'always',
  `priority` decimal(2,1) NOT NULL DEFAULT '0.5',
  `maxitems` INT(11) NOT NULL DEFAULT '0',

  `item_type` varchar(64) DEFAULT NULL,
  `item_order` varchar(128) DEFAULT NULL,
  `custom` tinyint(1) NOT NULL DEFAULT '0',
  
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `order` smallint(3) NOT NULL DEFAULT '999',
  
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_seo_channels`
--
-- START

INSERT IGNORE INTO `engine4_seo_channels` (`order`, `name`, `title`, `description`,`enabled`, `changefreq`,`priority`) VALUES
(0,'core','Core','List main urls of your site.',1,'always',1.0),
(5,'page','Custom Pages','List your custom pages built via Admin - Layout Editor.',1,'daily',0.9),
(10,'user','Users','Latest member profile urls of your site.',1,'hourly',0.8),
(12,'album','Albums','Latest albums posted by your members.',1,'daily',0.5),
(13,'article','Articles','Latest articles posted by your members.',1,'daily',0.5),
(14,'blog','Blogs','Latest blogs posted by your members.',1,'daily',0.5),
(15,'classified','Classifieds','Latest classifieds posted by your members.',1,'daily',0.5),
(16,'cmspage','Pages','Latest pages posted by your members.',1,'daily',0.5),
(17,'event','Events','Latest events posted by your members.',1,'daily',0.5),
(18,'forum','Forums','Latest forums posted by your members.',1,'daily',0.5),
(19,'gmap','GMaps','Latest gmaps posted by your members.',1,'daily',0.5),
(20,'group','Groups','Latest groups posted by your members.',1,'daily',0.5),
(21,'listing','Listings','Latest listings posted by your members.',1,'daily',0.5),
(22,'music','Music Playlists','Latest music playlists posted by your members.',1,'daily',0.5),
(23,'pet','Pets','Latest pets posted by your members.',1,'daily',0.5),
(24,'poll','Polls','Latest polls posted by your members.',1,'daily',0.5),
(25,'video','Videos','Latest videos posted by your members.',1,'daily',0.5),
(26,'rssfeed','RSS Feeds','Latest RSS feeds posted by your members.',1,'daily',0.5),
(27,'education','Educations','Latest educations posted by your members.',1,'always',0.5),
(28,'employment','Employments','Latest employments posted by your members.',1,'daily',0.5),
(29,'link','Links','Latest links posted by your members.',1,'hourly',0.5),
(30,'review','Reviews','Latest reviews posted by your members.',1,'daily',0.5);

-- END
-- --------------------------------------------------------
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--
DELETE FROM `engine4_core_menuitems` WHERE module = 'seo';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_seo', 'seo', 'SEO Sitemaps', '', '{"route":"admin_default","module":"seo","controller":"settings"}', 'core_admin_main_plugins', '', 100),

('seo_admin_main_pages', 'seo', 'Manage Pages', '', '{"route":"admin_default","module":"seo","controller":"pages"}', 'seo_admin_main', '', 1),
('seo_admin_main_settings', 'seo', 'Global Settings', '', '{"route":"admin_default","module":"seo","controller":"settings"}', 'seo_admin_main', '', 2),
('seo_admin_main_channels', 'seo', 'XML Sitemap', '', '{"route":"admin_default","module":"seo","controller":"channels"}', 'seo_admin_main', '', 3);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_tasks`
--
DELETE FROM `engine4_core_tasks` WHERE module = 'seo';

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`) VALUES
('Sitemap Build Submit', 'seo', 'Seo_Plugin_Task_Sitemap_Submit', 604800);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('seo', 'SEO Sitemap', 'This plugin provides Search Engine Optimization (SEO) for Title, Description, Keywords as well as customized additional headers. Including Sitemap file building and update notification to Google, Bing, Ask, Yahoo etc..', '4.0.6', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--

DELETE FROM `engine4_core_settings` WHERE name LIKE 'seo.%';

INSERT IGNORE INTO `engine4_core_settings` (`name` , `value`) VALUES
('seo.license', ''),
('seo.sitemapfilename', 'sitemap.xml'),
('seo.gzipsitemap', '1'),

('seo.sitemaplastupdate', '0'),
('seo.sitemaplastsubmit', '0'),

('seo.notifyservices', 'google,bing,ask'),
('seo.notifyyahooappid', ''),

('seo.enableheaders', 'title,description,keywords,extra'),

('seo.titlemode', 'prepend'),
('seo.descriptionmode', 'override'),
('seo.keywordsmode', 'append');

