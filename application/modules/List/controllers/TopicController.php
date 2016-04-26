<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: TopicController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_TopicController extends Core_Controller_Action_Standard {

	//COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {

		//AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'view')->isValid())
      return;

		//RETURN IF SUBJECT IS ALREADY SET
    if (Engine_Api::_()->core()->hasSubject())
      return;

		//SET TOPIC OR LISTING SUBJECT
    if (0 != ($topic_id = (int) $this->_getParam('topic_id')) &&
        null != ($topic = Engine_Api::_()->getItem('list_topic', $topic_id))) {
      Engine_Api::_()->core()->setSubject($topic);
    } else if (0 != ($listing_id = (int) $this->_getParam('listing_id')) &&
        null != ($list = Engine_Api::_()->getItem('list_listing', $listing_id))) {
      Engine_Api::_()->core()->setSubject($list);
    }
  }

	//ACTION TO BROWSE ALL TOPICS
  public function indexAction() {

		//RETURN IF LISTING SUBJECT IS NOT SET
    if (!$this->_helper->requireSubject('list_listing')->isValid())
      return;

		//GET LISTING SUBJECT
    $this->view->list = $list = Engine_Api::_()->core()->getSubject();

		//GET PAGINATOR
    $this->view->paginator = Engine_Api::_()->getDbtable('topics', 'list')->getListingTopices($list->getIdentity());
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page'));

		//CAN POST DISCUSSION IF COMMENTING IS ALLOWED
    $this->view->can_post = $this->_helper->requireAuth->setAuthParams('list_listing', null, 'comment')->checkRequire();
  }

	//ACTION TO VIEW TOPIC
  public function viewAction() {

		//RETURN IF TOPIC SUBJECT IS NOT SET
    if (!$this->_helper->requireSubject('list_topic')->isValid())
      return;

		//GET VIEWER
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->viewer_id = $viewer_id = $viewer->getIdentity();

		//GET TOPIC  SUBJECT
    $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

		//GET LIST OBJECT
    $this->view->list = $list = Engine_Api::_()->getItem('list_listing', $topic->listing_id);

		//WHO CAN POST TOPIC
    $this->view->canPost = $canPost = $list->authorization()->isAllowed($viewer, 'comment');

		//INCREASE THE VIEW COUNT
    if (!$viewer || !$viewer_id || $viewer_id != $topic->user_id) {
      $topic->view_count = new Zend_Db_Expr('view_count + 1');
      $topic->save();
    }

    //CHECK WATHCHING
    $isWatching = null;
    if ($viewer->getIdentity()) {
      $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'list');
      $isWatching = $topicWatchesTable->isWatching($list->getIdentity(), $topic->getIdentity(), $viewer_id);
      if (false == $isWatching) {
        $isWatching = null;
      } else {
        $isWatching = (bool) $isWatching;
      }
    }
    $this->view->isWatching = $isWatching;

    //GET POST ID
    $this->view->post_id = $post_id = (int) $this->_getParam('post');

    $table = Engine_Api::_()->getDbtable('posts', 'list');
    $select = $table->select()
            ->where('listing_id = ?', $list->getIdentity())
            ->where('topic_id = ?', $topic->getIdentity())
            ->order('creation_date ASC');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

		//SKIP TO PAGE OF SPECIFIED POST
    if (0 != ($post_id = (int) $this->_getParam('post_id')) &&
        null != ($post = Engine_Api::_()->getItem('list_post', $post_id))) {
      $icpp = $paginator->getItemCountPerPage();
      $page = ceil(($post->getPostIndex() + 1) / $icpp);
      $paginator->setCurrentPageNumber($page);
    }	
		//USE SPECIFIED PAGE
    else if (0 != ($page = (int) $this->_getParam('page'))) {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }

    if ($canPost && !$topic->closed) {
      $this->view->form = $form = new List_Form_Post_Create();
      $form->populate(array(
              'topic_id' => $topic->getIdentity(),
              'ref' => $topic->getHref(),
              'watch' => ( false == $isWatching ? '0' : '1' ),
      ));
    }
  }

  public function createAction() {

		//ONLY LOGGED IN USER CAN CREATE TOPIC
    if (!$this->_helper->requireUser()->isValid())
      return;

		//LISTING SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject('list_listing')->isValid())
      return;

		//AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'comment')->isValid())
      return;

		//GET LISTING
    $this->view->list = $list = Engine_Api::_()->core()->getSubject('list_listing');

		//GET VIEWER
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    //MAKE FORM
    $this->view->form = $form = new List_Form_Topic_Create();

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
    $values['listing_id'] = $list->getIdentity();

		//GET TABLES
    $topicTable = Engine_Api::_()->getDbtable('topics', 'list');
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'list');
    $postTable = Engine_Api::_()->getDbtable('posts', 'list');

    $db = Engine_Api::_()->getDbTable('listings', 'list')->getAdapter();
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
              'resource_id' => $list->getIdentity(),
              'topic_id' => $topic->getIdentity(),
              'user_id' => $viewer->getIdentity(),
              'watch' => (bool) $values['watch'],
      ));

      //ADD ACTIVITY
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $topic, 'list_topic_create');

      if ($action) {
        $action->attach($topic);
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECT TO THE POST
    $this->_redirectCustom($post);
  }

	//ACTION FOR TOPIC POST
  public function postAction() {

		//LOGGED IN USER CAN POST
    if (!$this->_helper->requireUser()->isValid())
      return;

		//TOPIC SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject('list_topic')->isValid())
      return;

		//AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'comment')->isValid())
      return;

		//GET TOPIC SUBJECT
    $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

		//GET LIST OBJECT
    $this->view->list = $list = Engine_Api::_()->getItem('list_listing', $topic->listing_id);

    if ($topic->closed) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('This has been closed for posting.');
      return;
    }

    //MAKE FORM
    $this->view->form = $form = new List_Form_Post_Create();

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

    $postTable = Engine_Api::_()->getDbtable('posts', 'list');
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'list');
    $userTable = Engine_Api::_()->getItemTable('user');
    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['listing_id'] = $list->getIdentity();
    $values['topic_id'] = $topic->getIdentity();

    $watch = (bool) $values['watch'];
    $isWatching = $topicWatchesTable->isWatching($list->getIdentity(), $topic->getIdentity(), $viewer->getIdentity());

    $db = Engine_Api::_()->getDbTable('listings', 'list')->getAdapter();
    $db->beginTransaction();

    try {

      //CREATE POST
      $post = $postTable->createRow();
      $post->setFromArray($values);
      $post->save();

      //WATCH
      if (false == $isWatching) {
        $topicWatchesTable->insert(array(
                'resource_id' => $list->getIdentity(),
                'topic_id' => $topic->getIdentity(),
                'user_id' => $viewer->getIdentity(),
                'watch' => (bool) $watch,
        ));
      } else if ($watch != $isWatching) {
        $topicWatchesTable->update(array(
                'watch' => (bool) $watch,
            ), array(
                'resource_id = ?' => $list->getIdentity(),
                'topic_id = ?' => $topic->getIdentity(),
                'user_id = ?' => $viewer->getIdentity(),
        ));
      }

      //ACTIVITY
      $action = $activityApi->addActivity($viewer, $topic, 'list_topic_reply');
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
          $type = 'list_discussion_response';
        } else {
          $type = 'list_discussion_reply';
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

    //REDIRECT
    $this->_redirectCustom($post);
  }

	//ACTION FOR MAKE STICKY
  public function stickyAction() {

		//TOPIC SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject('list_topic')->isValid())
      return;

		//AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'edit')->isValid())
      return;

		//GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();
	
		//GET TOPIC TABLE
    $table = Engine_Api::_()->getDbTable('topics', 'list');
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
    if (!$this->_helper->requireSubject('list_topic')->isValid())
      return;

		//AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'edit')->isValid())
      return;

		//GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();
	
		//GET TOPIC TABLE
    $table = Engine_Api::_()->getDbTable('topics', 'list');
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
    if (!$this->_helper->requireSubject('list_topic')->isValid())
      return;

		//AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'edit')->isValid())
      return;

		//GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

		//GET FORM
    $this->view->form = $form = new List_Form_Topic_Rename();

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
    $table = Engine_Api::_()->getDbTable('topics', 'list');
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

    return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Topic renamed.')),
            'layout' => 'default-simple',
            'parentRefresh' => true,
    ));
  }

	//ACTION FOR DELETING THE TOPIC
  public function deleteAction() {

		//TOPIC SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject('list_topic')->isValid())
      return;

		//AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'edit')->isValid())
      return;

		//GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

		//MAKE FORM
    $this->view->form = $form = new List_Form_Topic_Delete();

		//CHECK POST
    if (!$this->getRequest()->isPost()) {
      return;
    }

		//FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

		//GET TOPIC TABLE
    $table = Engine_Api::_()->getDbTable('topics', 'list');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $topic = Engine_Api::_()->core()->getSubject();
      $list = $topic->getParent('list_listing');
      $topic->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Topic deleted.')),
            'layout' => 'default-simple',
            'parentRedirect' => $list->getHref(),
    ));
  }

	//ACTION FOR TOPIC WATCH
  public function watchAction() {

		//GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

		//GET LIST OBJECT
    $list = Engine_Api::_()->getItem('list_listing', $topic->listing_id);

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    $watch = $this->_getParam('watch', true);
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'list');
    $db = $topicWatchesTable->getAdapter();
    $db->beginTransaction();
    try {

      $resultWatch = $topicWatchesTable
              ->select()
              ->from($topicWatchesTable->info('name'), 'watch')
              ->where('resource_id = ?', $list->getIdentity())
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
                'resource_id' => $list->getIdentity(),
                'topic_id' => $topic->getIdentity(),
                'user_id' => $viewer->getIdentity(),
                'watch' => (bool) $watch,
        ));
      } else{
        $topicWatchesTable->update(array(
                'watch' => (bool) $watch,
            ), array(
                'resource_id = ?' => $list->getIdentity(),
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
