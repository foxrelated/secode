/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventinvite
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('siteeventinvite', 'Advanced Events - Inviter and Promotion', 'Advanced Events - Inviter and Promotion', '4.8.10', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('siteevent_gutter_promote', 'siteeventinvite', 'Promote Event', 'Siteeventinvite_Plugin_Menus::canPromote', '', 'siteevent_gutter', '', 1, 0, 9);