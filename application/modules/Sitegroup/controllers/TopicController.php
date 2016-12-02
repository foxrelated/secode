<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TopicController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_TopicController extends Seaocore_Controller_Action_Standard {

  public function init() {

    //GET GROUP ID
    $group_id = $this->_getParam('group_id');

    //PACKAGE BASE PRIYACY START
    if (!empty($group_id)) {
      $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
      if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupdiscussion")) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      } else {
        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sdicreate');
        if (empty($isGroupOwnerAllow)) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if (0 !== ($topic_id = (int) $this->_getParam('topic_id'))) {
        $topic = Engine_Api::_()->getItem('sitegroup_topic', $topic_id);
        $group_id = $topic->group_id;
      }
      if (Engine_Api::_()->core()->hasSubject('sitegroup_group') != null) {
        $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
        if (!empty($sitegroup))
          $group_id = $sitegroup->group_id;
      }
    }

    if (Engine_Api::_()->core()->hasSubject())
      return;

    if (0 !== ($topic_id = (int) $this->_getParam('topic_id')) &&
            null !== ($topic = Engine_Api::_()->getItem('sitegroup_topic', $topic_id))) {
      Engine_Api::_()->core()->setSubject($topic);
    } else if (0 !== ($group_id = (int) $this->_getParam('group_id')) &&
            null !== ($sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id))) {
      Engine_Api::_()->core()->setSubject($sitegroup);
    }
  }

  //ACTION FOR SHOWING THE TOPIC
  public function indexAction() {

    //CHECK SITEGROUP SUBJECT IS VALID OR NOT
    if (!$this->_helper->requireSubject('sitegroup_group')->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');

    //GET SITEGROUP SUBJECT
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject();

    //SEND THE TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('topics', 'sitegroup')->getGroupTopics($sitegroup->getIdentity());
    $paginator->setCurrentPageNumber($this->_getParam('page'));
  }

  //ACTION FOR VIEW THE TOPIC
  public function viewAction() {

    //CHECK SITEGROUP SUBJECT IS VALID OR NOT
    if (!$this->_helper->requireSubject('sitegroup_topic')->isValid())
      return;
    
    //SEND THE TOPIC SUBJECT TO THE TPL
    $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

    //GET THE SITEGROUP ITEM 
    $this->view->sitegroup = $sitegroup = $topic->getParentSitegroup();    

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled();
    }    

  }

  //ACTION FOR CREATING THE TOPIC, POST 
  public function createAction() {

    //USER VALIDATION REQURIED
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK SITEGROUP SUBJECT IS VALID OR NOT
    if (!$this->_helper->requireSubject('sitegroup_group')->isValid())
      return;

    //GET SITEGROUP SUBJECT
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdicreate');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //GET LOGGED USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');

    //MAKE FORM
    $this->view->form = $form = new Sitegroup_Form_Topic_Create();

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');
    $this->view->resource_type = $this->_getParam('resource_type', null);
    $this->view->resource_id = $this->_getParam('resource_id', 0);
    //CHECK METHOD / DATA
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['group_id'] = $group_id = $sitegroup->getIdentity();

    $topicTable = Engine_Api::_()->getDbtable('topics', 'sitegroup');
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitegroup');
    $postTable = Engine_Api::_()->getDbtable('posts', 'sitegroup');

    //GET DB
    $db = $sitegroup->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //CREATE TOPIC
      $values['resource_type'] = $this->_getParam('resource_type', null);
      $values['resource_id'] = $this->_getParam('resource_id', 0);
      $topic = $topicTable->createRow();
      $topic->setFromArray($values);
      $topic->view_count = 1;
      $topic->save();
      $values['topic_id'] = $topic->topic_id;

      //CREATE POST
      $post = $postTable->createRow();
      $post->setFromArray($values);
      $post->save();

      $topicWatchesTable->insert(array(
          'resource_id' => $sitegroup->getIdentity(),
          'topic_id' => $topic->getIdentity(),
          'user_id' => $viewer->getIdentity(),
          'watch' => (bool) $values['watch'],
          'group_id' => $group_id,
      ));
      
      //START WORK WHEN ANY ONE CREATE DISCUSSION IN THE GROUP AND IF GROUP HAVE MANY MEMBER THEN ENTRY FOR ALL GROUP MEMEBR IS WATHCED.
      if(Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitegroupmember')) {
      $paginator = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinMembers($sitegroup->getIdentity(), '', '', 0);
				if(!empty($paginator)) {
					foreach($paginator as $result) {						
						$user_id = $result->user_id;
						$topic_id = $topic->getIdentity();

						$db = Engine_Db_Table::getDefaultAdapter();
						$db->query("INSERT IGNORE INTO `engine4_sitegroup_topicwatches` (`resource_id`, `topic_id`, `user_id`, `watch`, `group_id`) VALUES ('$group_id', '$topic_id', '$user_id', '1', '$group_id');");
					}
				}
      }
      //END WORK WHEN ANY ONE CREATE DISCUSSION IN THE GROUP AND IF GROUP HAVE MANY MEMBER THEN ENTRY FOR ALL GROUP MEMEBR IS WATHCED.
      

      //ADD ACTIVITY
      if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $activityFeedType = null;
        if (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
          $activityFeedType = 'sitegroup_admin_topic_create';
        elseif ($sitegroup->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup))
          $activityFeedType = 'sitegroup_topic_create';


        if ($activityFeedType) {
          $action = $activityApi->addActivity($viewer, $sitegroup, $activityFeedType, null, array('child_id' => $topic->getIdentity()));
          Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
        }
        if ($action) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $topic);
          //SENDING ACTIVITY FEED TO FACEBOOK.
          $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
          if (!empty($enable_Facebooksefeed)) {
            $topiccreate_array = array();
            $topiccreate_array['type'] = 'sitegroup_topic_create';
            $topiccreate_array['object'] = $topic;
            $topiccreate_array['description'] = $values['body'];
            Engine_Api::_()->facebooksefeed()->sendFacebookFeed($topiccreate_array);
          }
        }
        
        //GROUP NOTE CREATE NOTIFICATION AND EMAIL WORK
        if(!empty($action)) {
					Engine_Api::_()->sitegroup()->sendNotificationEmail($topic, $action, 'sitegroupdiscussion_create', 'SITEGROUPDISCUSSION_CREATENOTIFICATION_EMAIL', 'Groupevent Invite');
					$isGroupAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->isGroupAdmins($viewer->getIdentity(), $group_id);
					if (!empty($isGroupAdmins)) {
						//NOTIFICATION FOR ALL FOLLWERS.
						Engine_Api::_()->sitegroup()->sendNotificationToFollowers($topic, $action, 'sitegroupdiscussion_create');
					}
				}
      }

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECT TO THE TOPIC VIEW GROUP
    return $this->_redirectCustom($topic->getHref(array('tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab'))), array('prependBase' => false));
  }

  //ACTION FOR SENDING THE POST 
  public function postAction() {

    //USER VALIDATION REQURIED
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitegroup_topic')->isValid())
      return;

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //GET TOPIC SUBJECT
    $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

    //GET SITEGROUP SUBJECT ADN GROUP ID
    $this->view->sitegroup = $sitegroup = $topic->getParentSitegroup();
    $group_id = $sitegroup->group_id;

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdicreate');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');

    if ($topic->closed) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('This topic is closed for posting.');
      return;
    }
    //MAKE FORM
    $this->view->form = $form = new Sitegroup_Form_Post_Create();

    $quote_id = $this->getRequest()->getParam('quote_id');
    if( !empty($quote_id) ) {
      $quote = Engine_Api::_()->getItem('sitegroup_post', $quote_id);
      if($quote->user_id == 0) {
          $owner_name = Zend_Registry::get('Zend_Translate')->_('Deleted Member');
      } else {
          $owner_name = $quote->getOwner()->__toString();
      }

			$form->body->setValue("<blockquote><strong>" . $this->view->translate('%1$s said:', $owner_name) . "</strong><br />" . $quote->body . "</blockquote><br />");

    }

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    //PROCESS
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET TOPIC OWNER
    $topicOwner = $topic->getOwner();

    //SELF CREATED TOPIC OR NOT
    $isOwnTopic = $viewer->isSelf($topicOwner);

    //GET POST TABLE
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitegroup');

    //GET FORM VALUES
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['group_id'] = $sitegroup->getIdentity();
    $values['topic_id'] = $topic->getIdentity();

    $watch = (bool) $values['watch'];
    $isWatching = $topicWatchesTable->isWatching($sitegroup->getIdentity(), $topic->getIdentity(), $viewer->getIdentity());

    //GET DB
    $db = $sitegroup->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //CREATE POST
      $post = Engine_Api::_()->getDbtable('posts', 'sitegroup')->createRow();
      $post->setFromArray($values);
      $post->save();

      //WATCH
      if (false === $isWatching) {
        $topicWatchesTable->insert(array(
            'resource_id' => $sitegroup->getIdentity(),
            'topic_id' => $topic->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'watch' => (bool) $watch,
            'group_id' => $values['group_id'],
        ));
      } else if ($watch != $isWatching) {
        $topicWatchesTable->update(array(
            'watch' => (bool) $watch,
            'group_id' => $values['group_id'],
                ), array(
            'resource_id = ?' => $sitegroup->getIdentity(),
            'topic_id = ?' => $topic->getIdentity(),
            'user_id = ?' => $viewer->getIdentity(),
        ));
      }

      //ADD ACTIVITY
      if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $activityFeedType = null;
        if (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
          $activityFeedType = 'sitegroup_admin_topic_reply';
        elseif ($sitegroup->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup))
          $activityFeedType = 'sitegroup_topic_reply';


  //      if ($activityFeedType) {
  //        $action = $activityApi->addActivity($viewer, $sitegroup, $activityFeedType);
  //        Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
  //      }
        //ACTIVITY      
        if ($activityFeedType) {
          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitegroup, $activityFeedType, null, array('child_id' => $topic->getIdentity()));
          Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
          if (!empty($action))
            $action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);

          //SENDING ACTIVITY FEED TO FACEBOOK.
          $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
          if (!empty($enable_Facebooksefeed)) {
            $topicreply_array = array();
            $topicreply_array['type'] = 'sitegroup_topic_reply';
            $topicreply_array['object'] = $topic;
            $topicreply_array['description'] = $values['body'];
            Engine_Api::_()->facebooksefeed()->sendFacebookFeed($topicreply_array);
          }
        }
      }
      //NOTIFICATIONS
      $notifyUserIds = $topicWatchesTable->getNotifyUserIds($values);

      foreach (Engine_Api::_()->getItemTable('user')->find($notifyUserIds) as $notifyUser) {
        //DON'T NOTIFY SELF
        if ($notifyUser->isSelf($viewer)) {
          continue;
        }

        if ($notifyUser->isSelf($topicOwner)) {
          $type = 'sitegroup_discussion_response';
        } else {
          $type = 'sitegroup_discussion_reply';
        }

        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($notifyUser, $viewer, $topic, $type, array(
            'message' => $this->view->BBCode($post->body),
        ));
      }

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    if($this->_getParam('page'))
            $redirct_Url = $post->getHref(array('page' => $this->_getParam('page')))."#sitegroup_post_".$post->getIdentity();
        else
            $redirct_Url = $post->getHref()."#sitegroup_post_".$post->getIdentity();
    

    //REDIRECT TO THE POST GROUP
    $this->_redirectCustom($redirct_Url);
  }

  //ACTION FOR STICKY THE TOPIC 
  public function stickyAction() {

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitegroup_topic')->isValid())
      return;

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject('sitegroup_topic');

    //START MANAGE-ADMIN CHECK
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $topic->group_id);
    $can_edit = $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');

    //CHECKING WHETHER THE USER HAVE THE PERMISSION OR NOT.
    if ($can_edit != 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != $topic->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET DB
    $db = $topic->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE STICKY
      $topic->sticky = ( null === $this->_getParam('sticky') ? !$topic->sticky : (bool) $this->_getParam('sticky') );
      $topic->save();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    //REDIRECT TO THE TOPIC VIEW GROUP
    $this->_redirectCustom($topic);
  }

  //ACTION FOR CLOSE THE TOPIC 
  public function closeAction() {

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitegroup_topic')->isValid())
      return;

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

    //GET SITEGROUP ITEM
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $topic->group_id);

    //START MANAGE-ADMIN CHECK
    $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if ($can_edit != 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != $topic->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET DB
    $db = $topic->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE TOPIC CLOSED
      $topic->closed = ( null === $this->_getParam('closed') ? !$topic->closed : (bool) $this->_getParam('closed') );
      $topic->save();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    //REDIRECT TO THE TOPIC VIEW GROUP
    $this->_redirectCustom($topic);
  }

  //ACTION FOR RENAME THE TOPIC 
  public function renameAction() {

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitegroup_topic')->isValid())
      return;

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

    //GET SITEGROUP ITEM
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $topic->group_id);

    //START MANAGE-ADMIN CHECK
    $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if ($can_edit != 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != $topic->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //MAKE FORM
    $this->view->form = $form = new Sitegroup_Form_Topic_Rename();

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $form->title->setValue(htmlspecialchars_decode($topic->title));
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET DB
    $db = $topic->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE TOPIC TITLE
      $topic->title = htmlspecialchars($form->getValue('title'));
      ;
      $topic->save();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECTING
    return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected topic has been successfully renamed.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
  }

  //ACTION FOR DELETE THE TOPIC 
  public function deleteAction() {

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitegroup_topic')->isValid())
      return;

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

    //START MANAGE-ADMIN CHECK
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $topic->group_id);
    $can_edit = $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if ($can_edit != 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != $topic->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //MAKE FORM
    $this->view->form = $form = new Sitegroup_Form_Topic_Delete();

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET DB
    $db = $topic->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //GET GROUP ID
      $group_id = $topic->group_id;

      //DELETE TOPIC
      $topic->delete();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECTING
    return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected topic has been deleted.')),
                'layout' => 'default-simple',
                'parentRedirect' => $this->_helper->url->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id), 'tab' => $this->view->tab_selected_id), 'sitegroup_entry_view'),
            ));
  }

  //ACTION FOR WATCH THE TOPIC 
  public function watchAction() {

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

    //GET SITEGROUP ITEM
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $topic->group_id);

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET WATCH PARAM
    $watch = $this->_getParam('watch', true);

    //GET TOPIC WATCH TABLE
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitegroup');

    //GET DB
    $db = $topicWatchesTable->getAdapter();
    $db->beginTransaction();
    try {
      $isWatching = $topicWatchesTable->isWatching($sitegroup->getIdentity(), $topic->getIdentity(), $viewer->getIdentity());
      if (false === $isWatching) {
        $topicWatchesTable->insert(array(
            'resource_id' => $sitegroup->getIdentity(),
            'topic_id' => $topic->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'watch' => (bool) $watch,
            'group_id' => $topic->group_id,
        ));
      } else {
        $topicWatchesTable->update(array(
            'watch' => (bool) $watch,
            'group_id' => $topic->group_id,
                ), array(
            'resource_id = ?' => $sitegroup->getIdentity(),
            'topic_id = ?' => $topic->getIdentity(),
            'user_id = ?' => $viewer->getIdentity(),
        ));
      }

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirectCustom($topic);
  }

}

?>