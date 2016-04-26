-- -------------------------------------------------------------------

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('sugg.sitepage.wid', 5),
('sitepage.sugg.link', 1),
('after.sitepage.create', 1);

-- --------------------------------------------------------------------

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitepage_suggest_friend', 'suggestion', 'Suggest to Friends', 'Suggestion_Plugin_Menus', '{"route":"default", "class":"buttonlink icon_page_friend_suggestion smoothbox"}', 'sitepage_gutter', NULL, 1, 0, 999),
('sitepage_event_suggest_friend', 'suggestion', 'Suggest to Friends', 'Suggestion_Plugin_Menus', '{"route":"default", "icon":"application/modules/Suggestion/externals/images/sugg_blub.png"}', 'sitepageevent_gutter', NULL, 1, 0, 999);

-- --------------------------------------------------------------------

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('page_suggestion', 'suggestion', '{item:$subject} has suggested to you a {item:$object:page}.', 1, 'suggestion.widget.get-notify', 1),
('page_document_suggestion', 'suggestion', '{item:$subject} has suggested to you a {item:$object:page document}.', 1, 'suggestion.widget.get-notify', 1),
('page_album_suggestion', 'suggestion', '{item:$subject} has suggested to you a {item:$object:page album}.', 1, 'suggestion.widget.get-notify', 1),
('page_video_suggestion', 'suggestion', '{item:$subject} has suggested to you a {item:$object:page video}.', 1, 'suggestion.widget.get-notify', 1),
('page_poll_suggestion', 'suggestion', '{item:$subject} has suggested to you a {item:$object:page poll}.', 1, 'suggestion.widget.get-notify', 1),
('page_event_suggestion', 'suggestion', '{item:$subject} has suggested to you a {item:$object:page event}.', 1, 'suggestion.widget.get-notify', 1),
('page_note_suggestion', 'suggestion', '{item:$subject} has suggested to you a {item:$object:page note}.', 1, 'suggestion.widget.get-notify', 1),
('page_review_suggestion', 'suggestion', '{item:$subject} has suggested to you a {item:$object:page review}.', 1, 'suggestion.widget.get-notify', 1);

-- ---------------------------------------------------------------------

INSERT IGNORE INTO  `engine4_core_mailtemplates` (
`type` ,
`module` ,
`vars`
)
VALUES 
('notify_page_suggestion',  'suggestion',  '[suggestion_sender], [suggestion_entity], [email], [link]'),
('notify_page_suggestion',  'suggestion',  '[suggestion_sender], [suggestion_entity], [email], [link]'),
('notify_page_document_suggestion',  'suggestion',  '[suggestion_sender], [suggestion_entity], [email], [link]'),
('notify_page_album_suggestion',  'suggestion',  '[suggestion_sender], [suggestion_entity], [email], [link]'),
('notify_page_video_suggestion',  'suggestion',  '[suggestion_sender], [suggestion_entity], [email], [link]'),
('notify_page_poll_suggestion',  'suggestion',  '[suggestion_sender], [suggestion_entity], [email], [link]'),
('notify_page_event_suggestion',  'suggestion',  '[suggestion_sender], [suggestion_entity], [email], [link]'),
('notify_page_note_suggestion',  'suggestion',  '[suggestion_sender], [suggestion_entity], [email], [link]'),
('notify_page_review_suggestion',  'suggestion',  '[suggestion_sender], [suggestion_entity], [email], [link]');

-- ---------------------------------------------------------------------------------------------