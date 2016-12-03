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
class Sitegroupmember_IndexController extends Siteapi_Controller_Action_Standard {

    /**
     * Auth checkup and creating the subject.
     * 
     */
    public function init() {
        $group_id = $this->_getParam('group_id');
        if (!empty($group_id)) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
            Engine_Api::_()->core()->setSubject($sitegroup);

            $allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate');
            if (empty($allowGroup)) {
                $this->respondWithError('unauthorized');
            }
        }
    }

    /*
     * Calling of member search form
     * 
     * @return array
     */

    public function searchFormAction() {
        $this->validateRequestMethod();

        if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
            $this->respondWithError('no_record');
        }

        $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

        if (!Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate')) {
            $this->respondWithError('no_record');
        }

        try {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Sitegroupmember')->getMemberSearchForm($sitegroup);
            $this->respondWithSuccess($response, true);
        } catch (Expection $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Calling of browse members
     * 
     * @return array
     */

    public function browseAction() {
        $this->validateRequestMethod();

        if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
            $this->respondWithError('no_record');
        }

        //Get Subject
        $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

        if (!Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate')) {
            $this->respondWithError('no_record');
        }

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET SEARCHING PARAMETERS
        $values = array();
        $group = $this->_getParam('group', 1);
        $search = $this->_getParam('search');
        $role_id = $this->_getParam('role_id', 0);
        $selectbox = $this->_getParam('visibility');
        $seeWaiting = $this->_getParam('waiting', 0);
        $page = $this->_getParam('page', 1);
        $limit = $this->_getParam('limit', 10);

        if (!empty($search)) {
            $values['search'] = $search;
        }

        if (!empty($selectbox)) {
            $values['orderby'] = $selectbox;
        }

        $values['group_id'] = $sitegroup->group_id;

        if (!empty($role_id)) {
            $values['roles_id'] = $role_id > 0 ? array($role_id) : array(0);
        }
        $menu = $this->getRequestParam('menu', true);

        $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');

        //TOTAL members
        $memberCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroup', 'membership');
        $request_count = $membershipTable->getSitegroupmembersPaginator($values, 'request');
        $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');

        if (empty($memberCount) && empty($can_edit)) {
            $this->respondWithError('no_record');
        }

        //MAKE PAGINATOR
        try {
            if (isset($seeWaiting) && !empty($seeWaiting)) {
                $paginator = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getSitegroupmembersPaginator($values, 'request');
            } else {
                $paginator = $membershipTable->getSitegroupmembersPaginator($values);
            }
            $paginator->setItemCountPerPage($limit)->setCurrentPageNumber($page);

            foreach ($paginator as $member) {
                if (!empty($member))
                    $membersArray[] = $this->_getMemberInfo(array(
                        "member" => $member,
                        "group" => $sitegroup,
                        "menu" => $menu
                    ));

                $bodyParams['members'] = $membersArray;
                $bodyParams['getWaitingItemCount'] = $request_count->getTotalItemCount();
                $getTotalItemCount = $paginator->getTotalItemCount();
            }
            $bodyParams['getTotalItemCount'] = !empty($getTotalItemCount) ? $getTotalItemCount : 0;

            if (($level_id == 1 || $sitegroup->owner_id == $viewer_id)) {
                $bodyParams['messageGuest'] = array(
                    'label' => $this->translate('Message Guest'),
                    'name' => 'messageGuest',
                    'url' => 'advancedgroups/members/compose/' . $sitegroup->getIdentity(),
                );
            }

            $this->respondWithSuccess($bodyParams, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /*
     * Calling of remove member
     * 
     */

    public function removeAction() {
        $this->validateRequestMethod('DELETE');

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $member_id = $this->_getParam('member_id');
        $sitegroupmember = Engine_Api::_()->getDbTable('membership', 'sitegroup')->getMembersObject($member_id);
        $params = $this->_getParam('params', null);

        if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
            $this->respondWithError('no_record');
        }

        //Get Subject
        $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
        $group_id = $sitegroup->group_id;

        // Authorization checks
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        $canEdit = 0;
        if (!empty($isManageAdmin)) {
            $canEdit = 1;
        }
        try {
            if ($viewer_id == $sitegroupmember->user_id || !empty($canEdit)) {
                if (!empty($member_id)) {
                    Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('member_id =?' => $member_id, 'group_id =?' => $group_id));
                    Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);

                    $action_id = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type = ?' => 'sitegroup_join', 'subject_id = ?' => $viewer_id, 'object_id = ?' => $group_id));
                    if ($action_id) {
                        $action = Engine_Api::_()->getItem('activity_action', $action_id->action_id);
                        $action->delete();
                    }
                }
            } else {
                $this->respondWithError('unauthorized');
            }
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Calling of approve member
     * 
     */

    public function approveAction() {
        $this->validateRequestMethod('POST');

        //Get viewer info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // User validation
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        //Get member id & object
        $member_id = $this->_getParam('member_id');
        $sitegroupmember = Engine_Api::_()->getDbTable('membership', 'sitegroup')->getMembersObject($this->_getParam('member_id'));
        $active = $sitegroupmember->active;
        $user_approved = $sitegroupmember->user_approved;

        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $sitegroupmember->group_id);

        // Auth checks
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        $canEdit = 0;
        if (!empty($isManageAdmin)) {
            $canEdit = 1;
        }

        if ($viewer_id == $sitegroupmember->user_id || !empty($canEdit)) {
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

                $grouptitle = $sitegroup->title;
                $group_url = Engine_Api::_()->sitegroup()->getGroupUrl($sitegroup->group_id);
                $group_baseurl = 'http://' . $_SERVER['HTTP_HOST'] .
                        Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true);
                $group_title_link = '<a href="' . $group_baseurl . '"  >' . $grouptitle . ' </a>';

                Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'SITEGROUPMEMBER_APPROVE_EMAIL', array(
                    'group_title' => $grouptitle,
                    'group_title_with_link' => $group_title_link,
                    'object_link' => $group_baseurl,
                    'email' => $email,
                    'queue' => true
                ));

                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $sitegroup, 'sitegroupmember_accepted');

                // Set the request as handled
                $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                        $viewer, $sitegroup, 'sitegroupmember_approve');
                if ($notification) {
                    $notification->mitigated = true;
                    $notification->read = true;
                    $notification->save();
                }

                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $action = $activityApi->addActivity($user, $sitegroup, 'sitegroup_join');
                Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action, true);
                //Member count increase when member join the group.
                Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
                $sitegroup->save();

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
        } else {
            $this->respondWithError('unauthorized');
        }
    }

    /*
     * Calling of reject member
     * 
     */

    public function rejectAction() {
        $this->validateRequestMethod('POST');

        //GET THE GROUP ID AND MEMBER ID AND USER ID
        $group_id = $this->_getParam('group_id');
        $member_id = $this->_getParam('member_id');
        $user_id = $this->_getParam('user_id');

        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        // Auth checks
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');
        try {

            //Set the request as handled
            Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_type =?' => 'sitegroup_group', 'object_id =?' => $group_id, 'subject_id =?' => $user_id));

            if (!empty($group_id)) {
                //DELETE THE RESULT FORM THE TABLE.
                Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('group_id =?' => $group_id, 'member_id =?' => $member_id));

                //Member count decrease when member join the group.
                $sitegroup->member_count--;
                $sitegroup->save();
            }
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Calling of compose message for members
     * 
     */

    public function composeAction() {
        $multi = 'member';
        $multi_ids = '';

        $resource_id = $group_id = $this->_getParam("group_id");
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        $viewer = Engine_Api::_()->user()->getViewer();

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $form = Engine_Api::_()->getApi('Siteapi_Core', 'sitegroupmember')->getMessageComposeForm($event);

        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess($form, true);
        } else if ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();
            try {
                $friends = Engine_Api::_()->user()->getViewer()->membership()->getMembers();
                $data = array();

                foreach ($friends as $friend) {
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($friend, true);
                    $friend_photo = $getContentImages['owner_image_icon'];
                    $data[] = array('label' => $friend->getTitle(), 'id' => $friend->getIdentity(), 'photo' => $friend_photo);
                }

                $data = Zend_Json::encode($data);

                //ASSIGN THE COMPOSING STUFF.
                $composePartials = array();
                foreach (Zend_Registry::get('Engine_Manifest') as $data) {
                    if (empty($data['composer']))
                        continue;
                    foreach ($data['composer'] as $type => $config) {
                        $composePartials[] = $config['script'];
                    }
                }

                if ($values['coupon_mail'] == 1) {
                    $ids = explode(",", $values['toValues']);
                    foreach ($ids as $id) {
                        $members_ids[] = Engine_Api::_()->getItem('user', $id);
                    }
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
                        $members_ids = $select->query()->fetchAll();
                    }
                }
                if (!empty($members_ids)) {
                    foreach ($members_ids as $member_id) {
                        $multi_ids .= ',' . $member_id['user_id'];
                    }

                    $multi_ids = ltrim($multi_ids, ",");
                    if ($multi_ids) {
                        $multi = $multi;
                        $multi_name = $viewer->getTitle();
                        $multi_ids = $multi_ids;
                    }
                }

                $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
                $db->beginTransaction();
                $values['toValues'] = $multi_ids;

                $viewer = Engine_Api::_()->user()->getViewer();
                $recipients = preg_split('/[,. ]+/', $values['toValues']);

                // limit recipients if it is not a special list of members
                if (empty($multi))
                    $recipients = array_slice($recipients, 0, 10);

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
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $ex) {
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
        }
    }

    /*
     * Calling of users autosuggest
     * 
     */

    public function getusersAction() {
        $this->validateRequestMethod();
        $data = array();

        //GET COUPON ID.
        $group_id = $this->_getParam('group_id', null);

        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $viewer->getIdentity();

        $tableMember = Engine_Api::_()->getDbtable('membership', 'sitegroup');
        $tableMemberName = $tableMember->info('name');

        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $userTable->info('name');
        try {

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
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($user_subject);
                $data = array(
                    'id' => $user->user_id,
                    'label' => $user->displayname,
                );
                $data = array_merge($data, $getContentImages);
                $tempresponse[] = $data;
            }
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }

        $this->respondWithSuccess($tempresponse);
    }

    /*
     * Calling of get users autosuggest
     * 
     */

    public function getmembersAction() {
        $this->validateRequestMethod();
        $data = array();
        //GET COUPON ID.
        $group_id = $this->_getParam('group_id', null);

        $usersTable = Engine_Api::_()->getDbtable('users', 'user');
        $usersTableName = $usersTable->info('name');

        $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
        $membershipTableName = $membershipTable->info('name');

        $autoRequest = $this->_getParam('text', null);

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
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($user);
            $data = array(
                'id' => $user->user_id,
                'label' => $user->displayname,
            );
            $data = array_merge($data, $getContentImages);
            $tempresponse[] = $data;
        }

        $this->respondWithSuccess($data);
    }

    private function _getMemberInfo($params) {
        $staff = '';
        $member = $params["member"];
        $sitegroup = $params["group"];
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $memberArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($member);
        $memberInfo = $sitegroup->membership()->getMemberInfo($member);
        $isGroupAdmin = $sitegroup->isGroupAdmin($member->user_id);

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($member);
        $memberArray = array_merge($memberArray, $getContentImages);
        $memberArray['is_owner'] = ($sitegroup->isOwner($member)) ? 1 : 0;

        if (isset($params["menu"]) && !empty($params["menu"])) {
            if ($sitegroup->owner_id == $viewer_id) {
                if ($sitegroup->owner_id != $member->user_id && $memberInfo->active == true) {
                    $menus[] = array(
                        'label' => $this->translate('Remove Member'),
                        'name' => 'remove_member',
                        'url' => 'advancedgroups/members/remove/' . $sitegroup->getIdentity(),
                        'urlParams' => array(
                            "member_id" => $member->member_id
                        )
                    );
                }

                if ($memberInfo->active == false && $memberInfo->resource_approved == false) {
                    $menus[] = array(
                        'label' => $this->translate('Approve Request'),
                        'name' => 'approved_member',
                        'url' => 'advancedgroups/members/approve/' . $sitegroup->getIdentity(),
                        'urlParams' => array(
                            "member_id" => $member->member_id
                        )
                    );

                    $menus[] = array(
                        'label' => $this->translate('Reject Request'),
                        'name' => 'reject_member',
                        'url' => 'advancedgroups/members/reject/' . $sitegroup->getIdentity(),
                        'urlParams' => array(
                            "member_id" => $member->member_id
                        )
                    );
                }
            }
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');

            $manageAdminAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1);
            if ($sitegroup->owner_id != $member->user_id && $memberInfo->active == true) {
                if (!empty($isManageAdmin) && !empty($manageAdminAllowed)) {
                    if (empty($isGroupAdmin)) {
                        $menus[] = array(
                            'label' => $this->translate('Make Group Admin'),
                            'name' => 'make_admin',
                            'url' => 'advancedgroups/member/makeadmin/' . $sitegroup->getIdentity(),
                            'urlParams' => array(
                                "user_id" => $member->user_id
                            )
                        );
                    } else if ($sitegroup->owner_id != $member->user_id) {
                        $menus[] = array(
                            'label' => $this->translate('Remove Group Admin'),
                            'name' => 'remove_admin',
                            'url' => 'advancedgroups/member/removeadmin/' . $sitegroup->getIdentity(),
                            'urlParams' => array(
                                "user_id" => $member->user_id
                            )
                        );
                    }
                }
            }
            $memberArray['menu'] = $menus;
        }

        return $memberArray;
    }

}
