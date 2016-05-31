<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Installer extends Engine_Package_Installer_Module {

    function onPreinstall() {
        $db = $this->getDb();

        $getErrorMsg = $this->_getVersion();
        if (!empty($getErrorMsg)) {
            return $this->_error($getErrorMsg);
        }

        $PRODUCT_TYPE = 'siteadvsearch';
        $PLUGIN_TITLE = 'Siteadvsearch';
        $PLUGIN_VERSION = '4.8.9p1';
        $PLUGIN_CATEGORY = 'plugin';
        $PRODUCT_DESCRIPTION = 'Advanced Search Plugin';
        $PRODUCT_TITLE = 'Advanced Search Plugin';
        $_PRODUCT_FINAL_FILE = 0;
        $SocialEngineAddOns_version = '4.8.9p12';
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
    }

    function onInstall() {

        $db = $this->getDb();

        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='siteadvsearch';");

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteadvsearch')
                ->where('version <= ?', '4.8.6p2');
        $is_enabled = $select->query()->fetchObject();
        if (!empty($is_enabled)) {
            $itemTypeField = $db->query("SHOW COLUMNS FROM engine4_siteadvsearch_contents LIKE 'item_type'")->fetch();
            if (!empty($itemTypeField)) {
                $db->query("ALTER TABLE `engine4_siteadvsearch_contents` DROP `item_type`");
            }
        }

        $location_field = $db->query("SHOW COLUMNS FROM engine4_core_search LIKE 'location'")->fetch();
        if (empty($location_field)) {
            $db->query("ALTER TABLE `engine4_core_search` ADD `location` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER `hidden` ");
        }

        $itemType_field = $db->query("SHOW COLUMNS FROM engine4_core_search LIKE 'item_type'")->fetch();
        if (empty($itemType_field)) {
            $db->query("ALTER TABLE `engine4_core_search` ADD `item_type` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `location`");
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitevideo')
                ->where('enabled = ?', 1);
        $is_sitevideo_object = $select->query()->fetchObject();
        if ($is_sitevideo_object) {
            $engine4siteadvsearchTable = $db->query("SHOW TABLES LIKE 'engine4_siteadvsearch_contents'")->fetch();
            if ($engine4siteadvsearchTable) {
                $db->query("UPDATE `engine4_siteadvsearch_contents` SET `module_name` = 'sitevideo', `resource_type` = 'sitevideo_video', `resource_title` = 'Videos', `main_search` =1  WHERE `engine4_siteadvsearch_contents`.`resource_type` ='video' LIMIT 1 ;");

                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_siteadvsearch_contents')
                        ->where('resource_type = ?', 'sitevideo_channel');
                $sitevideo_channel_isActivate_object = $select->query()->fetchObject();
                if (!$sitevideo_channel_isActivate_object) {
                    $db->query("INSERT IGNORE INTO `engine4_siteadvsearch_contents` ( `module_name`, `resource_type`, `resource_title`, `listingtype_id`, `widgetize`, `content_tab`, `main_search`, `order`, `file_id`, `default`, `enabled`) VALUES ( 'sitevideo', 'sitevideo_channel', 'Channels', '0', '1', '1', '1', '999', '', '1', '1');");
                }
            }
        }
        parent::onInstall();
    }

    function onDisable() {

        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_content')
                ->where('name = ?', "siteadvsearch.menu-mini");
        $isExist = $select->query()->fetchObject();
        if (!empty($isExist)) {
            $db->query("UPDATE `engine4_core_content` SET `name` = 'core.menu-mini' WHERE `engine4_core_content`.`name` ='siteadvsearch.menu-mini' LIMIT 1 ;");
        }
        parent::onDisable();
    }

    function onEnable() {

        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_content')
                ->where('name = ?', "core.menu-mini");
        $isExist = $select->query()->fetchObject();
        if (!empty($isExist)) {
            $db->query("UPDATE `engine4_core_content` SET `name` = 'siteadvsearch.menu-mini' WHERE `engine4_core_content`.`name` ='core.menu-mini' LIMIT 1 ;");
        }
        parent::onEnable();
    }

    private function _getVersion() {

        $db = $this->getDb();

        $errorMsg = '';
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'sitetheme' => '4.8.6',
            'sitemenu' => '4.8.6',
            'sitemailtemplates' => '4.8.6',
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
                    $finalModules[$key] = $getModVersion->title;
                }
            }
        }

        foreach ($finalModules as $modArray) {
            $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Advanced Search Plugin".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
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

}
