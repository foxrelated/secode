<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MemberController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_MemberController extends Siteapi_Controller_Action_Standard {

    //COMMON FUNCTION TO BE CALLED BEFORE EVERY FUNCTION
    public function init() {
        if (0 !== ($event_id = (int) $this->getRequestParam('event_id')) &&
                null !== ($siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id))) {
            Engine_Api::_()->core()->setSubject($siteevent);
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        } else {
            $this->_forward('throw-error', 'member', 'event', array(
                "error_code" => "parameter_missing",
                "message" => "event_id"
            ));
            return;
        }

        if (!Engine_Api::_()->core()->hasSubject() && !$this->getRequestParam('event_id')) {
            $this->_forward('throw-error', 'member', 'event', array(
                "error_code" => "no_record"
            ));
            return;
        }
    }

    /**
     * Throw the init constructor errors.
     *
     * @return array
     */
    public function throwErrorAction() {
        $message = $this->getRequestParam("message", null);
        if (($error_code = $this->getRequestParam("error_code")) && !empty($error_code)) {
            if (!empty($message))
                $this->respondWithValidationError($error_code, $message);
            else
                $this->respondWithError($error_code);
        }

        return;
    }

    /**
     * Return the search Form of Event Members.
     * 
     * @return array
     */
    public function searchFormAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->core()->hasSubject())
            $this->respondWithError('no_record');

        $siteevent = Engine_Api::_()->core()->getSubject();

        //GET USER LEVEL ID
        if ($viewer->getIdentity()) {
            $level_id = $viewer->level_id;
            $viewer_id = $viewer->getIdentity();
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')) {
            $hasEventMember = $siteevent->membership()->hasEventMember($viewer, true);

            $datesInfo = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($siteevent->event_id);

            $multiOptions['all'] = $this->translate('All occurrences of this event');
            foreach ($datesInfo as $key => $date) {
                $multiOptions[$date['occurrence_id']] = $date['starttime'] . " - " . $date['endtime'];
            }

            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'occurrence_id',
                'Label' => $this->translate('Filter Dates'),
                'multiOptions' => $multiOptions,
                'value' => 'all'
            );
        }
        $multiOptions = array();
        $multiOptions['-1'] = $this->translate('All');
        $multiOptions['2'] = $this->translate('Attending');
        $multiOptions['1'] = $this->translate('Maybe');
        $multiOptions['0'] = $this->translate('Not Attending');

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'rsvp',
            'Label' => $this->translate('RSVP'),
            'multiOptions' => $multiOptions,
            'value' => '-1'
        );

        $this->respondWithSuccess($searchForm, true);
    }

    /**
     * Return the list of Event Members.
     * 
     * @return array
     */
    public function listAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->core()->hasSubject())
            $this->respondWithError('no_record');

        $siteevent = Engine_Api::_()->core()->getSubject();

        //GET USER LEVEL ID
        if ($viewer->getIdentity()) {
            $level_id = $viewer->level_id;
            $viewer_id = $viewer->getIdentity();
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        // SET PARAMS
        $bodyParams = array();
        $page = $this->getRequestParam('page', 1);
        $limit = $this->getRequestParam('limit', 20);
        $search = $this->getRequestParam('search');
        $waiting = $this->getRequestParam('waiting', 0);
        $menu = $this->getRequestParam('menu', true);
        $rsvp = $this->getRequestParam('rsvp', -1);
        $friendsonly = $this->getRequestParam('friendsonly', 0);
        $occurrence_id = $this->getRequestParam('occurrence_id', 'all');
        //SAVE THE OCCURRENCE ID IN THE.
        if (empty($occurrence_id) || !is_numeric($occurrence_id)) {
            //GET THE NEXT UPCOMING OCCURRENCE ID
            $occurr_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($this->_getParam('event_id'));
        }

        $membershipTable = Engine_Api::_()->getDbTable("membership", "siteevent");
        $membershipTableName = $membershipTable->info('name');
        //  GET WAITING MEMBER OBJECT COUNT.
        if ($viewer->getIdentity() && ($siteevent->isOwner($viewer))) {
            $waitselect = $siteevent->membership()->getMembersSelect(false);
            if (!empty($occurr_id) && $occurr_id != 'all')
                $waitselect->where("$membershipTableName.occurrence_id=?", $occurr_id);
            $waitingMembers = Zend_Paginator::factory($waitselect);
            $getWaitingMemberCount = $waitingMembers->getTotalItemCount();
        }
        $select = $siteevent->membership()->getMembersObjectSelect();

        if (isset($rsvp) && $rsvp >= 0) {

            $select->where("rsvp=?", $rsvp);
        }
        if ($search)
            $select->where('displayname LIKE ?', '%' . $search . '%');

        if (!empty($occurrence_id) && $occurrence_id != 'all')
            $select->where("occurrence_id=?", $occurrence_id);
        else
            $occurrence_id = '';
        //IF REQUEST IS ONLY TO SHOW VIEWER FRIENDS THEN ALSO PUT THE JOIN WITH USER MEMBERSHIP TABLE.
        if ($friendsonly) {
            $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
            $membershipEventTableName = 'engine4_siteevent_membership';
            $membershipTableName = $membershipTable->info('name');
            $select->join($membershipTableName, "$membershipTableName.resource_id = $membershipEventTableName.user_id", null)
                    ->where($membershipTableName . '.user_id = ?', $viewer->getIdentity())
                    ->where($membershipTableName . '.active = ?', 1)
                    ->where('engine4_users.verified = ?', 1)
                    ->where('engine4_users.enabled = ?', 1);
        }


        $select->group('engine4_users.user_id');

        $members = Zend_Paginator::factory($select);

        try {
            //  RETURN THE WAITING MEMBERS AS RESPONSE.
            if (($viewer->getIdentity() && $siteevent->isOwner($viewer)) && ($waiting)) {
                foreach ($waitingMembers as $value) {
                    $member = Engine_Api::_()->getItem('user', $value->user_id);
                    if (!empty($member) && !empty($member->user_id))
                        $membersArray[] = $this->_getMemberInfo(array(
                            "member" => $member,
                            "event" => $siteevent,
                            "menu" => $menu
                        ));
                }

                $bodyParams['members'] = $membersArray;
                $bodyParams['waiting'] = $waiting = true;
            } else {
                //  RETURN THE FULL MEMBERS AS RESPONSE.      
                $eventOwner = $siteevent->getOwner();
                foreach ($members as $member) {
                    if (!$member->user_id)
                        continue;

                    if (!empty($member))
                        $membersArray[] = $this->_getMemberInfo(array(
                            "member" => $member,
                            "event" => $siteevent,
                            "menu" => $menu
                        ));


                    $bodyParams['members'] = $membersArray;
                    $bodyParams['waiting'] = $waiting = false;
                    $getTotalItemCount = $members->getTotalItemCount();
                }
            }

            $isAllowedCreate = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, 'create');
            $isAllowedEdit = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, 'edit');
            $bodyParams['getWaitingItemCount'] = $getWaitingMemberCount;
            $bodyParams['getTotalItemCount'] = !empty($getTotalItemCount) ? $getTotalItemCount : 0;
            $bodyParams['canEdit'] = $canEdit = !empty($isAllowedEdit) ? $isAllowedEdit : 0;
            $bodyParams['canCreate'] = !empty($isAllowedCreate) ? $isAllowedCreate : 0;
