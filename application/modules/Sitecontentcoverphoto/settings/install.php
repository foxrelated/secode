<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Installer extends Engine_Package_Installer_Module {

    public function onPreinstall() {
        $db = $this->getDb();
        $PRODUCT_TYPE = 'sitecontentcoverphoto';
        $PLUGIN_TITLE = 'Sitecontentcoverphoto';
        $PLUGIN_VERSION = '4.8.8';
        $PLUGIN_CATEGORY = 'plugin';
        $PRODUCT_DESCRIPTION = 'Content Profiles - Cover Photo, Banner & Site Branding Plugin';
        $_PRODUCT_FINAL_FILE = 0;
        $SocialEngineAddOns_version = '4.8.10p3';
        $PRODUCT_TITLE = 'Content Profiles - Cover Photo, Banner & Site Branding Plugin';
        $getErrorMsg = $this->getVersion();
        if (!empty($getErrorMsg)) {
            return $this->_error($getErrorMsg);
        }


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

        $db->update('engine4_core_modules', array('title' => 'Content Profiles - Cover Photo, Banner & Site Branding Plugin', 'description' => 'Content Profiles - Cover Photo, Banner & Site Branding Plugin'), array('name = ?' => 'sitecontentcoverphoto'));

        $db->update('engine4_core_menuitems', array('label' => 'SEAO - Content Profiles-Cover Photo, Banner & Site Branding'), array('name = ?' => 'core_admin_main_plugins_sitecontentcoverphoto', 'module =?' => 'sitecontentcoverphoto'));


        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='sitecontentcoverphoto';");

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteevent')
                ->where('enabled = ?', 1);
        $is_siteevent_object = $select->query()->fetchObject();
        if (!empty($is_siteevent_object)) {
            $db->query('INSERT IGNORE INTO `engine4_sitecontentcoverphoto_modules` (`module`, `resource_type`, `resource_id`, `enabled`) VALUES
		("siteevent", "siteevent_event", "event_id", 1)');
        }

        // WORK FOR ADVANCED ALBUM PLUGIN
        $db->query('INSERT IGNORE INTO `engine4_sitecontentcoverphoto_modules` ( `module`, `resource_type`, `resource_id`, `enabled`) VALUES ( "sitealbum", "album", "album_id", "0");');

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("album_cover_update", "sitealbum", \'{item:$subject} updated cover photo of the album {item:$object}:\', 1, 3, 2, 1, 1, 1)');

        $db->query("
        INSERT IGNORE INTO `engine4_authorization_permissions` 
        SELECT 
              level_id as `level_id`, 
              'sitecontentcoverphoto_album' as `type`, 
              'upload' as `name`, 
              1 as `value`, 
              NULL as `params` 
        FROM `engine4_authorization_levels` WHERE `type` IN('moderator','admin','user');
      ");

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitereview')
                ->where('version <= ?', '4.8.8')
                ->where('enabled = ?', 1);
        $is_sitereview_object = $select->query()->fetchObject();
        if (!empty($is_sitereview_object)) {
            $sitereview_cover_column = $db->query("SHOW COLUMNS FROM engine4_sitereview_listings LIKE '%_cover'")->fetchAll();
            foreach ($sitereview_cover_column as $sitereview_cover) {
                $columnName = $sitereview_cover['Field'];
                $checkExistColumn = $db->query("SHOW COLUMNS FROM engine4_sitereview_otherinfo LIKE '$columnName'")->fetch();
                if (empty($checkExistColumn)) {
                    $db->query("ALTER TABLE engine4_sitereview_otherinfo add `$columnName` int (11);");
                    $db->query("UPDATE engine4_sitereview_otherinfo eso join engine4_sitereview_listings esl on eso.listing_id = esl.listing_id set eso.`$columnName` = esl.`$columnName`");
                    $db->query("ALTER TABLE engine4_sitereview_otherinfo modify `$columnName` int (11);");
                }
                $checkExistColumnName = $db->query("SHOW COLUMNS FROM engine4_sitereview_listings LIKE '$columnName'")->fetch();
                if (!empty($checkExistColumnName)) {
                    $db->query("ALTER TABLE `engine4_sitereview_listings` DROP `$columnName`");
                }
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_sitereview_albums', array('album_id', 'listing_id'))
                    ->query()
                    ->fetchAll();

            $db = Engine_Db_Table::getDefaultAdapter();
            foreach ($results as $key => $value) {
                $album_id = $value['album_id'];
                $listing_id = $value['listing_id'];
                $db->query("UPDATE `engine4_sitereview_photos` SET `album_id` = $album_id, `collection_id` = $album_id WHERE `engine4_sitereview_photos`.`listing_id` = $listing_id;");
            }
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteevent')
                ->where('version <= ?', '4.8.8')
                ->where('enabled = ?', 1);
        $is_siteevent_object = $select->query()->fetchObject();
        if (!empty($is_siteevent_object)) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_siteevent_albums', array('album_id', 'event_id'))
                    ->query()
                    ->fetchAll();
            $db = Engine_Db_Table::getDefaultAdapter();
            foreach ($results as $key => $value) {
                $album_id = $value['album_id'];
                $event_id = $value['event_id'];
                $db->query("UPDATE `engine4_siteevent_photos` SET `album_id` = $album_id, `collection_id` = $album_id WHERE `engine4_siteevent_photos`.`event_id` = $event_id;");
            }
        }


        $sitevideo = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitevideo')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (!empty($sitevideo)) {
            $field = $db->query("SHOW COLUMNS FROM engine4_sitevideo_channels LIKE 'channel_cover'")->fetch();
            if (empty($field)) {
                $db->query("ALTER TABLE `engine4_sitevideo_channels` ADD `channel_cover` INT( 11 ) NOT NULL DEFAULT '0'");
            }
        }
        parent::onInstall();
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
            $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES ('sitecontentcoverphoto', '1', '1', '1', '1')");
        }
    }

    private function getVersion() {
        $db = $this->getDb();

        $errorMsg = '';
        $finalModules = $getResultArray = array();
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'sitealbum' => '4.8.9p3'
        );

        foreach ($modArray as $key => $value) {
            $isMod = $db->query("SELECT * FROM  `engine4_core_modules` WHERE  `name` LIKE  '" . $key . "'")->fetch();
            if (!empty($isMod) && !empty($isMod['version'])) {
                $isModSupport = $this->checkVersion($isMod['version'], $value);
                if (empty($isModSupport)) {
                    $finalModules['modName'] = $key;
                    $finalModules['title'] = $isMod['title'];
                    $finalModules['versionRequired'] = $value;
                    $finalModules['versionUse'] = $isMod['version'];
                    $getResultArray[] = $finalModules;
                }
            }
        }

        foreach ($getResultArray as $modArray) {
            $errorMsg .= '<div class="tip"><span>Note: Your website does not have the latest version of "' . $modArray['title'] . '". Please upgrade "' . $modArray['title'] . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Advanced Photo Albums Plugin".<br/> Please <a href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
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
