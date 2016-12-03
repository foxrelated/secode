<?php

class Money_Model_DbTable_Transactions extends Engine_Db_Table {

    protected $_rowClass = 'Money_Model_Transaction';

    public function getTransactionPaginator($params = array()) {
        $paginator = Zend_Paginator::factory($this->getTransactionSelect($params));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }

    public function getTransactionSelect($params = array()) {
        $table = Engine_Api::_()->getDbtable('transactions', 'money');
        $rName = $table->info('name');
        $userTable = Engine_Api::_()->getItemTable('user');
        $userTableName = $userTable->info('name');

        $select = $table->select()
                ->from($rName)
                ->joinLeft($userTableName, "`{$userTableName}`.`user_id` = `{$rName}`.`user_id`", null)
                ->order('transaction_id DESC');

        if (!empty($params['type'])) {
            $select->where('type = ?', $params['type']);
        }
        if (!empty($params['text'])) {
            $select->where("`{$userTableName}`.`username` LIKE ?", "%{$params['text']}%");
        }

        if (!empty($params['gateway'])) {
            $select->where('gateway_id =?', $params['gateway']);
        }

        if (!empty($params['user_id'])) {
            $select->where("`{$rName}`.`user_id` = ?", $params['user_id']);
        }

        return $select;
    }

    public function createTransaction(Core_Model_Item_Abstract $order, Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $poster) {
        $moneyTable = Engine_Api::_()->getDbtable('money', 'money');

        $row = $this->createRow();
        $row->user_id = $subject->owner_id;
        $row->timestamp = date('Y-m-d H:i:s');
        $row->order_id = $order->order_id;
        $row->type = '10';
        $row->amount = $subject->amount;
        $row->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('money.currency', 'USD');
        $row->gateway_parent_transaction_id = $poster->getIdentity();
        $row->save();

        $moneyTable->setMoney($subject->owner_id, $subject->amount);


        $rowS = $this->createRow();
        $rowS->user_id = $poster->getIdentity();
        $rowS->timestamp = date('Y-m-d H:i:s');
        $rowS->order_id = $order->order_id;
        $rowS->type = '10';
        $rowS->amount = -($subject->amount);
        $rowS->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('money.currency', 'USD');
        $rowS->save();

        $moneyTable->setMoney($poster->getIdentity(), -($subject->amount));

        $order->state = 'complete';
        $order->save();
    }

    /**
     * @param $row
     * @throws Exception
     */
    public function createSubjectTransaction($row) {
        $db = $this->getDefaultAdapter();
        $db->beginTransaction();
        try {
            $_row = $this->createRow($row);
            $_row->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * @param string $state
     * @return Zend_Paginator
     */
    public function getTransactionsByState($subject_type, $state = 'pending') {
        $orderTable = Engine_Api::_()->getDbtable('orders', 'money');
        $select = $this->select()
                ->distinct(true)
                ->setIntegrityCheck(false)
                ->from($this->info('name'), array($this->info('name') . '.*'))
                ->joinLeft($orderTable->info('name'), $this->info('name') . '.order_id = ' . $orderTable->info('name') . '.order_id', array($orderTable->info('name') . '.source_id', $orderTable->info('name') . '.source_type'))
                ->where($orderTable->info('name') . '.source_type = ?', $subject_type)
                ->where($this->info('name') . '.state = ?', $state)
                ->order($this->info('name') . '.timestamp DESC');

        return Zend_Paginator::factory($select);
    }

    /**
     * @param Core_Model_Item_Abstract $subject
     * @param string $state
     * @throws Exception
     */
    public function changeTransactionState($transaction_id, $state = 'complete') {
        $db = $this->getDefaultAdapter();
        $db->beginTransaction();
        try {
            $row = $this->find($transaction_id)->current();
            $row->state = $state;
            $row->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function setState($subject, $state = 'complete') {

        $orderTable = Engine_Api::_()->getDbtable('orders', 'money');

        $select = $this->select()
                ->from($this->info('name'), array($this->info('name') . '.transaction_id'))
                ->joinLeft($orderTable->info('name'), $orderTable->info('name') . '.order_id = ' . $this->info('name') . '.order_id', null)
                ->where($orderTable->info('name') . '.source_id = ?', $subject->getIdentity())
                ->where($orderTable->info('name') . '.source_type = ?', $subject->getType())
                ->where($this->info('name') . '.user_id = ?', $subject->getOwner()->getIdentity())
                ->where($this->info('name') . '.state = ?', 'pending')
                ->query()
                ->fetchColumn();

        $transaction = Engine_Api::_()->getItem('money_transaction', $select);

        $db = $this->getDefaultAdapter();
        $db->beginTransaction();
        try {

            $transaction->state = $state;
            $transaction->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

}