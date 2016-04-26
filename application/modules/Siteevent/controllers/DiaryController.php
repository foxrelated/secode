<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DiaryController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_DiaryController extends Seaocore_Controller_Action_Standard {

    //COMMON FUNCTION WHICH CALL AUTOMATICALLY BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            return;

        $event_id = $this->_getParam('event_id');
        if (!empty($event_id)) {

            //AUTHORIZATION CHECK
            if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
                return;
        }

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_diary', null, "view")->isValid())
            return;
    }

    //NONE USER SPECIFIC METHODS
    public function browseAction() {

        //GET SEARCH TEXT
        if ($this->_getParam('search', null)) {
            $metaParams['search'] = $this->_getParam('search', null);

            //SET META KEYWORDS
            Engine_Api::_()->siteevent()->setMetaKeywords($metaParams);
        }

        $this->_helper->content
                ->setNoRender()
                ->setEnabled();
        
    }

    //NONE USER SPECIFIC METHODS
    public function profileAction() {
        //GET DIARY ID AND OBJECT
        $diary_id = $this->_getParam('diary_id');
        $diary = Engine_Api::_()->getItem('siteevent_diary', $diary_id);

        //SET SITEEVENT SUBJECT
        Engine_Api::_()->core()->setSubject($diary);

        //GET PAGE OBJECT
//    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
//    $pageSelect = $pageTable->select()->where('name = ?', "siteevent_diary_profile");
//    $pageObject = $pageTable->fetchRow($pageSelect);

        $params['diary'] = 'Diaries';
        Engine_Api::_()->siteevent()->setMetaTitles($params);

        $params['diary_creator_name'] = $diary->getOwner()->getTitle();
        Engine_Api::_()->siteevent()->setMetaKeywords($params);

        //CHECK AUTHENTICATION
        if (!Engine_Api::_()->authorization()->isAllowed($diary, null, "view")) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //INCREASE VIEW COUNT IF VIEWER IS NOT OWNER
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$diary->getOwner()->isSelf($viewer)) {
            $diary->view_count++;
            $diary->save();
        }

        $this->_helper->content
