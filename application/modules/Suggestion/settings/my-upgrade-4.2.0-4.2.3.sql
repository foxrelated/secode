
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`)VALUES (
'suggestion_modInfo', 'suggestion', 'Manage Modules', NULL , '{"route":"admin_default","module":"suggestion","controller":"settings", "action":"manage-module"}', 'sugg_admin_main', NULL , '1', '0', '3'
);


-- ---------------------------------------------------------------------------

DROP TABLE IF EXISTS `engine4_suggestion_mixinfos`;