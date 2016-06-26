<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminTransactionsController.php 17.09.12 11:18 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heevent_AdminTransactionsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('heevent_admin_main', array(), 'heevent_admin_main_transactions');
  }

  public function indexAction()
  {
    // Make form
    $this->view->formFilter = $formFilter = new Heevent_Form_Admin_Transaction_Filter();
    $this->view->formRefund = $formFilter = new Heevent_Form_Admin_Transaction_Refund();
    if($refundId = $this->getRequest()->refund_id){
      $transactionData = array(
        'state' => 'refunded',
      );
      $orderData = array(
        'state' => 'cancelled',
      );
      $subscriptionsData = array(
        'status' => 'refunded',
      );
      $transactionTable = Engine_Api::_()->getDbTable('transactions', 'heevent');
      $transaction = $transactionTable->fetchrow($transactionTable->select()->where('transaction_id = ?',$refundId));



      $orderTable = Engine_Api::_()->getDbTable('orders', 'heevent');
      $order = $orderTable->fetchrow($orderTable->select()->where('order_id = ?',$transaction->order_id));

      $subscriptionsTable = Engine_Api::_()->getDbTable('subscriptions', 'heevent');
      $subscriptions = $subscriptionsTable->fetchrow($subscriptionsTable->select()->where('subscription_id = ?',$order->source_id));

      $cardTable = Engine_Api::_()->getDbTable('cards', 'heevent');
      $card = $cardTable->fetchrow($cardTable->select()->where('order_id = ?',$order->order_id));

      $this->_getAllParams();
      $transaction->setFromArray($transactionData);
      $transaction->save();

      $order->setFromArray($orderData);
      $order->save();

      $subscriptions->setFromArray($subscriptionsData);
      $subscriptions->save();

      $card->setFromArray($transactionData);
      $card->save();

    }
    // Process form
   if ($formFilter->isValid($this->_getAllParams())) {
      $filterValues = $formFilter->getValues();
    } else {
      $filterValues = array();
    }
    if (empty($filterValues['order'])) {
      $filterValues['order'] = 'transaction_id';
    }
    if (empty($filterValues['direction'])) {
      $filterValues['direction'] = 'DESC';
    }
    $this->view->filterValues = $filterValues;
    $this->view->order = $filterValues['order'];
    $this->view->direction = $filterValues['direction'];

    $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');

    // Initialize select
    /**
     * @var $transactionsTable Offers_Model_DbTable_Transactions
     * @var $subscriptionsTable Offers_Model_DbTable_Subscriptions
     * @var $ordersTable Offers_Model_DbTable_Orders
     * @var $heeventsTable Offers_Model_DbTable_Offers
     * @var $usersTable User_Model_DbTable_Users
     * @var $pagesTable Page_Model_DbTable_Pages
     */
    $pagesTable = null;
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'heevent');
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'heevent');
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'heevent');
    $heeventTable = Engine_Api::_()->getDbtable('events', 'event');
    $usersTable = Engine_Api::_()->getItemTable('user');
    if ($isPageEnabled) {
      $pagesTable = Engine_Api::_()->getItemTable('page');
    }
    $transactionSelect = $transactionsTable->select()
      ->setIntegrityCheck(false)
      ->from(array('t' => $transactionsTable->info('name')))
      ->joinLeft(array('o' => $ordersTable->info('name')), 'o.order_id=t.order_id', array())
      ->joinLeft(array('s' => $subscriptionsTable->info('name')), 's.subscription_id=o.source_id', array())
      ->joinLeft(array('of' => $heeventTable->info('name')), 's.event_id=of.event_id', array('of.event_id'))
    ;

    // Add filter values
    if (!empty($filterValues['gateway_id'])) {
      if ($filterValues['gateway_id'] == 999) {
        $filterValues['gateway_id'] = 0;
      }
      $transactionSelect->where('t.gateway_id = ?', $filterValues['gateway_id']);
    }
    if (!empty($filterValues['event_title'])) {
      $transactionSelect
        ->where('of.title LIKE ?', '%' . $filterValues['event_title'] . '%');
    }

    if (!empty($filterValues['query'])) {
      $transactionSelect
        ->joinInner(array('u' => $usersTable->info('name')), 'u.user_id=t.user_id', null)
        ->where('(t.gateway_transaction_id LIKE ? || ' .
          't.gateway_parent_transaction_id LIKE ? || ' .
          't.gateway_order_id LIKE ? || ' .
          'u.displayname LIKE ? || u.username LIKE ? || ' .
          'u.email LIKE ?)', '%' . $filterValues['query'] . '%');
      ;
    }
    if (($user_id = $this->_getParam('user_id', @$filterValues['user_id']))) {
      $this->view->filterValues['user_id'] = $user_id;
      $transactionSelect->where('t.user_id = ?', $user_id);
    }
    if (!empty($filterValues['order'])) {
      if (empty($filterValues['direction'])) {
        $filterValues['direction'] = 'DESC';
      }
      $transactionSelect->order($filterValues['order'] . ' ' . $filterValues['direction']);
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($transactionSelect);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Preload info
    $gatewayIds = array();
    $userIds = array();
    $orderIds = array();
    $eventIds = array();
    foreach( $paginator as $transaction ) {
      if( !empty($transaction->gateway_id) ) {
        $gatewayIds[] = $transaction->gateway_id;
      }
      if( !empty($transaction->user_id) ) {
        $userIds[] = $transaction->user_id;
      }
      if( !empty($transaction->order_id) ) {
        $orderIds[] = $transaction->order_id;
      }
      if (!empty($transaction->event_id)) {
        $eventIds[] = $transaction->event_id;
      }
    }
    $gatewayIds = array_unique($gatewayIds);
    $userIds = array_unique($userIds);
    $orderIds = array_unique($orderIds);
    $eventIds = array_unique($eventIds);

    // Preload gateways
    $gateways = array();
    if( !empty($gatewayIds) ) {
      foreach( Engine_Api::_()->getDbtable('gateways', 'payment')->find($gatewayIds) as $gateway ) {
        $gateways[$gateway->gateway_id] = $gateway;
      }
    }
    $this->view->gateways = $gateways;

    // Preload users
    $users = array();
    if( !empty($userIds) ) {
      foreach( Engine_Api::_()->getItemTable('user')->find($userIds) as $user ) {
        $users[$user->user_id] = $user;
      }
    }
    $this->view->users = $users;

    // Preload orders
    $orders = array();
    if( !empty($orderIds) ) {
      foreach( Engine_Api::_()->getDbtable('orders', 'heevent')->find($orderIds) as $order ) {
        $orders[$order->order_id] = $order;
      }
    }
    $this->view->orders = $orders;
    // Preload heevent
    $heevent = array();
    if( !empty($eventIds) ) {
      foreach (Engine_Api::_()->getDbtable('events', 'event')->find($eventIds) as $event) {
        $heevent[$event->event_id] = $event;
      }
    }
    $this->view->heevent = $heevent;
  }

  public function detailAction()
  {
    // Missing transaction
    if( !($transaction_id = $this->_getParam('transaction_id')) ||
        !($transaction = Engine_Api::_()->getItem('transaction', $transaction_id)) ) {
      return;
    }

    $this->view->transaction = $transaction;
    $this->view->gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);
    $this->view->order = Engine_Api::_()->getItem('order', $transaction->order_id);
    $this->view->user = Engine_Api::_()->getItem('user', $transaction->user_id);
  }

  public function detailTransactionAction()
  {
    $transaction_id = $this->_getParam('transaction_id');
    $transaction = Engine_Api::_()->getItem('transaction', $transaction_id);
    $gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);

    $link = null;
    if( $this->_getParam('show-parent') ) {
      if( !empty($transaction->gateway_parent_transaction_id) ) {
        $link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_parent_transaction_id);
      }
    } else {
      if( !empty($transaction->gateway_transaction_id) ) {
        $link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_transaction_id);
      }
    }

    if( $link ) {
      return $this->_helper->redirector->gotoUrl($link, array('prependBase' => false));
    } else {
      die();
    }
  }

  public function detailOrderAction()
  {
    $transaction_id = $this->_getParam('transaction_id');
    $transaction = Engine_Api::_()->getItem('transaction', $transaction_id);
    $gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);

    if( !empty($transaction->gateway_order_id) ) {
      $link = $gateway->getPlugin()->getOrderDetailLink($transaction->gateway_order_id);
    } else {
      $link = false;
    }

    if( $link ) {
      return $this->_helper->redirector->gotoUrl($link, array('prependBase' => false));
    } else {
      die();
    }
  }

  public function rawOrderDetailAction()
  {
    // By transaction
    if( null != ($transaction_id = $this->_getParam('transaction_id')) &&
        null != ($transaction = Engine_Api::_()->getItem('transaction', $transaction_id)) ) {
      $gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);
      $gateway_order_id = $transaction->gateway_order_id;
    }

    // By order
    else if( null != ($order_id = $this->_getParam('order_id')) &&
        null != ($order = Engine_Api::_()->getItem('order', $order_id)) ) {
      $gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id);
      $gateway_order_id = $order->gateway_order_id;
    }

    // By raw string
    else if( null != ($gateway_order_id = $this->_getParam('gateway_order_id')) &&
        null != ($gateway_id = $this->_getParam('gateway_id')) ) {
      $gateway = Engine_Api::_()->getItem('payment_gateway', $gateway_id);
    }

    if( !$gateway || !$gateway_order_id  ) {
      $this->view->data = false;
      return;
    }

    $gatewayPlugin = $gateway->getPlugin();

    try {
      $data = $gatewayPlugin->getOrderDetails($gateway_order_id);
      $this->view->data = $this->_flattenArray($data);
    } catch( Exception $e ) {
      $this->view->data = false;
      return;
    }
  }

  public function rawTransactionDetailAction()
  {
    // By transaction
    if( null != ($transaction_id = $this->_getParam('transaction_id')) &&
        null != ($transaction = Engine_Api::_()->getItem('transaction', $transaction_id)) ) {
      $gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);
      $gateway_transaction_id = $transaction->gateway_transaction_id;
    }

    // By order
    else if( null != ($order_id = $this->_getParam('order_id')) &&
        null != ($order = Engine_Api::_()->getItem('order', $order_id)) ) {
      $gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id);
      $gateway_transaction_id = $order->gateway_transaction_id;
    }

    // By raw string
    else if( null != ($gateway_transaction_id = $this->_getParam('gateway_transaction_id')) &&
        null != ($gateway_id = $this->_getParam('gateway_id')) ) {
      $gateway = Engine_Api::_()->getItem('payment_gateway', $gateway_id);
    }

    if( !$gateway || !$gateway_transaction_id  ) {
      $this->view->data = false;
      return;
    }

    $gatewayPlugin = $gateway->getPlugin();

    try {
      $data = $gatewayPlugin->getTransactionDetails($gateway_transaction_id);
      $this->view->data = $this->_flattenArray($data);
    } catch( Exception $e ) {
      $this->view->data = false;
      return;
    }
  }

  protected function _flattenArray($array, $separator = '_', $prefix = '')
  {
    if( !is_array($array) ) {
      return false;
    }

    $flattenedArray = array();
    foreach( $array as $key => $value ) {
      $newPrefix = ( $prefix != '' ? $prefix . $separator : '' ) . $key;
      if( is_array($value) ) {
        $flattenedArray = array_merge($flattenedArray,
            $this->_flattenArray($value, $separator, $newPrefix));
      } else {
        $flattenedArray[$newPrefix] = $value;
      }
    }

    return $flattenedArray;
  }
}