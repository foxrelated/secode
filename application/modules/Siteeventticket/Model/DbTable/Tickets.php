<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Tickets.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_DbTable_Tickets extends Engine_Db_Table {

    protected $_rowClass = "Siteeventticket_Model_Ticket";

    public function getTicketsPaginator($params = array(), $customParams = null) {

        $paginator = Zend_Paginator::factory($this->getTicketsSelect($params, $customParams));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    //GET SITEEVENT SELECT QUERY
    public function getTicketsSelect($params = array()) {

        //GET EVENT TABLE NAME
        $siteeventticketTableName = $this->info('name');

        //MAKE QUERY
        $select = $this->select()
                ->setIntegrityCheck(false);
        
        if(!empty($params['columns'])) {
            $select->from($siteeventticketTableName, $params['columns']);
        }
        else {
            $select->from($siteeventticketTableName);
        }

        //MANAGE TICKETS (TICKETS OF A PARTICULAR EVENT)
        if(!empty($params['event_id'])) {
            $select->where($siteeventticketTableName . ".event_id = ?", $params['event_id']);
        }
        
        //TICKETS OF A PARTICULAR OCCURRENCE. (JOIN WITH OCCURRENCE TABLE)
        if (isset($params['occurrence_id']) && $params['occurrence_id']) {
            $SiteEventOccuretable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
            $siteeventOccurTableName = $SiteEventOccuretable->info('name');

            $select->joinLeft($siteeventOccurTableName, "$siteeventticketTableName.event_id = $siteeventOccurTableName.event_id")
                    ->where($siteeventOccurTableName . ".occurrence_id = ?", $params['occurrence_id']);
        }
        //CHECK - FOR DISPLAYING ONLY CLOSED & OPEN TICKETS.
        if (!isset($params['hiddenTickets']) || !$params['hiddenTickets']) {
            $select->where($siteeventticketTableName . ".status IN(?)", array('open', 'closed'));
        }
        
        if (!empty($params['showOnlyFreeTickets'])) {
            $select->where($siteeventticketTableName . ".price <= ?", 0);
        }

        //DISPLAY TICKETS IN ORDER SELECTED IN WIDGET SETTING
        if (!empty($params['orderby'])) {
            if ($params['orderby'] == 'price') {
                $order = ' DESC';
            } else {
                $order = ' ASC';
            }
            $select->order($siteeventticketTableName . '.' . $params['orderby'] . $order);
        }

        return $select;
    }

    /**
     * Return tickets by text
     *
     * @param $event_ids
     * @param $text
     * @param $limit
     * @param $ticket_ids
     * @param $viewer_id
     * @return int
     */
    public function getTicketsByText($event_ids = null, $text = null, $limit = 20, $ticket_ids = null, $viewer_id = null) {
        $select = $this->select()
                ->order('title ASC')
                ->limit($limit);
        if (!empty($event_ids))
            $select->where("event_id IN ($event_ids)");

        if (!empty($ticket_ids))
            $select->where("ticket_id NOT IN ($ticket_ids)");

        if (!empty($text))
            $select->where('title LIKE ?', '%' . $text . '%');

        if (!empty($viewer_id))
            $select->where('owner_id = ?', $viewer_id);

        return $this->fetchAll($select);
    }

    public function resetTicketIdSoldArray($event_id) {

        $ticketsIdArray = array();
        $select = $this->select()
                ->from($this->info('name'), array('ticket_id'))
                ->where("event_id = ?", $event_id);

        $ticketRows = $this->fetchAll($select);
        foreach ($ticketRows as $ticketRow) {
            $ticket_id = $ticketRow['ticket_id'];
            $ticketsIdArray["tid_$ticket_id"] = 0;
        }

        return $ticketsIdArray;
    }

    public function getTicketsBySettings($params = array()) {

        $siteeventticketTableName = $this->info('name');

        $select = $this->select();
        
        if(!empty($params['ticktsCountOnly'])) {
            $select->from($siteeventticketTableName, array('COUNT(*) AS total_tickets'));
        }
        else {
            $select->from($siteeventticketTableName);
        }
        
        if (!empty($params['event_id'])) {
            $select->where($siteeventticketTableName . ".event_id = ?", $params['event_id']);
        }
        
        if (!empty($params['is_same_end_date'])) {
            $select->where($siteeventticketTableName . ".is_same_end_date = ?", $params['is_same_end_date']);
        }                     

        if(!empty($params['status'])) {
            $select->where($siteeventticketTableName . ".status IN(?)", $params['status']);
        }
        
        if (!empty($params['sell_endtime'])) {
            $select->where($siteeventticketTableName . ".sell_endtime >= NOW()");
        }   
        
        if (!empty($params['orderby'])) {
            $select->order($siteeventticketTableName . $params['orderby']);
        }         
        
        if (!empty($params['limit'])) {
            $select->limit($params['limit']);
        }           

        if(!empty($params['ticktsCountOnly'])) {
            return $select->query()->fetchColumn();
        }
        
        return $this->fetchAll($select);
    }
}
