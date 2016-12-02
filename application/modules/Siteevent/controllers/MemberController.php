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
class Siteevent_MemberController extends Core_Controller_Action_Standard {

    public function init() {
        if (0 !== ($event_id = (int) $this->_getParam('event_id')) &&
                null !== ($event = Engine_Api::_()->getItem('siteevent_event', $event_id))) {
            Engine_Api::_()->core()->setSubject($event);
        }

        $this->_helper->requireUser();
        $this->_helper->requireSubject('siteevent_event');
        /*
          $this->_helper->requireAuth()->setAuthParams(
          null,
          null,
          null
          //'edit'
          );
         *
         */
    }

    public function joinAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject()->isValid())
            return;

        $this->_helper->layout->setLayout('default-simple');

        // Check resource approval
        $viewer = Engine_Api::_()->user()->getViewer();

        $subject = Engine_Api::_()->core()->getSubject();
        $occurrence_id = $this->_getParam('occurrence_id', null);
        $tabId = $this->_getParam('tab', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);

        if (($waitlist_id = $this->_getParam('waitlist_id', false))) {
            $waitlist = Engine_Api::_()->getItem('siteevent_waitlist', $waitlist_id);
            if ($waitlist) {
                $viewer = Engine_Api::_()->getItem('user', $waitlist->user_id);
            }
        }

        $this->view->isEventFull = $subject->isEventFull(array('occurrence_id' => $occurrence_id, 'doNotCheckCapacityFlag' => 1));
        $this->view->waitlist_id = $waitlist_id;

        if (!$waitlist_id && $this->view->isEventFull) {
            return;
        }

        $occurrenceItem = Engine_Api::_()->getItem('siteevent_occurrence', $occurrence_id);
        if (!$waitlist_id && $occurrenceItem->waitlist_flag) {
            return;
        }

        if ($subject->membership()->isResourceApprovalRequired()) {
            $row = $subject->membership()->getReceiver()
                    ->select()
                    ->where('resource_id = ?', $subject->getIdentity())
                    ->where('user_id = ?', $viewer->getIdentity())
                    ->where('occurrence_id = ?', $occurrence_id)
                    ->query()
                    ->fetch(Zend_Db::FETCH_ASSOC, 0);
            ;
            if (empty($row)) {
                // has not yet requested an invite
                return $this->_helper->redirector->gotoRoute(array('action' => 'request', 'format' => 'smoothbox'));
            } elseif ($row['user_approved'] && !$row['resource_approved']) {
                // has requested an invite; show cancel invite page
                return $this->_helper->redirector->gotoRoute(array('action' => 'cancel', 'format' => 'smoothbox'));
            }
        }

        // Make form
        $this->view->form = $form = new Siteevent_Form_Member_Join();

        // Process form
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //$viewer = Engine_Api::_()->user()->getViewer();
            $subject = Engine_Api::_()->core()->getSubject();
            $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
            $db->beginTransaction();

            try {


                $subject->membership()
                        ->addMember($viewer)
                        ->setUserApproved($viewer)
                ;
                $oldRow = $row = $subject->membership()
                        ->getRow($viewer);
                $row = $subject->membership()
                        ->getRow($viewer);

                $row->rsvp = $form->getValue('rsvp');
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
                if ($form->getValue('rsvp') == 2) {

                    if ($starttime <= $currentTime && $currentTime <= $endtime) {
                        $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_mid_join', null, array('occurrence_id' => $occurrence_id));
                    } else {
                        $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_join', null, array('occurrence_id' => $occurrence_id));
                    }

                    if ($action != null) {
                        Engine_Api::_()->getDbtable('actions', 'seaocore')->attachActivity($action, $subject);
                    }

                    //  }
//                     Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subject->getOwner(), $viewer, $subject, 'siteevent_join', array('occurrence_id' => $occurrence_id));
                    //START NOTIFICATION AND EMAIL WORK
                    Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $action, 'siteevent_join', 'SITEEVENT_JOIN_CREATENOTIFICATION_EMAIL', null, $occurrence_id, 'joined', $viewer, 3, $viewer);
                    $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
                    if (!empty($isChildIdLeader)) {
                        Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_join');
                    }
                    //END NOTIFICATION AND EMAIL WORK
                }

                //START NOTIFICATION AND EMAIL WORK
                if ($form->getValue('rsvp') == 2 && ($oldRow->rsvp != 2)) {
                    Engine_Api::_()->siteevent()->joinEventNotifications($subject, 'siteevent_notification_send');
                    Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $subject, 'siteevent_rsvp_change', 'SITEEVENT_RSVP_CHANGENOTIFICATION_EMAIL', null, $occurrence_id, 'rsvp', $viewer, $row->rsvp, $viewer);
                    $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
                    if (!empty($isChildIdLeader)) {
                        Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_rsvp_change');
                    }
                } elseif ($form->getValue('rsvp') == 0 || $form->getValue('rsvp') == 1) {

                    if ($form->getValue('rsvp') == 1) {
                        if ($starttime <= $currentTime && $currentTime <= $endtime) {
                            $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_mid_maybe', null, array('occurrence_id' => $occurrence_id));
                        } elseif ($currentTime <= $endtime) {
                            $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_maybe_join', null, array('occurrence_id' => $occurrence_id));
                        }
                    }

                    if ($form->getValue('rsvp') == 0) {
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
                    Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $subject, 'siteevent_rsvp_change', 'SITEEVENT_RSVP_CHANGENOTIFICATION_EMAIL', null, $occurrence_id, 'rsvp', $viewer, $form->getValue('rsvp'), $viewer);
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
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $params = array('occurrence_id' => $occurrence_id, 'tab' => $tabId);
            if (!$tabId)
                unset($params['tab']);

            $messages = 'You have successfully joined this event.';
            if (!empty($waitlist_id)) {
                $messages = 'Waitlist user have successfully joined this event.';
            }

            return $this->_forward('success', 'utility', 'core', array(
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_($messages)),
                        'layout' => 'default-simple',
                        'parentRedirect' => $subject->getHref($params),
                        'parentRedirectTime' => 10,
            ));
        }
    }

    public function requestAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject()->isValid())
            return;

        $this->_helper->layout->setLayout('default-simple');

        $occurrence_id = $this->_getParam('occurrence_id', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);
        // Make form
        $this->view->form = $form = new Siteevent_Form_Member_Request();

        // Process form
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $subject = Engine_Api::_()->core()->getSubject();
            $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $subject->membership()->addMember($viewer)->setUserApproved($viewer);

                // Add notification
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $notifyApi->addNotification($subject->getOwner(), $viewer, $subject, 'siteevent_approve', array('occurrence_id' => $occurrence_id));

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_forward('success', 'utility', 'core', array(
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your invite request has been sent.')),
                        'layout' => 'default-simple',
                        'parentRedirect' => $subject->getHref(array('occurrence_id' => $occurrence_id)),
                        'parentRedirectTime' => 10,
            ));
        }
    }

    public function cancelAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject()->isValid())
            return;

        // Make form
        $this->view->form = $form = new Siteevent_Form_Member_Cancel();

        // Process form
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
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
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_forward('success', 'utility', 'core', array(
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your invite request has been cancelled.')),
                        'layout' => 'default-simple',
                        'parentRefresh' => true,
            ));
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
        $tabId = $this->_getParam('tab', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);
