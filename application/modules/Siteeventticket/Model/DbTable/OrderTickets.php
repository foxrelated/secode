<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: OrderTickets.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_DbTable_OrderTickets extends Engine_Db_Table {

    /**
     * Return ticket details purchased in an order
     *
     * @param $params
     * @return object
     */
    public function getOrderTicketsDetail($params = array()) {

        $tableName = $this->info('name');
        
        $select = $this->select();
        
        if(!empty($params['columns'])) {
            $select->from($tableName, $params['columns']);
        }
        else {
            $select->from($tableName);
        }
        
        if(!empty($params['order_id'])) {
            $select->where('order_id =?', $params['order_id']);
        }

        return $this->fetchAll($select);
    }

    /**
     * Return tickets of an order
     *
     * @param $order_id
     * @return object
     */
    public function getOrderTickets($order_id) {

        $orderTicketTableName = $this->info('name');
        $orderTableName = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->info('name');

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($orderTicketTableName)
                ->joinLeft($orderTableName, "$orderTableName.order_id = $orderTicketTableName.order_id", array("$orderTicketTableName.ticket_id", "$orderTicketTableName.quantity", "$orderTableName.occurrence_id", "occurrence_starttime", "occurrence_endtime"))
                ->where($orderTicketTableName . '.order_id =?', $order_id);

        return $this->fetchAll($select);
    }

    public function isOrderHavingDiscount($params = array()) {

        $select = $this->select()
                ->from($this->info('name'), 'ticket_id');

        if (!empty($params['order_id'])) {
            $select->where('order_id = ?', $params['order_id']);
        }

        if (!empty($params['event_id'])) {
            $select->where('event_id = ?', $params['event_id']);
        }

        $select->where('price > discounted_price');

        return $select->limit(1)->query()->fetchColumn();
    }
    
    public function getTicketDetails($params = array()) {

        $select = $this->select()->from($this->info('name'), 'title');

        if (!empty($params['order_id'])) {
            $select->where('order_id = ?', $params['order_id']);
        }

        if (!empty($params['ticket_id'])) {
            $select->where('ticket_id = ?', $params['ticket_id']);
        }

        return $select->limit(1)->query()->fetchColumn();
    }    

}
