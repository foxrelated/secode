<?php

class Money_Model_Transaction extends Core_Model_Item_Abstract {

    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'money_general',
            'reset' => true,
            'action' => 'transaction'
                ), $params);

        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    public function getTitle() {
        return 'Transaction';
    }

    public function _postUpdate() {
        $orderTable = Engine_Api::_()->getDbtable('orders', 'money');
        $moneyTable = Engine_Api::_()->getDbtable('money', 'money');

        $row = $orderTable->find($this->order_id)->current();
        $row->state = $this->state;

        if ($this->state === 'refuse') {
            $moneyTable->setMoney($this->user_id, abs($this->amount));
        }

        $row->save();
    }

}