//@todo paid extension
            if (!Engine_Api::_()->siteevent()->isTicketBasedEvent() && ($level_id == 1 || $siteevent->isOwner($viewer) || $canEdit)) {
                if (($level_id == 1 || $siteevent->isOwner($viewer) || $canEdit)) {
                    $bodyParams['messageGuest'] = array(
                        'label' => $this->translate('Message Guest'),
                        'name' => 'messageGuest',
                        'url' => 'advancedevents/member/compose/' . $siteevent->getIdentity(),
                    );
                }
            }

            if ($siteevent->authorization()->isAllowed($viewer, 'invite')) {

                $occure_id = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurrence($siteevent->event_id);

                //CHECK IF THE EVENT IS PAST EVENT THEN ALSO DO NOT SHOW THE INVITE AND PROMOTE LINK
                $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($siteevent->event_id, 'DESC', $occure_id);

                // $currentDate = $this->locale()->toEventDateTime(time());
                if (strtotime($endDate) > time()) {
                    $bodyParams['inviteGuest'] = array(
                        'name' => 'invite',
                        'label' => $this->translate('Invite Guests'),
                        'url' => 'advancedevents/member/invite/' . $siteevent->getIdentity() . '/' . $occure_id,
                    );
                }
            }

            $this->respondWithSuccess($bodyParams, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    private function _getMemberInfo($params) {
        $staff = '';
        $member = $params["member"];
        $event = $params["event"];
        $viewer = Engine_Api::_()->user()->getViewer();
        $memberArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($member);
        $memberInfo = $event->membership()->getMemberInfo($member);

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($member);
        $memberArray = array_merge($memberArray, $getContentImages);
        $memberArray['is_owner'] = ($event->isOwner($member)) ? 1 : 0;
        if (isset($memberInfo->rsvp)) {
            $memberArray['rsvp'] = $memberInfo->rsvp;
        }

        if (isset($params["menu"]) && !empty($params["menu"])) {
            if ($event->isOwner($viewer)) {
                if (!$event->isOwner($member) && $memberInfo->active == true) {
                    $menus[] = array(
                        'label' => $this->translate('Remove Member'),
                        'name' => 'remove_member',
                        'url' => 'advancedevents/member/remove/' . $event->getIdentity(),
                        'urlParams' => array(
                            "user_id" => $member->getIdentity()
                        )
                    );
                }

                if ($memberInfo->active == false && $memberInfo->resource_approved == false) {
                    $menus[] = array(
                        'label' => $this->translate('Approve Request'),
                        'name' => 'approved_member',
                        'url' => 'advancedevents/member/approve/' . $event->getIdentity(),
                        'urlParams' => array(
                            "user_id" => $member->getIdentity()
                        )
                    );

                    $menus[] = array(
                        'label' => $this->translate('Reject Request'),
                        'name' => 'reject_member',
                        'url' => 'advancedevents/member/reject/' . $event->getIdentity(),
                        'urlParams' => array(
                            "user_id" => $member->getIdentity()
                        )
                    );
                }

                if ($memberInfo->active == false && $memberInfo->resource_approved == true) {
                    $menus[] = array(
                        'label' => $this->translate('Cancel Invite'),
                        'name' => 'cancel_invite',
                        'url' => 'advancedevents/member/remove/' . $event->getIdentity(),
                        'urlParams' => array(
                            "user_id" => $member->getIdentity()
                        )
                    );
                }

                $memberArray['menu'] = $menus;
            }
        }

        return $memberArray;
    }

//        2 => 'Attending',
//        1 => 'Maybe Attending',
//        0 => 'Not Attending',
    public function joinAction() {

        $this->validateRequestMethod('POST');

        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        if (!Engine_Api::_()->core()->hasSubject())
            $this->respondWithError('unauthorized');

        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $occurrence_id = $this->_getParam('occurrence_id', null);
        //SAVE THE OCCURRENCE ID IN THE.
        if (empty($occurrence_id) || !is_numeric($occurrence_id)) {
            //GET THE NEXT UPCOMING OCCURRENCE ID
            $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($this->_getParam('event_id'));
        }
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        if (($waitlist_id = $this->_getParam('waitlist_id', false))) {
            $waitlist = Engine_Api::_()->getItem('siteevent_waitlist', $waitlist_id);
            if ($waitlist) {
                $viewer = Engine_Api::_()->getItem('user', $waitlist->user_id);
            }
        }

        $isEventFull = $subject->isEventFull(array('occurrence_id' => $occurrence_id, 'doNotCheckCapacityFlag' => 1));

        if (!$waitlist_id && $isEventFull) {
            $this->respondWithError('error_message');
        }

        $occurrenceItem = Engine_Api::_()->getItem('siteevent_occurrence', $occurrence_id);
        if (!$waitlist_id && $occurrenceItem->waitlist_flag) {
            $this->respondWithError('error_message');
        }
        try {

            if ($subject->membership()->isResourceApprovalRequired() && !$subject->membership()->isMember($viewer, null)) {
                $row = $subject->membership()->getReceiver()
                        ->select()
                        ->where('resource_id = ?', $subject->getIdentity())
                        ->where('user_id = ?', $viewer->getIdentity())
                        ->where('occurrence_id = ?', $occurrence_id)
                        ->query()
                        ->fetch(Zend_Db::FETCH_ASSOC, 0);

                if (empty($row)) {
                    $this->respondWithError('unauthorized');
                } elseif ($row['user_approved'] && !$row['resource_approved']) {
                    $this->respondWithError('unauthorized');
                }
            }

            $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
            $db->beginTransaction();

//            if ($subject->membership()->isMember($viewer, null))
//                $this->respondWithError('already member');

            if (!$subject->membership()->isMember($viewer, null)) {
                $subject->membership()
                        ->addMember($viewer)
                        ->setUserApproved($viewer);
            }

            $oldRow = $row = $subject->membership()
                    ->getRow($viewer);
            $row = $subject->membership()
                    ->getRow($viewer);
            $rsvp = $this->_getParam('rsvp', 2);
            $row->rsvp = $rsvp;
            $row->save();

            //UPDATE THE MEMBER COUNT IN EVENT TABLE
            $member_count = $subject->membership()->getMemberCount();
            $subject->member_count = $member_count;
            $subject->save();


            Engine_Api::_()->siteevent()->deleteFeedNotifications('{"occurrence_id":"' . $occurrence_id . '"}', $subject, $viewer);

            $currentTime = time();
            $starttime = strtotime($occurrenceItem->starttime);
            $endtime = strtotime($occurrenceItem->endtime);

            // Add activity if membership status was not valid from before
            if ($rsvp == 2) {
                if ($starttime <= $currentTime && $currentTime <= $endtime) {
                    $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_mid_join', null, array('occurrence_id' => $occurrence_id));
                } else {
                    $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_join', null, array('occurrence_id' => $occurrence_id));
                }

                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'seaocore')->attachActivity($action, $subject);
                }

                try {
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subject->getOwner(), $viewer, $subject, 'siteevent_join', array('occurrence_id' => $occurrence_id));
                    //START NOTIFICATION AND EMAIL WORK
                    Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $action, 'siteevent_join', 'SITEEVENT_JOIN_CREATENOTIFICATION_EMAIL', null, $occurrence_id, 'joined', $viewer, 3, $viewer);
                    $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
                    if (!empty($isChildIdLeader)) {
                        Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_join');
                    }
                    //END NOTIFICATION AND EMAIL WORK
                } catch (Exception $e) {
                    //Blank Exception
                }
            }
            //START NOTIFICATION AND EMAIL WORK
            if ($rsvp == 2 && ($oldRow->rsvp != 2)) {

                Engine_Api::_()->siteevent()->joinEventNotifications($subject, 'siteevent_notification_send');

                Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $subject, 'siteevent_rsvp_change', 'SITEEVENT_RSVP_CHANGENOTIFICATION_EMAIL', null, $occurrence_id, 'rsvp', $viewer, $row->rsvp, $viewer);
                $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
                if (!empty($isChildIdLeader)) {
                    Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_rsvp_change');
                }
            } elseif ($rsvp == 0 || $rsvp == 1) {

                if ($rsvp == 1) {
                    if ($starttime <= $currentTime && $currentTime <= $endtime) {
                        $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_mid_maybe', null, array('occurrence_id' => $occurrence_id));
                    } elseif ($currentTime <= $endtime) {
                        $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_maybe_join', null, array('occurrence_id' => $occurrence_id));
                    }
                }

                if ($rsvp == 0) {
                    if ($starttime <= $currentTime && $currentTime <= $endtime) {
                        $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_mid_leave', null, array('occurrence_id' => $occurrence_id));
                    } elseif ($currentTime <= $endtime) {
                        $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_leave', null, array('occurrence_id' => $occurrence_id));
                    }
                }

                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'seaocore')->attachActivity($action, $subject);
                }
                Engine_Api::_()->siteevent()->joinEventNotifications($subject, 'siteevent_notification_send');
                Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $subject, 'siteevent_rsvp_change', 'SITEEVENT_RSVP_CHANGENOTIFICATION_EMAIL', null, $occurrence_id, 'rsvp', $viewer, $rsvp, $viewer);
                $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);

                if (!empty($isChildIdLeader)) {
                    Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_rsvp_change', $viewer);
                }
            }
            //END NOTIFICATION AND EMAIL WORK

            if (!empty($waitlist_id) && !empty($waitlist)) {
                $waitlist->delete();
            }

            $db->commit();

            if (isset($_REQUEST['getJoinInfo']) && !empty($_REQUEST['getJoinInfo'])) {
                $this->_forward('index', 'index', 'siteeventrepeat', array(
                    'event_id' => $subject->getIdentity(),
                ));
                return;
            }

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //Leave event-
    public function leaveAction() {

        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $event_id = $this->_getParam("event_id");
        $event = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($event))
            $this->respondWithError('no_record');
        $occurrence_id = $this->_getParam('occurrence_id', null);
        //SAVE THE OCCURRENCE ID IN THE.
        if (empty($occurrence_id) || !is_numeric($occurrence_id)) {
            //GET THE NEXT UPCOMING OCCURRENCE ID
            $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($this->_getParam('event_id'));
        }
        Zend_Registry::set('occurrence_id', $occurrence_id);

        $db = $event->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {

            if (!$event->membership()->isMember($viewer, true))
                $this->respondWithError('unauthorized');

            $event->membership()->removeMember($viewer);

            // remove from leader list if the member is no longer member of any event.
            if (!$event->membership()->isEventMember($viewer, true)) {
                $list = $event->getLeaderList();
                $list->remove($viewer);
            }

            //UPDATE THE MEMBER COUNT IN EVENT TABLE
            $member_count = $event->membership()->getMemberCount();
            $event->member_count = $member_count;
            $event->save();
            Engine_Api::_()->siteevent()->deleteFeedNotifications('{"occurrence_id":"' . $occurrence_id . '"}', $event);
            $db->commit();

            if (isset($_REQUEST['getJoinInfo']) && !empty($_REQUEST['getJoinInfo'])) {
                $this->_forward('index', 'index', 'siteeventrepeat', array(
                    'event_id' => $event->getIdentity(),
                ));
                return;
            }

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Invite the Event Members by Event Owner OR by Allowed Members.
     * 
     * @return array
     */
    public function inviteAction() {

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $event_id = $this->_getParam("event_id");
        $subject = $event = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $friends = $viewer->membership()->getMembers();
        if (empty($event))
            $this->respondWithError('no_record');

        if (!$subject->authorization()->isAllowed($viewer, 'invite'))
            $this->respondWithError('unauthorized');

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

//GET THE LEADERS LIST AND CHECK IF THE VIEWER IS LEADER OR NORMAL USER.
        if ($event->owner_id == $viewer->getIdentity()) {
            $isLeader = 1;
        } else {
            $list = $event->getLeaderList();

            $listItem = $list->get($viewer);
            $isLeader = ( null !== $listItem );
        }
        try {
            $occurrence_id = Engine_Api::_()->getItem('siteevent_occurrence', $event_id)->occurrence_id;

            if ($this->getRequest()->isGet()) {
                $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getInviteForm($isLeader, $subject, $viewer);
                $this->respondWithSuccess($response, true);
            } else if ($this->getRequest()->isPost()) {
                $values = $this->_getAllParams();
                // Process

                $table = $event->getTable();
                $db = $table->getAdapter();
                $db->beginTransaction();

                if (empty($values['user_ids']))
                    $this->respondWithValidationError('validation_fail', 'User ids are required');

                if ($isLeader) {
                    //TODO LIST OF FRIENDS API
                    $usersIds = $this->_getParam('user_ids');
                    $usersIds = explode(',', $usersIds);

                    //GET ALL THE SITE MEMBERS WHO ARE NOT YET MEMBERS OF THIS EVENT.
                    $friends = Engine_Api::_()->siteevent()->getMembers($event->event_id, $occurrence_id, '');
                } else
                    $usersIds = $values['users'];
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $friendsToJoin = array();
                //TODO MEMBERS AUTOSUGGEST API AND ARRAY OF USERIDS
                foreach ($usersIds as $user_id) {
                    $user = Engine_Api::_()->getItem('user', $user_id);
                    if (!$event->membership()->isMember($user)) {
                        if (null === $user || !isset($user->email)) {
                            continue;
                        }

                        $friendsToJoin[] = $user->email . '#' . $user->displayname;
                        $event->membership()->addMember($user)
                                ->setResourceApproved($user);

                        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite'))
                            $notifyApi->addNotification($user, $viewer, $event, 'siteevent_invite', array('occurrence_id' => $occurrence_id));
                    }
                }
                try {
                    //SEND MAIL NOTIFICATION AS WELL
                    if (!empty($friendsToJoin) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite') && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                        Engine_Api::_()->getApi('Invite', 'Seaocore')->sendPageInvites($friendsToJoin, $event->event_id, 'siteevent', '', 'siteevent_event');
                    }
                } catch (Exception $ex) {
                    //Blank Exception
                }

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

//accept invite
    public function acceptAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $subject = Engine_Api::_()->core()->getSubject();

// $tabId = $this->_getParam('tab', null);
        if ($this->getRequest()->isGet()) {
//SAVE THE OCCURRENCE ID IN THE ZEND REGISTRY.
            $occurrence_id = $this->_getParam('occurrence_id', '');
            if (empty($occurrence_id) || !is_numeric($occurrence_id)) {
                //GET THE NEXT UPCOMING OCCURRENCE ID
                $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($subject->getIdentity());
            }

            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getMemberJoinForm();
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();

            $occurrence_id = $this->_getParam('occurrence_id', '');
            if (empty($occurrence_id) || !is_numeric($occurrence_id)) {
                //GET THE NEXT UPCOMING OCCURRENCE ID
                $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($subject->getIdentity());
            }

            Engine_Api::_()->getApi('Core', 'siteapi')->setView();
            try {
                $is_error = 0;
                if (!isset($values['rsvp'])) {
                    $is_error = 1;
                }

                $errorMessage = array();
                $errorMessage[] = "Rsvp field is required";

                //SENDING MESSAGE
                if ($is_error == 1) {
                    $this->respondWithValidationError('validation_fail', $errorMessage);
                }


                $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
                $db->beginTransaction();

                $canAccept = $subject->membership()->getRow($viewer);

                if ($canAccept->user_approved || !$canAccept->resource_approved)
                    $this->respondWithError('unauthorized');

                $subject->membership()->setUserApproved($viewer);

                $row = $subject->membership()
                        ->getRow($viewer);
                $oldRow = $subject->membership()
                        ->getRow($viewer);
                $row->rsvp = $values['rsvp'];
                $row->save();

                // Set the request as handled
                $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                        $viewer, $subject, 'siteevent_invite', array('occurrence_id' => $occurrence_id));
                if ($notification) {
                    $notification->mitigated = true;
                    $notification->read = true;
                    $notification->save();
                }

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite')) {
                    // Set the request as handled
                    $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                            $viewer, $subject, 'siteevent_suggested', array('occurrence_id' => $occurrence_id));
                    if ($notification) {
                        $notification->mitigated = true;
                        $notification->read = true;
                        $notification->save();
                    }
                }

                // Add activity
                //if (!$membership_status) {
                if ($row->rsvp == 2) {

                    //Engine_Api::_()->siteevent()->deleteFeedNotifications('{"occurrence_id":"' . $occurrence_id . '"}', $subject);

                    $occurrenceItem = Engine_Api::_()->getItem('siteevent_occurrence', $occurrence_id);
                    $currentTime = time();
                    $starttime = strtotime($occurrenceItem->starttime);
                    $endtime = strtotime($occurrenceItem->endtime);
                    if ($starttime <= $currentTime && $currentTime <= $endtime) {
                        $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_mid_join', null, array('occurrence_id' => $occurrence_id));
                    } else {
                        $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_join', null, array('occurrence_id' => $occurrence_id));
                    }

                    if ($action != null) {
                        Engine_Api::_()->getDbtable('actions', 'seaocore')->attachActivity($action, $subject);
                    }


                    //START NOTIFICATION AND EMAIL WORK
                    Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $action, 'siteevent_join', 'SITEEVENT_JOIN_CREATENOTIFICATION_EMAIL', null, $occurrence_id, 'joined', $viewer);
                    $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
                    if (!empty($isChildIdLeader)) {
                        Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_join');
                    }
                    //END NOTIFICATION AND EMAIL WORK
                }
                //}
                //START NOTIFICATION AND EMAIL WORK
                if ($values['rsvp'] == 2 && ($oldRow->rsvp != 2)) {
                    Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $subject, 'siteevent_rsvp_change', 'SITEEVENT_RSVP_CHANGENOTIFICATION_EMAIL', null, $occurrence_id, 'rsvp', $viewer, $values['rsvp']);
                    $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
                    if (!empty($isChildIdLeader)) {
                        Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_rsvp_change');
                    }
                } elseif ($values['rsvp'] == 0 || $values['rsvp'] == 1) {
                    Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $subject, 'siteevent_rsvp_change', 'SITEEVENT_RSVP_CHANGENOTIFICATION_EMAIL', null, $occurrence_id, 'rsvp', $viewer, $values['rsvp']);
                    $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
                    if (!empty($isChildIdLeader)) {
                        Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_rsvp_change');
                    }
                }
                //END NOTIFICATION AND EMAIL WORK

                $db->commit();
                //$message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the event %s');
                $this->successResponseNoContent("no_content", true);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

