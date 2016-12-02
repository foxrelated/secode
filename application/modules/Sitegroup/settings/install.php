<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Installer extends Engine_Package_Installer_Module {

    function onPreInstall() {

        $db = $this->getDb();

        $getErrorMsg = $this->getVersion();
        if (!empty($getErrorMsg)) {
            return $this->_error($getErrorMsg);
        }

        $PRODUCT_TYPE = 'sitegroup';
        $PLUGIN_TITLE = 'Sitegroup';
        $PLUGIN_VERSION = '4.8.12p3';
        $PLUGIN_CATEGORY = 'plugin';
        $PRODUCT_DESCRIPTION = 'Sitegroup Plugin';
        $PRODUCT_TITLE = 'Groups / Communities Plugin';
        $_PRODUCT_FINAL_FILE = 0;
        $SocialEngineAddOns_version = '4.8.11';
        $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
        $is_file = file_exists($file_path);
        if (empty($is_file)) {
            include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license4.php";
        } else {
            include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
            $db = $this->getDb();
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
            $is_Mod = $select->query()->fetchObject();
            if (empty($is_Mod)) {
                include_once $file_path;
            }
        }
        parent::onPreInstall();
    }

    function onInstall() {

        $db = $this->getDb();
        $db->query('UPDATE `engine4_activity_actiontypes` SET `body` = \'{item:$object} added {var:$count} photo(s) to the album {itemChild:$object:sitegroup_album:$child_id}:\' WHERE `engine4_activity_actiontypes`.`type` = \'sitegroupalbum_admin_photo_new\' LIMIT 1 ;');

        $db->query('UPDATE `engine4_activity_actiontypes` SET `body` = \'{item:$subject} added {var:$count} photo(s) to the album {itemChild:$object:sitegroup_album:$child_id} of group {item:$object}:\' WHERE `engine4_activity_actiontypes`.`type` = \'sitegroupalbum_photo_new\' LIMIT 1 ;');

        $select = new Zend_Db_Select($db);
        $select->from('engine4_activity_actions', array('object_id', 'params', 'action_id'))
                ->where('type =?', 'sitegroupalbum_admin_photo_new')
                ->orWhere('type =?', 'sitegroupalbum_photo_new');
        $results = $select->query()->fetchAll();

        foreach ($results as $result) {
            if (strstr($result['params'], 'slug')) {
                $decoded_cover_param = Zend_Json_Decoder::decode($result['params']);
                $count = $decoded_cover_param['count'];
                $select = new Zend_Db_Select($db);
                $album_id = $select->from('engine4_sitegroup_albums', 'album_id')
                                ->where('group_id =?', $result['object_id'])
                                ->order('album_id DESC')
                                ->limit(1)
                                ->query()->fetchColumn();

                $db->query('UPDATE `engine4_activity_actions` SET `params` = \' ' . array('child_id' => $album_id, 'count' => $count) . ' \' WHERE `engine4_activity_actions`.`action_id` = "' . $result['action_id'] . '" LIMIT 1 ;');
            }
        }
        $db->query('UPDATE  `engine4_activity_notificationtypes` SET  `body` =  \'{item:$subject} added {var:$count} photo(s) to the album {item:$object}.\' WHERE  `engine4_activity_notificationtypes`.`type` =  "sitegroupalbum_create";');

        $db->query("DELETE FROM `engine4_core_content` WHERE name = 'sitegroup.foursquare-sitegroup' and type = 'widget'");
        $db->query("DELETE FROM `engine4_sitegroup_content` WHERE name = 'sitegroup.foursquare-sitegroup' and type = 'widget'");
        $db->query("DELETE FROM `engine4_sitegroup_admincontent` WHERE name = 'sitegroup.foursquare-sitegroup' and type = 'widget'");

        $db->query("UPDATE `engine4_core_menuitems` SET `plugin` = 'Sitegroup_Plugin_Menus::canViewSitegroups' WHERE `engine4_core_menuitems`.`name` = 'core_main_sitegroup' LIMIT 1");

        $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('SITEGROUP_GROUP_CREATION', 'sitegroup', '[host],[object_title],[sender],[object_link],[object_description]');");

        $db->query('UPDATE  `engine4_activity_notificationtypes` SET  `body` =  \'{item:$subject} has created a group album {item:$object}.\' WHERE  `engine4_activity_notificationtypes`.`type` =  "sitegroupalbum_create";');

        $db->query("UPDATE  `engine4_activity_notificationtypes` SET  `handler` =  'sitegroupmember.widget.approve-group' WHERE  `engine4_activity_notificationtypes`.`type` =  'sitegroupmember_approve';");

        $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroup_admin_main_general", "sitegroup", "General Settings", "", \'{"route":"admin_default","module":"sitegroup","controller":"settings"}\', "sitegroup_admin_main_settings", "", "1", "0", "1")');
        $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroup_admin_main_createedit", "sitegroup", "Miscellaneous Settings", "", \'{"route":"admin_default","module":"sitegroup","controller":"settings", "action":"create-edit"}\', "sitegroup_admin_main_settings", "", "1", "0", "2")');

        $albumTable = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_albums\'')->fetch();
        if (!empty($albumTable)) {

            $featuredColumn = $db->query("SHOW COLUMNS FROM engine4_sitegroup_albums LIKE 'featured'")->fetch();
            if (!empty($featuredColumn)) {
                $featuredIndex = $db->query("SHOW INDEX FROM `engine4_sitegroup_albums` WHERE Key_name = 'featured'")->fetch();
                if (empty($featuredIndex)) {
                    $db->query("ALTER TABLE `engine4_sitegroup_albums` ADD INDEX ( `featured` )");
                }
            }
        }

        $photoTable = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_photos\'')->fetch();
        if (!empty($photoTable)) {

            $featuredColumn = $db->query("SHOW COLUMNS FROM engine4_sitegroup_photos LIKE 'featured'")->fetch();
            if (!empty($featuredColumn)) {
                $featuredIndex = $db->query("SHOW INDEX FROM `engine4_sitegroup_photos` WHERE Key_name = 'featured'")->fetch();
                if (empty($featuredIndex)) {
                    $db->query("ALTER TABLE `engine4_sitegroup_photos` ADD INDEX ( `featured` )");
                }
            }
        }

        // ADD COLUMN BROWSE IN GROUP TABLE META TABLE
        $meta_table_exist = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_group_fields_meta\'')->fetch();
        if (!empty($meta_table_exist)) {
            $column_exist = $db->query('SHOW COLUMNS FROM engine4_sitegroup_group_fields_meta LIKE \'browse\'')->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_sitegroup_group_fields_meta`  ADD `browse` TINYINT UNSIGNED NOT NULL DEFAULT '0';");
            }
        }

        $column_exist_action_email = $db->query('SHOW COLUMNS FROM engine4_sitegroup_manageadmins LIKE \'action_email\'')->fetch();
        if (empty($column_exist_action_email)) {
            $db->query("ALTER TABLE `engine4_sitegroup_manageadmins` ADD `action_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL");
        }
        $db->query("DELETE FROM `engine4_seaocore_searchformsetting` WHERE `engine4_seaocore_searchformsetting`.`module` = 'sitegroup' AND `engine4_seaocore_searchformsetting`.`name` = 'profile_type' LIMIT 1");

        $sitegroupGroupsTable = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_groups\'')->fetch();
        if (!empty($sitegroupGroupsTable)) {
            $subgroup = $db->query("SHOW COLUMNS FROM engine4_sitegroup_groups LIKE 'subgroup'")->fetch();
            if (empty($subgroup)) {
                $db->query("ALTER TABLE `engine4_sitegroup_groups` ADD `subgroup` TINYINT( 1 ) NOT NULL");
            }

            $parent_id = $db->query("SHOW COLUMNS FROM engine4_sitegroup_groups LIKE 'parent_id'")->fetch();
            if (empty($parent_id)) {
                $db->query("ALTER TABLE `engine4_sitegroup_groups` ADD `parent_id` INT( 11 ) NOT NULL DEFAULT '0'");
            }
        }

        //DROP THE INDEX FROM THE `engine4_sitegroup_lists` TABLE
        $sitegroupListsTable = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_lists\'')->fetch();
        if (!empty($sitegroupListsTable)) {
            $sitegrouplistsResults = $db->query("SHOW INDEX FROM `engine4_sitegroup_lists` WHERE Key_name = 'group_id'")->fetch();
            if (!empty($sitegrouplistsResults)) {
                $db->query("ALTER TABLE engine4_sitegroup_lists DROP INDEX group_id");
                $db->query("ALTER TABLE `engine4_sitegroup_lists` ADD UNIQUE (`owner_id`, `group_id`);");
            }
        }

        //Group member install work
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitegroupmember');
        $module_enabled = $select->query()->fetchObject();
        if ($module_enabled) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitegroup')
                    ->where('version <= ?', '4.8.0');
            $version_check = $select->query()->fetchObject();
            if (!empty($version_check)) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_sitegroup_membership', array('title', 'role_id', 'member_id'));
                $results = $select->query()->fetchAll();
                foreach ($results as $result) {
                    $title = Zend_Json::encode($result['title']);
                    $role_id = Zend_Json::encode($result['role_id']);
                    $db->update('engine4_sitegroup_membership', array('title' => "[" . $title . "]", "role_id" => "[" . $role_id . "]"), array('member_id = ?' => $result['member_id']));
                }
            }

            $role_id = $db->query("SHOW COLUMNS FROM engine4_sitegroup_membership LIKE 'role_id'")->fetch();
            if (!empty($role_id)) {
                $db->query("ALTER TABLE `engine4_sitegroup_membership` CHANGE `role_id` `role_id` VARCHAR( 255 ) NOT NULL");
            }



            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitegroup_gutter_notifications', 'sitegroupmember', 'Notification Settings', 'Sitegroupmember_Plugin_Menus::sitegroupGutterNotificationSettings', '', 'sitegroup_gutter', NULL, '1', '0', '999');");

            $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitegroup_membership'")->fetch();
            if (!empty($table_exist)) {
                $email_field = $db->query("SHOW COLUMNS FROM engine4_sitegroup_membership LIKE 'email'")->fetch();
                if (empty($email_field)) {
                    $db->query("ALTER TABLE `engine4_sitegroup_membership` ADD `email` TINYINT( 1 ) NOT NULL DEFAULT '1'");
                }

                //For add column in the 'engine4_sitegroup_membership' table.
                $action_notification_field = $db->query("SHOW COLUMNS FROM engine4_sitegroup_membership LIKE 'action_email'")->fetch();
                if (empty($action_notification_field)) {
                    $db->query("ALTER TABLE  `engine4_sitegroup_membership` ADD `action_email` VARCHAR( 255 ) NULL");
                }

                //For add column in the 'engine4_sitegroup_membership' table.
                $action_notification_field = $db->query("SHOW COLUMNS FROM engine4_sitegroup_membership LIKE 'action_notification'")->fetch();
                if (empty($action_notification_field)) {
                    $db->query("ALTER TABLE  `engine4_sitegroup_membership` ADD `action_notification` VARCHAR( 255 ) NULL");
                }
            }
            $groupIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitegroupmember_roles LIKE 'group_id'")->fetch();
            if (!empty($groupIdColumn)) {
                $groupIdIndex = $db->query("SHOW INDEX FROM `engine4_sitegroupmember_roles` WHERE Key_name = 'group_id'")->fetch();
                if (empty($groupIdIndex)) {
                    $db->query("ALTER TABLE `engine4_sitegroupmember_roles` ADD INDEX ( `group_id` )");
                }
            }
        }


        //Group member install work
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

        $select = new Zend_Db_Select($db);
        $advancedactivity = $select->from('engine4_core_modules', 'name')
                ->where('name = ?', 'advancedactivity')
                ->query()
                ->fetchcolumn();

        $is_enabled = $select->query()->fetchObject();
        if (!empty($advancedactivity)) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`) VALUES ("follow_sitegroup_group", "sitegroup", \'{item:$subject} is following {item:$owner}\'\'s {item:$object:group}: {body:$body}\', 1, 5, 1, 1, 1, 1, 1)');
        } else {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("follow_sitegroup_group", "sitegroup", \'{item:$subject} is following {item:$owner}\'\'s {item:$object:group}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        }
        //END FOLLOW WORK

        $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('sitegroupalbum.isActivate', '1'), ('sitegroupmember.isActivate', '1');");

        //START LIKE PRIVACY WORKENTRY FOR LIST TABLE. 
        $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitegroup_lists` (
			`list_id` int(11) NOT NULL AUTO_INCREMENT,
			`title` varchar(64) NOT NULL,
			`owner_id` int(11) NOT NULL,
			`group_id` int(11) NOT NULL,
			PRIMARY KEY (`list_id`),
			UNIQUE KEY `owner_id` (`owner_id`,`group_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitegroup')
                ->where('version <= ?', '4.2.9');
        $is_enabled = $select->query()->fetchObject();
        if (!empty($is_enabled)) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_sitegroup_groups', array('group_id', 'owner_id'));
            $sitegroup_results = $select->query()->fetchAll();
            if (!empty($sitegroup_results)) {
                foreach ($sitegroup_results as $result) {
                    $db->query("INSERT IGNORE INTO `engine4_sitegroup_lists` (`title`, `owner_id`, `group_id`) VALUES ('SITEGROUP_LIKE', " . $result['owner_id'] . " , " . $result['group_id'] . ");");
                }
            }

            //START UPDATE ALL MEMBER LEVEL SETTINGS WITH NEW SETTING LIKE PRIVACY.
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_authorization_levels', array('level_id'))
                    ->where('title != ?', 'public');
            $check_sitegroup = $select->query()->fetchAll();
            foreach ($check_sitegroup as $modArray) {

                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_authorization_permissions', array('params', 'name', 'level_id'))
                        ->where('type LIKE "%sitegroup_group%"')
                        ->where('level_id = ?', $modArray['level_id'])
                        ->where('name LIKE "%auth_s%"');
                $result = $select->query()->fetchAll();

                foreach ($result as $results) {
                    $params = Zend_Json::decode($results['params']);
                    $params[] = 'like_member';
                    $paramss = Zend_Json::encode($params);
                    $db->query("UPDATE `engine4_authorization_permissions` SET `params` = '$paramss' WHERE `engine4_authorization_permissions`.`type` = 'sitegroup_group' AND `engine4_authorization_permissions`.`name` = '" . $results['name'] . "' AND `engine4_authorization_permissions`.`level_id` = '" . $results['level_id'] . "';");
                }
            }
            //START UPDATE ALL MEMBER LEVEL SETTINGS WITH NEW SETTING LIKE PRIVACY.
        }
        //END LIKE PRIVACY WORKENTRY FOR LIST TABLE. 

        $member_titleCover = $db->query("SHOW COLUMNS FROM engine4_sitegroup_groups LIKE 'member_title'")->fetch();
        if (empty($member_titleCover)) {
            $db->query("ALTER TABLE `engine4_sitegroup_groups` ADD `member_title` VARCHAR( 64 ) NOT NULL");
        }

        $groupCover = $db->query("SHOW COLUMNS FROM engine4_sitegroup_groups LIKE 'group_cover'")->fetch();
        if (empty($groupCover)) {
            $db->query("ALTER TABLE `engine4_sitegroup_groups` ADD `group_cover` INT( 11 ) NOT NULL DEFAULT '0'");
        }

        $groupCoverParams = $db->query("SHOW COLUMNS FROM engine4_sitegroup_albums LIKE 'cover_params'")->fetch();
        if (empty($groupCoverParams)) {
            $db->query("ALTER TABLE `engine4_sitegroup_albums` ADD `cover_params` VARCHAR( 265 ) NULL");
        }

        $column_exist_action_notification = $db->query('SHOW COLUMNS FROM engine4_sitegroup_manageadmins LIKE \'action_notification\'')->fetch();
        if (empty($column_exist_action_notification)) {
            $db->query("ALTER TABLE `engine4_sitegroup_manageadmins` ADD `action_notification` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL");
        }

        $column_exist_locationname = $db->query('SHOW COLUMNS FROM engine4_sitegroup_locations LIKE \'locationname\'')->fetch();
        if (empty($column_exist_locationname)) {
            $db->query("ALTER TABLE `engine4_sitegroup_locations` ADD `locationname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL");
        }

        $column_exist_follow_count = $db->query('SHOW COLUMNS FROM engine4_sitegroup_groups LIKE \'follow_count\'')->fetch();
        if (empty($column_exist_follow_count)) {
            $db->query("ALTER TABLE `engine4_sitegroup_groups` ADD `follow_count` int(11) NOT NULL");
        }

        //Notification seetings work
        $column_exist_email = $db->query('SHOW COLUMNS FROM engine4_sitegroup_manageadmins LIKE \'email\'')->fetch();
        $column_exist_notification = $db->query('SHOW COLUMNS FROM engine4_sitegroup_manageadmins LIKE \'notification\'')->fetch();
        if (empty($column_exist) && empty($column_exist_notification)) {
            $db->query("ALTER TABLE `engine4_sitegroup_manageadmins` ADD `email` TINYINT( 1 ) NOT NULL DEFAULT '1'");
            $db->query("ALTER TABLE `engine4_sitegroup_manageadmins` ADD `notification` TINYINT( 1 ) NOT NULL");
        }

        //START THE WORK FOR MAKE WIDGETIZE GROUP OF Locatio or map.
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitegroup')
                ->where('version < ?', '4.2.3');
        $is_enabled = $select->query()->fetchObject();
        if (empty($is_enabled)) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'sitegroup_index_map')
                    ->limit(1);
            $info = $select->query()->fetch();

            if (empty($info)) {
                $db->insert('engine4_core_pages', array(
                    'name' => 'sitegroup_index_map',
                    'displayname' => 'Browse Groups’ Locations',
                    'title' => 'Browse Groups’ Locations',
                    'description' => 'Browse Groups’ Locations',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId('engine4_core_pages');

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'top',
                    'parent_content_id' => null,
                    'order' => 1,
                    'params' => '',
                ));
                $top_id = $db->lastInsertId('engine4_core_content');

                //CONTAINERS
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'main',
                    'parent_content_id' => Null,
                    'order' => 2,
                    'params' => '',
                ));
                $container_id = $db->lastInsertId('engine4_core_content');

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'middle',
                    'parent_content_id' => $top_id,
                    'params' => '',
                ));
                $top_middle_id = $db->lastInsertId('engine4_core_content');

                //INSERT MAIN - MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'middle',
                    'parent_content_id' => $container_id,
                    'order' => 2,
                    'params' => '',
                ));
                $middle_id = $db->lastInsertId('engine4_core_content');

                // Top Middle
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitegroup.browsenevigation-sitegroup',
                    'parent_content_id' => $top_middle_id,
                    'order' => 1,
                    'params' => '',
                ));

                //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitegroup.location-search',
                    'parent_content_id' => $middle_id,
                    'order' => 2,
                    'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitegroup.browselocation-sitegroup',
                    'parent_content_id' => $middle_id,
                    'order' => 3,
                    'params' => '{"title":"","titleCount":"true"}',
                ));
            }
        }
        //END THE WORK FOR MAKE WIDGETIZE GROUP OF LOCATIO OR MAP.
        //START THE WORK FOR MAKE WIDGETIZE GROUP OF Locatio or map.MOBILE GROUP.
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitegroup')
                ->where('version < ?', '4.2.3');
        $is_enabled = $select->query()->fetchObject();
        if (empty($is_enabled)) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'sitegroup_index_mobilemap')
                    ->limit(1);
            $info = $select->query()->fetch();

            if (empty($info)) {
                $db->insert('engine4_core_pages', array(
                    'name' => 'sitegroup_index_mobilemap',
                    'displayname' => 'Mobile Browse Groups’ Locations',
                    'title' => 'Mobile Browse Groups’ Locations',
                    'description' => 'Mobile Browse Groups’ Locations',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId('engine4_core_pages');

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'top',
                    'parent_content_id' => null,
                    'order' => 1,
                    'params' => '',
                ));
                $top_id = $db->lastInsertId('engine4_core_content');

                //CONTAINERS
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'main',
                    'parent_content_id' => Null,
                    'order' => 2,
                    'params' => '',
                ));
                $container_id = $db->lastInsertId('engine4_core_content');

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'middle',
                    'parent_content_id' => $top_id,
                    'params' => '',
                ));
                $top_middle_id = $db->lastInsertId('engine4_core_content');

                //INSERT MAIN - MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'middle',
                    'parent_content_id' => $container_id,
                    'order' => 2,
                    'params' => '',
                ));
                $middle_id = $db->lastInsertId('engine4_core_content');


                // Top Middle
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitegroup.browsenevigation-sitegroup',
                    'parent_content_id' => $top_middle_id,
                    'order' => 1,
                    'params' => '',
                ));

                //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitegroup.location-search',
                    'parent_content_id' => $middle_id,
                    'order' => 2,
                    'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitegroup.browselocation-sitegroup',
                    'parent_content_id' => $middle_id,
                    'order' => 3,
                    'params' => '{"title":"","titleCount":"true"}',
                ));
            }
        }
        //END THE WORK FOR MAKE WIDGETIZE GROUP OF LOCATIO OR MAP.MOBILE GROUP.
        //WORK FOR CORE CONTENT GROUPS
        $select = new Zend_Db_Select($db);

