<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: ReviewController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_ReviewController extends Core_Controller_Action_Standard {

	//COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {

		//CHECK SUBJECT
    if (Engine_Api::_()->core()->hasSubject())
      return;

		//SET LISTING SUBJECT
    if (0 != ($listing_id = (int) $this->_getParam('listing_id')) &&
        null != ($list = Engine_Api::_()->getItem('list_listing', $listing_id))) {
      Engine_Api::_()->core()->setSubject($list);
    }
  }

  //ACTION FOR CREATING A NEW REVIEW
  public function createAction() {

		//ONLY LOGGED IN USER CAN CREATE REVIEW
    if (!$this->_helper->requireUser()->isValid())
      return;

		//LISTING SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject('list_listing')->isValid())
      return;

    //GET LISITING
    $this->view->list = $list = Engine_Api::_()->core()->getSubject();

    //MAKE FORM
    $this->view->form = $form = new List_Form_Review_Create();

    //PROCESS FORM
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
				//GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

				//GET FROM VALUES
        $values = $form->getValues();
        $values['owner_id'] = $viewer->getIdentity();
        $values['listing_id'] = $list->getIdentity();

        //CREATE REVIEW
        $reviewTable = Engine_Api::_()->getDbtable('reviews', 'list');
        $review = $reviewTable->createRow();
        $review->setFromArray($values);
        $review->save();

				//INCREASE REVIEW COUNT
				$list->review_count++;
				$list->save();

				//ADD ACTIVITY
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $subject = Engine_Api::_()->core()->getSubject();
        $subjectOwner = $subject->getOwner('user');
        $action = $activityApi->addActivity($viewer, $subject, 'review_list', '', array(
                    'title' => $subject->getTitle(),
            ));
        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $review);
        }

        $db->commit();
				$content_id = $this->_getParam('content_id');
        $url = $this->_helper->url->url(array('listing_id' => $list->getIdentity(), 'user_id' => $list->owner_id, 'slug' => $list->getSlug(), 'tab' => $content_id), 'list_entry_view', true);

        $this->_forward('success', 'utility', 'core', array(
                'parentRefresh' => 3,
                'parentRedirect' => $url,
                'parentRedirectTime' => 1,
                'messages' => array('Your Review has been posted successfully.')
        ));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  //ACTION FOR EDITING REVIEW
  public function editAction() {

		//ONLY LOGGED IN USER CAN EDIT REVIEW
    if (!$this->_helper->requireUser()->isValid())
      return;

		//LISING SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject('list_listing')->isValid())
      return;

		//GET LIST SUBJECT
    $list = Engine_Api::_()->core()->getSubject();

		//GET REVIEW ID AND REVIEW OBJECT
    $review_id = $this->_getParam('id');
    $review = Engine_Api::_()->getItem('list_reviews', $review_id);

		//GET VIEWER
		$viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

		//ONLY REVIEW OWNER AND SUPER ADMIN CAN EDIT REVIEW
    if ($review->owner_id != $viewer_id && $viewer->level_id != 1) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //MAKE FORM
    $this->view->form = $form = new List_Form_Review_Create();
    $form->populate($review->toarray());
    $form->setTitle('Edit your Review');
    $form->submit->setLabel('Save Changes');

    //PROCESS FORM
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

				//GET FORM VALUES
        $values = $form->getValues();
        $values['owner_id'] = $viewer_id;
        $values['listing_id'] = $list->getIdentity();
        $values['modified_date'] = date('Y-m-d H:i:s');
        $review->setFromArray($values);
        $review->save();

        $db->commit();
        $content_id = $this->_getParam('content_id');
        $url = $this->_helper->url->url(array('listing_id' => $list->getIdentity(), 'user_id' => $list->owner_id, 'slug' => $list->getSlug(), 'tab' => $content_id), 'list_entry_view', true);
        $this->_forward('success', 'utility', 'core', array(
                'parentRefresh' => 3,
                'parentRedirect' => $url,
                'parentRedirectTime' => 1,
                'messages' => array('Your Review has been edited successfully.')
        ));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  //ACTION FOR DELETING REVIEW
  public function deleteAction() {

		//ONLY LOGGED IN USER CAN DELETE REVIEW
    if (!$this->_helper->requireUser()->isValid())
      return;

		//SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject('list_listing')->isValid())
      return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

		//GET REVIEW ID AND REVIEW OBJECT
    $review_id = $this->_getParam('id');
    $review = Engine_Api::_()->getItem('list_reviews', $review_id);

		//ONLY REVIEW OWNER AND SUPER ADMIN CAN DELETE REVIEW
    if (!($review->owner_id == $viewer_id || $viewer->level_id == 1)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if ($this->getRequest()->isPost()) {

      $this->view->list = $list = Engine_Api::_()->core()->getSubject();
      $content_id = $this->_getParam('content_id');
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //DELETE REVIEW FROM DATABASE
        $review->delete();

				//DECREASE REVIEW COUNT
				$list->review_count--;
				$list->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //REDIRECT
      $url = $this->_helper->url->url(array('listing_id' => $list->getIdentity(), 'user_id' => $list->owner_id, 'slug' => $list->getSlug(), 'tab' => $content_id), 'list_entry_view', true);

      $this->_forward('success', 'utility', 'core', array(
              'parentRefresh' => 3,
              'parentRedirect' => $url,
              'parentRedirectTime' => 1,
              'messages' => array('Your Review has been deleted successfully.')
      ));
    } else {
      $this->renderScript('review/delete.tpl');
    }
  }

}