//Request Invite
    public function requestAction() {

        $this->validateRequestMethod('POST');

// Check auth
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireSubject()->isValid())
            $this->respondWithError('no_record');


        $occurrence_id = $this->_getParam('occurrence_id', null);
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

// Process Request
        if ($this->getRequest()->isPost()) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $subject = Engine_Api::_()->core()->getSubject('siteevent_event');
            $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $subject->membership()->addMember($viewer)->setUserApproved($viewer);

                // Add notification
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                try {
                    $notifyApi->addNotification($subject->getOwner(), $viewer, $subject, 'siteevent_approve', array('occurrence_id' => $occurrence_id));
                } catch (Exception $e) {
                    //Blank Exception
                }

                $db->commit();
                $this->successResponseNoContent("no_content", true);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

//Cancel Invite request sent
    public function cancelAction() {

        $this->validateRequestMethod('POST');

// Check auth
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');
        if (!$this->_helper->requireSubject()->isValid())
            $this->respondWithError('no_record');

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();


//Process Values
        $user_id = $this->_getParam('user_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        if (!$subject->authorization()->isAllowed($viewer, 'invite') &&
                $user_id != $viewer->getIdentity() &&
                $user_id) {
            $this->respondWithError('no_record');
        }

        if ($user_id) {
            $user = Engine_Api::_()->getItem('user', $user_id);
            if (!$user) {
                $this->respondWithError('no_record');
            }
        } else {
            $user = $viewer;
        }

        $subject = Engine_Api::_()->core()->getSubject('siteevent_event');
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            $subject->membership()->removeMember($user);

// Remove the notification?
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $subject->getOwner(), $subject, 'siteevent_approve', array('occurrence_id' => $occurrence_id));
            if ($notification) {
                $notification->delete();
            }
            $db->commit();
            $this->successResponseNoContent("no_content", true);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

