<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddToDiary.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_View_Helper_EventLinks extends Zend_View_Helper_Abstract {

    public function eventLinks($subject) {

        $viewer = Engine_Api::_()->user()->getViewer();
        if ($subject->getType() !== 'siteevent_event') {
            throw new Event_Model_Exception('Whoops, not an event!');
        }
        $isTicketBasedEvent = Engine_Api::_()->siteevent()->isTicketBasedEvent();
        if (!$isTicketBasedEvent && !$viewer->getIdentity()) {
            return false;
        }
        $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
        $row = $subject->membership()->getRow($viewer);
        //CHECK IF THE EVENT IS PAST EVENT THEN WE WILL NOT SHOW JOIN EVENT LINK.
			  $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($subject->event_id, 'DESC', $occurrence_id);
              
        $isEventFull = $subject->isEventFull(array('occurrence_id' => $occurrence_id));
        
        if($isTicketBasedEvent && Engine_Api::_()->siteeventticket()->bookNowButton($subject) && ($subject->isRepeatEvent() || (!$subject->isRepeatEvent() && !$isEventFull))){   
            $url = $this->view->url(array('action' => 'buy', 'event_id' => $subject->event_id, 'occurrence_id' => $occurrence_id), "siteeventticket_ticket", true);
            return "<a  id='siteevent_member_" . $subject->getIdentity() . "' href='$url'><span>" .$this->view->translate('Book Now') . "</span></a>";
        }              
        $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $occurrence_id);      
        if(!$isTicketBasedEvent) {      
            // Not yet associated at all
            if (null === $row && !$isEventFull) { 

                            if(!empty($occurrence->waitlist_flag)) {
                                return;
                            }
                
                            if (strtotime($endDate) < time())
                                    return;
                if ($subject->membership()->isResourceApprovalRequired()) {
                    return "<a  id='siteevent_member_" . $subject->getIdentity() . "' href='javascript:void(0);'
    onclick='en4.siteevent.member.request(" . $subject->getIdentity() . ", " . $occurrence_id . ")'><span>" .
                            $this->view->translate('Request Invite') . "</span></a>";
                } elseif(!$isEventFull) {               

                    return "<a  id='siteevent_member_" . $subject->getIdentity() . "' href='javascript:void(0);'
    onclick='en4.siteevent.member.join(" . $subject->getIdentity() . ", " . $occurrence_id . ")'><span>" .
                            $this->view->translate('Join Event') . "</span></a>";
                }
            }
            // Full member
            // @todo consider owner
            else if ($row->active) {
                //if (!$subject->isOwner($viewer)) {
                return "<a  id='siteevent_member_" . $subject->getIdentity() . "' href='javascript:void(0);'
    onclick='en4.siteevent.member.leave(" . $subject->getIdentity() . ", " . $occurrence_id . ")'><span>" .
                        $this->view->translate('Leave Event') . "</span></a>";
                //	}
            } else if (!$row->resource_approved && $row->user_approved) {
                return "<a  id='siteevent_member_" . $subject->getIdentity() . "' href='javascript:void(0);'
    onclick='en4.siteevent.member.cancel(" . $subject->getIdentity() . ", " . $occurrence_id . ")'><span>" .
                        $this->view->translate('Cancel Invite Request') . "</span></a>";
            } else if (!$row->user_approved && $row->resource_approved) {
                if(strtotime($endDate) > time()) {
                    $accept = "<div class='fleft'><a  id='siteevent_member_" . $subject->getIdentity() . "' href='javascript:void(0);'
    onclick='en4.siteevent.member.accept(" . $subject->getIdentity() . ", " . $occurrence_id . ")'><span>" .
                        $this->view->translate('Accept Event Invite') . "</span></a></div>";

                    $reject =  "<div class='fleft mleft5'><a  id='siteevent_member_" . $subject->getIdentity() . "' href='javascript:void(0);'
    onclick='en4.siteevent.member.ignore(" . $subject->getIdentity() . ", " . $occurrence_id . ")'><span>" .
                        $this->view->translate('Ignore Event Invite') . "</span></a></div>";

                    return $accept . $reject;
                }
            } 
//            else {
//                throw new Event_Model_Exception('An error has occurred.');
//            }
        }
    }

}