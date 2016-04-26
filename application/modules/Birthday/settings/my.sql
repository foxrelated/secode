/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: my.sql 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('birthday', 'Birthday', 'Birthday', '4.8.7p1', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('birthdayemail', 'Birthdayemail', 'Birthday Email', '4.8.7p1', 1, 'extra');

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('birthday.widget', '3'); 

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES ('core_admin_main_plugins_birthday', 'birthday', 'SEAO - Birthdays', '', '{"route":"admin_default","module":"birthday","controller":"settings"}', 'core_admin_main_plugins', '', 999), ('birthday_admin_main_settings', 'birthday', 'Global Settings', '', '{"route":"admin_default","module":"birthday","controller":"settings"}', 'birthday_admin_main', '', 1), 
('birthdayemail_admin_main_settings', 'birthday', 'Email Settings', '', '{"route":"admin_default","module":"birthday","controller":"settings","action":"birthdayemail"}', 'birthday_admin_main', '', 2),
('birthday_admin_main_faq', 'birthday', 'FAQ', '', '{"route":"admin_default","module":"birthday","controller":"settings", "action":"faq"}', 'birthday_admin_main', '', 3), 
('user_home_birthday', 'birthday', 'Birthdays', 'Birthday_Plugin_Menus', '', 'user_home', '', 12);

-- --------------------------------------------------------
--
-- Dumping data for table `engine4_core_mailtemplates`
--

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
( 'birthday_reminder', 'birthdayemail', '[host][email][recipient_title][template_header][count_friends][days][friend_list][template_footer]'),
( 'birthday_wish', 'birthdayemail', '[host][email][recipient_title][template_header][wish_image][site_title][template_footer]');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('birthday_reminder', 'birthdayemail', '{item:$subject} has birthday today.', 0, '');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('birthdayemail.reminder.time', '1');
