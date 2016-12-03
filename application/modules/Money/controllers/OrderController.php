<?php

class Money_OrderController extends Core_Controller_Action_Standard {

    /**
     * subject cost: there may be two cases
     * first: in your table must exist field with name: 'amount'
     * second: you must save your items cost in core settings table with name $itemtype.cost(there my be video.cost or question.cost)
     */
    protected $_subject_cost = 0;

    /**
     * Plugin types: purchase, cell
     */
    protected $_plugin_type = null;

    /**
     * order status
     */
    protected $_order_state = 'pending';

    /**
     * transaction status
     */
    protected $_transaction_state = 'pending';

    /**
     * e-money plugin
     * money.currency from settings table
     */
    protected $_currency = 'USD';

    /**
     * @var null
     */
    protected $_subject_owner_id = null;
    protected $_viewer_id = null;

    public function init() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || !$viewer->getIdentity()) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        $type = $this->_getParam('type');
        $identity = $this->_getParam('id');

        $this->_viewer_id = $viewer->getIdentity();
        $this->_plugin_type = $this->_getParam('plugin_type');
        $this->_currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('money.currency', 'USD');

        if ($type && $identity) {
            $item = Engine_Api::_()->getItem($type, $identity);
            if ($item instanceof Core_Model_Item_Abstract) {
                if (!Engine_Api::_()->core()->hasSubject()) {
                    $this->_subject_owner_id = $item->getOwner()->getIdentity();
                    Engine_Api::_()->core()->setSubject($item);
                }
                if (isset($item->amount)) {
                    $this->_subject_cost = $item->amount;
                } else {
                    $this->_subject_cost = Engine_Api::_()->getDbtable('settings', 'core')->getSetting(str_replace('_', '.', $item->getType()) . '.cost', 0);
                }
            }
        }

        $this->_helper->requireSubject();
    }

    public function paidAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->money = $money = Engine_Api::_()->money()->getUserBalance($viewer);
        $this->view->item = $subject = Engine_Api::_()->core()->getSubject();

        if ($money < $subject->amount) {
            $this->_helper->redirector->gotoRoute(array('action' => 'recharge', 'type' => $subject->getType(),
                'id' => $subject->getIdentity()));
        }
        $this->view->form = $form = new Money_Form_Paid();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = Engine_Api::_()->getDbtable('orders', 'money');

        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $order = $table->setItem($subject, $viewer);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $transactiontable = Engine_Api::_()->getDbtable('transactions', 'money');


        try {
            $transactiontable->createTransaction($order, $subject, $viewer);
        } catch (Money_Model_Exception $e) {
            
        }
        return $this->_forward('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Course was created successfully')),
                    'redirect' => $this->_helper->redirector->gotoUrl($subject->getHref(), array('prependBase' => false))
                ));
    }

    public function paidSubjectAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;


        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        $this->view->subject_cost = $this->_subject_cost;
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->user_balance = $user_balance = Engine_Api::_()->money()->getUserBalance($viewer);

        if ($user_balance < $this->_subject_cost) {
            $this->_helper->redirector->gotoRoute(array(
                'action' => 'recharge',
                'type' => $subject->getType(),
                'id' => $subject->getIdentity()
                    ), 'money_order', true);
        }

        $orderTable = Engine_Api::_()->getDbtable('orders', 'money');
        $transactionTable = Engine_Api::_()->getDbtable('transactions', 'money');
        $moneyTable = Engine_Api::_()->getDbtable('money', 'money');

        $this->view->form = $form = new Money_Form_Paid();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $order_row = array(
            'user_id' => $viewer->getIdentity(),
            'state' => $this->_order_state,
            'creation_date' => date('Y-m-d H:i:s'),
            'source_type' => $subject->getType(),
            'source_id' => $subject->getIdentity(),
            'source_cost' => $this->_subject_cost
        );
        $order = $orderTable->createOrder($order_row);

        if ($this->_plugin_type === 'purchase') {
            $clientRow = array(
                'user_id' => $this->_subject_owner_id,
                'timestamp' => date('Y-m-d H:i:s'),
                'order_id' => $order->order_id,
                'type' => 10,
                'amount' => -$this->_subject_cost,
                'currency' => $this->_currency,
                'state' => $this->_transaction_state
            );

            $transactionTable->createSubjectTransaction($clientRow);
            $moneyTable->setMoney($this->_subject_owner_id, $clientRow['amount']);
        } elseif ($this->_plugin_type === 'sell') {
            $ownerRow = array(
                'user_id' => $this->_subject_owner_id,
                'timestamp' => date('Y-m-d H:i:s'),
                'order_id' => $order->order_id,
                'type' => 10,
                'amount' => $this->_subject_cost,
                'currency' => $this->_currency,
                'state' => $this->_transaction_state,
                'gateway_parent_transaction_id' => $this->_viewer_id
            );

            $transactionTable->createSubjectTransaction($ownerRow);
            $moneyTable->setMoney($this->_subject_owner_id, $ownerRow['amount']);

            $clientRow = array(
                'user_id' => $this->_viewer_id,
                'timestamp' => date('Y-m-d H:i:s'),
                'order_id' => $order->order_id,
                'type' => 10,
                'amount' => -$this->_subject_cost,
                'currency' => $this->_currency,
                'state' => $this->_transaction_state
            );

            $transactionTable->createSubjectTransaction($clientRow);
            $moneyTable->setMoney($this->_viewer_id, $clientRow['amount']);
        } else {
            return;
        }

        $this->_customRedirect($subject);
    }

    protected function _customRedirect($subject) {
        $orderTable = Engine_Api::_()->getDbtable('orders', 'money');
        $subject->setState($orderTable->getSubjectState($subject));

        return $this->_forward('success', 'utility', 'core', array(
                    'messages' => array(
                        Zend_Registry::get('Zend_Translate')->_($subject->getType() . ' was created successfully')
                    ),
                    'redirect' => $this->_helper->redirector->gotoUrl($subject->getHref(), array('prependBase' => false))
                ));
    }

    public function rechargeAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('money_main');

        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        $this->view->subject_cost = $this->_subject_cost;
    }

}
