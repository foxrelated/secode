/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitemailtemplates', 'Sitemailtemplates', 'Sitemailtemplates', '4.8.10p1', 1, 'extra') ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitemailtemplates_templates`
--

DROP TABLE IF EXISTS `engine4_sitemailtemplates_templates`;
CREATE TABLE `engine4_sitemailtemplates_templates` (
  `template_id` int(11) NOT NULL auto_increment,
  `testemail_admin` varchar(255) NOT NULL,
  `template_title` varchar(128) NOT NULL,
  `show_title` tinyint(1) NOT NULL default '1',
  `site_title` varchar(128) NOT NULL,
  `sitetitle_fontsize` tinyint(2) NOT NULL ,
  `sitetitle_fontfamily` varchar(32) NOT NULL,
  `sitetitle_location` varchar(16) NOT NULL,
  `sitetitle_position` varchar(16) NOT NULL,
  `show_icon` tinyint(1) NOT NULL,
  `img_path` varchar(255) NOT NULL,
  `sitelogo_location` varchar(16) NOT NULL,
  `sitelogo_position` varchar(16) NOT NULL,
  `show_tagline` tinyint(1) NOT NULL ,
  `tagline_title` varchar(128) NOT NULL,
  `tagline_fontsize` tinyint(2) NOT NULL ,
  `tagline_fontfamily` varchar(32) NOT NULL,
  `tagline_location` varchar(16) NOT NULL,
  `tagline_position` varchar(16) NOT NULL,
  `header_bgcol` varchar(16) NOT NULL,
  `header_outpadding` tinyint(2) NOT NULL ,
  `header_titlecolor` varchar(16) NOT NULL,
  `header_tagcolor` varchar(16) NOT NULL,
  `header_bottomcolor` varchar(16) NOT NULL,
  `header_bottomwidth` tinyint(2) NOT NULL ,
  `footer_bottomcol` varchar(16) NOT NULL,
  `footer_bottomwidth` tinyint(2) NOT NULL ,
  `lr_bordercolor` varchar(16) NOT NULL,
  `body_outerbgcol` varchar(16) NOT NULL,
  `body_innerbgcol` varchar(16) NOT NULL,
  `signature_bgcol` varchar(16) NOT NULL,
  `active_delete` tinyint(1) NOT NULL,
  `lr_bottomwidth` tinyint(2) NOT NULL ,
  `active_template` tinyint(2) NOT NULL ,
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemailtemplates_templates`
--

INSERT IGNORE INTO `engine4_sitemailtemplates_templates` (`template_id`, `testemail_admin`, `template_title`, `show_title`, `site_title`, `sitetitle_fontsize`, `sitetitle_fontfamily`, `sitetitle_location`, `sitetitle_position`, `show_icon`, `img_path`, `sitelogo_location`, `sitelogo_position`, `show_tagline`, `tagline_title`, `tagline_fontsize`, `tagline_fontfamily`, `tagline_location`, `tagline_position`, `header_bgcol`, `header_outpadding`, `header_titlecolor`, `header_tagcolor`, `header_bottomcolor`, `header_bottomwidth`, `footer_bottomcol`, `footer_bottomwidth`, `lr_bordercolor`, `body_outerbgcol`, `body_innerbgcol`, `signature_bgcol`, `active_delete`, `lr_bottomwidth`, `active_template`) VALUES
(1, '', 'Template 1', 1, 'My Community', 17, 'Arial', 'header', 'left', 1, 'application/modules/Sitemailtemplates/externals/images/web.png', 'header', 'left', 0, 'There''s a lot to do here!', 11, 'Arial', 'header', 'right', '#79b4d4', 10, '#ffffff', '#ffffff', '#cccccc', 1, '#cccccc', 1, '#cccccc', '#f7f7f7', '#ffffff', '#f7f7f7', 1, 1, 1),
(2, '', 'Template 2', 1, 'My Community', 17, 'Arial', 'header', 'left', 1, 'application/modules/Sitemailtemplates/externals/images/web.png', 'header', 'left', 1, 'There''s a lot to do here!', 11, 'Arial', 'header', 'right', '#79b4d4', 10, '#ffffff', '#ffffff', '#cccccc', 1, '#cccccc', 1, '#cccccc', '#f7f7f7', '#ffffff', '#f7f7f7', 1, 1, 0),
(3, '', 'Template 3', 0, 'My Community', 17, 'Arial', 'header', 'left', 1, 'application/modules/Sitemailtemplates/externals/images/default-logo.png', 'header', 'center', 0, 'There''s a lot to do here!', 11, 'Arial', 'header', 'right', '#79b4d4', 10, '#ffffff', '#ffffff', '#79b4d4', 0, '#79b4d4', 3, '#79b4d4', '#f7f7f7', '#ffffff', '#f7f7f7', 1, 3, 0),
(4, '', 'Template 4', 0, 'My Community', 17, 'Arial', 'header', 'left', 1, 'application/modules/Sitemailtemplates/externals/images/default-logo.png', 'header', 'left', 1, 'There''s a lot to do here!', 11, 'Arial', 'header', 'right', '#79b4d4', 10, '#ffffff', '#ffffff', '#cccccc', 1, '#cccccc', 1, '#cccccc', '#f7f7f7', '#ffffff', '#f7f7f7', 1, 1, 0),
(5, '', 'Template 5', 1, 'My Community', 31, 'Trebuchet MS', 'header', 'center', 0, 'application/modules/Sitemailtemplates/externals/images/default-logo.png', 'header', 'right', 1, 'There''s a lot to do here!', 11, 'Arial', 'above_header', 'center', '#474747', 10, '#ffffff', '#474747', '#474747', 1, '#474747', 1, '#474747', '#f7f7f7', '#ffffff', '#f7eded', 1, 1, 0),
(6, '', 'Template 6', 0, 'My Community', 17, 'Arial', 'header', 'left', 1, 'application/modules/Sitemailtemplates/externals/images/default-logo.png', 'header', 'left', 0, 'There''s a lot to do here!', 11, 'Arial', 'header', 'right', '#ffffff', 5, '#ffffff', '#ffffff', '#8c4386', 5, '#8c4386', 5, '#8c4386', '#fff', '#ffffff', '#ffffff', 1, 5, 0),
(7, '', 'Template 7', 1, 'My Community', 17, 'Arial', 'header', 'left', 0, 'application/modules/Sitemailtemplates/externals/images/web.png', 'header', 'left', 0, 'There''s a lot to do here!', 11, 'Arial', 'header', 'right', '#79b4d4', 10, '#ffffff', '#ffffff', '#cccccc', 1, '#cccccc', 1, '#cccccc', '#f7f7f7', '#ffffff', '#f7f7f7', 1, 1, 0),
(8, '', 'Template 8', 1, 'My Community', 35, 'Times New Roman', 'header', 'center', 0, 'application/modules/Sitemailtemplates/externals/images/default-logo.png', 'body', 'right', 1, 'There''s a lot to do here!', 12, 'Trebuchet MS', 'header', 'center', '#fe9900', 10, '#ffffff', '#ffffff', '#dbd6d0', 5, '#dbd6d0', 5, '#dbd6d0', '#ffffff', '#ffffff', '#dbd6d0', 1, 5, 0),
(9, '', 'Template 9', 0, 'My Community', 17, 'Arial', 'header', 'left', 1, 'application/modules/Sitemailtemplates/externals/images/default-logo.png', 'body', 'left', 0, 'There''s a lot to do here!', 11, 'Arial', 'header', 'right', '#79b4d4', 10, '#ffffff', '#ffffff', '#8c4386', 3, '#8c4386', 3, '#8c4386', '#f7f7f7', '#ffffff', '#f7f7f7', 1, 3, 0),
(10, '', 'Template 10', 0, 'My Community', 17, 'Arial', 'header', 'center', 1, 'application/modules/Sitemailtemplates/externals/images/default-logo.png', 'header', 'left', 0, 'There''s a lot to do here!', 11, 'Arial', 'header', 'right', '#ffffff', 2, '#ffffff', '#46449c', '#e6e9e7', 5, '#e6e9e7', 0, '#cccccc', '#e6e9e7', '#ffffff', '#ffffff', 1, 0, 0);
