/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
-- --------------------------------------------------------
--
-- Dumping data for table `engine4_core_modules`
--
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('suggestion', 'Suggestions', 'Suggestions', '4.8.5p1', 1, 'extra');

-- --------------------------------------------------------------------

INSERT IGNORE INTO `engine4_activity_notificationtypes` ( `type` , `module` , `body` , `is_request` , `handler` ) VALUES ( 'picture_suggestion', 'core', '{item:$subject} has suggested to you a {item:$object:profile photo}.', '1', 'suggestion.widget.request-photo' );

/*INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`)VALUES (
'suggestion_invitestatistics', 'suggestion', 'Invite Statistics', NULL , '{"route":"admin_default","module":"suggestion","controller":"settings", "action":"invite-statistics"}', 'sugg_admin_main', NULL , '1', '0', '3');*/

