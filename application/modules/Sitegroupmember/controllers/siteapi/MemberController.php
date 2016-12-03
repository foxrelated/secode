<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MemberController.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupmember_MemberController extends Siteapi_Controller_Action_Standard {
    /*
     * Calling of join group
     * 
     */

    public function joinAction() {
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $group_id = $this->_getParam('group_id');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        $owner = $sitegroup->getOwner();

        //MAKE FORM
        $form = Engine_Api::_()->getApi('Siteapi_Core', 'Sitegroupmember')->getMemberJoinForm($sitegroup);

        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess($form, true);
        } else if ($this->getRequest()->isPost()) {
            $action_notification = array();
            $notificationSettings = Engine_Api::_()->getDbTable('membership', 'sitegroup')->notificationSettings(array('user_id' => $sitegroup->owner_id, 'group_id' => $group_id, 'columnName' => array('action_notification')));
            if ($notificationSettings)
                $action_notification = Zend_Json_Decoder::decode($notificationSettings);

            $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $group_id);

            if (!empty($hasMembers)) {
                $this->respondWithError('unauthorized');
            }
            try {

                // Set Notifications
                $friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
                if ($action_notification && $action_notification['notificationjoin'] == 1) {
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $sitegroup, 'sitegroup_join');
                } elseif ($action_notification && in_array($sitegroup->owner_id, $friendId) && $action_notification['notificationjoin'] == 2) {
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $sitegroup, 'sitegroup_join');
                }

                // Attach Activity
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitegroup, 'sitegroup_join');
                if ($action) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sitegroup);
                }
                Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action, true);

                //GET VALUE FROM THE FORM.
                $values = $this->getRequest()->getPost();
                $membersTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
                $row = $membersTable->createRow();
                $row->resource_id = $group_id;
                $row->group_id = $group_id;
                $row->user_id = $viewer_id;

                // Set Role Id
                if (isset($values['role_id'])) {
                    $roleName = array();
                    foreach ($values['role_id'] as $role_id) {
                        $roleName[] = Engine_Api::_()->getDbtable('roles', 'sitegroupmember')->getRoleName($role_id);
                    }
                    $roleTitle = json_encode($roleName);
                    $roleIDs = json_encode($values['role_id']);
                    if ($roleTitle && $roleIDs) {
                        $row->title = $roleTitle;
                        $row->role_id = $roleIDs;
                    }
                }

                // Join Date work
                if (!empty($values['year']) || !empty($values['month']) || !empty($values['day'])) {
                    $member_date = $values['year'] . '-' . (int) $values['month'] . '-' . (int) $values['day'];
                    $row->date = $member_date;
                }

                $sitegroupmember = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id);
                if (!empty($sitegroupmember->featured) && $sitegroupmember->featured == 1) {
                    $row->featured = 1;
                }

                $row->save();

                //Set increase count
                Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
                $sitegroup->save();

                //Set like group.
                $autoLike = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.automatically.like', 0);
                if (!empty($autoLike)) {
                    Engine_Api::_()->sitegroup()->autoLike($group_id, 'sitegroup_group');
                }

                // Start Discussion work
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
                    $results = Engine_Api::_()->getDbTable('topics', 'sitegroup')->getGroupTopics($group_id);
                    if (!empty($results)) {
                        foreach ($results as $result) {

                            $topic_id = $result->topic_id;

                            $db = Engine_Db_Table::getDefaultAdapter();

                            $db->query("INSERT IGNORE INTO `engine4_sitegroup_topicwatches` (`resource_id`, `topic_id`, `user_id`, `watch`, `group_id`) VALUES ('$group_id', '$topic_id', '$viewer_id', '1', '$group_id');");
                        }
                    }
                }
                // End discussion
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $ex) {
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
        }
    }

    /*
     * Calling of leave group
     * 
     */

    public function leaveAction() {
        $this->validateRequestMethod('POST');

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET GROUP ID.
        $group_id = $this->_getParam('group_id');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        try {

            if (!empty($group_id)) {

                // Delete entry from table
                Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('resource_id =?' => $group_id, 'user_id = ?' => $viewer_id));

                //Delete activity feed
                $action_id = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type = ?' => 'sitegroup_join', 'subject_id = ?' => $viewer_id, 'object_id = ?' => $group_id));
                $action = Engine_Api::_()->getItem('activity_action', $action_id->action_id);
                if (!empty($action)) {
                    $action->delete();
                }

                //Remove Notification
                $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($sitegroup->getOwner(), $sitegroup, 'sitegroup_join');
                if ($notification) {
                    $notification->delete();
                }

                //Set decrease count
                $sitegroup->member_count--;
                $sitegroup->save();
                $this->successResponseNoContent('no_content', true);
            }
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Calling of request to join group
     * 
     */

    public function requestAction() {
        $this->validateRequestMethod('POST');

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $group_id = $this->_getParam('group_id');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        $grouptitle = $sitegroup->title;
        $group_url = Engine_Api::_()->sitegroup()->getGroupUrl($group_id);

        $group_baseurl = ((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true);
        $group_title_link = '<a href="' . $group_baseurl . '"  >' . $grouptitle . ' </a>';

        $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $group_id);

        //IF MEMBER IS ALREADY PART OF THE GROUP
        if (!empty($hasMembers)) {
            $this->respondWithError('unauthorized');
        }
        try {

            $sitegroupmember = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id);

            $manageadmins = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdmin($group_id);

            $values = $this->getRequest()->getPost();

            $membersTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');

            $row = $membersTable->createRow();
            $row->resource_id = $group_id;
            $row->group_id = $group_id;
            $row->user_id = $viewer_id;
            $row->active = 0;
            $row->resource_approved = 0;
            $row->user_approved = 0;

            if (!empty($sitegroupmember->featured) && $sitegroupmember->featured == 1) {
                $row->featured = 1;
            }

            $row->save();

            foreach ($manageadmins as $manageadmin) {

                $user_subject = Engine_Api::_()->user()->getUser($manageadmin['user_id']);

                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitegroup, 'sitegroupmember_approve', array('member_id' => $row->member_id));

                //Email to all group admins.
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, 'SITEGROUPMEMBER_REQUEST_EMAIL', array(
                    'group_title' => $grouptitle,
                    'group_title_with_link' => $group_title_link,
                    'object_link' => $group_baseurl,
                    'queue' => true
                ));
            }
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Calling of cancel membership request sent
     * 
     */

    public function cancelAction() {
        $this->validateRequestMethod('POST');

        //Check Auth
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $group_id = $this->_getParam('group_id');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        $viewer = Engine_Api::_()->user()->getViewer();

        try {

            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($sitegroup->getOwner(), $sitegroup, 'sitegroupmember_approve');
            if ($notification) {
                $notification->delete();
            }

            if (!empty($group_id)) {
                Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('resource_id =?' => $group_id, 'user_id =?' => $viewer->getIdentity()));
            }

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Calling of respond
     * 
     */

    public function respondAction() {
        $this->validateRequestMethod('POST');

        //Check auth
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();

        $group_id = $this->_getParam('group_id');
        $param = $this->_getParam('param');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        //Get all values
        $values = $this->_getAllParams();
        try {
            if (isset($values['accept'])) {

                if (!empty($sitegroup->member_approval)) {
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitegroup, 'sitegroup_join');
                    if ($action) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sitegroup);
                    }
                    Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action, true);
                    Engine_Api::_()->getDbtable('membership', 'sitegroup')->update(array('active' => '1', 'user_approved' => '1'), array('resource_id =?' => $group_id, 'user_id =?' => $viewer->getIdentity()));

                    //Set Increase count
                    Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
                    $sitegroup->save();
                } else {
                    Engine_Api::_()->getDbtable('membership', 'sitegroup')->update(array('active' => '0', 'user_approved' => '0', 'resource_approved' => '0'), array('resource_id =?' => $group_id, 'user_id =?' => $viewer->getIdentity()));

                    $manageadmins = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdmin($group_id);
                    foreach ($manageadmins as $manageadmin) {

                        $user_subject = Engine_Api::_()->user()->getUser($manageadmin['user_id']);
                        $row = Engine_Api::_()->getDbTable('membership', 'sitegroup')->getRow($sitegroup, $viewer);
                        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitegroup, 'sitegroupmember_approve', array('member_id' => $row->member_id));
                    }
                }
            } else {
                if (!empty($group_id)) {
                    Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('resource_id =?' => $group_id, 'user_id =?' => $viewer->getIdentity()));
                }
            }
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($viewer, $sitegroup, 'sitegroupmember_invite');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = true;
                $notification->save();
            }

            if (isset($values['accept']) && !empty($automaticallyJoin) && !empty($sitegroup->member_approval)) {
                $message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the group %s.');
            } elseif (isset($values['accept']) && empty($automaticallyJoin) && empty($sitegroup->member_approval)) {
                $message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invitation to join the group %s.');
            } elseif (isset($values['accept']) && empty($automaticallyJoin) && !empty($sitegroup->member_approval)) {
                $message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invitation to join the group %s.');
            } else {
                $message = Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the group %s.');
            }

            $message = sprintf($message, $sitegroup->__toString());
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Calling of accept invite
     * 
     */

    public function acceptAction() {
        $this->validateRequestMethod('POST');

        //Auth check
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();

        $values = $this->_getAllParams();
        $group_id = $this->_getParam('group_id');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        try {
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($viewer, $sitegroup, 'sitegroupmember_invite');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = true;
                $notification->save();
            }

            //Attach Activity
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitegroup, 'sitegroup_join');
            Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action, true);

            Engine_Api::_()->getDbtable('membership', 'sitegroup')->update(array('active' => '1', 'user_approved' => '1'), array('resource_id =?' => $group_id, 'user_id =?' => $viewer->getIdentity()));

            Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
            $sitegroup->save();

            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $viewer, $sitegroup, 'sitegroupmember_approve');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = true;
                $notification->save();
            }

            $manageadmins = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdmin($sitegroup->group_id);
            foreach ($manageadmins as $manageadmin) {
                $user_subject = Engine_Api::_()->user()->getUser($manageadmin['user_id']);
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitegroup, 'sitegroup_join');
            }

            $message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the group %s');
            $message = sprintf($message, $sitegroup->__toString());
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Calling of reject invite
     * 
     */

    public function rejectAction() {
        $this->validateRequestMethod('POST');

        //Check Auth
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();

        $group_id = $this->_getParam('group_id');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        try {
            if (!empty($group_id)) {

                //Delete from table
                Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('resource_id =?' => $group_id, 'user_id =?' => $viewer->getIdentity()));
            }

            $message = Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the group %s');
            $message = sprintf($message, $sitegroup->__toString());

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($viewer, $sitegroup, 'sitegroupmember_approve');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = true;
                $notification->save();
            }

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($viewer, $sitegroup, 'sitegroupmember_invite');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = true;
                $notification->save();
            }
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Calling of invite members
     * 
     */

    public function inviteMembersAction() {
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $group_id = $this->_getParam('group_id');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $viewer = Engine_Api::_()->user()->getViewer();

        $isGroupAdmin = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->isGroupAdmins($viewer->getIdentity(), $sitegroup->getIdentity());

        $automaticallyJoin = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.automatically.addmember', 1);
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Sitegroupmember')->getMemberInviteForm();
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();

            if (empty($values['user_ids'])) {
                $errorMessage[] = $this->translate("Please complete this field - It is requried.");
            }

            $members_ids = explode(",", $values['user_ids']);
            try {

                $membersTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');

                if (!empty($members_ids)) {

                    foreach ($members_ids as $members_id) {

                        $row = $membersTable->createRow();
                        $row->resource_id = $group_id;
                        $row->group_id = $group_id;
                        $row->user_id = $members_id;
                        $row->resource_approved = 1;

                        if (!empty($automaticallyJoin) && !empty($sitegroup->member_approval) && empty($isGroupAdmin)) {
                            $row->active = 1;
                            $row->user_approved = 1;
                            $row->save();

                            //MEMBER COUNT INCREASE WHEN MEMBER JOIN THE GROUP.
                            Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
                            $sitegroup->save();
                        } elseif (!empty($automaticallyJoin) && !empty($isGroupAdmin)) {
                            $row->active = 1;
                            $row->user_approved = 1;
                            $row->save();
                            //MEMBER COUNT INCREASE WHEN MEMBER JOIN THE GROUP.
                            Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
                            $sitegroup->save();
                        } else {
                            $row->active = 0;
                            $row->user_approved = 0;
                        }

                        $row->save();

                        if (empty($automaticallyJoin)) {
                            $user_subject = Engine_Api::_()->user()->getUser($members_id);
                            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitegroup, 'sitegroupmember_invite');
                        } else {
                            $user_subject = Engine_Api::_()->user()->getUser($members_id);
                            //SET THE REQUEST AS HANDLED FOR NOTIFACTION.
                            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitegroup, 'sitegroup_addmember');

                            //ADD ACTIVITY
                            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user_subject, $sitegroup, 'sitegroup_join');
                            if ($action) {
                                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sitegroup);
                            }
                            Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action, true);
                        }
                    }
                }
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $ex) {
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
        }
    }

    /*
     * Calling of remove member admin
     * 
     */

    public function removeadminAction() {
        $this->validateRequestMethod('DELETE');

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //GET GROUP ID.
        $group_id = $this->_getParam('group_id');
        $owner_id = $this->_getParam('user_id');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        try {

            if (!empty($group_id)) {
                Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->delete(array('user_id =?' => $this->_getParam('user_id'), 'group_id =?' => $group_id));

                //Start Sitegroup Integration work
                $sitegroupintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration');
                if (!empty($sitegroupintegrationEnabled)) {
                    $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitegroupintegration');
                    $contentsTable->delete(array('resource_owner_id = ?' => $owner_id, 'group_id = ?' => $group_id));
                }
                // End sitegroup Integration work
                $this->successResponseNoContent('no_content', true);
            }
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Calling of make member admin
     * 
     */

    public function makeadminAction() {
        $this->validateRequestMethod('POST');

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $group_id = $this->_getParam('group_id', null);
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        $owner_id = $sitegroup->owner_id;

        //EDIT PRIVACY
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');

        $manageAdminAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1);
        if (empty($isManageAdmin) || empty($manageAdminAllowed)) {
            $this->respondWithError('unauthorized');
        }

        $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');

        //FETCH DATA
        $manageHistories = $manageadminsTable->getManageAdminUser($group_id);

        $values = $this->_getAllParams();
        $selected_user_id = $values['user_id'];

        if (empty($selected_user_id))
            $this->respondWithError('unauthorized');

        try {
            $row = $manageadminsTable->createRow();
            $row->user_id = $selected_user_id;
            $row->group_id = $group_id;
            $row->save();

            //START SITEGROUPMEMBER PLUGIN WORK
            $sitegroupMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');

            if ($sitegroupMemberEnabled) {
                $membersTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
                $membersTableName = $membersTable->info('name');

                $select = $membersTable->select()
                        ->from($membersTableName)
                        ->where('user_id = ?', $selected_user_id)
                        ->where($membersTableName . '.resource_id = ?', $group_id);
                $select = $membersTable->fetchRow($select);

                if (empty($select)) {
                    $row = $membersTable->createRow();
                    $row->resource_id = $group_id;
                    $row->group_id = $group_id;
                    $row->user_id = $selected_user_id;
                    $row->save();
                }
            }

            //END SITEGROUPMEMBER PLUGIN WORK
            $newManageAdmin = Engine_Api::_()->getItem('user', $selected_user_id);
            $sitegroup_title = $sitegroup->title;
            $group_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id)), 'sitegroup_entry_view') . ">$sitegroup_title</a>";

            $host = $_SERVER['HTTP_HOST'];
            $group_url = $host . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id)), 'sitegroup_entry_view');

            Engine_Api::_()->getApi('mail', 'core')->sendSystem($newManageAdmin->email, 'SITEGROUP_MANAGEADMIN_EMAIL', array(
                'group_title_with_link' => $group_title_with_link,
                'sender' => $viewer->toString(),
                'group_url' => $group_url,
                'queue' => true
            ));

            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notifyApi->addNotification($newManageAdmin, $viewer, $sitegroup, 'sitegroup_manageadmin');

            //INCREMENT MESSAGE COUNTER.
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

}