//     $select->from('engine4_core_content',array('params'))
//             ->where('name = ?', 'sitegroup.socialshare-sitegroup');
// 		$result = $select->query()->fetchObject();
//     if(!empty($result->params)) {
// 			$params = Zend_Json::decode($result->params);
// 			if(isset($params['code'])) {
// 				$code = $params['code'];
// 				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
// 				('sitegroup.code.share','".$code. "');");
// 			}
//     }
        //MIGRATE DATA TO 'engine4_seaocore_searchformsetting' FROM 'engine4_sitegroup_searchform'
        $seocoreSearchformTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_searchformsetting\'')->fetch();
        $sitegroupSearchformTable = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_searchform\'')->fetch();
        if (!empty($seocoreSearchformTable) && !empty($sitegroupSearchformTable)) {
            $datas = $db->query('SELECT * FROM `engine4_sitegroup_searchform`')->fetchAll();
            foreach ($datas as $data) {
                $data_module = 'sitegroup';
                $data_name = $data['name'];
                $data_display = $data['display'];
                $data_order = $data['order'];
                $data_label = $data['label'];

                $db->query("INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`module`, `name`, `display`, `order`, `label`) VALUES ('$data_module', '$data_name', $data_display, $data_order, '$data_label')");
            }

            $db->query('DROP TABLE IF EXISTS `engine4_sitegroup_searchform`');
        }

        $table_exist = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_photos\'')->fetch();
        if (!empty($table_exist)) {
            $column_exist = $db->query('SHOW COLUMNS FROM engine4_sitegroup_photos LIKE \'description\'')->fetch();
            if (empty($column_exist)) {
                $db->query('ALTER TABLE `engine4_sitegroup_photos` CHANGE `description` `description` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
            }
        }

        //CHECK THAT SITEGROUP PLUGIN IS ACTIVATED OR NOT
//     $select = new Zend_Db_Select($db);
//     $select
//             ->from('engine4_core_settings')
//             ->where('name = ?', 'sitegroup.is.active')
//             ->limit(1);
//     $sitegroup_settings = $select->query()->fetchAll();
// 
//     if ( !empty($sitegroup_settings) ) {
//       $sitegroup_is_active = $sitegroup_settings[0]['value'];
//     }
//     else {
//       $sitegroup_is_active = 0;
//     }
// 
//     //CHECK SITEGROUP PLUGIN IS INSTALL OR NOT.
//     $select = new Zend_Db_Select($db);
//     $select
//             ->from('engine4_core_modules')
//             ->where('name = ?', 'sitegroup');
//     $check_sitegroup = $select->query()->fetchAll();
// 
//     $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
//     $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
//     if ( strstr($url_string, "manage/install") ) {
//       $calling_from = 'install';
//     }
//     else if ( strstr($url_string, "manage/query") ) {
//       $calling_from = 'queary';
//     }
//     $explode_base_url = explode("/", $baseUrl);
//     foreach ( $explode_base_url as $url_key ) {
//       if ( $url_key != 'install' ) {
//         $core_final_url .= $url_key . '/';
//       }
//     }
//     if( empty($check_sitegroup) ) {
//       // Group plugin is not install at your site.
//       return $this->_error('<span style="color:red">You have not installed the <a
// href="http://www.socialengineaddons.com/socialengine-groups-communities-plugin" target="_blank"> Directory /
// Groups Plugin </a> on your website. Please install and enable the <a
// href="http://www.socialengineaddons.com/socialengine-directory-groupss-plugin" target="_blank"> Directory /
// Groups Plugin </a> before installing
// the <a href="http://www.socialengineaddons.com/socialengine-groups-communities-plugin" target="_blank">
// Groups Plugin </a>. If you have any questions then please file a support ticket for this
// from the "Support" section of your Client Area on SocialEngineAddOns.</span>');
//     } else if( !empty($check_sitegroup) && empty($check_sitegroup[0]['enabled']) ) { 
//       // Plugin not Enable at your site
//       return $this->_error('<span style="color:red">You have installed the <a
// href="http://www.socialengineaddons.com/socialengine-directory-buinsesses-plugin" target="_blank"> Directory /
// Groups Plugin </a> on your website, but have not enabled it yet. Please enabled
// the <a
// href="http://www.socialengineaddons.com/socialengine-groups-communities-plugin" target="_blank"> Directory /
// Groups Plugin </a> before installing
// the <a href="http://www.socialengineaddons.com/socialengine-groups-communities-plugin" target="_blank">
// Groups Plugin </a>. <a href="http://' . $core_final_url .
// 'install/manage/" target="_blank"> Click here </a> to enable the
// Groups Plugin. </span>');
//     } else if( !empty($check_sitegroup) && empty($sitegroup_is_active) ) {
//       // Please activate Groups plugin
//       return $this->_error('<span style="color:red">You have installed the <a
// href="http://www.socialengineaddons.com/socialengine-groups-communities-plugin" target="_blank"> Directory /
// Groups Plugin </a> on your website but have not activated it yet. Please activate
// the <a
// href="http://www.socialengineaddons.com/socialengine-groups-communities-plugin" target="_blank"> Directory /
// Groups Plugin </a> before installing
// the <a href="http://www.socialengineaddons.com/socialengine-groups-communities-plugin" target="_blank">
// Groups Plugin </a>. <a href="http://' . $core_final_url .
// 'admin/sitegroup/settings/readme" target="_blank"> Click here </a> to activate the
// Groups Plugin. </span>');
//     } else {

        $type_array = $db->query("SHOW COLUMNS FROM engine4_core_likes LIKE 'creation_date'")->fetch();
        if (empty($type_array)) {
            $run_query = $db->query("ALTER TABLE `engine4_core_likes` ADD `creation_date` DATETIME NOT NULL");
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

        //CODE FOR INCREASE THE SIZE OF engine4_activity_stream's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_stream LIKE 'target_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_stream` CHANGE `target_type` `target_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
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

        //CODE FOR INCREASE THE SIZE OF engine4_activity_attachments's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_attachments LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_attachments` CHANGE `type` `type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'subject_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `subject_type` `subject_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_actiontypes's FIELD type
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

        $groupTime = time();
        $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
		('sitegroup.basetime', $groupTime ),
		('sitegroup.isvar', 0 ),
		('sitegroup.filepath', 'Sitegroup/controllers/license/license2.php');");

        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'object_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `object_type` `object_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_core_menuitems LIKE 'label'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_core_menuitems` CHANGE `label` `label` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //
        // Mobile Groups Home
        // group
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'sitegroup_mobi_home')
                ->limit(1);
        ;
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'sitegroup_mobi_home',
                'displayname' => 'Mobile Groups Home',
                'title' => 'Mobile Groups Home',
                'description' => 'This is the mobile verison of a Groups home group.',
                'custom' => 0
            ));
            $group_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // widgets entry
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.browsenevigation-sitegroup',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"title":"","titleCount":"true"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.zerogroup-sitegroup',
                'parent_content_id' => $middle_id,
                'order' => 3,
                'params' => '{"title":"","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.search-sitegroup',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.recently-popular-random-sitegroup',
                'parent_content_id' => $middle_id,
                'order' => 4,
                'params' => '{"title":"","titleCount":"true"}',
            ));
        }

        // Mobile Browse Groups
        // group
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'sitegroup_mobi_index')
                ->limit(1);
        ;
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'sitegroup_mobi_index',
                'displayname' => 'Mobile Browse Groups',
                'title' => 'Mobile Browse Groups',
                'description' => 'This is the mobile verison of a groups browse group.',
                'custom' => 0
            ));
            $group_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');


            // widgets entry
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.browsenevigation-sitegroup',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"title":"","titleCount":"true"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.search-sitegroup',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.groups-sitegroup',
                'parent_content_id' => $middle_id,
                'order' => 3,
                'params' => '{"title":"","titleCount":"true"}',
            ));
        }

        //
        // Mobile Groups Profile
        // group
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'sitegroup_mobi_view')
                ->limit(1);
        ;
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'sitegroup_mobi_view',
                'displayname' => 'Mobile Group Profile',
                'title' => 'Mobile Group Profile',
                'description' => 'This is the mobile verison of a listing profile.',
                'custom' => 0
            ));
            $group_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // widgets entry

            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.title-sitegroup',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"title":"","titleCount":"true"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.mainphoto-sitegroup',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":"true"}',
            ));


            // middle tabs
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'parent_content_id' => $middle_id,
                'order' => 4,
                'params' => '{"max":"6"}',
            ));
            $tab_middle_id = $db->lastInsertId('engine4_core_content');


            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'activity.feed',
                'parent_content_id' => $tab_middle_id,
                'order' => 1,
                'params' => '{"title":"What\'s New","titleCount":"true"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.info-sitegroup',
                'parent_content_id' => $tab_middle_id,
                'order' => 2,
                'params' => '{"title":"Info","titleCount":"true"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.overview-sitegroup',
                'parent_content_id' => $tab_middle_id,
                'order' => 3,
                'params' => '{"title":"Overview","titleCount":"true"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.location-sitegroup',
                'parent_content_id' => $tab_middle_id,
                'order' => 4,
                'params' => '{"title":"Map","titleCount":"true"}',
            ));
        }

        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitegroup_admincontent'")->fetch();
        if (!empty($table_exist)) {
            $defaultColumn = $db->query("SHOW COLUMNS FROM engine4_sitegroup_admincontent LIKE 'default_admin_layout'")->fetch();
            if (empty($defaultColumn)) {
                $db->query("ALTER TABLE `engine4_sitegroup_admincontent` ADD `default_admin_layout` BOOL NOT NULL DEFAULT '0'");
            }
        }

        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitegroup_content'")->fetch();
        if (!empty($table_exist)) {
            $widgetAdminColumn = $db->query("SHOW COLUMNS FROM `engine4_sitegroup_content` LIKE 'widget_admin'")->fetch();
            if (empty($widgetAdminColumn)) {
                $db->query("ALTER TABLE `engine4_sitegroup_content` ADD `widget_admin` BOOL NOT NULL DEFAULT '1'");
            }

            if (!empty($widgetAdminColumn)) {
                $db->query("ALTER TABLE `engine4_sitegroup_content` CHANGE `widget_admin` `widget_admin` TINYINT( 1 ) NOT NULL DEFAULT '1'");
            }
        }

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules')
                ->where('name = ?', 'sitegroup')
                ->where('version <= ?', '4.2.3');
        $is_old_version = $select->query()->fetchObject();
        if ($is_old_version) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_sitegroup_content');
            $adminContentResults = $select->query()->fetchAll();
            if (!empty($adminContentResults)) {
                $contentArray = array();
                foreach ($adminContentResults as $value) {
                    if (!in_array($value['name'], array('core.html-block', 'core.ad-campaign'))) {
                        $db->update('engine4_sitegroup_content', array('widget_admin' => 1), array('name = ?' => $value['name']));
                    } else {
                        $contentArray[] = $value;
                    }
                }
                foreach ($contentArray as $value) {
                    $db->update('engine4_sitegroup_content', array('widget_admin' => 1), array('name = ?' => $value['name'], 'params = ?' => $value['params']));
                }
            }
        }

        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_activity_actiontypes'")->fetch();
        if (!empty($table_exist)) {
            $is_object_thumbColumn = $db->query("SHOW COLUMNS FROM `engine4_activity_actiontypes` LIKE 'is_object_thumb'")->fetch();
            if (empty($is_object_thumbColumn)) {
                $db->query("ALTER TABLE `engine4_activity_actiontypes` ADD `is_object_thumb` BOOL NOT NULL DEFAULT '0'");
            }
        }

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_settings', array('value'))
                ->where('name = ?', 'sitegroup.feed.type');
        $feedType = $select->query()->fetchAll();
        if (!empty($feedType) && $feedType[0]['value'] == 1) {

            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')->where('name = ?', 'sitegroup')->where('version <= ?', '4.2.0p1');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_activity_actions')->where('subject_type = ?', 'sitegroup_group');

                $resultAction = $select->query()->fetchAll();
                if (!empty($resultAction)) {
                    foreach ($resultAction as $result) {

                        $db->query("UPDATE `engine4_activity_actions` SET `subject_type` = '" . $result['object_type'] . "',
        `subject_id` = " . $result['object_id'] . ", `object_type` = '" . $result['subject_type'] . "',
        `object_id` = " . $result['subject_id'] . " WHERE `engine4_activity_actions`.`action_id` =
        " . $result['action_id'] . " ;");

                        $db->query("UPDATE `engine4_activity_stream` SET `subject_type` = '" . $result['object_type'] . "',
        `subject_id` = " . $result['object_id'] . ", `object_type` = '" . $result['subject_type'] . "',
        `object_id` = " . $result['subject_id'] . " WHERE `engine4_activity_stream`.`action_id` =
        " . $result['action_id'] . " ;");
                    }
                }

                $select = new Zend_Db_Select($db);
                $select->from('engine4_activity_stream')->where('object_type = ?', 'sitegroup_group')->group('action_id');
                $resultStreams = $select->query()->fetchAll();
                if (!empty($resultStreams)) {
                    foreach ($resultStreams as $result) {

                        $db->query("INSERT IGNORE INTO `engine4_activity_stream` (`target_type`, `target_id`,
        `subject_type`, `subject_id`, `object_type`, `object_id`, `type`, `action_id`) VALUES ('sitegroup_group',
        " . $result['object_id'] . " , '" . $result['subject_type'] . "', " . $result['subject_id'] . ",
        '" . $result['object_type'] . "', " . $result['object_id'] . ", '" . $result['type'] . "', " .
                                $result['action_id'] . ");");
                    }
                }
            }
        }

        //ADD NEW COLUMN IN engine4_sitegroup_imports TABLE
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitegroup_imports'")->fetch();
        if (!empty($table_exist)) {

            $column_exist = $db->query("SHOW COLUMNS FROM engine4_sitegroup_imports LIKE 'userclaim'")->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_sitegroup_imports` ADD `userclaim` TINYINT( 1 ) NOT NULL DEFAULT '0'");
            }
        }

//		//CHECK THAT foursquare_text COLUMN EXIST OR NOT IN GROUP TABLE
//		$column_exist = $db->query("SHOW COLUMNS FROM engine4_sitegroup_groups LIKE 'foursquare_text'")->fetch();
//		$table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitegroup_groups'")->fetch();
//		if (!empty($column_exist) && !empty($table_exist)) {
//
//			$column_type = $db->query("SELECT data_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'engine4_sitegroup_groups' AND COLUMN_NAME = 'foursquare_text'")->fetch();
//
//			if($column_type != 'tinyint') {
//
//				//FETCH GROUPS
//				$groups = $db->select()->from('engine4_sitegroup_groups', array('foursquare_text', 'group_id'))->query()->fetchAll();
//
//				if (!empty($groups)) {
//					foreach($groups as $group)
//					{
//						$group_id = $group['group_id'];
//						$foursquare_text = $group['foursquare_text'];
//
//						if(!empty($group_id)) {
//						
//							//UPDATE FOURSQUARE TEXT VALUE
//							if(!empty($foursquare_text)) {
//								$db->update('engine4_sitegroup_groups', array('foursquare_text' => 1), array('group_id = ?' => $group_id));
//							}
//							else {
//								$db->update('engine4_sitegroup_groups', array('foursquare_text' => 0), array('group_id = ?' => $group_id));
//							}
//						}
//					}
//				}
//			}
//
//			$db->query("ALTER TABLE `engine4_sitegroup_groups` CHANGE `foursquare_text` `foursquare_text` TINYINT(1) NULL DEFAULT '0'");
//		}
        //START SOCIAL SHARE WIDGET WORK 
        //CHECK PLUGIN VERSION
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitegroup')
                ->where('version < ?', '4.2.1');
        $is_enabled_module = $select->query()->fetchObject();

        if (!empty($is_enabled_module)) {

            $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitegroup.socialshare-sitegroup"}';

            $db->update('engine4_core_content', array('params' => $social_share_default_code,), array('name =?' => 'sitegroup.socialshare-sitegroup'));
            $db->update('engine4_sitegroup_content', array('params' => $social_share_default_code,), array('name =?' => 'sitegroup.socialshare-sitegroup'));
        }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitegroup')
                ->where('version < ?', '4.2.3');
        $is_enabled_module = $select->query()->fetchObject();

        if (!empty($is_enabled_module)) {

            $is_installed_facebookse = $db->query("SELECT `name` FROM `engine4_core_modules` WHERE `name` = 'facebookse' LIMIT 1")->fetchColumn();
            if (!empty($is_installed_facebookse))
                $db->update('engine4_core_content', array('name' => 'Facebookse.facebookse-commonlike'), array('name =?' => 'Facebookse.facebookse-commonlike'));
        }
        //END SOCIAL SHARE WIDGET WORK 

        $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('sitetagcheckin.selectable.2', 'groups')");

        $type_array = $db->query("SHOW COLUMNS FROM engine4_sitegroup_groups LIKE 'fbpage_url' ")->fetch();
        if (empty($type_array)) {
            $run_query = $db->query("ALTER TABLE  `engine4_sitegroup_groups` ADD  `fbpage_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
        }

        //INSERT COLUMN FOR FB GROUP NAME 
        $type_array = $db->query("SHOW COLUMNS FROM engine4_sitegroup_groups LIKE 'fbpage_title' ")->fetch();
        if (empty($type_array)) {
            $run_query = $db->query("ALTER TABLE  `engine4_sitegroup_groups` ADD  `fbpage_title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
        }
        //INSERT COLUMN FOR FB GROUP NAME 
        $type_array = $db->query("SHOW COLUMNS FROM engine4_sitegroup_groups LIKE 'fbgroup_id' ")->fetch();
        if (empty($type_array)) {
            $run_query = $db->query("ALTER TABLE  `engine4_sitegroup_groups` ADD  `fbgroup_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
        }

        $db->query('UPDATE  `engine4_core_content` SET  `name` =  "seaocore.like-button" WHERE  `engine4_core_content`.`name` ="sitegroup.buinsess-like-button";');

        $db->query('UPDATE  `engine4_sitegroup_content` SET  `name` =  "seaocore.like-button" WHERE  `engine4_sitegroup_content`.`name` ="sitegroup.buinsess-like-button";');

        $db->query('UPDATE  `engine4_core_content` SET  `name` =  "seaocore.people-like" WHERE  `engine4_core_content`.`name` ="sitegroup.buinsess-like";');

        $db->query('UPDATE  `engine4_sitegroup_content` SET  `name` =  "seaocore.people-like" WHERE  `engine4_sitegroup_content`.`name` ="sitegroup.buinsess-like";');


        $column_exist_body = $db->query('SHOW COLUMNS FROM engine4_sitegroup_groups LIKE \'body\'')->fetch();
        if (!empty($column_exist_body)) {
            $db->query("ALTER TABLE  `engine4_sitegroup_groups` CHANGE  `body`  `body` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
        }

        $column_exist_description = $db->query('SHOW COLUMNS FROM engine4_sitegroup_contentgroups LIKE \'description\'')->fetch();
        if (!empty($column_exist_description)) {
            $db->query("ALTER TABLE  `engine4_sitegroup_contentgroups` CHANGE  `description`  `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'sitegroup_index_pinboard_browse')
                ->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'sitegroup_index_pinboard_browse',
                'displayname' => 'Browse Groups’ Pinboard View',
                'title' => 'Browse Groups’ Pinboard View',
                'description' => 'Browse Groups’ Pinboard View',
                'custom' => 0,
            ));
            $group_id = $db->lastInsertId('engine4_core_pages');

            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            //CONTAINERS
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => Null,
                'order' => 2,
                'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'params' => '',
            ));
            $top_middle_id = $db->lastInsertId('engine4_core_content');

            //INSERT MAIN - MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // Top Middle
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.browsenevigation-sitegroup',
                'parent_content_id' => $top_middle_id,
                'order' => 1,
                'params' => '',
            ));

            //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.horizontal-search',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1","browseredirect":"pinboard"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroup.pinboard-browse',
                'parent_content_id' => $middle_id,
                'order' => 3,
                'params' => '{"title":"","titleCount":true,"postedby":"1","showoptions":["viewCount","likeCount","commentCount","price","location"],"detactLocation":"0","defaultlocationmiles":"1000","itemWidth":"237","withoutStretch":"0","itemCount":"12","show_buttons":["comment","like","share","facebook","twitter"],"truncationDescription":"100"}',
            ));
        }

        //DROP THE COLUMN FROM THE "engine4_sitepage_albums" TABLE
        $typeTypeColumn = $db->query("SHOW COLUMNS FROM engine4_sitegroup_albums LIKE 'type'")->fetch();
        if (!empty($typeTypeColumn)) {
            $db->query("ALTER TABLE `engine4_sitegroup_albums` CHANGE `type` `type` ENUM( 'note', 'overview', 'wall', 'announcements', 'discussions', 'cover' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;");
        }

