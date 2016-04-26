INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('siteevent_page_invite', 'siteevent', '{item:$subject} has invited you to the page event {item:$object}.', 0, '', 1);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEEVENT_PAGE_INVITE_EMAIL", "siteevent", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('siteevent_business_invite', 'siteevent', '{item:$subject} has invited you to the business event {item:$object}.', 0, '', 1);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEEVENT_BUSINESS_INVITE_EMAIL", "siteevent", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('siteevent_group_invite', 'siteevent', '{item:$subject} has invited you to the group event {item:$object}.', 0, '', 1);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEEVENT_GROUP_INVITE_EMAIL", "siteevent", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");