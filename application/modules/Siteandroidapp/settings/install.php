<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    install.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteandroidapp_Installer extends Engine_Package_Installer_Module {

    function onPreinstall() {
        $db = $this->getDb();

        $select = new Zend_Db_Select($db);
        $isAPIActivate = $select
                ->from('engine4_core_settings', 'value')
                ->where('name = ?', 'siteapi.isActivate')
                ->limit(1)
                ->query()
                ->fetchColumn();
        $isAPIActivate = !empty($isAPIActivate) ? $isAPIActivate : 0;

        $select = new Zend_Db_Select($db);
        $isAPIEnabled = $select
                ->from('engine4_core_modules', 'enabled')
                ->where('name = ?', 'siteapi')
                ->where('enabled = ?', 1)
                ->query()
                ->fetchColumn();

        if (empty($isAPIEnabled)) {
            $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
            return $this->_error("<span style='color:red'>Note: You have not installed the '<a href='http://www.socialengineaddons.com/socialengine-rest-api-plugin' target='_blank'>SocialEngine REST API Plugin</a>' on your site yet. Please install it first before installing the 'Android Mobile Application'. <a href='" . $base_url . "/manage'>Click here</a> to go to Manage Packages.</span>");
        } else {
            if (empty($isAPIActivate)) {
                $core_final_url = '';
                $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
                $explode_base_url = explode("/", $baseUrl);
                foreach ($explode_base_url as $url_key) {
                    if ($url_key != 'install') {
                        $core_final_url .= $url_key . '/';
                    }
                }
                return $this->_error("<span style='color:red'>Note: You have installed the 'SocialEngine REST API Plugin' but not activated it on your site yet. Please activate it first before installing the 'Android Mobile Application'.</span> <a href='" . 'http://' . $core_final_url . "admin/siteapi/settings/readme'>Click here</a> to activate the 'SocialEngine REST API Plugin'.");
            }

            $getErrorMsg = $this->_getVersion();
            if (!empty($getErrorMsg)) {
                return $this->_error($getErrorMsg);
            }

            //CHECK THAT ADVANCED ACTIVITY FEED PLUGIN IS ACTIVATED OR NOT
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'advancedactivity.navi.auth')
                    ->limit(1);
            $isAAFActivate = $select->query()->fetchAll();
            $flagAAFActivate = !empty($isAAFActivate) ? $isAAFActivate[0]['value'] : 0;

            //CHECK THAT ADVANCED ACTIVITY PLUGIN IS INSTALLED OR NOT
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'advancedactivity')
                    ->where('enabled = ?', 1);
            $isAAFInstalled = $select->query()->fetchObject();
            if (!empty($isAAFInstalled) && !empty($flagAAFActivate)) {
                $PRODUCT_TYPE = 'siteandroidapp';
                $PLUGIN_TITLE = 'Siteandroidapp';
                $PLUGIN_VERSION = '4.8.10p7';
                $PLUGIN_CATEGORY = 'plugin';
                $PRODUCT_DESCRIPTION = 'Android Mobile Application';
                $PRODUCT_TITLE = 'Android Mobile Application';
                $_PRODUCT_FINAL_FILE = 0;
                $SocialEngineAddOns_version = '4.8.9p14';
                $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
                $is_file = file_exists($file_path);
                if (empty($is_file)) {
                    include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
                } else {
                    $db = $this->getDb();
                    $select = new Zend_Db_Select($db);
                    $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
                    $is_Mod = $select->query()->fetchObject();
                    if (empty($is_Mod)) {
                        include_once $file_path;
                    }
                }
                parent::onPreinstall();
            } elseif (!empty($isAAFInstalled) && empty($flagAAFActivate)) {
                $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
                $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
                if (strstr($url_string, "manage/install")) {
                    $calling_from = 'install';
                } else if (strstr($url_string, "manage/query")) {
                    $calling_from = 'queary';
                }
                $explode_base_url = explode("/", $baseUrl);
                foreach ($explode_base_url as $url_key) {
                    if ($url_key != 'install') {
                        $core_final_url .= $url_key . '/';
                    }
                }

                return $this->_error("<span style='color:red'>Note: You have installed the Advanced Activity Feeds / Wall Plugin but not activated it on your site yet. Please activate it first before installing the Android Mobile Application.</span><br/><a href='" . 'http://' . $core_final_url . "admin/advancedactivity/settings/readme'>Click here</a> to activate the Advanced Activity Feeds / Wall Plugin.");
            } else {
                $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
                return $this->_error("The \"Android Mobile Application\" is dependant on our \"<a href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'>Advanced Activity Feeds / Wall Plugin</a>\". So please install this plugin before installing the \"Android Mobile Application\"");
            }
        }
    }

    public function onInstall() {
        $db = $this->getDb();

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_menuitems')
                ->where('name = ?', 'siteandroidapp_admin_api_sitereview_views')
                ->where('plugin = ?', 'Siteapi_Plugin_Menus::mltMapping');
        $rowExists = $select->query()->fetchObject();
        if (isset($rowExists) && !empty($rowExists)) {
            $db->query("UPDATE `engine4_core_menuitems` SET `plugin` = 'Siteandroidapp_Plugin_Menus::mltMapping' WHERE `engine4_core_menuitems`.`name` = 'siteandroidapp_admin_api_sitereview_views'");
        }

        $this->_updateAppCreationProcessTabs();

        // Rename "app-builder" directory to "android-HOST-app-builder". It needed for our client, who are going to upgrade this plugin and have old directory with "app-builder" name.
        $getWebsiteName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $websiteStr = str_replace(".", "-", $getWebsiteName);
        if (is_dir(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/app-builder')) {
            @rename(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/app-builder', APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/' . 'android-' . $websiteStr . '-app-builder');
        }

        if (is_dir(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/' . 'android-' . str_replace('www.', '', strtolower($_SERVER['HTTP_HOST'])) . '-app-builder')) {
            @rename(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/' . 'android-' . str_replace('www.', '', strtolower($_SERVER['HTTP_HOST'])) . '-app-builder', APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/' . 'android-' . $websiteStr . '-app-builder');
        }

        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='siteandroidapp';");

        $activitynotificationtypesTable = $db->query('SHOW TABLES LIKE \'engine4_activity_notificationtypes\'')->fetch();
        if (!empty($activitynotificationtypesTable)) {
            $enable_push = $db->query("SHOW COLUMNS FROM engine4_activity_notificationtypes LIKE 'siteandroidapp_enable_push'")->fetch();
            if (empty($enable_push)) {
                $db->query("ALTER TABLE `engine4_activity_notificationtypes` ADD `siteandroidapp_enable_push` TINYINT( 1 ) NOT NULL DEFAULT '1';");
            }

            $android_pushtype = $db->query("SHOW COLUMNS FROM engine4_activity_notificationtypes LIKE 'siteandroidapp_pushtype'")->fetch();
            if (empty($android_pushtype)) {
                $db->query("ALTER TABLE `engine4_activity_notificationtypes` ADD `siteandroidapp_pushtype` TINYINT( 4 ) NOT NULL DEFAULT '7';");
            }
        }

        $db->query("UPDATE  `engine4_activity_notificationtypes` SET  `siteandroidapp_enable_push` =  '1' WHERE  `type` IN ('" . join("','", self::_getDefaultEnablePushNotification()) . "')");

        $this->_addMultipleListingViewMappingTabs();

        //Advanced Event Dashboard Query
        $select = new Zend_Db_Select($db);
        $isSiteeventRowExist = $select->from('engine4_siteandroidapp_menus')
                ->where('name = ?', "core_main_siteevent")
                ->limit(1)
                ->query()
                ->fetchColumn();
        //CHECK THAT ADVANCED EVENT PLUGIN IS INSTALLED OR NOT
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteevent')
                ->where('enabled = ?', 1);
        $isSiteeventModuleEnabled = $select->query()->fetchObject();
        if (empty($isSiteeventRowExist) && $isSiteeventModuleEnabled)
            $db->query('INSERT INTO `engine4_siteandroidapp_menus` (`name`, `dashboard_label`, `header_label`, `module`, `icon`, `url`, `show`, `type`, `status`, `default`, `order`) VALUES ("core_main_siteevent", "Advanced Events", "Advanced Events", "siteevent", NULL, NULL, "both", "menu", "1", "1", "19")');

        // Insert Review Wishlist
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitereview')
                ->where('enabled = ?', 1);
        $isSitereviewEnabled = $select->query()->fetchObject();
        if (!empty($isSitereviewEnabled)) {
            $select = new Zend_Db_Select($db);
            $isWishlistRowExist = $select->from('engine4_siteandroidapp_menus')
                    ->where('name = ?', "sitereview_wishlist")
                    ->limit(1)
                    ->query()
                    ->fetchColumn();

            if (empty($isWishlistRowExist))
                $db->query('INSERT INTO `engine4_siteandroidapp_menus` (`menu_id`, `name`, `dashboard_label`, `header_label`, `module`, `icon`, `url`, `show`, `type`, `status`, `default`, `order`) VALUES (NULL, "sitereview_wishlist", "Wishlists", "Wishlists", "sitereview", NULL, NULL, "both", "menu", "1", "1", "7")');
        }

        parent::onInstall();
    }

    private function _addMultipleListingViewMappingTabs() {
        $db = $this->getDb();
        $isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteandroidapp_admin_api_sitereview_views\' LIMIT 1')->fetch();
        if (empty($isRowExist)) {
            $db->query('INSERT INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES (NULL, "siteandroidapp_admin_api_sitereview_views", "siteandroidapp", "Multiple Listing Type Layout", \'Siteandroidapp_Plugin_Menus::mltMapping\', \'{"route":"admin_default","module":"siteandroidapp","controller":"view-maps-listing-type", "action":"manage"}\', "siteandroidapp_admin_main", NULL, "1", "0", "11")');
        }

        //create table for Multiple Listing Type Layout Mapping
        $db->query("CREATE TABLE IF NOT EXISTS `engine4_siteandroidapp_listingtypeViewMaps` (
  `listingtype_id` int(5) NOT NULL,  
  `profileView_id` int(11) NOT NULL,
  `browseView_id` int(11) NOT NULL,
  PRIMARY KEY (`listingtype_id`),
  KEY `profileView_id` (`profileView_id`),
  KEY `browseView_id` (`browseView_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
    }

    public function _getDefaultEnablePushNotification() {
        return array("commented_commented", "commented", "friend_accepted", "friend_request", "liked", "liked_commented", "message_new", "post_user", "shared", "tagged", "aaf_tagged", "event_accepted", "group_accepted", "group_approve", "siteevent_accepted", "siteevent_approve", "siteevent_editorreview", "siteevent_invite", "sitegroup_manageadmin", "sitegroup_suggested", "sitegroupmember_accepted", "sitegroupmember_approve", "sitegroupmember_invite", "sitegroup_addmember", "sitepage_contentlike", "sitepage_manageadmin", "sitepage_suggested", "sitepagemember_accepted", "sitepagemember_approve", "sitepagemember_invite", "sitepage_addmember", "follow_sitereview_wishlist", "sitereview_approved_review", "sitereview_editorreview", "sitereview_write_review", "sitestoreproduct_approved_review", "sitestoreproduct_editorreview", "sitestoreproduct_order_comment_from_buyer", "sitestoreproduct_order_comment_to_buyer", "sitestoreproduct_order_comment_to_store_admin", "sitestoreproduct_order_ship", "sitestoreproduct_order_status_admin_change", "sitestoreproduct_order_status_change", "sitestore-sitestoreproductwritereview", "sitestore_manageadmin", "sitestore_suggested", "video_processed");
    }

    private function _updateAppCreationProcessTabs() {
        $db = $this->getDb();

        // Add params column in menu table
        $menuTableObj = $db->query('SHOW TABLES LIKE \'engine4_siteandroidapp_menus\'')->fetch();
        if (!empty($menuTableObj)) {
            $paramColumn = $db->query("SHOW COLUMNS FROM engine4_siteandroidapp_menus LIKE 'params'")->fetch();
            if (empty($paramColumn)) {
                $db->query("ALTER TABLE `engine4_siteandroidapp_menus` ADD `params` TEXT NULL;");
            }
        }

        $select = new Zend_Db_Select($db);
        $isLanguageRowExist = $select->from('engine4_siteandroidapp_menus')
                ->where('name = ?', "core_multi_languages")
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (empty($isLanguageRowExist))
            $db->query("INSERT INTO `engine4_siteandroidapp_menus` (`name`, `dashboard_label`, `header_label`, `module`, `icon`, `url`, `show`, `type`, `status`, `default`, `order`) VALUES ('core_multi_languages', 'Multi Languages', NULL, NULL, NULL, NULL, 'both', 'menu', '1', '1', '23');");

        // Set "Location" row in table
        $select = new Zend_Db_Select($db);
        $isLanguageRowExist = $select->from('engine4_siteandroidapp_menus')
                ->where('name = ?', "seaocore_location")
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (empty($isLanguageRowExist))
            $db->query("INSERT INTO `engine4_siteandroidapp_menus` (`name`, `dashboard_label`, `header_label`, `module`, `icon`, `url`, `show`, `type`, `status`, `default`, `order`) VALUES ('seaocore_location', 'Choose Location', 'Choose Location', NULL, NULL, NULL, 'both', 'menu', '1', '1', '22');");

        $select = new Zend_Db_Select($db);
        $isLanguageRowExist = $select->from('engine4_siteandroidapp_menus')
                ->where('name = ?', "spread_the_word")
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (empty($isLanguageRowExist))
            $db->query("INSERT INTO `engine4_siteandroidapp_menus` (`name`, `dashboard_label`, `header_label`, `module`, `icon`, `url`, `show`, `type`, `status`, `default`, `order`) VALUES ('spread_the_word', 'Spread the Word', 'Spread the Word', NULL, 'f045', NULL, 'both', 'menu', '1', '1', '21');");

        $isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteandroidapp_admin_general_settings\' LIMIT 1')->fetch();
        if (empty($isRowExist))
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("siteandroidapp_admin_general_settings", "siteandroidapp", "App Submission Info", \'Siteandroidapp_Plugin_Menus::appBuildUrls\', \'{"tab":"1"}\', "siteandroidapp_admin_appsetup_main", "", 1);');

        $isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteandroidapp_admin_graphic_assets\' LIMIT 1')->fetch();
        if (empty($isRowExist))
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("siteandroidapp_admin_graphic_assets", "siteandroidapp", "Graphic Assets", \'Siteandroidapp_Plugin_Menus::appBuildUrls\', \'{"tab":"2"}\', "siteandroidapp_admin_appsetup_main", "", 2);');

        $isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteandroidapp_admin_splash_and_slideshows\' LIMIT 1')->fetch();
        if (empty($isRowExist))
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("siteandroidapp_admin_splash_and_slideshows", "siteandroidapp", "Introductory Slideshow", \'Siteandroidapp_Plugin_Menus::appBuildUrls\', \'{"tab":"3"}\', "siteandroidapp_admin_appsetup_main", "", 3);');

        $isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteandroidapp_admin_language_assets\' LIMIT 1')->fetch();
        if (empty($isRowExist))
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("siteandroidapp_admin_language_assets", "siteandroidapp", "Language Assets", \'Siteandroidapp_Plugin_Menus::appBuildUrls\', \'{"tab":"4"}\', "siteandroidapp_admin_appsetup_main", "", 4);');

        $isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteandroidapp_admin_advertising\' LIMIT 1')->fetch();
        if (empty($isRowExist))
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("siteandroidapp_admin_advertising", "siteandroidapp", "Advertising", \'Siteandroidapp_Plugin_Menus::appBuildUrls\', \'{"tab":"5"}\', "siteandroidapp_admin_appsetup_main", "", 5);');

        $isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteandroidapp_admin_download\' LIMIT 1')->fetch();
        if (empty($isRowExist))
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("siteandroidapp_admin_download", "siteandroidapp", "Download Tar", \'Siteandroidapp_Plugin_Menus::appBuildUrls\', \'{"tab":"6"}\', "siteandroidapp_admin_appsetup_main", "", 6);');
    }

    private function _getVersion() {

        $db = $this->getDb();

        $errorMsg = '';
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'siteapi' => '4.8.9p12'
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

//				$isModSupport = strcasecmp($getModVersion->version, $value);
                $running_version = $getModVersion->version;
                $product_version = $value;
                $shouldUpgrade = false;
                if (!empty($running_version) && !empty($product_version)) {
                    $temp_running_verion_2 = $temp_product_verion_2 = 0;
                    if (strstr($product_version, "p")) {
                        $temp_starting_product_version_array = @explode("p", $product_version);
                        $temp_product_verion_1 = $temp_starting_product_version_array[0];
                        $temp_product_verion_2 = $temp_starting_product_version_array[1];
                    } else {
                        $temp_product_verion_1 = $product_version;
                    }
                    $temp_product_verion_1 = @str_replace(".", "", $temp_product_verion_1);


                    if (strstr($running_version, "p")) {
                        $temp_starting_running_version_array = @explode("p", $running_version);
                        $temp_running_verion_1 = $temp_starting_running_version_array[0];
                        $temp_running_verion_2 = $temp_starting_running_version_array[1];
                    } else {
                        $temp_running_verion_1 = $running_version;
                    }
                    $temp_running_verion_1 = @str_replace(".", "", $temp_running_verion_1);


                    if (($temp_running_verion_1 < $temp_product_verion_1) || (($temp_running_verion_1 == $temp_product_verion_1) && ($temp_running_verion_2 < $temp_product_verion_2))) {
                        $shouldUpgrade = true;
                    }
                }

                if (!empty($shouldUpgrade)) {
                    $finalModules[$key] = $getModVersion->title;
                }
            }
        }

        foreach ($finalModules as $modArray) {
            $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "' . $modArray . '".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
        }

        return $errorMsg;
    }

}
