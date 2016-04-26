
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menus`
--

INSERT IGNORE INTO `engine4_sitemobile_menus` ( `name`, `type`, `title`, `order`) 
VALUES 
('sitegroupalbum_profile', 'standard', 'Group Album Profile Options Menu', '999'),
('sitegroupalbum_photo', 'standard', 'Group Album Photo View Options Menu', '999');
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitegroupalbum_viewAlbums', 'sitegroupalbum', 'View Albums', 'Sitegroupalbum_Plugin_Menus', '', 'sitegroupalbum_profile', NULL, 0, 1, 1, 1),
('sitegroupalbum_add', 'sitegroupalbum', 'Add More Photos', 'Sitegroupalbum_Plugin_Menus', '', 'sitegroupalbum_profile', NULL, 0, 2, 1, 1),
('sitegroupalbum_edit', 'sitegroupalbum', 'Edit Album', 'Sitegroupalbum_Plugin_Menus', '', 'sitegroupalbum_profile', NULL, 0, 3, 1, 1),
('sitegroupalbum_delete', 'sitegroupalbum', 'Delete Album', 'Sitegroupalbum_Plugin_Menus', '', 'sitegroupalbum_profile', NULL, 0, 4, 1, 1),
('sitegroup_main_album', 'sitegroupalbum', 'Albums', 'Sitegroupalbum_Plugin_Menus::canViewAlbums', '{"route":"sitegroupalbum_browse","action":"browse"}', 'sitegroup_main', NULL, 0,'30', 1, 1),
 ('sitegroupalbum_photo_edit', 'sitegroupalbum', 'Edit', 'Sitegroupalbum_Plugin_Menus', '', 'sitegroupalbum_photo', NULL, '0', '1', '1', '1'),
 ('sitegroupalbum_photo_delete', 'sitegroupalbum', 'Delete', 'Sitegroupalbum_Plugin_Menus', '', 'sitegroupalbum_photo', NULL, '0', '2', '1', '1'),
('sitegroupalbum_photo_share', 'sitegroupalbum', 'Share', 'Sitegroupalbum_Plugin_Menus', '', 'sitegroupalbum_photo', NULL, '0', '3', '1', '1'),
('sitegroupalbum_photo_report', 'sitegroupalbum', 'Report', 'Sitegroupalbum_Plugin_Menus', '', 'sitegroupalbum_photo', NULL, '0', '4', '1', '1'),
('sitegroupalbum_photo_profile', 'sitegroupalbum', 'Make Group Profile Photo', 'Sitegroupalbum_Plugin_Menus', '', 'sitegroupalbum_photo', NULL, '0', '5', '1', '1');

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitegroup_album_browse', 'Sitegroupalbum_Form_Searchwidget', 'search_album', '', '', '');

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitegroup_album_view', 'sitegroupalbum_profile', 'sitegroup_album'),
('sitegroup_photo_view', 'sitegroupalbum_photo', 'sitegroup_photo');
