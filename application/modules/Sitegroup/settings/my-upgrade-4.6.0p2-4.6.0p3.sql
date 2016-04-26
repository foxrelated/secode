INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('sitegroup_dashboard', 'standard', 'Group Dashboard Menu', '999');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitegroup_dashboard_getstarted', 'sitegroup', 'Get Started', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard","action":"get-started"}', 'sitegroup_dashboard', '', 1, 0, 1),
('sitegroup_dashboard_editinfo', 'sitegroup', 'Edit Info', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_edit"}', 'sitegroup_dashboard', '', 1, 0, 2),
('sitegroup_dashboard_profilepicture', 'sitegroup', 'Profile Picture', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"profile-picture"}', 'sitegroup_dashboard', '', 1, 0, 3),
('sitegroup_dashboard_overview', 'sitegroup', 'Overview', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"overview"}', 'sitegroup_dashboard', '', 1, 0, 4),
('sitegroup_dashboard_contact', 'sitegroup', 'Contact Details', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"contact"}', 'sitegroup_dashboard', '', 1, 0, 5),
('sitegroup_dashboard_managememberroles', 'sitegroup', 'Manage Member Roles', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"manage-member-category"}', 'sitegroup_dashboard', '', 1, 0, 6),
('sitegroup_dashboard_announcements', 'sitegroup', 'Manage Announcements', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"announcements"}', 'sitegroup_dashboard', '', 1, 0, 7),
('sitegroup_dashboard_alllocation', 'sitegroup', 'Location', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"all-location"}', 'sitegroup_dashboard', '', 1, 0, 8),
('sitegroup_dashboard_editlocation', 'sitegroup', 'Location', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"edit-location"}', 'sitegroup_dashboard', '', 1, 0, 9),
('sitegroup_dashboard_profiletype', 'sitegroup', 'Profile Info', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"profile-type"}', 'sitegroup_dashboard', '', 1, 0, 10),
('sitegroup_dashboard_apps', 'sitegroup', 'Apps', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"app"}', 'sitegroup_dashboard', '', 1, 0, 11),
('sitegroup_dashboard_marketing', 'sitegroup', 'Marketing', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"marketing"}', 'sitegroup_dashboard', '', 1, 0, 12),
('sitegroup_dashboard_badge', 'sitegroup', 'Badge', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroupbadge_request"}', 'sitegroup_dashboard', '', 1, 0, 13),
('sitegroup_dashboard_notificationsettings', 'sitegroup', 'Manage Notifications', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"notification-settings"}', 'sitegroup_dashboard', '', 1, 0, 14),
('sitegroup_dashboard_insights', 'sitegroup', 'Insights', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_insights"}', 'sitegroup_dashboard', '', 1, 0, 15),
('sitegroup_dashboard_reports', 'sitegroup', 'Reports', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_reports"}', 'sitegroup_dashboard', '', 1, 0, 16),
('sitegroup_dashboard_manageadmins', 'sitegroup', 'Manage Admins', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_manageadmins", "action":"index"}', 'sitegroup_dashboard', '', 1, 0, 17),
('sitegroup_dashboard_featuredowners', 'sitegroup', 'Featured Admins', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"featured-owners"}', 'sitegroup_dashboard', '', 1, 0, 18),
('sitegroup_dashboard_editstyle', 'sitegroup', 'Edit Style', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_dashboard", "action":"edit-style"}', 'sitegroup_dashboard', '', 1, 0, 19),
('sitegroup_dashboard_editlayout', 'sitegroup', 'Edit Layout', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_layout"}', 'sitegroup_dashboard', '', 1, 0, 20),
('sitegroup_dashboard_updatepackages', 'sitegroup', 'Packages', 'Sitegroup_Plugin_Dashboardmenus', '{"route":"sitegroup_packages", "action":"update-package"}', 'sitegroup_dashboard', '', 1, 0, 21);