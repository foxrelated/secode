<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Buyerdetails.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_DbTable_Buyerdetails extends Engine_Db_Table {

    protected $_rowClass = 'Siteeventticket_Model_Buyerdetail';

    /**
     * Return ticket details purchased in an order
     *
     * @param $order_id
     * @return object
     */
    public function getBuyerDetails($params = array()) {

        $order_id = $params['order_id'];
        
        //GET EVENT TABLE NAME
        $buyerDetailTableName = $this->info('name');
        $select = $this->select()
                ->setIntegrityCheck(false);
        
        if(!empty($params['groupBy'])) {
            $select->from($buyerDetailTableName, array('first_name', 'last_name', 'email', 'buyer_ticket_id', 'ticket_id', 'order_id', 'COUNT(*) AS total_tickets'));
        }
        else {
            $select->from($buyerDetailTableName, array('first_name', 'last_name', 'email', 'buyer_ticket_id', 'ticket_id', 'order_id'));
        }

        $orderTicketstable = Engine_Api::_()->getDbTable('orderTickets', 'siteeventticket');
        $orderTicketstableName = $orderTicketstable->info('name');

        $select->Join($orderTicketstableName, "$orderTicketstableName.ticket_id = $buyerDetailTableName.ticket_id", array('title', 'price'))
                ->where($orderTicketstableName . ".order_id = ?", $order_id)
                ->where($buyerDetailTableName . ".order_id = ?", $order_id);
        
        if(!empty($params['groupBy'])) {
            $select->group(array('first_name', 'last_name', 'ticket_id'));
        }

        $buyerRows = $this->fetchAll($select);

        return $buyerRows;
    }

    public function getBuyerEmailIds($order_id) {

        $buyerDetailTableName = $this->info('name');
        $select = $this->select()
                ->from($buyerDetailTableName, 'DISTINCT(email)')
                ->where($buyerDetailTableName . ".email IS NOT NULL")
                ->where($buyerDetailTableName . ".order_id = ?", $order_id);

        $buyerRows = $this->fetchAll($select);

        return $buyerRows;
    }

}
