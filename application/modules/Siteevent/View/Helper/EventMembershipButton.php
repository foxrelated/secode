<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ShowRatingStar.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_View_Helper_EventMembershipButton extends Zend_View_Helper_Abstract {

    /**
     * Assembles action string
     * 
     * @return string
     */
    public function eventMembershipButton($item, $params = array()) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($item->getType() !== 'siteevent_event' || !$viewer->getIdentity()) {
            return false;
        }
        $data = array();
        $occure_id = isset($params['occurrence_id']) ? $params['occurrence_id'] : 0;
        if (!$occure_id && isset($item->occurrence_id))
            $occure_id = $item->occurrence_id;
        $row = $item->membership()->getRow($viewer, array('occurrence_id' => $occure_id));
        //CHECK IF THE EVENT IS PAST EVENT THEN WE WILL NOT SHOW JOIN EVENT LINK.
			  $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($item->event_id, 'DESC', $occure_id);
              
        $isEventFull = $item->isEventFull(array('occurrence_id' => $occure_id));      
              
        if(Engine_Api::_()->siteevent()->isTicketBasedEvent()){
            
            if(!Engine_Api::_()->siteeventticket()->bookNowButton($item) || (!$item->isRepeatEvent() && $isEventFull)) {
                return $this->view->partial('_membershipButton.tpl', 'siteevent', $data);
            }            
            
            $data['links'][] = array(
                'label' => 'Book Now',
                'class' => 'icon_siteevents_invitejoin',
                'route' => 'siteeventticket_ticket',
                'params' => array(
                    'action' => 'buy',
                    'event_id' => $item->getIdentity(),
                    'occurrence_id' => $occure_id,
                ),
            );
            
            $data = array_merge($params, $data);
            
            return $this->view->partial('_membershipButton.tpl', 'siteevent', $data);
        }       
              
        // Not yet associated at all
        if (null === $row && !$isEventFull) { 
            
            $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $occure_id); 
            if(!empty($occurrence->waitlist_flag)) {
                return false;
            }
        
            if (strtotime($endDate) < time() || !$item->isViewableByNetwork())
                    return false;
                    
            if ($item->membership()->isResourceApprovalRequired()) {
                $data['links'][] = array(
                    'label' => 'Request Invite',
                    'class' => 'smoothbox icon_siteevents_invitejoin',
                    'route' => 'siteevent_extended',
                    'params' => array(
                        'controller' => 'member',
                        'action' => 'request',
                        'event_id' => $item->getIdentity(),
                        'occurrence_id' => $occure_id,
                    ),
                );
            } else {                

                $data['links'][] = array(
                    'label' => 'Join Event',
                    'class' => 'smoothbox icon_siteevents_invitejoin',
                    'route' => 'siteevent_extended',
                    'params' => array(
                        'controller' => 'member',
                        'action' => 'join',
                        'event_id' => $item->getIdentity(),
                        'occurrence_id' => $occure_id
                    ),
                );
            }
        }

        // Full member
        // @todo consider owner
        else if ($row->active) {
            //if (!$item->isOwner($viewer)) {
            $data['links'][] = array(
                'label' => 'Leave Event',
                'class' => 'smoothbox icon_siteevents_inviteleave',
                'route' => 'siteevent_extended',
                'params' => array(
                    'controller' => 'member',
                    'action' => 'leave',
                    'event_id' => $item->getIdentity(),
                    'occurrence_id' => $occure_id
                ),
            );
            // }
        } else if (!$row->resource_approved && $row->user_approved) {
            $data['links'][] = array(
                'label' => 'Cancel Invite Request',
                'class' => 'smoothbox icon_siteevents_invitecancel',
                'route' => 'siteevent_extended',
                'params' => array(
                    'controller' => 'member',
                    'action' => 'cancel',
                    'event_id' => $item->getIdentity(),
                    'occurrence_id' => $occure_id
                ),
            );
        } else if (!$row->user_approved && $row->resource_approved) {
            
          
            $data['links'] = array(
                array(
                    'label' => 'Accept Event Invite',
                    'class' => 'smoothbox icon_siteevents_inviteaccept',
                    'route' => 'siteevent_extended',
                    'params' => array(
                        'controller' => 'member',
                        'action' => 'accept',
                        'event_id' => $item->getIdentity(),
                        'occurrence_id' => $occure_id
                    ),
                ), array(
                    'label' => 'Ignore Event Invite',
                    'class' => 'smoothbox icon_siteevents_invitereject',
                    'route' => 'siteevent_extended',
                    'params' => array(
                        'controller' => 'member',
                        'action' => 'reject',
                        'event_id' => $item->getIdentity(),
                        'occurrence_id' => $occure_id
                    ),
                )
            );
            if (strtotime($endDate) < time()) {
              unset($data['links'][0]);
            }
            
        }
        $data = array_merge($params, $data);
        if (isset($data['links']))
            return $this->view->partial('_membershipButton.tpl', 'siteevent', $data);
    }

}