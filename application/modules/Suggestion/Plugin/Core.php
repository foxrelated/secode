<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Plugin_Core {

  public function onItemDeleteAfter($event) {
    $payload = $event->getPayload();
    $activitActionType = $payload['type'];
    $activitActionIdentity = $payload['identity'];
    $mod_array = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModWhenDeleteContent();
    // Delete suggestion if user delete
    if (in_array($activitActionType, $mod_array)) {
      switch ($activitActionType) {
        case 'music_playlist': $activitActionType = 'music';
          break;
        case 'sitepagepoll_poll': $activitActionType = 'page_poll';
          break;
        case 'sitepageevent_event': $activitActionType = 'page_event';
          break;
        case 'sitepagereview_review': $activitActionType = 'page_review';
          break;
        case 'sitepagenote_note': $activitActionType = 'page_note';
          break;
        case 'sitepagedocument_document': $activitActionType = 'page_document';
          break;
        case 'sitepagevideo_video': $activitActionType = 'page_video';
          break;
        case 'sitepage_album': $activitActionType = 'page_album';
          break;
        case 'sitepage_page': $activitActionType = 'sitepage';
          break;
      }

      // Delete all entry from "suggestion_rejected" table.
      Engine_Api::_()->getDbtable('rejecteds', 'suggestion')->delete(array(
          'entity = ?' => $activitActionType,
          'entity_id = ?' => $activitActionIdentity
      ));
      // Delete all entry from "Notification table" table.
      $suggestion_table = Engine_Api::_()->getItemTable('suggestion');
      $suggestion_name = $suggestion_table->info('name');
      $select_suggestion = $suggestion_table->select()
                      ->from($suggestion_name, array('suggestion_id'))
                      ->where('entity = ?', $activitActionType)
                      ->where('entity_id = ?', $activitActionIdentity);
      $sugg_suggestion = $select_suggestion->query()->fetchAll();
      if (!empty($sugg_suggestion)) {
        foreach ($sugg_suggestion as $sugg_delete) {
          // Delete from "Notification" table.
          Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_id = ?' => $sugg_delete['suggestion_id'], 'object_type = ?' => 'suggestion'));
        }
      }

      // Delete all entry from "suggestion_suggestions" table.
      Engine_Api::_()->getDbtable('suggestions', 'suggestion')->delete(array(
          'entity = ?' => $activitActionType,
          'entity_id = ?' => $activitActionIdentity
      ));
    }
  }

  public function onUserDeleteBefore($event) {
    $payload = $event->getPayload();
    $user_id = $payload['user_id']; // User id which are delete self or by admin.
    // Delete from "suggestions" table.
    $suggestion_table = Engine_Api::_()->getItemTable('suggestion');
    $suggestion_name = $suggestion_table->info('name');
    // Delete all suggestion which user have.
    $sugg_select_owner = $suggestion_table->select()
                    ->from($suggestion_name, array('suggestion_id'))
                    ->where('owner_id = ?', $user_id);
    $sugg_owner = $sugg_select_owner->query()->fetchAll();
    // User does not have any suggestion.
    if (empty($sugg_owner)) {
      $sugg_owner = array(0);
    }
    // Delete all suggestion which user sent.
    $sugg_select_sender = $suggestion_table->select()
                    ->from($suggestion_name, array('suggestion_id'))
                    ->where('sender_id = ?', $user_id);
    $sugg_sender = $sugg_select_sender->query()->fetchAll();
    // If user does not send any suggestion.
    if (empty($sugg_sender)) {
      $sugg_sender = array(0);
    }
    $sugg_id_array = (array_merge($sugg_owner, $sugg_sender));
    // Delete one by one all entry which user sent or recieved.
    foreach ($sugg_id_array as $row_suggestion) {
      // Value would be 0 if no data return.
      if ($row_suggestion != 0) {
        $sugg_table_select = Engine_Api::_()->getItem('suggestion', $row_suggestion['suggestion_id']);
        if ($sugg_table_select->entity == 'photo') {
          // Delete from "Suggestion_Album" table.
          Engine_Api::_()->getItem('suggestion_album', $sugg_table_select->suggestion_id)->delete();
          // Delete from "Suggestion" table.
          Engine_Api::_()->getItem('suggestion', $sugg_table_select->suggestion_id)->delete();
          // Delete from "Notification_Activity" table.
          Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_id = ?' => $sugg_table_select->suggestion_id, 'object_type = ?' => 'suggestion'));
        } else {
          // Delete from "Suggestion" table.
          Engine_Api::_()->getItem('suggestion', $sugg_table_select->suggestion_id)->delete();
          // Delete from "Notification_Activity" table.
          Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_id = ?' => $sugg_table_select->suggestion_id, 'object_type = ?' => 'suggestion'));
        }
      }
    }

    // Delete all entry from "suggestion_rejected" table.
    $reject_table = Engine_Api::_()->getItemTable('suggestion_rejected');
    $reject_name = $reject_table->info('name');
    $select_reject = $reject_table->select()
                    ->from($reject_name, array('rejected_id'))
                    ->where('owner_id = ?', $user_id);
    $sugg_reject = $select_reject->query()->fetchAll();
    if (!empty($sugg_reject)) {
      foreach ($sugg_reject as $row_reject) {
        Engine_Api::_()->getItem('suggestion_rejected', $row_reject['rejected_id'])->delete();
      }
    }
  }

  // Hook for new user signup
  public function onUserCreateAfter($event) {
    $session = new Zend_Session_Namespace();
    $session->isUserSignupPopupShow = 1;
  }

  // Set session variables for suggest popups.
  public function addActivity($event) {
    $payload = $event->getPayload();
    $activitActionType = $payload['type'];
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $session = new Zend_Session_Namespace();
    $suggestion_show_popup = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.mixinfo.status', 0);

    if( strstr($activitActionType, 'sitereview_new_listtype') && ($module == 'sitereview') && ($action == 'create') ) {
      $temReviewArray = @explode("_", $activitActionType);
      $listingTypeId = @end($temReviewArray);
      $getModId = Engine_Api::_()->suggestion()->getReviewModInfo($listingTypeId);
      if( !empty($getModId) ) {
        $show_popup = Engine_Api::_()->suggestion()->getModSettings("sitereview_" . $getModId, "popup");
        if( !empty($show_popup) ) {
          $modInfo['modName'] = 'sitereview';
          $modInfo['value'] = $payload['object']->listing_id;
          $modInfo['listing_id'] = $listingTypeId;
          $session->suggestion_popup_content_info = $modInfo;
        }
      }
    }
    
    // If "Loggden User" are creating "Forum-Topic" then set the session for popup. We are using this session variable in "postDispatch".
    if (($activitActionType == 'forum_topic_create') && ($module == 'forum') && ($action == 'topic-create') && !empty($suggestion_show_popup)) {
      $show_popup = Engine_Api::_()->suggestion()->getModSettings('forum', 'popup');
      if (!empty($show_popup)) {
        $modInfo['modName'] = 'forum';
        $modInfo['value'] = $payload['object']->topic_id;
        $session->suggestion_popup_content_info = $modInfo;
      }
    }

    // If "Loggden User" are reply on "Forum-Topic" then set the session for popup. We are using this session variable in "postDispatch".
    if (($activitActionType == 'forum_topic_reply') && ($module == 'forum') && ($action == 'post-create') && !empty($suggestion_show_popup)) {
      $show_popup = Engine_Api::_()->suggestion()->getModSettings('forum', 'after_forum_join'); 
      if (!empty($show_popup)) {
        $modInfo['modName'] = 'forum';
        $modInfo['value'] = $payload['object']->topic_id;
        $session->suggestion_popup_content_info = $modInfo;
      }
    }

    // If "Loggden User" are creating "Blog" then set the session for popup. We are using this session variable in "postDispatch".
    if (($activitActionType == 'blog_new') && ($module == 'blog') && ($action == 'create') && !empty($suggestion_show_popup)) {
      if (!empty($payload['object']->search)) {
        $show_popup = Engine_Api::_()->suggestion()->getModSettings('blog', 'popup'); 
        if (!empty($show_popup)) {
          $modInfo['modName'] = 'blog';
          $modInfo['value'] = $payload['object']->blog_id;
          $session->suggestion_popup_content_info = $modInfo;
        }
      }
    }

    // If "Loggden User" are creating "Album" then set the session for popup. We are using this session variable in "postDispatch".
    if (($activitActionType == 'album_photo_new') && ($module == 'album') && ($action == 'upload') && !empty($suggestion_show_popup)) {
      if (!empty($payload['object']->search)) {
        $show_popup = Engine_Api::_()->suggestion()->getModSettings('album', 'popup');
        if (!empty($show_popup)) {
          $modInfo['modName'] = 'album';
          $modInfo['value'] = $payload['object']->album_id;
          $session->suggestion_popup_content_info = $modInfo;
        }
      }
    }

    // If "Loggden User" are creating "Classified" then set the session for popup. We are using this session variable in "postDispatch".
    if (($activitActionType == 'classified_new') && ($module == 'classified') && ($action == 'create') && !empty($suggestion_show_popup)) {
      if (!empty($payload['object']->search) && empty($payload['object']->closed)) {
        $show_popup =  Engine_Api::_()->suggestion()->getModSettings('classified', 'popup');
        if (!empty($show_popup)) {
          $modInfo['modName'] = 'classified';
          $modInfo['value'] = $payload['object']->classified_id;
          $session->suggestion_popup_content_info = $modInfo;
        }
      }
    }

    // If "Loggden User" are creating "Video" then set the session for popup. We are using this session variable in "postDispatch".
    if (($activitActionType == 'video_new') && ($module == 'video') && ($action == 'create') && !empty($suggestion_show_popup)) {
      if (!empty($payload['object']->search) && !empty($payload['object']->status)) {
        $show_popup = Engine_Api::_()->suggestion()->getModSettings('video', 'popup');
        if (!empty($show_popup)) {
          $modInfo['modName'] = 'video';
          $modInfo['value'] = $payload['object']->video_id;
          ;
          $session->suggestion_popup_content_info = $modInfo;
        }
      }
    }

    // If "Loggden User" are creating "Music" then set the session for popup. We are using this session variable in "postDispatch".
    if (($activitActionType == 'music_playlist_new') && ($module == 'music') && ($action == 'create') && !empty($suggestion_show_popup)) {
      if (!empty($payload['object']->search)) {
        $show_popup = Engine_Api::_()->suggestion()->getModSettings('music', 'popup');
        if (!empty($show_popup)) {
          $modInfo['modName'] = 'music';
          $modInfo['value'] = $payload['object']->playlist_id;
          $session->suggestion_popup_content_info = $modInfo;
        }
      }
    }

    // If "Loggden User" are creating "Poll" then set the session for popup. We are using this session variable in "postDispatch".
    if (($activitActionType == 'poll_new') && ($module == 'poll') && ($action == 'create') && !empty($suggestion_show_popup)) {
      $show_popup = Engine_Api::_()->suggestion()->getModSettings('poll', 'popup');
      if (!empty($show_popup)) {
        $modInfo['modName'] = 'poll';
        $modInfo['value'] = $payload['object']->poll_id;
        $session->suggestion_popup_content_info = $modInfo;
      }
    }

    // If "Loggden User" are creating "Group" then set the session for popup. We are using this session variable in "postDispatch".
    if (($activitActionType == 'group_create') && ($module == 'group') && ($action == 'create') && !empty($suggestion_show_popup)) {
      if (!empty($payload['object']->search) && !empty($payload['object']->invite)) {
        $show_popup = Engine_Api::_()->suggestion()->getModSettings('group', 'popup');
        if (!empty($show_popup)) {
          $modInfo['modName'] = 'group';
          $modInfo['value'] = $payload['object']->group_id;
          $session->suggestion_popup_content_info = $modInfo;
        }
      }
    }

    // If "Loggden User" Join "Group" then set the session for popup. We are using this session variable in "postDispatch".
    if (($activitActionType == 'group_join') && ($module == 'group') && ($action == 'join') && !empty($suggestion_show_popup)) {
      if (!empty($payload['object']->search) && !empty($payload['object']->invite)) {
        $show_popup =  Engine_Api::_()->suggestion()->getModSettings('group', 'after_group_join');
        if (!empty($show_popup)) {
          $modInfo['modName'] = 'group';
          $modInfo['value'] = $payload['object']->group_id;
          $modInfo['joinFlag'] = 1;
          $session->suggestion_popup_content_info = $modInfo;
          $session->sugg_group_join = $payload['object']->group_id;
        }
      }
    }

    // If "Loggden User" are creating "Event" then set the session for popup. We are using this session variable in "postDispatch".
    if (($activitActionType == 'event_create') && ($module == 'event') && ($action == 'create') && !empty($suggestion_show_popup)) {
      if (!empty($payload['object']->search)) {
        $show_popup = Engine_Api::_()->suggestion()->getModSettings('event', 'popup');
        if (!empty($show_popup)) {
          $modInfo['modName'] = 'event';
          $modInfo['value'] = $payload['object']->event_id;
          $session->suggestion_popup_content_info = $modInfo;
        }
      }
    }

    // If "Loggden User" join "Event" then set the session for popup. We are using this session variable in "postDispatch".
    if (($activitActionType == 'event_join') && ($module == 'event') && ($action == 'join') && !empty($suggestion_show_popup)) {
      if (!empty($payload['object']->search)) {
        $show_popup = Engine_Api::_()->suggestion()->getModSettings('event', 'after_event_join');
        if (!empty($show_popup)) {
          $modInfo['modName'] = 'event';
          $modInfo['value'] = $payload['object']->event_id;
          $modInfo['joinFlag'] = 1;
          $session->suggestion_popup_content_info = $modInfo;
        }
      }
    }

    // If "Loggden User" are creating "Listing" then set the session for popup. We are using this session variable in "postDispatch".
    if (($activitActionType == 'list_new') && ($module == 'list') && ($action == 'create') && !empty($suggestion_show_popup)) {
      if (!empty($payload['object']->approved) && empty($payload['object']->closed) && !empty($payload['object']->draft)) {
        $show_popup = Engine_Api::_()->suggestion()->getModSettings('list', 'popup');
        if (!empty($show_popup)) {
          $modInfo['modName'] = 'list';
          $modInfo['value'] = $payload['object']->listing_id;
          $session->suggestion_popup_content_info = $modInfo;
        }
      }
    }

    // If "Loggden User" make an profile photo as his photo then "PHoto Suggestion" should be delete for this user.
    if (($activitActionType == 'profile_photo_update') && ($module == 'user') && ($action == 'external-photo')) {
      $getGuide = Zend_Controller_Front::getInstance()->getRequest()->getParam('photo', null);
      $photo_id = Engine_Api::_()->getItemByGuid($getGuide)->photo_id;
      Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion('photo', $photo_id, 'picture_suggestion');
    }

    // If "Loggden User" are creating "Recipe" then set the session for popup. We are using this session variable in "postDispatch".
    if (($activitActionType == 'recipe_new') && ($module == 'recipe') && ($action == 'create') && !empty($suggestion_show_popup)) {
      $show_popup = Engine_Api::_()->suggestion()->getModSettings('recipe', 'popup'); // Engine_Api::_()->getApi('settings', 'core')->getSetting('after.recipe.create');
      if (!empty($show_popup)) {
        $modInfo['modName'] = 'recipe';
        $modInfo['value'] = $payload['object']->recipe_id;
        $session->suggestion_popup_content_info = $modInfo;
      }
    }
  }

  public function onRenderLayoutDefault($event) {

    $session = new Zend_Session_Namespace();
    // If new user signup in your Community then open the "Community-Introduction Popup". If siteadmin set this popups.
    if (isset($session->isUserSignupPopupShow)) {
      $isPopupShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sugg.admin.introduction');
      if (!empty($isPopupShow)) {
        $popup_content = Engine_Api::_()->suggestion()->getIntroductionContent();
        $accept_view_this = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
        $accept_script = <<<EOF
				var data = "$popup_content";
				this.onload = function() {
					Smoothbox.open(data, {autoResize : true});
				}  
EOF;
        $accept_view_this->headScript()->appendScript($accept_script);
      }
      unset($session->isUserSignupPopupShow);
    }
  }
}