//        if ($subject->isOwner($viewer))
//            return;
        // Make form
        $this->view->form = $form = new Siteevent_Form_Member_Leave();

        if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
            $tempCondition = $this->getRequest()->isPost();
        } else {
            $tempCondition = $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost());
        }

        // Process form
        if ($tempCondition) {

            $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $subject->membership()->removeMember($viewer);

                // remove from leader list if the member is no longer member of any event.
                if (!$subject->membership()->isEventMember($viewer, true)) {
                    $list = $subject->getLeaderList();
                    $list->remove($viewer);
                }

                //UPDATE THE MEMBER COUNT IN EVENT TABLE
                $member_count = $subject->membership()->getMemberCount();
                $subject->member_count = $member_count;
                $subject->save();
                Engine_Api::_()->siteevent()->deleteFeedNotifications('{"occurrence_id":"' . $occurrence_id . '"}', $subject);
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $params = array('occurrence_id' => $occurrence_id, 'tab' => $tabId);
            if (!$tabId)
                unset($params['tab']);
            return $this->_forward('success', 'utility', 'core', array(
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You are no longer a guest of this event.')),
                        'layout' => 'default-simple',
                        'parentRedirect' => $subject->getHref($params),
                        'parentRedirectTime' => 10
            ));
        }
    }

    public function acceptAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
            return;

        $tabId = $this->_getParam('tab', null);
        $subject = Engine_Api::_()->core()->getSubject();
        //SAVE THE OCCURRENCE ID IN THE ZEND REGISTRY.
        $occurrence_id = $this->_getParam('occurrence_id', '');
        if (empty($occurrence_id) || !is_numeric($occurrence_id)) {
            //GET THE NEXT UPCOMING OCCURRENCE ID
            $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($subject->getIdentity());
        }

        Zend_Registry::set('occurrence_id', $occurrence_id);

        // Make form
        $this->view->form = $form = new Siteevent_Form_Member_Join();
        $formValid = false;
        $isAjax = (int) $this->_getParam('isAjax');
        if ($isAjax) {
            $formValid = true;
        } else {
            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $formValid = true;
            }
        }


        if (!$formValid) {
            $this->view->status = false;
            $this->view->error = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
            return;
        }

        // Process form
        $viewer = Engine_Api::_()->user()->getViewer();
        //$subject = Engine_Api::_()->core()->getSubject();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {

            $subject->membership()->setUserApproved($viewer);

            $row = $subject->membership()
                    ->getRow($viewer);
            $oldRow = $subject->membership()
                    ->getRow($viewer);
            $row->rsvp = $form->getValue('rsvp');
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
            if ($form->getValue('rsvp') == 2 && ($oldRow->rsvp != 2)) {
                Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $subject, 'siteevent_rsvp_change', 'SITEEVENT_RSVP_CHANGENOTIFICATION_EMAIL', null, $occurrence_id, 'rsvp', $viewer, $form->getValue('rsvp'));
                $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
                if (!empty($isChildIdLeader)) {
                    Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_rsvp_change');
                }
            } elseif ($form->getValue('rsvp') == 0 || $form->getValue('rsvp') == 1) {
                Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $subject, 'siteevent_rsvp_change', 'SITEEVENT_RSVP_CHANGENOTIFICATION_EMAIL', null, $occurrence_id, 'rsvp', $viewer, $form->getValue('rsvp'));
                $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
                if (!empty($isChildIdLeader)) {
                    Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_rsvp_change');
                }
            }
            //END NOTIFICATION AND EMAIL WORK

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->error = false;

        $message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the event %s');
        $message = sprintf($message, $subject->__toString());
        $this->view->message = $message;
        $params = array('occurrence_id' => $occurrence_id, 'tab' => $tabId);
        if (!$tabId)
            unset($params['tab']);
        if ($this->_helper->contextSwitch->getCurrentContext() == "smoothbox") {
            return $this->_forward('success', 'utility', 'core', array(
                        'messages' => array($message),
                        'layout' => 'default-simple',
                        'parentRedirect' => $subject->getHref($params),
                        'parentRedirectTime' => 10,
            ));
        }
    }

    public function rejectAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
            return;
        $occurrence_id = $this->_getParam('occurrence_id', null);
        $tabId = $this->_getParam('tab', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);
        // Make form
        $this->view->form = $form = new Siteevent_Form_Member_Reject();

        $formValid = false;
        $isAjax = (int) $this->_getParam('isAjax');
        if ($isAjax) {
            $formValid = true;
        } else {
            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $formValid = true;
            }
        }


        if (!$formValid) {
            $this->view->status = false;
            $this->view->error = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
            return;
        }

        // Process form
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            if ($subject->membership()->hasMembers($viewer, $subject->getIdentity(), 'Reject'))
                $subject->membership()->removeMember($viewer);

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

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->error = false;
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite')) {
            $message = Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the event %s.');
        } else {
            $message = Zend_Registry::get('Zend_Translate')->_('You have ignored the request for the visit and explore the event %s.');
        }

        $message = sprintf($message, $subject->__toString());
        $this->view->message = $message;
        $params = array('occurrence_id' => $occurrence_id, 'tab' => $tabId);
        if (!$tabId)
            unset($params['tab']);
        if ($this->_helper->contextSwitch->getCurrentContext() == "smoothbox") {
            return $this->_forward('success', 'utility', 'core', array(
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Event invite rejected')),
                        'layout' => 'default-simple',
                        'parentRedirect' => $subject->getHref($params),
                        'parentRedirectTime' => 10,
            ));
        }
    }

    public function removeAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject()->isValid())
            return;

        // Get user
        if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            return $this->_helper->requireSubject->forward();
        }

        $event = $subject = Engine_Api::_()->core()->getSubject();
        $widgetIgnoreRequest = $this->_getParam('ignore_request', 0);
        if (!$event->membership()->isMember($user)) {
            throw new Event_Model_Exception('Cannot remove a non-member');
        }
        $occurrence_id = $this->_getParam('occurrence_id', null);
        $tabId = $this->_getParam('tab', null);
        // Make form
        $this->view->form = $form = new Siteevent_Form_Member_Remove();
        $formValid = false;
        $isAjax = (int) $this->_getParam('isAjax');
        if ($isAjax) {
            $formValid = true;
        } else {
            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $formValid = true;
            }
        }

        if (!$formValid) {
            $this->view->status = false;
            $this->view->error = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
            return;
        }

        // Process form
        if ($formValid) {
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

                if (!$widgetIgnoreRequest) {
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $event, 'siteevent_guest_removed', array('occurrence_id' => $occurrence_id));

                    $subjectOwner = $subject->getOwner();
                    $host = $_SERVER['HTTP_HOST'];
                    $newVar = _ENGINE_SSL ? 'https://' : 'http://';
                    $object_link = $newVar . $host . $subject->getHref();
                    $viewerGetTitle = $subjectOwner->getTitle();
                    $subjectTitle = $subject->getTitle();
                    $sender_link = '<a href=' . $newVar . $host . $subject->getOwner()->getHref() . ">$viewerGetTitle</a>";
                    $object_title_with_link = '<a href=' . $newVar . $host . $subject->getHref() . ">$subjectTitle</a>";

                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'SITEEVENT_GUEST_REMOVED', array(
                        'sender' => $sender_link,
                        'object_link' => $object_link,
                        'object_title' => $subject->getTitle(),
                        'object_description' => $subject->getDescription(),
                        'object_title_with_link' => $object_title_with_link,
                        'queue' => false
                    ));
                } else {
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $event, 'siteevent_request_disapprove', array('occurrence_id' => $occurrence_id));
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $params = array('occurrence_id' => $occurrence_id, 'tab' => $tabId);
            if (!$tabId)
                unset($params['tab']);

            $this->view->status = true;
            $this->view->error = false;
            $message = Zend_Registry::get('Zend_Translate')->_('Event member request removed.');
            $message = sprintf($message, $subject->__toString());
            $this->view->message = $message;

            if ($this->_helper->contextSwitch->getCurrentContext() == "smoothbox") {
                return $this->_forward('success', 'utility', 'core', array(
                            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Event member removed.')),
                            'layout' => 'default-simple',
                            'parentRedirect' => $subject->getHref($params),
                            'parentRedirectTime' => 10,
                ));
            }
        }
    }

    public function inviteAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
            return;

        // @todo auth
        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->event = $event = $subject = Engine_Api::_()->core()->getSubject();
        //GET THE LEADERS LIST AND CHECK IF THE VIEWER IS LEADER OR NORMAL USER.
        if ($event->owner_id == $viewer->getIdentity()) {
            $isLeader = 1;
        } else {
            $this->view->list = $list = $event->getLeaderList();

            $listItem = $list->get($viewer);
            $isLeader = ( null !== $listItem );
        }
        $this->view->occurrence_id = $occurrence_id = $this->_getParam('occurrence_id', null);
        $tabId = $this->_getParam('tab', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);
        // Prepare form
        $this->view->form = $form = new Siteevent_Form_Invite(array('isLeader' => $isLeader));
        $this->view->isLeader = $isLeader;
        //PUT CHECK FOR EVENT ADMIN.
        // Prepare friends
        if (!$isLeader) {
            $friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
            $friendsIds = $friendsTable->select()
                    ->from($friendsTable, 'user_id')
                    ->where('resource_id = ?', $viewer->getIdentity())
                    ->where('active = ?', true)
                    ->limit(100)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
            if (!empty($friendsIds)) {
                $friends = Engine_Api::_()->getItemTable('user')->find($friendsIds);
            } else {
                $friends = array();
            }
            $this->view->friends = $friends;

            $count = 0;
            foreach ($friends as $friend) {
                if ($event->membership()->isMember($friend, null)) {
                    continue;
                }
                $multiOptions[$friend->getIdentity()] = $friend->getTitle();
                $count++;
            }
            sort($multiOptions);
            $form->users->addMultiOptions($multiOptions);
            $this->view->count = $count;
        }
        // Not posting
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }


        // Process
        $table = $event->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            if ($isLeader) {
                $usersIds = $form->getValue('toValues');
                $usersIds = explode(',', $usersIds);
                //GET ALL THE SITE MEMBERS WHO ARE NOT YET MEMBERS OF THIS EVENT.
                $friends = Engine_Api::_()->siteevent()->getMembers($event->event_id, $occurrence_id, '');
            } else
                $usersIds = $form->getValue('users');

            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $friendsToJoin = array();
            foreach ($usersIds as $user_id) {
                $user = Engine_Api::_()->getItem('user', $user_id);
                if (null === $user || !isset($user->email)) {
                    continue;
                }
                $friendsToJoin[] = $user->email . '#' . $user->displayname;
                $event->membership()->addMember($user)
                        ->setResourceApproved($user);

                if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite'))
                    $notifyApi->addNotification($user, $viewer, $event, 'siteevent_invite', array('occurrence_id' => $occurrence_id));
            }

            //SEND MAIL NOTIFICATION AS WELL
            if (!empty($friendsToJoin) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite') && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode'))
                Engine_Api::_()->getApi('Invite', 'Seaocore')->sendPageInvites($friendsToJoin, $event->event_id, 'siteevent', '', 'siteevent_event');



            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $params = array('occurrence_id' => $occurrence_id, 'tab' => $tabId);
        if (!$tabId)
            unset($params['tab']);
        return $this->_forward('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Members invited')),
                    'layout' => 'default-simple',
                    'parentRedirect' => $subject->getHref($params),
                    'parentRedirectTime' => 10
        ));
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

        // Make form
        $this->view->form = $form = new Siteevent_Form_Member_Approve();
        $occurrence_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('occurrence_id');
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id');

        Zend_Registry::set('occurrence_id', $occurrence_id);
        $formValid = false;
        $isAjax = (int) $this->_getParam('isAjax');
        if ($isAjax) {
            $formValid = true;
        } else {
            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $formValid = true;
            }
        }

        if (!$formValid) {
            $this->view->status = false;
            $this->view->error = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
            return;
        }

        // Process form
        if ($formValid) {
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

                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $subject, 'siteevent_accepted', array('occurrence_id' => $occurrence_id));

                // Set the request as handled
                $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                        $viewer, $subject, 'siteevent_approve', array('occurrence_id' => $occurrence_id));
                if ($notification) {
                    $notification->mitigated = true;
                    $notification->read = true;
                    $notification->save();
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }


            $this->view->status = true;
            $this->view->error = false;

            $message = Zend_Registry::get('Zend_Translate')->_("You have accepted the request to join the event %s");
            $message = sprintf($message, $subject->__toString());
            $this->view->message = $message;
            if ($this->_helper->contextSwitch->getCurrentContext() == "smoothbox") {
                return $this->_forward('success', 'utility', 'core', array(
                            'messages' => array($message),
                            'layout' => 'default-simple',
                            'parentRefresh' => true,
                ));
            }
        }
    }

    public function exportExcelAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->_helper->layout->setLayout('default-simple');

        $this->view->event = $event = Engine_Api::_()->getItem('siteevent_event', $this->_getParam('event_id'));
        $eventDates['starttime'] = $this->view->eventStarttime = $eventStarttime = $this->_getParam('starttime', null);
        $eventDates['endtime'] = $this->view->eventEndtime = $eventEndtime = $this->_getParam('endtime', null);

        $this->view->occurrence_id = $occurrence_id = $this->_getParam('occurrence_id', null);
        if ($this->_getParam('starttime', null)) {
            $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');
            $eventDates['starttime'] = $this->view->locale()->toEventDateTime($this->_getParam('starttime', null), array('size' => $datetimeFormat));
            $eventDates['endtime'] = $this->view->locale()->toEventDateTime($this->_getParam('endtime', null), array('size' => $datetimeFormat));
        } else
            $eventDates = $event->getStartEndDate($occurrence_id);
        $this->view->eventDates = $eventDates;

        $viewer = Engine_Api::_()->user()->getViewer();
        $canEdit = $event->authorization()->isAllowed($viewer, "edit");

        if (!$event->isOwner($viewer) && $viewer->level_id != 1 && !$canEdit) {
            return;
        }

        $select = $event->membership()->getMembersObjectSelect();
        $SiteEventMembershiptable = Engine_Api::_()->getDbTable('membership', 'siteevent');
        $siteeventMembershipTableName = $SiteEventMembershiptable->info('name');
        if ($occurrence_id) {
            $select->where('`' . $siteeventMembershipTableName . '`.`occurrence_id` = ?', $occurrence_id);
        } elseif ($this->_getParam('starttime', null)) {
            $eventDates = Engine_Api::_()->siteevent()->userToDbDateTime($eventDates);
            $SiteEventOccuretable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
            $siteeventOccurTableName = $SiteEventOccuretable->info('name');
            $select->join($siteeventOccurTableName, "$siteeventOccurTableName.occurrence_id = $siteeventMembershipTableName.occurrence_id", array())
                    ->where('`' . $siteeventOccurTableName . '`.`starttime` >= ?', $eventStarttime)
                    ->where('`' . $siteeventOccurTableName . '`.`starttime` <= ?', $eventEndtime);
        }

        $this->view->guestDetails = $guestDetails = Engine_Api::_()->getDbTable('users', 'user')->fetchAll($select);
    }

    //ACTION FOR MESSAGING THE EVENT OWNER
    public function notifyGuestAction() {

        //LOGGED IN USER CAN SEND THE MESSAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam("event_id");
        $event = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //OWNER CANT SEND A MESSAGE TO HIMSELF
        //GET THE ORGANIZER ID TO WHOM THE MESSAGE HAS TO BE SEND
        $organizer_id = $this->_getParam("host_id");
        if ($viewer_id == $organizer_id) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Member_Notify();
        $form->setDescription('Create your message with the form given below.');
        $form->removeElement('to');
        $form->toValues->setValue($organizer_id);

        //CHECK METHOD/DATA
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();

        try {
            $values = $this->getRequest()->getPost();

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

            //LIMIT RECIPIENTS IF IT IS NOT A SPECIAL SITEEVENT OF MEMBERS
            $recipients = array_slice($recipients, 0, 1000);

            //CLEAN THE RECIPIENTS FOR REPEATING IDS
            $recipients = array_unique($recipients);

            $user = Engine_Api::_()->getItem('user', $organizer_id);

            $event_title = $event->title;
            $http = _ENGINE_SSL ? 'https://' : 'http://';
            $event_title_with_link = '<a href =' . $http . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $event_id, 'slug' => $event->getSlug()), "siteevent_entry_view") . ">$event_title</a>";

            $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . $this->view->translate('This message corresponds to the Event: %s', $event_title_with_link));

            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $conversation, 'message_new');

            //INCREMENT MESSAGE COUNTER
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

            $db->commit();

            return $this->_forwardCustom('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //USE FOR COMPOSE THE MESSAGE.
    public function composeAction() {

        $multi = 'member';
        $multi_ids = '';

        $tab_selected_id = $this->_getParam('tab');
        $occurrence_id = $this->_getParam('occurrence_id', 'all');
        if (empty($occurrence_id))
            $occurrence_id = 'all';

        $this->view->event_id = $event_id = $this->_getParam("event_id");
        $this->view->event = $event = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $viewer = Engine_Api::_()->user()->getViewer();
        $canEdit = $event->authorization()->isAllowed($viewer, "edit");

        if (!$event->isOwner($viewer) && $viewer->level_id != 1 && !$canEdit) {
            return;
        }

        $this->view->form = $form = new Siteevent_Form_Compose(array('occurrenceid' => $occurrence_id));

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
            foreach ($recipientsUsers as $user) {
                if ($user->getIdentity() == $viewer->getIdentity()) {
                    continue;
                }
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $conversation, 'message_new');
            }

            //Increment messages counter
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
            $db->commit();

            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        //'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR USER AUTO SUGGEST.
    public function getGuestsAction() {

        $data = array();
        $text = $this->_getParam('searchGuests', null);
        $event_id = $this->_getParam('event_id', null);
        $occurrence_id = $this->_getParam('occurrence_id', 'all');
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $usersTable = Engine_Api::_()->getDbtable('users', 'user');
        $usersTableName = $usersTable->info('name');

        if (Engine_Api::_()->siteevent()->isTicketBasedEvent()) {

            $orderTable = Engine_Api::_()->getDbTable('orders', 'siteeventticket');
            $orderTableName = $orderTable->info('name');

            $select = $orderTable->select()
                    ->from($orderTableName, array("user_id"))
                    ->where("$orderTableName.event_id =?", $event_id)
                    ->where($orderTableName . '.user_id != ?', $viewer_id)
                    ->group("$orderTableName.user_id");
        } else {
            $membershipTable = Engine_Api::_()->getDbtable('membership', 'siteevent');
            $membershipTableName = $membershipTable->info('name');
            $select = $membershipTable->select()
                    ->from($membershipTableName, 'user_id')
                    ->where('resource_id = ?', $event_id)
                    ->where('active = ?', 1)
                    ->where('user_id != ?', $viewer_id);
        }

        if ($occurrence_id != 'all') {
            $select->where('occurrence_id = ?', $occurrence_id);
        }

        $searchGuests = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        $select = $usersTable->select()
                ->where('displayname  LIKE ? OR username LIKE ?', '%' . $text . '%')
                ->where($usersTableName . '.user_id IN (?)', (array) $searchGuests)
                ->order('displayname ASC')
                ->limit('40');
        $users = $usersTable->fetchAll($select);

        foreach ($users as $user) {
            $user_photo = $this->view->itemPhoto($user, 'thumb.icon');
            $data[] = array(
                'id' => $user->user_id,
                'label' => $user->displayname,
                'photo' => $user_photo
            );
        }
        return $this->_helper->json($data);
    }

    public function promoteAction() {

        $multipleLeaderSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.leader', 1);

        if (empty($multipleLeaderSetting)) {
            return;
        }

        // Get user
        if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            return $this->_helper->requireSubject->forward();
        }

        $siteevent = Engine_Api::_()->core()->getSubject();
        $list = $siteevent->getLeaderList();
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$siteevent->membership()->isMember($user)) {
            throw new Siteevent_Model_Exception('Cannot add a non-member as a leader');
        }

        $this->view->form = $form = new Siteevent_Form_Member_Promote();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

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
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Member becomes leader')),
                    'layout' => 'default-simple',
                    'parentRefresh' => true,
        ));
    }

    public function demoteAction() {

        $multipleLeaderSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.leader', 1);

        if (empty($multipleLeaderSetting)) {
            return;
        }

        // Get user
        if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            return $this->_helper->requireSubject->forward();
        }

        $siteevent = Engine_Api::_()->core()->getSubject();
        $list = $siteevent->getLeaderList();

