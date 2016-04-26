
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
INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES
('siteevent', 1, 0, 0, 0),
('siteeventrepeat', 1, 0, 0, 0),
('siteeventdocument', 1, 0, 0, 0);

INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES 
('siteevent_main', 'standard', 'Event Main Options Menu', '150');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`,  `order`, `enable_mobile`, `enable_tablet`) VALUES
('core_main_siteevent', 'siteevent', 'Events', 'Siteevent_Plugin_Menus::canViewSiteevents', '{"route":"siteevent_general","action":"home"}', 'core_main','',15, 1, 1),
('siteevent_main_categories', 'siteevent', 'Categories', 'Siteevent_Plugin_Menus::canViewCategories', '{"route":"siteevent_review_categories","action":"categories"}','siteevent_main','', 7,1, 1),
('siteevent_main_reviews', 'siteevent', 'Browse Reviews', 'Siteevent_Plugin_Menus::canViewBrosweReview', '{"route":"siteevent_review_browse", "action":"browse"}','siteevent_main','', 8, 1, 1),
('siteevent_main_diaries', 'siteevent', 'Diaries', 'Siteevent_Plugin_Menus::canViewDiary', '{"route":"siteevent_diary_general","action":"browse"}', 'siteevent_main','', 9, 1,1),
('siteevent_main_home', 'siteevent', 'Events Home', 'Siteevent_Plugin_Menus::canViewSiteevents', '{"route":"siteevent_general","action":"home"}','siteevent_main','',1, 1, 1),
('siteevent_main_browse', 'siteevent', 'Browse Events', 'Siteevent_Plugin_Menus::canViewSiteevents', '{"route":"siteevent_general","action":"index"}', 'siteevent_main','',2, 1, 1),
('siteevent_main_manage', 'siteevent', 'My Events', 'Siteevent_Plugin_Menus::canCreateSiteevents', '{"route":"siteevent_general","action":"manage"}', 'siteevent_main','', 4, 1, 1);

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('siteevent_index_index', 'Siteevent_Form_Search', 'search', '{"type":"siteevent_event"}', '', '{"route":"siteevent_general","action":"index"}'),
('siteevent_review_browse', 'Siteevent_Form_Review_Search', 'search', '{"type":"siteevent_review"}', '', '{"route":"siteevent_review_browse","action":"browse"}'),
('siteevent_diary_browse', 'Siteevent_Form_Diary_Search', 'search', '{"type":"siteevent_diary"}', '','{"route":"siteevent_diary_general","action":"browse"}'),
('siteevent_index_manage', 'Siteevent_Form_Search', 'search', '', '', ''),
('siteevent_index_home', 'Siteevent_Form_Search', 'search', '', '', '');


--
-- Dumping data for Event profile options
--

INSERT IGNORE INTO `engine4_sitemobile_menus` ( `name`, `type`, `title`, `order`) VALUES
( 'siteevent_gutter', 'standard', 'Event Profile Options Menu', 999);

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES 
('siteevent_index_view', 'siteevent_gutter', 'siteevent_event');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('siteevent_gutter_member', 'siteevent', 'Join Button', 'Siteevent_Plugin_Menus::siteeventGutterMember', '', 'siteevent_gutter', '', 2, 1, 1),
('siteevent_gutter_diary', 'siteevent', 'Add to Diary', 'Siteevent_Plugin_Menus::siteeventGutterDiary', '', 'siteevent_gutter', '', 2, 1, 1),
('siteevent_gutter_messageowner', 'siteevent', 'Message Owner', 'Siteevent_Plugin_Menus::siteeventGutterMessageowner', '', 'siteevent_gutter', '', 2, 1, 1),
('siteevent_gutter_share', 'siteevent', 'Share This Event', 'Siteevent_Plugin_Menus::siteeventGutterShare', '', 'siteevent_gutter', '', 4, 1, 1),
('siteevent_gutter_tfriend', 'siteevent', 'Tell a Friend', 'Siteevent_Plugin_Menus::siteeventGutterTfriend', '', 'siteevent_gutter', '', 5, 1, 1),
('siteevent_gutter_report', 'siteevent', 'Report This Event', 'Siteevent_Plugin_Menus::siteeventGutterReport', '', 'siteevent_gutter', '', 6, 1, 1),
('siteevent_gutter_close', 'siteevent', 'Re-publish / Cancel', 'Siteevent_Plugin_Menus::siteeventGutterClose', '', 'siteevent_gutter', '', 10, 1, 1);

--
-- Dumping data for Diary profile options
--

INSERT IGNORE INTO `engine4_sitemobile_menus` ( `name`, `type`, `title`, `order`) VALUES
( 'siteevent_diary_gutter', 'standard', 'Event: Diary Profile Options Menu', 999);

INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
( 'siteevent_diary_gutter_share', 'siteevent', 'Share', 'Siteevent_Plugin_Menus', '', 'siteevent_diary_gutter', '', 0, 4, 1, 1),
( 'siteevent_diary_gutter_tfriend', 'siteevent', 'Tell a Friend', 'Siteevent_Plugin_Menus', '', 'siteevent_diary_gutter', '', 0, 5, 1, 1),
( 'siteevent_diary_gutter_report', 'siteevent', 'Report','Siteevent_Plugin_Menus', '', 'siteevent_diary_gutter', '', 0, 6, 1, 1);


INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES 
('siteevent_diary_profile', 'siteevent_diary_gutter', 'siteevent_diary');

--
-- Dumping data for Topic profile options
--

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('siteevent_topic_view', 'siteevent_topic', 'siteevent_topic');


INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('Siteevent_topic_watch', 'siteevent', 'Watch Topic', 'Siteevent_Plugin_Menus', '', 'siteevent_topic', NULL, '0', '1', '1', '1'), 
('Siteevent_topic_sticky', 'siteevent', 'Make Sticky', 'Siteevent_Plugin_Menus', '', 'siteevent_topic', NULL, '0', '2', '1', '1'),
('Siteevent_topic_open', 'siteevent', 'Open', 'Siteevent_Plugin_Menus', '', 'siteevent_topic', NULL, '0', '3', '1', '1'),
('Siteevent_topic_rename', 'siteevent', 'Rename', 'Siteevent_Plugin_Menus', '', 'siteevent_topic', NULL, '0', '4', '1', '1'),
('Siteevent_topic_delete', 'siteevent', 'Delete', 'Siteevent_Plugin_Menus', '', 'siteevent_topic', NULL, '0', '5', '1', '1');

INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES 
('siteevent_topic', 'standard', 'Event Topic Options Menu', '999');

--
-- Dumping data for Photo profile options
--

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('siteevent_photo_view', 'siteevent_photo', 'siteevent_photo');

INSERT IGNORE INTO `engine4_sitemobile_menus` (`id`, `name`, `type`, `title`, `order`) VALUES (NULL, 'siteevent_photo', 'standard', 'Event Photo View Options Menu', '999');



INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('siteevent_photo_share', 'siteevent', 'Share', 'Siteevent_Plugin_Menus', '', 'siteevent_photo', NULL, '0', '3', '1', '1'),
('siteevent_photo_report', 'siteevent', 'Report', 'Siteevent_Plugin_Menus', '', 'siteevent_photo', NULL, '0', '4', '1', '1'),
('siteevent_photo_profile', 'siteevent', 'Make Profile Photo', 'Siteevent_Plugin_Menus', '', 'siteevent_photo', NULL, '0', '5', '1', '1');

--
-- Dumping data for Review profile options
--

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('siteevent_review_view', 'siteevent_review', 'siteevent_review');

INSERT IGNORE INTO `engine4_sitemobile_menus` (`id`, `name`, `type`, `title`, `order`) VALUES (NULL, 'siteevent_review', 'standard', 'Events - Review Profile Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('siteevent_review_update', 'siteevent', 'Update your Review', 'Siteevent_Plugin_Menus', '', 'siteevent_review', NULL, '0', '1', '1', '1'),
('siteevent_review_create', 'siteevent', 'Write a Review', 'Siteevent_Plugin_Menus', '', 'siteevent_review', NULL, '0', '2', '1', '1'),
('siteevent_review_share', 'siteevent', 'Share Review', 'Siteevent_Plugin_Menus', '', 'siteevent_review', NULL, '0', '3', '1', '1'),
('siteevent_review_email', 'siteevent', 'Email Review', 'Siteevent_Plugin_Menus', '', 'siteevent_review', NULL, '0', '4', '1', '1'),
('siteevent_review_delete', 'siteevent', 'Delete Review', 'Siteevent_Plugin_Menus', '', 'siteevent_review', NULL, '0', '5', '1', '1'),
('siteevent_review_report', 'siteevent', 'Report Review', 'Siteevent_Plugin_Menus', '', 'siteevent_review', NULL, '0', '6', '1', '1');


INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`,  `order`, `enable_mobile`, `enable_tablet`) VALUES
('siteevent_main_calendar', 'siteevent', 'Calendar', 'Siteevent_Plugin_Menus::canViewSiteevents', '{"route":"siteevent_general","action":"calendar"}', 'siteevent_main','',10, 1, 1);

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('siteevent_gutter_invite', 'siteevent', 'Invite Guests', 'Siteevent_Plugin_Menus::siteeventGutterInvite', '', 'siteevent_gutter', '', 1, 1, 1);