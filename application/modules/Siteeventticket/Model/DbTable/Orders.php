<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Orders.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_DbTable_Orders extends Engine_Db_Table {

    protected $_name = 'siteeventticket_orders';
    protected $_rowClass = 'Siteeventticket_Model_Order';

    /**
     * Return list of placed orders
     *
     * @param $param = page id/ buyer id of the order
     * @param $flag
     * @return object
     */
    public function getOrdersPaginator($params = array()) {

        $paginator = Zend_Paginator::factory($this->getOrdersSelect($params));

        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    public function getOrdersSelect($params) {

        $userTableName = Engine_Api::_()->getItemTable('user')->info('name');
        $orderTableName = $this->info('name');

        $select = $this->select()
                ->setIntegrityCheck(false);
        
        if(!empty($params['countOnly'])){
                 $select->from($orderTableName, array('order_id'))
                ->joinLeft($userTableName, "$orderTableName.user_id = $userTableName.user_id", array())
                ->group($orderTableName . '.order_id');         
        }
        else{
                $select->from($orderTableName)
                ->joinLeft($userTableName, "$orderTableName.user_id = $userTableName.user_id", array("$userTableName.user_id"))
                ->group($orderTableName . '.order_id')
                ->order('creation_date DESC')
                ->order('order_id DESC');
        }

        if (!empty($params['event_id'])) {
            $select->where("$orderTableName.event_id =?", $params['event_id']);
        }

        if (!empty($params['order_id'])) {
            $select->where("$orderTableName.order_id =?", $params['order_id']);
        }

        if (!empty($params['user_id'])) {
            $select->where("$orderTableName.user_id =?", $params['user_id']);
        }

        //MY TICKETS PAGE - CURRENT & PAST FILTER
        if (!empty($params['viewType'])) {
            $current_date = date("Y-m-d H:i:s");
            if ($params['viewType'] == 'current') {
                $select->where("($orderTableName.occurrence_starttime <= '$current_date' AND $orderTableName.occurrence_endtime >= '$current_date') OR ($orderTableName.occurrence_starttime >= '$current_date')");
            } else {
                $select->where("$orderTableName.occurrence_endtime < ?", $current_date);
            }
        }

        if (isset($params['search'])) {

            if (!empty($params['order_id']))
                $select->where($orderTableName . '.order_id =?', $params['order_id']);

            if (!empty($params['username']))
                $select->where($userTableName . '.displayname  LIKE ?', '%' . trim($params['username']) . '%');

            if (!empty($params['creation_date_start']))
                $select->where("CAST($orderTableName.creation_date AS DATE) >=?", trim($params['creation_date_start']));

            if (!empty($params['creation_date_end']))
                $select->where("CAST($orderTableName.creation_date AS DATE) <=?", trim($params['creation_date_end']));

            if (!empty($params['order_min_amount']))
                $select->where("$orderTableName.grand_total >=?", trim($params['order_min_amount']));

            if (!empty($params['order_max_amount']))
                $select->where("$orderTableName.grand_total <=?", trim($params['order_max_amount']));

            if (!empty($params['commission_min_amount']))
                $select->where("$orderTableName.commission_value >=?", trim($params['commission_min_amount']));

            if (!empty($params['commission_max_amount']))
                $select->where("$orderTableName.commission_value <=?", trim($params['commission_max_amount']));


            if (!empty($params['order_status'])) {
                --$params['order_status'];
                $select->where($orderTableName . '.order_status = ? ', $params['order_status']);
            }
        }

        //  DISPLAY ALL ORDERS OF "CHEQUE - 3" ,"PAY AT EVENT - 4", "FREE - 5" & ONLY ACTIVE ORDERS OF "2CHECKOUT -1","PAYPAL - 2" (GATEWAY_ID)
        if (isset($params['my_tickets_page'])) {
            $select->where("($orderTableName.gateway_id NOT IN ('3','4','5') AND $orderTableName.payment_status = 'active') OR ($orderTableName.gateway_id IN ('3','4','5'))");
        }

        
        if(!empty($params['countOnly'])){
          return $select->limit(1)->query()->fetchColumn();
        }
        
        return $select;
    }

//    public function getOrderDetails($params = array()) {
//        //GET EVENT TICKET TABLE NAME
//        $siteeventticketOrderTableName = $this->info('name');
//
//        //MAKE QUERY
//        $select = $this->select()
//                ->setIntegrityCheck(false)
//                ->from($siteeventticketOrderTableName);
//
//        $siteeventticketOrdertable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
//        $siteeventticketOrdertableName = $siteeventticketOrdertable->info('name');
//
//        $SiteeventOccurencetable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
//        $siteeventOccurenceTableName = $SiteeventOccurencetable->info('name');
//
//        //Remaining - need to optimize. two columns of order_id fetched.
//
//        $select->join($siteeventOccurenceTableName, "$siteeventticketOrderTableName.occurrence_id = $siteeventOccurTableName.occurrence_id")
//                ->where($siteeventOccurTableName . ".occurrence_id = ?", $params['occurrence_id']);
//    }

    /**
     * Return all orders detail for a order id
     *
     * @param $order_id
     * @return array
     */
    public function getAllOrders($order_id, $params = array()) {
        $order_table_name = $this->info('name');
        $order_ticket_table_name = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket')->info('name');


        $fetchColumnOrderTable = array("$order_table_name.order_id", "$order_table_name.occurrence_id", "$order_table_name.sub_total", "$order_table_name.tax_amount", "$order_table_name.grand_total", "$order_table_name.coupon_detail");
        $fetchColumnOrderTicketTable = array("$order_ticket_table_name.ticket_id", "$order_ticket_table_name.price", "$order_ticket_table_name.quantity", "$order_ticket_table_name.title");

        $select = $this->select()
                ->from($order_table_name, $fetchColumnOrderTable)
                ->setIntegrityCheck(false)
                ->join($order_ticket_table_name, "($order_ticket_table_name.order_id = $order_table_name.order_id)", $fetchColumnOrderTicketTable)
                ->where($order_table_name . '.order_id =?', $order_id);

//    if( !empty($params) && !empty($params['occurrence_id']) ) {
//      $select->where("$order_table_name.occurrence_id =?", $params['occurrence_id']);
//    }

        return $select->query()->fetchAll();
    }

    /**
     * Return sum of grand-total, tax and for a event
     *
     * @param $event_id
     * @return object
     */
    public function getTotalAmount($event_id) {
        $select = $this->select()
                ->from($this->info('name'), array('SUM(sub_total) as sub_total, SUM(tax_amount) as tax_amount, SUM(commission_value) as commission_value, COUNT(order_id) as order_count'))
                ->where('event_id =? AND payment_request_id = 0 AND direct_payment = 0 AND payment_status LIKE \'active\' AND order_status = 2', $event_id);
        
        if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $select->where('payment_split = ?', 0);
        }

        return $this->fetchRow($select);
    }

    public function notPaidBillAmount($event_id) {
        $select = $this->select()
                ->from($this->info('name'), array("sum(commission_value) as commission"))
                ->where('event_id =?', $event_id)
                ->where('direct_payment = 1')
                ->where('non_payment_admin_reason = 1')
                ->where("payment_status != 'not_paid'")
                ->where('order_status = 3');

        return $select->query()->fetchColumn();
    }

    /**
     * Return bill amount for a event
     *
     * @param $event_id
     * @return object
     */
    public function getEventBillAmount($event_id) {
        $select = $this->select()
                        ->from($this->info('name'), array('SUM(commission_value) as commission'))
                        ->where("event_id =? AND eventbill_id = 0 AND direct_payment = 1 AND non_payment_admin_reason != 1 AND order_status != 3 AND payment_status != 'not_paid'", $event_id);
        
        if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $select->where('payment_split = ?', 0);
        }        

        return $select->query()->fetchColumn();
    }

    public function getEventBillPaginator($params = array()) {

        $paginator = Zend_Paginator::factory($this->getEventBillSelect($params));

        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    public function getEventBillSelect($params) {
        $orderTableName = $this->info('name');

        $select = $this->select()
                ->from($orderTableName, array("sum(grand_total) as grand_total", "sum(commission_value) as commission", "count(order_id) as order_count", "MONTHNAME(creation_date) as month", "MONTH(creation_date) as month_no", "YEAR(creation_date) as year"))
                ->where('event_id =?', $params['event_id'])
                ->where('direct_payment = 1')
                ->where('non_payment_admin_reason != 1')
                ->where('order_status != 3');

        $select->group("YEAR($orderTableName.creation_date), MONTH($orderTableName.creation_date)");
        return $select;
    }

    public function getEventMonthlyBillPaginator($params = array()) {

        $paginator = Zend_Paginator::factory($this->getEventMonthlyBillSelect($params));

        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    public function getEventMonthlyBillSelect($params) {
        $orderTableName = $this->info('name');

        $select = $this->select()
                ->from($orderTableName, array("order_id", "ticket_qty", "commission_value", "grand_total", "creation_date", "payment_status"))
                ->where('event_id =?', $params['event_id'])
                ->where('direct_payment = 1');

        if (isset($params['month']) && !empty($params['month'])) {
            $select->where('MONTH(creation_date) = ?', $params['month']);
        }
        if (isset($params['year']) && !empty($params['year'])) {
            $select->where('YEAR(creation_date) = ?', $params['year']);
        }

        $select->order('order_id DESC');
        return $select;
    }

    public function getEventCommissionAmountDetailPaginator($params = array()) {
        $paginator = Zend_Paginator::factory($this->getEventCommissionAmountDetail($params));

        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    public function getEventCommissionAmountDetail($params = array()) {
        $orderTableName = $this->info('name');
        $eventTableName = Engine_Api::_()->getDbtable('events', 'siteevent')->info('name');
        $userTableName = Engine_Api::_()->getItemTable('user')->info('name');

        $select = $this->select()->setIntegrityCheck(false);

        if (isset($params['tab']) && empty($params['tab'])) {
            $select->from($orderTableName, array("SUM(grand_total) as order_total", "SUM(commission_value) as commission", "COUNT(order_id) as order_count"));
        } else if (isset($params['tab']) && !empty($params['tab'])) {
            $select->from($orderTableName, array("order_id", "event_id", "commission_value", "grand_total", "non_payment_seller_reason", "non_payment_admin_reason", "non_payment_seller_message", "non_payment_admin_message"));
        }

        $select->join("$eventTableName", ("$eventTableName.event_id = $orderTableName.event_id"), array('event_id', 'title'))
                ->joinLeft($userTableName, "$eventTableName.owner_id = $userTableName.user_id", array("$userTableName.username"))
                ->where("$orderTableName.direct_payment = 1")
                ->where("$orderTableName.order_status != 3")
                ->where("$orderTableName.non_payment_admin_reason != 1");     

        if (isset($params['tab']) && empty($params['tab'])) {
            $select->group("$eventTableName.event_id");
        }

        if (isset($params['tab']) && !empty($params['tab'])) {
            $select->where("$orderTableName.non_payment_seller_reason != 0");
        }

        if (isset($params['order_id']) && !empty($params['order_id'])) {
            $select->where("$orderTableName.order_id = " . trim($params['order_id']));
        }

        if (isset($params['username']) && !empty($params['username'])) {
            $select->where($userTableName . '.username  LIKE ?', '%' . trim($params['username']) . '%');
        }

        if (isset($params['title']) && !empty($params['title'])) {
            $select->where($eventTableName . '.title LIKE ?', '%' . trim($params['title']) . '%');
        }

        if (isset($params['from']) && !empty($params['from'])) {
            $select->where("CAST($orderTableName.creation_date AS DATE) >=?", trim($params['from']));
        }

        if (isset($params['to']) && !empty($params['to'])) {
            $select->where("CAST($orderTableName.creation_date AS DATE) <=?", trim($params['to']));
        }

        if (isset($params['commission_min_amount']) && !empty($params['commission_min_amount'])) {
            $select->having("commission >= " . trim($params['commission_min_amount']));
        }

        if (isset($params['commission_max_amount']) && !empty($params['commission_max_amount'])) {
            $select->having("commission <= " . trim($params['commission_max_amount']));
        }

        if (isset($params['order_min_amount']) && !empty($params['order_min_amount'])) {
            $select->having("order_total >= " . trim($params['order_min_amount']));
        }

        if (isset($params['order_max_amount']) && !empty($params['order_max_amount'])) {
            $select->having("order_total <= " . trim($params['order_max_amount']));
        }

        if (isset($params['tab']) && empty($params['tab']) && isset($params['order'])) {
            $select->order((!empty($params['order']) ? $params['order'] : 'event_id' ) . ' ' . (!empty($params['order_direction']) ? $params['order_direction'] : 'DESC' ));
        }

        if (isset($params['tab']) && !empty($params['tab'])) {
            if ($params['order'] == 'event_id') {
                $select->order("$orderTableName.order_id DESC");
            }
        }

        return $select;
    }

    /**
     * Return event overview : Selling of the event
     *
     * @param array $params
     * @return object
     */
    public function getEventStatistics($params) {
        $select = $this->select()
                ->from($this->info('name'), array("SUM(sub_total) as sub_total", "COUNT(order_id) as order_count", "SUM(commission_value) as commission", "SUM(tax_amount) as tax_amount", "SUM(ticket_qty) as ticket_qty"))
                ->where('event_id =?', $params['event_id'])
                ->where("order_status = 2");

        return $select->query()->fetch();
    }

    /**
     * Return event earning over a particular time duration
     *
     * @param $event_id
     * @param $time_duration
     * @return float
     */
    public function getEventEarning($event_id, $time_duration) {

        $select = $this->select()
                ->from($this->info('name'), array("SUM(grand_total) as grand_total"))
                ->where("event_id =?", $event_id)
                ->where("order_status = 2");

        if ($time_duration == 'today')
            $select->where("DATE(creation_date) = DATE(NOW())");

        if ($time_duration == 'week')
            $select->where("YEARWEEK(creation_date) = YEARWEEK(CURRENT_DATE)");

        if ($time_duration == 'month')
            $select->where("YEAR(creation_date) = YEAR(NOW()) AND MONTH(creation_date) = MONTH(NOW())");

        return $select->query()->fetchColumn();
    }

    /**
     * Return latest order for a event
     *
     * @param array $params
      @return object
     */
    public function getLatestOrders($params) {

        $userTable = Engine_Api::_()->getItemTable('user');
        $userTableName = $userTable->info('name');
        $orderTableName = $this->info('name');

        $select = $userTable->select()
                ->setIntegrityCheck(false)
                ->from($userTableName, array("displayname", "username"))
                ->joinRight($orderTableName, "$orderTableName.user_id = $userTableName.user_id", array("$orderTableName.grand_total", "$orderTableName.order_id", "$orderTableName.ticket_qty", "$orderTableName.order_status", "$orderTableName.user_id", "$orderTableName.event_id", "DATE_FORMAT($orderTableName.creation_date, '%b %d %Y, %h:%i %p') as order_date"))
                ->where("$orderTableName.event_id =?", $params['event_id'])
                ->order("$orderTableName.creation_date DESC");

        if (!empty($params['limit'])) {
            $select->limit($params['limit']);
        }

        return $userTable->fetchAll($select);
    }

    /**
     * Return order according to status value
     *
     * @param array $params
     * @return object
     */
    public function getStatusOrders($params) {
        $select = $this->select()
                ->from($this->info('name'), array("COUNT(order_id)"));

        if (isset($params['event_id']) && !empty($params['event_id'])) {
            $select->where("event_id =?", $params['event_id']);
        }

        if (isset($params['order_status'])) {
            $select->where("order_status =?", $params['order_status']);
        }

        return $select->query()->fetchColumn();
    }

    /**
     * Return report of a event for a particular time interval over tickets or orders
     *
     * @param array $values
     * @return object
     */
    public function getReports($values = array()) {

        if (!empty($values['owner_id']))
            $owner_id = $values['owner_id'];

        $orderTableName = $this->info('name');

        $ticketsTable = Engine_Api::_()->getDbtable('tickets', 'siteeventticket');
        $ticketsTableName = $ticketsTable->info('name');

        $orderTicketsTable = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket');
        $orderTicketsTableName = $orderTicketsTable->info('name');

        if (!empty($values['select_event']))
            $event = $values['select_event'];

        if ($values['time_summary'] == 'Daily')
            $day = "%d";
        else
            $day = "";

        $select = $this->select()->setIntegrityCheck(false);

        if (!empty($values['type']) || (isset($values['report_depend']) && $values['report_depend'] == 'ticket')) {
            $select->from($orderTableName, array("COUNT($orderTableName.order_id) as order_count", "DATE_FORMAT($orderTableName.creation_date, '$day %M %Y') as creation_date", "$orderTableName.event_id"))
                    ->join($orderTicketsTableName, $orderTicketsTableName . '.order_id = ' . $orderTableName . '.order_id', array("SUM($orderTicketsTableName.quantity) AS quantity", "SUM($orderTicketsTableName.price) AS price"))
                    ->join($ticketsTableName, "($ticketsTableName.ticket_id  = $orderTicketsTableName.ticket_id)", array("title", "ticket_id"));

            $ticket = $values['select_ticket'];
            if ($ticket == 'specific_ticket') {
                $ticket_ids = $values['ticket_ids'];
                $select->where("$ticketsTableName.ticket_id IN($ticket_ids)");
            }
            if ($event != 'all') {
                if ($event == 'current_event')
                    $event_ids = $values['event_id'];
                elseif ($event == 'specific_event')
                    $event_ids = $values['event_ids'];

                $select->where("$ticketsTableName.event_id IN($event_ids)");
            }

            $ticket_group_by = "$ticketsTableName.ticket_id,";
            $order_group_by = '';
        } else if ((isset($values['report_depend']) && $values['report_depend'] == 'order' ) || empty($values['type'])) {
            $select->from($orderTableName, array("SUM($orderTableName.ticket_qty) as quantity", "COUNT($orderTableName.order_id) as order_count", "DATE_FORMAT($orderTableName.creation_date, '$day %M %Y') as creation_date", "SUM($orderTableName.tax_amount) as tax_amount", "SUM($orderTableName.commission_value) as commission", "SUM($orderTableName.grand_total) as grand_total", "SUM($orderTableName.sub_total) as sub_total", "$orderTableName.event_id"));
            if ($event == 'specific_event') {
                $event_ids = $values['event_ids'];
                $select->where("$orderTableName.event_id IN($event_ids)");
            } else if (!empty($owner_id)) {
                $viewer_event_ids = Engine_Api::_()->getDbTable('events', 'siteevent')->getEventId($owner_id);
                foreach ($viewer_event_ids as $event_id) {
                    $temp_event_ids[] = $event_id['event_id'];
                }
                $viewer_event_ids = implode(",", $temp_event_ids);
                $select->where("$orderTableName.event_id IN($viewer_event_ids)");
            }
            $ticket_group_by = '';
            $order_group_by = "$orderTableName.event_id, ";
        }

        if (isset($values['order_status']) && $values['order_status'] != 'all')
            $select->where("$orderTableName.order_status = ? ", $values['order_status']);

        if (!empty($values['time_summary'])) {
            if ($values['time_summary'] == 'Monthly') {
                $startTime = date('Y-m', mktime(0, 0, 0, $values['month_start'], date('d'), $values['year_start']));
                $endTime = date('Y-m', mktime(0, 0, 0, $values['month_end'], date('d'), $values['year_end']));
            } else {
                if (!empty($values['start_daily_time'])) {
                    $start = $values['start_daily_time'];
                }
                if (!empty($values['end_daily_time'])) {
                    $end = $values['end_daily_time'];
                }
                $startTime = date('Y-m-d', $start);
                $endTime = date('Y-m-d', $end);
            }

            switch ($values['time_summary']) {

                case 'Monthly':
                    $select
                            ->where("DATE_FORMAT(" . $orderTableName . " .creation_date, '%Y-%m') >= ?", $startTime)
                            ->where("DATE_FORMAT(" . $orderTableName . " .creation_date, '%Y-%m') <= ?", $endTime)
                            ->group("$ticket_group_by $order_group_by YEAR($orderTableName.creation_date), MONTH($orderTableName.creation_date)");
                    break;

                case 'Daily':
                    $select
                            ->where("DATE_FORMAT(" . $orderTableName . " .creation_date, '%Y-%m-%d') >= ?", $startTime)
                            ->where("DATE_FORMAT(" . $orderTableName . " .creation_date, '%Y-%m-%d') <= ?", $endTime)
                            ->group("$ticket_group_by $order_group_by YEAR($orderTableName.creation_date), MONTH($orderTableName.creation_date), DAY($orderTableName.creation_date)");
                    break;
            }
        }

        if (isset($values['display']) && $values['display'] == 'date_wise')
            $select->order("$orderTableName.creation_date");
        else
            $select->order("$orderTableName.event_id");

        return $this->fetchAll($select);
    }

    //IF EVENT GUESTS OTHER THAN LEADERS & HOST
    public function hasEventTicketGuest(Core_Model_Item_Abstract $resource, User_Model_User $user) {
        //IF SITEREPEAT EVENT MODULE IS NOT ENABLED THEN WE WILL ALWAYS SEND TRUE HERE.
        if (!Engine_Api::_()->hasModuleBootstrap('siteeventrepeat'))
            return false;

        $host = $resource->getHost();
        //GET LEADER LIST OF EVENTS
        $leaderList = $resource->getLeaderList();
        if ($leaderList->child_count) {
            $eventLeaders = Engine_Api::_()->getItemTable('siteevent_list')->getLeaders($leaderList->list_id);
        }

        $eventLeadersTotal = $resource->owner_id;
        if (!empty($eventLeaders)) {
            $eventLeadersTotal = $eventLeadersTotal . ',' . $eventLeaders;
        }
        if ($host && $host->getType() == 'user') {
            $eventLeadersTotal = $eventLeadersTotal . ',' . $host->getIdentity();
        }

        $select = $this->select()
                ->where('event_id = ?', $resource->getIdentity())
                ->where('user_id NOT IN (' . $eventLeadersTotal . ')');

        $row = $this->fetchRow($select);
        if ($row === null) {
            return false;
        }

        return true;
    }
    
    public function getMembers($params = array()) {

        $userTable = Engine_Api::_()->getItemTable('user');
        $userTableName = $userTable->info('name');
        $orderTableName = $this->info('name');

        $select = $userTable->select()
                ->setIntegrityCheck(false)
                ->from($userTableName, array("user_id", "displayname", "username", 'photo_id'))
                ->join($orderTableName, "$orderTableName.user_id = $userTableName.user_id", array(""))
                ->where("(payment_status = 'active' OR (non_payment_seller_reason = 0 && non_payment_admin_reason = 0))")
                ->where("$orderTableName.event_id =?", $params['event_id'])
                ->group("$orderTableName.user_id")
                ->order("$orderTableName.creation_date DESC");
        
        if (!empty($params['occurrence_id']) && $params['occurrence_id'] != 'all') {
            $select->where($orderTableName.'.occurrence_id = ?', $params['occurrence_id']);
        }   
        
        if (isset($params['is_private_order'])) {
            $select->where($orderTableName.'.is_private_order = ?', $params['is_private_order']);
        }               

        if (!empty($params['limit'])) {
            $select->limit($params['limit']);
        }

        return $userTable->fetchAll($select);
    }

}