//     	$select = new Zend_Db_Select($db);
// 		$select
// 						->from('engine4_core_pages', array('page_id'))
// 						->where('name = ?', 'sitegroup_index_view');
// 		$coregroupObject = $select->query()->fetchObject();
// 		$select = new Zend_Db_Select($db);
// 		$select
// 						->from('engine4_core_settings', array('value'))
// 						->where('name = ?', 'sitegroup.core.cover.layout');
// 		$coreSettingsObject = $select->query()->fetchObject();
// 
// 		if (!empty($coregroupObject) && empty($coreSettingsObject)) {
// 			$group_id = $coregroupObject->page_id;
// 			$select = new Zend_Db_Select($db);
// 
// 			$select
// 							->from('engine4_core_content', array('name'))
// 							->where('name=?', 'left')->where('name=?', 'right')->where('page_id=?', $group_id);
// 			$coregroupObject = $select->query()->fetchObject();
// 
// 			if(empty($coregroupObject)) {
// 				$select = new Zend_Db_Select($db);
// 				$select
// 								->from('engine4_core_content', array('name'))
// 								->where('name = ?', 'left')
// 								->where('page_id = ?', $group_id);
// 				$coregroupObject = $select->query()->fetchObject();
// 				if($coregroupObject) {
// 					$db->update('engine4_core_content', array('name' => 'right'), array('name = ?' => 'left', 'page_id =?' => $group_id));
// 				}
// 				$db->delete('engine4_core_content', array('name =?' => 'sitegroup.mainphoto-sitegroup'));
// 				$db->delete('engine4_core_content', array('name =?' => 'sitegroupmember.profile-sitegroupmembers-announcements'));
// 				$db->delete('engine4_core_content', array('name =?' => 'seaocore.like-buttons'));
// 				$db->delete('engine4_core_content', array('name =?' => 'seaocore.seaocore-follow'));
// 				$db->delete('engine4_core_content', array('name =?' => 'facebookse.facebookse-commonlike'));
// 				$db->delete('engine4_core_content', array('name =?' => 'sitegroup.title-sitegroup'));
// 				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('sitegroup.core.cover.layout', 1);");
// 			}
// 		}

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitetagcheckin')
                ->where('enabled = ?', 1);
        $is_sitetagcheckin_object = $select->query()->fetchObject();
        if (!empty($is_sitetagcheckin_object)) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
