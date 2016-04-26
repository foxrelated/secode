<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Suggestions.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Model_DbTable_Suggestions extends Engine_Db_Table {

  protected $_name = 'suggestion_suggestions';
  protected $_rowClass = 'Suggestion_Model_Suggestion';

  public function setSuggestion($ownerId, $entity, $entityId, $notificationType, $emailParams = array()) {
    $senderId = Engine_Api::_()->user()->getViewer()->getIdentity();
    $row = $this->createRow();
    $row->owner_id = $ownerId;
    $row->sender_id = $senderId;
    $row->entity = $entity;
    $row->entity_id = $entityId;
    $row->save();

    $ownerObj = Engine_Api::_()->getItem('user', $ownerId);
    $senderObj = Engine_Api::_()->getItem('user', $senderId);
    // $ownerObj : Object which are geting suggestion.
    // $senderObj : Object which are sending suggestion.
    // $row : Object from which table we'll link.
    // $notificationType :notification type.
    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($ownerObj, $senderObj, $row, $notificationType, $emailParams);
  }

  public function removeSuggestion($entity, $entityId, $notificationType = null) {
    if( strstr($entity, "sitereview") ){ $entity = "sitereview"; }
    
    $getModInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($entity);
    if( empty($getModInfo) ){ return; }
    
    if (empty($notificationType)) {
      $notificationType = $getModInfo[$entity]['notificationType'];
    }
    $ownerId = Engine_Api::_()->user()->getViewer()->getIdentity();
    $tableName = $this->info('name');
    $select = $this->select()
                    ->from($tableName, array('suggestion_id'))
                    ->where('owner_id = ?', $ownerId)
                    ->where('entity = ?', $entity)
                    ->where('entity_id = ?', $entityId);
    $fetchObj = $select->query()->fetchAll();
    if (!empty($fetchObj)) {
      foreach ($fetchObj as $suggestionId) {
        $suggestionId = $suggestionId['suggestion_id'];
        Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_id = ?' => $suggestionId, 'type = ?' => $notificationType));
        $getSuggObj = Engine_Api::_()->getItem('suggestion', $suggestionId);
	if( !empty($getSuggObj) ) {
		$getSuggObj->delete();
	}
        if ($entity == 'photo') {
          Engine_Api::_()->getItem('suggestion_photo', $entityId)->delete();
          Engine_Api::_()->getItem('suggestion_album', $suggestionId)->delete();
        }
      }
    }
    // If entity is "Friend" then we are again re,oved the suggestion of "Few Friend" becouse there are entity is differ.
    if ($entity == 'friend') {
      $this->removeSuggestion('friendfewfriend', $entityId, $notificationType);
    }
  }

  public function removeAllSuggestion($modName) {

    $notificationType = $modName . '_suggestion';
    switch($modName){
      case 'sitepage':	
	$notification_type_array = array('page_suggestion', 'page_album_suggestion', 'page_document_suggestion', 'page_event_suggestion', 'page_music_suggestion', 'page_note_suggestion', 'page_offer_suggestion', 'page_poll_suggestion', 'page_review_suggestion', 'page_video_suggestion');
      break;
      case 'sitebusiness':	
	$notification_type_array = array('business_suggestion', 'business_album_suggestion', 'business_document_suggestion', 'business_event_suggestion', 'business_music_suggestion', 'business_note_suggestion', 'business_offer_suggestion', 'business_poll_suggestion', 'business_review_suggestion', 'business_video_suggestion');
      break;
      case 'sitegroup':	
	$notification_type_array = array('group_suggestion', 'group_album_suggestion', 'group_document_suggestion', 'group_event_suggestion', 'group_music_suggestion', 'group_note_suggestion', 'group_offer_suggestion', 'group_poll_suggestion', 'group_review_suggestion', 'group_video_suggestion');
      break;
      default:	
	$notification_type_array = array($modName . '_suggestion');
      break;
    }

    foreach( $notification_type_array as $notificationType ) {
      $userTable = Engine_Api::_()->getItemTable('activity_notification');
      $memberName = $userTable->info('name');
      $select = $userTable->select()
		      ->from($memberName, array('notification_id', 'object_id'))
		      ->where('object_type = ?', 'suggestion')
		      ->where('type = ?', $notificationType);
      $user_ids_array = $select->query()->fetchAll();
      if (!empty($user_ids_array)) {
	foreach ($user_ids_array as $modules) {
	  if (!empty($modules)) {
	    Engine_Api::_()->getItem('activity_notification', $modules['notification_id'])->delete();
	    Engine_Api::_()->getItem('suggestion', $modules['object_id'])->delete();
	  }
	}
      }
    }
  }

  public function getSendSuggestion($entity) {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (empty($entity)) {
      return;
    }
    $select = $this->select()
                    ->where('entity = ?', $entity)
                    ->where('sender_id = ?', $viewer->getIdentity());

    $select = $select->query()->fetchAll();
    $suggestedUserStr = 0;
    foreach ($select as $notInUser) {
      $suggestedUserStr .= "," . $notInUser['owner_id'];
    }
    $suggestedUserStr = trim($suggestedUserStr, ",");
    return $suggestedUserStr;
  }

  // We pass "suggestion_id" in this table and it will find out entity on that Id and then reurn to us the complate object of suggestion.
  public function getSuggestion($suggId, $getSenderIds = null) {
    if (empty($suggId)) {
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $suggObj = Engine_Api::_()->getItem('suggestion', $suggId);
	if( empty($suggObj) ){ return; } 
    $entity = $suggObj->entity;
    
    if( strstr($entity, "sitereview") ) {
      $isReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitereview");
      if( !empty($isReviewEnabled) ) {
        $listingTypeId = Engine_Api::_()->getItem('sitereview_listing', $suggObj->entity_id)->listingtype_id;
        $getModId = Engine_Api::_()->suggestion()->getReviewModInfo($listingTypeId);
        $entity = $entity . "_" . $getModId;
      }
    }
    
    // if( $entity == 'friendfewfriend' ){ $entity = 'friend'; }
    $getModInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($entity);
    $senderObj = Engine_Api::_()->getItem('user', $suggObj->sender_id);
    if (!empty($suggObj) && !empty($getModInfo) && !empty($getModInfo[$entity]['itemType'])) {
      $modObj = Engine_Api::_()->getItem($getModInfo[$entity]['itemType'], $suggObj->entity_id);
      // If "Mod Object" will be empty then it mense that content has been deleted but suggestion not deleted in that case we are deleting that suggestion forcefully.
      if (!empty($modObj)) {
        $modArray['modInfos'] = $getModInfo[$entity];
        $modArray['senderName'] = $senderObj;
        $modArray['suggObj'] = $suggObj;
        $modArray['modObj'] = $modObj;
        
        // Variable for Sitemobile code.
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')){
        $modArray['modHref'] = $modObj->getHref();
        }
        
        if (!empty($getSenderIds)) {
          $getUserIdsArray = explode(',', $getSenderIds);
          $modArray['senderCount'] = @COUNT($getUserIdsArray);
        }
        return $modArray;
      } else {
        $this->removeSuggestion($entity, $suggObj->entity_id);
        return;
      }
    }
    return;
  }
}