//Reject Invite
    public function rejectAction() {

        $this->validateRequestMethod('POST');

// Check auth
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');
        if (!$this->_helper->requireSubject()->isValid())
            $this->respondWithError('no_record');

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        $occurrence_id = $this->_getParam('occurrence_id', null);
        $user_id = $this->_getParam('user_id', null);

        if ($user_id)
            $user = Engine_Api::_()->getItem('user', $user_id);
        if (!$user) {
            $this->respondWithError('no_record');
        }

// Process form
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $row = $subject->membership()->getRow($user);
            if ($row->user_approved && !$row->resource_approved) {
                $subject->membership()->removeMember($user);
            } else {
                $this->respondWithError('unauthorized');
            }
// Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $viewer, $subject, 'siteevent_invite', array('occurrence_id' => $occurrence_id));
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = true;
                $notification->save();
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite')) {
                // Set the request as handled
                $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                        $viewer, $subject, 'siteevent_suggested', array('occurrence_id' => $occurrence_id));
                if ($notification) {
                    $notification->mitigated = true;
                    $notification->read = true;
                    $notification->save();
                }
            }
//TODO DISPLAY SUCCESS MESSAGE
//        if(!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite')) {
//          $message = Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the event %s.');
//        } else { 
//          $message = Zend_Registry::get('Zend_Translate')->_('You have ignored the request for the visit and explore the event %s.');  
//        }
            $db->commit();
            $this->successResponseNoContent("no_content", true);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

