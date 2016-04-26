
--
-- Table structure for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`)
VALUES  ('sitepageurl', 'Directory / Pages - Short Page URL Extension', 'Directory / Pages - Short Page URL
Extension', '4.2.3', 1, 'extra') ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_admin_main_plugins_sitepageurl', 'sitepageurl', 'Directory/Pages - Short Page URL', '', '{"route":"admin_default","module":"sitepageurl","controller":"settings"}', 'core_admin_main_plugins', '', 1, 0, 999),
('sitepageurl_admin_main_settings', 'sitepageurl', 'Global Settings', '',
'{"route":"admin_default","module":"sitepageurl","controller":"settings"}', 'sitepageurl_admin_main', '',
1,0,1),
( 'sitepageurl_admin_main_faq', 'sitepageurl', 'FAQ', NULL,
'{"route":"admin_default","module":"sitepageurl","controller":"settings","action":"faq"}',
'sitepageurl_admin_main', NULL, 1, 0, 4);
