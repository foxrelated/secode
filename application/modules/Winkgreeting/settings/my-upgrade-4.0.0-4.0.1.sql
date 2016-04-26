UPDATE `engine4_core_mailtemplates` SET `vars` = '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_link]' WHERE `type` = 'notify_wink_new';

UPDATE `engine4_core_mailtemplates` SET `vars` = '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_link]' WHERE `type` = 'notify_greeting_new';

UPDATE `engine4_authorization_permissions` SET `value` = '0' WHERE `level_id` = 5 AND `type` = 'winkgreeting';