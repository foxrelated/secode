UPDATE `engine4_core_modules` SET `version` = '4.02p5' where 'name' = 'ynfundraising';

ALTER TABLE  `engine4_ynfundraising_campaigns` ADD  `activated` TINYINT( 1 ) NOT NULL DEFAULT  '1';


INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('campaign_activated', 'ynfundraising', '{item:$subject} has just activated your campaign {item:$object}.', 0, ''),
('campaign_inactivated', 'ynfundraising', '{item:$subject} has just inactivated your campaign {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_campaign_activated', 'ynfundraising', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]'),
('notify_campaign_inactivated', 'ynfundraising', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]');
