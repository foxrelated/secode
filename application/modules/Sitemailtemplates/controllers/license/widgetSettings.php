<?php
  $db = Zend_Db_Table_Abstract::getDefaultAdapter() ;

	$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
	("sitemailtemplates_admin_main_templates", "sitemailtemplates", "Mail Templates", "", \'{"route":"admin_default","controller":"mail","action":"templates"}\', "sitemailtemplates_admin_main", "", 3),
	("sitemailtemplates_admin_main_mail", "sitemailtemplates", "Email Members", "", \'{"route":"admin_default","module":"sitemailtemplates","controller":"message","action":"mail"}\', "sitemailtemplates_admin_main", "", 4),
	("sitemailtemplates_admin_main_manage", "sitemailtemplates", "Manage Templates", "", \'{"route":"admin_default","module":"sitemailtemplates","controller":"settings","action":"manage"}\', "sitemailtemplates_admin_main", "", 2),
	("sitemailtemplates_admin_main_faq", "sitemailtemplates", "FAQ", "", \'{"route":"admin_default","module":"sitemailtemplates","controller":"settings","action":"faq"}\', "sitemailtemplates_admin_main", "", 5);');

	$db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES
	("SITEMAILTEMPLATES_CONTACTS_EMAIL_NOTIFICATION", "sitemailtemplates", "[host],[email],[recipient_title],[subject],[message],[site_title]");');

	$db->query("UPDATE `engine4_core_mailtemplates` SET `enable_template` = '0' WHERE `engine4_core_mailtemplates`.`type` ='SITEPAGEADMINCONTACT_CONTACTS_EMAIL_NOTIFICATION' LIMIT 1 ;");

	$db->query("UPDATE `engine4_core_mailtemplates` SET `enable_template` = '0' WHERE `engine4_core_mailtemplates`.`type` ='SITEBUSINESSADMINCONTACT_CONTACTS_EMAIL_NOTIFICATION' LIMIT 1 ;");

	$db->query("UPDATE `engine4_core_mailtemplates` SET `enable_template` = '0' WHERE `engine4_core_mailtemplates`.`type` ='SITEPAGE_INSIGHTS_EMAIL_NOTIFICATION' LIMIT 1 ;");

	$db->query("UPDATE `engine4_core_mailtemplates` SET `enable_template` = '0' WHERE `engine4_core_mailtemplates`.`type` ='SITEBUSINESS_INSIGHTS_EMAIL_NOTIFICATION' LIMIT 1 ;");

	$db->query("UPDATE `engine4_core_mailtemplates` SET `enable_template` = '0' WHERE `engine4_core_mailtemplates`.`type` ='birthday_reminder' LIMIT 1 ;");

	$db->query("UPDATE `engine4_core_mailtemplates` SET `enable_template` = '0' WHERE `engine4_core_mailtemplates`.`type` ='birthday_wish' LIMIT 1 ;");