//            ->setContentName($pageObject->page_id)
                ->setNoRender()
                ->setEnabled();
          if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      Zend_Registry::set('setFixedCreationFormBack', 'Back');
     }
    }

    //ACTION FOR ADDING THE ITEM IN DIARY
    public function addAction() {

        $param = $this->_getParam('param');
        $request_url = $this->_getParam('request_url');
        $return_url = $this->_getParam('return_url');
        $front = Zend_Controller_Front::getInstance();
        $base_url = $front->getBaseUrl();

        // CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid()) {
            if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                return;
            }
            $host = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://";
            if ($base_url == '') {
                $URL_Home = $host . $_SERVER['HTTP_HOST'] . '/login';
            } else {
                if ($request_url)
                    $URL_Home = $host . $_SERVER['HTTP_HOST'] . '/' . $request_url . '/login';
                else
                    $URL_Home = $host . $_SERVER['HTTP_HOST'] . $base_url . '/login';
            }
            if (empty($param)) {
                return $this->_helper->redirector->gotoUrl($URL_Home, array('prependBase' => false));
            } else {
                return $this->_helper->redirector->gotoUrl($URL_Home . '?return_url=' . urlencode($return_url), array('prependBase' => false));
            }
        }

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //CREATION PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_diary', null, "create")->isValid())
            return;

        //GET PAGE ID AND CHECK PAGE ID VALIDATION
        $event_id = $this->_getParam('event_id');
        if (empty($event_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET USER DIARIES
        $diaryTable = Engine_Api::_()->getDbtable('diaries', 'siteevent');
        $diaryDatas = $diaryTable->getUserDiaries($viewer_id);
        $this->view->diaryDatasCount = $diaryDataCount = Count($diaryDatas);

        //LISING WILL ADD IF YOU CAN VIEW THIS
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $this->_getParam('event_id'));

        $this->view->can_add = 1;
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, null, "view")->isValid()) {
            $this->view->can_add = 0;
        }

        //AUTHORIZATION CHECK
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.diary', 1) || !empty($siteevent->draft) || empty($siteevent->search) || empty($siteevent->approved)) {
            $this->view->can_add = 0;
        }

        //FORM GENERATION
        $this->view->form = $form = new Siteevent_Form_Diary_Add();

        $this->view->success = 0;

        //FORM VALIDATION
        if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //GET FORM VALUES
        $values = $form->getValues();

        //CHECK FOR NEW ADDED DIARY TITLE
        if (!empty($values['body']) && empty($values['title'])) {

            $error = $this->view->translate('Please enter the diary name to create a new diary otherwise remove the diary description.');
            $this->view->status = false;
            $error = Zend_Registry::get('Zend_Translate')->_($error);
            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }

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
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }

            $diaryEventTable->insert(array(
                'diary_id' => $diary->diary_id,
                'event_id' => $event_id,
                'date' => new Zend_Db_Expr('NOW()')
            ));

            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($viewer, $siteevent, "siteevent_diary_add_event", null, array('child_id' => $diary->diary_id));

            if ($action)
                $activityApi->attachActivity($action, $siteevent);
        }

        $this->view->diaryOldDatas = $diaryOldIds;
        $this->view->diaryNewDatas = $diaryEventTable->pageDiaries($event_id, $viewer_id);
        $this->view->success = 1;
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                   $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Event has been added to diary successfully.'))
      ));
        }
        
     
    }

    //ACTION FOR MESSAGING THE EVENT OWNER
    public function messageOwnerAction() {

        //LOGGED IN USER CAN SEND THE MESSAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET EVENT ID AND OBJECT
        $diary_id = $this->_getParam("diary_id");
        $diary = Engine_Api::_()->getItem('siteevent_diary', $diary_id);

        //OWNER CANT SEND A MESSAGE TO HIMSELF
        if ($viewer_id == $diary->owner_id) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //MAKE FORM
        $this->view->form = $form = new Messages_Form_Compose();
        $form->setDescription('Create your message with the form given below. (This message will be sent to the owner of this Diary.)');
        $form->removeElement('to');
        $form->toValues->setValue("$diary->owner_id");
       
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
        $form->removeElement('toValues');
        }
        
        //CHECK METHOD/DATA
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();

        try {
            $values = $this->getRequest()->getPost();

            $form->populate($values);

            $is_error = 0;
            if (empty($values['title'])) {
                $is_error = 1;
            }

            //SENDING MESSAGE
            if ($is_error == 1) {
                $error = $this->view->translate('Subject is required field !');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            $recipients = preg_split('/[,. ]+/', $values['toValues']);

            //LIMIT RECIPIENTS
            $recipients = array_slice($recipients, 0, 1000);

            //CLEAN THE RECIPIENTS FOR REPEATING IDS
            $recipients = array_unique($recipients);

            //GET USER
            $user = Engine_Api::_()->getItem('user', $diary->owner_id);

            $diary_title = $diary->getTitle();
            $diary_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('diary_id' => $diary_id, 'slug' => $diary->getSlug()), "siteevent_diary_view") . ">$diary_title</a>";

            $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . $this->view->translate('This message corresponds to the Diary: ') . $diary_title_with_link);

            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $conversation, 'message_new');

            //INCREMENT MESSAGE COUNTER
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

            $db->commit();

            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR REMOVE ITEM FROM THIS DIARY
    public function removeAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //GET DIARY AND PAGE ID 
        $this->view->diary_id = $diary_id = $this->_getParam('diary_id');
        $event_id = $this->_getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                //DELETE FROM DATABASE
                Engine_Api::_()->getDbtable('diarymaps', 'siteevent')->delete(array('diary_id = ?' => $diary_id, 'event_id = ?' => $event_id));


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
                        ->where($actionTableName . '.object_type = ?', 'siteevent_event')
                        ->where($actionTableName . '.object_id = ?', $event_id)
                        ->where($actionTableName . '.params like(?)', '{"child_id":' . $diary_id . '}')
                        ->query()
                        ->fetchColumn();

                if (!empty($action_id)) {
                    $activity = Engine_Api::_()->getItem('activity_action', $action_id);
                    if (!empty($activity)) {
                        $activity->delete();
                    }
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('This entry has been removed successfully from this diary!'))
            ));
        }
        $this->renderScript('diary/remove.tpl');
    }

    //ACTION FOR TELL TO THE FRIEND FOR THIS DIARY
    public function tellAFriendAction() {

        //DEFAULT LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //GET VIEWER DETAIL
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewr_id = $viewer->getIdentity();

        //GET DIARY ID AND DIARY OBJECT
        $diary_id = $this->_getParam('diary_id', $this->_getParam('diary_id', null));
        $diary = Engine_Api::_()->getItem('siteevent_diary', $diary_id);

        //FORM GENERATION
        $this->view->form = $form = new Siteevent_Form_Diary_TellAFriend();

        if (!empty($viewr_id)) {
            $value['diary_sender_email'] = $viewer->email;
            $value['diary_sender_name'] = $viewer->displayname;
            $form->populate($value);
        }

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET FORM VALUES
            $values = $form->getValues();

            //EMAIL IDS
            $reciver_ids = explode(',', $values['diary_reciver_emails']);

            if (!empty($values['diary_send_me'])) {
                $reciver_ids[] = $values['diary_sender_email'];
            }

            $reciver_ids = array_unique($reciver_ids);

            $sender_email = $values['diary_sender_email'];

            //CHECK VALID EMAIL ID FORMITE
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);

            if (!$validator->isValid($sender_email)) {
                $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
                return;
            }
            foreach ($reciver_ids as $reciver_id) {
                $reciver_id = trim($reciver_id, ' ');
                if (!$validator->isValid($reciver_id)) {
                    $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
                    return;
                }
            }

            //GET EMAIL DETAILS
            $sender = $values['diary_sender_name'];
            $message = $values['diary_message'];
            $params['diary_id'] = $diary_id;
            $params['slug'] = $diary->getSlug();
            $heading = ucfirst($diary->getTitle());

            Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITEEVENT_DIARY_TELLAFRIEND_EMAIL', array(
                'host' => $_SERVER['HTTP_HOST'],
                'sender_name' => $sender,
                'diary_title' => $heading,
                'message' => '<div>' . $message . '</div>',
                'object_link' => $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble($params, 'siteevent_diary_view', true),
                'sender_email' => $sender_email,
                'queue' => true
            ));

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => false,
                'format' => 'smoothbox',
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.'))
            ));
        }
    }

    //ACTION FOR CREATING THE DIARY
    public function createAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //CREATION PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_diary', null, "create")->isValid())
            return;

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //FORM GENERATION
        $this->view->form = $form = new Siteevent_Form_Diary_Create();

        if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //GET DIARY TABLE
        $diaryTable = Engine_Api::_()->getItemTable('siteevent_diary');
        $db = $diaryTable->getAdapter();
        $db->beginTransaction();

        try {

            //GET FORM VALUES
            $values = $form->getValues();
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
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }

        //GET URL
        $url = $this->_helper->url->url(array('diary_id' => $diary->diary_id, 'slug' => $diary->getSlug()), "siteevent_diary_view", true);

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'smoothboxClose' => 10,
            'parentRedirect' => $url,
            'parentRedirectTime' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your diary has been created successfully.'))
        ));
    }

    //ACTION FOR EDIT DIARY
    public function editAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET DIARY ID AND CHECK VALIDATION
        $diary_id = $this->_getParam('diary_id');
        if (empty($diary_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //GET DIARY OBJECT
        $diary = Engine_Api::_()->getItem('siteevent_diary', $diary_id);

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $level_id = $viewer->level_id;

        if ($level_id != 1 && $diary->owner_id != $viewer_id) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //FORM GENERATION
        $this->view->form = $form = new Siteevent_Form_Diary_Edit();

        if (!$this->getRequest()->isPost()) {

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

            $form->populate($diary->toArray());
            $form->populate($perms);
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $db = Engine_Api::_()->getItemTable('siteevent_diary')->getAdapter();
        $db->beginTransaction();

        try {

            //GET FORM VALUES
            $values = $form->getValues();

            //SAVE DATA
            $diary->setFromArray($values);
            $diary->save();

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
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //GET URL
        $url = $this->_helper->url->url(array('diary_id' => $diary->diary_id, 'slug' => $diary->getSlug()), "siteevent_diary_view", true);

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'smoothboxClose' => 10,
            'parentRedirect' => $url,
            'parentRedirectTime' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your diary has been edited successfully.'))
        ));
    }

    //ACTION FOR PRINT DIARY
    public function printAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //GET DIARY ID AND OBJECT
        $diary_id = $this->_getParam('diary_id');
        $this->view->diary = $diary = Engine_Api::_()->getItem('siteevent_diary', $diary_id);

        $content_id = $this->_getParam('content_id', 0);
        $params = Engine_Api::_()->siteevent()->getWidgetInfo('siteevent.diary-profile-items', $content_id)->params;
        $this->view->statisticsDiary = array("entryCount", "viewCount");
        if (isset($params['statisticsDiary'])) {
            $this->view->statisticsDiary = $params['statisticsDiary'];
        }

        //FETCH RESULTS
        $this->view->paginator = Engine_Api::_()->getDbTable('diarymaps', 'siteevent')->diaryEvents($diary->diary_id);
        $this->view->paginator->setItemCountPerPage(500);
        $this->view->total_item = $this->view->paginator->getTotalItemCount();
    }

    public function coverPhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SMOOTHBOX
        if (null == $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            //NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        //GET EVENT ID
        $event_id = $this->view->event_id = $this->_getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (empty($siteevent)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET EVENT ID
        $diary_id = $this->view->diary_id = $this->_getParam('diary_id');
        $diary = Engine_Api::_()->getItem('siteevent_diary', $diary_id);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        if ($viewer->level_id != 1 && $diary->owner_id != $viewer_id) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                //DELETE DIARY CONTENT
                $diary->event_id = $event_id;
                $diary->save();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRedirect' => $this->_helper->url->url(array('action' => 'browse'), "siteevent_diary_general", true),
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'))
            ));
        } else {
            $this->renderScript('diary/cover-photo.tpl');
        }
    }

    //ACTION FOR DELETE DIARY
    public function deleteAction() {

        //DEFAULT LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET DIARY ID
        $this->view->diary_id = $diary_id = $this->_getParam('diary_id');

        $diary = Engine_Api::_()->getItem('siteevent_diary', $diary_id);

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $level_id = $viewer->level_id;

        if ($level_id != 1 && $diary->owner_id != $viewer_id) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                //DELETE DIARY CONTENT
                $diary->delete();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRedirect' => $this->_helper->url->url(array('action' => 'browse'), "siteevent_diary_general", true),
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your diary has been deleted successfully.'))
            ));
        } else {
            $this->renderScript('diary/delete.tpl');
        }
    }

}