("sitetagcheckin_sgal_photo_new", "sitetagcheckin", "{item:$object} added {var:$count} photo(s) to the album {var:$linked_album_title} - {var:$prefixadd} {var:$location}.", 1, 5, 1, 3, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES("sitetagcheckin_group_tagged", "sitetagcheckin", "{item:$subject} mentioned your group with a {item:$object:$label}.", "0", "", "1")');
            $db->query('INSERT IGNORE INTO `engine4_sitetagcheckin_contents` (`module`, `resource_type`, `resource_id`, `value`, `default`, `enabled`) VALUES("sitegroup", "sitegroup_group", "group_id", "1", "1", "1"),("sitegroupalbum", "sitegroup_album", "album_id", 1, 1, 1),("sitegroupnote", "sitegroupnote_note", "note_id", 1, 1, 1),("sitegroupevent", "sitegroupevent_event", "event_id", 1, 1, 1),("sitegroupmusic", "sitegroupmusic_playlist", "playlist_id", 1, 1, 1),("sitegroupdiscussion", "sitegroup_topic", "topic_id", 1, 1, 1),("sitegroupvideo", "sitegroupvideo_video", "video_id", 1, 1, 1),("sitegrouppoll", "sitegrouppoll_poll", "poll_id", 1, 1, 1),("sitegroupdocument", "sitegroupdocument_document", "document_id", 1, 1, 1),("sitegroupreview", "sitegroupreview_review", "review_id", 1, 1, 1)');
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitetagcheckin')
                ->where('enabled = ?', 1);
        $is_sitetagcheckin_object = $select->query()->fetchObject();
        if (!empty($is_sitetagcheckin_object)) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
	("sitetagcheckin_sbal_photo_new", "sitetagcheckin", "{item:$object} added {var:$count} photo(s) to the album {var:$linked_album_title} - {var:$prefixadd} {var:$location}.", 1, 5, 1, 3, 1, 1)');
        }

        //     $select = new Zend_Db_Select($db);
        //     $select
        //             ->from('engine4_core_modules')
        //             ->where('name = ?', 'sitegroup')
        //             ->where('version <= ?', '4.6.0');
        //     $is_enabled = $select->query()->fetchObject();
        //     if (!empty($is_enabled)) {
        //	$db->query("DROP TABLE IF EXISTS `engine4_sitegroup_mobileadmincontent`;");
        $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitegroup_mobileadmincontent` (
				`mobileadmincontent_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`group_id` int(11) unsigned NOT NULL,
				`type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'widget',
				`name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
				`parent_content_id` int(11) unsigned DEFAULT NULL,
				`order` int(11) NOT NULL DEFAULT '1',
				`params` text COLLATE utf8_unicode_ci,
				`attribs` text COLLATE utf8_unicode_ci,
				`module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				`default_admin_layout` tinyint(4) NOT NULL DEFAULT '0',
				PRIMARY KEY (`mobileadmincontent_id`),
				KEY `group_id` (`group_id`,`order`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");

        //$db->query("DROP TABLE IF EXISTS `engine4_sitegroup_mobilecontent`;");
        $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitegroup_mobilecontent` (
				`mobilecontent_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`mobilecontentgroup_id` int(11) unsigned NOT NULL,
				`type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'widget',
				`name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
				`parent_content_id` int(11) unsigned DEFAULT NULL,
				`order` int(11) NOT NULL DEFAULT '1',
				`params` text COLLATE utf8_unicode_ci,
				`attribs` text COLLATE utf8_unicode_ci,
				`module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				`widget_admin` tinyint(1) NOT NULL DEFAULT '1',
				PRIMARY KEY (`mobilecontent_id`),
				KEY `group_id` (`mobilecontentgroup_id`,`order`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");

        //$db->query("DROP TABLE IF EXISTS `engine4_sitegroup_mobilecontentgroups`;");
        $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitegroup_mobilecontentgroups` (
				`mobilecontentgroup_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(11) unsigned NOT NULL,
				`group_id` int(11) unsigned NOT NULL,
				`name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
				`displayname` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
				`url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
				`title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`description` text COLLATE utf8_unicode_ci,
				`keywords` text COLLATE utf8_unicode_ci NOT NULL,
				`custom` tinyint(1) NOT NULL DEFAULT '1',
				`fragment` tinyint(1) NOT NULL DEFAULT '0',
				`layout` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
				`view_count` int(11) unsigned NOT NULL DEFAULT '1',
				PRIMARY KEY (`mobilecontentgroup_id`),
				KEY `group_id` (`group_id`),
				KEY `user_id` (`user_id`),
				KEY `name` (`name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if ($is_sitemobile_object) {
            include APPLICATION_PATH . "/application/modules/Sitegroup/controllers/license/mobileLayoutCreation.php";
        }

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`,`is_object_thumb`) VALUES ("sitegroup_admin_profile_photo", "sitegroup", "{item:$object} updated a new profile photo.", 1, 3, 2, 1, 1, 1, 1);');

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitegroup')
                ->where('version < ?', '4.8.6p6');
        $is_enabled_module = $select->query()->fetchObject();
        if ($is_enabled_module) {
            $this->makeWidgitizeManageGroup('sitegroup_index_manage', 'Groups / Communities - Manage Groups', 'My Groups', 'This group lists a user\'s Groups\'s.');
        }

        $this->makeWidgitizeGroup('sitegroup_index_edit', 'Groups / Communities - Edit Group', 'Edit Group', 'This is group edit group.');

        $this->makeWidgitizeGroup('sitegroup_index_create', 'Groups / Communities - Create Group', 'Create new Group', 'This is group create group.');

        $this->makeWidgitizeGroup('sitegroup_like_my-joined', 'Groups / Communities - Manage Group (Groups I\'ve Joined)', 'Groups I\'ve Joined', 'This group lists a user\'s Groups\'s which user\'s have joined.');

        $this->makeWidgitizeGroup('sitegroup_like_mylikes', 'Groups / Communities - Manage Group (Groups I Like)', 'Groups I Like', 'This group lists a user\'s Groups\'s which user\'s likes.');

        $this->makeWidgitizeGroup('sitegroup_manageadmin_my-groups', 'Groups / Communities - Manage Group (Groups I Admin)', 'Groups I Admin', 'This group lists a user\'s Groups\'s of which user\'s is admin.');

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteevent')
                ->where('enabled = ?', 1);
        $is_siteevent_object = $select->query()->fetchObject();
        if ($is_siteevent_object) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'sitegroup.isActivate')
                    ->where('value = ?', 1);
            $sitegroup_isActivate_object = $select->query()->fetchObject();
            if ($sitegroup_isActivate_object) {

                $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES("siteevent_group_host", "siteevent", \'{item:$subject} has made your group {var:$group} host of the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.\', "");');

                $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES("SITEEVENT_GROUP_HOST", "siteevent", "[host],[email],[sender],[event_title_with_link],[event_url],[group_title_with_link]");');
                $itemMemberTypeColumn = $db->query("SHOW COLUMNS FROM `engine4_siteevent_modules` LIKE 'item_membertype'")->fetch();
                if (empty($itemMemberTypeColumn)) {
                    $db->query("ALTER TABLE `engine4_siteevent_modules` ADD `item_membertype` VARCHAR( 255 ) NOT NULL AFTER `item_title`");
                }
                $db->query("INSERT IGNORE INTO `engine4_siteevent_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitegroup_group', 'group_id', 'sitegroup', '0', '0', 'Group Events', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");

                $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitegroup_admin_main_manage", "siteevent", "Manage Events", "", \'{"uri":"admin/siteevent/manage/index/contentType/sitegroup_group/contentModule/sitegroup"}\', "sitegroup_admin_main", "", 1, 0, 24);');
                $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "siteevent.event.leader.owner.sitegroup.group", "0");');
            }
        }

        $db->query('UPDATE `engine4_activity_notificationtypes` SET `body` = \'{item:$subject} has liked {item:$object}.\' WHERE `engine4_activity_notificationtypes`.`type` = "sitegroup_contentlike" LIMIT 1 ;');

        $db->query('UPDATE `engine4_activity_notificationtypes` SET `body` = \'{item:$subject} has commented on {item:$object}.\' WHERE `engine4_activity_notificationtypes`.`type` = "sitegroup_contentcomment" LIMIT 1 ;');

        $categoriesTable = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_categories\'')->fetch();
        if (!empty($categoriesTable)) {

            $catDependencyIndex = $db->query("SHOW INDEX FROM `engine4_sitegroup_categories` WHERE Key_name = 'cat_dependency'")->fetch();
            if (empty($catDependencyIndex)) {
                $db->query("ALTER TABLE `engine4_sitegroup_categories` ADD INDEX ( `cat_dependency` )");
            }

            $subcatDependencyIndex = $db->query("SHOW INDEX FROM `engine4_sitegroup_categories` WHERE Key_name = 'subcat_dependency'")->fetch();
            if (empty($subcatDependencyIndex)) {
                $db->query("ALTER TABLE `engine4_sitegroup_categories` ADD INDEX ( `subcat_dependency` )");
            }
        }

        $favouritesTable = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_favourites\'')->fetch();
        if (!empty($favouritesTable)) {
            $groupIdForIndex = $db->query("SHOW INDEX FROM `engine4_sitegroup_favourites` WHERE Key_name = 'group_id_for'")->fetch();
            if (empty($groupIdForIndex)) {
                $db->query("ALTER TABLE `engine4_sitegroup_favourites` ADD INDEX ( `group_id_for` )");
            }
        }

        $groupsTable = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_groups\'')->fetch();
        if (!empty($groupsTable)) {
            $categoryIdIndex = $db->query("SHOW INDEX FROM `engine4_sitegroup_groups` WHERE Key_name = 'category_id'")->fetch();
            if (empty($categoryIdIndex)) {
                $db->query("ALTER TABLE `engine4_sitegroup_groups` ADD INDEX ( `category_id` )");
            }

            $parentIdIndex = $db->query("SHOW INDEX FROM `engine4_sitegroup_groups` WHERE Key_name = 'parent_id'")->fetch();
            if (empty($parentIdIndex)) {
                $db->query("ALTER TABLE `engine4_sitegroup_groups` ADD INDEX ( `parent_id` )");
            }



            $profileTypeIndex = $db->query("SHOW INDEX FROM `engine4_sitegroup_groups` WHERE Key_name = 'profile_type'")->fetch();
            if (empty($profileTypeIndex)) {
                $db->query("ALTER TABLE `engine4_sitegroup_groups` ADD INDEX ( `profile_type` )");
            }

            $featuredIndex = $db->query("SHOW INDEX FROM `engine4_sitegroup_groups` WHERE Key_name = 'featured'")->fetch();
            $sponsoredIndex = $db->query("SHOW INDEX FROM `engine4_sitegroup_groups` WHERE Key_name = 'sponsored'")->fetch();
            if (empty($featuredIndex) && empty($sponsoredIndex)) {
                $db->query("ALTER TABLE `engine4_sitegroup_groups` ADD INDEX ( `featured` )");
                $db->query("ALTER TABLE `engine4_sitegroup_groups` ADD INDEX ( `sponsored` )");
                $db->query("ALTER TABLE `engine4_sitegroup_groups` ADD INDEX featured_sponsored ( `featured`, `sponsored` )");
            }

            $searchIndex = $db->query("SHOW INDEX FROM `engine4_sitegroup_groups` WHERE Key_name = 'closed'")->fetch();
            if (!empty($searchIndex)) {
                $db->query("ALTER TABLE `engine4_sitegroup_groups` ADD INDEX closed ( `search`,`closed`,`approved`,`declined`,`draft` )");
            }
        }

        $profilemapsTable = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_profilemaps\'')->fetch();
        if (!empty($profilemapsTable)) {
            $categoryIdIndex = $db->query("SHOW INDEX FROM `engine4_sitegroup_profilemaps` WHERE Key_name = 'category_id'")->fetch();
            if (empty($categoryIdIndex)) {
                $db->query("ALTER TABLE `engine4_sitegroup_profilemaps` ADD INDEX ( `category_id` )");
            }
        }

        $itemTable = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_itemofthedays\'')->fetch();
        if (!empty($itemTable)) {
            $dateColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitegroup_itemofthedays` WHERE Key_name = 'date'")->fetch();
            $endTimeColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitegroup_itemofthedays` WHERE Key_name = 'endtime'")->fetch();
            if (!empty($dateColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitegroup_itemofthedays` DROP INDEX `date`;");
            }

            if (!empty($endTimeColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitegroup_itemofthedays` DROP INDEX `endtime`;");
            }
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'sitegroup_index_pinboard_browse');
        $pinboard_page_id = $select->query()->fetchObject();
        if (!empty($pinboard_page_id)) {
            $db->query('UPDATE `engine4_core_content` SET `params` = \'{"title":"","titleCount":true,"postedby":"1","showoptions":["viewCount","likeCount","commentCount","price","location"],"detactLocation":"0","defaultlocationmiles":"1000","itemWidth":"274","withoutStretch":"1","itemCount":"12","show_buttons":["comment","like","share","facebook","twitter"],"truncationDescription":"100","nomobile":"0","name":"sitegroup.pinboard-browse"}\' WHERE `engine4_core_content`.`name` ="sitegroup.pinboard-browse" AND  `engine4_core_content`.`page_id` ="' . $pinboard_page_id->page_id . '" LIMIT 1 ;');
        }

        //ADD FILE_ID COLUMN IN CATEGORIES TABLE
        $categoriesTable = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_categories\'')->fetch();
        if (!empty($categoriesTable)) {
            $file_id = $db->query("SHOW COLUMNS FROM engine4_sitegroup_categories LIKE 'file_id'")->fetch();
            if (empty($file_id)) {
                $db->query("ALTER TABLE `engine4_sitegroup_categories` ADD `file_id` int(11) NOT NULL DEFAULT '0';");
            }
        }
        $this->_changeWidgetParam($db, 'sitegroup', '4.8.0p1');

        $table_import_exist = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_imports\'')->fetch();
        if (!empty($table_import_exist)) {
            $img_column = $db->query("SHOW COLUMNS FROM engine4_sitegroup_imports LIKE 'img_name'")->fetch();
            if (empty($img_column)) {
                $db->query("ALTER TABLE `engine4_sitegroup_imports` ADD `img_name` VARCHAR( 512 ) NOT NULL AFTER `tags`");
            }
            $column_subsub_category = $db->query("SHOW COLUMNS FROM engine4_sitegroup_imports LIKE 'subsub_category'")->fetch();
            if (empty($column_subsub_category)) {
                $db->query("ALTER TABLE `engine4_sitegroup_imports` ADD `subsub_category` VARCHAR( 255 ) NOT NULL AFTER `sub_category`");
            }
        }
        $table_importfiles_exist = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_importfiles\'')->fetch();
        if (!empty($table_importfiles_exist)) {
            $column_filename = $db->query("SHOW COLUMNS FROM engine4_sitegroup_importfiles LIKE 'photo_filename'")->fetch();
            if (empty($column_filename)) {
                $db->query("ALTER TABLE `engine4_sitegroup_importfiles` ADD `photo_filename` VARCHAR( 255 ) NOT NULL AFTER `filename`");
            }
        }
        
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'documentintegration')
                ->where('enabled = ?', 1);
        $is_documentintegration_object = $select->query()->fetchObject();
        if ($is_documentintegration_object) {
            $db->query("INSERT IGNORE INTO `engine4_document_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`) VALUES ('sitegroup_group', 'group_id', 'sitegroup', '0', '0', 'Group Documents')");
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitegroup_admin_main_managedocument", "documentintegration", "Manage Documents", "", \'{"uri":"admin/document/manage-document/index/contentType/sitegroup_group/contentModule/sitegroup"}\', "sitegroup_admin_main", "", 0, 0, 25);');
            $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "document.leader.owner.sitegroup.group", "1");');
        }

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
                    ->where('name = ?', 'sitegroup.isActivate')
                    ->where('value = ?', 1);
            $sitegroup_isActivate_object = $select->query()->fetchObject();
            if ($sitegroup_isActivate_object) {

                $db->query("INSERT IGNORE INTO `engine4_sitevideo_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitegroup_group', 'group_id', 'sitegroup', '0', '0', 'Group Videos', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
                $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitegroup_admin_main_managevideo", "sitevideointegration", "Manage Videos", "", \'{"uri":"admin/sitevideo/manage-video/index/contentType/sitegroup_group/contentModule/sitegroup"}\', "sitegroup_admin_main", "", 0, 0, 24);');
                $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitevideo.video.leader.owner.sitegroup.group", "1");');
            }
        }
        $this->setActivityFeeds();
        parent::onInstall();
        // }  
    }

    function onDisable() {
        $db = $this->getDb();

        $db->query("UPDATE `engine4_core_modules` SET  `enabled` =  '0' WHERE  `engine4_core_modules`.`name` ='sitegroupmember';");
        $db->query("UPDATE `engine4_core_modules` SET  `enabled` =  '0' WHERE  `engine4_core_modules`.`name` ='sitegroupalbum';");

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules', array('name'))
                ->where('enabled = ?', 1);
        $moduleData = $select->query()->fetchAll();

        $subModuleArray = array("sitegroupalbum", "sitegroupbadge", "sitegroupdiscussion", "sitegroupdocument", "sitegroupevent", "sitegroupform", "sitegroupinvite", "sitegroupnote", "sitegroupoffer", "sitegrouppoll", "sitegroupreview", "sitegroupvideo", "sitegroupmusic", "sitegroupwishlist", "sitegroupadmincontact", "sitegroupurl");

        foreach ($moduleData as $key => $moduleName) {
            if (in_array($moduleName['name'], $subModuleArray)) {
                $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
                $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Note: Please disable all the integrated sub-modules of Groups / Communities plugin before disabling the Groups / Communities plugin itself.');
                echo "<div style='background-color: #E9F4FA;border-radius:7px 7px 7px 7px;float:left;overflow: hidden;padding:10px;'><div style='background:#FFFFFF;border:1px solid #D7E8F1;overflow:hidden;padding:20px;'><span style='color:red'>$error_msg1</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.</div></div>";
                die;
            }
        }

        parent::onDisable();
    }

    public function onEnable() {

        $db = $this->getDb();
        $db->query("UPDATE `engine4_core_modules` SET  `enabled` =  '1' WHERE  `engine4_core_modules`.`name` ='sitegroupmember';");
        $db->query("UPDATE `engine4_core_modules` SET  `enabled` =  '1' WHERE  `engine4_core_modules`.`name` ='sitegroupalbum';");

        parent::onEnable();
    }

    private function getVersion() {

        $db = $this->getDb();

        $errorMsg = '';
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'sitelike' => '4.6.0',
            'advancedactivity' => '4.6.0',
            'facebookse' => '4.6.0',
            'seaocore' => '4.6.0',
            'sitealbum' => '4.6.0',
            'sitetagcheckin' => '4.6.0',
            'suggestion' => '4.6.0',
            'sitevideoview' => '4.6.0',
            'sitereview' => '4.6.0',
            'sitereviewlistingtype' => '4.6.0',
            'facebooksefeed' => '4.6.0',
            'communityad' => '4.6.0',
            'communityadsponsored' => '4.6.0'
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
            $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Groups / Communities Plugin".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
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
    
    public function onPostInstall() {

        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if (!empty($is_sitemobile_object)) {
            $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES ('sitegroup','1')");
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_sitemobile_modules')
                    ->where('name = ?', 'sitegroup')
                    ->where('integrated = ?', 0);
            $is_sitemobile_object = $select->query()->fetchObject();
            if ($is_sitemobile_object) {
                $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
                $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
                if ($controllerName == 'manage' && $actionName == 'install') {
                    $view = new Zend_View();
                    $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    $redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/sitegroup/integrated/0/redirect/install');
                }
            }
        }

        //Work for the word changes in the group plugin .csv file.
        $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        if ($controllerName == 'manage' && ($actionName == 'install' || $actionName == 'query')) {
            $view = new Zend_View();
            $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            if ($actionName == 'install') {
                $redirector->gotoUrl($baseUrl . 'admin/sitegroup/settings/language/redirect/install');
            } else {
                $redirector->gotoUrl($baseUrl . 'admin/sitegroup/settings/language/redirect/query');
            }
        }
    }

    public function makeWidgitizeGroup($groupname, $displayname, $title, $description) {

        $db = $this->getDb();
        //Create a group for the edit group.
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', "$groupname")
                ->limit(1);
        $info = $select->query()->fetch();

        // insert if it doesn't exist yet
        if (empty($info)) {
            // Insert group
            $db->insert('engine4_core_pages', array(
                'name' => $groupname,
                'displayname' => $displayname,
                'title' => $title,
                'description' => $description,
                'custom' => 0,
            ));
            $group_id = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $group_id,
                'order' => 1,
            ));
            $main_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $group_id,
                'parent_content_id' => $main_id,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $group_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
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

    public function setActivityFeeds() {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'nestedcomment')
                ->where('enabled = ?', 1);
        $is_nestedcomment_object = $select->query()->fetchObject();
        if ($is_nestedcomment_object) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitegroup_group", "sitegroup", \'{item:$subject} replied to a comment on {item:$owner}\'\'s group {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitegroup_album", "sitegroupalbum", \'{item:$subject} replied to a comment on {item:$owner}\'\'s group album {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitegroup_photo", "sitegroupalbum", \'{item:$subject} replied to a comment on {item:$owner}\'\'s group album photo {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("sitegroup_activityreply", "sitegroup", \'{item:$subject} has replied on {var:$eventname}.\', 0, "");');
        }
    }

    public function makeWidgitizeManageGroup($groupname, $displayname, $title, $description) {
        $db = $this->getDb();

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', "$groupname")
                ->limit(1);
        $info = $select->query()->fetch();

        if (!empty($info)) {
            $info_page_id = $info['page_id'];
            $db->query("DELETE FROM `engine4_core_pages` WHERE `engine4_core_pages`.`page_id` = $info_page_id LIMIT 1");
            $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $info_page_id");
        }

        $db->insert('engine4_core_pages', array(
            'name' => $groupname,
            'displayname' => $displayname,
            'title' => $title,
            'description' => $description,
            'custom' => 0,
        ));
        $page_id = $db->lastInsertId();

        // Insert main
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'top',
            'page_id' => $page_id,
            'order' => 1,
        ));
        $top_id = $db->lastInsertId();

        // Insert top-middle
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $top_id,
            'order' => 6,
        ));
        $top_middle_id = $db->lastInsertId();

        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitegroup.browsenevigation-sitegroup',
            'page_id' => $page_id,
            'parent_content_id' => $top_middle_id,
            'order' => 3,
        ));

        // Insert main
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'main',
            'page_id' => $page_id,
            'order' => 2,
        ));
        $main_id = $db->lastInsertId();

        // Insert main-middle
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $main_id,
            'order' => 6,
        ));
        $main_middle_id = $db->lastInsertId();

        // Insert main-middle
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'right',
            'page_id' => $page_id,
            'parent_content_id' => $main_id,
            'order' => 5,
        ));
        $right_id = $db->lastInsertId();

        // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitegroup.manage-sitegroup',
            'page_id' => $page_id,
            'parent_content_id' => $main_middle_id,
            'order' => 5,
        ));

        // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitegroup.links-sitegroup',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'params' => '{"title":"","titleCount":false,"showLinks":["groupAdmin","groupClaimed","groupLiked"],"nomobile":"0","name":"sitegroup.links-sitegroup"}',
            'order' => 1,
        ));

        // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitegroup.manage-search-sitegroup',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'order' => 2,
        ));

        // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitegroup.newgroup-sitegroup',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'order' => 3,
        ));
        //  }
    }

}

?>
