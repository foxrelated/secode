<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Events.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Occurrences extends Engine_Db_Table {

    //protected $_rowClass = "Siteevent_Model_Event";
    //protected $_serializedColumns = array('main_video', 'networks_privacy');

    protected $_rowClass = 'Siteevent_Model_Occurrence';
    protected $_occurrence_id;
    protected $_serializedColumns = array('ticket_id_sold');
    
    public function getEventOccurrencesPaginator($params = array(), $customParams = null) {

        $paginator = Zend_Paginator::factory($this->getEventOccurrenceSelect($params, $customParams));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    //GET SITEEVENT SELECT QUERY
    public function getEventOccurrenceSelect($params = array(), $customParams = null) {
        $occurrenceTableName = $this->info('name');
        $select = $this->select();
        $select = $select
                ->from($this->info('name'), array('starttime', 'endtime', 'occurrence_id'));
        $select->where("event_id =?", $params['event_id']);
        if (isset($params['order']))
            $select->order('starttime ' . $params['order']);
        if (isset($params['firststarttime']) && $params['laststarttime']) {
            $select->where('starttime >= ?', $params['firststarttime']);
            $select->where('starttime <= ?', $params['laststarttime']);
        }

        return $select;
    }

    public function getCustomEventInfo($event_id) {
        $repeatDatesTableName = $this->info('name');
        $select = $this->select();
        $select = $select
                ->from($repeatDatesTableName);
        $select->where("event_id =?", $event_id);
        $repeatEventInfo = $this->fetchAll($select);
        return $repeatEventInfo;
    }

    public function getEventType($event_id) {
        $repeatDatesTableName = $this->info('name');
        $select = $this->select();
        $select = $select
                ->from($repeatDatesTableName, array('eventrepeat_type'));
        $select->where("event_id =?", $event_id);
        $select->limit("1");
        $repeatEventType = $select->query()->fetchColumn();
        return $repeatEventType;
    }

    //DELETE THE ROWS FROM THIS TABLE FOR AN EVENT

    public function deleteRepeatEvent($event_id) {

        if (empty($event_id))
            return;
        $this->delete(array('event_id = ?' => $event_id));
    }

    public function getEventDate($event_id, $occurrence_id = null) {
        $occurrenceTableName = $this->info('name');
        $select = $this->select();
        $select = $select
                ->from($occurrenceTableName, array('starttime', 'endtime'));
        $select->where("event_id =?", $event_id);
        if (!empty($occurrence_id))
            $select->where("occurrence_id =?", $occurrence_id);
        else
            $select->order("$occurrenceTableName.starttime ASC");
        $select->limit("1");
        $dateInfo = $this->fetchRow($select);
        if (!empty($dateInfo))
            $dateInfo = $dateInfo->toarray();
        return $dateInfo;
    }

    //get all occurrence dates for the event

    public function getAllOccurrenceDates($event_id, $firstOccurrence = 0, $params = array()) {

        $occurrenceTableName = $this->info('name');

        if ($firstOccurrence) {
            $select = $this->select()
                            ->from($this->info('name'), 'occurrence_id')
                            ->order('starttime asc')
                            ->where('event_id = ?', $event_id);
            
            if (!empty($params['notIncludePastEvents'])) {
                $select->where("endtime > NOW()");
            }                 
            
            return $select->limit(1)->query()->fetchColumn();
            
        } else {
            $select = $this->select()
                    ->from($this->info('name'), array('starttime', 'endtime', 'occurrence_id'));
            $select->where("event_id =?", $event_id);
            
            if (!empty($params['notIncludePastEvents'])) {
                $select->where("endtime > NOW()");
            }            
            
            $select->order('starttime asc');
            $datesInfo = $this->fetchAll($select);

            return $datesInfo;
        }
    }

    public function getOccurenceEndDate($event_id, $endtimeOrder = 'ASC', $occurrence_id = null) {

        $select = $this->select()
                ->from($this->info('name'), 'endtime')
                ->where('event_id = ?', $event_id);
        if ($occurrence_id)
            $select->where('occurrence_id = ?', $occurrence_id);
        
        //ORDER
        $select->order('endtime ' . $endtimeOrder);
        return $select->query()
                      ->fetchColumn();
    }

    public function getOccurenceStartDate($event_id, $starttimeOrder = 'ASC', $occurrence_id = null) {

        $select = $this->select()
                ->from($this->info('name'), 'starttime')
                ->order('starttime ' . $starttimeOrder)
                ->where('event_id = ?', $event_id);
        if ($occurrence_id)
            $select
                    ->where('occurrence_id = ?', $occurrence_id);
        return $select->query()
                        ->fetchColumn();
    }

    public function getOccurenceStartEndDate($event_id, $starttimeOrder = 'DESC') {

        $select = $this->select()
                        ->from($this->info('name'), array('starttime', 'endtime'))
                        ->where('event_id = ?', $event_id)
                        ->order('starttime ' . $starttimeOrder);
                        
        
       return $this->fetchRow($select);
            
                        
    }

    //DELETE THE ROWS FROM THIS TABLE FOR AN EVENT

    public function deleteOccurrenceEvent($occurrence_id) {

        if (empty($occurrence_id))
            return;
        $this->delete(array('occurrence_id = ?' => $occurrence_id));
    }

    //GET THE TOTAL OCCURRENCE OF THE EVENT
    public function getOccurrenceCount($event_id, $params = array()) {

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('COUNT(*) AS count'))
                ->where('event_id = ?', $event_id);

        if (isset($params['upcomingOccurrences']) && !empty($params['upcomingOccurrences'])) {
            $select->where("starttime > NOW()");
        }

        $totalOccurrences = $select->query()->fetchColumn();

        //RETURN EVENTS COUNT
        return $totalOccurrences;
    }

    /**
     * Return start time and end time of next occurrence of an event
     *
     * @param int $event_id
     * @return array
     */
    public function getNextOccurrenceDateTime($event_id) {

        $select = $this->select()
                ->from($this->info('name'), array('starttime', 'endtime'))
                ->where("event_id =?", $event_id)
                ->where("starttime > NOW()")
                ->order("starttime ASC")
                ->limit("1");

        $dateInfo = $this->fetchRow($select);
        if (!empty($dateInfo))
            $dateInfo = $dateInfo->toarray();
        return $dateInfo;
    }
    
   /**
     * Set the current occurrence id of an event
     *
     * @param int $occurrence_id
     * 
     */   
   public function setOccurrence($occurrence_id) {
     
     $this->_occurrence_id = $occurrence_id;
   }
   
   /**
     * Get the current occurrence id of an event
     *
     * @returns int $occurrence_id
     * 
     */   
   public function getOccurrence() {
     if($this->_occurrence_id) { 
       return $this->_occurrence_id;
     }else {
       $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
      if (empty($occurrence_id)) 
        $this->_occurrence_id = $occurrence_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('occurrence_id', null);
        if($occurrence_id == 'all' || empty($occurrence_id)) { 
          $subject = Engine_Api::_()->core()->getSubject('siteevent_event');
          $this->_occurrence_id = $occurrence_id =  Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($subject->event_id);
        }
     }
     return $this->_occurrence_id;
   }
     
  //FUNCTION TO SAVE DETAILS OF TICKETS IN OCCURRENCES TABLE.
  public function setTicketDetails($event_id, $ticket_id) {
    if (empty($event_id)) {
      return;
    }
    $occurrencesRows = $this->select()
      ->where('event_id = ?', $event_id)
      ->query()
      ->fetchAll();

    $arrayDetail = array("tid_$ticket_id" => 0);
    foreach ($occurrencesRows as $occurrencesRow) {
      if (empty($occurrencesRow['ticket_id_sold'])) {
        $this->update(array('ticket_id_sold' => $arrayDetail), array('occurrence_id = ?' => $occurrencesRow['occurrence_id']));
      } else {
        //  WHEN ALREADY TICKETS EXISTS. NEED TO MERGE THE ARRAY
        $mergeArray = array_merge(Zend_Json_Decoder::decode($occurrencesRow['ticket_id_sold']), $arrayDetail);
        $this->update(array('ticket_id_sold' => $mergeArray), array('occurrence_id = ?' => $occurrencesRow['occurrence_id']));
      }
    }
  }

  //UPDATE ANY PARTICULAR ticket_id_sold ARRAY OF ANY OCCURRENCE ON DELETE TICKET / ADD SOLD COUNT
  public function updateTicketDetails($occurrence_id, $ticket_id, $qty) {
      $occurrencesRow = $this->fetchRow(array('occurrence_id = ?' => $occurrence_id));
    
      $ticketDetailArray = $occurrencesRow->ticket_id_sold;
      
      $ticket_sold = $ticketDetailArray["tid_$ticket_id"] + $qty;
      $arrayDetail = array("tid_$ticket_id" => $ticket_sold);
      $mergeArray = array_merge($occurrencesRow->ticket_id_sold, $arrayDetail);
      $this->update(array('ticket_id_sold' => $mergeArray), array('occurrence_id = ?' => $occurrence_id)); 
  }
  
  //UPDATE ANY PARTICULAR ticket_id_sold ARRAY OF ANY OCCURRENCE ON DELETE TICKET
  public function deleteTicketDetails($event_id, $ticket_id) {
      if (empty($event_id)) {
      return;
      }
    $occurrencesRows = $this->select()
      ->where('event_id = ?', $event_id)
      ->query()
      ->fetchAll();

      foreach ($occurrencesRows as $occurrencesRow) {
        if (!empty($occurrencesRow['ticket_id_sold'])) {
          $arrayTicketIdSold = Zend_Json_Decoder::decode($occurrencesRow['ticket_id_sold']);
          if(in_array("tid_$ticket_id",$arrayTicketIdSold)){
            unset($arrayTicketIdSold["tid_$ticket_id"]);
            $this->update(array('ticket_id_sold' => $arrayTicketIdSold), array('occurrence_id = ?' => $occurrencesRow['occurrence_id']));
          }         
        }
      }
  }
  
  public function totalSoldTickets($params = array()) {

      $select = $this->select()->from($this->info('name'), 'ticket_id_sold');

      if(!empty($params['occurrence_id'])) {
          $select->where('occurrence_id = ?', $params['occurrence_id']);
      }
      
      $ticketIds = $select->query()->fetchColumn();

      $ticketsCount = 0;
      if(!empty($ticketIds)) {
            $ticketIds = Zend_Json_Decoder::decode($ticketIds);

            $ticketsCount = 0;
            foreach($ticketIds as $value) {
                $ticketsCount += $value;
            }
      }
      
      return $ticketsCount;
  }
  
  public function maxSoldTickets($params = array()) {
            
      $select = $this->select()->from($this->info('name'), 'ticket_id_sold');

      if(!empty($params['event_id'])) {
          $select->where('event_id = ?', $params['event_id']);
      }
      
      if(!empty($params['occurrence_id']) && $params['occurrence_id'] != 'all') {
          $select->where('occurrence_id = ?', $params['occurrence_id']);
      }      
      
      $select->where('ticket_id_sold is not NULL');
      
      $occurrences = $this->fetchAll($select);
      
      $totalTicketsCount = $ticketsCount = 0;
      foreach($occurrences as $occurrence) {

        $currentTicketsCount = 0;
        foreach($occurrence->ticket_id_sold as $value) {
            $currentTicketsCount += $value;
            $totalTicketsCount += $value;
        }
        
        if($currentTicketsCount > $ticketsCount) {
            $ticketsCount = $currentTicketsCount;
        }
      }
      
      if(!empty($params['totalTicketsCount'])) {
          return $totalTicketsCount;
      }
      
      return $ticketsCount;
  }
  
  public function maxMembers($params = array()) {
      
      $occurrenceTableName = $this->info('name');
      
      $membershipTable = Engine_Api::_()->getDbTable('membership', 'siteevent');
      $membershipTableName = $membershipTable->info('name');
      
      $select = $this->select()
          ->setIntegrityCheck(false)
          ->from($occurrenceTableName, null)
          ->joinRight($membershipTableName, "$membershipTableName.occurrence_id = $occurrenceTableName.occurrence_id", 'COUNT(user_id) as total_members');
      
      if(!empty($params['rsvp'])) {
          $select->where($membershipTableName.'.rsvp = ?', $params['rsvp']);
      }      
          
      if(!empty($params['event_id'])) {
          $select->where($occurrenceTableName.'.event_id = ?', $params['event_id']);
      }
      
      return $select->query()->fetchColumn();
  }
  
  //CALCULATES MAXIMUM SOLD OF EACH TICKET - while ticket edit, owner can not set the quantity less than this count.
  public function maxEachTicketSoldCount($params = array()) {
            
      $select = $this->select()->from($this->info('name'), 'ticket_id_sold');

      if(!empty($params['event_id'])) {
          $select->where('event_id = ?', $params['event_id']);
      }
      
      $select->where('ticket_id_sold is not NULL');
      
      $occurrences = $this->fetchAll($select);
      
      $ticketsCount = 0;
      foreach($occurrences as $occurrence) {

        $currentTicketsCount = 0;
        foreach($occurrence->ticket_id_sold as $key => $value) {
          if($key == 'tid_'.$params['ticket_id']){
            $currentTicketsCount = $value;
          }
        }
        
        if($currentTicketsCount > $ticketsCount) {
            $ticketsCount = $currentTicketsCount;
        }
      }
      
      return $ticketsCount;
  }

}

