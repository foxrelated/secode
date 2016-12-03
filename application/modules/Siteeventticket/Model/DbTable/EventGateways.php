<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EventGateways.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_DbTable_EventGateways extends Engine_Db_Table {

    protected $_name = 'siteeventticket_event_gateways';

    public function getEventChequeDetail($params = array()) {
        $select = $this->select()
                ->from($this->info('name'), 'details')
                ->where("title =?", "ByCheque")
                ->where("details IS NOT NULL")
                ->limit(1);

        if (isset($params['event_id']) && !empty($params['event_id']))
            $select->where("event_id =?", $params['event_id']);

        if (isset($params['eventgateway_id']) && !empty($params['eventgateway_id']))
            $select->where("eventgateway_id =?", $params['eventgateway_id']);

        return $select->query()->fetchColumn();
    }

//    public function isGatewayEnable($params = array()) {
//        $select = $this->select()
//                ->from($this->info('name'), 'eventgateway_id')
//                ->where('title =?', $params['title'])
//                ->where('event_id =?', $params['event_id']);
//
//        if (isset($params['gateway_type']) && !empty($params['gateway_type']))
//            $select->where('gateway_type =?', $params['gateway_type']);
//
//        if (isset($params['detailNotNull']) && !empty($params['detailNotNull']))
//            $select->where("details IS NOT NULL");
//
//        return $select->limit(1)->query()->fetchColumn();
//    }
//
//    public function getEventEnabledGateway($params = array()) {
//        $select = $this->select()
//                ->from($this->info('name'), 'title')
//                ->where('enabled =?', 1)
//                ->where('event_id =?', $params['event_id'])
//                ->where('gateway_type =?', $params['gateway_type']);
//
//        return $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
//    }

}
