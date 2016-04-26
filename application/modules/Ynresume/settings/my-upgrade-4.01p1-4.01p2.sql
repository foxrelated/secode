UPDATE `engine4_core_modules` SET `version` = '4.01p2' WHERE `engine4_core_modules`.`name` = 'ynresume' LIMIT 1 ;
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynresume_endorsement', 'ynresume', '{item:$subject} has endorsed your {item:$object:skill(s)}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynresume_endorsement', 'ynresume', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link]');
