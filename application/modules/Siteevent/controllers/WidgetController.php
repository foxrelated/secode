<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WidgetController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_WidgetController extends Core_Controller_Action_Standard {

    public function profileInfoAction() {
        // Don't render this if not authorized
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid())
            return $this->_helper->viewRenderer->setNoRender(true);
    }

    public function profileRsvpAction() {

        $this->view->form = new Siteevent_Form_Rsvp();
        $subject = $event = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        $occurrence_id = $this->getRequest()->getParam('occurrence_id');
        Zend_Registry::set('occurrence_id', $occurrence_id);
        if (!$event->membership()->isMember($viewer, true)) {
            return;
        }
        $row = $event->membership()->getRow($viewer);
        $this->view->viewer_id = $viewer->getIdentity();
        if ($row) {
            $this->view->rsvp = $row->rsvp;
        } else {
            return $this->_helper->viewRenderer->setNoRender(true);
        }
        if ($this->getRequest()->isPost()) {
            $option_id = $this->getRequest()->getParam('option_id');

            $row->rsvp = $option_id;
            $row->save();
        }
        Engine_Api::_()->siteevent()->deleteFeedNotifications('{"occurrence_id":"' . $occurrence_id . '"}', $subject);
        if ($option_id == 2) {
            $occurrenceItem = Engine_Api::_()->getItem('siteevent_occurrence', $occurrence_id);
            $currentTime = time();
            $starttime = strtotime($occurrenceItem->starttime);
            $endtime = strtotime($occurrenceItem->endtime);
            if ($starttime <= $currentTime && $currentTime <= $endtime) {
                $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_join', null, array('occurrence_id' => $occurrence_id));
            } else {
                $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_mid_join', null, array('occurrence_id' => $occurrence_id));
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

        //START NOTIFICATION AND EMAIL WORK
        Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $subject, 'siteevent_rsvp_change', 'SITEEVENT_RSVP_CHANGENOTIFICATION_EMAIL', null, $occurrence_id, 'rsvp', $viewer, $option_id);
        $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
        if (!empty($isChildIdLeader)) {
            Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_rsvp_change');
        }
        //END NOTIFICATION AND EMAIL WORK
    }

    public function requestEventAction() {
        $this->view->notification = $notification = $this->_getParam('notification');
        //$this->view->occurrence_id = '';
       // if(isset($notification->params) && isset($notification->params['occurrence_id'])) {
         //   $this->view->occurrence_id = $notification->params['occurrence_id'];
        //}
    }

    public function approveEventAction() {
        $this->view->notification = $notification = $this->_getParam('notification');
        $this->view->occurrence_id = '';
        if(isset($notification->params) && isset($notification->params['occurrence_id'])) {
            $this->view->occurrence_id = $notification->params['occurrence_id'];
        }
    }    
    
    public function profileRsvpAjaxAction() {

        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$subject->membership()->isMember($viewer, true)) {
            return;
        }
        $occurrence_id = $this->getRequest()->getParam('occurrence_id');
        Zend_Registry::set('occurrence_id', $occurrence_id);
        $row = $subject->membership()->getRow($viewer);

        $this->view->viewer_id = $viewer->getIdentity();
        if ($row) {
            $this->view->rsvp = $row->rsvp;
        } else {
            return $this->_helper->viewRenderer->setNoRender(true);
        }
        $option_id = $this->getRequest()->getParam('option_id');
        $row->rsvp = $option_id;
        $row->save();
        Engine_Api::_()->siteevent()->deleteFeedNotifications('{"occurrence_id":"' . $occurrence_id . '"}', $subject);
        if ($option_id == 2) {
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

        }

        //START NOTIFICATION AND EMAIL WORK
        Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $subject, 'siteevent_rsvp_change', 'SITEEVENT_RSVP_CHANGENOTIFICATION_EMAIL', null, $occurrence_id, 'rsvp', $viewer, $option_id);
        $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
        if (!empty($isChildIdLeader)) {
            Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_rsvp_change');
        }
        //END NOTIFICATION AND EMAIL WORK

        //$this->view->status = true;
        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
        }
    }

    public function sendActivityAction() {

        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        $body = $this->getRequest()->getParam('body');
        $reason = $this->getRequest()->getParam('reason');
        $occurrence_id = $this->getRequest()->getParam('occurrence_id');
        Zend_Registry::set('occurrence_id', $occurrence_id);
        Engine_Api::_()->siteevent()->deleteFeedNotifications('{"occurrence_id":"' . $occurrence_id . '"}', $subject);
        $occurrenceItem = Engine_Api::_()->getItem('siteevent_occurrence', $occurrence_id);
        $currentTime = time();
        $starttime = strtotime($occurrenceItem->starttime);
        $endtime = strtotime($occurrenceItem->endtime);
        if ($reason == 1 && $currentTime <= $endtime) {
					
            if ($starttime <= $currentTime && $currentTime <= $endtime) {
                $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_mid_join', $body, array('occurrence_id' => $occurrence_id));
            } else {
                $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_join', $body, array('occurrence_id' => $occurrence_id));
            }
        } elseif ($reason == 2 && $currentTime <= $endtime) {
            if ($starttime <= $currentTime && $currentTime <= $endtime) {
                $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_mid_leave', $body, array('occurrence_id' => $occurrence_id));
            } else {
                $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_leave', $body, array('occurrence_id' => $occurrence_id));
            }
        } elseif ($reason == 3 && $currentTime <= $endtime) {
            if ($starttime <= $currentTime && $currentTime <= $endtime) {
                $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_mid_maybe', $body, array('occurrence_id' => $occurrence_id));
            } else {
                $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $subject, 'siteevent_maybe_join', $body, array('occurrence_id' => $occurrence_id));
            }
        }

					if ($action != null) {
							Engine_Api::_()->getDbtable('actions', 'seaocore')->attachActivity($action, $subject);
					}
    }

    
    public function checkEventFullAction() {
        
        $occurrence_id = $this->getRequest()->getParam('occurrence_id');

        $siteevent = Engine_Api::_()->getItem('siteevent_occurrence', $occurrence_id)->getParent();
        
        $this->view->eventCapacity = $siteevent->isEventFull(array('occurrence_id' => $occurrence_id));
    }
}