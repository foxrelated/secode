<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MainController.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Suggestion_MainController extends Core_Controller_Action_User
{
	public function init() {
		if( !$this->_helper->requireUser()->isValid() ) return;
	}
	 
	 	/** This function use for delete the value from "suggestion" table in the case of, if click on Ignore button from "viewall page" or "view page" .
	 * @return Message.
	 */
// 	 public function suggestionCancelAction()
// 	 {
// 	    //RECIEVE VALUE FROM AJAX
// 	    $sugg_ids = (string) $this->_getParam('sugg_id');
// 	    $entity = (string) $this->_getParam('entity');
// 			if($entity == 'page') {
// 				$entity == 'sitepage';
// 			}
// 	    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
// 	    
// 	    $sender_id_array = explode(",", $sugg_ids);
// 	    foreach ($sender_id_array as $sugg_id)
// 	    {
// 				$entity_id = Engine_Api::_()->getItem('suggestion', $sugg_id)->entity_id;
// 				// Delete from "Notification table" from update tab.
// 				Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_id = ?' => $sugg_id, 'object_type = ?' => 'suggestion'));
// 				Engine_Api::_()->getDbtable('suggestions', 'suggestion')->delete(array('suggestion_id = ?'=>$sugg_id));
// 				if( $entity == 'photo' ) {
// 					Engine_Api::_()->getDbtable('albums', 'suggestion')->delete(array('album_id = ?'=>$sugg_id));
// 					Engine_Api::_()->getDbtable('photos', 'suggestion')->delete(array('photo_id = ?'=>$entity_id));
// 				}
// 	    }
// 	    
// 	    $this->view->status = true;
// 	    $this->view->sugg_page = $entity;
// 	 		$this->view->suggestion_msg = $this->view->translate('Suggestion has been removed successfully.');
// 	 }



	public function getContentAction()	{
		$this->view->modName = $modName	=	$this->_getParam('mod_name');// Getting module name.
		$modId	=	$this->_getParam('mod_id');// Getting module Id.
		$widget_content_count	=	$this->_getParam('widget_content_count');// Getting limit of widgets.
		$display_mod_str	=	$this->_getParam('display_mod_str');// Getting displayed content ids str.
		$modFlag	=	$this->_getParam('modFlag');// Getting Flag Modules name.
		$this->view->is_middleLayoutEnabled = $is_middle_layout_enabled = $this->_getParam('is_middleLayoutEnabled');// Is Function calling from Welcome Tab.
		$this->view->div_id = $div_id = $this->_getParam('div_id');// Ajax responce will take this div_id insted of again counting and make div_id.

		if( ($modName == 'friend_middle') && !empty($is_middle_layout_enabled) ) {
		  $modName = 'friend';
		}

		$viewer = Engine_Api::_()->user()->getViewer();
		$limit	=	1;

		if( ($modName == 'mix') || ($modName == 'explore') || ($modName == 'findFriend') ) {
			$getSugg	=	Engine_Api::_()->suggestion()->mix_suggestions(	$limit,	$modName, $display_mod_str	);
			if( !empty($getSugg) ){ $getSugg = $getSugg[0]; }
			$rejectedModName = $modFlag;
		}else {
			$display_mod_str = Engine_Api::_()->suggestion()->getDisplayModID($modName, $display_mod_str);
			$getSugg	=	Engine_Api::_()->suggestion()->getSuggestions(	$modName, $limit,	$display_mod_str	);
			$rejectedModName = $modName;
		}

		// That suggestion should be insert in "Rejected table".
		Engine_Api::_()->getDbTable('rejecteds', 'suggestion')->setSettings( $rejectedModName, $modId );

		if( empty($getSugg) ) {
			$widget_content_count--;
		}
		$this->view->widgetCount = $widget_content_count;
		$getSugg['count'] = $widget_content_count;
		$this->view->modArray = $getSugg;
	}


	// This function call from "Notification page, view page" when click on Ignore button.
	public function removeNotificationAction()
	{
		//RECIEVE VALUE FROM AJAX
		$entity = (string) $this->_getParam('entity');
		$entity_id = (int) $this->_getParam('entity_id');
		$notificationType = (string) $this->_getParam('notificationType');
		$this->view->responseWithTip = (int) $this->_getParam('responseWithTip');
    
    if( strstr($entity, "sitereview") ) {
      $isReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitereview");
      if( !empty($isReviewEnabled) ) {
        $listingTypeId = Engine_Api::_()->getItem('sitereview_listing', $entity_id)->listingtype_id;
        $getModId = Engine_Api::_()->suggestion()->getReviewModInfo($listingTypeId);
        $entity = $entity . "_" . $getModId;
      }
    }
    
		Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion( $entity, $entity_id, $notificationType );
	}


	 	/** Json Request: This function use for delete the value from "suggestion" table in the case of, if click on Ignore button from "viewall page" or "view page" .
	 * @return Message.
	 */
	 public function suggestionCancelAction()
	 {
	    $entity = (string) $this->_getParam('entity');
	    $entity_id = (int) $this->_getParam('entity_id');
	    $notificationType = (string) $this->_getParam('notificationType');
      
      if( strstr($entity, "sitereview") ) {
        $isReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitereview");
        if( !empty($isReviewEnabled) ) {
          $listingTypeId = Engine_Api::_()->getItem('sitereview_listing', $entity_id)->listingtype_id;
          $getModId = Engine_Api::_()->suggestion()->getReviewModInfo($listingTypeId);
          $entity = $entity . "_" . $getModId;
        }
      }
      
			Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion( $entity, $entity_id, $notificationType );
	    $this->view->status = true;
	 }
}