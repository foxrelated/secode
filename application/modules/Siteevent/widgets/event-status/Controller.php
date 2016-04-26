<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_EventStatusController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        //IS EVENT FINISHED
        $this->view->occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
        
        $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
        
        $dates = $occurrenceTable->getEventDate($siteevent->event_id, $this->view->occurrence_id);
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $endDate = strtotime($dates['endtime']);
        $startDate = strtotime($dates['starttime']);
        $currentDate = time();

        $lastOccurrenceEndDate = $occurrenceTable->getOccurenceEndDate($siteevent->event_id, 'DESC');
        $lastOccurrenceEndDate = strtotime($view->locale()->toEventDateTime($lastOccurrenceEndDate, array('format' => 'M/d/yy h:mm a' )));

        $this->view->isLastOccurrenceEnd = 1;
        if ($lastOccurrenceEndDate > $currentDate) {
            $this->view->isLastOccurrenceEnd = 0;
        }

        $firstOccurrenceStartDate = $occurrenceTable->getOccurenceStartDate($siteevent->event_id, 'ASC');
        $firstOccurrenceStartDate = strtotime($view->locale()->toEventDateTime($firstOccurrenceStartDate));

        $this->view->isFirstOccurrenceStart = 1;
        if ($firstOccurrenceStartDate > $currentDate) {
            $this->view->isFirstOccurrenceStart = 0;
        }

        $this->view->isEventFinished = 0;
        $this->view->leftOccurrences = 0;
        if ($endDate < $currentDate) {
            $this->view->isEventFinished = 1;

            $this->view->next_occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($siteevent->event_id);
            $this->view->nextOccurrenceDate = $occurrenceTable->getEventDate($siteevent->event_id, $this->view->next_occurrence_id);

            $this->view->leftOccurrences = $occurrenceTable->getOccurrenceCount($siteevent->event_id, array('upcomingOccurrences' => 1));
        }

        $this->view->futureEvent = 0;
        if ($startDate > $currentDate) {
            $this->view->futureEvent = 1;
        }

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $this->viewerIsMember = $siteevent->membership()->isMember($viewer, true);
        $this->view->showButton = $this->_getParam('showButton', 0);
        $this->view->showEventFullStatus = $this->_getParam('showEventFullStatus', 1);
        $this->view->isEventFull = $siteevent->isEventFull();
        $this->view->inWaitlist = $this->view->waitlist_id = 0;
        if($this->view->showEventFullStatus && $this->view->isEventFull) {
            $params = array();
            $params['occurrence_id'] = $this->view->occurrence_id;
            $params['user_id'] = $viewer_id;
            $params['columnName'] = 'waitlist_id';
            $this->view->inWaitlist = $this->view->waitlist_id = Engine_Api::_()->getDbTable('waitlists', 'siteevent')->getColumnValue($params);            
        }
        
        //IF VIEWER IS ALREADY A MEMBER OF THIS RSVP EVENT
        if(!$this->view->waitlist_id && !Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
            $this->view->waitlist_id = $siteevent->membership()->isEventMember($viewer, true);  
        }
    }

}
