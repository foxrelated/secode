<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UserreviewController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_UserreviewController extends Seaocore_Controller_Action_Standard {

	//COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
	public function init() {
	
		//AUTHORIZATION CHECK
		if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
			return;
	}

  //ACTION FOR VIEW USER REVIEWS
  public function viewAction() {

    $this->view->event_id = $event_id = $this->_getParam('event_id');
    $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
    if (empty($siteevent)) {
      return $this->_forwardCustom('notfound', 'error', 'core');
    }
    
	  //Check event is end or not
		$endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($siteevent->event_id);
		$currentDate = date('Y-m-d H:i:s');
		$endDate = strtotime($endDate);
		$currentDate = strtotime($currentDate);
		$this->view->rateuser = Engine_Api::_()->getDbTable("categories", "siteevent")->isGuestReviewAllowed($siteevent->category_id);
		if ($endDate > $currentDate && empty($this->view->rateuser)) {
			return $this->_forwardCustom('notfound', 'error', 'core');
		}

    $this->view->tab_id = $this->_getParam('tab_id');
    $this->view->user_id = $user_id = $this->_getParam('user_id');
    $this->view->user_subject = $user_subject = Engine_Api::_()->user()->getUser($user_id);

    //GET VIEWER INFO
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    $reviewTable = Engine_Api::_()->getDbtable('userreviews', 'siteevent');

    $this->view->can_rated = $can_rated = $reviewTable->isGuestReviewAllowed(array('event_id' => $event_id, 'user_id' => $user_id, 'viewer_id' => $viewer_id));
    $this->view->myReviews = $reviewTable->myRatings(array('viewer_id' => $viewer_id, 'event_id' => $event_id, 'user_id' => $user_id));
    $this->view->totalReviews = $reviewTable->totalReviews($event_id, $user_id);
    $this->view->averageUserReviews = $reviewTable->averageUserRatings(array('user_id' => $user_id, 'event_id' => $event_id));

    $select = $reviewTable->getUserReviesSelect(array('event_id' => $event_id, 'user_id' => $user_id));
    //GET DATA
    $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setItemCountPerPage(20);
    $this->view->current_page = $current_page = $this->_getParam('page', 1);
    $this->view->paginator = $paginator->setCurrentPageNumber($current_page);

    //UPDATE FORM
    if ($can_rated && $this->view->rateuser) {
      $this->view->update_form = $update_form = new Siteevent_Form_Review_UpdateUserreview(array('item' => $user_subject));
    } else {
			$this->view->form = $form = new Siteevent_Form_Review_Userreview(array('item' => $user_subject));
		}
    $this->_helper->content->setEnabled();
  }

  //ACTION FOR CREATE USER REVIEW
  public function createAction() {

    $this->view->event_id = $event_id = $this->_getParam('event_id');

    $this->view->user_id = $user_id = $this->_getParam('user_id');
    $user_subject = Engine_Api::_()->user()->getUser($user_id);
    $title = $user_subject->getTitle();
    $link = $user_subject->getHref();
    $newTitle = "<b><a href='$link'>$title</a></b>";

    $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $postData = $this->getRequest()->getPost();

    if ($this->getRequest()->isPost() && $postData) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $form = new Siteevent_Form_Review_Create(array('item' => $siteevent));
        $form->populate($postData);
        $otherValues = $form->getValues();

        $values = array_merge($postData, $otherValues);
        $values['event_id'] = $event_id;
        $values['user_id'] = $user_id;
        $values['viewer_id'] = $viewer_id;
        $values['rating'] = $values['review_rate_0'];
        $values['title'] = $values['title'];
        $values['description'] = $values['description'];
        $userreviewsTable = Engine_Api::_()->getDbtable('userreviews', 'siteevent');
        $review = $userreviewsTable->createRow();
        $review->setFromArray($values);
        $review->save();

        if (!empty($viewer_id)) {
          $activityApi = Engine_Api::_()->getDbtable('actions', 'seaocore');

          //ACTIVITY FEED
          $action = $activityApi->addActivity($viewer, $siteevent, 'siteevent_userreview_add', null, array('username' => $newTitle));
          if ($action != null) 
            $activityApi->attachActivity($action, $review);
            $subjectOwner = Engine_Api::_()->getItem('user', $user_id);
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subjectOwner, $viewer, $review, 'siteevent_userreview_add');
          
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  //ACTION FOR UPDATE THE USER REVIEW
  public function updateAction() {

    $this->view->event_id = $event_id = $this->_getParam('event_id');
    $this->view->user_id = $user_id = $this->_getParam('user_id');
    $user_subject = Engine_Api::_()->user()->getUser($user_id);
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $postData = $this->getRequest()->getPost();
    if ($this->getRequest()->isPost() && $postData) {
      $form = new Siteevent_Form_Review_UpdateUserreview(array('item' => $user_subject));
      $form->populate($postData);
      $otherValues = $form->getValues();
      $postData = array_merge($postData, $otherValues);
      Engine_Api::_()->getDbtable('userreviews', 'siteevent')->update(array('rating' => $postData['update_review_rate_0'], 'description' => $postData['description']), array('event_id =?' => $event_id, 'user_id =?' => $user_id, 'viewer_id=?' => $viewer_id));
    }
  }

}