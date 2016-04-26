<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SuggestionPopups.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Controller_Action_Helper_SuggestionPopups extends Zend_Controller_Action_Helper_Abstract {

  function preDispatch() {
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $controller = $front->getRequest()->getControllerName();
    $session = new Zend_Session_Namespace();

    // Condition call for "Send Friend Request" & "Accept Friend Request".
    if ($module == 'user' && $controller == 'friends') {
      switch ($action) {
        case 'add':
          $isPopupEnabled = Engine_Api::_()->suggestion()->getModSettings('user', 'popup');
          if (!empty($isPopupEnabled)) {
            $sending_friend_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('user_id', null);
            $session->isSendRequestPopupOpen = $sending_friend_id;
          }
          break;

        case 'confirm':
          $isPopupEnabled = Engine_Api::_()->suggestion()->getModSettings('user', 'accept_friend_popup');
          if (!empty($isPopupEnabled)) {
            $accept_friend_req_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('user_id', null);
            $session->isAcceptRequestPopupOpen = $accept_friend_req_id;
          }
          break;
      }
    }
  }

  function postDispatch() {
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $controller = $front->getRequest()->getControllerName();
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $session = new Zend_Session_Namespace();
    $curr_url = $front->getRequest()->getRequestUri();
    $session = new Zend_Session_Namespace();
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
    $modSuggFirstClass = '';
    $modFirstClass = '';
    
    // CHECK IF ADMIN
		if(substr($front->getRequest()->getPathInfo(), 1, 5) == "admin") { return; } 


    $sugg_coreversion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;
    if ($sugg_coreversion < '4.1.0') {
      $musicController = 'index';
      $musicAction = 'playlist';
    } else {
      $musicController = 'playlist';
      $musicAction = 'view';
    }


    // When "Loggden user" will send friend request. Then suggestion popup will open after closing the sending the "Friend Request" popup.
    if (isset($session->isSendRequestPopupOpen)) {
      if ($module == 'core' && $action == 'success') {
        $session->isSendRequestPopupClosed = $session->isSendRequestPopupOpen;
        $this->setSettings();
      }
      unset($session->isSendRequestPopupOpen);
    }

    if (isset($session->isSendRequestPopupClosed)) {
      $isTimeout = 1;
      $modName = 'friend';
      $modRedirectValue = $session->isSendRequestPopupClosed;
      $curr_url = $front->getRequest()->getRequestUri();

      $friendObject = Engine_Api::_()->suggestion()->add_friend_suggestion($modRedirectValue, 1, 'add_friend');
      $isShowPopup = count($friendObject);
      if (strpos($curr_url, 'members/friends/add/user_id/') === FALSE) {
        Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion('friend', $modRedirectValue, 'friend_suggestion');
        if (!empty($isShowPopup)) {
          $contentViewPopup = 1;
        }
        unset($session->isSendRequestPopupClosed);
      }
    }

    // When "Loggden user" will accept friend request. Then suggestion popup will open after closing the "Accept Friend Request" popup.
    if (isset($session->isAcceptRequestPopupOpen)) {
      if ($module == 'core' && $action == 'success' && $session->isAcceptRequestPopupOpen != 'confirm') {
        $session->isAcceptRequestPopupClosed = $session->isAcceptRequestPopupOpen;
        $this->setSettings();
      }
      unset($session->isAcceptRequestPopupOpen);
    }

    if (isset($session->isAcceptRequestPopupClosed)) {
      $isTimeout = 1;
      $modName = 'accept_request';
      $modRedirectValue = $session->isAcceptRequestPopupClosed;
      $modRedirectValue = $session->isAcceptRequestPopupClosed;
      $curr_url = $front->getRequest()->getRequestUri();

      $friendObject = Engine_Api::_()->suggestion()->add_friend_suggestion($modRedirectValue, 1, 'accept_request');
      $isShowPopup = count($friendObject);
      if (strpos($curr_url, 'members/friends/confirm/user_id/') === FALSE) {
        Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion('friend', $modRedirectValue, 'friend_suggestion');
        if (!empty($isShowPopup)) {
          $contentViewPopup = 1;
        }
        unset($session->isAcceptRequestPopupClosed);
      }
    }


    // "Suggestion popup" will open after join any "Group" or "Event". Set this another session becouse "Suggestion-Popup" should be open after closing "Join Popup".
    if (isset($session->temp_suggestion_popup_content_join)) {
      $getTempURL = $session->temp_suggestion_popup_content_join['URL'];
      $getTempContentValue = $session->temp_suggestion_popup_content_join['value'];
      $getTempContentName = $session->temp_suggestion_popup_content_join['modName'];
      if (strpos($curr_url, $getTempURL) === FALSE) {
        $contentCreatePopup = 1;
        $isTimeout = 1;
        $modRedirectValue = $getTempContentValue;
        $modName = $getTempContentName;
        unset($session->temp_suggestion_popup_content_join);
      }
    }

    // This is the "Parent Function" for "Open Popup". Popup will open after creating any type "Content". This session set from "addActivity" hook.
    if (isset($session->suggestion_popup_content_info)) {
      $modRedirectValue = $session->suggestion_popup_content_info['value'];
      $reviewListing = !empty($session->suggestion_popup_content_info['listing_id']) ? $session->suggestion_popup_content_info['listing_id']: 0;
      $showPopupFlag = 1;
      $onCreatePopups = 1;
      $modName = $session->suggestion_popup_content_info['modName'];
      $joinFlag = 0;
      if (!empty($session->suggestion_popup_content_info['joinFlag'])) {
        $joinFlag = 1;
      }

      // If there are no friends for suggestion then we are not opening popup.
      $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend($modName, $modRedirectValue, 1);
      $contentCreatePopup = 0;
      if (!empty($modContentObj)) {
        $contentCreatePopup = @COUNT($modContentObj);
      }

      switch ($modName) {
        case 'group':
          $isTimeout = 1;
          if (!empty($joinFlag) && ($module == 'core') && ($action == 'success')) {

            // Set another session variable for open the popup after join "Group". Set this another session becouse "Suggestion-Popup" should be open after closing "Join Popup".
            if (!empty($contentCreatePopup)) {
              $session->temp_suggestion_popup_content_join['URL'] = 'groups/member/join/group_id/';
              $session->temp_suggestion_popup_content_join['value'] = $modRedirectValue;
              $session->temp_suggestion_popup_content_join['modName'] = $modName;
              $contentCreatePopup = 0;
            }
          }
          break;
        case 'event':
          $isTimeout = 1;
          if (!empty($joinFlag) && ($module == 'core') && ($action == 'success')) {

            // Set another session variable for open the popup after join "Event". Set this another session becouse "Suggestion-Popup" should be open after closing "Join Popup".
            if (!empty($contentCreatePopup)) {
              $session->temp_suggestion_popup_content_join['URL'] = 'events/member/join/event_id/';
              $session->temp_suggestion_popup_content_join['value'] = $modRedirectValue;
              $session->temp_suggestion_popup_content_join['modName'] = $modName;
              $contentCreatePopup = 0;
            }
          }
          break;
      }
      unset($session->suggestion_popup_content_info);
    }


    // If "Loggden User" are viewing any "Album" then "There should be show "Suggest to Friend" link" and "Suggestion should be delete Only If loggden user have viewing album suggestion"
    if ($module == 'album' && $controller == 'album' && $action == 'view') {
      $entity = 'album';
      $entityId = Zend_Controller_Front::getInstance()->getRequest()->getParam('album_id', null);

      // If there are no friends for suggestion then we are not opening popup.
      $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('album', $entityId, 1);
      $contentCreatePopup = 0;
      if (!empty($modContentObj)) {
        $contentCreatePopup = @COUNT($modContentObj);
      }

      // Suggest to friend link will not show, If loggden user are viewing the self profile album.
      $modObject = Engine_Api::_()->getItem($entity, $entityId);
      $isSearchable = $modObject->search;
      $isProfile = $modObject->type;
      if ($isProfile == 'profile') {
        $contentCreatePopup = 0;
      }

      $isSuggestToFriendLinkActive = Engine_Api::_()->suggestion()->getModSettings('album', 'link');
      if (!empty($contentCreatePopup) && !empty($user_id) && !empty($isSuggestToFriendLinkActive) && !empty($isSearchable)) {
        // Set the "Class Name" for show the "Suggest to Friend" link on "Album View Page".
        $modSuggFirstClass = 'sugg_display_link_album';
        $modSuggSecondClass = 'user_sugg_link_album';
        $modFirstClass = '.album_options';
        $modSecondClass = '.layout_middle';
        $contentViewPopup = 1;
      }
    }


    // If "Loggden User" are viewing any "Video" then "There should be show "Suggest to Friend" link" and "Suggestion should be delete Only If loggden user have viewing video suggestion"
    if ($module == 'video' && $controller == 'index' && $action == 'view') {
      $entity = 'video';
      $entityId = Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id', null);

      $isSearchable = Engine_Api::_()->getItem($entity, $entityId)->search;
      $isStatus = Engine_Api::_()->getItem($entity, $entityId)->status;
      $isSuggestToFriendLinkActive = Engine_Api::_()->suggestion()->getModSettings('video', 'link');
      if (!empty($user_id) && !empty($isSuggestToFriendLinkActive) && !empty($isSearchable) && !empty($isStatus)) {

	// If there are no friends for suggestion then we are not opening popup.
	$modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('video', $entityId, 1);
	$contentCreatePopup = 0;
	if (!empty($modContentObj)) {
	  $contentCreatePopup = @COUNT($modContentObj);
	}

        // Set the "Class Name" for show the "Suggest to Friend" link on "Video View Page".
        $modSuggFirstClass = 'sugg_display_link_video';
        $modFirstClass = '.video_options';
        $isShowPipe = 1;
        $contentViewPopup = 1;
      }
    }

    // If "Loggden User" are viewing any "Music" then "There should be show "Suggest to Friend" link" and "Suggestion should be delete Only If loggden user have viewing Music suggestion"
    if (($module == 'music' && $controller == $musicController && $action == $musicAction)) {
      $entity = 'music';
      $entityId = Zend_Controller_Front::getInstance()->getRequest()->getParam('playlist_id', null);

      $isSearchable = Engine_Api::_()->getItem('music_playlist', $entityId)->search;
      $isSuggestToFriendLinkActive = Engine_Api::_()->suggestion()->getModSettings('music', 'link');
      if (!empty($user_id) && !empty($isSuggestToFriendLinkActive) && !empty($isSearchable)) {

	// If there are no friends for suggestion then we are not opening popup.
	$modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('music', $entityId, 1);
	$contentCreatePopup = 0;
	if (!empty($modContentObj)) {
	  $contentCreatePopup = @COUNT($modContentObj);
	}

        // Set the "Class Name" for show the "Suggest to Friend" link on "Music View Page".
        $modSuggFirstClass = 'sugg_display_link_music';
        $modFirstClass = '.music_playlist_options';
        $contentViewPopup = 1;
      }
    }

    // If "Loggden User" are viewing any "Poll" then "There should be show "Suggest to Friend" link" and "Suggestion should be delete Only If loggden user have viewing Poll suggestion"
    if ($module == 'poll' && (($controller == 'index') || ($controller == 'poll')) && $action == 'view') {
      $entity = 'poll';
      $entityId = Zend_Controller_Front::getInstance()->getRequest()->getParam('poll_id', null);

      $isSuggestToFriendLinkActive = Engine_Api::_()->suggestion()->getModSettings('poll', 'link');
      if (!empty($user_id) && !empty($isSuggestToFriendLinkActive)) {

	// If there are no friends for suggestion then we are not opening popup.
	$modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('poll', $entityId, 1);
	$contentCreatePopup = 0;
	if (!empty($modContentObj)) {
	  $contentCreatePopup = @COUNT($modContentObj);
	}

        // Set the "Class Name" for show the "Suggest to Friend" link on "Poll View Page".
        $isShowPipe = 1;
        $modSuggFirstClass = 'sugg_display_link_poll';
        $modFirstClass = '.poll_stats';
        $contentViewPopup = 1;
      }
    }

    // If "Loggden User" are viewing any "Classified" then "There should be show "Suggest to Friend" link" and "Suggestion should be delete Only If loggden user have viewing Classified suggestion"
    if ($module == 'classified' && $controller == 'index' && $action == 'view') {
      $entity = 'classified';
      $entityId = Zend_Controller_Front::getInstance()->getRequest()->getParam('classified_id', null);
      $notificationType = 'classified_suggestion';
      Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($entity, $entityId, $notificationType);

      $isSearchable = Engine_Api::_()->getItem($entity, $entityId)->search;
      $isClose = Engine_Api::_()->getItem($entity, $entityId)->closed;
      $isSuggestToFriendLinkActive =  Engine_Api::_()->suggestion()->getModSettings('classified', 'link');
      if (($sugg_coreversion >= '4.1.0') && !empty($user_id) && !empty($isSuggestToFriendLinkActive) && !empty($isSearchable) && empty($isClose)) {
	// If there are no friends for suggestion then we are not opening popup.
	$modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('classified', $entityId, 1);
	$contentCreatePopup = 0;
	if (!empty($modContentObj)) {
	  $contentCreatePopup = @COUNT($modContentObj);
	}

        // Set the "Class Name" for show the "Suggest to Friend" link on "Classified View Page".
        $isShowPipe = 1;
        $modSuggFirstClass = 'sugg_display_link_classified';
        $modFirstClass = '.classified_stats';
        $contentViewPopup = 1;
      }
    }

    // If "Loggden User" are viewing any "Forum-Topics" then "There should be show "Suggest to Friend" link" and "Suggestion should be delete Only If loggden user have viewing Forum-Topics suggestion"
    if ($module == 'forum' && $controller == 'topic' && $action == 'view') {
      $entity = 'forum';
      $entityId = Zend_Controller_Front::getInstance()->getRequest()->getParam('topic_id', null);
      $notificationType = 'forum_suggestion';
      Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($entity, $entityId, $notificationType);

      $isSuggestToFriendLinkActive = Engine_Api::_()->suggestion()->getModSettings('forum', 'link');
      if (!empty($user_id) && !empty($isSuggestToFriendLinkActive)) {

	// If there are no friends for suggestion then we are not opening popup.
	$modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('forum', $entityId, 1);
	$contentCreatePopup = 0;
	if (!empty($modContentObj)) {
	  $contentCreatePopup = @COUNT($modContentObj);
	}
        // Set the "Class Name" for show the "Suggest to Friend" link on "Forum-Topics View Page".
        $modSuggFirstClass = 'sugg_display_link_forum';
        $modFirstClass = '.forum_topic_title_wrapper';
        $contentViewPopup = 1;
      }
    }

    // If "Loggden User" are viewing any "Listing" then suggestion should be delete, Only If loggden user have viewing Listing suggestion".
    if ($module == 'list' && $controller == 'index' && $action == 'view') {
      $entity = 'list';
      $entityId = Zend_Controller_Front::getInstance()->getRequest()->getParam('listing_id', null);
      $notificationType = 'listing_suggestion';
      Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($entity, $entityId, $notificationType);
    }

    // If "Loggden User" are viewing any "Recipe" then suggestion should be delete, Only If loggden user have viewing Recipe suggestion".
    if ($module == 'recipe' && $controller == 'index' && $action == 'view') {
      $entity = 'recipe';
      $entityId = Zend_Controller_Front::getInstance()->getRequest()->getParam('recipe_id', null);
      $notificationType = 'recipe_suggestion';
      Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($entity, $entityId, $notificationType);
    }

    // If "Loggden User" are viewing any "Document" then suggestion should be delete, Only If loggden user have viewing Document suggestion".
    if ($module == 'document' && $controller == 'index' && $action == 'view') {
      $entity = 'document';
      $entityId = Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id', null);
      $notificationType = 'document_suggestion';
      Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($entity, $entityId, $notificationType);
    }
    
    // Delete the current subject suggstion, which hold by loggin user.
    $this->deleteSuggestion();

    if (!empty($contentCreatePopup) || (!empty($contentViewPopup) && empty($onCreatePopups) )) {
      include_once APPLICATION_PATH . '/application/modules/Suggestion/views/scripts/_openPopups.tpl';
    }
  }

  // Delete the current subject suggstion, which hold by loggin user. 
  function deleteSuggestion() {
    $modArray = array('forum', 'classified', 'list', 'recipe', 'document');
    $is_subject = Engine_Api::_()->core()->hasSubject();
    if( !empty($is_subject) ) {
      $entity = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
      $entity_id = Engine_Api::_()->core()->getSubject()->getIdentity();
      if( !in_array($entity, $modArray) && !empty($entity) && !empty($entity_id) ) {        
        if( strstr($entity, "sitereview") ) {
          $isReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitereview");
          if( !empty($isReviewEnabled) ) {
            $listingTypeId = Engine_Api::_()->getItem('sitereview_listing', $entity_id);
            if( !empty($listingTypeId) ) {
              $listingTypeId = $listingTypeId->listingtype_id;
              $getModId = Engine_Api::_()->suggestion()->getReviewModInfo($listingTypeId);
              $entity = $entity . "_" . $getModId; 
            }
          }
        }
	Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($entity, $entity_id);
      }
    } 
  }

  function setSettings() {
    $suggestion_host_type = str_replace("www.", "", strtolower($_SERVER['HTTP_HOST']));
    $suggestion_mixinfo_status = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.mixinfo.status', 0);
    if (!empty($suggestion_mixinfo_status)) {
      $suggestion_view_attempt = convert_uuencode($suggestion_host_type);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('suggestion.view.attempt', $suggestion_view_attempt);
    }
  }
}