<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupmember_IndexController extends Seaocore_Controller_Action_Standard {

    public function init() {

        //GET GROUP ID
        $group_id = $this->_getParam('group_id');

        //PACKAGE BASE PRIYACY START
        if (!empty($group_id)) {

            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

            $allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate');
            if (empty($allowGroup)) {
                return $this->_forwardCustom('requireauth', 'error', 'core');
            }
        }
    }

    //ACTION FOR GROUP MEMBER HOME.
    public function homeAction() {

        //CHECK VIEW PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'view')->isValid())
            return;

        //CHECK THE VERSION OF THE CORE MODULE
        $this->_helper->content->setNoRender()->setEnabled();
    }

    //ACTION FOR MEMBER BROWSE GROUP.
    public function browseAction() {

        //CHECK VIEW PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'view')->isValid())
            return;

        $sitegroupmemberBrowse = Zend_Registry::isRegistered('sitegroupmemberBrowse') ? Zend_Registry::get('sitegroupmemberBrowse') : null;
        if (empty($sitegroupmemberBrowse))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        //CHECK THE VERSION OF THE CORE MODULE
        $this->_helper->content->setNoRender()->setEnabled();
    }

    //ACTION FOR HIGHLIGHTED MEMBER.
    public function highlightedAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET MEMBER ID AND OBJECT
        $this->view->member_id = $member_id = $this->_getParam('member_id');
        $sitegroupmember = Engine_Api::_()->getDbTable('membership', 'sitegroup')->getMembersObject($member_id);

        $group_id = $sitegroupmember->group_id;
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        $this->view->highlighted = $sitegroupmember->highlighted;

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        $this->view->canEdit = 0;
        if (!empty($isManageAdmin)) {
            $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            //NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        if (!$this->getRequest()->isPost())
            return;

        //GET VIEWER INFORMATION
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $tab_selected_id = $this->_getParam('tab');

        if ($viewer_id == $sitegroupmember->user_id || !empty($this->view->canEdit)) {

            $this->view->permission = true;
            $this->view->success = false;
            $db = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getAdapter();
            $db->beginTransaction();

            try {
                if ($sitegroupmember->highlighted == 0) {
                    $sitegroupmember->highlighted = 1;
                } else {
                    $sitegroupmember->highlighted = 0;
                }
                $sitegroupmember->save();

                $db->commit();
                $this->view->success = true;
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        } else {
            $this->view->permission = false;
        }

        if ($sitegroupmember->highlighted) {
            $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Member has been successfully made highlighted.'));
        } else {
            $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Member has been successfully made un-highlighted.'));
        }

        $this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 2,
            'parentRedirectTime' => '2',
            'parentRedirect' => $this->_helper->url->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id), 'tab' => $tab_selected_id), 'sitegroup_entry_view', true),
            'format' => 'smoothbox',
            'messages' => ''
        ));
    }

    //ACTION FOR REMOVE THE MEMBER FROM A GROUP.
    public function removeAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET MEMBER ID AND OBJECT
        $this->view->member_id = $member_id = $this->_getParam('member_id');
        $sitegroupmember = Engine_Api::_()->getDbTable('membership', 'sitegroup')->getMembersObject($member_id);
        $this->view->params = $this->_getParam('params', null);

        $group_id = $sitegroupmember->group_id;
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        //$this->view->active = $sitegroupmember->active;
        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        $this->view->canEdit = 0;
        if (!empty($isManageAdmin)) {
            $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {//NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        if (!$this->getRequest()->isPost())
            return;

        //GET VIEWER INFORMATION
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $tab_selected_id = $this->_getParam('tab');

        if ($viewer_id == $sitegroupmember->user_id || !empty($this->view->canEdit)) {
            $this->view->permission = true;
            $this->view->success = false;

            if (!empty($member_id)) {

                //DELETE THE RESULT FORM THE TABLE.
                Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('member_id =?' => $member_id, 'group_id =?' => $group_id));
                Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
                
                //DELETE ACTIVITY FEED OF JOIN GROUP ACCORDING TO USER ID.        
                $action_id = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type = ?' => 'sitegroup_join', 'subject_id = ?' => $viewer_id, 'object_id = ?' => $group_id));
                if ($action_id) {
                    $action = Engine_Api::_()->getItem('activity_action', $action_id->action_id);
                    $action->delete();
                }
            }
        } else {
            $this->view->permission = false;
        }

        $this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 2,
            'parentRedirect' => $this->_helper->url->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id), 'tab' => $tab_selected_id), 'sitegroup_entry_view', true),
            'parentRedirectTime' => '2',
            'format' => 'smoothbox',
            'messages' => ''
        ));
    }

    //REMPVE GROUP COVER PHOTO.
    public function removeCoverphotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $group_id = $this->_getParam('group_id');
        if ($this->getRequest()->isPost()) {

            Engine_Api::_()->getDbtable('groups', 'sitegroup')->update(array('group_cover' => 0), array('group_id =?' => $group_id));

            $this->_forwardCustom('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
    }

    //ACTION FOR APPROVE THE MEMBER WHO SEND REQUEST FOR JOIN GROUP.
    public function approveAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET MEMBER ID AND OBJECT
        $this->view->member_id = $this->_getParam('member_id');
        $sitegroupmember = Engine_Api::_()->getDbTable('membership', 'sitegroup')->getMembersObject($this->_getParam('member_id'));
        $this->view->active = $sitegroupmember->active;
        $this->view->user_approved = $sitegroupmember->user_approved;

        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $sitegroupmember->group_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        $this->view->canEdit = 0;
        if (!empty($isManageAdmin)) {
            $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {//NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        if (!$this->getRequest()->isPost())
            return;

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $tab_selected_id = $this->_getParam('tab');

        if ($viewer_id == $sitegroupmember->user_id || !empty($this->view->canEdit)) {

            $this->view->permission = true;
            $this->view->success = false;
            $db = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getAdapter();
            $db->beginTransaction();

            try {

                if ($sitegroupmember->active == 0 && $sitegroupmember->user_approved == 0) {
                    $sitegroupmember->active = 1;
                    $sitegroupmember->user_approved = 1;
                    $sitegroupmember->resource_approved = 1;
                }
                $sitegroupmember->save();

                $user = Engine_Api::_()->getItem('user', $sitegroupmember->user_id);

                //GET GROUP TITLE
                $grouptitle = $sitegroup->title;

                //GROUP URL
                $group_url = Engine_Api::_()->sitegroup()->getGroupUrl($sitegroup->group_id);

                //GET GROUP URL
                $group_baseurl = 'http://' . $_SERVER['HTTP_HOST'] .
                        Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true);

                //MAKING GROUP TITLE LINK
                $group_title_link = '<a href="' . $group_baseurl . '"  >' . $grouptitle . ' </a>';

                //EMAIL THAT GOES TO NEW OWNER
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'SITEGROUPMEMBER_APPROVE_EMAIL', array(
                    'group_title' => $grouptitle,
                    'group_title_with_link' => $group_title_link,
                    'object_link' => $group_baseurl,
                    'email' => $email,
                    'queue' => true
                ));

                //MEMBER APPROVED NOTIFICATION.
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $sitegroup, 'sitegroupmember_accepted');

                // Set the request as handled
                $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                        $viewer, $sitegroup, 'sitegroupmember_approve');
                if ($notification) {
                    $notification->mitigated = true;
                    $notification->read = true;
                    $notification->save();
                }

                //ADD ACTIVITY
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $action = $activityApi->addActivity($user, $sitegroup, 'sitegroup_join');
                Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action, true);
                //Member count increase when member join the group.
                Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
                $sitegroup->save();

                $db->commit();
                $this->view->success = true;
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        } else {
            $this->view->permission = false;
        }
    }

    //ACTION FOR REJECT GROUP MEMBER REQUEST.
    public function rejectAction() {

        //GET THE GROUP ID AND MEMBER ID AND USER ID
        $group_id = $this->_getParam('group_id');
        $member_id = $this->_getParam('member_id');
        $user_id = $this->_getParam('user_id');

        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        //CHECK AUTH
        if (!$this->_helper->requireUser()->isValid())
            return;

        //MAKE FORM
        $this->view->form = $form = new Sitegroupmember_Form_Member();

        $form->submit->setLabel('Reject Invitation');
        $form->setTitle('Reject Group Invitation');
        $form->setDescription('Would you like to reject the invitation to this group?');

        //PROCESS FORM
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
            return;
        }

        // Process
        //$viewer = Engine_Api::_()->user()->getViewer();
        //Set the request as handled
        Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_type =?' => 'sitegroup_group', 'object_id =?' => $group_id, 'subject_id =?' => $user_id));

        if (!empty($group_id)) {
            //DELETE THE RESULT FORM THE TABLE.
            Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('group_id =?' => $group_id, 'member_id =?' => $member_id));

            //Member count decrease when member join the group.
            $sitegroup->member_count--;
            $sitegroup->save();
        }
    }

    public function createAnnouncementAction() {

        //GETTING THE OBJECT AND GROUP ID AND RESOURCE TYPE.
        $this->view->group_id = $group_id = $this->_getParam('group_id', null);
        $this->view->sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        $this->view->sitegroups_view_menu = 30;
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegroup_main');
        $announcementsTable = Engine_Api::_()->getDbTable('announcements', 'sitegroup');

        //MAKE FORM
        $this->view->form = $form = new Sitegroupmember_Form_Announcement_Create();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $values['group_id'] = $group_id;
                $announcement = $announcementsTable->createRow();
                $announcement->setFromArray($values);
                $announcement->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            return $this->_helper->redirector->gotoRoute(array('action' => 'announcements', 'group_id' => $group_id), 'sitegroup_dashboard', true);
        }
    }

    public function editAnnouncementAction() {

        $announcement_id = $this->_getParam('announcement_id', null);
        $this->view->group_id = $group_id = $this->_getParam('group_id', null);
        $this->view->sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        $this->view->sitegroups_view_menu = 30;
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegroup_main');
        //MAKE FORM
        $this->view->form = $form = new Sitegroupmember_Form_Announcement_Edit();

        //SHOW PRE-FIELD FORM 
        $announcement = Engine_Api::_()->getItem('sitegroup_announcements', $announcement_id);
        $resultArray = $announcement->toArray();

        $resultArray['startdate'] = $resultArray['startdate'] . ' 00:00:00';
        $resultArray['expirydate'] = $resultArray['expirydate'] . ' 00:00:00';

        //IF NOT POST OR FORM NOT VALID THAN RETURN AND POPULATE THE FROM.
        if (!$this->getRequest()->isPost()) {
            $form->populate($resultArray);
            return;
        }

        //IF NOT POST OR FORM NOT VALID THAN RETURN
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //GET FORM VALUES
        $values = $form->getValues();

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $announcement->setFromArray($values);
            $announcement->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'announcements', 'group_id' => $group_id), 'sitegroup_dashboard', true);
    }

    public function deleteAnnouncementAction() {

        //GET THE CONTENT ID AND RESOURCE TYPE.
        $announcement_id = (int) $this->_getParam('announcement_id');
        $group_id = $this->_getParam('group_id');
        Engine_Api::_()->getDbtable('announcements', 'sitegroup')->delete(array('announcement_id = ?' => $announcement_id, 'group_id = ?' => $group_id));
        exit();
    }

    public function notificationSettingsAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->_helper->layout->setLayout('default-simple');

        //GET GROUP ID
        $member_id = $this->_getParam('member_id');
        $sitegroupmember = Engine_Api::_()->getDbTable('membership', 'sitegroup')->getMembersObject($member_id);
        $group_id = $sitegroupmember->group_id;
        $user_id = $sitegroupmember->user_id;

        //SET FORM
        $this->view->form = $form = new Sitegroupmember_Form_NotificationSettings();

        $results = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($user_id, $group_id);

        //EMAIL NOTIFICATION WORK
        $emailSettings = 0;
        $notificationSettings = 0;
        $this->view->email = $value['email'] = $results["email"];
        $action_email = json_decode($results['action_email']);
        if ($action_email) {
            $value['emailcreated'] = $action_email->emailcreated;
            $value['emailposted'] = $action_email->emailposted;
            if ($value['emailcreated'] == 0 && $value['emailcreated'] == 0) {
                $emailSettings = 1;
            }
        }

        //ONLY NOTIFICATION WORK
        $this->view->notification = $value['notification'] = $results["notification"];
        $action_notification = json_decode($results['action_notification']);
        if ($action_notification) {
            $value['notificationcreated'] = $action_notification->notificationcreated;
            $value['notificationposted'] = $action_notification->notificationposted;
            $value['notificationfollow'] = $action_notification->notificationfollow;
            $value['notificationlike'] = $action_notification->notificationlike;
            $value['notificationcomment'] = $action_notification->notificationcomment;
            $value['notificationjoin'] = $action_notification->notificationjoin;
            if ($value['notificationcreated'] == 0 && $value['notificationposted'] == 0 && $value['notificationfollow'] == 0 && $value['notificationlike'] == 0 && $value['notificationcomment'] == 0 && $value['notificationjoin'] == 0) {
                $notificationSettings = 1;
            }
        }
        $this->view->notificationSettings = $notificationSettings;
        $this->view->emailSettings = $emailSettings;
        //$value['action_notification'] = json_decode($results['action_notification']);

        $form->populate($value);

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET FORM VALUES

            $values = $form->getValues();

            //EMAIL NOTIFICATION work
            $tempArray['emailposted'] = $values['emailposted'];
            $tempArray['emailcreated'] = $values['emailcreated'];
            $action_email = json_encode($tempArray);

            //$action_notification = json_encode($values['action_notification']);
            //only NOTIFICATION work
            $tempNotificationArray['notificationposted'] = $values['notificationposted'];
            $tempNotificationArray['notificationcreated'] = $values['notificationcreated'];
            $tempNotificationArray['notificationfollow'] = $values['notificationfollow'];
            $tempNotificationArray['notificationlike'] = $values['notificationlike'];
            $tempNotificationArray['notificationcomment'] = $values['notificationcomment'];
            $tempNotificationArray['notificationjoin'] = $values['notificationjoin'];
            $action_notification = json_encode($tempNotificationArray);

            if (isset($values['email'])) {

                //MANAGEADMIN TABLE UPDATE WHEN ANY MEMBER UPDATE EMAIL AND NOTIFICATION SETTINGS FROM THE MEMBER TAB.
                $email = array();
                if ($values['emailposted'] == 1)
                    $email[] = 'posted';
                if ($values['emailcreated'] == 1)
                    $email[] = 'created';
                Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->update(array('email' => $values['email'], 'action_email' => json_encode($email)), array('group_id =?' => $group_id, 'user_id =?' => $user_id));

                //UPDATE WHEN ANY MEMBERSHIP UPDATE EMAIL AND NOTIFICATION SETTINGS FROM THE MEMBER TAB.
                Engine_Api::_()->getDbtable('membership', 'sitegroup')->update(array('email' => $values['email'], 'action_email' => $action_email), array('resource_id =?' => $group_id, 'user_id =?' => $user_id));
            }

            if (isset($values['notification'])) {
                $notification = array();
                if ($values['notificationposted'] == 1)
                    $notification[] = 'posted';
                if ($values['notificationcreated'] == 1)
                    $notification[] = 'created';
                if ($values['notificationfollow'] == 1)
                    $notification[] = 'follow';
                if ($values['notificationlike'] == 1)
                    $notification[] = 'like';
                if ($values['notificationcomment'] == 1)
                    $notification[] = 'comment';
                if ($values['notificationjoin'] == 1)
                    $notification[] = 'join';
                Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->update(array('notification' => $values['notification'], 'action_notification' => serialize($notification)), array('group_id =?' => $group_id, 'user_id =?' => $user_id));

//         if($values['notification'] == 1) {
//         //, 'action_notification' => $action_notification
// 					//$action_notification = 'a:5:{i:0;s:6:"posted";i:1;s:7:"created";i:2;s:7:"comment";i:3;s:4:"like";i:4;s:6:"follow";}';
// 					Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->update(array('notification'=> '1'), array('group_id =?' => $group_id, 'user_id =?' => $user_id));
// 					
// 					//Engine_Api::_()->getDbtable('membership', 'sitegroup')->update(array('notification'=> $values['notification'], 'action_notification' => $action_notification), array('group_id =?' => $group_id, 'user_id =?' => $user_id));
//         }

                Engine_Api::_()->getDbtable('membership', 'sitegroup')->update(array('notification' => $values['notification'], 'action_notification' => $action_notification), array('group_id =?' => $group_id, 'user_id =?' => $user_id));
            }


            return $this->_forwardCustom('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRedirect' => $this->_helper->url->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id), 'tab' => $this->_getParam('tab')), 'sitegroup_entry_view', true),
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Notification settings have been saved successfully.'))
            ));
        }
    }

    //ACTION FOR EDIT TITLE OF GROUP MEMBER.
    public function editAction() {

        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;

        $member_id = $this->_getParam('member_id');
        $group_id = $this->_getParam('group_id');

        $table = Engine_Api::_()->getDbtable('membership', 'sitegroup');

        //MAKE FORM
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->form = $form = new Sitegroupmember_Form_Edit();
        } else {
            $this->view->form = $form = new Sitegroupmember_Form_SitemobileEdit();
        }

        $table = Engine_Api::_()->getDbtable('membership', 'sitegroup');
        $tablename = $table->info('name');
        $select = $table->select()
                ->from($table->info('name'), array('title', 'date', 'role_id'))
                ->where($tablename . '.member_id = ?', $member_id);
        $result = $table->fetchRow($select)->toArray();

        if (!$this->getRequest()->isPost()) {
            $form->populate(array(
                'role_id' => json_decode($result['role_id'])
                    //'title' => $result['title'],
            ));
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();

        //FOR DATE WORK.
        if (!empty($values['year']) || !empty($values['month']) || !empty($values['day'])) {
            $member_date = $values['year'] . '-' . (int) $values['month'] . '-' . (int) $values['day'];
        }

        //BEGIN TRANSACTION
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {

            if (isset($values['role_id'])) {
                $roleName = array();
                foreach ($values['role_id'] as $role_id) {
                    $roleName[] = Engine_Api::_()->getDbtable('roles', 'sitegroupmember')->getRoleName($role_id);
                }
                $roleTitle = json_encode($roleName);
                $roleIDs = json_encode($values['role_id']);
                $table->update(array('title' => $roleTitle, 'role_id' => $roleIDs), array('member_id =?' => $member_id));
            }

            if (!empty($member_date)) {
                $table->update(array('date' => $member_date), array('member_id =?' => $member_id));
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 2,
            'parentRedirect' => $this->_helper->url->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id), 'tab' => $this->_getParam('tab')), 'sitegroup_entry_view', true),
            'parentRedirectTime' => '2',
            'format' => 'smoothbox',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your membership information has been edited successfully.')),
        ));
    }

    //ACTIO FOR REQUEST MEMBER
    public function requestMemberAction() {

        $values = array();
        $group_id = $values['group_id'] = $this->_getParam('group_id');
        $this->view->tab_selected_id = $this->_getParam('tab');
        $this->view->paginator = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getSitegroupmembersPaginator($values, 'request');
    }

    //ACTION FOR JOIN GROUP.
    public function groupJoinAction() {

        $this->view->user_id = $user_id = $this->_getParam('user_id');

        //GET THE FRIEND ID AND OBJECT OF USER.
        $this->view->showViewMore = $this->_getParam('showViewMore', 0);
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinGroups($user_id, 'groupJoin');

        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($this->_getParam('group', 1));
        $this->view->count = $paginator->getTotalItemCount();
    }

    //ACTION FOR MEMBER JOIN THE GROUP.
    public function memberJoinAction() {

        $this->view->group_id = $group_id = $this->_getParam('group_id');
        $this->view->showViewMore = $this->_getParam('showViewMore', 0);
        $memberJoin = $this->_getParam('params', null);
        if ($memberJoin == 'memberJoin') {
            $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinMembers($group_id, '', '', 0);
        } else {
            $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinMembers($group_id);
        }

        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($this->_getParam('group', 1));
        $this->view->count = $paginator->getTotalItemCount();
    }

    //ACTION FOR EDIT TITLE OF GROUP MEMBER.
    public function edittitleAction() {

        $member_id = $this->_getParam('member_id');
        $group_id = $this->_getParam('group_id');
        $str_temp = $this->_getParam('str_temp');
        $table = Engine_Api::_()->getDbtable('membership', 'sitegroup');
        $table->update(array('title' => $str_temp), array('member_id =?' => $member_id));
        exit();
    }

    //USE FOR COMPOSE THE MESSAGE.
    public function composeAction() {

        $multi = 'member';
        $multi_ids = '';

        $tab_selected_id = $this->_getParam('tab');

        $this->view->resource_id = $resource_id = $this->_getParam("resource_id");
        
        $temproleParamsArray = $this->_getParam("roleParamsArray");
        
        parse_str($temproleParamsArray , $roleParamsArray);
        
        $this->view->roleParamsArray = $roleParamsArray;

        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->form = $form = new Sitegroupmember_Form_Compose();
        
        $form->roles_id->addMultiOptions($roleParamsArray);

        $form->removeElement('to');
        $form->setDescription('Create your new message with the form below.');

        $friends = Engine_Api::_()->user()->getViewer()->membership()->getMembers();
        $data = array();

        foreach ($friends as $friend) {
            $friend_photo = $this->view->itemPhoto($friend, 'thumb.icon');
            $data[] = array('label' => $friend->getTitle(), 'id' => $friend->getIdentity(), 'photo' => $friend_photo);
        }

        $data = Zend_Json::encode($data);
        $this->view->friends = $data;

        //ASSIGN THE COMPOSING STUFF.
        $composePartials = array();
        foreach (Zend_Registry::get('Engine_Manifest') as $data) {
            if (empty($data['composer']))
                continue;
            foreach ($data['composer'] as $type => $config) {
                $composePartials[] = $config['script'];
            }
        }
        $this->view->composePartials = $composePartials;

        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if ($values['coupon_mail'] == 1) {
            $members_ids = explode(",", $values['toValues']);
        } else {
            if (!empty($multi)) {
                $user_id = $viewer->getIdentity();

                $tableMember = Engine_Api::_()->getDbtable('membership', 'sitegroup');
                $tableMemberName = $tableMember->info('name');

                $userTable = Engine_Api::_()->getDbtable('users', 'user');
                $userTableName = $userTable->info('name');

                $select = $tableMember->select()
                        ->setIntegrityCheck(false)
                        ->from($tableMemberName, array('user_id'))
                        ->join($userTableName, $userTableName . '.user_id = ' . $tableMemberName . '.user_id')
                        ->where($tableMemberName . '.active = ?', 1)
                        ->where($tableMemberName . '.resource_approved = ?', 1)
                        ->where($tableMemberName . '.user_approved = ?', 1)
                        ->where($tableMemberName . '.user_id != ?', $user_id)
                        ->where($tableMemberName . '.group_id = ?', $resource_id);
                
                if($values['coupon_mail'] == 2) {
                      if (isset($values['roles_id']) && $values['roles_id']) {
                    			$select = $select->where($tableMemberName . '.role_id LIKE ?', '%' . $values['roles_id'] . '%');
                  }
                }
                
                $members_ids = $select->query()->fetchAll();
            }
        }

        if (!empty($members_ids)) {
            foreach ($members_ids as $member_id) {
                $multi_ids .= ',' . $member_id['user_id'];
            }

            $multi_ids = ltrim($multi_ids, ",");
            if ($multi_ids) {
                $this->view->multi = $multi;
                $this->view->multi_name = $viewer->getTitle();
                $this->view->multi_ids = $multi_ids;
                $form->toValues->setValue($multi_ids);
            }
        }

        //PROCESS.
        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();

        try {

            $attachment = null;
            $attachmentData = $this->getRequest()->getParam('attachment');
            if (!empty($attachmentData) && !empty($attachmentData['type'])) {
                $type = $attachmentData['type'];
                $config = null;
                foreach (Zend_Registry::get('Engine_Manifest') as $data) {
                    if (!empty($data['composer'][$type])) {
                        $config = $data['composer'][$type];
                    }
                }
                if ($config) {
                    $plugin = Engine_Api::_()->loadClass($config['plugin']);
                    $method = 'onAttach' . ucfirst($type);
                    $attachment = $plugin->$method($attachmentData);
                    $parent = $attachment->getParent();
                    if ($parent->getType() === 'user') {
                        $attachment->search = 0;
                        $attachment->save();
                    } else {
                        $parent->search = 0;
                        $parent->save();
                    }
                }
            }

            $viewer = Engine_Api::_()->user()->getViewer();

            $values = $form->getValues();
            $recipients = preg_split('/[,. ]+/', $values['toValues']);

            // limit recipients if it is not a special list of members
            if (empty($multi))
                $recipients = array_slice($recipients, 0, 10); // Slice down to 10
                
// clean the recipients for repeating ids
            // this can happen if recipient is selected and then a friend list is selected
            $recipients = array_unique($recipients);
            $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);

            $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $recipients, $values['title'], $values['body'], $attachment);
            if ($conversation->recipients > 1) {
                $conversation->locked = 1; //DO NOT ADD RESOURCE TYPE AND RESOURCE ID
                $conversation->save();
            }
            foreach ($recipientsUsers as $user) {
                if ($user->getIdentity() == $viewer->getIdentity()) {
                    continue;
                }
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $conversation, 'message_new');
            }

            //Increment messages counter
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
            $db->commit();

            return $this->_forwardCustom('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                         'parentRedirect' => $this->_helper->url->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($resource_id), 'tab' => $tab_selected_id), 'sitegroup_entry_view', true),
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR GET USER.
    public function getItemAction() {

        $member_name = $this->_getParam('sitegroup_members_search_input_text', null);

        $data = array();

        $UserTable = Engine_Api::_()->getDbtable('users', 'user');

        $select = $UserTable->select()
                ->setIntegrityCheck(false)
                ->from($UserTable->info('name'), array('user_id', 'displayname', 'photo_id'))
                ->where('username  LIKE ? ', '%' . $member_name . '%')
                ->order('displayname ASC');

        //FETCH RESULTS
        $members = $UserTable->fetchAll($select);

        foreach ($members as $member) {
            $member_photo = $this->view->itemPhoto($member, 'thumb.icon');
            $data[] = array(
                'id' => $member->user_id,
                'label' => $member->displayname,
                'photo' => $member_photo
            );
        }

        if (!empty($data)) {
            return $this->_helper->json($data);
        }
    }

    //ACTION FOR USER AUTO SUGGEST.
    public function getusersAction() {

        $data = array();

        //GET COUPON ID.
        $group_id = $this->_getParam('group_id', null);

        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $viewer->getIdentity();

        $tableMember = Engine_Api::_()->getDbtable('membership', 'sitegroup');
        $tableMemberName = $tableMember->info('name');

        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $userTable->info('name');

        $select = $tableMember->select()
                ->setIntegrityCheck(false)
                ->from($tableMemberName, array('user_id'))
                ->join($userTableName, $userTableName . '.user_id = ' . $tableMemberName . '.user_id')
                ->where($tableMemberName . '.active = ?', 1)
                ->where($tableMemberName . '.resource_approved = ?', 1)
                ->where($tableMemberName . '.user_approved = ?', 1)
                ->where($tableMemberName . '.user_id != ?', $user_id)
                ->where($tableMemberName . '.group_id = ?', $group_id);
        $select->where($userTableName . '.displayname  LIKE ? ', '%' . $this->_getParam('user_ids', null) . '%')
                ->order($userTableName . '.displayname ASC')->limit('40');

        $users = $tableMember->fetchAll($select);

        foreach ($users as $user) {
            $user_subject = Engine_Api::_()->user()->getUser($user->user_id);
            $user_photo = $this->view->itemPhoto($user_subject, 'thumb.icon');
            $data[] = array(
                'id' => $user->user_id,
                'label' => $user->displayname,
                'photo' => $user_photo
            );
        }

        return $this->_helper->json($data);
    }

    //ACTION FOR USER AUTO SUGGEST.
    public function getmembersAction() {

        $data = array();

        //GET COUPON ID.
        $group_id = $this->_getParam('group_id', null);

        $usersTable = Engine_Api::_()->getDbtable('users', 'user');
        $usersTableName = $usersTable->info('name');

        $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
        $membershipTableName = $membershipTable->info('name');

        $autoRequest = '';
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $autoRequest = $this->_getParam('user_ids', null);
        } else {
            $autoRequest = $this->_getParam('text', null);
        }

        $select = $membershipTable->select()
                ->from($membershipTableName, 'user_id')
                ->where('group_id = ?', $group_id);
        $user_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        $select = $usersTable->select()
                ->where('displayname  LIKE ? ', '%' . $autoRequest . '%')
                ->where($usersTableName . '.user_id NOT IN (?)', (array) $user_ids)
                ->order('displayname ASC')
                ->limit('40');
        $users = $usersTable->fetchAll($select);

        foreach ($users as $user) {
            $user_photo = $this->view->itemPhoto($user, 'thumb.icon', '', array('nolazy' => true));
            $data[] = array(
                'id' => $user->user_id,
                'label' => $user->displayname,
                'photo' => $user_photo
            );
        }

        //if( $groupJoinType == $groupPhraseNum ) {
        return $this->_helper->json($data);
//     }else {
//       return;
//     }
    }

}
