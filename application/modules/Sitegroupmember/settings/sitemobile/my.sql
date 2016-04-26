INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitegroup_main_member', 'sitegroupmember', 'Members', 'Sitegroupmember_Plugin_Menus::canViewMembers', '{"route":"sitegroupmember_browse","action":"browse"}', 'sitegroup_main', '','40', '1', '1');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitegroup_main_managejoin', 'sitegroup', 'Groups I Joined', 'Sitegroup_Plugin_Menus::canCreateSitegroups', '{"route":"sitegroup_like","action":"my-joined"}', 'sitegroup_main', '','170', 1, 1);

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitegroupmember_index_browse', 'Sitegroupmember_Form_Searchwidget', 'search_member', '', '', '');