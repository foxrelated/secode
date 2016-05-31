<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_Installer extends Engine_Package_Installer_Module {

    function onPreinstall() {
        $db = $this->getDb();

        $PRODUCT_TYPE = 'nestedcomment';
        $PLUGIN_TITLE = 'Nestedcomment';
        $PLUGIN_VERSION = '4.8.10p5';
        $PLUGIN_CATEGORY = 'plugin';
        $PRODUCT_DESCRIPTION = 'Advanced Comments Plugin - Nested Comments, Replies, Voting & Attachments';
        $PRODUCT_TITLE = 'Advanced Comments Plugin - Nested Comments, Replies, Voting & Attachments';
        $_PRODUCT_FINAL_FILE = 0;
        $SocialEngineAddOns_version = '4.8.7p11';
        $getErrorMsg = $this->getVersion();
        if (!empty($getErrorMsg)) {
            return $this->_error($getErrorMsg);
        }
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
        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='nestedcomment';");

        $tableObj = $db->query("SHOW TABLES LIKE 'engine4_core_comments'")->fetch();
        if (!empty($tableObj)) {
            $parent_comment_id = $db->query("SHOW COLUMNS FROM engine4_core_comments LIKE 'parent_comment_id' ")->fetch();
            if (empty($parent_comment_id)) {
                $db->query("ALTER TABLE  `engine4_core_comments` ADD  `parent_comment_id` int( 11 ) NOT NULL DEFAULT  '0';");
            }

            $params = $db->query("SHOW COLUMNS FROM engine4_core_comments LIKE 'params' ")->fetch();
            if (empty($params)) {
                $db->query("ALTER TABLE  `engine4_core_comments` ADD  `params` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;");
            }

            $attachment_type = $db->query("SHOW COLUMNS FROM engine4_core_comments LIKE 'attachment_type' ")->fetch();
            if (empty($attachment_type)) {
                $db->query("ALTER TABLE  `engine4_core_comments` ADD  `attachment_type` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;");
            }

            $attachment_id = $db->query("SHOW COLUMNS FROM engine4_core_comments LIKE 'attachment_id' ")->fetch();
            if (empty($attachment_id)) {
                $db->query("ALTER TABLE  `engine4_core_comments` ADD  `attachment_id` INT( 11 ) NULL DEFAULT  '0';");
            }
        }

        $tableObj = $db->query("SHOW TABLES LIKE 'engine4_activity_comments'")->fetch();
        if (!empty($tableObj)) {

            $parent_comment_id = $db->query("SHOW COLUMNS FROM engine4_activity_comments LIKE 'parent_comment_id' ")->fetch();
            if (empty($parent_comment_id)) {
                $db->query("ALTER TABLE  `engine4_activity_comments` ADD  `parent_comment_id` int( 11 ) NOT NULL DEFAULT  '0';");
            }

            $attachment_type = $db->query("SHOW COLUMNS FROM engine4_activity_comments LIKE 'attachment_type' ")->fetch();
            if (empty($attachment_type)) {
                $db->query("ALTER TABLE  `engine4_activity_comments` ADD  `attachment_type` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;");
            }

            $attachment_id = $db->query("SHOW COLUMNS FROM engine4_activity_comments LIKE 'attachment_id' ")->fetch();
            if (empty($attachment_id)) {
                $db->query("ALTER TABLE  `engine4_activity_comments` ADD  `attachment_id` INT( 11 ) NULL DEFAULT  '0';");
            }

            $params = $db->query("SHOW COLUMNS FROM engine4_activity_comments LIKE 'params' ")->fetch();
            if (empty($params)) {
                $db->query("ALTER TABLE  `engine4_activity_comments` ADD  `params` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;");
            }
        }

        $table_engine4_album_albums_exist = $db->query("SHOW TABLES LIKE 'engine4_album_albums'")->fetch();
        if ($table_engine4_album_albums_exist) {
//      $column = $db->query("SHOW COLUMNS FROM `engine4_album_albums` LIKE 'type'")->fetch();
//      if (!empty($column)) {
//        $type = $column['Type'];
//        if (!strpos($type, "'wall', 'wall_friend', 'wall_network',")) {
//          $type = str_replace("'wall',", "'wall', 'wall_friend', 'wall_network', 'wall_onlyme', ", $type);
//          $db->query("ALTER TABLE `engine4_album_albums` CHANGE `type` `type` $type CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
//        } else if (!strpos($type, "'wall', 'wall_friend', 'wall_network', 'wall_onlyme',")) {
//          $type = str_replace("'wall', 'wall_friend', 'wall_network', ", "'wall', 'wall_friend', 'wall_network', 'wall_onlyme', ", $type);
//          $db->query("ALTER TABLE `engine4_album_albums` CHANGE `type` `type` $type CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
//        }
//      }

            $album_type = $db->query("SHOW COLUMNS FROM engine4_album_albums LIKE 'type'")->fetch();
            if (!empty($album_type)) {
                $db->query("ALTER TABLE `engine4_album_albums` CHANGE  `type`  `type` ENUM(  'wall','wall_friend','wall_network','wall_onlyme','profile','message','blog','cover','comment' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;");
            }
        }

        $tableObj = $db->query("SHOW TABLES LIKE 'engine4_sitereview_listingtypes'")->fetch();
        $tableNewObj = $db->query("SHOW TABLES LIKE 'engine4_nestedcomment_modules'")->fetch();
        if (!empty($tableObj) && !empty($tableNewObj)) {
            $listingTypes = $db->select()
                    ->from('engine4_sitereview_listingtypes', array('listingtype_id', ''))
                    ->query()
                    ->fetchAll();
            foreach ($listingTypes as $listingType) {
                $listingTypeId = $listingType['listingtype_id'];
                $db->query("INSERT IGNORE INTO `engine4_nestedcomment_modules` (`module`, `resource_type`, `enabled`) 
VALUES ('sitereview', 'sitereview_listing_$listingTypeId', 0)");
            }
        }

        $this->setActivityFeeds();

        parent::onInstall();
    }

    public function setActivityFeeds() {
        $db = $this->getDb();

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("replied", "activity", \'{item:$subject} has replied on your {item:$object:$label}.\', "0", ""), ("replied_replied", "activity", \'{item:$subject} has replied on a {item:$object:$label} you replied on.\', "0", "")');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("liked_replied", "activity", \'{item:$subject} has replied on a {item:$object:$label} you liked.\', "0", "")');

        $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES ( 'notify_liked_replied', 'activity', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]')");

        $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES ('notify_replied', 'activity', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), ('notify_replied_replied', 'activity', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');");

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_blog", "blog", \'{item:$subject} replied to a comment on {item:$owner}\'\'s blog {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_album", "album", \'{item:$subject} replied to a comment on {item:$owner}\'\'s album {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_album_photo", "album", \'{item:$subject} replied to a comment on {item:$owner}\'\'s album photo {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_video", "video", \'{item:$subject} replied to a comment on {item:$owner}\'\'s video {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_poll", "poll", \'{item:$subject} replied to a comment on {item:$owner}\'\'s poll {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_group", "group", \'{item:$subject} replied to a comment on {item:$owner}\'\'s group {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_event", "event", \'{item:$subject} replied to a comment on {item:$owner}\'\'s event {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_classified", "classified", \'{item:$subject} replied to a comment on {item:$owner}\'\'s classified {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("comment_group", "group", \'{item:$subject} commented on {item:$owner}\'\'s group {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("comment_event", "event", \'{item:$subject} commented on {item:$owner}\'\'s event {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');

        $db->query('INSERT IGNORE INTO `engine4_nestedcomment_modules` (`module`, `resource_type`, `enabled`, `params`) VALUES ("advancedactivity", "advancedactivity", "0", \'{"module":"advancedactivity","resource_type":"advancedactivity","taggingContent":["friends"],"advancedactivity_comment_show_bottom_post":"1","aaf_comment_like_box":"0","showComposerOptions":["addSmilies","addPhoto"],"showAsLike":"1","showDislikeUsers":"1","showLikeWithoutIcon":"1","showLikeWithoutIconInReplies":"1"}\')');

        $db->query('INSERT IGNORE INTO `engine4_nestedcomment_modules` (`module`, `resource_type`, `enabled`, `params`) VALUES ("sitestaticpage", "sitestaticpage_page", "0", \'{"taggingContent":["friends"],"showComposerOptions":["addLink","addPhoto", "addSmilies"],"showAsNested":"1","showAsLike":"1","showDislikeUsers":"0","showLikeWithoutIcon":"0","showLikeWithoutIconInReplies":"0","loaded_by_ajax":"1"}\')');

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if (!empty($is_sitemobile_object)) {
            $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES ('nestedcomment', '1', '1', '1', '1')");
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitevideo')
                ->where('enabled = ?', 1);
        $is_sitevideo_object = $select->query()->fetchObject();
        if ($is_sitevideo_object) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitevideo_channel", "sitevideo", \'{item:$subject} replied to a comment on {item:$owner}\'\'s channel {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("sitevideo_activityreply", "sitevideo", \'{item:$subject} has replied on {var:$eventname}.\', 0, "");');

            $db->query("INSERT IGNORE INTO `engine4_nestedcomment_modules` (`module`, `resource_type`, `enabled`) 
VALUES ('sitevideo', 'sitevideo_channel', 0)");

            $db->query("INSERT IGNORE INTO `engine4_nestedcomment_modules` (`module`, `resource_type`, `enabled`) 
VALUES ('sitevideo', 'video', 0)");
        }
    }

    private function getVersion() {
        $db = $this->getDb();

        $errorMsg = '';
        $finalModules = $getResultArray = array();
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'sitealbum' => '4.8.7p4',
            'advancedactivity' => '4.8.9p10',
            'sitestaticpage' => '4.8.6p3'
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
            $errorMsg .= '<div class="tip"><span>Note: Your website does not have the latest version of "' . $modArray['title'] . '". Please upgrade "' . $modArray['title'] . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Advanced Comments Plugin - Nested Comments, Replies, Voting & Attachments Plugin".<br/> Please <a href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
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
