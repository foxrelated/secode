<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: SubscriptionController.php 28.08.12 17:51 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Heevent_SubscriptionController extends Core_Controller_Action_Standard
{
  /**
   * @var User_Model_User
   */
  protected $_user;
  
  /**
   * @var Zend_Session_Namespace
   */
  protected $_session;

  /**
   * @var Heevent_Model_Order
   */
  protected $_order;

  /**
   * @var Payment_Model_Gateway
   */
  protected $_gateway;

  /**
   * @var Heevent_Model_Subscription
   */
  protected $_subscription;

  /**
   * @var Event_Model_Event
   */
  protected $_event;
  
  public function init()
  {
    $this->_session = new Zend_Session_Namespace('Event_Subscription');
    // Get event
    $eventId = $this->_getParam('event_id', $this->_session->event_id);

    if (!$eventId || !($this->_event = Engine_Api::_()->getItem('event', $eventId))) {
     // $this->_goBack(false);
    }


    // Get user and session
    $this->_user = Engine_Api::_()->user()->getViewer();


    // Check viewer and user
    if (!$this->_user || !$this->_user->getIdentity()) {
      if (!empty($this->_session->user_id)) {
        $this->_user = Engine_Api::_()->getItem('user', $this->_session->user_id);
      }
      // If no user, redirect to home?
      if (!$this->_user || !$this->_user->getIdentity()) {
        $this->_session->unsetAll();
        $this->_goBack();
      }
    }
  }

  public function indexAction()
  {
    return $this->_forward('choose');
  }
  
  public function chooseAction()
  {
    print_firebug('choose');
    print_firebug(Zend_Json::encode($this->_getAllParams()));
    // Check subscription status
/*    if ($status = $this->_checkEventStatus()) {
      return $this->_finishPayment($status);\

    }*/
    $checkTicketMax = Engine_Api::_()->getDbTable('cards', 'heevent')->getUserCountBuy($this->_event->event_id)->count;

    $q = $this->_getAllParams();
    $quantity = $q['ticket_quantity']+1;
    if($checkTicketMax>=5){
      $this->_goBack(0);
    }
    if($checkTicketMax+$quantity>=5){

      $this->_goBack(5-$checkTicketMax);
    }

    // Unset certain keys
    unset($this->_session->event_id);
    unset($this->_session->subscription_id);
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);
    unset($this->_session->errorMessage);
    
    // Process
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'heevent');
    $user = $this->_user;

    // Insert the new temporary subscription
    $db = $subscriptionsTable->getAdapter();
    $db->beginTransaction();

    try {
      $values = array(
        'event_id' => $this->_event->event_id,
        'user_id' => $user->getIdentity(),
        'status' => 'initial',
        'active' => false, // Will set to active on payment success
        'creation_date' => new Zend_Db_Expr('NOW()'),
        'coupon_code' =>$this->ticketCode(),
        'quantity' =>$quantity,
      );




      $subscription = $subscriptionsTable->createRow();
      $subscription->setFromArray($values);
      $subscription->save();

      // If the event is free, let's set it active now
      if (!$this->_event->getPrice()) {

        $subscription->setActive(true);
        $subscription->onPaymentSuccess();
        $activity = Engine_Api::_()->getDbTable('actions', 'activity');
        $activity->addActivity($this->_user, $this->_event, 'events_accept');

 /*       $page = $this->_event->getPage();
        if ($page) {
          $action = $activity->addActivity($this->_user, $page, 'page_events_accept', null, array('link' => $this->_event->getLink()));
          $activity->attachActivity($action, $this->_event, Activity_Model_Action::ATTACH_DESCRIPTION);
          $activity->addActivity($this->_user, $this->_event, 'events_accept');
        } else {
          $activity->addActivity($this->_user, $this->_event, 'events_accept');
        }*/
      }

      Engine_Api::_()->getApi('mail', 'core')->sendSystemRaw($user, 'events_subscription_active', array(
        'subscription_title' => $this->_event->title,
        'subscription_description' => $this->_event->description,
        'subscription_terms' => $this->_event->getEventDescription('active'),
        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
          Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
      ));

      $subscription_id = $subscription->subscription_id;

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->_session->subscription_id = $subscription_id;
    $this->_session->quantity = $quantity;

    // Otherwise redirect to the payment page
    return $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
  }

  public function gatewayAction()
  {
    // Get subscription
  $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
    if( !$subscriptionId ||
        !($subscription = Engine_Api::_()->getItem('subscription', $subscriptionId))  ) {
      $this->_goBack();
    }
    $this->view->subscription = $subscription;

    // Check subscription status
/*    if ($status = $this->_checkEventStatus($subscription)) {
      return $this->_finishPayment($status);
    }*/

    // Get subscription
    if (!$this->_user ||
        $subscription->user_id != $this->_user->getIdentity() ||
        !($event = Engine_Api::_()->getItem('event', $subscription->event_id))) {
      $this->_goBack();
    }

    $this->view->event = $event;
    $this->view->count_quantity = $this->_session->quantity;

    // Unset certain keys
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);

    $this->_session->event_id = $event->getIdentity();

    // Gateways

      $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
      $gatewaySelect = $gatewayTable->select()
        ->where('enabled = ?', 1);
      $gateways = $gatewayTable->fetchAll($gatewaySelect);


    $gatewayPlugins = array();
    foreach( $gateways as $gateway ) {
      $gatewayPlugins[] = array(
        'gateway' => $gateway,
      );
    }
    $this->view->gateways = $gatewayPlugins;
  }

  public function processAction()
  {
    print_firebug('process');
    print_firebug(Zend_Json::encode($this->_getAllParams()));
    // Get gateway
    $gatewayId = $this->_getParam('gateway_id', $this->_session->gateway_id);

      if (!$gatewayId ||
          !($gateway = Engine_Api::_()->getItem('payment_gateway', $gatewayId)) ||
          !($gateway->enabled)) {
        return $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
      }



    $this->view->gateway = $gateway;

    // Get subscription
    $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);

    if (!$subscriptionId ||
        !($subscription = Engine_Api::_()->getItem('subscription', $subscriptionId))) {
      $this->_goBack();
    }

    $this->view->subscription = $subscription;

    // Get package
    $event = $subscription->getEvent();

    $this->view->event = $event;

    // Check subscription?
