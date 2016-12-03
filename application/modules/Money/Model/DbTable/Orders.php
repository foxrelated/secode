<?php

class Money_Model_DbTable_Orders extends Engine_Db_Table {

    protected $_rowClass = 'Money_Model_Order';

    public function setItem(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster) {
        $table = $this->getOrderTable();
        $row = $table->createRow();

        if (isset($row->source_type)) {
            $row->source_type = $resource->getType();
        }
        $row->user_id = $poster->getIdentity();
        $row->state = 'pending';
        $row->creation_date = date('Y-m-d H:i:s');
        $row->source_id = $resource->getIdentity();

        if (isset($resource->comment_count)) {
            $resource->paid_count++;
            $resource->save();
        }
        $row->save();

        return $row;
    }

    public function getOrderTable() {
        return $this;
    }

    function getItemPaid(Core_Model_Item_Abstract $resource) {
        return FALSE;
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if ($resource->getOwner()->getIdentity() == $viewer_id) {
            return true;
        }
        $table = $this->getOrderTable();
        $row = $table->select()
                ->from($table->info('name'), new Zend_Db_Expr('COUNT(*)'))
                ->where('source_type =?', $resource->getType())
                ->where('user_id =?', $viewer_id)
                ->where('source_id =?', $resource->getIdentity())
                ->where('state =?', 'complete')
                ->query()
                ->fetchColumn();
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $row
     * @return Zend_Db_Table_Row_Abstract
     * @throws Exception
     */
    public function createOrder($row) {
        $db = $this->getDefaultAdapter();
        $db->beginTransaction();
        $_row = $this->createRow($row);
        try {
            $_row->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        return $_row;
    }

    public function isItemPurchased(Core_Model_Item_Abstract $item, $plugin_type) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = 0;

        if ($viewer->isAdmin()) {
            return true;
        } elseif ($plugin_type === 'purchase') {
            $user_id = $viewer->getIdentity();
        } elseif ($plugin_type === 'sell') {
            if ($viewer->getIdentity() === $item->getOwner()->getIdentity()) {
                return true;
            }
        }

        return (bool) $this->select()
                        ->from($this->info('name'), new Zend_Db_Expr('TRUE'))
                        ->where('source_type = ?', $item->getType())
                        ->where('source_id = ?', $item->getIdentity())
                        ->where('user_id = ?', $user_id)
                        ->where('state =?', 'complete')
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
    }

    /**
     * @param Core_Model_Item_Abstract $subject
     * @param string $state_type
     * @return int
     *
     * state_type : int, string
     * states: pending -> 0
     *         complete -> 2
     *         refuse -> 0
     *         not exist -> -1
     */
    public function getSubjectState(Core_Model_Item_Abstract $subject) {
        $select = $this->select()
                ->from($this->info('name'), array(($this->info('name') . '.state')))
                ->where('source_type = ?', $subject->getType())
                ->where('source_id = ?', $subject->getIdentity())
                ->where('user_id = ?', $subject->getOwner()->getIdentity())
                ->order('order_id DESC')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if ($select === 'pending') {
            return 1;
        } elseif ($select === 'complete') {
            return 2;
        }
//        elseif ($select === 'refuse') {
//            return 3;
//        }
        return 0;
    }

}