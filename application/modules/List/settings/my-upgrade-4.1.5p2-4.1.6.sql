UPDATE `engine4_activity_actiontypes` SET `type` = 'comment_list_listing' , `displayable` = '1',
`attachable` = '1',`is_generated` = '0' WHERE `engine4_activity_actiontypes`.`type` = 'comment_list' LIMIT 1 ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('list_gutter_share', 'list', 'Share Listing', 'List_Plugin_Menus', '', 'list_gutter', '', 1, 0, 4),
('list_gutter_messageowner', 'list', 'Message Owner', 'List_Plugin_Menus', '', 'list_gutter', '', 1, 0, 5),
('list_gutter_tfriend', 'list', 'Tell a friend', 'List_Plugin_Menus', '', 'list_gutter', '', 1, 0, 6),
('list_gutter_print', 'list', 'Print Listing', 'List_Plugin_Menus', '', 'list_gutter', '', 1, 0, 7),
('list_gutter_publish', 'list', 'Publish Listing', 'List_Plugin_Menus', '', 'list_gutter', '', 1, 0, 8),
('list_gutter_open', 'list', 'Open Listing', 'List_Plugin_Menus', '', 'list_gutter', '', 1, 0, 9),
('list_gutter_close', 'list', 'Close Listing', 'List_Plugin_Menus', '', 'list_gutter', '', 1, 0, 10),
('list_gutter_delete', 'list', 'Delete Listing', 'List_Plugin_Menus', '', 'list_gutter', '', 1, 0, 11),
('list_gutter_report', 'list', 'Report Listing', 'List_Plugin_Menus', '', 'list_gutter', '', 1, 0, 12);