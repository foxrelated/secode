<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WidgetController.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_WidgetController extends Seaocore_Controller_Action_Standard {

  public function init() {
    if (!$this->_helper->requireUser()->isValid())
      return;
  }

  // This is the common function, which calling for all other modules for the "Request Page".
  public function getNotifyAction() {
    $this->view->notification = $notification = $this->_getParam('notification', 0);
    $suggObj = Engine_Api::_()->getItem('suggestion', $notification->object_id);
    if (!empty($suggObj)) {
			$this->view->suggObj = $suggObj;

      if( strstr($suggObj->entity, "sitereview") ) {
        $getListingTypeId = Engine_Api::_()->getItem('sitereview_listing', $suggObj->entity_id)->listingtype_id;
        $getModId = Engine_Api::_()->suggestion()->getReviewModInfo($getListingTypeId);
        $modInfoArray = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed("sitereview_" . $getModId);
        $this->view->modInfoArray = $modInfoArray = $modInfoArray["sitereview_" . $getModId];
      }else {
        $modInfoArray = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($suggObj->entity);
        $this->view->modInfoArray = $modInfoArray = $modInfoArray[$suggObj->entity];
      }
      
      if ($this->isModuleEnabled($modInfoArray['pluginName'])) {
        if ( $suggObj->entity == 'photo' ) {
          $modItemId = $suggObj->sender_id;
        } else {
          $modItemId = $suggObj->entity_id;
        }
        $modObj = Engine_Api::_()->getItem($modInfoArray['itemType'], $modItemId);

	// Check Sender exist on site or not.
	$isSenderExist= Engine_Api::_()->getItem('user', $suggObj->sender_id)->getIdentity();
	if( empty($isSenderExist) ) {
	  Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($suggObj->entity, $suggObj->entity_id, $modInfoArray['notificationType']);
	  $this->_helper->redirector->gotoRoute(array('route' => 'default'));
	  $this->view->modObj = null;
	}

	// If Loggden user have "Friend Suggestion" Which already his friend then that friend suggestion should be delete.
	if( empty($modObj) || (( $suggObj->entity != 'photo' ) && ($modInfoArray['itemType'] == 'user') && !empty($modItemId)) ) {
		$is_user = Engine_Api::_()->getItem('user', $suggObj->entity_id)->getIdentity();
		$isFriend = Engine_Api::_()->getApi('coreFun', 'suggestion')->isMember($modItemId);
		if( empty($is_user) || !empty($isFriend) || empty($modObj) ) {
		  Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($suggObj->entity, $suggObj->entity_id, $modInfoArray['notificationType']);
		  $this->_helper->redirector->gotoRoute(array('route' => 'default'));
		  $this->view->modObj = null;
		}
	}

        // It would be "NULL", If that entry already deleteed from the table.
        if (empty($modObj)) {
          Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($suggObj->entity, $suggObj->entity_id, $modInfoArray['notificationType']);
          $this->_helper->redirector->gotoRoute(array('route' => 'default'));
          $this->view->modObj = null;
        } else {
					$this->view->modObj = $modObj;
          $this->view->senderObj = $senderObj = Engine_Api::_()->getItem('user', $suggObj->sender_id);
          $this->view->sender_name = $this->view->htmlLink($senderObj->getHref(), $senderObj->displayname);
        }
      }else {
	$this->view->modNotEnable = true;
      }
    }else {
			// If suggestion are not available in "Suggestion" table but available in "Notifications table" then we are deleting from "Notifications Table".
			Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('notification_id = ?' => $notification->notification_id));
			$this->_helper->redirector->gotoRoute(array('route' => 'default'));
		}
  }

  // Only call when anybody send friend request.
  public function requestAcceptAction() {
    $notification = $this->_getParam('notification', 0);
		$is_suggestionExist = Engine_Api::_()->getItem('user', $notification->object_id);
		if( empty($is_suggestionExist) ) {
			// If user are not exist then we are deleting the "User Request" which loggden user are gettig.
			Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('notification_id = ?' => $notification->notification_id));
			$this->_helper->redirector->gotoRoute(array('route' => 'default'));
		}else {
			$this->view->notification = $notification;
		}
  }

  // Return: Is module enabled or not.
  public function isModuleEnabled($module_name) {
    return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($module_name);
  }
}
?>