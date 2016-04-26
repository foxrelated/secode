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
'document_suggestion',  'suggestion',  '{item:$subject} has suggested to you a {item:$object:document}.',  '1',  'suggestion.widget.get-notify'
);

-- --------------------------------------------------------
--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('sugg.document.wid', 5),
('document.sugg.link', 1),
('sugg.truncate.limit', 15);


