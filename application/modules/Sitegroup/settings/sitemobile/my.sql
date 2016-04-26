
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
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES 
('sitegroup_main', 'standard', 'Group Main Options Menu', '999'),
('sitegroup_gutter', 'standard', 'Group Profile Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('core_main_sitegroup', 'sitegroup', 'Groups',  'Sitegroup_Plugin_Menus::canViewSitegroups', '{"route":"sitegroup_general", "action":"home"}', 'core_main', '', 36, 1, 1),
( 'sitegroup_main_home', 'sitegroup', 'Groups Home', 'Sitegroup_Plugin_Menus::canViewSitegroups', '{"route":"sitegroup_general","action":"home"}', 'sitegroup_main', '', '10', 1, 1),
( 'sitegroup_main_browse', 'sitegroup', 'Browse Groups', 'Sitegroup_Plugin_Menus::canViewSitegroups', '{"route":"sitegroup_general","action":"index"}', 'sitegroup_main', '', '20', 1, 1),
( 'sitegroup_main_manage', 'sitegroup', 'My Groups', 'Sitegroup_Plugin_Menus::canCreateSitegroups', '{"route":"sitegroup_general","action":"manage"}', 'sitegroup_main', '','140', 1, 1),
( 'sitegroup_main_manageadmin', 'sitegroup', 'Groups I Admin', 'Sitegroup_Plugin_Menus::canCreateSitegroups', '{"route":"sitegroup_manageadmins","action":"my-groups"}', 'sitegroup_main', '','150', 1, 1),
( 'sitegroup_main_managelike', 'sitegroup', 'Groups I Like', 'Sitegroup_Plugin_Menus::canCreateSitegroups', '{"route":"sitegroup_like","action":"mylikes"}', 'sitegroup_main', '','160', 1, 1),
('sitegroup_gutter_share', 'sitegroup', 'Share', 'Sitegroup_Plugin_Menus', '', 'sitegroup_gutter', '',5, 1, 1),
('sitegroup_gutter_messageowner', 'sitegroup', 'Message', 'Sitegroup_Plugin_Menus', '', 'sitegroup_gutter', '', 6,1, 1),
('sitegroup_gutter_tfriend', 'sitegroup', 'Tell a friend', 'Sitegroup_Plugin_Menus', '', 'sitegroup_gutter', '', 7, 1, 1),
('sitegroup_gutter_claim', 'sitegroup', 'Claim this Group', 'Sitegroup_Plugin_Menus', '', 'sitegroup_gutter', '', 13, 1, 1),
('sitegroup_gutter_report', 'sitegroup', 'Report', 'Sitegroup_Plugin_Menus', '', 'sitegroup_gutter', '', 16, 1, 1);

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitegroup_index_home', 'Sitegroup_Form_Search', 'search', '{"type":"sitegroup_group"}', '', '{"route":"sitegroup_general","action":"index"}'),
('sitegroup_index_index', 'Sitegroup_Form_Search', 'search', '{"type":"sitegroup_group"}', '', ''),
('sitegroup_index_manage', 'Sitegroup_Form_ManageSearch', 'search', '{"type":"sitegroup_group"}', '', '');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitegroup_index_view', 'sitegroup_gutter', 'sitegroup_group');