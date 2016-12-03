<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Paymentrequests.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_DbTable_Paymentrequests extends Engine_Db_Table {

    protected $_name = 'siteeventticket_payment_requests';
    protected $_rowClass = 'Siteeventticket_Model_Paymentrequest';

    /**
     * Return event payment request object
     *
     * @param array $params
     * @return object
     */
    public function getEventPaymentRequestPaginator($params = array()) {

        $paginator = Zend_Paginator::factory($this->getEventPaymentRequestSelect($params));

        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    public function getEventPaymentRequestSelect($params) {
        $select = $this->select()->where('event_id =?', $params['event_id'])->group('request_id');

        if (isset($params['search'])) {
            if (!empty($params['request_date']))
                $select->where("CAST(request_date AS DATE) =?", trim($params['request_date']));

            if (!empty($params['response_date']))
                $select->where("CAST(response_date AS DATE) =?", trim($params['response_date']));

            if (!empty($params['request_min_amount']))
                $select->where("request_amount >=?", trim($params['request_min_amount']));

            if (!empty($params['request_max_amount']))
                $select->where("request_amount <=?", trim($params['request_max_amount']));

            if (!empty($params['response_min_amount']))
                $select->where("response_amount >=?", trim($params['response_min_amount']));

            if (!empty($params['response_max_amount']))
                $select->where("response_amount <=?", trim($params['response_max_amount']));

            if (!empty($params['request_status'])) {
                $params['request_status'] --;
                $select->where('request_status =? ', $params['request_status']);
            }
        }

        $select->order('request_id DESC');
        return $select;
    }

    /**
     * Return response detail for a payment request id
     *
     * @param $request_id
     * @return object
     */
    public function getResponseDetail($request_id) {
        return $this->select()->from($this->info('name'), array('response_amount', 'event_id', 'response_message'))->where('request_id =?', $request_id)->query()->fetchAll();
    }

    /**
     * Return sum of requested amount of a event
     *
     * @param $request_id
     * @return float
     */
    public function getRequestedAmount($event_id) {
        return $this->select()
                        ->from($this->info('name'), array('SUM(request_amount)'))
                        ->where('event_id =? AND request_status = 0', $event_id)
                        ->query()
                        ->fetchColumn();
    }

    /**
     * Return all admin transactions detail for a event
     *
     * @param array $params
     * @return object
     */
    public function getAllAdminTransactionsPaginator($params = array()) {
        $paginator = Zend_Paginator::factory($this->getAllAdminTransactionsSelect($params));

        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    public function getAllAdminTransactionsSelect($params) {
        $transactionTableName = Engine_Api::_()->getDbtable('transactions', 'siteeventticket')->info('name');
        $paymentRequestTableName = $this->info('name');

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($paymentRequestTableName, array("$paymentRequestTableName.request_id", "$paymentRequestTableName.event_id", "$paymentRequestTableName.response_amount", "$paymentRequestTableName.gateway_id", "$paymentRequestTableName.gateway_profile_id", "$paymentRequestTableName.response_date"))
                ->join($transactionTableName, "($transactionTableName.order_id = $paymentRequestTableName.request_id)", array("$transactionTableName.transaction_id", "$transactionTableName.type", "$transactionTableName.state"))
                ->where("$transactionTableName.sender_type = 1")
                ->where("$paymentRequestTableName.event_id =?", $params['event_id'])
                ->group($paymentRequestTableName . '.request_id');

        if (isset($params['search'])) {
            if (!empty($params['date']))
                $select->where("CAST($transactionTableName.date AS DATE) =?", trim($params['date']));

            if (!empty($params['response_min_amount']))
                $select->where("$paymentRequestTableName.response_amount >=?", trim($params['response_min_amount']));

            if (!empty($params['response_max_amount']))
                $select->where("$paymentRequestTableName.response_amount <=?", trim($params['response_max_amount']));

            if (!empty($params['state'])) {
                switch ($params['state']) {
                    case 1:
                        $state = 'processing';
                        break;

                    case 2:
                        $state = 'pending';
                        break;
                }

                $params['state'] --;

                $select->where($transactionTableName . '.state LIKE ? ', '%' . $state . '%');
            }
        }

        $select->order('transaction_id DESC');

        return $select;
    }

}
