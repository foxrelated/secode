<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TopicController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_TopicController extends Seaocore_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            return;

        //RETURN IF SUBJECT IS ALREADY SET
        if (Engine_Api::_()->core()->hasSubject())
            return;

        //SET TOPIC OR EVENT SUBJECT
        if (0 != ($topic_id = (int) $this->_getParam('topic_id')) &&
                null != ($topic = Engine_Api::_()->getItem('siteevent_topic', $topic_id))) {
            Engine_Api::_()->core()->setSubject($topic);
        } else if (0 != ($event_id = (int) $this->_getParam('event_id')) &&
                null != ($siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id))) {
            Engine_Api::_()->core()->setSubject($siteevent);
        }
    }

    //ACTION TO BROWSE ALL TOPICS
    public function indexAction() {

        //RETURN IF EVENT SUBJECT IS NOT SET
        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
            return;

        //GET EVENT SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject();

        //SEND THE TAB ID TO THE TPL
        $this->view->tab_selected_id = $this->_getParam('tab');

        //GET PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('topics', 'siteevent')->getEventTopices($siteevent->getIdentity());
        $this->view->paginator->setCurrentPageNumber($this->_getParam('page'));

        //CAN POST DISCUSSION IF DISCUSSION PRIVACY IS ALLOWED
        $this->view->can_post = $this->_helper->requireAuth->setAuthParams('siteevent_event', null, "topic")->checkRequire();
    }

    //ACTION TO VIEW TOPIC
    public function viewAction() {

        //RETURN IF TOPIC SUBJECT IS NOT SET
        if (!$this->_helper->requireSubject('siteevent_topic')->isValid())
            return;

        //GET VIEWER
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        //SEND TAB ID TO THE TPL
        $this->view->tab_selected_id = $this->_getParam('content_id');
        //GET TOPIC  SUBJECT
        $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

        $this->view->canEdit = $topic->canEdit();

        //GET SITEEVENT OBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $topic->event_id);

        //WHO CAN POST TOPIC
        $this->view->canPost = $canPost = $siteevent->authorization()->isAllowed($viewer, "topic");

        //INCREASE THE VIEW COUNT
        if (!$viewer || !$viewer_id || $viewer_id != $topic->user_id) {
            $topic->view_count = new Zend_Db_Expr('view_count + 1');
            $topic->save();
        }

        //CHECK WATHCHING
        $isWatching = null;
        if ($viewer->getIdentity()) {
            $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'siteevent');
            $isWatching = $topicWatchesTable->isWatching($siteevent->getIdentity(), $topic->getIdentity(), $viewer_id);
            if (false == $isWatching) {
                $isWatching = null;
            } else {
                $isWatching = (bool) $isWatching;
            }
        }
        $this->view->isWatching = $isWatching;

        //GET POST ID
        $this->view->post_id = $post_id = (int) $this->_getParam('post');

        $table = Engine_Api::_()->getDbtable('posts', 'siteevent');
        $select = $table->select()
                ->where('event_id = ?', $siteevent->getIdentity())
                ->where('topic_id = ?', $topic->getIdentity())
                ->order('creation_date DESC');
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        //SKIP TO PAGE OF SPECIFIED POST
        if (0 != ($post_id = (int) $this->_getParam('post_id')) &&
                null != ($post = Engine_Api::_()->getItem('siteevent_post', $post_id))) {
            $icpp = $paginator->getItemCountPerPage();
            $page = ceil(($post->getPostIndex() + 1) / $icpp);
            $paginator->setCurrentPageNumber($page);
        }
        //USE SPECIFIED PAGE
        else if (0 != ($page = (int) $this->_getParam('page'))) {
            $paginator->setCurrentPageNumber($this->_getParam('page'));
        }

        if ($canPost && !$topic->closed) {
            $this->view->form = $form = new Siteevent_Form_Post_Create();
            $form->populate(array(
                'topic_id' => $topic->getIdentity(),
                'ref' => $topic->getHref(),
                'watch' => ( false == $isWatching ? '0' : '1' ),
            ));
        }

