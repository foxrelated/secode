<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: modInfo.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Api_ModInfo extends Core_Api_Abstract {

  /**
   * Return array, when loggd-in user view the "view page" or "viewall listing page" then required supported modules name for suggestion.
   */
  public function getModNameForOldFunction() {
	$defaultMod = array(
		'friend',
		'user',
		'group',
		'friendphoto',
		'event',
		'classified',
		'album',
		'video',
		'music',
		'blog',
		'poll',
		'forum',
		'document',
		'siteestore',
    'sitestore',
    'sitestoreproduct',
    'siteevent',
		'list',
		'sitepage',
		'sitepagealbum',
    'sitepagereport',
		'sitepagedocument',
		'sitepagepoll',
		'sitepagevideo',
		'sitepageevent',
		'sitepagenote',
		'sitepagereview',
		'sitepagemusic',
		'sitepageoffer',
		'recipe',
		'sitebusiness',
		'sitebusinessalbum',
		'sitebusinessdocument',
		'sitebusinesspoll',
		'sitebusinessvideo',
		'sitebusinessevent',
		'sitebusinessnote',
		'sitebusinessreview',
		'sitebusinessmusic',
		'sitebusinessoffer',      
		'sitegroup',
		'sitegroupalbum',
		'sitegroupdocument',
		'sitegrouppoll',
		'sitegroupvideo',
		'sitegroupevent',
		'sitegroupnote',
		'sitegroupreview',
		'sitegroupmusic',
		'sitegroupoffer',
	);

	// Added the 3rd party integrate plugin in the array.
	$getAddedMod = Engine_Api::_()->getDbtable('modinfos', 'suggestion')->getModContent();
	if (!empty($getAddedMod)) {
	  foreach ($getAddedMod as $mod) {
		if (!empty($mod) && !empty($mod['module']) && (strstr($mod['module'], "sitereview") || empty($mod['default']))) {
		  $defaultMod[] = $mod['module'];
		}
	  }
	}
  $defaultMod = array_unique($defaultMod);
	return $defaultMod;
  }

  /**
   * Return array, when loggd-in user delete any content which integrate with suggestion then its suggestions shoould be deleted modules name are following.
   */
  public function getModWhenDeleteContent() {
	$defaultMod = array(
		'group',
		'blog',
		'event',
		'album',
		'classified',
		'video',
		'music_playlist',
		'poll',
		'forum',
		'document',
		'list',
		'sitepagepoll_poll',
		'sitepageevent_event',
		'sitepagereview_review',
		'sitepagenote_note',
		'sitepagedocument_document',
		'sitepagevideo_video',
		'sitepage_album',
		'sitepage_page',
		'sitepagemusic_playlist',
		'sitepageoffer_offer',
		'recipe',
		'sitebusinesspoll_poll',
		'sitebusinessevent_event',
		'sitebusinessreview_review',
		'sitebusinessnote_note',
		'sitebusinessdocument_document',
		'sitebusinessvideo_video',
		'sitebusiness_album',
		'sitebusiness_business',
		'sitebusinessmusic_playlist',
		'sitebusinessoffer_offer',      
		'sitegrouppoll_poll',
		'sitegroupevent_event',
		'sitegroupreview_review',
		'sitegroupnote_note',
		'sitegroupdocument_document',
		'sitegroupvideo_video',
		'sitegroup_album',
		'sitegroup_group',
		'sitegroupmusic_playlist',
		'sitegroupoffer_offer'
	);

	// Added the 3rd party integrate plugin in the array.
	$getAddedMod = Engine_Api::_()->getDbtable('modinfos', 'suggestion')->getModContent();
	if (!empty($getAddedMod)) {
	  foreach ($getAddedMod as $mod) {
		if (!empty($mod) && !empty($mod['item_type']) && empty($mod['default'])) {
		  $defaultMod[] = $mod['item_type'];
		}
	  }
	}

	return $defaultMod;
  }

  /**
   * Find out the Modules name which support of "Suggestion/Recommendation Plugin" and "Enabled".
   *
   * @param $modName: Optional, If want to check only this modules then return TRUE and FALSE.
   * @return "Array" or "TRUE or FALSE".
   */
  public function getSupportedMods($modName = null) {
	$modSupport = false;
	$moduleArray = array(
		'friend' => 0,
		'user' => 0,
		'messagefriend' => 0,
		'friendfewfriend' => 0,
		'friendphoto' => 0,
		'album' => 1,
		'blog' => 1,
		'classified' => 1,
		'document' => 1,
		'event' => 1,
		'forum' => 1,
		'group' => 1,
		'list' => 1,
		'music' => 1,
		'poll' => 1,
		'video' => 1,
		'recipe' => 1,
		'sitepage' => 1,
		'sitepagevideo' => 1,
		'sitepagenote' => 1,
		'sitepagedocument' => 1,
		'sitepageevent' => 1,
		'sitepagepoll' => 1,
		'sitepagereview' => 1,
		'sitepagealbum' => 1,
    'sitepagereport' => 1,
		'sitepagemusic' => 1,
		'sitepageoffer' => 1,
		'photo' => 0,
		'sitebusiness' => 1,
		'sitebusinessvideo' => 1,
		'sitebusinessnote' => 1,
		'sitebusinessdocument' => 1,
		'sitebusinessevent' => 1,
		'sitebusinesspoll' => 1,
		'sitebusinessreview' => 1,
		'sitebusinessalbum' => 1,
		'sitebusinessmusic' => 1,
		'sitebusinessoffer' => 1,      
		'sitegroup' => 1,
		'sitegroupvideo' => 1,
		'sitegroupnote' => 1,
		'sitegroupdocument' => 1,
		'sitegroupevent' => 1,
		'sitegrouppoll' => 1,
		'sitegroupreview' => 1,
		'sitegroupalbum' => 1,
		'sitegroupmusic' => 1,
		'sitegroupoffer' => 1,
    'sitestoreproduct' => 1,
    'siteevent' => 1,
    'sitestore' => 1
	);

	if (empty($modName)) {// If we are not pass any module name then we will return array of modules which should be enabled and support to suggestion plugin.
	  foreach ($moduleArray as $moduleName => $flag) {
		$isModEnabled = $this->isModEnabled($moduleName);
		if (!empty($isModEnabled) || empty($flag)) {
		  $supportModArray[] = $moduleName;
		}
	  }
	  $modSupport = $supportModArray;
	} else {// If we are passing module name then we will return 0/1, 1) plugin should be support of suggestion plugin 2) should be enabled.
	  $isModEnabled = $this->isModEnabled($modName);
	  $isModSupport = array_key_exists($modName, $moduleArray);
	  if (!empty($isModSupport) && (empty($moduleArray[$modName]) || !empty($isModEnabled))) {
		$modSupport = true;
	  }
	}
	return $modSupport;
  }

  /**
   * Find out the modules enabled or disabled.
   *
   * @param $modName: Module name, which we find out that this module "Enabled" or "Disabled".
   * @return true or false.
   */
  public function isModEnabled($modName) {
    if( strstr($modName, "sitereview") ){ $modName = "sitereview"; }
	if (($modName != 'friend') || ($modName != 'user')) {
	  return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($modName);
	}
	return true;
  }

  public function isSuggestionEnabled($modName) {
	$modObj = Engine_Api::_()->getDbTable('modinfos', 'suggestion')->getSelectedModContent($modName);
	if (!empty($modObj) && !empty($modObj[0]['enabled'])) {
	  return true;
	}
	return false;
  }

  /**
   * Function which reaturn the information of the plugin. If $pluginname is empty then function will return all modules info else return perticular modules info.
   *
   * @param $pluginname: Module name.
   * @return Array
   */
  public function getPluginDetailed($pluginname = null) {
    $getSupportMod = array();
	if (empty($pluginname)) {
	  $getSupportMod = $this->getSupportedMods();    
	  $getModResult = Engine_Api::_()->getDbtable('modinfos', 'suggestion')->getModContent(array('module', 'default'));
	  if (!empty($getModResult)) {
		foreach ($getModResult as $modInfo1) {
		  if (empty($modInfo1['default'])) {
			$getSupportMod[] = $modInfo1['module'];
		  }
		}
	  }
	} else {
	  $getSupportMod[] = $pluginname;
	}
	foreach ($getSupportMod as $modName) {
	  $modInfo[$modName] = $this->getPluginInfos($modName);
	}
	return $modInfo;
  }
  
  private function isExtension($modName) {
    $extensionArray = array(
        "page_document",  "pagedocument", "page_poll", "sitepagepoll", "page_video", "sitepagevideo",
        "page_event", "sitepageevent", "page_review", "sitepagereview", "page_album", "sitepagealbum", 
        "page_note", "sitepagenote", "page_music", "sitepagemusic", "page_offer", "sitepageoffer", 
        "page_report", "sitepagereport", "photo",
        
        "business_document",  "businessdocument", "business_poll", "sitebusinesspoll", "business_video", "sitebusinessvideo",
        "business_event", "sitebusinessevent", "business_review", "sitebusinessreview", "business_album", "sitebusinessalbum", 
        "business_note", "sitebusinessnote", "business_music", "sitebusinessmusic", "business_offer", "sitebusinessoffer", 
        "business_report", "sitebusinessreport",        
        
        "group_document",  "sitegroupdocument", "group_poll", "sitegrouppoll", "group_video", "sitegroupvideo",
        "group_event", "sitegroupevent", "group_review", "sitegroupreview", "group_album", "sitegroupalbum", 
        "group_note", "sitegroupnote", "group_music", "sitegroupmusic", "group_offer", "sitegroupoffer", 
        "group_report", "sitegroupreport"
    );
    if(in_array($modName, $extensionArray))
            return true;
    return false;
  }

  /**
   * Find out the modules complete information, which required in suggestion plugin for the modules.
   *
   * @param $modName: Module name.
   * @return Array
   */
  public function getPluginInfos($modName) {
	$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
	$getResArray = array('accept_request', 'few_friend');
  
  $pageExtensionArray = array('sitepagereview', 'sitepagenote', 'sitepagealbum', 'sitepagereport', 'sitepageevent', 'sitepagevideo', 'sitepagemusic', 'sitepageoffer', 'sitepagepoll', 'sitepagedocument');
  $businessExtensionArray = array('sitebusinessreview', 'sitebusinessnote', 'sitebusinessalbum', 'sitebusinessreport', 'sitebusinessevent', 'sitebusinessvideo', 'sitebusinessmusic', 'sitebusinessoffer', 'sitebusinesspoll', 'sitebusinessdocument');
  
  $groupExtensionArray = array('sitegroupreview', 'sitegroupnote', 'sitegroupalbum', 'sitegroupreport', 'sitegroupevent', 'sitegroupvideo', 'sitegroupmusic', 'sitegroupoffer', 'sitegrouppoll', 'sitegroupdocument');
  
  $getResArray = array_merge($getResArray, array_merge($pageExtensionArray, array_merge($businessExtensionArray, $groupExtensionArray)));
  
  $isExtension = $this->isExtension($modName);
  if( empty($isExtension) ) {
	  if( !in_array($modName, $getResArray) ) {
	  $modTabInfo = Engine_Api::_()->getDbtable('modinfos', 'suggestion')->getSelectedModContent($modName);
	  if (empty($modTabInfo) && empty($modTabInfo[0])) {
	    return;
	  }
	}
	}

	$modTabInfo = !empty($modTabInfo[0])? $modTabInfo[0]: FALSE;
	$pluginInfo = null;
	$isModule = true;
	$getDefaultFlag = false;
	switch ($modName) {
	  case 'messagefriend':
		$pluginInfo['pluginName'] = 'user';
		$pluginInfo['templateInfoFlag'] = 'messagefriend';
		$pluginInfo['displayName'] = 'Send Message';
		$pluginInfo['itemType'] = 'user';
		$pluginInfo['notificationType'] = '';
		$pluginInfo['buttonLabel'] = '';
		$pluginInfo['idColumnName'] = 'user_id';
		$pluginInfo['findFriendFunName'] = '';
		$isModule = false;
		break;
	  case 'friendfewfriend':
		$pluginInfo['pluginName'] = 'user';
		$pluginInfo['templateInfoFlag'] = 'friend';
		$pluginInfo['defaultFriendship'] = true;
		$pluginInfo['displayName'] = 'Friend';
		$pluginInfo['itemType'] = 'user';
		$pluginInfo['notificationType'] = 'friend_suggestion';
		$pluginInfo['buttonLabel'] = '';
		$pluginInfo['idColumnName'] = 'user_id';
		$pluginInfo['findFriendFunName'] = 'few_friend_suggestion';
		$isModule = false;
		break;
	  case 'friendphoto':
		$pluginInfo['pluginName'] = 'user';
		$pluginInfo['templateInfoFlag'] = 'photo';
		$pluginInfo['displayName'] = 'Suggest Picture';
		$pluginInfo['itemType'] = 'user';
		$pluginInfo['notificationType'] = '';
		$pluginInfo['buttonLabel'] = '';
		$pluginInfo['idColumnName'] = 'user_id';
		$pluginInfo['findFriendFunName'] = '';
		$isModule = false;
		break;
	  case 'friend':
	  case 'user':
		$pluginInfo['pluginName'] = $modTabInfo['module'];
		$pluginInfo['templateInfoFlag'] = 'friend';
		$pluginInfo['defaultFriendship'] = true;
		$pluginInfo['displayName'] = $modTabInfo['item_title'];
		$pluginInfo['itemType'] = $modTabInfo['item_type'];
		$pluginInfo['notificationType'] = $modTabInfo['notification_type'];
		$buttonLabelFlag = $modTabInfo['button_title'];
		$pluginInfo['buttonLabel'] = $view->translate($buttonLabelFlag);
		$pluginInfo['idColumnName'] = $modTabInfo['field_name'];
		$pluginInfo['findFriendFunName'] = 'add_friend_suggestion';
		$isModule = false;
		break;
	  case 'accept_request':
		$pluginInfo['findFriendFunName'] = 'add_friend_suggestion';
		$isModule = false;
		break;
	  case 'few_friend':
		$pluginInfo['notificationType'] = 'friend_suggestion';
		$pluginInfo['findFriendFunName'] = 'few_friend_suggestion';
		$pluginInfo['modName'] = 'friend';
		$isModule = false;
		break;
	  case 'sitepage':
	  case 'page':
		$pluginInfo['pluginName'] = $modName;
		$pluginInfo['templateInfoFlag'] = 'sitepage';
		$pluginInfo['displayName'] = $modTabInfo['item_title'];
		$pluginInfo['itemType'] = 'sitepage_page';
		$pluginInfo['notificationType'] = 'page_suggestion';
		$buttonLabelFlag = $modTabInfo['button_title'];
		$pluginInfo['buttonLabel'] = $view->translate($buttonLabelFlag);
		$pluginInfo['idColumnName'] = 'page_id';
		$pluginInfo['findFriendFunName'] = 'sitepage_suggestion';
		$pluginInfo['ownerColumnName'] = 'owner_id';
		break;
	  case 'page_review':
	  case 'sitepagereview':
		$pluginInfo['pluginName'] = 'sitepagereview';
		$pluginInfo['templateInfoFlag'] = 'sitepagereview';
		$pluginInfo['displayName'] = 'Page Review';
		$pluginInfo['itemType'] = 'sitepagereview_review';
		$pluginInfo['notificationType'] = 'page_review_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Page Review');
		$pluginInfo['idColumnName'] = 'review_id';
		$pluginInfo['findFriendFunName'] = 'sitepage_suggestion';
		break;
	  case 'page_note':
	  case 'sitepagenote':
		$pluginInfo['pluginName'] = 'sitepagenote';
		$pluginInfo['templateInfoFlag'] = 'sitepagenote';
		$pluginInfo['displayName'] = 'Page Note';
		$pluginInfo['itemType'] = 'sitepagenote_note';
		$pluginInfo['notificationType'] = 'page_note_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Page Note');
		$pluginInfo['idColumnName'] = 'note_id';
		$pluginInfo['findFriendFunName'] = 'sitepage_suggestion';
		break;
	  case 'page_album':
	  case 'sitepagealbum':
		$pluginInfo['pluginName'] = 'sitepagealbum';
		$pluginInfo['templateInfoFlag'] = 'sitepagealbum';
		$pluginInfo['displayName'] = 'Page Album';
		$pluginInfo['itemType'] = 'sitepage_album';
		$pluginInfo['notificationType'] = 'page_album_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Page Album');
		$pluginInfo['idColumnName'] = 'album_id';
		$pluginInfo['findFriendFunName'] = 'sitepage_suggestion';
		break;
  	case 'page_report':
	  case 'sitepagereport':
		$pluginInfo['pluginName'] = 'sitepagereport';
		$pluginInfo['templateInfoFlag'] = 'sitepagereport';
		$pluginInfo['displayName'] = 'Page Report';
		$pluginInfo['itemType'] = 'sitepage_report';
		$pluginInfo['notificationType'] = 'page_report_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Page report');
		$pluginInfo['idColumnName'] = 'album_id';
		$pluginInfo['findFriendFunName'] = 'sitepage_suggestion';
		break;
	  case 'page_event':
	  case 'sitepageevent':
		$pluginInfo['pluginName'] = 'sitepageevent';
		$pluginInfo['templateInfoFlag'] = 'sitepageevent';
		$pluginInfo['displayName'] = 'Page Event';
		$pluginInfo['itemType'] = 'sitepageevent_event';
		$pluginInfo['notificationType'] = 'page_event_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('Join Page Event');
		$pluginInfo['idColumnName'] = 'event_id';
		$pluginInfo['findFriendFunName'] = 'sitepage_suggestion';
		break;
	  case 'page_video':
	  case 'sitepagevideo':
		$pluginInfo['pluginName'] = 'sitepagevideo';
		$pluginInfo['templateInfoFlag'] = 'sitepagevideo';
		$pluginInfo['displayName'] = 'Page Video';
		$pluginInfo['itemType'] = 'sitepagevideo_video';
		$pluginInfo['notificationType'] = 'page_video_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Page Video');
		$pluginInfo['idColumnName'] = 'video_id';
		$pluginInfo['findFriendFunName'] = 'sitepage_suggestion';
		break;
	  case 'page_music':
	  case 'sitepagemusic':
		$pluginInfo['pluginName'] = 'sitepagemusic';
		$pluginInfo['templateInfoFlag'] = 'sitepagemusic';
		$pluginInfo['displayName'] = 'Page Music';
		$pluginInfo['itemType'] = 'sitepagemusic_playlist';
		$pluginInfo['notificationType'] = 'page_music_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('Listen to this Page Music');
		$pluginInfo['idColumnName'] = 'playlist_id';
		$pluginInfo['findFriendFunName'] = 'sitepage_suggestion';
		break;
	  case 'page_offer':
	  case 'sitepageoffer':
		$pluginInfo['pluginName'] = 'sitepageoffer';
		$pluginInfo['templateInfoFlag'] = 'sitepageoffer';
		$pluginInfo['displayName'] = 'Page Offer';
		$pluginInfo['itemType'] = 'sitepageoffer_offer';
		$pluginInfo['notificationType'] = 'page_offer_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Page Offer');
		$pluginInfo['idColumnName'] = 'offer_id';
		$pluginInfo['findFriendFunName'] = 'sitepage_suggestion';
		break;
	  case 'page_poll':
	  case 'sitepagepoll':
		$pluginInfo['pluginName'] = 'sitepagepoll';
		$pluginInfo['templateInfoFlag'] = 'sitepagepoll';
		$pluginInfo['displayName'] = 'Page Poll';
		$pluginInfo['itemType'] = 'sitepagepoll_poll';
		$pluginInfo['notificationType'] = 'page_poll_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('Vote on this Page Poll');
		$pluginInfo['idColumnName'] = 'poll_id';
		$pluginInfo['findFriendFunName'] = 'sitepage_suggestion';
		break;
	  case 'page_document':
	  case 'sitepagedocument':
		$pluginInfo['pluginName'] = 'sitepagedocument';
		$pluginInfo['templateInfoFlag'] = 'sitepagedocument';
		$pluginInfo['displayName'] = 'Page Document';
		$pluginInfo['itemType'] = 'sitepagedocument_document';
		$pluginInfo['notificationType'] = 'page_document_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Page Document');
		$pluginInfo['idColumnName'] = 'document_id';
		$pluginInfo['findFriendFunName'] = 'sitepage_suggestion';
		break;
	  case 'sitebusiness':
	  case 'business':
		$pluginInfo['pluginName'] = $modName;
		$pluginInfo['templateInfoFlag'] = 'sitebusiness';
		$pluginInfo['displayName'] = $modTabInfo['item_title'];
		$pluginInfo['itemType'] = 'sitebusiness_business';
		$pluginInfo['notificationType'] = 'business_suggestion';
		$buttonLabelFlag = $modTabInfo['button_title'];
		$pluginInfo['buttonLabel'] = $view->translate($buttonLabelFlag);
		$pluginInfo['idColumnName'] = 'business_id';
		$pluginInfo['findFriendFunName'] = 'sitebusiness_suggestion';
		$pluginInfo['ownerColumnName'] = 'owner_id';
		break;
	  case 'business_review':
	  case 'sitebusinessreview':
		$pluginInfo['pluginName'] = 'sitebusinessreview';
		$pluginInfo['templateInfoFlag'] = 'sitebusinessreview';
		$pluginInfo['displayName'] = 'Business Review';
		$pluginInfo['itemType'] = 'sitebusinessreview_review';
		$pluginInfo['notificationType'] = 'business_review_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Business Review');
		$pluginInfo['idColumnName'] = 'review_id';
		$pluginInfo['findFriendFunName'] = 'sitebusiness_suggestion';
		break;
	  case 'business_note':
	  case 'sitebusinessnote':
		$pluginInfo['pluginName'] = 'sitebusinessnote';
		$pluginInfo['templateInfoFlag'] = 'sitebusinessnote';
		$pluginInfo['displayName'] = 'Business Note';
		$pluginInfo['itemType'] = 'sitebusinessnote_note';
		$pluginInfo['notificationType'] = 'business_note_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Business Note');
		$pluginInfo['idColumnName'] = 'note_id';
		$pluginInfo['findFriendFunName'] = 'sitebusiness_suggestion';
		break;
	  case 'business_album':
	  case 'sitebusinessalbum':
		$pluginInfo['pluginName'] = 'sitebusinessalbum';
		$pluginInfo['templateInfoFlag'] = 'sitebusinessalbum';
		$pluginInfo['displayName'] = 'Business Album';
		$pluginInfo['itemType'] = 'sitebusiness_album';
		$pluginInfo['notificationType'] = 'business_album_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Business Album');
		$pluginInfo['idColumnName'] = 'album_id';
		$pluginInfo['findFriendFunName'] = 'sitebusiness_suggestion';
		break;
	  case 'business_event':
	  case 'sitebusinessevent':
		$pluginInfo['pluginName'] = 'sitebusinessevent';
		$pluginInfo['templateInfoFlag'] = 'sitebusinessevent';
		$pluginInfo['displayName'] = 'Business Event';
		$pluginInfo['itemType'] = 'sitebusinessevent_event';
		$pluginInfo['notificationType'] = 'business_event_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('Join Business Event');
		$pluginInfo['idColumnName'] = 'event_id';
		$pluginInfo['findFriendFunName'] = 'sitebusiness_suggestion';
		break;
	  case 'business_video':
	  case 'sitebusinessvideo':
		$pluginInfo['pluginName'] = 'sitebusinessvideo';
		$pluginInfo['templateInfoFlag'] = 'sitebusinessvideo';
		$pluginInfo['displayName'] = 'Business Video';
		$pluginInfo['itemType'] = 'sitebusinessvideo_video';
		$pluginInfo['notificationType'] = 'business_video_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Business Video');
		$pluginInfo['idColumnName'] = 'video_id';
		$pluginInfo['findFriendFunName'] = 'sitebusiness_suggestion';
		break;
	  case 'business_music':
	  case 'sitebusinessmusic':
		$pluginInfo['pluginName'] = 'sitebusinessmusic';
		$pluginInfo['templateInfoFlag'] = 'sitebusinessmusic';
		$pluginInfo['displayName'] = 'Business Music';
		$pluginInfo['itemType'] = 'sitebusinessmusic_playlist';
		$pluginInfo['notificationType'] = 'business_music_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('Listen to this Business Music');
		$pluginInfo['idColumnName'] = 'playlist_id';
		$pluginInfo['findFriendFunName'] = 'sitebusiness_suggestion';
		break;
	  case 'business_offer':
	  case 'sitebusinessoffer':
		$pluginInfo['pluginName'] = 'sitebusinessoffer';
		$pluginInfo['templateInfoFlag'] = 'sitebusinessoffer';
		$pluginInfo['displayName'] = 'Business Offer';
		$pluginInfo['itemType'] = 'sitebusinessoffer_offer';
		$pluginInfo['notificationType'] = 'business_offer_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Business Offer');
		$pluginInfo['idColumnName'] = 'offer_id';
		$pluginInfo['findFriendFunName'] = 'sitebusiness_suggestion';
		break;
	  case 'business_poll':
	  case 'sitebusinesspoll':
		$pluginInfo['pluginName'] = 'sitebusinesspoll';
		$pluginInfo['templateInfoFlag'] = 'sitebusinesspoll';
		$pluginInfo['displayName'] = 'Business Poll';
		$pluginInfo['itemType'] = 'sitebusinesspoll_poll';
		$pluginInfo['notificationType'] = 'business_poll_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('Vote on this Business Poll');
		$pluginInfo['idColumnName'] = 'poll_id';
		$pluginInfo['findFriendFunName'] = 'sitebusiness_suggestion';
		break;
	  case 'business_document':
	  case 'sitebusinessdocument':
		$pluginInfo['pluginName'] = 'sitebusinessdocument';
		$pluginInfo['templateInfoFlag'] = 'sitebusinessdocument';
		$pluginInfo['displayName'] = 'Business Document';
		$pluginInfo['itemType'] = 'sitebusinessdocument_document';
		$pluginInfo['notificationType'] = 'business_document_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Business Document');
		$pluginInfo['idColumnName'] = 'document_id';
		$pluginInfo['findFriendFunName'] = 'sitebusiness_suggestion';
		break;
 
	  case 'sitegroup':
		$pluginInfo['pluginName'] = $modName;
		$pluginInfo['templateInfoFlag'] = 'sitegroup';
		$pluginInfo['displayName'] = $modTabInfo['item_title'];
		$pluginInfo['itemType'] = 'sitegroup_group';
		$pluginInfo['notificationType'] = 'group_suggestion';
		$buttonLabelFlag = $modTabInfo['button_title'];
		$pluginInfo['buttonLabel'] = $view->translate($buttonLabelFlag);
		$pluginInfo['idColumnName'] = 'group_id';
		$pluginInfo['findFriendFunName'] = 'sitegroup_suggestion';
		$pluginInfo['ownerColumnName'] = 'owner_id';
		break;
	  case 'group_review':
	  case 'sitegroupreview':
		$pluginInfo['pluginName'] = 'sitegroupreview';
		$pluginInfo['templateInfoFlag'] = 'sitegroupreview';
		$pluginInfo['displayName'] = 'Group Review';
		$pluginInfo['itemType'] = 'sitegroupreview_review';
		$pluginInfo['notificationType'] = 'group_review_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Group Review');
		$pluginInfo['idColumnName'] = 'review_id';
		$pluginInfo['findFriendFunName'] = 'sitegroup_suggestion';
		break;
	  case 'group_note':
	  case 'sitegroupnote':
		$pluginInfo['pluginName'] = 'sitegroupnote';
		$pluginInfo['templateInfoFlag'] = 'sitegroupnote';
		$pluginInfo['displayName'] = 'Group Note';
		$pluginInfo['itemType'] = 'sitegroupnote_note';
		$pluginInfo['notificationType'] = 'group_note_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Group Note');
		$pluginInfo['idColumnName'] = 'note_id';
		$pluginInfo['findFriendFunName'] = 'sitegroup_suggestion';
		break;
	  case 'group_album':
	  case 'sitegroupalbum':
		$pluginInfo['pluginName'] = 'sitegroupalbum';
		$pluginInfo['templateInfoFlag'] = 'sitegroupalbum';
		$pluginInfo['displayName'] = 'Group Album';
		$pluginInfo['itemType'] = 'sitegroup_album';
		$pluginInfo['notificationType'] = 'group_album_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Group Album');
		$pluginInfo['idColumnName'] = 'album_id';
		$pluginInfo['findFriendFunName'] = 'sitegroup_suggestion';
		break;
	  case 'group_event':
	  case 'sitegroupevent':
		$pluginInfo['pluginName'] = 'sitegroupevent';
		$pluginInfo['templateInfoFlag'] = 'sitegroupevent';
		$pluginInfo['displayName'] = 'Group Event';
		$pluginInfo['itemType'] = 'sitegroupevent_event';
		$pluginInfo['notificationType'] = 'group_event_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('Join Group Event');
		$pluginInfo['idColumnName'] = 'event_id';
		$pluginInfo['findFriendFunName'] = 'sitegroup_suggestion';
		break;
	  case 'group_video':
	  case 'sitegroupvideo':
		$pluginInfo['pluginName'] = 'sitegroupvideo';
		$pluginInfo['templateInfoFlag'] = 'sitegroupvideo';
		$pluginInfo['displayName'] = 'Group Video';
		$pluginInfo['itemType'] = 'sitegroupvideo_video';
		$pluginInfo['notificationType'] = 'group_video_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Group Video');
		$pluginInfo['idColumnName'] = 'video_id';
		$pluginInfo['findFriendFunName'] = 'sitegroup_suggestion';
		break;
	  case 'group_music':
	  case 'sitegroupmusic':
		$pluginInfo['pluginName'] = 'sitegroupmusic';
		$pluginInfo['templateInfoFlag'] = 'sitegroupmusic';
		$pluginInfo['displayName'] = 'Group Music';
		$pluginInfo['itemType'] = 'sitegroupmusic_playlist';
		$pluginInfo['notificationType'] = 'group_music_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('Listen to this Group Music');
		$pluginInfo['idColumnName'] = 'playlist_id';
		$pluginInfo['findFriendFunName'] = 'sitegroup_suggestion';
		break;
	  case 'group_offer':
	  case 'sitegroupoffer':
		$pluginInfo['pluginName'] = 'sitegroupoffer';
		$pluginInfo['templateInfoFlag'] = 'sitegroupoffer';
		$pluginInfo['displayName'] = 'Group Offer';
		$pluginInfo['itemType'] = 'sitegroupoffer_offer';
		$pluginInfo['notificationType'] = 'group_offer_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Group Offer');
		$pluginInfo['idColumnName'] = 'offer_id';
		$pluginInfo['findFriendFunName'] = 'sitegroup_suggestion';
		break;
	  case 'group_poll':
	  case 'sitegrouppoll':
		$pluginInfo['pluginName'] = 'sitegrouppoll';
		$pluginInfo['templateInfoFlag'] = 'sitegrouppoll';
		$pluginInfo['displayName'] = 'Group Poll';
		$pluginInfo['itemType'] = 'sitegrouppoll_poll';
		$pluginInfo['notificationType'] = 'group_poll_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('Vote on this Group Poll');
		$pluginInfo['idColumnName'] = 'poll_id';
		$pluginInfo['findFriendFunName'] = 'sitegroup_suggestion';
		break;
	  case 'group_document':
	  case 'sitegroupdocument':
		$pluginInfo['pluginName'] = 'sitegroupdocument';
		$pluginInfo['templateInfoFlag'] = 'sitegroupdocument';
		$pluginInfo['displayName'] = 'Group Document';
		$pluginInfo['itemType'] = 'sitegroupdocument_document';
		$pluginInfo['notificationType'] = 'group_document_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View this Group Document');
		$pluginInfo['idColumnName'] = 'document_id';
		$pluginInfo['findFriendFunName'] = 'sitegroup_suggestion';
		break;
  
  
	  case 'photo':
		$pluginInfo['pluginName'] = 'user';
		$pluginInfo['templateInfoFlag'] = 'photo';
		$pluginInfo['displayName'] = 'Profile Photo';
		$pluginInfo['itemType'] = 'user';
		$pluginInfo['notificationType'] = 'picture_suggestion';
		$pluginInfo['buttonLabel'] = $view->translate('View Photo Suggestion');
		$pluginInfo['idColumnName'] = 'user_id';
		break;
	  case 'list':
	  case 'listing':
		$pluginInfo['pluginName'] = $modName;
		$pluginInfo['findFriendFunName'] = 'list_suggestion';
		$getDefaultFlag = true;
		break;
	  case 'music':
		$pluginInfo['pluginName'] = $modName;
		$getDefaultFlag = true;
		break;
	  case 'forum':
		$pluginInfo['pluginName'] = $modName;
		$getDefaultFlag = true;
		break;
	  case 'poll':
		$pluginInfo['pluginName'] = $modName;
		$getDefaultFlag = true;
		break;
	  case 'album':
		$pluginInfo['pluginName'] = $modName;
		$pluginInfo['tagName'] = 'album_photo';
		$getDefaultFlag = true;
		break;
	  case 'event':
	  case 'group':
		$pluginInfo['pluginName'] = $modName;
		$getDefaultFlag = true;
		break;
	  case 'recipe':
		$pluginInfo['pluginName'] = $modName;
		$pluginInfo['findFriendFunName'] = 'recipe_suggestion';
		$getDefaultFlag = true;
		break;
	  default:
		$pluginInfo['pluginName'] = $modTabInfo['module'];
		$getDefaultFlag = true;
		break;
	}
  
	if (!empty($getDefaultFlag)) {
	  $pluginInfo['templateInfoFlag'] = $modTabInfo['module'];
	  $pluginInfo['displayName'] = $modTabInfo['item_title'];
	  $pluginInfo['itemType'] = $modTabInfo['item_type'];
	  $pluginInfo['notificationType'] = $modTabInfo['notification_type'];
	  $buttonLabelFlag = $modTabInfo['button_title'];
	  $pluginInfo['buttonLabel'] = $view->translate($buttonLabelFlag);
	  $pluginInfo['ownerColumnName'] = $modTabInfo['owner_field'];
	  $pluginInfo['idColumnName'] = $modTabInfo['field_name'];
	}
  
	if (!empty($isModule)) {
	  if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($pluginInfo['pluginName'])) {
		return;
	 }
	}
	return $pluginInfo;
  }

  /**
   * Return the name of functions name array for pass module name. So pass modules will return the results for widgets only from the returned functions.
   *
   * @param $modName: Module Name.
   * @param $is_viewer: There are deffrent function return for "Loggden User" or "Loggdeout User".
   * @return Array.
   */
  public function getFunArray($modName, $is_viewer = null) {

	if (empty($modName)) {
	  return;
	}

	$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	if (!empty($is_viewer)) {
	  $viewer_id = 0;
	}

	$getSuggMod = Engine_Api::_()->suggestion()->getModSettings($modName, 'quality');

	$modInfoArray = array('popularMod' => 0);

	switch ($modName) {
	  case 'poll':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('likedByFriend' => 1, 'createByFriend' => 1, 'popularMod' => 0, 'commentByFriend' => 1, 'voteByFriend' => 1);
		}
		break;
	  case 'album':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('likedByFriend' => 1, 'createByFriend' => 1, 'popularMod' => 0, 'commentByFriend' => 1, 'tagByFriend' => 1);
		}
		break;
	  case 'blog':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('likedByFriend' => 1, 'createByFriend' => 1, 'popularMod' => 0, 'commentByFriend' => 1);
		}
		break;
	  case 'classified':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('likedByFriend' => 1, 'createByFriend' => 1, 'popularMod' => 0, 'commentByFriend' => 1);
		}
		break;
	  case 'event':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('popularMod' => 0, 'createByFriend' => 1, 'intrestedByFriend' => 1, 'orderOfAttendByFriend' => 1, 'viewModSameCategory' => 0, 'mostAttendingByFriend' => 1);
		}
		break;
	  case 'group':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('createByFriend' => 1, 'popularMod' => 0, 'intrestedByFriend' => 1, 'viewModSameCategory' => 0);
		}
		break;
	  case 'forum':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('createByFriend' => 1, 'popularMod' => 0, 'replyByFriend' => 1, 'viewByFriend' => 1, 'mostViewReplyByFriend' => 1);
		}
		break;
	  case 'music':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('likedByFriend' => 1, 'createByFriend' => 1, 'popularMod' => 0, 'commentByFriend' => 1);
		}
		break;
	  case 'video':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('likedByFriend' => 1, 'createByFriend' => 1, 'popularMod' => 0, 'commentByFriend' => 1, 'ratedByFriend' => 1);
		}
		break;
	  case 'document':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('likedByFriend' => 1, 'createByFriend' => 1, 'popularMod' => 0, 'commentByFriend' => 1);
		}
		break;
	  case 'list':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('likedByFriend' => 1, 'createByFriend' => 1, 'popularMod' => 0, 'commentByFriend' => 1);
		}
		break;
	  case 'recipe':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('likedByFriend' => 1, 'createByFriend' => 1, 'popularMod' => 0, 'commentByFriend' => 1);
		}
		break;
	  case 'sitepage':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('likedByFriend' => 1, 'createByFriend' => 1, 'popularMod' => 0, 'commentByFriend' => 1);
		}
		break;
	  case 'sitebusiness':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('likedByFriend' => 1, 'createByFriend' => 1, 'popularMod' => 0, 'commentByFriend' => 1);
		}
		break;
	  case 'sitegroup':
		if ($getSuggMod == 2) {
		  $modInfoArray = array('likedByFriend' => 1, 'createByFriend' => 1, 'popularMod' => 0, 'commentByFriend' => 1);
		}
		break;
	  case 'messagefriend':
		$modInfoArray = array('messagefriend_mix_sugg' => 0);
		break;
	  case 'friendfewfriend':
		$modInfoArray = array('friendfewfriend_mix_sugg' => 0);
		break;
	  case 'friendphoto':
		$modInfoArray = array('friendphoto_mix_sugg' => 0);
		break;
	  case 'friend':
	  case 'user':
		$modInfoArray = array('friend_mix_sugg' => 0);
		break;
	  default:
		if ($getSuggMod == 2) {
		  $modInfoArray = array('createByFriend' => 1, 'popularMod' => 0);
		}
		break;
	}

	if (empty($viewer_id) || !empty($is_recommendation)) { // For loggd-out user
	  foreach ($modInfoArray as $funName => $status) {
		if (empty($status)) {
		  $funNameArray[] = $funName;
		}
	  }
	} else {
	  $funNameArray = array_keys($modInfoArray);
	}
	$modInfo[$modName] = $funNameArray;
	return $modInfo;
  }

  /**
   * Getting the order for SQL  according to the modules.
   *
   * @param $modName: Module Name.
   * @return Array.
   */
  public function getOrder($modName) {
  $orderArray = array();
	switch ($modName) {
	  case 'poll':
		$orderArray = array('vote_count DESC', 'comment_count DESC', 'view_count DESC');
		break;
	  case 'album':
		$orderArray = array('comment_count DESC', 'view_count DESC');
		break;
	  case 'blog':
		$orderArray = array('comment_count DESC', 'view_count DESC');
		break;
	  case 'classified':
		$orderArray = array('comment_count DESC', 'view_count DESC');
		break;
	  case 'event':
		$orderArray = array('member_count DESC', 'view_count DESC');
		break;
	  case 'group':
		$orderArray = array('member_count DESC', 'view_count DESC');
		break;
	  case 'forum':
		$orderArray = array('post_count DESC', 'view_count DESC');
		break;
	  case 'music':
		$orderArray = array('play_count DESC');
		break;
	  case 'video':
		$orderArray = array('view_count DESC', 'rating DESC');
		break;
	  case 'document':
		$orderArray = array('rating DESC', 'comment_count DESC', 'views DESC');
		break;
	  case 'list':
		$orderArray = array('comment_count DESC', 'view_count DESC', 'like_count DESC');
		break;
	  case 'sitepage':
		$orderArray = array('comment_count DESC', 'view_count DESC', 'like_count DESC');
		break;
	  case 'sitebusiness':
		$orderArray = array('comment_count DESC', 'view_count DESC', 'like_count DESC');
		break;
	  case 'sitegroup':
		$orderArray = array('comment_count DESC', 'view_count DESC', 'like_count DESC');
		break;
	  case 'recipe':
		$orderArray = array('comment_count DESC', 'view_count DESC', 'like_count DESC');
		break;
	}

  if( empty($orderArray) ){ return; }
	$getOrderKey = array_rand($orderArray, 1);
	$getOrder = $orderArray[$getOrderKey];
	return $getOrder;
  }

  /**
   * Getting modules special condition. Result( Suggestion ) will depend on these conditions.
   *
   * @param $modName: Module Name.
   * @return Array.
   */
  public function getModCondition($modName) {
	$getModCondition = array();
	switch ($modName) {
	  case 'classified':
		$getModCondition = array('search' => 1, 'closed' => 0);
		break;
	  case 'poll':
		$getPollVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('poll')->version;
		if ($getPollVersion < '4.1.5') {
		  $getModCondition = array('search' => 1, 'is_closed' => 0);
		} else {
		  $getModCondition = array('search' => 1, 'closed' => 0);
		}
		break;
	  case 'recipe':
		$getModCondition = array('search' => 1, 'closed' => 0, 'draft' => 1);
		break;
	  case 'document':
		$getModCondition = array('draft' => 0, 'approved' => 1, 'status' => 1);
		break;
	  case 'forum':
		$getModCondition = array('closed' => 0);
		break;
	  case 'group':
		$getModCondition = array('search' => 1, 'invite' => 1);
		break;
	  case 'list':
		$getModCondition = array('approved' => 1, 'closed' => 0, 'draft' => 1);
		break;
	  case 'video':
		$getModCondition = array('search' => 1, 'status' => 1);
		break;
	  case 'sitepage':
		$getModCondition = array('closed' => 0, 'approved' => 1, 'declined' => 0, 'draft' => 1);
		break;
	  case 'sitebusiness':
		$getModCondition = array('closed' => 0, 'approved' => 1, 'declined' => 0, 'draft' => 1);
		break;
	  case 'sitegroup':
		$getModCondition = array('closed' => 0, 'approved' => 1, 'declined' => 0, 'draft' => 1);
		break;
	  default:
		$getModCondition = array('search' => 1);
		break;
	}
	return $getModCondition;
  }

  /**
   * Getting module name from the entity. We are using this on viewal page.
   * @return Array.
   */
  public function getModName() {
	return array(
		'page_video' => 'sitepagevideo',
		'page_note' => 'sitepagenote',
		'page_document' => 'sitepagedocument',
		'page_event' => 'sitepageevent',
		'page_poll' => 'sitepagepoll',
		'page_review' => 'sitepagereview',
		'page_album' => 'sitepagealbum',
    'page_report' => 'sitepagereport',
		'page_music' => 'sitepagemusic',
		'page_offer' => 'sitepageoffer',
		'business_video' => 'sitebusinessvideo',
		'business_note' => 'sitebusinessnote',
		'business_document' => 'sitebusinessdocument',
		'business_event' => 'sitebusinessevent',
		'business_poll' => 'sitebusinesspoll',
		'business_review' => 'sitebusinessreview',
		'business_album' => 'sitebusinessalbum',
		'business_music' => 'sitebusinessmusic',
		'business_offer' => 'sitebusinessoffer',      
		'group_video' => 'sitegroupvideo',
		'group_note' => 'sitegroupnote',
		'group_document' => 'sitegroupdocument',
		'group_event' => 'sitegroupevent',
		'group_poll' => 'sitegrouppoll',
		'group_review' => 'sitegroupreview',
		'group_album' => 'sitegroupalbum',
		'group_music' => 'sitegroupmusic',
		'group_offer' => 'sitegroupoffer'
	);
  }

}
?>
