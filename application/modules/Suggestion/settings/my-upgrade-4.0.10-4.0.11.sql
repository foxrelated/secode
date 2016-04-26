-- --------------------------------------------------------
--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (
`type` ,
`module` ,
`body` ,
`is_request` ,
`handler`
)
VALUES (
'listing_suggestion',  'suggestion',  '{item:$subject} has suggested to you a {item:$object:listing}.',  '1',  'suggestion.widget.get-notify'
);

-- --------------------------------------------------------
--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('sugg.list.wid', 5),
('after.list.create', 1),
('list.sugg.link', 1);

-- ---------------------------------------------------------
--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('list_suggest_friend', 'suggestion', 'Suggest to Friends', 'Suggestion_Plugin_Menus', '{"route":"suggest_list","class":"buttonlink icon_list_friend_suggestion smoothbox"}', 'list_gutter', NULL, 0, 999);

-- ----------------------------------------------------------