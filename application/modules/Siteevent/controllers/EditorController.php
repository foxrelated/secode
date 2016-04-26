<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EditorController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_EditorController extends Seaocore_Controller_Action_Standard {

    public function init() {

        //CHECK SUBJECT
        if (Engine_Api::_()->core()->hasSubject())
            return;

        //SET EVENT SUBJECT
        if (0 != ($event_id = (int) $this->_getParam('event_id')) &&
                null != ($siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id))) {
            Engine_Api::_()->core()->setSubject($siteevent);
        }
    }

    public function homeAction() {

        //GET PAGE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "siteevent_editor_home");
        $pageObject = $pageTable->fetchRow($pageSelect);

        $this->_helper->content
                ->setContentName($pageObject->page_id)
                ->setNoRender()
                ->setEnabled();
    }

    //NONE USER SPECIFIC METHODS
    public function profileAction() {

        //GET USER ID
        $user_id = $this->_getParam('user_id');

        //IF EDITOR
        $isEditor = Engine_Api::_()->getDbTable('editors', 'siteevent')->isEditor($user_id, 0);

        //IF USER IS NOT FOUND
        if (empty($user_id) || empty($isEditor)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //SET USER SUBJECT
        $user = Engine_Api::_()->getItem('user', $user_id);
        Engine_Api::_()->core()->setSubject($user);
        $params = array('displayname' => $user->getTitle());

        //GET EDITOR TABLE
        $editorTable = Engine_Api::_()->getDbTable('editors', 'siteevent');
        $editor = Engine_Api::_()->getItem('siteevent_editor', $user_id);

        //GET EDITOR DETAILS
        $values = array();
        $values['visible'] = 1;
        $values['editorReviewAllow'] = 1;
        $getDetails = $editorTable->getEditorDetails($user_id, 0, $values);
        $params['eventTypes'] = 'Events';

        //SET META KEYWORDS
        Engine_Api::_()->siteevent()->setMetaKeywords($params);

        // Render
        $this->_helper->content
                //->setNoRender()
                ->setEnabled()
        ;
    }

    //ACTION FOR POSTING A REVIEW
    public function createAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
            return;

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET LISITING
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject();
        $this->view->event_id = $event_id = $siteevent->event_id;

        //CHECK EDITOR REVIEW IS ALLOWED OR NOT
        $allow_editor_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2);
        if (empty($allow_editor_review) || $allow_editor_review == 2) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //SHOW THIS LINK ONLY EDITOR
        $isEditor = Engine_Api::_()->getDbTable('editors', 'siteevent')->isEditor($viewer_id);
        if (empty($isEditor)) {
            return $this->_forward('requireauth', 'error', 'core');
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
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("siteevent_main");

        //FATCH REVIEW CATEGORIES
        $categoryIdsArray = array();
        $categoryIdsArray[] = $siteevent->category_id;
        $categoryIdsArray[] = $siteevent->subcategory_id;
        $categoryIdsArray[] = $siteevent->subsubcategory_id;
        $this->view->reviewCategory = Engine_Api::_()->getDbtable('ratingparams', 'siteevent')->reviewParams($categoryIdsArray, 'siteevent_event');
        $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIdsArray, 0, 'profile_type_review');

        //GET FORM
        $this->view->form = $form = new Siteevent_Form_Editor_Create(array('profileTypeReview' => $profileTypeReview));

        if (isset($_POST['submit'])) {
            $this->view->first_time_load = 0;
        } else {
            $this->view->first_time_load = 1;
        }

        //GET TINYMCE SETTINGS
        $this->view->upload_url = "";
        if (Engine_Api::_()->authorization()->isAllowed($siteevent, $viewer, "photo")) {
            $this->view->upload_url = $this->view->url(array('controller' => 'editor', 'action' => 'upload-photo', 'event_id' => $siteevent->event_id), "siteevent_extended", true);
        }

        $orientation = $this->view->layout()->orientation;
        if ($orientation == 'right-to-left') {
            $this->view->directionality = 'rtl';
        } else {
            $this->view->directionality = 'ltr';
        }

        $local_language = $this->view->locale()->getLocale()->__toString();
        $local_language = explode('_', $local_language);
        $this->view->language = $local_language[0];

        $this->view->total_reviewcats = Count($this->view->reviewCategory);

        $this->view->tab_selected_id = $this->_getParam('tab');

        //SHOW PRE-FIELD THE RATINGS IF OVERALL RATING IS EMPTY
        $this->view->reviewRateData = Engine_Api::_()->siteevent()->prefieldRatingData($_POST);

        //GET CATEGORIES ARRAY
        $this->view->bodyElementValue = $bodyElementValue = array();
        foreach ($_POST as $key => $value) {
            $bodyElement = strstr($key, 'body_');

            if (!empty($bodyElement) && !empty($value)) {
                $this->view->bodyElementValue[] = $bodyElementValue[] = $value;
            }
        }

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            if (empty($_POST['review_rate_0'])) {

                $error = $this->view->translate('Please choose Overall Rating field - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
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
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_redirectCustom($siteevent->getHref(), array('prependBase' => false));
        }
    }

    //ACTION FOR POSTING A REVIEW
    public function editAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
            return;

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET LISITING
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject();
        $this->view->event_id = $event_id = $siteevent->event_id;

        //SHOW THIS LINK ONLY EDITOR
        $isEditor = Engine_Api::_()->getDbTable('editors', 'siteevent')->isEditor($viewer_id);
        if (empty($isEditor)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $review_id = $this->_getParam('review_id', null);
        $review = Engine_Api::_()->getItem('siteevent_review', $review_id);

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("siteevent_main");

        //FATCH REVIEW CATEGORIES
        $categoryIdsArray = array();
        $categoryIdsArray[] = $siteevent->category_id;
        $categoryIdsArray[] = $siteevent->subcategory_id;
        $categoryIdsArray[] = $siteevent->subsubcategory_id;
        $this->view->reviewCategory = Engine_Api::_()->getDbtable('ratingparams', 'siteevent')->reviewParams($categoryIdsArray, 'siteevent_event');
        $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIdsArray, 0, 'profile_type_review');

        //GET FORM
        $this->view->form = $form = new Siteevent_Form_Editor_Edit(array('item' => $review, 'profileTypeReview' => $profileTypeReview));

        //REMOVE DRAFT ELEMENT IF ALREADY PUBLISHED
        if ($review->status == "1") {
            $form->removeElement('status');
        }

        //GET TINYMCE SETTINGS
        $this->view->upload_url = "";
        if (Engine_Api::_()->authorization()->isAllowed($siteevent, $viewer, "photo")) {
            $this->view->upload_url = $this->view->url(array('controller' => 'editor', 'action' => 'upload-photo', 'event_id' => $siteevent->event_id), "siteevent_extended", true);
        }

        $orientation = $this->view->layout()->orientation;
        if ($orientation == 'right-to-left') {
            $this->view->directionality = 'rtl';
        } else {
            $this->view->directionality = 'ltr';
        }

        $local_language = $this->view->locale()->getLocale()->__toString();
        $local_language = explode('_', $local_language);
        $this->view->language = $local_language[0];

        $this->view->total_reviewcats = Count($this->view->reviewCategory);
        $this->view->tab_selected_id = $this->_getParam('tab');

        //GET CATEGORIES ARRAY
        $this->view->bodyElementValue = $bodyElementValue = array();
        if ($this->getRequest()->isPost()) {
            foreach ($_POST as $key => $value) {
                $bodyElement = strstr($key, 'body_');

                if (!empty($bodyElement) && !empty($value)) {
                    $this->view->bodyElementValue[] = $bodyElementValue[] = $value;
                }
            }
        } else {
            $encoded_value = $review->body_pages;
            $decoded_value = Zend_Json_Decoder::decode($encoded_value);
            foreach ($decoded_value as $key => $value) {

                if (!empty($value)) {
                    $this->view->bodyElementValue[] = $bodyElementValue[] = $value;
                }
            }
        }

        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            $this->view->reviewRateData = Engine_Api::_()->getDbtable('ratings', 'siteevent')->ratingsData($review_id);
            $form->populate($review->toArray());
            return;
        } else {
            $this->view->reviewRateData = Engine_Api::_()->siteevent()->prefieldRatingData($_POST);
        }

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //SHOW PRE-FIELD THE RATINGS IF OVERALL RATING IS EMPTY
            $this->view->reviewRateData = Engine_Api::_()->siteevent()->prefieldRatingData($_POST);

            if (empty($_POST['review_rate_0'])) {

                $error = $this->view->translate('Please choose Overall Rating field - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
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
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_redirectCustom($siteevent->getHref(), array('prependBase' => false));
        }
    }

    //ACTION FOR UPLOADING THE OVERVIEWS PHOTOS FROM THE EDITOR
    public function uploadPhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //LAYOUT
        $this->_helper->layout->disableLayout();
        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        //PAGE ID
        $event_id = $this->_getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //IF NOT POST OR FORM NOT VALID, RETURN
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        $fileName = Engine_Api::_()->seaocore()->tinymceEditorPhotoUploadedFileName();
        //IF NOT POST OR FORM NOT VALID, RETURN
        if (!isset($_FILES[$fileName]) || !is_uploaded_file($_FILES[$fileName]['tmp_name'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
            return;
        }

        //PROCESS
        $db = Engine_Api::_()->getDbtable('photos', 'siteevent')->getAdapter();
        $db->beginTransaction();
        try {
            //CREATE PHOTO
            $tablePhoto = Engine_Api::_()->getDbtable('photos', 'siteevent');
            $photo = $tablePhoto->createRow();
            $album = $siteevent->getSingletonAlbum();
            $photo->setFromArray(array(
                'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
                'event_id' => $event_id,
                'album_id' => $album->getIdentity(),
                'collection_id' => $album->getIdentity()
            ));
            $photo->save();
            $photo->setPhoto($_FILES[$fileName]);

            $this->view->status = true;
            $this->view->name = $_FILES[$fileName]['name'];
            $this->view->photo_id = $photo->photo_id;
            $this->view->photo_url = $photo->getPhotoUrl();

            if (!$album->photo_id) {
                $album->photo_id = $photo->file_id;
                $album->save();
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
            return;
        }
    }

    public function editorMailAction() {

        //ONLY LOGGED IN USER CAN VIEW THIS PAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewr_id = $viewer->getIdentity();
        $user_id = $this->_getParam('user_id', null);
        $editor = Engine_Api::_()->getItem('user', $user_id);
        //GET FORM
        $this->view->form = $form = new Siteevent_Form_Editor_EditorMail(array('editor' => $editor));

        $form->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_('Email %s'), $editor->getTitle()))
                ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Have a question or comments for %s? Ask from below and our editor will get back to you."), $editor->getTitle()));
        $value['reciver_email'] = $editor->email;
        if (!empty($viewr_id)) {
            $value['sender_email'] = $viewer->email;
            $value['sender_name'] = $viewer->getTitle();
            $form->populate($value);
        }

        //FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //GET FORM VALUES
            $values = $form->getValues();
            $reciver_ids[] = $values['reciver_email'];
            //CHECK VALID EMAIL ID FORMAT
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);
            $sender_email = $values['sender_email'];
            if (!$validator->isValid($sender_email)) {
                $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
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

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefreshTime' => '15',
                'format' => 'smoothbox',
                'messages' => Zend_Registry::get('Zend_Translate')->_('Email to the editor has been sent successfully.')
            ));
        }
    }

    public function categoriesAction() {

        $element_value = $this->_getParam('element_value', 1);
        $element_type = $this->_getParam('element_type');

        $this->view->categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->similarItemsCategories($element_value, $element_type);
    }

}
