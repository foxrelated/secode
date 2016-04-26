<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MemberAjaxBasedController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_MemberAjaxBasedController extends Core_Controller_Action_Standard {

    public function init() {

        if (0 !== ($event_id = (int) $this->_getParam('event_id')) &&
                null !== ($event = Engine_Api::_()->getItem('siteevent_event', $event_id))) {
            Engine_Api::_()->core()->setSubject($event);
        }

        $this->_helper->requireUser();
        $this->_helper->requireSubject('siteevent_event');
    }

    public function joinAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject()->isValid())
            return;

        // Check resource approval
        $viewer = Engine_Api::_()->user()->getViewer();

        if ($this->_getParam('ismanagepage', false))
            $subject = $event = Engine_Api::_()->getItem('siteevent_event', $this->_getParam('event_id', 0));
        else
            $subject = $event = Engine_Api::_()->core()->getSubject();

        $occurrence_id = $this->_getParam('occurrence_id', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);
        if ($subject->membership()->isResourceApprovalRequired()) {
            $row = $subject->membership()->getReceiver()
                    ->select()
                    ->where('resource_id = ?', $subject->getIdentity())
                    ->where('user_id = ?', $viewer->getIdentity())
                    ->where('occurrence_id = ?', $this->_getParam('occurrence_id', null))
                    ->query()
                    ->fetch(Zend_Db::FETCH_ASSOC, 0);
            ;
        }

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
//			$membership_status = $subject->membership()->getRow($viewer)->active;

            if ($this->_getParam('ismanagepage', false)) {
                $subject->membership()
                        ->setUserApproved($viewer)
                ;
            }
            else
                $subject->membership()
                        ->addMember($viewer)
                        ->setUserApproved($viewer)
                ;

            $row = $subject->membership()
                    ->getRow($viewer);

            if ($this->_getParam('ismanagepage', false))
                $row->rsvp = $this->_getParam('option_id', 0);
            else
                $row->rsvp = 2;
            $row->save();

            //UPDATE THE MEMBER COUNT IN EVENT TABLE
            $member_count = $subject->membership()->getMemberCount();
            $subject->member_count = $member_count;
            $subject->save();

            Engine_Api::_()->siteevent()->deleteFeedNotifications('{"occurrence_id":"' . $occurrence_id . '"}', $subject);
            // Add activity if membership status was not valid from before
            //if (!$membership_status){

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

            //}
// 			Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subject->getOwner(), $viewer, $subject, 'siteevent_join', array('occurrence_id' => $occurrence_id));
// 				//START NOTIFICATION AND EMAIL WORK
// 				Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $action, 'siteevent_join', 'SITEEVENT_JOIN_CREATENOTIFICATION_EMAIL', null, $occurrence_id, 'joined', $viewer);
// 				$isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
// 				if (!empty($isChildIdLeader)){
// 					Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_join');
// 				}
// 				//END NOTIFICATION AND EMAIL WORK
// 
// 				//START NOTIFICATION AND EMAIL WORK
// 				Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $subject, 'siteevent_rsvp_change', 'SITEEVENT_RSVP_CHANGENOTIFICATION_EMAIL', null, $occurrence_id, 'rsvp', $viewer);
// 				$isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
// 				if (!empty($isChildIdLeader)){
// 					Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_rsvp_change');
// 				}
// 				//END NOTIFICATION AND EMAIL WORK


            $db->commit();
            if ($this->_getParam('ismanagepage', false)) {
                //CHECK IF THE JOINED FRIEND CAN INVITE HIS FRIENDS OR NOT
                $auth = Engine_Api::_()->authorization()->context;
                $this->view->canInvite = false;
                if ($auth->isAllowed($event, $viewer, "invite"))
                    $this->view->canInvite = true;

                //GET THE NO. OF INVITE EVENTS REMAINING FOR THIS USER
                $this->view->invite_count = Engine_Api::_()->getDbTable('membership', 'siteevent')->getInviteCount($viewer->getIdentity());
            }
            $this->view->status = true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
