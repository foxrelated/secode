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
INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('core_main_suggestion', 'suggestion', 'Suggestions',  '', '{"route":"suggestion_explore_suggestions"}', 'core_main', '', 8, 1, 1); 

INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES
('suggestion', 1, 0, 0, 0);

 INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES 
 ('suggestion_main', 'standard', 'Suggestion Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('suggestion_main_view', 'suggestion', 'Suggestions', 'Suggestion_Plugin_Menus::canViewSuggestions', '{"route":"default","module":"suggestion","controller":"index","action":"viewfriendsuggestion"}', 'suggestion_main', '', 1, 1, 1),
('suggestion_main_request', 'suggestion', 'Requests', 'Suggestion_Plugin_Menus::canViewSuggestions', '{"route":"suggestion_friend_request"}', 'suggestion_main', '', 2, 1, 1),
('suggestion_main_member', 'suggestion', 'Members', '', '{"route":"user_general","module":"user", "controller":"index","action":"browse"}', 'suggestion_main', '', 3, 1, 1);