/*    if ($status = $this->_checkEventStatus($subscription) ) {
      return $this->_finishPayment($status);
    }*/

    // Process
    
    // Create order
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'heevent');
    if (!empty($this->_session->order_id)) {
      $previousOrder = $ordersTable->find($this->_session->order_id)->current();
      if ($previousOrder && $previousOrder->state == 'pending') {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }

    $ordersTable->insert(array(
      'user_id' => $this->_user->getIdentity(),
      'gateway_id' => $gateway->gateway_id,
      'state' => 'pending',
      'creation_date' => new Zend_Db_Expr('NOW()'),
      'source_type' => 'subscription',
      'source_id' => $subscription->subscription_id,
      'code' => $this->ticketCode(),

    ));

    $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

    // Unset certain keys
    unset($this->_session->event_id);
    unset($this->_session->subscription_id);
    unset($this->_session->gateway_id);

    // Prepare host info
    $schema = 'http://';
    if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
      $schema = 'https://';
    }
    $host = $_SERVER['HTTP_HOST'];
    

    // Prepare transaction
    $params = array();
    $params['language'] = $this->_user->language;
    $localeParts = explode('_', $this->_user->language);
    if( count($localeParts) > 1 ) {
      $params['region'] = $localeParts[1];
    }
    $params['vendor_order_id'] = $order_id;
    $params['return_url'] = $schema . $host
      . $this->view->url(array('action' => 'return'))
      . '?order_id=' . $order_id
      . '&state=' . 'return';
    $params['cancel_url'] = $schema . $host
      . $this->view->url(array('action' => 'return'))
      . '?order_id=' . $order_id
      . '&state=' . 'cancel';
    $params['ipn_url'] = $schema . $host
      . $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'event'))
      . '?order_id=' . $order_id;

    // Process transaction

      $api = Engine_Api::_()->heevent();
      $gatewayPlugin = $api->getGateway($gateway->gateway_id);
      $plugin = $api->getPlugin($gateway->gateway_id);


    try{
      $transaction = $plugin->createHeeventTransaction($this->_user, $subscription, $event, $params);
    }catch (Exception $e){
      print_die($e.'-');
    }


    // Pull transaction params
    $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
    $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
    $this->view->transactionData = $transactionData = $transaction->getData();

    // Handle redirection
    if ($transactionMethod == 'GET') {
      $transactionUrl .= '?' . http_build_query($transactionData);
      return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
    }

    // Post will be handled by the view script
  }

  public function returnAction()
  {
    print_firebug('return');
    print_firebug(Zend_Json::encode($this->_getAllParams()));
    // Get order
    if (!$this->_user ||
        !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
        !($order = Engine_Api::_()->getItem('order', $orderId)) ||
        $order->source_type != 'subscription' ||
        !($subscription = $order->getSource()) ||
        !($event = $subscription->getEvent())) {
      $this->_goBack();
    }


/*   if (null == ($page = $event->getPage())) {


    }*/

    if (!($gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id))) {
      $this->_goBack();
    }

      $api = Engine_Api::_()->heevent();
      $gatewayPlugin = $api->getGateway($gateway->gateway_id);
      $plugin = $api->getPlugin($gateway->gateway_id);


    // Get gateway plugin
    $this->view->gatewayPlugin = $gatewayPlugin;

    // Process return
    unset($this->_session->errorMessage);
    try {
      $status = $plugin->onEventTransactionReturn($order, $this->_getAllParams());
      if ($status == 'active') {
        $activity = Engine_Api::_()->getDbTable('actions', 'activity');
       // $page = $this->_event->getPage();
       /* if ($page) {
          $action = $activity->addActivity($this->_user, $page, 'page_events_purchase', null, array('link' => $this->_event->getLink()));
          $activity->attachActivity($action, $this->_event, Activity_Model_Action::ATTACH_DESCRIPTION);
          $activity->addActivity($this->_user, $this->_event, 'events_purchase');
        } else {*/
          $activity->addActivity($this->_user, $this->_event, 'event_purchase');
       // }
      }
    } catch( Payment_Model_Exception $e ) {
      $status = 'failure';
      $this->_session->errorMessage = $e->getMessage();
    }
    
    return $this->_finishPayment($status);
  }

  public function finishAction()
  {
    $this->view->status = $status = $this->_getParam('state');
    $this->view->error = $this->_session->errorMessage;
  }

  protected function _checkEventStatus(Zend_Db_Table_Row_Abstract $subscription = null)
  {
    if( !$this->_user ) {
      return false;
    }

    if (null === $subscription) {
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'heevent');
      $subscription = $subscriptionsTable->fetchRow(array(
        'user_id = ?' => $this->_user->getIdentity(),
        'event_id = ?' => $this->_event->getIdentity(),
        'active = ?' => true,
      ));
    }

    if (!$subscription) {
      return false;
    }
    
    if ($subscription->status == 'active' || $subscription->status == 'trial') {
      return 'active';
    } else if ($subscription->status == 'pending') {
      return 'pending';
    }
    
    return false;
  }

  protected function _finishPayment($state = 'active')
  {
    // No user?
    if( !$this->_user ) {
      $this->_goBack();
    }
    
    // Clear session
    $errorMessage = $this->_session->errorMessage;
    $userIdentity = $this->_session->user_id;
    $this->_session->unsetAll();
    $this->_session->user_id = $userIdentity;
    $this->_session->errorMessage = $errorMessage;

    if ($state == 'active') {
      $state = 'accept';
    }

    // Redirect
    return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state));
  }

  protected function _goBack($back = true)
  {
    unset($this->_session->event_id);
    unset($this->_session->subscription_id);
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);
    unset($this->_session->errorMessage);
    if($back == 0){
      $this->_session->errorMessage = 'Exhausted  limit of 5 Tickets';
    }else{
      $this->_session->errorMessage = 'Exhausted  limit of 5 Tickets, You can buy only '.$back.' tickets';
    }




    return $this->_helper->redirector->gotoRoute(array(), 'event_upcoming', true);
  }
  function ticketCode(){

    $length = 10;
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
      $index = rand(0, $count - 1);
      $result .= mb_substr($chars, $index, 1);
    }

    return $this->_event->getIdentity().'-'.$result;
  }
}