//       $helper = 'advancedActivity';
//       $this->view->body = $this->view->$helper($action, array('noList' => true));
        }
    }

    public function requestAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();
        $occurrence_id = $this->_getParam('occurrence_id', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);
        try {
            $subject->membership()->addMember($viewer)->setUserApproved($viewer);

            // Add notification
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notifyApi->addNotification($subject->getOwner(), $viewer, $subject, 'siteevent_approve', array('occurrence_id' => $occurrence_id));

            $db->commit();
            $row = $subject->membership()->getRow($viewer);
            $this->view->status = true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
//       $helper = 'advancedActivity';
//       $this->view->body = $this->view->$helper($action, array('noList' => true));
        }
    }

    public function cancelAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject()->isValid())
            return;
        $occurrence_id = $this->_getParam('occurrence_id', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);
        $user_id = $this->_getParam('user_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        if (!$subject->authorization()->isAllowed($viewer, 'invite') &&
                $user_id != $viewer->getIdentity() &&
                $user_id) {
            return;
        }

        if ($user_id) {
            $user = Engine_Api::_()->getItem('user', $user_id);
            if (!$user) {
                return;
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
            $this->view->status = true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
//       $helper = 'advancedActivity';
//       $this->view->body = $this->view->$helper($action, array('noList' => true));
        }
    }

    public function leaveAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject()->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $occurrence_id = $this->_getParam('occurrence_id', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);
        //if( $subject->isOwner($viewer) ) return;

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->membership()->removeMember($viewer);
            // remove from leader list if the member is no longer member of any event.
            if (!$subject->membership()->isEventMember($viewer, true)) {
                $list = $subject->getLeaderList();
                $list->remove($viewer);
            }
            $row = $subject->membership()->getRow($viewer);

            //UPDATE THE MEMBER COUNT IN EVENT TABLE
            $member_count = $subject->membership()->getMemberCount();
            $subject->member_count = $member_count;
            $subject->save();

            Engine_Api::_()->siteevent()->deleteFeedNotifications('{"occurrence_id":"' . $occurrence_id . '"}', $subject);
            $this->view->showLink = 2;
            if (null === $row) {
                if ($subject->membership()->isResourceApprovalRequired()) {
                    $this->view->showLink = 1;
                } else {
                    $this->view->showLink = 2;
                }
            }
            $db->commit();
            $this->view->status = true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
//       $helper = 'advancedActivity';
//       $this->view->body = $this->view->$helper($action, array('noList' => true));
        }
    }

    public function acceptAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
            return;
        $occurrence_id = $this->_getParam('occurrence_id', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);
        // Process form
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            //$membership_status = $subject->membership()->getRow($viewer)->active;

            $subject->membership()->setUserApproved($viewer);

            $row = $subject->membership()
                    ->getRow($viewer);

            $row->rsvp = 2;
            $row->save();

            Engine_Api::_()->siteevent()->deleteFeedNotifications('{"occurrence_id":"' . $occurrence_id . '"}', $subject);

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $viewer, $subject, 'siteevent_invite', array('occurrence_id' => $occurrence_id));
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = true;
                $notification->save();
            }

            // Add activity
            //if (!$membership_status){
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

// 				//START NOTIFICATION AND EMAIL WORK
// 				Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $action, 'siteevent_join', 'SITEEVENT_JOIN_CREATENOTIFICATION_EMAIL', null, $occurrence_id, 'joined', $viewer);
// 				$isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
// 				if (!empty($isChildIdLeader)){
// 					Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_join');
// 				}
// 				//END NOTIFICATION AND EMAIL WORK
// 
// 				//START NOTIFICATION AND EMAIL WORK
// 				Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $subject, 'siteevent_rsvp_change', 'SITEEVENT_RSVP_CHANGENOTIFICATION_EMAIL', null, $occurrence_id, 'rsvp', $viewer);
// 				$isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
// 				if (!empty($isChildIdLeader)){
// 					Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_rsvp_change');
// 				}
// 				//END NOTIFICATION AND EMAIL WORK
            //}
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->error = false;
        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
//       $helper = 'advancedActivity';
//       $this->view->body = $this->view->$helper($action, array('noList' => true));
        }
    }

    public function rejectAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
            return;
        $occurrence_id = $this->_getParam('occurrence_id', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);
        // Process form
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($this->_getParam('ismanagepage', false))
            $subject = $event = Engine_Api::_()->getItem('siteevent_event', $this->_getParam('event_id', 0));
        else
            $subject = $event = Engine_Api::_()->core()->getSubject();

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->membership()->removeMember($viewer);

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $viewer, $subject, 'siteevent_invite', array('occurrence_id' => $occurrence_id));
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = true;
                $notification->save();
            }

            $db->commit();

            if ($this->_getParam('ismanagepage', false)) {
                //CHECK IF THE JOINED FRIEND CAN INVITE HIS FRIENDS OR NOT					
                //GET THE NO. OF INVITE EVENTS REMAINING FOR THIS USER
                $this->view->invite_count = Engine_Api::_()->getDbTable('membership', 'siteevent')->getInviteCount($viewer->getIdentity());
            }
            $this->view->status = true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
//       $helper = 'advancedActivity';
//       $this->view->body = $this->view->$helper($action, array('noList' => true));
        }
    }

    public function removeAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject()->isValid())
            return;
        $occurrence_id = $this->_getParam('occurrence_id', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);
        // Get user
        if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            return $this->_helper->requireSubject->forward();
        }

        $event = Engine_Api::_()->core()->getSubject();

        if (!$event->membership()->isMember($user)) {
            throw new Event_Model_Exception('Cannot remove a non-member');
        }

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

            $db->commit();
            $this->view->status = true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
//       $helper = 'advancedActivity';
//       $this->view->body = $this->view->$helper($action, array('noList' => true));
        }
    }

    public function approveAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
            return;

        // Get user
        if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            return $this->_helper->requireSubject->forward();
        }
        $occurrence_id = $this->_getParam('occurrence_id', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->membership()->setResourceApproved($user);

            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $subject, 'siteevent_accepted', array('occurrence_id' => $occurrence_id));
            $this->view->status = true;
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
//       $helper = 'advancedActivity';
//       $this->view->body = $this->view->$helper($action, array('noList' => true));
        }
    }

}