//REMOVE MEMBER ACTION FROM THE GUEST LIST
//TODO Correct error message 
    public function removeAction() {
        $this->validateRequestMethod('DELETE');

// Check auth
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');
        if (!$this->_helper->requireSubject()->isValid())
            $this->respondWithError('no_record');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

// Get user
        if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
//TODO Correct message
            $this->respondWithError('missing_parameter');
        }

        $event = $subject = Engine_Api::_()->core()->getSubject();

        if ($event->owner_id != $viewer_id && $viewer_id->level_id != 1)
            $this->respondWithError('unauthorized');

        $widgetIgnoreRequest = $this->_getParam('ignore_request', 0);
        if (!$event->membership()->isMember($user)) {
            $this->respondWithError('non_member');
        }

        $occurrence_id = $this->_getParam('occurrence_id', null);
        $tabId = $this->_getParam('tab', null);

        $db = $event->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
// Remove membership
            $event->membership()->removeMember($user);

// Remove the notification?
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $event->getOwner(), $event, 'siteevent_approve', array('occurrence_id' => $occurrence_id));
            if ($notification) {
                $notification->delete();
            }
            $viewer = Engine_Api::_()->user()->getViewer();

//            if (!$widgetIgnoreRequest) {
//                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $event, 'siteevent_guest_removed', array('occurrence_id' => $occurrence_id));
//                $subjectOwner = $subject->getOwner();
//                $host = $_SERVER['HTTP_HOST'];
//                $newVar = _ENGINE_SSL ? 'https://' : 'http://';
//                $object_link = $newVar . $host . $subject->getHref();
//                $vender_link = '<a href=' . $newVar . $host . $subject->getOwner()->getHref() . ">$viewerGetTitle</a>";
//                $obiewerGetTitle = $subjectOwner->getTitle();
//                $subjectTitle = $subject->getTitle();
//                $sender_link = '<a href=' . $newVar . $host . $subject->getOwner()->getHref() . ">$viewerGetTitle</a>";
//                $object_title_with_link = '<a href=' . $newVar . $host . $subject->getHref() . ">$subjectTitle</a>";
//
//                Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'SITEEVENT_GUEST_REMOVED', array(
//                    'sender' => $sender_link,
//                    'object_link' => $object_link,
//                    'object_title' => $subject->getTitle(),
//                    'object_description' => $subject->getDescription(),
//                    'object_title_with_link' => $object_title_with_link,
//                    'queue' => false
//                ));
//            } else {
            //               Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $event, 'siteevent_request_disapprove', array('occurrence_id' => $occurrence_id));
