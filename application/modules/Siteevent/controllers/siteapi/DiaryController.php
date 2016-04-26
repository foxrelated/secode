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
class Siteevent_DiaryController extends Siteapi_Controller_Action_Standard {

    public function init() {

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            $this->respondWithError('unauthorized');

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_diary', null, "view")->isValid())
            $this->respondWithError('unauthorized');

        //SET SUBJECT
        if ($this->getRequestParam('diary_id') && (0 !== ($diary_id = (int) $this->getRequestParam('diary_id')) &&
                null !== ($diary = Engine_Api::_()->getItem('siteevent_diary', $diary_id)))) {
            Engine_Api::_()->core()->setSubject($diary);
        } else if (0 !== ($event_id = (int) $this->getRequestParam('event_id')) &&
                null !== ($siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id))) {
            Engine_Api::_()->core()->setSubject($siteevent);
        }
    }

    /**
     * RETURN THE LIST AND DETAILS OF ALL DIARIES WITH SEARCH PARAMETERS.
     * 
     * @return array
     */
    public function browseAction() {

        $this->validateRequestMethod();
        // Prepare the response
        $params = $response = array();
        $params = $this->_getAllParams();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        //GET PAGINATOR
        $params['pagination'] = 1;

        $paginator = Engine_Api::_()->getDbtable('diaries', 'siteevent')->getBrowseDiaries($params);
        $page = $this->_getParam('page', 1);
        $limit = $this->_getParam('limit', 20);
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $totalItemCount = $paginator->getTotalItemCount();
        $totalPages = ceil(($totalItemCount) / $limit);
        $response['totalItemCount'] = $totalItemCount;
        if (!empty($totalItemCount)) {
            foreach ($paginator as $dairyObj) {
                $diary = $dairyObj->toArray();
                $lists = $dairyObj->getDiaryMap(array('orderby' => 'random'));
                $count = $lists->getTotalItemCount();
                $tempEvents = array();
                $counter = 0;
                if (empty($count) || !isset($count) || $count == 0) {
                    $tempEvents['event_images_' . $counter] = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($dairyObj);
                } else {
                    foreach ($lists as $siteevent) {
                        if ($counter >= 3)
                            break;
                        else {
                            $counter++;
                            $tempEvents['event_images_' . $counter] = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($siteevent);
                        }
                    }
                }
                $diary = array_merge($diary, $tempEvents);
                $tempResponse[] = $diary;
            }
        }
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_diary', "create");
        $response['can_create'] = $can_create;
        if (!empty($tempResponse))
            $response['response'] = $tempResponse;
        $this->respondWithSuccess($response, true);
    }

    /**
     * Return the "Diary Browse Search" form. 
     * 
     * @return array
     */
    public function searchFormAction() {

        // Validate request methods
        $this->validateRequestMethod();
        $response = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        try {
            try {
                $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getDiarySearchForm();
            } catch (Exception $e) {
                echo $e;
                die;
            }
            $this->respondWithSuccess($response, true);
        } catch (Expection $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Return the Diary View page.
     * 
     * @return array
     */
    public function profileAction() {
        // Validate request methods
        $this->validateRequestMethod();
        //GET DIARY ID AND SUBJECT

        if (Engine_Api::_()->core()->hasSubject())
            $subject = $diary = Engine_Api::_()->core()->getSubject('siteevent_diary');

        if (empty($diary)) {
            $this->respondWithError('no_record');
        }

        $diary_id = $this->_getParam('diary_id');

        $viewer = Engine_Api::_()->user()->getViewer();
        //CHECK AUTHENTICATION
        if (!Engine_Api::_()->authorization()->isAllowed($diary, null, "view")) {
            $this->respondWithError('unauthorized');
        }

        //INCREASE VIEW COUNT IF VIEWER IS NOT OWNER
        if (!$diary->getOwner()->isSelf($viewer)) {
            $diary->view_count++;
            $diary->save();
        }

        // PREPARE RESPONSE ARRAY
        $bodyParams['response'] = $subject->toArray();

        if (isset($bodyParams['response']['body']) && !empty($bodyParams['response']['body']))
            $bodyParams['response']['body'] = strip_tags($bodyParams['response']['body']);

        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $showMessageOwner = 0;
        $showMessageOwner = Engine_Api::_()->authorization()->getPermission($level_id, 'messages', 'auth');
        if ($showMessageOwner != 'none') {
            $showMessageOwner = 1;
        }

        $messageOwner = 1;
        if ($diary->owner_id == $viewer_id || empty($viewer_id) || empty($showMessageOwner)) {
            $messageOwner = 0;
        }
        //GET LEVEL SETTING
        $can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_diary', "create");
        $bodyParams['response']['diary_creator_name'] = $diary->getOwner()->getTitle();

        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject, true);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
        $perms = array();
        //PRIVACY WORK
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $perms = array();
        foreach ($roles as $roleString) {
            $role = $roleString;
            if ($auth->isAllowed($su, $role, 'view')) {
                $perms['auth_view'] = $roleString;
            }
        }
        $bodyParams['response'] = array_merge($bodyParams['response'], $perms);

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        try {
            //FETCH RESULTS
            $paginator = Engine_Api::_()->getDbTable('diarymaps', 'siteevent')->diaryEvents($diary->diary_id);
            $paginator->setItemCountPerPage($itemCount);
            $paginator->setCurrentPageNumber($this->_getParam('currentpage', 1));
            $total_item = $paginator->getTotalItemCount();
            $bodyParams['response']['total_events'] = $total_item;

            foreach ($paginator as $eventObj) {

                $event['event_id'] = $eventObj->event_id;
                $event['title'] = $eventObj->title;
                $event['body'] = $eventObj->body;

                //CATEGORY NAME
                $event['category_name'] = Engine_Api::_()->getItem('siteevent_category', $eventObj->category_id)->category_name;

                $event['featured'] = $eventObj->featured;
                $event['sponsored'] = $eventObj->sponsored;
                $event['like_count'] = $eventObj->like_count;
                $event['comment_count'] = $eventObj->comment_count;
                $event['member_count'] = $eventObj->member_count;

                $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
                $dates = $occurrenceTable->getEventDate($eventObj->event_id);
                $event = array_merge($event, $dates);
                //GET EXACT LOCATION
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1) && !$eventObj->is_online) {
                    //GET LOCATION
                    $value['id'] = $event['event_id'];
                    $location = Engine_Api::_()->getDbtable('locations', 'siteevent')->getLocation($value);
                    if ($location)
                        $event['location'] = $location->toArray();
                }
                // Add Image
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($eventObj);
                $event = array_merge($event, $getContentImages);

                $event['hosted_by'] = '';
                if (isset($eventObj->host_type) && !empty($eventObj->host_id) && ($eventObj->host_type == 'siteevent_organizer')) {
                    $organizerObj = Engine_Api::_()->getItem('siteevent_organizer', $eventObj->host_id);
                    $organizer['host_type'] = 'siteevent_organizer';
                    $organizer['host_id'] = $organizerObj->organizer_id;
                    $organizer['host_title'] = $organizerObj->title;
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($organizerObj);
                    $organizer = array_merge($organizer, $getContentImages);
                    $event['hosted_by'] = $organizer;
                } else if (isset($eventObj->host_type) && !empty($eventObj->host_id) && $eventObj->host_type == 'user') {
                    $organizerObj = Engine_Api::_()->getItem('user', $eventObj->host_id);
                    $organizer['host_type'] = 'user';
                    $organizer['host_id'] = $organizerObj->user_id;
                    $organizer['host_title'] = $organizerObj->displayname;
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($organizerObj);
                    $organizer = array_merge($organizer, $getContentImages);
                    $event['hosted_by'] = $organizer;
                }

                $leaders = Engine_Api::_()->getItem('siteevent_event', $event['event_id'])->getLedBys(false);
                // Set default ledby.
                $defaultLedby['title'] = $eventObj->getOwner()->getTitle();
                $defaultLedby['type'] = $eventObj->getOwner()->getType();
                $defaultLedby['id'] = $eventObj->getOwner()->getIdentity();
                $event['ledby'][] = $defaultLedby;

                $event['owner_id'] = $eventObj->getOwner()->getIdentity();
                $event["owner_title"] = $eventObj->getOwner()->getTitle();
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($eventObj, true);
                $event = array_merge($event, $getContentImages);
                $event['get_attending_count'] = $eventObj->getAttendingCount();
                $event['get_maybe_count'] = $eventObj->getMaybeCount();
                $event['get_not_attending_count'] = $eventObj->getNotAttendingCount();
                $event['get_awaiting_reply_count'] = $eventObj->getAwaitingReplyCount();
                $tempResponse[] = $event;
            }
            if (!empty($tempResponse)) {
                $bodyParams['response']['event'] = $tempResponse;
            }

            if ($viewer_id) {
                $diaryMenus[] = array(
                    'name' => 'memberDiaries',
                    'label' => $this->translate($diary->getOwner()->getTitle() . " Event Diaries"),
                    'url' => 'advancedevents/diaries',
                    'urlParams' => array(
                        "member" => $diary->getOwner()->getTitle())
                );
                if ($can_create) {
                    $diaryMenus[] = array(
                        'name' => 'create',
                        'label' => $this->translate('Create New Event Diary'),
                        'url' => 'advancedevents/diaries/create',
                    );
                }
                if (!empty($messageOwner)) {
                    $diaryMenus[] = array(
                        'name' => 'messageOwner',
                        'label' => $this->translate('Message Owner'),
                        'url' => 'advancedevents/diaries/message-owner',
                        'urlParams' => array(
                            "diary_id" => $diary->getIdentity())
                    );
                }

                if ($diary->owner_id == $viewer_id || $level_id == 1) {
                    $diaryMenus[] = array(
                        'name' => 'edit',
                        'label' => $this->translate('Edit Diary'),
                        'url' => 'advancedevents/diary/edit/' . $diary->getIdentity(),
                    );
                    $diaryMenus[] = array(
                        'name' => 'delete',
                        'label' => $this->translate('Delete Diary'),
                        'url' => 'advancedevents/diary/delete/' . $diary->getIdentity(),
                    );
                }
            }

            $diaryMenus[] = array(
                'name' => 'tellafriend',
                'label' => $this->translate('Tell A Friend'),
                'url' => 'advancedevents/diaries/tell-a-friend',
                'urlParams' => array(
                    "diary_id" => $diary->getIdentity())
            );
            if (!empty($diaryMenus)) {
                $bodyParams['gutterMenus'] = $diaryMenus;
            }

            $this->respondWithSuccess($bodyParams);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Return the Message Owner Form and Send message.
     * 
     * @return array
     */
    public function messageOwnerAction() {

        //LOGGED IN USER CAN SEND THE MESSAGE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET EVENT ID AND OBJECT
        $diary_id = $this->_getParam("diary_id");
        $diary = Engine_Api::_()->getItem('siteevent_diary', $diary_id);

        if (empty($diary))
            $this->respondWithError('no_record');

        $owner_id = $diary->owner_id;

        //OWNER CANT SEND A MESSAGE TO HIMSELF
        if ($viewer_id == $diary->owner_id) {
            $this->respondWithError('unauthorized');
        }

        //MAKE FORM
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getMessageOwnerForm();
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();


            $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
            $db->beginTransaction();

            try {

                $is_error = 0;
                if (empty($values['title'])) {
                    $this->respondWithValidationError('validation_fail', 'Subject field is required');
                }

                $recipients = preg_split('/[,. ]+/', $owner_id);

                //LIMIT RECIPIENTS
                $recipients = array_slice($recipients, 0, 1000);

                //CLEAN THE RECIPIENTS FOR REPEATING IDS
                $recipients = array_unique($recipients);

                //GET USER
                $user = Engine_Api::_()->getItem('user', $diary->owner_id);

                $diary_title = $diary->getTitle();
                $diary_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('diary_id' => $diary_id, 'slug' => $diary->getSlug()), "siteevent_diary_view") . ">$diary_title</a>";

                $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . 'This message corresponds to the Diary: ' . $diary_title_with_link);

                try {
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $conversation, 'message_new');
                } catch (Exception $e) {
                    //Blank Exception
                }
                //INCREMENT MESSAGE COUNTER
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    /**
     * Remove event from diary.
     * 
     * @return status
     */
    public function removeAction() {

        // Validate request methods
        $this->validateRequestMethod('POST');

        //GET DIARY ID AND SUBJECT
        if (Engine_Api::_()->core()->hasSubject())
            $diary = Engine_Api::_()->core()->getSubject('siteevent_diary');

        if (empty($diary))
            $this->respondWithError('no_record');

        $diary_id = $this->_getParam('diary_id');

        $viewer = Engine_Api::_()->user()->getViewer();

        //CHECK AUTHENTICATION
        if (!Engine_Api::_()->authorization()->isAllowed($diary, null, "create")) {
            $this->respondWithError('unauthorized');
        }

        //GET EVENT ID AND EVENT
        $event_id = $this->_getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (empty($siteevent) && !isset($siteevent))
            $this->respondWithError('no_record');

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            //DELETE FROM DATABASE
            Engine_Api::_()->getDbtable('diarymaps', 'siteevent')->delete(array('diary_id = ?' => $diary_id, 'event_id = ?' => $event_id));

            try {
                //DELETE ACTIVITY FEED
                //SQL ERROR TO BE CORRECTED
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                $actionTableName = $actionTable->info('name');

                $action_id = $actionTable->select()
                        ->setIntegrityCheck(false)
                        ->from($actionTableName, 'action_id')
                        ->joinInner('engine4_activity_attachments', "engine4_activity_attachments.action_id = $actionTableName.action_id", array())
                        ->where('engine4_activity_attachments.id = ?', $event_id)
                        ->where($actionTableName . '.type = ?', "siteevent_diary_add_event")
                        ->where($actionTableName . '.subject_type = ?', 'user')
                        ->where($actionTableName . '.object_type = ?', 'siteevent_event')
                        ->where($actionTableName . '.object_id = ?', $event_id)
                        //->where($actionTableName . '.params like(?)', '{"child_id":' . $diary_id . '}')
                        ->query()
                        ->fetchColumn();
            } catch (Exception $ex) {
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
            if (!empty($action_id)) {
                $activity = Engine_Api::_()->getItem('activity_action', $action_id);
                if (!empty($activity)) {
                    $activity->delete();
                }
            }
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Return the Create Diary Form.
     * 
     * @return array
     */
    public function createAction() {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //CREATION PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_diary', null, "create")->isValid())
            $this->respondWithError('unauthorized');

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();


        //FORM GENERATION
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getCreateDiaryForm();
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {

            //GET DIARY TABLE
            $diaryTable = Engine_Api::_()->getItemTable('siteevent_diary');
            $db = $diaryTable->getAdapter();
            $db->beginTransaction();

            try {
                //GET FORM VALUES
                $values = $this->_getAllParams();
                if (empty($values['title'])) {
                    $this->respondWithValidationError('validation_fail', 'Please complete this field - it is required.');
                }
                $values['owner_id'] = $viewer->getIdentity();

                //CREATE DIARY
                $diary = $diaryTable->createRow();
                $diary->setFromArray($values);
                $diary->save();

                //PRIVACY WORK
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = 'owner';
                }
                $viewMax = array_search($values['auth_view'], $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($diary, $role, 'view', ($i <= $viewMax));
                }
                $db->commit();
                // Change request method POST to GET
                $this->setRequestMethod();
                $this->_forward('profile', 'diary', 'siteevent', array(
                    'diary_id' => $diary->getIdentity()
                ));
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }
    }

    /**
     * Add item to diary.
     * 
     * @return status
     */
    public function addAction() {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //CREATION PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_diary', null, "create")->isValid())
            $this->respondWithError('unauthorized');

        //GET PAGE ID AND CHECK PAGE ID VALIDATION
        $event_id = $this->_getParam('event_id');
        if (empty($event_id)) {
            $this->respondWithError('no_record');
        }

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET USER DIARIES
        $diaryTable = Engine_Api::_()->getDbtable('diaries', 'siteevent');
        $diaryDatas = $diaryTable->getUserDiaries($viewer_id);
        $diaryDataCount = Count($diaryDatas);


        $siteevent = Engine_Api::_()->getItem('siteevent_event', $this->_getParam('event_id'));
        if (empty($siteevent)) {
            $this->respondWithError('no_record');
        }
//
//        $this->view->can_add = 1;
//        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, null, "view")->isValid()) {
//            $this->view->can_add = 0;
//        }
//
//        //AUTHORIZATION CHECK
//        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.diary', 1) || !empty($siteevent->draft) || empty($siteevent->search) || empty($siteevent->approved)) {
//            $this->view->can_add = 0;
//        }
        //FORM GENERATION
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getAddToDiaryForm();
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();
            //CHECK FOR NEW ADDED DIARY TITLE OR 
            if (!empty($values['body']) && empty($values['title'])) {
                $this->respondWithError('parameter_missing');
            }
            //CHECK FOR TITLE IF NO DIARY
            if (empty($diaryDatas) && empty($values['title']))
                $this->respondWithError('Title feild required');

            //GET DIARY PAGE TABLE
            $diaryEventTable = Engine_Api::_()->getDbtable('diarymaps', 'siteevent');

            $diaryOldIds = array();

            //GET NOTIFY API
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');


            //WORK ON PREVIOUSLY CREATED DIARY
            if (!empty($diaryDatas)) {
                foreach ($diaryDatas as $diaryData) {
                    $key_name = 'diary_' . $diaryData->diary_id;
                    if (isset($values[$key_name]) && !empty($values[$key_name])) {
                        $diaryEventTable->insert(array(
                            'diary_id' => $diaryData->diary_id,
                            'event_id' => $event_id,
                        ));

                        //DIARY COVER PHOTO
                        $diaryTable->update(
                                array(
                            'event_id' => $event_id,
                                ), array(
                            'diary_id = ?' => $diaryData->diary_id,
                            'event_id = ?' => 0
                                )
                        );

                        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                        $action = $activityApi->addActivity($viewer, $siteevent, "siteevent_diary_add_event", null, array('child_id' => $diaryData->diary_id));

                        if ($action)
                            $activityApi->attachActivity($action, $siteevent);
                    }

                    $in_key_name = 'inDiary_' . $diaryData->diary_id;
                    if (isset($values[$in_key_name]) && empty($values[$in_key_name])) {
                        $diaryOldIds[$diaryData->diary_id] = $diaryData;
                        $diaryEventTable->delete(array('diary_id = ?' => $diaryData->diary_id, 'event_id = ?' => $event_id));

                        //DELETE ACTIVITY FEED
                        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                        $actionTableName = $actionTable->info('name');

                        $action_id = $actionTable->select()
                                ->setIntegrityCheck(false)
                                ->from($actionTableName, 'action_id')
                                ->joinInner('engine4_activity_attachments', "engine4_activity_attachments.action_id = $actionTableName.action_id", array())
                                ->where('engine4_activity_attachments.id = ?', $event_id)
                                ->where($actionTableName . '.type = ?', "siteevent_diary_add_event")
                                ->where($actionTableName . '.subject_type = ?', 'user')
                                ->where($actionTableName . '.object_type = ?', 'siteevent_diary')
                                ->where($actionTableName . '.object_id = ?', $diaryData->diary_id)
                                ->query()
                                ->fetchColumn();

                        if (!empty($action_id)) {
                            $activity = Engine_Api::_()->getItem('activity_action', $action_id);
                            if (!empty($activity)) {
                                $activity->delete();
                            }
                        }
                    }
                }
            }

            if (!empty($values['title'])) {

                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();

                try {
                    //CREATE DIARY
                    $diary = $diaryTable->createRow();
                    $diary->setFromArray($values);
                    $diary->owner_id = $viewer_id;
                    $diary->event_id = $event_id; //DIARY COVER PHOTO
                    $diary->save();

                    //PRIVACY WORK
                    $auth = Engine_Api::_()->authorization()->context;
                    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                    if (empty($values['auth_view'])) {
                        $values['auth_view'] = 'owner';
                    }

                    $viewMax = array_search($values['auth_view'], $roles);
                    foreach ($roles as $i => $role) {
                        $auth->setAllowed($diary, $role, 'view', ($i <= $viewMax));
                    }

                    $db->commit();
                    $diaryEventTable->insert(array(
                        'diary_id' => $diary->diary_id,
                        'event_id' => $event_id,
                        'date' => new Zend_Db_Expr('NOW()')
                    ));

                    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                    $action = $activityApi->addActivity($viewer, $siteevent, "siteevent_diary_add_event", null, array('child_id' => $diary->diary_id));

                    if ($action) {
                        $activityApi->attachActivity($action, $siteevent);
                    }
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            }
            $this->successResponseNoContent('no_content', true);
        }
    }

    /**
     * Return the Diary Edit Form.
     * 
     * @return array
     */
    public function editAction() {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $diary = Engine_Api::_()->core()->getSubject('siteevent_diary');

        if (empty($diary)) {
            $this->respondWithError('no record');
        }

        //GET PAGE ID AND CHECK PAGE ID VALIDATION
        $diary_id = $this->_getParam('diary_id');


        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $level_id = $viewer->level_id;

        //CREATION PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_diary', null, "create")->isValid())
            $this->respondWithError('unauthorized');

        if ($level_id != 1 && $diary->owner_id != $viewer_id) {
            $this->respondWithError('unauthorized');
        }
        //GET USER DIARIES
        $diaryTable = Engine_Api::_()->getDbtable('diaries', 'siteevent');
        $diaryDatas = $diaryTable->getUserDiaries($viewer_id);
        //PRIVACY WORK
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $perms = array();
        foreach ($roles as $roleString) {
            $role = $roleString;
            if ($auth->isAllowed($diary, $role, 'view')) {
                $perms['auth_view'] = $roleString;
            }
        }
        //FORM GENERATION
        if ($this->getRequest()->isGet()) {
            $formValues = $diary->toArray();
            $formValues = array_merge($formValues, $perms);

            if (isset($formValues['body']) && !empty($formValues['body']))
                $formValues['body'] = strip_tags($formValues['body']);


            $this->respondWithSuccess(array(
                'form' => Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->getCreateDiaryForm(),
                'formValues' => $formValues
            ));
        }

        //FORM VALIDATION
        else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {

            $db = Engine_Api::_()->getItemTable('siteevent_diary')->getAdapter();
            $db->beginTransaction();
            try {
                $values = array();
                $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->getCreateDiaryForm();
                foreach ($getForm as $element) {

                    if (isset($_REQUEST[$element['name']]))
                        $values[$element['name']] = $_REQUEST[$element['name']];
                }
                if (empty($values['title'])) {
                    $validationMessage = "title is required";
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }

                $diary->setFromArray($values)->save();
                $db->commit();

                //PRIVACTY WORK
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = 'owner';
                }

                $viewMax = array_search($values['auth_view'], $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($diary, $role, 'view', ($i <= $viewMax));
                }
                $db->commit();
                // Change request method POST to GET
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    /**
     * Return the Diary Tell A Friend Form.
     * 
     * @return array
     */
    public function tellAFriendAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $diary_id = $this->_getParam('diary_id', $this->_getParam('diary_id', null));
        $diary = Engine_Api::_()->getItem('siteevent_diary', $diary_id);
        if (empty($diary))
            $this->respondWithError('no_record');

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET FORM
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getTellAFriendForm();
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {

            $values = $this->_getAllParams();

            if (empty($values['sender_email']) && !isset($values['sender_email']))
                $this->respondWithValidationError('validation_fail', "Your Email field is required");

            if (empty($values['sender_name']) && !isset($values['sender_name']))
                $this->respondWithValidationError('validation_fail', "Your Name field is required");

            if (empty($values['message']) && !isset($values['message']))
                $this->respondWithValidationError('validation_fail', "Message field is required");

            if (empty($values['receiver_emails']) && !isset($values['receiver_emails']))
                $this->respondWithValidationError('validation_fail', "To field is required");

            //EXPLODE EMAIL IDS
            $reciver_ids = explode(',', $values['receiver_emails']);

            if (!empty($values['send_me'])) {
                $reciver_ids[] = $values['sender_email'];
            }

            $sender_email = $values['sender_email'];
            $heading = $diary->title;
            //CHECK VALID EMAIL ID FORMAT
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);

            if (!$validator->isValid($sender_email)) {
                $this->respondWithValidationError('Invalid sender email address value');
            }

            foreach ($receiver_ids as $receiver_id) {
                $receiver_id = trim($receiver_id, ' ');
                ($reciver_ids);
                if (!$validator->isValid($receiver_id)) {
                    $this->respondWithValidationError('Please enter correct email address of the receiver(s).');
                }
            }

            $sender = $values['sender_name'];
            $message = $values['message'];
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITEEVENT_TELLAFRIEND_EMAIL', array(
                'host' => $_SERVER['HTTP_HOST'],
                'sender' => $sender,
                'heading' => $heading,
                'message' => '<div>' . $message . '</div>',
                'object_link' => $diary->getHref(),
                'email' => $sender_email,
                'queue' => true
            ));
            $this->successResponseNoContent('no_content', true);
        }
    }

    /**
     * Delete Diary.
     * 
     * @return status
     */
    public function deleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');


        //GET DIARY ID
        $diary_id = $this->_getParam('diary_id');

        $diary = Engine_Api::_()->getItem('siteevent_diary', $diary_id);

        if (empty($diary) && !isset($diary))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $level_id = $viewer->level_id;

        if ($level_id != 1 && $diary->owner_id != $viewer_id) {
            $this->respondWithError('unauthorized');
        }
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            //DELETE DIARY CONTENT
            $diary->delete();

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

}