//         if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
        $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
        $coreversion = $coremodule->version;
        if ($coreversion < '4.1.0') {
            $this->_helper->content->render();
        } else {
            $this->_helper->content
                    ->setNoRender()
                    ->setEnabled();
        }
        // }
    }

    public function createAction() {

        //ONLY LOGGED IN USER CAN CREATE TOPIC
        if (!$this->_helper->requireUser()->isValid())
            return;

        //EVENT SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
            return;

        $this->view->tab_selected_id = $this->_getParam('content_id');
        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "topic")->isValid())
            return;

        //GET EVENT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        //GET VIEWER
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Topic_Create();

        //CHECK METHOD/DATA
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $values = $form->getValues();
        $values['user_id'] = $viewer->getIdentity();
        $values['event_id'] = $siteevent->getIdentity();

        //GET TABLES
        $topicTable = Engine_Api::_()->getDbtable('topics', 'siteevent');
        $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'siteevent');
        $postTable = Engine_Api::_()->getDbtable('posts', 'siteevent');

        $db = Engine_Api::_()->getDbTable('events', 'siteevent')->getAdapter();
        $db->beginTransaction();

        try {
            //CREATE TOPIC
            $topic = $topicTable->createRow();
            $topic->setFromArray($values);
            $topic->save();

            //CREATE POST
            $values['topic_id'] = $topic->topic_id;

            $post = $postTable->createRow();
            $post->setFromArray($values);
            $post->save();

            //CREATE TOPIC WATCH
            $topicWatchesTable->insert(array(
                'resource_id' => $siteevent->getIdentity(),
                'topic_id' => $topic->getIdentity(),
                'user_id' => $viewer->getIdentity(),
                'watch' => (bool) $values['watch'],
            ));

            //ADD ACTIVITY
            $activityApi = Engine_Api::_()->getDbtable('actions', 'seaocore');
            $action = $activityApi->addActivity($viewer, $siteevent, Engine_Api::_()->siteevent()->getActivtyFeedType($siteevent, 'siteevent_topic_create'), null, array('child_id' => $topic->getIdentity()));

            if ($action) {
                $action->attach($topic);

                //START NOTIFICATION AND EMAIL WORK
                Engine_Api::_()->siteevent()->sendNotificationEmail($siteevent, $action, 'siteevent_discussion_new', 'SITEEVENT_DISCUSSION_CREATENOTIFICATION_EMAIL', null, null, 'created', $topic);
                $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($siteevent);
                if (!empty($isChildIdLeader)) {
                    Engine_Api::_()->siteevent()->sendNotificationToFollowers($siteevent, 'siteevent_discussion_new');
                }
                //END NOTIFICATION AND EMAIL WORK
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //REDIRECT TO THE TOPIC VIEW PAGE
        return $this->_redirectCustom($topic->getHref(array('content_id' => $this->view->tab_selected_id)), array('prependBase' => false));
    }

    //ACTION FOR TOPIC POST
    public function postAction() {

        //LOGGED IN USER CAN POST
        if (!$this->_helper->requireUser()->isValid())
            return;

        //TOPIC SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('siteevent_topic')->isValid())
            return;



        //SEND THE TAB ID TO THE TPL
        $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id');

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "topic")->isValid())
            return;

        //GET TOPIC SUBJECT
        $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

        //GET SITEEVENT OBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $topic->event_id);

        if ($topic->closed) {
            $this->view->status = false;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('This has been closed for posting.');
            return;
        }

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Post_Create();

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $viewer = Engine_Api::_()->user()->getViewer();
        $topicOwner = $topic->getOwner();
        $isOwnTopic = $viewer->isSelf($topicOwner);

        $postTable = Engine_Api::_()->getDbtable('posts', 'siteevent');
        $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'siteevent');
        $userTable = Engine_Api::_()->getItemTable('user');
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $activityApi = Engine_Api::_()->getDbtable('actions', 'seaocore');

        $values = $form->getValues();
        $values['user_id'] = $viewer->getIdentity();
        $values['event_id'] = $siteevent->getIdentity();
        $values['topic_id'] = $topic->getIdentity();

        $watch = (bool) $values['watch'];
        $isWatching = $topicWatchesTable->isWatching($siteevent->getIdentity(), $topic->getIdentity(), $viewer->getIdentity());

        $db = Engine_Api::_()->getDbTable('events', 'siteevent')->getAdapter();
        $db->beginTransaction();

        try {

            //CREATE POST
            $post = $postTable->createRow();
            $post->setFromArray($values);
            $post->save();

            //WATCH
            if (false == $isWatching) {
                $rowTopicId = $topicWatchesTable->select()->from($topicWatchesTable->info('name'), 'topic_id')->where('resource_id =?',  $siteevent->getIdentity())->where('topic_id =?', $topic->getIdentity())->where('user_id =?', $viewer->getIdentity())->query()->fetchColumn();
								 if(!$rowTopicId)
									$topicWatchesTable->insert(array(
											'resource_id' => $siteevent->getIdentity(),
											'topic_id' => $topic->getIdentity(),
											'user_id' => $viewer->getIdentity(),
											'watch' => (bool) $watch,
									));
            } else if ($watch != $isWatching) {
                $topicWatchesTable->update(array(
                    'watch' => (bool) $watch,
                        ), array(
                    'resource_id = ?' => $siteevent->getIdentity(),
                    'topic_id = ?' => $topic->getIdentity(),
                    'user_id = ?' => $viewer->getIdentity(),
                ));
            }

            //ACTIVITY
            $action = $activityApi->addActivity($viewer, $siteevent, Engine_Api::_()->siteevent()->getActivtyFeedType($siteevent, 'siteevent_topic_reply'), null, array('child_id' => $topic->getIdentity()));
            if ($action) {
                $action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
            }

            //NOTIFICATIONS
            $notifyUserIds = $topicWatchesTable->getNotifyUserIds($values);

            foreach ($userTable->find($notifyUserIds) as $notifyUser) {

                //DONT NOTIFY SELF
                if ($notifyUser->isSelf($viewer)) {
                    continue;
                }

                if ($notifyUser->isSelf($topicOwner)) {
                    $type = 'siteevent_discussion_response';
                } else {
                    $type = 'siteevent_discussion_reply';
                }

                $notifyApi->addNotification($notifyUser, $viewer, $topic, $type, array(
                    'message' => $this->view->BBCode($post->body),
                ));
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //REDIRECT TO THE TOPIC VIEW PAGE
        return $this->_redirectCustom($topic->getHref(array('content_id' => $this->view->tab_selected_id)), array('prependBase' => false));
    }

    //ACTION FOR MAKE STICKY
    public function stickyAction() {

        //TOPIC SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('siteevent_topic')->isValid())
            return;

        //GET TOPIC SUBJECT
        $topic = Engine_Api::_()->core()->getSubject();

        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() != $topic->user_id) {

            $canEdit = $topic->canEdit();
            if (!$canEdit) {
                return;
            }
        }

        //GET TOPIC TABLE
        $table = Engine_Api::_()->getDbTable('topics', 'siteevent');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $topic = Engine_Api::_()->core()->getSubject();
            $topic->sticky = ( null == $this->_getParam('sticky') ? !$topic->sticky : (bool) $this->_getParam('sticky') );
            $topic->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_redirectCustom($topic);
    }

    //ACTINO FOR CLOSING THE TOPIC
    public function closeAction() {

        //TOPIC SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('siteevent_topic')->isValid())
            return;

        //GET TOPIC SUBJECT
        $topic = Engine_Api::_()->core()->getSubject();

        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() != $topic->user_id) {

            $canEdit = $topic->canEdit();
            if (!$canEdit) {
                return;
            }
        }

        //GET TOPIC TABLE
        $table = Engine_Api::_()->getDbTable('topics', 'siteevent');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $topic = Engine_Api::_()->core()->getSubject();
            $topic->closed = ( null == $this->_getParam('closed') ? !$topic->closed : (bool) $this->_getParam('closed') );
            $topic->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_redirectCustom($topic);
    }

    //ACTION FOR RENAME THE TOPIC
    public function renameAction() {

        //TOPIC SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('siteevent_topic')->isValid())
            return;

        //GET TOPIC SUBJECT
        $topic = Engine_Api::_()->core()->getSubject();

        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() != $topic->user_id) {

            $canEdit = $topic->canEdit();
            if (!$canEdit) {
                return;
            }
        }

        //GET FORM
        $this->view->form = $form = new Siteevent_Form_Topic_Rename();

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            $form->title->setValue(htmlspecialchars_decode($topic->title));
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //GET TOPIC TABLE
        $table = Engine_Api::_()->getDbTable('topics', 'siteevent');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $title = htmlspecialchars($form->getValue('title'));
            $topic = Engine_Api::_()->core()->getSubject();
            $topic->title = $title;
            $topic->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forwardCustom('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Topic renamed.')),
                    'layout' => 'default-simple',
                    'parentRefresh' => true,
        ));
    }

    //ACTION FOR DELETING THE TOPIC
    public function deleteAction() {

        //TOPIC SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('siteevent_topic')->isValid())
            return;

        //GET TOPIC SUBJECT
        $topic = Engine_Api::_()->core()->getSubject();

        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() != $topic->user_id) {

            $canEdit = $topic->canEdit();
            if (!$canEdit) {
                return;
            }
        }

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Topic_Delete();

        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //GET TOPIC TABLE
        $table = Engine_Api::_()->getDbTable('topics', 'siteevent');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $topic = Engine_Api::_()->core()->getSubject();
            $siteevent = $topic->getParent('siteevent_event');
            $topic->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forwardCustom('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Topic deleted.')),
                    'layout' => 'default-simple',
                    'parentRedirect' => $siteevent->getHref(),
        ));
    }

    //ACTION FOR TOPIC WATCH
    public function watchAction() {

        //GET TOPIC SUBJECT
        $topic = Engine_Api::_()->core()->getSubject();

        //GET SITEEVENT OBJECT
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $topic->event_id);

        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, null, 'view')->isValid()) {
            return;
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $watch = $this->_getParam('watch', true);
        $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'siteevent');
        $db = $topicWatchesTable->getAdapter();
        $db->beginTransaction();
        try {
            $resultWatch = $topicWatchesTable
                    ->select()
                    ->from($topicWatchesTable->info('name'), 'watch')
                    ->where('resource_id = ?', $siteevent->getIdentity())
                    ->where('topic_id = ?', $topic->getIdentity())
                    ->where('user_id = ?', $viewer->getIdentity())
                    ->limit(1)
                    ->query()
                    ->fetchAll();
            if (empty($resultWatch))
                $isWatching = 0;
            else
                $isWatching = 1;

            if (false == $isWatching) {
                $topicWatchesTable->insert(array(
                    'resource_id' => $siteevent->getIdentity(),
                    'topic_id' => $topic->getIdentity(),
                    'user_id' => $viewer->getIdentity(),
                    'watch' => (bool) $watch,
                ));
            } else {
                $topicWatchesTable->update(array(
                    'watch' => (bool) $watch,
                        ), array(
                    'resource_id = ?' => $siteevent->getIdentity(),
                    'topic_id = ?' => $topic->getIdentity(),
                    'user_id = ?' => $viewer->getIdentity(),
                ));
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_redirectCustom($topic);
    }

}