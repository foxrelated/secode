<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Modinfos.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Model_DbTable_Modinfos extends Engine_Db_Table {

    protected $_name = 'suggestion_module_settings';
    protected $_rowClass = 'Suggestion_Model_Modinfo';

    // @Return: Array of the modules, which is restricted in suggestion plugin.
    public function getRestrictedModule() {
        $getModArray = array();
        $getRegisterModName = $this->getModContent(array('module'));
        if (!empty($getRegisterModName)) {
            foreach ($getRegisterModName as $modName) {
                $getModArray[] = $modName['module'];
            }
        }
        $restrictedModArray = array(
            'activity', 'advancedactivity', 'sitealbum', 'sitelike', 'featuredcontent',
            'sitepagelikebox', 'mobi', 'advancedslideshow', 'birthday', 'birthdayemail',
            'communityad', 'dbbackup', 'facebookse', 'facebooksefeed', 'facebooksepage',
            'feedback', 'groupdocument', 'grouppoll', 'mapprofiletypelevel', 'mcard', 'poke',
            'sitealbum', 'sitepageinvite', 'siteslideshow', 'socialengineaddon', 'seaocore', 'suggestion',
		'userconnection', 'sitepageform', 'sitepageadmincontact', 'sitebusinessbadge', 'sitebusinessoffer','sitebusinessdiscussion', 'sitebusinesslikebox', 'sitebusinessinvite', 'sitebusinessform', 'sitebusinessadmincontact',
            'chat', 'sitepagepoll', 'sitepagedocument', 'sitepagevideo', 'sitepageevent',
            'sitepageoffer', 'sitepagebadge', 'sitepagealbum', 'sitepagediscussion', 'sitepagegeolocation',
            'sitepageintegration', 'sitepagemusic', 'sitepagereview', 'sitepageurl', 'sitepagewishlist',
            'sitebusinessalbum', 'sitebusinessdocument', 'sitebusinessevent', 'sitebusinessmusic', 'sitebusinessnote',
		'sitebusinessoffer', 'sitebusinesspoll', 'sitebusinessreview', 'sitebusinessvideo', 'sitepagenote', 'sitegroupbadge', 'sitegroupoffer','sitegroupdiscussion', 'sitegrouplikebox', 'sitegroupinvite', 'sitegroupform', 'sitegroupadmincontact', 'sitegroupalbum', 'sitegroupdocument', 'sitegroupevent', 'sitegroupmusic', 'sitegroupnote',
            'sitegroupoffer', 'sitegrouppoll', 'sitegroupreview', 'sitegroupvideo');
        $getFinalArray = array_merge($restrictedModArray, $getModArray);
        return $getFinalArray;
    }

    // @Return: Object of the table, which hold the modules detailes init.
    public function getModContent($colArray = array()) {
        $tableName = $this->info('name');
        $select = $this->select();
        if (!empty($colArray)) {
            $select->setIntegrityCheck(false)->from($tableName, $colArray);
        }
        $select = $select->query()->fetchAll();
        return $select;
    }

    // @Return: Object of the selected module.
    public function getSelectedModContent($modName = null) {
        if (empty($modName)) {
            return;
        }
        if (strstr($modName, 'friend')) {
            $modName = 'user';
        }
        $tableName = $this->info('name');

  if( strstr($modName, "sitereview_") ) {
            $modInfo = @explode("_", $modName);
            $select = $this->select()->where('modinfo_id = ?', $modInfo[1]);
  }else {
            $select = $this->select()->where('module = ?', $modName);
        }

        $select = $select->query()->fetchAll();
        return $select;
    }

    // Set the suggestion values, If admin disable any module from the manage module page.
    public function setNotificationType($modInfo) {
        $modName = $modInfo['module'];
        $itemTitle = $modInfo['item_title'];
        $modEnabled = $modInfo['enabled'];

        $queryObj = Zend_Db_Table_Abstract::getDefaultAdapter();
        if (strstr($modName, 'user')) {
            $modName = 'friend';
        }

        switch ($modName) {
            case 'sitepage':
                $notification_type_array = array('page_suggestion' => 'page', 'page_album_suggestion' => 'page album', 'page_document_suggestion' => 'page document', 'page_event_suggestion' => 'page event', 'page_music_suggestion' => 'page music', 'page_note_suggestion' => 'page note', 'page_offer_suggestion' => 'page offer', 'page_poll_suggestion' => 'page poll', 'page_review_suggestion' => 'page review', 'page_video_suggestion' => 'page video');
                break;
            case 'sitebusiness':
                $notification_type_array = array('business_suggestion' => 'business', 'business_album_suggestion' => 'business album', 'business_document_suggestion' => 'business document', 'business_event_suggestion' => 'business event', 'business_music_suggestion' => 'business music', 'business_note_suggestion' => 'business note', 'business_offer_suggestion' => 'business offer', 'business_poll_suggestion' => 'business poll', 'business_review_suggestion' => 'business review', 'business_video_suggestion' => 'business video');
                break;
            case 'sitegroup':
                $notification_type_array = array('group_suggestion' => 'group', 'group_album_suggestion' => 'group album', 'group_document_suggestion' => 'group document', 'group_event_suggestion' => 'group event', 'group_music_suggestion' => 'group music', 'group_note_suggestion' => 'group note', 'group_offer_suggestion' => 'group offer', 'group_poll_suggestion' => 'group poll', 'group_review_suggestion' => 'group review', 'group_video_suggestion' => 'group video');
                break;
            default:
                $notificationBody = strtolower($itemTitle);
                $notification_type_array = array($modName . '_suggestion' => $notificationBody);
                break;
        }

        foreach ($notification_type_array as $notificationType => $bodyTitle) {
            if (empty($modEnabled)) {
                $notificationBody = '{item:$subject} has suggested to you a {item:$object:' . strtolower($bodyTitle) . '}.';
                $queryObj->query("INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type` , `module` , `body` , `is_request` ,`handler`) VALUES ('$notificationType', 'suggestion', '$notificationBody', 1, 'suggestion.widget.get-notify')");

                $queryObj->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type` , `module` ,`vars`) VALUES ('notify_" . $notificationType . "', 'suggestion', '[suggestion_sender], [suggestion_entity], [email], [link]')");
            } else {
                $queryObj->query("DELETE FROM `engine4_activity_notificationtypes` WHERE `engine4_activity_notificationtypes`.`type` = '" . $notificationType . "' LIMIT 1");

                $queryObj->query("DELETE FROM `engine4_core_mailtemplates` WHERE `engine4_core_mailtemplates`.`type` = 'notify_" . $notificationType . "' LIMIT 1");

                Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeAllSuggestion($modName);
            }
        }
    }

    public function getMod($modName) {
        $tableName = $this->info('name');
        $select = $this->select()
                ->where('module LIKE ?', $modName);
        $fetch = $select->query()->fetchAll();
        return $fetch;
    }

    public function getValue($pluginName = null) {
        $modInfo = null;
        $name = $this->info('name');



        $select = $this->select()->from($name, array('module', 'recommendation'));
        if (!empty($pluginName)) {
            $select->where('module = ?', $pluginName);
        }
               
        $pluginsArray = $select->query()->fetchAll();
        if (!empty($pluginsArray)) {
            foreach ($pluginsArray as $pluginInfo) {
                //Sitemobile mode
                if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
//                    $modulestable = Engine_Api::_()->getDbtable('modules', 'sitemobile');
//                    $enabledModuleNames = $modulestable->getEnabledModuleNames();
                    if (Engine_API::_()->sitemobile()->isSupportedModule($pluginInfo['module']) && $pluginInfo['module'] != 'user') {
                       $modInfo[$pluginInfo['module']] = array('status' => $pluginInfo['recommendation']);
                    }
                    //End sitemobile Code.
                } else {
                    $modInfo[$pluginInfo['module']] = array('status' => $pluginInfo['recommendation']);
                }
            }
        }
        return $modInfo;
    }

    public function getModName($callingFrom = null) {
        if ($callingFrom == 'findFriend') {
            return array('friend');
        } // This eill return only for "Find Friend Suggestion" Page.
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $getModName = array();
        $getValue = $this->getValue();
        foreach ($getValue as $key => $value) {
            if (!empty($value['status']) && ($key != 'user')) {
                // Condition: We do'nt required "messagefriend", "friendfewfriend", "friendphoto" and "friend" suggestion for "Loggde-out" suggestion. So we are using "continue" for "Loggd-out" user.
                $isModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
                $modName = $key;


                if (($key == 'messagefriend') || ($key == 'friendfewfriend') || ($key == 'friendphoto') || ($key == 'friend')) {
                    if (empty($viewer_id)) {
                        continue;
                    } else {
                        $modName = 'user';
                        $isModuleEnabled = true;
                    }
                }

                $isSuggModEnabled = Engine_Api::_()->getDbtable('modinfos', 'suggestion')->getMod($modName);

        if (!empty($isModuleEnabled) && ( !empty($isSuggModEnabled) && $isSuggModEnabled[0]['enabled'] )   ) {
                    $getModName[] = $key;
                }
            }
        }
        return $getModName;
    }
}