//            }
            $db->commit();
            $this->successResponseNoContent("no_content", true);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

//Approve Invite Request
    public function approveAction() {
        $this->validateRequestMethod('POST');

// Check auth
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');
        if (!$this->_helper->requireSubject()->isValid())
            $this->respondWithError('no_record');

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();


// Get user
        if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithError('no_record');
        }

        $occurrence_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('occurrence_id');
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id');

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->membership()->setResourceApproved($user);
            $row = $subject->membership()->getRow($user);
//$row->active = 1;
//$row->user_approved = 1;
            $row->rsvp = 2;
            $row->save();

            try {
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $subject, 'siteevent_accepted', array('occurrence_id' => $occurrence_id));
            } catch (Exception $e) {
                //Blank Exception
            }

// Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $viewer, $subject, 'siteevent_approve', array('occurrence_id' => $occurrence_id));
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = true;
                $notification->save();
            }

            $db->commit();
            $this->successResponseNoContent("no_content", true);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function promoteAction() {

        // Validate request methods
        $this->validateRequestMethod('POST');

        $multipleLeaderSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.leader', 1);

        if (empty($multipleLeaderSetting)) {
            $this->respondWithError('unauthorized');
        }
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // Get user
        if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithError('no_record');
        }

        $siteevent = Engine_Api::_()->core()->getSubject();
        $list = $siteevent->getLeaderList();
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$siteevent->membership()->isMember($user))
            $this->respondWithError('unauthorized');

        $table = $list->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {

            $list->add($user);


            // Add notification
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notifyApi->addNotification($user, $viewer, $siteevent, 'siteevent_promote');
            // Add activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'seaocore');
            $action = $activityApi->addActivity($user, $siteevent, 'siteevent_promote');

            $db->commit();
            $this->successResponseNoContent("no_content", true);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function demoteAction() {

        // Validate request methods
        $this->validateRequestMethod('POST');

        $multipleLeaderSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.leader', 1);

        if (empty($multipleLeaderSetting)) {
            $this->respondWithError('unauthorized');
        }

        // Get user
        if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithError('unauthorized');
            // return $this->_helper->requireSubject->forward();
        }

        $siteevent = Engine_Api::_()->core()->getSubject();
        $list = $siteevent->getLeaderList();

        if (!$siteevent->membership()->isMember($user)) {
            $this->respondWithError('unauthorized');
            //throw new Siteevent_Model_Exception('Cannot remove a non-member as a leader');
        }

        $table = $list->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $list->remove($user);
            $db->commit();
            $this->successResponseNoContent("no_content", true);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

//Confirm 1/2
//userid
    public function confirmAction() {
        $this->validateRequestMethod('POST');

// Check auth
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');
        if (!$this->_helper->requireSubject()->isValid())
            $this->respondWithError('no_record');

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

// Get user
        if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithError('error_message');
        }

        $event = $subject = Engine_Api::_()->core()->getSubject();

        if (!$event->membership()->isMember($user)) {
            $this->respondWithError('error _message');
        }
        $row = $subject->membership()->getRow($user);

        $occurrence_id = $this->_getParam('occurrence_id', null);
        $tabId = $this->_getParam('tab', null);

        $db = $event->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();
        $confirm = $this->_getParam('confirm', 1);

        try {
            $row->confirm = $_POST['confirm'];
            $row->save();
            $subjectOwner = $subject->getOwner();
            $host = $_SERVER['HTTP_HOST'];
            $newVar = _ENGINE_SSL ? 'https://' : 'http://';
            $object_link = $newVar . $host . $subject->getHref();
            $viewerGetTitle = $subjectOwner->getTitle();
            $subjectTitle = $subject->getTitle();
            $sender_link = '<a href=' . $newVar . $host . $subject->getOwner()->getHref() . ">$viewerGetTitle</a>";
            $object_title_with_link = '<a href=' . $newVar . $host . $subject->getHref() . ">$subjectTitle</a>";
            if ($_POST['confirm'] == 1) {
                $message = 'This member has been confirmed for this event.';
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'SITEEVENT_CONFIRM_GUEST', array(
                    'sender' => $sender_link,
                    'object_link' => $object_link,
                    'object_title' => $subject->getTitle(),
                    'object_description' => $subject->getDescription(),
                    'object_title_with_link' => $object_title_with_link,
                    'queue' => true
                ));


                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $viewer = Engine_Api::_()->user()->getViewer();
                $notifyApi->addNotification($user, $subjectOwner, $subject, 'siteevent_confirm_guests', array('occurrence_id' => $occurrence_id));
            } else if ($_POST['confirm'] == 2) {
                $message = 'This member has been rejected for this event.';
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'SITEEVENT_NONCONFIRM_GUEST', array(
                    'sender' => $sender_link,
                    'object_link' => $object_link,
                    'object_title' => $subject->getTitle(),
                    'object_description' => $subject->getDescription(),
                    'object_title_with_link' => $object_title_with_link,
                    'queue' => true
                ));

                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $viewer = Engine_Api::_()->user()->getViewer();
                $notifyApi->addNotification($user, $subjectOwner, $subject, 'siteevent_reject_guests', array('occurrence_id' => $occurrence_id));
            }

            $db->commit();
            $this->successResponseNoContent("no_content", true);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function manageLeadersAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //GET EVENT ID
        $event_id = $this->_getParam('event_id');

        //GET SITEGEVENT ITEM
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $viewer = Engine_Api::_()->user()->getViewer();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            $this->respondWithError('unauthorized');
        }

        $itemTypeValue = $siteevent->getParent()->getType();
        $multipleLeader = 0;
        if ($itemTypeValue == 'sitereview_listing') {
            $item = Engine_Api::_()->getItem('sitereview_listing', $siteevent->getIdentity());
            $itemTypeValue = $itemTypeValue . $item->listingtype_id;
            $multipleLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteevent.multiple.leader.$itemTypeValue", 0);
        } elseif ($itemTypeValue != 'user') {
            $multipleLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteevent.multiple.leader.$itemTypeValue", 0);
        }
        if ($itemTypeValue != 'user' && !$multipleLeader) {
            $this->respondWithError('unauthorized');
        }

        if ($itemTypeValue == 'user' && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.leader', 1)) {
            $this->respondWithError('unauthorized');
        }

        $list = $siteevent->getLeaderList();
        $list_id = $list['list_id'];

        $listItemTable = Engine_Api::_()->getDbTable('listItems', 'siteevent');
        $listItemTableName = $listItemTable->info('name');

        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $userTable->info('name');

        $selectLeaders = $listItemTable->select()
                ->from($listItemTableName, array('child_id'))
                ->where("list_id = ?", $list_id)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
        $selectLeaders[] = $siteevent->owner_id;

        $select = $userTable->select()
                ->from($userTableName)
                ->where("$userTableName.user_id IN (?)", (array) $selectLeaders)
                ->order('displayname ASC');

        $members = $userTable->fetchAll($select);
        if ($this->getRequest()->isGet()) {
            foreach ($members as $member) {
                $leaders['user_id'] = $member->user_id;
                $leaders['name'] = $member->getIdentity();
                $leaders['email'] = $member->email;
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($member, true);
                $leaders = array_merge($leaders, $getContentImages);
                $response[] = $leaders;
            }
            $this->respondWithSuccess($response, true);
        }
        if ($this->getRequest()->isPost()) {

            $user_id = $this->_getParam("user_id");


            if (!empty($user_id)) {
                $viewer = Engine_Api::_()->user()->getViewer();
                $user = Engine_Api::_()->getItem('user', $user_id);

                $table = $list->getTable();
                $db = $table->getAdapter();
                $db->beginTransaction();

                try {
                    $list->add($user);

                    // Add notification
                    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                    // $notifyApi->addNotification($user, $viewer, $siteevent, 'siteevent_promote');
                    // Add activity
                    $activityApi = Engine_Api::_()->getDbtable('actions', 'seaocore');
                    $activityApi->addActivity($user, $siteevent, 'siteevent_promote');

                    $db->commit();
                    $this->successResponseNoContent('no_content', true);
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            }
        }
    }

    public function composeAction() {

        $multi = 'member';
        $multi_ids = '';

        $occurrence_id = $this->_getParam('occurrence_id', 'all');
        if (empty($occurrence_id))
            $occurrence_id = 'all';

        $event_id = $event_id = $this->_getParam("event_id");
        $event = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $viewer = Engine_Api::_()->user()->getViewer();
        $canEdit = $event->authorization()->isAllowed($viewer, "edit");
        if (!$event->isOwner($viewer) && $viewer->level_id != 1 && !$canEdit) {
            $this->respondWithError('unauthorized');
        }

        $form = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getMessageComposeForm($event);


        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess($form, true);
        } else if ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();

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

            $guestValue = $values['guests'];

            $occurrence_id = 0;
            if (isset($values['filter_occurrence_date']) && $values['filter_occurrence_date'] != 'all')
                $occurrence_id = $values['filter_occurrence_date'];
            if ($guestValue == 4) {
                $members_ids = explode(",", $values['toValues']);
            } else {

                $userTable = Engine_Api::_()->getDbtable('users', 'user');
                $userTableName = $userTable->info('name');

                $user_id = $viewer->getIdentity();

                if (!empty($multi) && !Engine_Api::_()->siteevent()->isTicketBasedEvent()) {

                    $tableMember = Engine_Api::_()->getDbtable('membership', 'siteevent');
                    $tableMemberName = $tableMember->info('name');

                    $select = $tableMember->select()
                            ->setIntegrityCheck(false)
                            ->from($tableMemberName, array('user_id'))
                            ->join($userTableName, $userTableName . '.user_id = ' . $tableMemberName . '.user_id')
                            ->where($tableMemberName . '.active = ?', 1)
                            ->where($tableMemberName . '.resource_approved = ?', 1)
                            ->where($tableMemberName . '.user_approved = ?', 1)
                            ->where($tableMemberName . '.user_id != ?', $user_id)
                            ->where($tableMemberName . '.resource_id = ?', $event_id);

                    if ($guestValue != 3) {
                        $select->where($tableMemberName . '.rsvp = ?', $guestValue);
                    }

                    if ($occurrence_id)
                        $select->where($tableMemberName . '.occurrence_id = ?', $occurrence_id);
                    $members_ids = $select->query()->fetchAll();
                }
                elseif (Engine_Api::_()->siteevent()->isTicketBasedEvent()) {

                    $orderTable = Engine_Api::_()->getDbTable('orders', 'siteeventticket');
                    $orderTableName = $orderTable->info('name');

                    $select = $orderTable->select()
                            ->setIntegrityCheck(false)
                            ->from($orderTableName, array("user_id"))
                            ->join($userTableName, $userTableName . '.user_id = ' . $orderTableName . '.user_id')
                            ->where("$orderTableName.event_id =?", $event_id)
                            ->where($orderTableName . '.user_id != ?', $user_id)
                            ->group("$orderTableName.user_id");

                    if ($occurrence_id) {
                        $select->where($orderTableName . '.occurrence_id = ?', $occurrence_id);
                    }

                    $members_ids = $select->query()->fetchAll();
                }
            }

            if (!empty($members_ids)) {
                foreach ($members_ids as $member_id) {
                    if ($guestValue != 4) {
                        $multi_ids .= ',' . $member_id['user_id'];
                    } else {
                        $multi_ids .= ',' . $member_id;
                    }
                }

                $multi_ids = ltrim($multi_ids, ",");
                if ($multi_ids) {
                    $multi = $multi;
                    $multi_name = $viewer->getTitle();
                    $multi_ids = $multi_ids;
                    $values['toValues'] = $multi_ids;
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
                $recipients = preg_split('/[,. ]+/', $values['toValues']);

                // limit recipients if it is not a special list of members
                if (empty($multi))
                    $recipients = array_slice($recipients, 0, 10); // Slice down to 10









                    
// clean the recipients for repeating ids
                // this can happen if recipient is selected and then a friend list is selected
                $recipients = array_unique($recipients);
                $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
                $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $recipients, $values['title'], $values['body'], $attachment);
                foreach ($recipientsUsers as $user) {
                    if ($user->getIdentity() == $viewer->getIdentity()) {
                        continue;
                    }
                    try {
                        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $conversation, 'message_new');
                    } catch (Exception $e) {
                        //Blank Exception 
                    }
                }
                //Increment messages counter
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

}
