<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Installer extends Engine_Package_Installer_Module {

    public function onPreinstall() {
        $getErrorMsg = $this->getVersion();
        if (!empty($getErrorMsg)) {
            return $this->_error($getErrorMsg);
        }

        $db = $this->getDb();
        $PRODUCT_TYPE = 'siteevent';
        $PLUGIN_TITLE = 'Siteevent';
        $PLUGIN_VERSION = '4.8.10p4';
        $PLUGIN_CATEGORY = 'plugin';
        $PRODUCT_DESCRIPTION = 'Advanced Events Plugin';
        $_PRODUCT_FINAL_FILE = 0;
        $SocialEngineAddOns_version = '4.8.9p15';
        $PRODUCT_TITLE = 'Advanced Events Plugin';
        $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
        $is_file = @file_exists($file_path);

        if (empty($is_file)) {
            include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
        } else {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
            $is_Mod = $select->query()->fetchObject();
            if (empty($is_Mod)) {
                include_once $file_path;
            }
        }
        parent::onPreinstall();
    }

    function onInstall() {

        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteevent')
                ->where('version <= ?', '4.8.9');
        $version_check = $select->query()->fetchObject();
        if (!empty($version_check)) {
            $select = new Zend_Db_Select($db);
            $calendarValue = $select
                    ->from('engine4_core_settings', array('value'))
                    ->where('name =?', 'siteevent.calendar.daystart')
                    ->query()
                    ->fetchColumn();

            $db->query("UPDATE  `engine4_core_settings` SET  `value` =  '$calendarValue' WHERE  `engine4_core_settings`.`name` =  'seaocore.calendar.daystart' LIMIT 1 ;");
        }
        $db->update('engine4_core_menuitems', array('label' => 'Announcements'), array('name = ?' => 'siteevent_dashboard_announcements'));
        $db->update('engine4_core_menuitems', array('menu' => 'siteevent_dashboard_content', 'order' => 95), array('name = ?' => 'siteevent_dashboard_editmetakeyword'));
            
        //START: PUT CALENDER WIDGET AUTOMATICALLY AT SITEEVENT PROFILE PAGE
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'siteevent_index_view')
            ->limit(1);
        $page_id = $select->query()->fetchObject()->page_id;
        if (!empty($page_id)) {

            $select = new Zend_Db_Select($db);
            $select_content = $select
                ->from('engine4_core_content')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'siteevent.add-to-my-calendar-siteevent')
                ->limit(1);
            $content_id = $select_content->query()->fetchObject()->content_id;
            if (empty($content_id)) {
                $select = new Zend_Db_Select($db);
                $select_right = $select
                    ->from('engine4_core_content')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'right')
                    ->limit(1);
                $right_id = $select_right->query()->fetchObject()->content_id;
                if (!empty($right_id)) {
                    $select = new Zend_Db_Select($db);
                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'siteevent.add-to-my-calendar-siteevent',
                        'parent_content_id' => $right_id,
                        'order' => 5,
                        'params' => '{"title":"","calendarOptions":["google","iCal","outlook","yahoo"],"nomobile":"0","name":"siteevent.add-to-my-calendar-siteevent"}',
                    ));
                }
                
            } 
        }
        //END: PUT CALENDER WIDGET AUTOMATICALLY AT SITEEVENT PROFILE PAGE 
        $db->query('UPDATE  `engine4_activity_actiontypes` SET  `body` =  \'{itemParent:$object} has changed the title of the event {var:$oldtitle} to {item:$object}.\' WHERE  `engine4_activity_actiontypes`.`type` = \'siteevent_title_updated_parent\' LIMIT 1 ;');
            
        $db->query('UPDATE  `engine4_activity_actiontypes` SET  `body` =  \'{item:subject} has changed the title of the event {item:$object}.\' WHERE  `engine4_activity_actiontypes`.`type` = \'siteevent_title_updated\' LIMIT 1 ;');
            
        //START CAPACITY & WAITLIST WORK
        $isWaitlistTableExist = $db->query("SHOW TABLES LIKE 'engine4_siteevent_waitlists'")->fetch();          if(empty($isWaitlistTableExist)) {
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_siteevent_waitlists` (
  `waitlist_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `occurrence_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`waitlist_id`),
  UNIQUE KEY `occurrence_id` (`occurrence_id`, `user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");
        }  
        
        $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('siteevent_dashboard_waitlist', 'siteevent', 'Capacity & Waitlist', 'Siteevent_Plugin_Dashboardmenus', '', 'siteevent_dashboard_content', NULL, 1, 0, 30)");    
        
        $siteevent_capacity_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_events LIKE 'capacity'")->fetch();
        if (empty($siteevent_capacity_column)) {
          $db->query("ALTER TABLE `engine4_siteevent_events` ADD `capacity` INT( 5 ) DEFAULT NULL");
        }        
        
        $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('NOTIFY_SITEEVENT_JOIN_WAITLIST', 'siteevent', '[host][email][[event_title][user_title_with_link][event_title_with_link]');"); 
        
        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("SITEEVENT_JOIN_WAITLIST", "siteevent", "{item:$subject} has joined the waitlist for {item:$object}.", 0, "");');
        
        //ADDING COLUMNS TO ADVANCED EVENTS OCCURRENCE TABLES
        $siteevent_occurrences_flag_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_occurrences LIKE 'waitlist_flag'")->fetch();
        if (empty($siteevent_occurrences_flag_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_occurrences`  ADD `waitlist_flag` TINYINT(1) NOT NULL DEFAULT '0'");
            
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'siteevent')
                    ->where('version <= ?', '4.8.8p3');
            $version_check = $select->query()->fetchObject();
            if (!empty($version_check)) {
                $select = new Zend_Db_Select($db);
                $waitlists = $select
                        ->from('engine4_siteevent_waitlists', array('occurrence_id'))
                        ->group('occurrence_id')
                        ->query()
                        ->fetchAll();

                foreach ($waitlists as $waitlist) {
                    
                    $occurrence_id = $waitlist['occurrence_id'];
                    
                    $select = new Zend_Db_Select($db);
                    $event_id = $select->from('engine4_siteevent_occurrences', 'event_id')
                                        ->where('occurrence_id = ?', $occurrence_id)
                                        ->query()
                                        ->fetchcolumn();                    
                    
                    $select = new Zend_Db_Select($db);
                    $capacity = $select->from('engine4_siteevent_events', 'capacity')
                                        ->where('event_id = ?', $event_id)
                                        ->query()
                                        ->fetchcolumn();
                    
                    
                    if(!empty($capacity) && $occurrence_id) {
                        $db->update('engine4_siteevent_occurrences', array('waitlist_flag' => 1), array('occurrence_id = ?' => $occurrence_id));
                    }
                }
            }
        }         
        //END CAPACITY & WAITLIST WORK
        
        // Mobile compatibility work for Advanced Event Creation
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobileapp')
                ->where('enabled = ?', 1);
        $is_sitemobileapp_object = $select->query()->fetchObject();
        
        if (false && !empty($is_sitemobile_object)) {
            
            $db->query("INSERT IGNORE INTO `engine4_sitemobile_navigation` (`name`, `menu`, `subject_type`) VALUES ('siteevent', 'siteevent_quick', '')");

            $db->query("INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES ('siteevent_quick_create', 'siteevent', 'Create New Event', 'Siteevent_Plugin_Menus::canCreateSiteevents', '{\"route\":\"siteevent_general\",\"action\":\"create\"}', 'siteevent_quick', NULL, '0', '999', '1', '1');");

            $db->query("INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES ('siteevent_dashboard_editmetadetails', 'siteevent', 'Edit Meta Keywords', 'Siteevent_Plugin_Dashboardmenus', '{\"route\":\"siteevent_dashboard\",\"action\":\"meta-detail\"}', 'siteevent_quick', NULL, '0', '6', '1', '1');");

            $db->query("INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES ('siteevent_dashboard_editlocation', 'siteevent', 'Edit Location', 'Siteevent_Plugin_Dashboardmenus', '{\"route\":\"siteevent_specific\",\"action\":\"editlocation\"}', 'siteevent_quick', NULL, '0', '5', '1', '1');");

            $db->query("INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES ('siteevent_dashboard_contact', 'siteevent', 'Edit Contact', 'Siteevent_Plugin_Dashboardmenus', '{\"route\":\"siteevent_dashboard\",\"action\":\"contact\"}', 'siteevent_quick', NULL, '0', '4', '1', '1');");

            $db->query("INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES ('siteevent_dashboard_editphoto', 'siteevent', 'Edit Photos', 'Siteevent_Plugin_Dashboardmenus', '{\"route\":\"siteevent_albumspecific\"}', 'siteevent_quick', NULL, '0', '3', '1', '1');");

            $db->query("INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES ('siteevent_dashboard_editvideo', 'siteevent', 'Edit Videos', 'Siteevent_Plugin_Dashboardmenus', '{\"route\":\"siteevent_videospecific\"}', 'siteevent_quick', NULL, '0', '2', '1', '1');");


            $db->query("INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES ('siteevent_gutter_edit', 'siteevent', 'Edit Event Details', 'Siteevent_Plugin_Menus::siteeventGutterEdit', '', 'siteevent_gutter', NULL, '0', '1', '1', '1');");
            
            $this->eventPageCreate('engine4_sitemobile_pages', 'engine4_sitemobile_content');
            $this->eventPageCreate('engine4_sitemobile_tablet_pages', 'engine4_sitemobile_tablet_content');
            $this->eventPageEdit('engine4_sitemobile_pages', 'engine4_sitemobile_content');
            $this->eventPageEdit('engine4_sitemobile_tablet_pages', 'engine4_sitemobile_tablet_content');
        }
        if (false && !empty($is_sitemobileapp_object)) {
            $this->eventPageCreate('engine4_sitemobileapp_pages', 'engine4_sitemobileapp_content');
            $this->eventPageCreate('engine4_sitemobileapp_tablet_pages', 'engine4_sitemobileapp_tablet_content');
            $this->eventPageEdit('engine4_sitemobileapp_pages', 'engine4_sitemobileapp_content');
            $this->eventPageEdit('engine4_sitemobileapp_tablet_pages', 'engine4_sitemobileapp_tablet_content');
        }


        $db->query("UPDATE `engine4_core_menuitems` SET  `plugin` =  '' WHERE  `engine4_core_menuitems`.`name` ='core_main_siteevent' LIMIT 1 ;");

        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='siteevent';");

        $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('SITEEVENT_CONFIRM_GUEST', 'siteevent', '[host],[email],[object_title],[sender],[object_link],[object_description],[object_title_with_link]');");

        $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('SITEEVENT_NONCONFIRM_GUEST', 'siteevent', '[host],[email],[object_title],[sender],[object_link],[object_description],[object_title_with_link]');");

        $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEEVENT_GUEST_REMOVED", "siteevent", "[host],[object_title],[object_link],[object_description],[sender]");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES("siteevent_request_disapprove", "siteevent", \'Your request to join the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id} has been rejected.\', "");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES("siteevent_userreview_add", "siteevent", \'{item:$subject} has written a {item:$object:review} for the {itemParent:$object::user}.\', "");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES("siteevent_userreview_add", "siteevent", \'{item:$subject} has written a {item:$object:review} for the {itemParent:$object::user}.\', "");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES("siteevent_rate_participants", "siteevent", \'You can now rate the other participants to the event {item:$object}.\', "");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES("siteevent_reject_guests", "siteevent", \'{item:$subject} has not confirmed you for the event {item:$object}.\', "");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES("siteevent_confirm_guests", "siteevent", \'{item:$subject} has confirmed you for the event {item:$object}.\', "");');

        $confirmTypeColumn = $db->query("SHOW COLUMNS FROM `engine4_siteevent_membership` LIKE 'confirm'")->fetch();
        if (empty($confirmTypeColumn)) {
            $db->query("ALTER TABLE `engine4_siteevent_membership` ADD `confirm` TINYINT( 1 ) NOT NULL DEFAULT  '0';");
        }

        $db->query("UPDATE  `engine4_activity_notificationtypes` SET  `is_request` =  '1' WHERE  `engine4_activity_notificationtypes`.`type` =  'siteevent_invite' LIMIT 1 ;");

        $db->query("UPDATE  `engine4_activity_notificationtypes` SET  `handler` =  'siteevent.widget.request-event' WHERE  `engine4_activity_notificationtypes`.`type` =  'siteevent_suggested' LIMIT 1 ;");

        $db->query("UPDATE  `engine4_activity_notificationtypes` SET  `is_request` =  '1' WHERE  `engine4_activity_notificationtypes`.`type` =  'siteevent_approve' LIMIT 1 ;");

        $db->query("UPDATE  `engine4_activity_notificationtypes` SET  `handler` =  'siteevent.widget.approve-event' WHERE  `engine4_activity_notificationtypes`.`type` =  'siteevent_approve' LIMIT 1 ;");


        $db->query("UPDATE  `engine4_activity_notificationtypes` SET  `is_request` =  '1' WHERE  `engine4_activity_notificationtypes`.`type` =  'siteevent_suggested' LIMIT 1 ;");

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'communityad')
                ->where('enabled = ?', 1);
        $is_siteevent_object = $select->query()->fetchObject();
        if (!empty($is_siteevent_object)) {
            $db->query("DELETE FROM `engine4_communityad_modules` WHERE `engine4_communityad_modules`.`module_name` = 'event' LIMIT 1");
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_actionsettings LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_actionsettings` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_actiontypes LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_actiontypes` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        $type_array = $db->query("SHOW COLUMNS FROM engine4_siteevent_otherinfo LIKE 'guest_lists'")->fetch();
        if (empty($type_array)) {
            $db->query("ALTER TABLE  `engine4_siteevent_otherinfo` ADD  `guest_lists` TINYINT( 1 ) NOT NULL DEFAULT  '1';");
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_actions FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_actions LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_actions` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_stream FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_stream LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_stream` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'name'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `name` `name` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_allow's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_allow LIKE 'resource_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_allow` CHANGE `resource_type` `resource_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CHANGE IN CORE COMMENT TABLE
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_core_comments'")->fetch();
        if (!empty($table_exist)) {
            $column_exist = $db->query("SHOW COLUMNS FROM `engine4_core_comments` LIKE 'parent_comment_id'")->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE  `engine4_core_comments` ADD  `parent_comment_id` INT( 11 ) NOT NULL DEFAULT  '0';");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_allow's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_allow LIKE 'action'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_allow` CHANGE `action` `action` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitecontentcoverphoto')
                ->where('enabled = ?', 1);
        $is_sitecontentcoverphoto_object = $select->query()->fetchObject();

        if (!empty($is_sitecontentcoverphoto_object)) {
            $db->query('INSERT IGNORE INTO `engine4_sitecontentcoverphoto_modules` (`module`, `resource_type`, `resource_id`, `enabled`) VALUES ("siteevent", "siteevent_event", "event_id", 1)');

            $contentType = 'sitecontentcoverphoto_siteevent_event';
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $db->query("
						INSERT IGNORE INTO `engine4_authorization_permissions` 
						SELECT 
									level_id as `level_id`, 
									'$contentType' as `type`, 
									'upload' as `name`, 
									1 as `value`, 
									NULL as `params` 
						FROM `engine4_authorization_levels` WHERE `type` IN('moderator','admin','user');
					");
        }

        $importTable = $db->query("SHOW TABLES LIKE 'engine4_siteevent_imports'")->fetch();
        if (!empty($importTable) && !empty($importfileTable)) {
            $db->query("DROP TABLE `engine4_siteevent_imports`");
        }

        $importfileTable = $db->query("SHOW TABLES LIKE 'engine4_siteevent_importfiles'")->fetch();
        if (!empty($importfileTable)) {
            $db->query("DROP TABLE `engine4_siteevent_importfiles`");
        }

        $subscriptionTable = $db->query("SHOW TABLES LIKE 'engine4_siteevent_subscriptions'")->fetch();
        if (!empty($subscriptionTable)) {
            $db->query("DROP TABLE `engine4_siteevent_subscriptions`");
        }

        $db->query("DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'siteevent_gutter_subscription' LIMIT 1");

        $reviewsTable = $db->query("SHOW TABLES LIKE 'engine4_siteevent_reviews'")->fetch();
        if (!empty($reviewsTable)) {

            $anonymousNameColumn = $db->query("SHOW COLUMNS FROM `engine4_siteevent_reviews` LIKE 'anonymous_name'")->fetch();
            if (!empty($anonymousNameColumn)) {
                $db->query("ALTER TABLE `engine4_siteevent_reviews` DROP `anonymous_name`");
            }

            $anonymousEmailColumn = $db->query("SHOW COLUMNS FROM `engine4_siteevent_reviews` LIKE 'anonymous_email'")->fetch();
            if (!empty($anonymousEmailColumn)) {
                $db->query("ALTER TABLE `engine4_siteevent_reviews` DROP `anonymous_email`");
            }
        }

        //ADD A COLUMN IN SITEEVENT_MODULE TALBE.
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_siteevent_modules'")->fetch();
        if (!empty($table_exist)) {
            $itemMemberTypeColumn = $db->query("SHOW COLUMNS FROM `engine4_siteevent_modules` LIKE 'item_membertype'")->fetch();
            if (empty($itemMemberTypeColumn)) {
                $db->query("ALTER TABLE `engine4_siteevent_modules` ADD `item_membertype` VARCHAR( 255 ) NOT NULL AFTER `item_title`");
                $db->update('engine4_siteevent_modules', array(
                    'item_membertype' => 'a:3:{i:0;s:14:"contentmembers";i:1;s:18:"contentlikemembers";i:2;s:20:"contentfollowmembers";}',
                        ), array(
                    'item_membertype = ?' => '',
                ));
            }
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitepage')
                ->where('enabled = ?', 1);
        $is_sitepage_object = $select->query()->fetchObject();
        if ($is_sitepage_object) {
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES("siteevent_page_host", "siteevent", \'{item:$subject} has made your page {var:$page} host of the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.\', "");');

            $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES("SITEEVENT_PAGE_HOST", "siteevent", "[host],[email],[sender],[event_title_with_link],[event_url],[page_title_with_link]");');

            $db->query("INSERT IGNORE INTO `engine4_siteevent_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitepage_page', 'page_id', 'sitepage', '0', '0', 'Page Events', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");

            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitepage_admin_main_manage", "siteevent", "Manage Events", "", \'{"uri":"admin/siteevent/manage/index/contentType/sitepage_page/contentModule/sitepage"}\', "sitepage_admin_main", "", 1, 0, 24);');
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitebusiness')
                ->where('enabled = ?', 1);
        $is_sitebusiness_object = $select->query()->fetchObject();
        if ($is_sitebusiness_object) {
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES
("siteevent_business_host", "siteevent", \'{item:$subject} has made your business {var:$business} host of the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.\', "")');

            $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES("SITEEVENT_BUSINESS_HOST", "siteevent", "[host],[email],[sender],[event_title_with_link],[event_url],[business_title_with_link]");');

            $db->query("INSERT IGNORE INTO `engine4_siteevent_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitebusiness_business', 'business_id', 'sitebusiness', '0', '0', 'Business Events', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");

            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitebusiness_admin_main_manage", "siteevent", "Manage Events", "", \'{"uri":"admin/siteevent/manage/index/contentType/sitebusiness_business/contentModule/sitebusiness"}\', "sitebusiness_admin_main", "", 1, 0, 24);');
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitegroup')
                ->where('enabled = ?', 1);
        $is_sitegroup_object = $select->query()->fetchObject();
        if ($is_sitegroup_object) {
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES
("siteevent_group_host", "siteevent", \'{item:$subject} has made your group {var:$group} host of the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.\', "")');

            $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES("SITEEVENT_GROUP_HOST", "siteevent", "[host],[email],[sender],[event_title_with_link],[event_url],[group_title_with_link]");');

            $db->query("INSERT IGNORE INTO `engine4_siteevent_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitegroup_group', 'group_id', 'sitegroup', '0', '0', 'Group Events', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");

            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitegroup_admin_main_manage", "siteevent", "Manage Events", "", \'{"uri":"admin/siteevent/manage/index/contentType/sitegroup_group/contentModule/sitegroup"}\', "sitegroup_admin_main", "", 1, 0, 24);');
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitestore')
                ->where('enabled = ?', 1);
        $is_sitestore_object = $select->query()->fetchObject();
        if ($is_sitestore_object) {
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES
("siteevent_store_host", "siteevent", \'{item:$subject} has made your store {var:$store} host of the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.\', "")');

            $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES("SITEEVENT_STORE_HOST", "siteevent", "[host],[email],[sender],[event_title_with_link],[event_url],[store_title_with_link]");');

            $db->query("INSERT IGNORE INTO `engine4_siteevent_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitestore_store', 'store_id', 'sitestore', '0', '0', 'Store Events', 'a:2:{i:0;s:18:\"contentlikemembers\";i:1;s:20:\"contentfollowmembers\";}')");

            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitestore_admin_main_manage", "siteevent", "Manage Events", "", \'{"uri":"admin/siteevent/manage/index/contentType/sitestore_store/contentModule/sitestore"}\', "sitestore_admin_main", "", 1, 0, 33);');
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitereview')
                ->where('enabled = ?', 1);
        $is_sitereview_object = $select->query()->fetchObject();
        if (!empty($is_sitereview_object)) {
            $select = new Zend_Db_Select($db);
            $listingtypeObject = $select
                    ->from('engine4_sitereview_listingtypes', array('listingtype_id', 'title_singular'))
                    ->query()
                    ->fetchAll();
            foreach ($listingtypeObject as $values) {
                $listingtype_id = $values['listingtype_id'];
                $singular_title = ucfirst($values['title_singular']);
                $db->query("INSERT IGNORE INTO `engine4_siteevent_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitereview_listing_$listingtype_id', 'listing_id', 'sitereview', '0', '0', '$singular_title Events', 'a:1:{i:0;s:18:\"contentlikemembers\";}')");
            }

            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitereview_admin_main_manageevent", "siteevent", "Manage Events", "", \'{"uri":"admin/siteevent/manage/index/contentType/sitereview_listing_1/contentModule/sitereview"}\', "sitereview_admin_main", "", 1, 0, 83);');
        }

        $column_exist_follow_count = $db->query('SHOW COLUMNS FROM engine4_siteevent_events LIKE \'follow_count\'')->fetch();
        if (empty($column_exist_follow_count)) {
            $db->query("ALTER TABLE `engine4_siteevent_events` ADD `follow_count` int(11) NOT NULL");
        }

        //START FOLLOW WORK
        //IF 'engine4_seaocore_follows' TABLE IS NOT EXIST THAN CREATE'
        $seocoreFollowTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_follows\'')->fetch();
        if (empty($seocoreFollowTable)) {
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_seaocore_follows` (
        `follow_id` int(11) unsigned NOT NULL auto_increment,
        `resource_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
        `resource_id` int(11) unsigned NOT NULL,
        `poster_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
        `poster_id` int(11) unsigned NOT NULL,
        `creation_date` datetime NOT NULL,
        PRIMARY KEY  (`follow_id`),
        KEY `resource_type` (`resource_type`, `resource_id`),
        KEY `poster_type` (`poster_type`, `poster_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;");
        }

        $userReviewsTable = $db->query('SHOW TABLES LIKE \'engine4_siteevent_userreviews\'')->fetch();
        if (empty($userReviewsTable)) {
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_siteevent_userreviews` (
  `userreview_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `viewer_id` int(10) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `rating` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`userreview_id`),
  UNIQUE KEY `event_id` (`event_id`,`user_id`,`viewer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
        }

        $select = new Zend_Db_Select($db);
        $advancedactivity = $select->from('engine4_core_modules', 'name')
                ->where('name = ?', 'advancedactivity')
                ->query()
                ->fetchcolumn();

        $is_enabled = $select->query()->fetchObject();
        if (!empty($is_enabled)) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`) VALUES ("follow_siteevent_event", "siteevent", \'{item:$subject} is following {item:$owner}\'\'s {item:$object:event}: {body:$body}\', 1, 5, 1, 1, 1, 1, 1)');

            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`, `is_object_thumb`) VALUES ("siteevent_change_photo_parent", "siteevent", \'{itemParent:$object} changed the profile picture of the event {item:$object:$title}:\', 1, 2, 2, 1, 1, 1, 0, 2)');

            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`, `is_object_thumb`) VALUES ("siteevent_cover_update_parent", "siteevent", \'{itemParent:$object} updated cover photo of the event {item:$object}:\', 1, 2, 2, 1, 1, 1, 0, 2)');

            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`, `is_object_thumb`) VALUES ("siteevent_photo_upload_parent", "siteevent", \'{itemParent:$object} added {var:$count} photo(s) to the event {item:$object:$title}: {body:$body}\', 1, 6, 2, 1, 1, 1, 0, 2)');

            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`, `is_object_thumb`) VALUES ("siteevent_post_parent", "siteevent", \'{itemParent:$object}:{body:$body}\', 1, 2, 1, 1, 1, 1, 0, 2)');

            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`, `is_object_thumb`) VALUES ("siteevent_topic_create_parent", "siteevent", \'{itemParent:$object} posted a discussion topic {itemSeaoChild:$object:siteevent_topic:$child_id} in the event {item:$object}:{body:$body}\', 1, 6, 2, 1, 1, 1, 0, 2)');

            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`, `is_object_thumb`) VALUES ("siteevent_video_new_parent", "siteevent", \'{itemParent:$object} added a new video to the event {item:$object}:\', 1, 2, 1, 1, 1, 1, 0, 2)');

            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`, `is_object_thumb`) VALUES ("video_siteevent_parent", "siteevent", \'{itemParent:$object} added a new video to the event {item:$object:$title}: {body:$body}\', 0, 6, 2, 1, 1, 1, 0, 2)');
        } else {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("follow_siteevent_event", "sitepage", \'{item:$subject} is following {item:$owner}\'\'s {item:$object:page}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        }
        //END FOLLOW WORK


        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteevent')
                ->where('version < ?', '4.7.1p3');
        $version_check = $select->query()->fetchObject();
        if (!empty($version_check)) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_activity_stream', "action_id")->where('type = ?', 'siteevent_join')->orWhere('type = ?', 'siteevent_leave')->orWhere('type = ?', 'siteevent_leave')->orWhere('type = ?', 'siteevent_maybe_join')->orWhere('type = ?', 'siteevent_mid_join')->orWhere('type = ?', 'siteevent_mid_leave')->orWhere('type = ?', 'siteevent_mid_maybe')->group('action_id');
            $str_action_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            if ($str_action_ids) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_activity_actions', "action_id")->where('type = ?', 'siteevent_join')->orWhere('type = ?', 'siteevent_leave')->orWhere('type = ?', 'siteevent_leave')->orWhere('type = ?', 'siteevent_maybe_join')->orWhere('type = ?', 'siteevent_mid_join')->orWhere('type = ?', 'siteevent_mid_leave')->orWhere('type = ?', 'siteevent_mid_maybe')->where('action_id IN(?)', $str_action_ids);
                $action_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
                $diff_action_ids = array_diff($str_action_ids, $action_ids);
                if ($diff_action_ids) {
                    $db->delete('engine4_activity_stream', array('action_id IN(?)' => $diff_action_ids));
                }
            }
        }

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("siteevent_title_updated", "siteevent", \'{item:$subject} has changed the title of the event {var:$oldtitle} to {item:$object}.\', "1", "3", "1", "1", "1", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ("siteevent_title_updated", "siteevent", \'{item:$subject} has updated title of the event {item:$object}.\', "0", "", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ("siteevent_title_updated_parent", "siteevent", \'{item:$subject} has updated title of the event {item:$object}.\', "0", "", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("siteevent_location_updated", "siteevent", \'{item:$subject} has changed the location of the event {item:$object} to {var:$newlocation}.\', "1", "3", "1", "1", "1", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ("siteevent_location_updated", "siteevent", \'{item:$subject} has updated location of the event {item:$object}.\', "0", "", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ("siteevent_location_updated_parent", "siteevent", \'{item:$subject} has updated location of the event {item:$object}.\', "0", "", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("siteevent_venue_updated", "siteevent", \'{item:$subject} has changed the venue of the event {item:$object} to {var:$newvenue}.\', "1", "3", "1", "1", "1", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ("siteevent_venue_updated", "siteevent", \'{item:$subject} has updated venue of the event {item:$object}.\', "0", "", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ("siteevent_venue_updated_parent", "siteevent", \'{item:$subject} has updated venue of the event {item:$object}.\', "0", "", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("siteevent_date_time_updated", "siteevent", \'{item:$subject} has changed the time of the event {item:$object} to be {var:$starttime} - {var:$endtime}.\', "1", "3", "1", "1", "1", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("siteevent_date_time_extended", "siteevent", \'{item:$subject} has extended the event {item:$object} to {var:$newtime}.\', "1", "3", "1", "1", "1", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ("siteevent_date_time_updated", "siteevent", \'{item:$subject} has updated time of the event {item:$object}.\', "0", "", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ("siteevent_date_time_updated_parent", "siteevent", \'{item:$subject} has updated time of the event {item:$object}.\', "0", "", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ("siteevent_notificationpost_parent", "siteevent", \'{item:$subject} posted in the {item:$object:event}.\', "0", "", "1");');

        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_activity_actiontypes'")->fetch();
        if (!empty($table_exist)) {
            $widgetAdminColumn = $db->query("SHOW COLUMNS FROM `engine4_activity_actiontypes` LIKE 'is_object_thumb'")->fetch();
            if (empty($widgetAdminColumn)) {
                $db->query("ALTER TABLE `engine4_activity_actiontypes` ADD `is_object_thumb` BOOL NOT NULL DEFAULT '0'");
            }
        }

        $select = new Zend_Db_Select($db);
        $advancedactivity = $select->from('engine4_core_modules', 'name')
                ->where('name = ?', 'advancedactivity')
                ->query()
                ->fetchcolumn();

        $is_enabled = $select->query()->fetchObject();
        if (!empty($is_enabled)) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`, `is_object_thumb`) VALUES ("siteevent_date_time_extended_parent", "siteevent", \'{itemParent:$object} has extended the event {item:$object} to {var:$newtime}.\', "1", "2", "2", "1", "1", "1", "0", "2");');

            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`, `is_object_thumb`) VALUES ("siteevent_date_time_updated_parent", "siteevent", \'{itemParent:$object} has changed the location of the event {item:$object} to {var:$newlocation}.\', "1", "2", "2", "1", "1", "1", "0", "2");');

            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`, `is_object_thumb`) VALUES ("siteevent_location_updated_parent", "siteevent", \'{itemParent:$object} has changed the location of the event {item:$object} to {var:$newlocation}.\', "1", "2", "2", "1", "1", "1", "0", "2");');

            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`, `is_object_thumb`) VALUES ("siteevent_title_updated_parent", "siteevent", \'{itemParent:$object} has changed the title of the event {var:$oldtitle} to {item:$object}.\', "1", "2", "2", "1", "1", "1", "0", "2");');

             $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`, `is_object_thumb`) VALUES ("siteevent_title_updated_parent", "siteevent", \'{itemParent:$object} has changed the title of the event {var:$oldtitle} to {item:$object}.\', "1", "2", "2", "1", "1", "1", "0", "2");');
             
            
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`, `is_object_thumb`) VALUES ("siteevent_venue_updated_parent", "siteevent", \'{itemParent:$object} has changed the venue of the event {item:$object} to {var:$newvenue}.\', "1", "2", "2", "1", "1", "1", "0", "2");');

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'siteevent_event' as `type`,
						'post' as `name`,
						2 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'siteevent_event' as `type`,
						'post' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('user');");

            $db->query('
						INSERT IGNORE INTO `engine4_authorization_permissions` 
						SELECT level_id as `level_id`, 
							"siteevent_event" as `type`, 
							"auth_post" as `name`, 
							5 as `value`, 
							\'["registered","owner_network","owner_member_member","owner_member","like_member","member","leader"]\' as `params` 
						FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");
					');
        }

        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_siteevent_modules'")->fetch();
        if (!empty($table_exist)) {
            //ADD THE INDEX FROM THE "engine4_sitepageevent_membership" TABLE
            $itemModuleColumnIndex = $db->query("SHOW INDEX FROM `engine4_siteevent_modules` WHERE Key_name = 'item_module'")->fetch();

            if (empty($itemModuleColumnIndex)) {
                $db->query("ALTER TABLE `engine4_siteevent_modules` ADD INDEX ( `item_module` );");
            }
        }

        $table_exist = $db->query('SHOW TABLES LIKE \'engine4_siteevent_categories\'')->fetch();
        if (!empty($table_exist)) {
            $column_exist = $db->query('SHOW COLUMNS FROM engine4_siteevent_categories LIKE \'userreview\'')->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_siteevent_categories` ADD `userreview` TINYINT( 1 ) NOT NULL DEFAULT '0'");
            }
        }

        $table_exist = $db->query('SHOW TABLES LIKE \'engine4_siteevent_categories\'')->fetch();
        if (!empty($table_exist)) {
            $column_exist = $db->query('SHOW COLUMNS FROM engine4_siteevent_categories LIKE \'allow_guestreview\'')->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_siteevent_categories` ADD `allow_guestreview` TINYINT( 1 ) NOT NULL;");
            }
        }

        $db->query("INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`module`, `name`, `display`, `order`, `label`) VALUES ('siteevent', 'has_free_price', '0', '85', 'Only Free Events')");

        // Browse User Reviews
        $page_id = $db->select()
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'siteevent_userreview_view')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (!$page_id) {

            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'siteevent_userreview_view',
                'displayname' => 'Advanced Events - Browse User Reviews',
                'title' => 'Browse User Reviews',
                'description' => 'This is the user review browse page.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'siteevent.navigation-siteevent',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            ));
        }

        $this->_checkFfmpegPath();
        $this->_integrateWithSuggestionPlugin();
        $this->makeCalenderPage();

        $paramsObject = $db->select()
                ->from('engine4_activity_actiontypes', array('type', 'body'))
                ->where('module like ?', '%siteevent%')
                ->where('body like ?', '%itemChild%')
                ->query()
                ->fetchAll();

        foreach ($paramsObject as $params) {
            $type = $params['type'];
            $haystack = $params['body'];
            $needle = 'itemChild';
            if (strpos($haystack, $needle) !== false) {
                $params = str_replace($needle, 'itemSeaoChild', $haystack);
                $db->update('engine4_activity_actiontypes', array('body' => "$params"), array('type =?' => $type));
            }
        }

        $paramsObject = $db->select()
                ->from('engine4_activity_notificationtypes', array('type', 'body'))
                ->where('module like ?', '%siteevent%')
                ->where('body like ?', '%itemChild%')
                ->query()
                ->fetchAll();

        foreach ($paramsObject as $params) {
            $type = $params['type'];
            $haystack = $params['body'];
            $needle = 'itemChild';
            if (strpos($haystack, $needle) !== false) {
                $params = str_replace($needle, 'itemSeaoChild', $haystack);
                $db->update('engine4_activity_notificationtypes', array('body' => "$params"), array('type =?' => $type));
            }
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteevent')
                ->where('version <= ?', '4.8.3');
        $version_check = $select->query()->fetchObject();
        if (!empty($version_check)) {

            $previousText = '"locationDetection":"0"';
            $newText = '"locationDetection":"1"';
            $db->query("UPDATE `engine4_core_content` SET `params` = REPLACE(params, '$previousText', '$newText') WHERE (`name` = 'siteevent.browselocation-siteevent' OR `name` = 'siteevent.searchbox-siteevent' OR `name` = 'siteevent.search-siteevent') AND `params` Like '%$previousText%'");

            $previousText = '"detactLocation":"0"';
            $newText = '"detactLocation":"1"';
            $db->query("UPDATE `engine4_core_content` SET `params` = REPLACE(params, '$previousText', '$newText') WHERE `name` = 'siteevent.browse-events-siteevent' AND `params` Like '%$previousText%'");
        }
        $this->_changeWidgetParam($db, 'siteevent', '4.8.0p1');
        $this->setActivityFeeds();

        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_siteevent_videos'")->fetch();
        if (!empty($table_exist)) {
            $email_field = $db->query("SHOW COLUMNS FROM engine4_siteevent_videos LIKE 'rotation'")->fetch();
            if (empty($email_field)) {
                $db->query("ALTER TABLE `engine4_siteevent_videos` ADD `rotation` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0'");
            }
        }

        //START: UPGRADE QUERY TO ORDER CHANGE OF DASHBOARD MENUS       
        $menusArray = array('siteevent_dashboard_editinfo' => 10,'siteevent_dashboard_overview' => 20,'siteevent_dashboard_profilepicture' => 40,'siteevent_dashboard_contact' => 50,'siteevent_dashboard_editlocation' => 60,'siteevent_dashboard_editphoto' => 70,'siteevent_dashboard_editvideo' => 80,'siteevent_dashboard_announcements' => 90,'siteevent_dashboard_editmetakeyword' => 95,'siteevent_dashboard_editstyle' => 100);
        foreach($menusArray as $menu => $value) {
            $db->update('engine4_core_menuitems', array('order' => $value, 'menu' => 'siteevent_dashboard_content'), array('name = ?' => $menu));
        }

        $menusArray = array('siteevent_dashboard_manageleaders' => 120,'siteevent_dashboard_notificationsettings' => 130);                       
        foreach($menusArray as $menu => $value) {
            $db->update('engine4_core_menuitems', array('order' => $value, 'menu' => 'siteevent_dashboard_admin'), array('name = ?' => $menu));
        }
        
        $db->query("INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
        ('siteevent_dashboard_content', 'standard', 'Advanced Events - Dashboard Navigation (Content)'),
        ('siteevent_dashboard_admin', 'standard', 'Advanced Events - Dashboard Navigation (Admin)')");        
        //END: UPGRADE QUERY TO ORDER CHANGE OF DASHBOARD MENUS
        
        $db->update('engine4_core_pages', array('custom' => 0), array('name = ?' => 'siteevent_topic_view'));
          
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitevideointegration')
                ->where('enabled = ?', 1);
        $is_sitevideointegration_object = $select->query()->fetchObject();
        if ($is_sitevideointegration_object) {
          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_settings')
                  ->where('name = ?', 'siteevent.isActivate')
                  ->where('value = ?', 1);
          $siteevent_isActivate_object = $select->query()->fetchObject();
          if ($siteevent_isActivate_object) {

            $db->query("INSERT IGNORE INTO `engine4_sitevideo_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('siteevent_event', 'event_id', 'siteevent', '0', '0', 'Events Videos', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("siteevent_admin_main_managevideo", "sitevideointegration", "Manage Videos", "", \'{"uri":"admin/sitevideo/manage-video/index/contentType/siteevent_event/contentModule/siteevent"}\', "siteevent_admin_main", "", 0, 0, 45);');
            $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitevideo.video.leader.owner.siteevent.event", "1");');
            
            $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '0' WHERE `engine4_core_menuitems`.`name` = 'siteevent_admin_submain_general_tab'");
          }
        }
    
        parent::onInstall();
    }

    protected function _checkFfmpegPath() {

        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        //CHECK FFMPEG PATH FOR CORRECTNESS
        if (function_exists('exec') && function_exists('shell_exec') && extension_loaded("ffmpeg")) {

            //API IS NOT AVAILABLE
            //$ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
            $ffmpeg_path = $db->select()
                    ->from('engine4_core_settings', 'value')
                    ->where('name = ?', 'siteevent_video.ffmpeg.path')
                    ->limit(1)
                    ->query()
                    ->fetchColumn(0);

            $output = null;
            $return = null;
            if (!empty($ffmpeg_path)) {
                exec($ffmpeg_path . ' -version', $output, $return);
            }

            //TRY TO AUTO-GUESS FFMPEG PATH IF IT IS NOT SET CORRECTLY
            $ffmpeg_path_original = $ffmpeg_path;
            if (empty($ffmpeg_path) || $return > 0 || stripos(join('', $output), 'ffmpeg') === false) {
                $ffmpeg_path = null;

                //WINDOWS
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    // @todo
                }
                //NOT WINDOWS
                else {
                    $output = null;
                    $return = null;
                    @exec('which ffmpeg', $output, $return);
                    if (0 == $return) {
                        $ffmpeg_path = array_shift($output);
                        $output = null;
                        $return = null;
                        exec($ffmpeg_path . ' -version', $output, $return);
                        if (0 == $return) {
                            $ffmpeg_path = null;
                        }
                    }
                }
            }
            if ($ffmpeg_path != $ffmpeg_path_original) {
                $count = $db->update('engine4_core_settings', array(
                    'value' => $ffmpeg_path,
                        ), array(
                    'name = ?' => 'siteevent.video.ffmpeg.path',
                ));
                if ($count === 0) {
                    try {
                        $db->insert('engine4_core_settings', array(
                            'value' => $ffmpeg_path,
                            'name' => 'siteevent.video.ffmpeg.path',
                        ));
                    } catch (Exception $e) {
                        
                    }
                }
            }
        }
    }

    private function getVersion() {

        $db = $this->getDb();

        $errorMsg = '';
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'sitemobile' => '4.8.0p1',
            'advancedactivity' => '4.8.8p3'
        );

        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')
                    ->where('name = ?', "$key")
                    ->where('enabled = ?', 1);
            $isModEnabled = $select->query()->fetchObject();
            if (!empty($isModEnabled)) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_modules', array('title', 'version'))
                        ->where('name = ?', "$key")
                        ->where('enabled = ?', 1);
                $getModVersion = $select->query()->fetchObject();

                $isModSupport = $this->checkVersion($getModVersion->version, $value);
                if (empty($isModSupport)) {
                    $finalModules[] = $getModVersion->title;
                }
            }
        }

        foreach ($finalModules as $modArray) {
            $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Advanced Events Plugin".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
        }

        return $errorMsg;
    }
    private function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }
    // INTEGRATE EVENT PLUGIN WITH SUGGESTION
    protected function _integrateWithSuggestionPlugin() {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'suggestion')
                ->where('enabled = ?', '1');
        $sitestore_temp = $select->query()->fetchObject();
        if (!empty($sitestore_temp)) {
            $db->query('UPDATE `engine4_suggestion_module_settings` SET `enabled` = "0" WHERE `engine4_suggestion_module_settings`.`module` = "event" LIMIT 1');

            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name` , `module` , `label` , `plugin` ,`params`, `menu`, `enabled`, `custom`, `order`) VALUES ("siteevent_gutter_suggesttofriend", "suggestion", "Suggest to Friends", \'Siteevent_Plugin_Menus::showSiteeventSuggestToFriendLink\', \'{"route":"suggest_to_friend_link","class":"buttonlink icon_review_friend_suggestion smoothbox", "type":"popup"}\', "siteevent_gutter", 1, 0, 999 )');

            $select = new Zend_Db_Select($db);
            $select->from('engine4_activity_notificationtypes')->where('type = ?', 'siteevent_suggestion');
            $fetch = $select->query()->fetchObject();
            if (empty($fetch)) {
                $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`,`is_request`, `handler`, `default`)VALUES ("siteevent_suggestion", "suggestion", \'{item:$subject} has suggested to you a {item:$object:event}.\', "1", "suggestion.widget.get-notify", "1" )
');
            }

            $select = new Zend_Db_Select($db);
            $select->from('engine4_suggestion_module_settings')->where('module = ?', 'siteevent');
            $fetch = $select->query()->fetchObject();
            if (empty($fetch)) {
                $db->query('
INSERT IGNORE INTO `engine4_suggestion_module_settings` (`module`, `item_type`, `field_name`, `owner_field`, `item_title`, `button_title`, `enabled`, `notification_type`, `quality`, `link`, `popup`, `recommendation`, `default`, `settings`) VALUES
("siteevent", "siteevent_event", "event_id", "owner_id", "Events", "View Event", 1, "siteevent_suggestion", 0, 1, 1, 1, 1, \'a:0:{}\');
');
            }

            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_mailtemplates')->where('type = ?', 'notify_siteevent_suggestion');
            $fetch = $select->query()->fetchObject();
            if (empty($fetch)) {
                $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`)VALUES 
("notify_siteevent_suggestion", "suggestion", "[suggestion_sender], [suggestion_entity], [email], [link]"
);');
            }
        }
    }

    function onDisable() {
        $db = $this->getDb();      

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules', array('name'))
                ->where('enabled = ?', 1);
        $moduleData = $select->query()->fetchAll();

        $subModuleArray = array("siteeventdocument", "siteeventrepeat", "siteeventadmincontact", "siteeventemail", "siteeventinvite", "siteeventticket");

        foreach ($moduleData as $moduleName) {
            if (in_array($moduleName['name'], $subModuleArray)) {
                $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
                $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Note: Please disable all the integrated sub-modules of "Advanced Events" Plugin before disabling the "Advanced Events" Plugin itself.');
                echo "<div style='background-color: #E9F4FA;border-radius:7px 7px 7px 7px;float:left;overflow: hidden;padding:10px;'><div style='background:#FFFFFF;border:1px solid #D7E8F1;overflow:hidden;padding:20px;'><span style='color:red'>$error_msg1</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.</div></div>";
                die;
            }
        }

        parent::onDisable();
    }

    public function onPostInstall() {
        //SITEMOBILE CODE TO CALL MY.SQL ON POST INSTALL
        $moduleName = 'siteevent';
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if (!empty($is_sitemobile_object)) {
            $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES
('$moduleName','1')");
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_sitemobile_modules')
                    ->where('name = ?', $moduleName)
                    ->where('integrated = ?', 0);
            $is_sitemobile_object = $select->query()->fetchObject();
            if ($is_sitemobile_object) {
                $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
                $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
                if ($controllerName == 'manage' && $actionName == 'install') {
                    $view = new Zend_View();
                    $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    $redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/' . $moduleName . '/integrated/0/redirect/install');
                }
            }
        }
        //END - SITEMOBILE CODE TO CALL MY.SQL ON POST INSTALL
        //WORK FOR THE WORD CHANGES IN THE ADVANCED EVENT PLUGIN .CSV FILE.
        $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        if ($controllerName == 'manage' && ($actionName == 'install' || $actionName == 'query')) {
            $view = new Zend_View();
            $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            if ($actionName == 'install') {
                $redirector->gotoUrl($baseUrl . 'admin/siteevent/settings/language/redirect/install');
            } else {
                $redirector->gotoUrl($baseUrl . 'admin/siteevent/settings/language/redirect/query');
            }
        }
    }

    protected function _changeWidgetParam($db, $pluginName, $version) {

        $isModuleExist = $db->query("SELECT * FROM `engine4_core_modules` WHERE `name` = '$pluginName'")->fetch();
        if (!empty($isModuleExist)) {
            $curr_module_version = strcasecmp($isModuleExist['version'], $version);
            if ($curr_module_version < 0) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_content', array('params', 'content_id'))
                        ->where('name LIKE ?', '%' . $pluginName . '%');
                $results = $select->query()->fetchAll();

                foreach ($results as $result) {
                    if (strstr($result['params'], '"titleCount":}')) {
                        $result['params'] = str_replace('"titleCount":}', '"titleCount":true}', $result['params']);
                        $db->query('UPDATE `engine4_core_content` SET `params` = \' ' . $result['params'] . ' \' WHERE `engine4_core_content`.`content_id` = "' . $result['content_id'] . '" LIMIT 1 ;');
                    }
                }
            }
        }
    }

    public function makeCalenderPage() {

        $db = $this->getDb();
        $page_id = $db->select()
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'siteevent_index_calender')
                ->limit(1)
                ->query()
                ->fetchColumn();

        $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("siteevent_main_calender", "siteevent", "Calender", NULL, \'{"route":"siteevent_general","action":"calendar"}\' ,"siteevent_main", NULL, "1", "0", "9")');

        if (!$page_id) {

            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'siteevent_index_calender',
                'displayname' => 'Advanced Events - Calender',
                'title' => 'Calender',
                'description' => 'This is the event calender page.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'siteevent.navigation-siteevent',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'siteevent.calendarview-siteevent',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":true,"loaded_by_ajax":"1","siteevent_calendar_event_count":"1","siteevent_calendar_event_count_type":"1","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.calendarview-siteevent"}'
            ));
        }
    }

    public function setActivityFeeds() {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'nestedcomment')
                ->where('enabled = ?', 1);
        $is_nestedcomment_object = $select->query()->fetchObject();
        if ($is_nestedcomment_object) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_siteeventdocument_document", "siteeventdocument", \'{item:$subject} replied to a comment on {item:$owner}\'\'s event document {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("siteevent_activityreply", "siteevent", \'{item:$subject} has replied on {var:$eventname}.\', 0, "");');
        }
    }

    public function eventPageCreate($pageTable, $contentTable) {
        //CREATE PACKAGE BASED EVENT 
        $db = $this->getDb();
        $page_id = $db->select()
                ->from($pageTable, 'page_id')
                ->where('name = ?', "siteevent_index_create")
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (empty($page_id)) {

            $containerCount = 0;

            //CREATE PAGE
            $db->insert($pageTable, array(
                'name' => "siteevent_index_create",
                'displayname' => 'Advanced Events - Event Create Page',
                'title' => 'Advanced Event Create Page',
                'description' => 'This page allows users to create events.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            $db->insert($contentTable, array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert($contentTable, array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert($contentTable, array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN-MIDDLE CONTAINER
            $db->insert($contentTable, array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.navigation-siteevent',
                'parent_content_id' => $top_middle_id,
                'params' => '',
            ));

            $db->insert($contentTable, array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
    }

    public function eventPageEdit($pageTable, $contentTable) {
        $db = $this->getDb();
        $page_id = $db->select()
                ->from($pageTable, 'page_id')
                ->where('name = ?', "siteevent_index_edit")
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {

            $containerCount = 0;

            //CREATE PAGE
            $db->insert($pageTable, array(
                'name' => "siteevent_index_edit",
                'displayname' => 'Advanced Events - Event Edit Page',
                'title' => 'Event Edit Page',
                'description' => 'This page allows users to edit events.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            //TOP CONTAINER
            $db->insert($contentTable, array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert($contentTable, array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert($contentTable, array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN-MIDDLE CONTAINER
            $db->insert($contentTable, array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.navigation-siteevent',
                'parent_content_id' => $top_middle_id,
                'params' => '',
            ));

            $db->insert($contentTable, array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
    }

}
