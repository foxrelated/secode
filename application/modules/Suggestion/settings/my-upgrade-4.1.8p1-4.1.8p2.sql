
-- ------------------------------------------------------------------

INSERT IGNORE INTO `engine4_suggestion_mixinfos` (`name`, `status`) VALUES
('sitebusiness', 1);

-- ------------------------------------------------------------------

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitebusiness_suggest_friend', 'suggestion', 'Suggest to Friends', 'Suggestion_Plugin_Menus', '{"route":"default", "class":"buttonlink icon_page_friend_suggestion smoothbox"}', 'sitebusiness_gutter', NULL, 1, 0, 999),
('sitebusiness_event_suggest_friend', 'suggestion', 'Suggest to Friends', 'Suggestion_Plugin_Menus', '{"route":"default", "icon":"application/modules/Suggestion/externals/images/sugg_blub.png"}', 'sitebusinessevent_gutter', NULL, 1, 0, 999);