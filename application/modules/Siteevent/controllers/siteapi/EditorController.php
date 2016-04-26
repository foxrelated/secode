<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    TopicController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Siteevent_EditorController extends Siteapi_Controller_Action_Standard {

    public function init() {

        //SET EVENT SUBJECT
        if (0 != ($event_id = (int) $this->_getParam('event_id')) &&
                null != ($siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id))) {
            Engine_Api::_()->core()->setSubject($siteevent);
        }
    }

    public function homeAction() {

        //GET EDITOR TABLE
        $editorTable = Engine_Api::_()->getDbTable('editors', 'siteevent');

        //GET EDITORS
        $params = array();
        if (!$this->_getParam('superEditor', 1)) {
            $params['user_id'] = $editorTable->getSuperEditor('user_id');
        }
        $editors = $editorTable->getEditorsEvent($params);

        $totalEditors = Count($editors);
        if ($totalEditors <= 0) {
            $this->respondWithError('no_record');
        }
        foreach ($editors as $editor) {
            $params = $editor->toArray();
            $arams["owner_title"] = $editor->getUserTitle($editor->user_id);
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($editor, true);
            $params = array_merge($params, $getContentImages);
            $bodyParams['type'] = 'editor';
            $bodyParams['owner_id'] = $editor->user_id;
            $params['totalReviews'] = Engine_Api::_()->getDbTable('reviews', 'siteevent')->totalReviews($bodyParams);
            $tempResponse[] = $params;
        } if (!empty($tempResponse))
            $response['response'] = $tempResponse;
        $this->respondWithSuccess($response, true);
    }

    //NONE USER SPECIFIC METHODS
    public function profileAction() {

        //GET USER ID
        $user_id = $this->_getParam('user_id');

        //IF EDITOR
        $isEditor = Engine_Api::_()->getDbTable('editors', 'siteevent')->isEditor($user_id, 0);

        //IF USER IS NOT FOUND
        if (empty($user_id) || empty($isEditor)) {
            $this->respondWithError('no_record');
        }

        //SET USER SUBJECT
        $user = Engine_Api::_()->getItem('user', $user_id);
        Engine_Api::_()->core()->setSubject($user);
        $params = array('displayname' => $user->getTitle());

        //GET EDITOR TABLE
        $editor = Engine_Api::_()->getDbTable('editors', 'siteevent')->getEditor($user_id);


        //GET EDITOR DETAILS
        $values = array();
        $values[] = $editor->toArray();
        $values['visible'] = 1;
        $values['editorReviewAllow'] = 1;
        $params['eventTypes'] = 'Events';

        //editor badge 
        $show_badge = $this->_getParam('show_badge', 1);
        $badge_photo_id = 0;
        if (!empty($editor->badge_id) && $show_badge) {
            $values['badge_photo_id'] = Engine_Api::_()->getItemTable('siteevent_badge')->getBadgeColumn($editor->badge_id, 'badge_main_id');
        }

        //GET TOTAL REVIEW COUNT
        $reviewTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');

        $params = array();
        $params['owner_id'] = $user->getIdentity();
        $params['type'] = 'user';
        $values['totalUserReviews'] = $reviewTable->totalReviews($params);
        $params['type'] = 'editor';
        $values['totalEditorReviews'] = $reviewTable->totalReviews($params);
        $values['totalReviews'] = $values['totalUserReviews'] + $values['totalEditorReviews'];

        //GET TOTAL COMMENT COUNT
        $values['totalComments'] = $reviewTable->countReviewComments($user->getIdentity());

        //GET TOTAL CATEGORIES IN WHICH THIS EDITOR HAS GIVEN REVIEWS
        $values['totalCategoriesReview'] = $reviewTable->countReviewCategories($user->getIdentity(), 'siteevent_event');

        $ratingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
        $ratingCount = array();
        for ($i = 5; $i > 0; $i--) {
            $ratingCount[$i] = $ratingTable->getNumbersOfUserRating(0, '', 0, $i, $user->getIdentity(), 'siteevent_event');
        }
        $values['ratingCount'] = $ratingCount;


        $response = $values;
        $this->respondWithSuccess($response, true);
    }

    //ACTION FOR POSTING A REVIEW
    public function createAction() {

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
            $this->respondWithError('no_record');

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET LISITING
        $siteevent = Engine_Api::_()->core()->getSubject();
        $event_id = $siteevent->event_id;

        //CHECK EDITOR REVIEW IS ALLOWED OR NOT
        $allow_editor_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2);
        if (empty($allow_editor_review) || $allow_editor_review == 2) {
            $this->respondWithError('unauthorized');
        }

        //SHOW THIS LINK ONLY EDITOR
        $isEditor = Engine_Api::_()->getDbTable('editors', 'siteevent')->isEditor($viewer_id);
        if (empty($isEditor)) {
            $this->respondWithError('unauthorized');
        }

        //EDITOR REVIEW HAS BEEN POSTED OR NOT
        $params = array();
        $params['resource_id'] = $siteevent->event_id;
        $params['resource_type'] = $siteevent->getType();
        $params['viewer_id'] = $viewer_id;
        $params['type'] = 'editor';
        $params['notIncludeStatusCheck'] = 1;
        $isEditorReviewed = Engine_Api::_()->getDbTable('reviews', 'siteevent')->canPostReview($params);
        if (!empty($isEditorReviewed)) {
            $this->respondWithError('unauthorized');
        }

        //FETCH REVIEW CATEGORIES
        $categoryIdsArray = array();
        $categoryIdsArray[] = $siteevent->category_id;
        $categoryIdsArray[] = $siteevent->subcategory_id;
        $categoryIdsArray[] = $siteevent->subsubcategory_id;
        $reviewCategory = Engine_Api::_()->getDbtable('ratingparams', 'siteevent')->reviewParams($categoryIdsArray, 'siteevent_event');
        $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIdsArray, 0, 'profile_type_review');

        //GET FORM
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getEditorCreateForm(array('profileTypeReview' => $profileTypeReview));
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {


            if (isset($_POST['submit'])) {
                $first_time_load = 0;
            } else {
                $first_time_load = 1;
            }

            // $local_language = $this->view->locale()->getLocale()->__toString();
            //$local_language = explode('_', $local_language);
            //$this->view->language = $local_language[0];
            // $this->view->total_reviewcats = Count($this->view->reviewCategory);

            $tab_selected_id = $this->_getParam('tab');

            //SHOW PRE-FIELD THE RATINGS IF OVERALL RATING IS EMPTY
            //    $this->view->reviewRateData = Engine_Api::_()->siteevent()->prefieldRatingData($_POST);
//
//            //GET CATEGORIES ARRAY
//            $this->view->bodyElementValue = $bodyElementValue = array();
//            foreach ($_POST as $key => $value) {
//                $bodyElement = strstr($key, 'body_');
//
//                if (!empty($bodyElement) && !empty($value)) {
//                    $this->view->bodyElementValue[] = $bodyElementValue[] = $value;
//                }
//            }
            $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getEditorCreateForm(array('profileTypeReview' => $profileTypeReview));
            foreach ($getForm as $element) {

                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'siteevent')->getEditorCreateValidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }
            if (empty($_POST['review_rate_0'])) {
                $this->respondWithValidationError('validation_fail', "overall rating required");
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                $values = $_POST;
                $values['owner_id'] = $viewer_id;
                $values['resource_id'] = $event_id;
                $values['resource_type'] = $siteevent->getType();
                $values['type'] = 'editor';
                $values['body_pages'] = Zend_Json_Encoder::encode($bodyElementValue);
                $values['recommend'] = 1;
                $values['profile_type_review'] = $profileTypeReview;

                //CREATE REVIEW
                $reviewTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');
                $review = $reviewTable->createRow();
                $review->setFromArray($values);
                $review->save();

                if (!empty($profileTypeReview)) {
                    //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
                    $customfieldform = $form->getSubForm('fields');
                    $customfieldform->setItem($review);
                    $customfieldform->saveValues();
                }

                $_POST['user_id'] = $viewer_id;
                $_POST['review_id'] = $review->review_id;
                $_POST['category_id'] = $siteevent->category_id;
                $_POST['resource_id'] = $review->resource_id;
                $_POST['resource_type'] = $review->resource_type;

                //CREATE RATING DATA
                $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
                $reviewRatingTable->createRatingData($_POST, 'editor');

                //IF PUBLISHED 
                if ($review->status == 1) {

                    //INCREASE REVIEW COUNT
                    $siteevent->review_count++;
                    $siteevent->save();

                    //RATING UPDATE
                    $exist_review = $reviewRatingTable->getReviewIdExist($viewer_id, $siteevent->getType(), $siteevent->getIdentity());
                    if (!empty($exist_review)) {
                        $reviewRatingTable->listRatingUpdate($review->resource_id, $review->resource_type);
                    } else {
                        $reviewRatingTable->listRatingUpdate($review->resource_id, $review->resource_type, 1);
                    }
                }

                //COMMENT PRIVACY
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                $commentMax = array_search("everyone", $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($review, $role, 'comment', ($i <= $commentMax));
                }

                if ($siteevent->owner_id != $viewer_id) {

                    $host = $_SERVER['HTTP_HOST'];
                    $viewer_page_url = (_ENGINE_SSL ? 'https://' : 'http://') . $host . $viewer->getHref();
                    $viewer_fullhref = '<a href="' . $viewer_page_url . '">' . $viewer->getTitle() . '</a>';
                    $object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $host . $review->getHref();
                    $object_parent_with_link = '<a href="' . (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/' . $siteevent->getHref() . '">' . $siteevent->getTitle() . '</a>';

                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($siteevent->getOwner()->email, 'SITEEVENT_EDITORREVIEW_CREATION', array(
                        'editor' => $viewer_fullhref,
                        'editor_name' => $viewer->getTitle(),
                        'object_parent_with_link' => $object_parent_with_link,
                        'object_title' => $review->title,
                        'object_description' => $review->body,
                        'object_parent_title' => $siteevent->getTitle(),
                        'object_link' => $object_link,
                        'queue' => true
                    ));
                }
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    //ACTION FOR EDITING A REVIEW
    public function editAction() {

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');


        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
            $this->respondWithError('no_record');


        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET LISITING
        $siteevent = Engine_Api::_()->core()->getSubject();
        $event_id = $siteevent->event_id;

        //SHOW THIS LINK ONLY EDITOR
        $isEditor = Engine_Api::_()->getDbTable('editors', 'siteevent')->isEditor($viewer_id);
        if (empty($isEditor)) {
            $this->respondWithError('unauthorized');
        }

        $review_id = $this->_getParam('review_id', null);
        $review = Engine_Api::_()->getItem('siteevent_review', $review_id);
        if(empty($review)){
             $this->respondWithError('no_record');
            
        }

        //FETCH REVIEW CATEGORIES
        $categoryIdsArray = array();
        $categoryIdsArray[] = $siteevent->category_id;
        $categoryIdsArray[] = $siteevent->subcategory_id;
        $categoryIdsArray[] = $siteevent->subsubcategory_id;
        $reviewCategory = Engine_Api::_()->getDbtable('ratingparams', 'siteevent')->reviewParams($categoryIdsArray, 'siteevent_event');
        $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIdsArray, 0, 'profile_type_review');

        //GET FORM
        if ($this->getRequest()->isGet()) {
            $formValues = $review->toArray();
            $formValues['reviewRateData'] = Engine_Api::_()->getDbtable('ratings', 'siteevent')->ratingsData($review_id);
            $this->respondWithSuccess(array(
                'form' => Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getEditorCreateForm(array('item' => $review, 'profileTypeReview' => $profileTypeReview)),
                'formValues' => $formValues
            ));
        } else if ($this->getRequest()->isPost()) {

//        
//        $local_language = $this->view->locale()->getLocale()->__toString();
//        $local_language = explode('_', $local_language);
//        $language = $local_language[0];

            $total_reviewcats = Count($reviewCategory);
            $tab_selected_id = $this->_getParam('tab');

//        //GET CATEGORIES ARRAY
//        $bodyElementValue = array();
//        if ($this->getRequest()->isPost()) {
//            foreach ($_POST as $key => $value) {
//                $bodyElement = strstr($key, 'body_');
//
//                if (!empty($bodyElement) && !empty($value)) {
//                    $bodyElementValue[] = $bodyElementValue[] = $value;
//                }
//            }
//        } else {
//            $encoded_value = $review->body_pages;
//            $decoded_value = Zend_Json_Decoder::decode($encoded_value);
//            foreach ($decoded_value as $key => $value) {
//
//                if (!empty($value)) {
//                    $bodyElementValue[] = $bodyElementValue[] = $value;
//                }
//            }
//        }
//            $reviewRateData = Engine_Api::_()->getDbtable('ratings', 'siteevent')->ratingsData($review_id);
//            return;
//        } else {
//            $this->view->reviewRateData = Engine_Api::_()->siteevent()->prefieldRatingData($_POST);
//        }

            $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getEditorCreateForm(array('item' => $review, 'profileTypeReview' => $profileTypeReview));
            $values=$review->toArray();

            foreach ($getForm as $element) {

                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }
            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'siteevent')->getEditorCreateValidators($review);
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }
           

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                $values = $_POST;
                $values['profile_type_review'] = $profileTypeReview;
                $reviewTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');
                $review->setFromArray($values);
                $review->body_pages = Zend_Json_Encoder::encode($bodyElementValue);
                $review->save();

                if (!empty($profileTypeReview)) {
                    //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
                    $customfieldform = $form->getSubForm('fields');
                    $customfieldform->setItem($review);
                    $customfieldform->saveValues();
                }

                $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
                $reviewRatingTable->delete(array('review_id = ?' => $review->review_id));

                $_POST['user_id'] = $viewer_id;
                $_POST['review_id'] = $review->review_id;
                $_POST['category_id'] = $siteevent->category_id;
                $_POST['resource_id'] = $review->resource_id;
                $_POST['resource_type'] = $review->resource_type;

                //CREATE RATING DATA
                $reviewRatingTable->createRatingData($_POST, 'editor');

                //IF PUBLISHED 
                if ($review->status == 1) {

                    //INCREASE REVIEW COUNT
                    $siteevent->review_count++;
                    $siteevent->save();

                    //RATING UPDATE
                    $exist_review = $reviewRatingTable->getReviewIdExist($viewer_id, $siteevent->getType(), $siteevent->getIdentity());
                    if (!empty($exist_review)) {
                        $reviewRatingTable->listRatingUpdate($review->resource_id, $review->resource_type);
                    } else {
                        $reviewRatingTable->listRatingUpdate($review->resource_id, $review->resource_type, 1);
                    }
                }

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    public function editorMailAction() {

        //ONLY LOGGED IN USER CAN VIEW THIS PAGE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $user_id = $this->_getParam('user_id', null);
        $editor = Engine_Api::_()->getItem('user', $user_id);

        $isEditor = Engine_Api::_()->getDbTable('editors', 'siteevent')->isEditor($user_id);
        if (empty($isEditor)) {
            $this->respondWithError('unauthorized');
        }
        //GET FORM
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getEditorMailForm(array('editor' => $editor));
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {


            $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getEditorMailForm(array('item' => $review, 'profileTypeReview' => $profileTypeReview));

            foreach ($getForm as $element) {

                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }
            $values['reciver_email'] = $editor->email;

            if (!empty($viewer_id)) {
                $values['sender_email'] = $viewer->email;
                $values['sender_name'] = $viewer->getTitle();
            }
            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'siteevent')->getEditorMailValidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $reciver_ids[] = $values['reciver_email'];
            //CHECK VALID EMAIL ID FORMAT
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);
            $sender_email = $values['sender_email'];
            if (!$validator->isValid($sender_email)) {
                $this->respondWithError('Invalid sender email address value');
                return;
            }

            $sender = $values['sender_name'];
            $message = $values['message'];
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITEEVENT_EDITOR_EMAIL', array(
                'host' => $_SERVER['HTTP_HOST'],
                'sender' => $sender,
                'message' => '<div>' . $message . '</div>',
                'email' => $sender_email,
                'queue' => false
            ));
            $this->successResponseNoContent('no_content', true);
        }
    }

}