//        if (!$siteevent->membership()->isMember($user)) {
//            throw new Siteevent_Model_Exception('Cannot remove a non-member as a leader');
//        }

        $this->view->form = $form = new Siteevent_Form_Member_Demote();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = $list->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $list->remove($user);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Removed as Leader')),
                    'layout' => 'default-simple',
                    'parentRefresh' => true,
        ));
    }

    //ACTION FOR USER AUTO SUGGEST.
    public function getmembersAction() {

        $data = array();

        //GET COUPON ID.
        $event_id = $this->_getParam('event_id', null);
        $occurrence_id = $this->_getParam('occurrence_id', null);

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $text = $this->_getParam('user_ids', null);
        } else { // Added for mobile plugin autocompleter
            $text = $this->_getParam('search', null);
        }

        $users = Engine_Api::_()->siteevent()->getMembers($event_id, $occurrence_id, $text);
        foreach ($users as $user) {
            $user_photo = $this->view->itemPhoto($user, 'thumb.icon');
            $data[] = array(
                'id' => $user->user_id,
                'label' => $user->displayname,
                'photo' => $user_photo
            );
        }

        return $this->_helper->json($data);
    }

    public function manageLeadersAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET EVENT ID
        $this->view->event_id = $event_id = $this->_getParam('event_id');

        //GET SITEGEVENT ITEM
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return $this->_forward('requireauth', 'error', 'core');
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
            return $this->_forward('requireauth', 'error', 'core');
        }

        if ($itemTypeValue == 'user' && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.leader', 1)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $this->view->TabActive = 'leaders';

        $this->view->list = $list = $siteevent->getLeaderList();
        $list_id = $list['list_id'];

        $listItemTable = Engine_Api::_()->getDbTable('listItems', 'siteevent');
        $listItemTableName = $listItemTable->info('name');

        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $userTable->info('name');

        if ($this->getRequest()->isPost()) {

            $values = $this->getRequest()->getPost();
            $user_id = $values['user_id'];

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
                    $notifyApi->addNotification($user, $viewer, $siteevent, 'siteevent_promote');

                    // Add activity
                    $activityApi = Engine_Api::_()->getDbtable('actions', 'seaocore');
                    $activityApi->addActivity($user, $siteevent, 'siteevent_promote');

                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            }
        }
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

        $this->view->members = $userTable->fetchAll($select);
    }

    //ACTINO FOR USER AUTO-SUGGEST LIST
    public function manageAutoSuggestAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GETTING THE PAGE ID.
        $event_id = $this->_getParam('event_id', $this->_getParam('id', null));
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $list = $siteevent->getLeaderList();

        $list_id = $list['list_id'];
        $text = $this->_getParam('text', null);

        $listItemTable = Engine_Api::_()->getDbTable('listItems', 'siteevent');
        $listItemTableName = $listItemTable->info('name');

        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $userTable->info('name');

        $membershipTable = Engine_Api::_()->getDbtable('membership', 'siteevent');
        $membershipTableName = $membershipTable->info('name');

        $selectLeaders = $listItemTable->select()
                ->from($listItemTableName, array('child_id'))
                ->where("list_id = ?", $list_id)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
        $selectLeaders[] = $siteevent->owner_id;

        $select = $userTable->select()
                ->setIntegrityCheck(false)
                ->from($userTableName);
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventticket'))
            $select = $select->join($membershipTableName, "$membershipTableName.user_id = $userTableName.user_id", array('resource_id'));

        $select = $select->where("$userTableName.user_id NOT IN (?)", (array) $selectLeaders);

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventticket'))
            $select = $select->where('resource_id = ?', $event_id);

        $select = $select->where($userTableName . ".displayname LIKE ? OR " . $userTableName . ".username LIKE ? OR " . $userTableName . ".email LIKE ?", '%' . $text . '%')
                ->group("$userTableName.user_id")
                ->order('displayname ASC')
                ->limit(20);

        //FETCH ALL RESULT.
        $userlists = $userTable->fetchAll($select);
        $data = array();

        foreach ($userlists as $userlist) {
            $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
            $data[] = array(
                'id' => $userlist->user_id,
                'label' => $userlist->displayname,
                'photo' => $content_photo
            );
        }

        if ($this->_getParam('sendNow', true)) {

            //RETURN TO THE RETRIVE RESULT.
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }
    }

    //CHECK IF THE OCCURRENCE HAS GUEST OR NOT.
    public function getMemberCountAction() {

        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        $occurrence_id = $this->_getParam('occurrence_id', 'all');
        $rsvp = $this->_getParam('rsvp', 'all');
        $other_Conditions = array();
        if (is_numeric($occurrence_id))
            $other_Conditions['occurrence_id'] = $occurrence_id;
        if ($rsvp == 3 || $rsvp == 4)
            $other_Conditions['rsvp !'] = 3;
        else
            $other_Conditions['rsvp'] = $rsvp;
        $this->view->member_count = $siteevent->membership()->getMemberCount(true, $other_Conditions);
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }

    public function confirmAction() {
        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject()->isValid())
            return;

        // Get user
        if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            return $this->_helper->requireSubject->forward();
        }

        $event = $subject = Engine_Api::_()->core()->getSubject();

        if (!$event->membership()->isMember($user)) {
            throw new Event_Model_Exception('Cannot confirm a non-member');
        }
        $row = $subject->membership()->getRow($user);

        $occurrence_id = $this->_getParam('occurrence_id', null);
        $tabId = $this->_getParam('tab', null);
        // Make form
        $this->view->form = $form = new Siteevent_Form_Member_Confirm(array('confirm' => $row->confirm));

        // Process form
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $db = $event->membership()->getReceiver()->getTable()->getAdapter();
            $db->beginTransaction();

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
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $params = array('occurrence_id' => $occurrence_id, 'tab' => $tabId);
            if (!$tabId)
                unset($params['tab']);

            return $this->_forward('success', 'utility', 'core', array(
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_("$message")),
                        'layout' => 'default-simple',
                        'parentRedirect' => $subject->getHref($params),
                        'parentRedirectTime' => 10,
            ));
        }
    }

}
