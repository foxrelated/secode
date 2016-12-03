<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGE TICKETS
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('siteevent_admin_main', array(), 'siteeventticket_admin_main_ticket');


    $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('siteeventticket_admin_main_ticket', array(), 'siteeventticket_admin_main_manage');

    //MAKE FORM
    $this->view->formFilter = $formFilter = new Siteeventticket_Form_Admin_Manage_Filter();

    //GET PAGE NUMBER
    $page = $this->_getParam('page', 1);
    //GET USER TABLE NAME
    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

    //GET TICKET TABLE
    $tableTicket = Engine_Api::_()->getDbtable('tickets', 'siteeventticket');
    $ticketTableName = $tableTicket->info('name');

    $getEventTable = Engine_Api::_()->getDbtable('events', 'siteevent');
    $eventTableName = $getEventTable->info('name');


    //MAKE QUERY
    $select = $tableTicket->select()
        ->setIntegrityCheck(false)
        ->from($ticketTableName)
        ->joinLeft($tableUserName, "$ticketTableName.owner_id = $tableUserName.user_id", 'username')
        ->group("$ticketTableName.ticket_id");
    $select->joinLeft($eventTableName, "$ticketTableName.event_id = $eventTableName.event_id", array("title as event"));
    $values = array();

    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    foreach ($values as $key => $value) {

      if (null == $value) {
        unset($values[$key]);
      }
    }

    // searching
    $this->view->owner = '';
    $this->view->title = '';
    $this->view->event = '';
    $this->view->price_min = '';
    $this->view->price_max = '';
    $this->view->status = '';
    $this->view->ticketbrowse = '';

    if (isset($_POST['search'])) {

      if (!empty($_POST['owner'])) {
        $this->view->owner = $_POST['owner'];
        $select->where($tableUserName . '.username  LIKE ?', '%' . trim($_POST['owner']) . '%');
      }

      if (!empty($_POST['title'])) {
        $this->view->title = $_POST['title'];
        $select->where($ticketTableName . '.title LIKE ?', '%' . trim($_POST['title']) . '%');
      }

      if (!empty($_POST['event'])) {
        $this->view->event = $_POST['event'];
        $select->where($eventTableName . '.title LIKE ?', '%' . trim($_POST['event']) . '%');
      }

      if (isset($_POST['price_min']) && $_POST['price_min'] != '') {
        $this->view->price_min = $_POST['price_min'];
        $select->where($ticketTableName . '.price  >=?', trim($_POST['price_min']));
      }

      if (isset($_POST['price_max']) && $_POST['price_max'] != '') {
        $this->view->price_max = $_POST['price_max'];
        $select->where($ticketTableName . '.price  <=?', trim($_POST['price_max']));
      }

      if (!empty($_POST['status'])) {
        $this->view->status = $_POST['status'];
        $_POST['status'] --;
        $select->where($ticketTableName . '.status = ? ', $_POST['status']);
      }
    }

    $values = array_merge(array(
     'order' => 'ticket_id',
     'order_direction' => 'DESC',
        ), $values);

    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'ticket_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    include APPLICATION_PATH . '/application/modules/Siteeventticket/controllers/license/license2.php'; 
  }

  //ACTION FOR VIEWING SITEEVENTTICKET DETAILS
  public function detailAction() {

    //GET THE SITEEVENTTICKET ITEM
    $this->view->siteeventticketDetail = $siteeventticketDetail =  Engine_Api::_()->getItem('siteeventticket_ticket', (int) $this->_getParam('id'));
    $this->view->siteevent = Engine_Api::_()->getItem('siteevent_event', $siteeventticketDetail->event_id);
  }

  //ACTION FOR MULTI-DELETE TICKETS
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          Engine_Api::_()->getItem('siteeventticket_ticket', (int) $value)->delete();
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function ordersAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('siteevent_admin_main', array(), 'siteeventticket_admin_main_ticket');


    $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('siteeventticket_admin_main_ticket', array(), 'siteeventticket_admin_main_orders');

    //PAYMENT FLOW CHECK
    $this->view->paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Siteeventticket_Form_Admin_Filter();

    $page = $this->_getParam('page', 1);

    $getEventTable = Engine_Api::_()->getDbtable('events', 'siteevent');
    $eventTableName = $getEventTable->info('name');

    $userTableName = Engine_Api::_()->getItemTable('user')->info('name');

    $orderTable = Engine_Api::_()->getDbtable('orders', 'siteeventticket');
    $orderTableName = $orderTable->info('name');


    $select = $orderTable->select()
        ->setIntegrityCheck(false)
        ->from($orderTableName)
        ->joinLeft($userTableName, "$orderTableName.user_id = $userTableName.user_id", array("$userTableName.username", "$userTableName.user_id"))
        ->joinLeft($eventTableName, "$orderTableName.event_id = $eventTableName.event_id", array("title"))
        ->group($orderTableName . '.order_id');

    //GET VALUES
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array('order' => 'order_id', 'order_direction' => 'DESC'), $values);

    if (!empty($_POST['username'])) {
      $username = $_POST['username'];
    } elseif (!empty($_GET['username']) && !isset($_POST['post_search'])) {
      $username = $_GET['username'];
    } else {
      $username = '';
    }

    if (!empty($_POST['title'])) {
      $title = $_POST['title'];
    } elseif (!empty($_GET['title']) && !isset($_POST['post_search'])) {
      $title = $_GET['title'];
    } else {
      $title = '';
    }

    if (!empty($_POST['creation_date_start'])) {
      $creation_date_start = $_POST['creation_date_start'];
    } elseif (!empty($_GET['creation_date_start']) && !isset($_POST['post_search'])) {
      $creation_date_start = $_GET['creation_date_start'];
    } else {
      $creation_date_start = '';
    }

    if (!empty($_POST['creation_date_end'])) {
      $creation_date_end = $_POST['creation_date_end'];
    } elseif (!empty($_GET['creation_date_end']) && !isset($_POST['post_search'])) {
      $creation_date_end = $_GET['creation_date_end'];
    } else {
      $creation_date_end = '';
    }

    if (isset($_POST['order_min_amount']) && $_POST['order_min_amount'] != '') {
      $order_min_amount = $_POST['order_min_amount'];
    } elseif (!empty($_GET['order_min_amount']) && !isset($_POST['post_search'])) {
      $order_min_amount = $_GET['order_min_amount'];
    } else {
      $order_min_amount = '';
    }

    if (isset($_POST['order_max_amount']) && $_POST['order_max_amount'] != '') {
      $order_max_amount = $_POST['order_max_amount'];
    } elseif (!empty($_GET['order_max_amount']) && !isset($_POST['post_search'])) {
      $order_max_amount = $_GET['order_max_amount'];
    } else {
      $order_max_amount = '';
    }

    if (isset($_POST['commission_min_amount']) && $_POST['commission_min_amount'] != '') {
      $commission_min_amount = $_POST['commission_min_amount'];
    } elseif (!empty($_GET['commission_min_amount']) && !isset($_POST['post_search'])) {
      $commission_min_amount = $_GET['commission_min_amount'];
    } else {
      $commission_min_amount = '';
    }

    if (isset($_POST['commission_max_amount']) && $_POST['commission_max_amount'] != '') {
      $commission_max_amount = $_POST['commission_max_amount'];
    } elseif (!empty($_GET['commission_max_amount']) && !isset($_POST['post_search'])) {
      $commission_max_amount = $_GET['commission_max_amount'];
    } else {
      $commission_max_amount = '';
    }

    if (!empty($_POST['order_status'])) {
      $order_status = $_POST['order_status'];
    } elseif (!empty($_GET['order_status']) && !isset($_POST['post_search'])) {
      $order_status = $_GET['order_status'];
    } else {
      $order_status = '';
    }

    if (!empty($_POST['display_only_site_payment_orders'])) {
      $display_only_site_payment_orders = $_POST['display_only_site_payment_orders'];
    } elseif (!empty($_GET['display_only_site_payment_orders']) && !isset($_POST['post_search'])) {
      $display_only_site_payment_orders = $_GET['display_only_site_payment_orders'];
    } else {
      $display_only_site_payment_orders = '';
    }

    if (!empty($_POST['payment_gateway'])) {
      $payment_gateway = $_POST['payment_gateway'];
    } elseif (!empty($_GET['payment_gateway']) && !isset($_POST['post_search'])) {
      $payment_gateway = $_GET['payment_gateway'];
    } else {
      $payment_gateway = '';
    }

    if (!empty($_POST['cheque_no'])) {
      $cheque_no = $_POST['cheque_no'];
    } elseif (!empty($_GET['cheque_no']) && !isset($_POST['cheque_no'])) {
      $cheque_no = $_GET['cheque_no'];
    } else {
      $cheque_no = '';
    }

    // searching
    $this->view->username = $values['username'] = $username;
    $this->view->title = $values['title'] = $title;
    $this->view->creation_date_start = $values['creation_date_start'] = $creation_date_start;
    $this->view->creation_date_end = $values['creation_date_end'] = $creation_date_end;
    $this->view->order_min_amount = $values['order_min_amount'] = $order_min_amount;
    $this->view->order_max_amount = $values['order_max_amount'] = $order_max_amount;
    $this->view->commission_min_amount = $values['commission_min_amount'] = $commission_min_amount;
    $this->view->commission_max_amount = $values['commission_max_amount'] = $commission_max_amount;
    $this->view->order_status = $values['order_status'] = $order_status;
    $this->view->payment_gateway = $values['payment_gateway'] = $payment_gateway;
    $this->view->cheque_no = $values['cheque_no'] = $cheque_no;


    if (!empty($username)) {
      $select->where($userTableName . '.username  LIKE ?', '%' . trim($username) . '%');
    }

    if (!empty($title)) {
      $select->where($eventTableName . '.title  LIKE ?', '%' . trim($title) . '%');
    }

    if (!empty($creation_date_start)) {
      $select->where("CAST($orderTableName.creation_date AS DATE) >=?", trim($creation_date_start));
    }

    if (!empty($creation_date_end)) {
      $select->where("CAST($orderTableName.creation_date AS DATE) <=?", trim($creation_date_end));
    }

    if ($order_min_amount != '') {
      $select->where("$orderTableName.grand_total >=?", trim($order_min_amount));
    }

    if ($order_max_amount != '') {
      $select->where("$orderTableName.grand_total <=?", trim($order_max_amount));
    }

    if ($commission_min_amount != '') {
      $select->where("$orderTableName.commission_value >=?", trim($commission_min_amount));
    }

    if ($commission_max_amount != '') {
      $select->where("$orderTableName.commission_value <=?", trim($commission_max_amount));
    }

    if (!empty($order_status)) {
      $order_status--;

      $select->where($orderTableName . '.order_status = ? ', $order_status);
    }

    if (!empty($display_only_site_payment_orders)) {
      $select->where($orderTableName . '.direct_payment = ? ', 0);
    }

    if (!empty($payment_gateway)) {
      $select->where($orderTableName . '.gateway_id = ? ', $payment_gateway);
    }

    if (!empty($cheque_no)) {
      $chequeTableName = Engine_Api::_()->getDbtable('ordercheques', 'siteeventticket')->info('name');
      $select->joinLeft($chequeTableName, "$orderTableName.cheque_id = $chequeTableName.ordercheque_id", array(""));
      $select->where($chequeTableName . '.cheque_no LIKE ? ', '%' . $cheque_no . '%');
    }

    $this->view->order_approve_count = $orderTable->select()->from($orderTableName, array("COUNT(order_id) as order_id"))->where("gateway_id = 3 AND order_status = 0 AND direct_payment = 0")->query()->fetchColumn();
    
    $this->view->showSendTicketLink = Engine_Api::_()->hasModuleBootstrap('sitemailtemplates') && file_exists('application/libraries/dompdf/dompdf_config.inc.php');

    //ASSIGN VALUES TO THE TPL
    $this->view->formValues = array_filter($values);
    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'order_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    include APPLICATION_PATH . '/application/modules/Siteeventticket/controllers/license/license2.php';
  }

  public function viewEventDetailsAction() {

    $event_id = $this->_getParam('event_id', null);

    if (empty($event_id))
      return $this->_forward('notfound', 'error', 'core');

    $eventTable = Engine_Api::_()->getDbtable('events', 'siteevent');
    $eventTableName = $eventTable->info('name');

    $select = $eventTable->select()
        ->from($eventTableName)
        ->where($eventTableName . '.event_id = ?', $event_id);

    $this->view->siteeventDetail = $detail = $eventTable->fetchRow($select);

    $orderTable = Engine_Api::_()->getDbtable('orders', 'siteeventticket');

    $this->view->total_sale_this_year = $orderTable->getTotalSaleThisYear(array('event_id' => $event_id));
    $this->view->event_statistics = $orderTable->getEventStatistics(array('event_id' => $event_id));

    $this->view->approval_pending_orders = $orderTable->getStatusOrders(array('event_id' => $event_id, 'order_status' => 0));
    $this->view->payment_pending_orders = $orderTable->getStatusOrders(array('event_id' => $event_id, 'order_status' => 1));
    $this->view->complete_orders = $orderTable->getStatusOrders(array('event_id' => $event_id, 'order_status' => 2));
  }

  public function paymentApproveAction() {

    $order_id = $this->_getParam('order_id', null);
    $order_obj = Engine_Api::_()->getItem('siteeventticket_order', $order_id);
    $this->view->paymentPending = $paymentPending = $this->_getParam("payment_pending", null);

    if (empty($order_id) || empty($order_obj) || (empty($order_obj->cheque_id) && empty($paymentPending))) {
      return $this->_forward('notfound', 'error', 'core');
    }
    
    $this->view->isAllowPaymentApprove = Engine_Api::_()->siteeventticket()->isAllowPaymentApprove(array('order_id' => $order_id));

    if (empty($paymentPending)) {
      $cheque_detail = Engine_Api::_()->getDbtable('ordercheques', 'siteeventticket')->getChequeDetail($order_obj->cheque_id);
      $this->view->form = $form = new Siteeventticket_Form_Admin_Payment_PaymentApprove($cheque_detail);
    }

      $payment_approve_message = '';

      $tempViewUrl = $this->view->url(array('action' => 'view', 'event_id' => $order_obj->event_id, 'order_id' => $order_obj->order_id, 'menuId' => 55), 'siteeventticket_order', false);

      $payment_approve_message .= '<a href="' . $tempViewUrl . '" target="_blank">#' . $order_obj->order_id . '</a>';
//      $index++;
//    }

    $this->view->payment_approve_message = $payment_approve_message;

    if ($this->getRequest()->isPost()) {
      if (empty($paymentPending)) {
        $form->populate($cheque_detail);
        $grand_total = $order_obj->grand_total;
      }
      $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        if (empty($paymentPending)) {
          $gateway_transaction_id = $_POST['transaction_no'];
          $type = 'cheque';
        } else {
          $gateway_transaction_id = '';
          $type = 'payment';
        }
        $gateway_transaction_id = empty($paymentPending) ? $_POST['transaction_no'] : 0;

        $transactionData = array(
         'user_id' => $order_obj->user_id,
         'gateway_id' => $order_obj->gateway_id,
         'date' => new Zend_Db_Expr('NOW()'),
         'payment_order_id' => 0,
         'order_id' => $order_obj->order_id,
         'gateway_transaction_id' => $gateway_transaction_id,
         'type' => $type,
         'state' => 'okay',
         'amount' => @round($grand_total, 2),
         'currency' => $currencyCode,
         'cheque_id' => $order_obj->cheque_id
        );
        
        Engine_Api::_()->getDbtable('transactions', 'siteeventticket')->insert($transactionData);
        
        if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $transactionParams = array_merge($transactionData, array('resource_type' => 'siteeventticket_order'));
            Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
        }           

        // UPDATE PAYMENT STATUS  - UPDATE ORDER STATUS
        Engine_Api::_()->getDbtable('orders', 'siteeventticket')->update(array("payment_status" => "active", "order_status" => 2), array("order_id =?" => $order_obj->order_id));

        //SEND NOTIFICATION TO SELLER 
        $newVar = (_ENGINE_SSL ? 'https://' : 'http://'). $_SERVER['HTTP_HOST'] ;
        $viewer = Engine_Api::_()->user()->getViewer();
        $order_no = $this->view->htmlLink($this->view->url(array('action' => 'view', 'event_id' => $order_obj->event_id, 'order_id' => $order_obj->order_id), 'siteeventticket_order', true), '#' . $order_obj->order_id);

        $siteevent = Engine_Api::_()->getItem('siteevent_event', $order_obj->event_id);
        $sellerObj = Engine_Api::_()->getItem('user', $siteevent->owner_id);
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($sellerObj, $viewer, $order_obj, 'siteeventticket_payment_approved_by_admin', array('order_id' => $order_no));

        $order_no = '<a href="' . $newVar . $this->view->url(array('action' => 'view', 'event_id' => $order_obj->event_id, 'order_id' => $order_obj->order_id), 'siteeventticket_order', true) . '">#' . $order_obj->order_id . '</a>';
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($sellerObj, 'siteeventticket_payment_approved_by_admin_to_seller', array(
         'order_id' => '#' . $order_obj->order_id,
         'order_no' => $order_no,
         'object_link' => $newVar .
         Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view', 'event_id' => $order_obj->event_id, 'order_id' => $order_obj->order_id), 'siteeventticket_order', true),
        ));
        
        if($order_obj->gateway_id == 1 || $order_obj->gateway_id == 2) {
            Engine_Api::_()->siteeventticket()->orderPlaceMailAndNotification(array('order_id' => $order_obj->order_id, 'activity_feed' => 0, 'seller_email' => 0, 'admin_email' => 0, 'buyer_email' => 1, 'notification_seller' => 0));
        }

        //UPDATE SOLD COUNT OF CORRESPONDING TICKETS ID IN EVENT_OCCURRENCES TABLE.
        Engine_Api::_()->siteeventticket()->updateTicketsSoldQuantity(array('occurrence_id' => $order_obj->occurrence_id));
        
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
       'smoothboxClose' => true,
       'parentRefreshTime' => '100',
       'parentRedirect' => $this->view->url(array('module' => 'siteeventticket', 'controller' => 'manage', 'action' => 'orders'), "admin_default", true),
       'format' => 'smoothbox',
       'messages' => 'Payment approved successfully.'
      ));
    }
  }

  public function commissionAction() {
    
      //GET NAVIGATION
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('siteevent_admin_main', array(), 'siteeventticket_admin_main_ticket');
    
      $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('siteeventticket_admin_main_ticket', array(), 'siteeventticket_admin_main_commission');
     
    $this->view->tab = $tab = $this->_getParam('tab', 0);

    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Siteeventticket_Form_Admin_Manage_Filter();
    //GET VALUES
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array('order' => 'event_id', 'order_direction' => 'DESC'), $values);

    if (!empty($_POST['username'])) {
      $values['username'] = $_POST['username'];
    } elseif (!empty($_GET['username']) && !isset($_POST['post_search'])) {
      $values['username'] = $_GET['username'];
    } else {
      $values['username'] = '';
    }

    if (!empty($_POST['title'])) {
      $values['title'] = $_POST['title'];
    } elseif (!empty($_GET['title']) && !isset($_POST['post_search'])) {
      $values['title'] = $_GET['title'];
    } else {
      $values['title'] = '';
    }

    if (!empty($_POST['starttime']) && !empty($_POST['starttime']['date'])) {
      $values['from'] = $_POST['starttime']['date'];
    } elseif (!empty($_GET['starttime']) && !empty($_GET['starttime']['date']) && !isset($_POST['post_search'])) {
      $values['from'] = $_GET['starttime']['date'];
    } else {
      $values['from'] = '';
    }

    if (!empty($_POST['endtime']) && !empty($_POST['endtime']['date'])) {
      $values['to'] = $_POST['endtime']['date'];
    } elseif (!empty($_GET['endtime']) && !empty($_GET['endtime']['date']) && !isset($_POST['post_search'])) {
      $values['to'] = $_GET['endtime']['date'];
    } else {
      $values['to'] = '';
    }

    if (!empty($_POST['commission_min_amount']) && is_numeric($_POST['commission_min_amount'])) {
      $values['commission_min_amount'] = $_POST['commission_min_amount'];
    } elseif (!empty($_GET['commission_min_amount']) && !isset($_POST['post_search']) && is_numeric($_GET['commission_min_amount'])) {
      $values['commission_min_amount'] = $_GET['commission_min_amount'];
    } else {
      $values['commission_min_amount'] = '';
    }

    if (!empty($_POST['commission_max_amount']) && is_numeric($_POST['commission_max_amount'])) {
      $values['commission_max_amount'] = $_POST['commission_max_amount'];
    } elseif (!empty($_GET['commission_max_amount']) && !isset($_POST['post_search']) && is_numeric($_GET['commission_max_amount'])) {
      $values['commission_max_amount'] = $_GET['commission_max_amount'];
    } else {
      $values['commission_max_amount'] = '';
    }

    if (!empty($_POST['order_min_amount']) && is_numeric($_POST['order_min_amount'])) {
      $values['order_min_amount'] = $_POST['order_min_amount'];
    } elseif (!empty($_GET['order_min_amount']) && !isset($_POST['post_search']) && is_numeric($_GET['order_min_amount'])) {
      $values['order_min_amount'] = $_GET['order_min_amount'];
    } else {
      $values['order_min_amount'] = '';
    }

    if (!empty($_POST['order_max_amount']) && is_numeric($_POST['order_max_amount'])) {
      $values['order_max_amount'] = $_POST['order_max_amount'];
    } elseif (!empty($_GET['order_max_amount']) && !isset($_POST['post_search']) && is_numeric($_GET['order_max_amount'])) {
      $values['order_max_amount'] = $_GET['order_max_amount'];
    } else {
      $values['order_max_amount'] = '';
    }

    if (!empty($_POST['order_id'])) {
      $values['order_id'] = $_POST['order_id'];
    } elseif (!empty($_GET['order_id']) && !isset($_POST['post_search'])) {
      $values['order_id'] = $_GET['order_id'];
    } else {
      $values['order_id'] = '';
    }

    $values['tab'] = $tab;
    $values['page'] = $this->_getParam('page', 1);
    $values['limit'] = 20;

    include APPLICATION_PATH . '/application/modules/Siteeventticket/controllers/license/license2.php';        
    if (empty($tab)) {
      $tempEventPaidCommission = Engine_Api::_()->getDbtable('eventbills', 'siteeventticket')->getPaidCommissionDetail();
      $eventPaidCommission = array();

      foreach ($tempEventPaidCommission as $amount) {
        $eventPaidCommission[$amount['event_id']]['paid_commission'] = $amount['paid_commission'];
        
        if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {  
            $eventPaidCommission[$amount['event_id']]['paid_commission'] = $amount['paid_commission'] + Engine_Api::_()->sitegateway()->getStripeConnectCommission(array('resource_type' => 'siteeventticket_order', 'resource_id' => $amount['event_id'], 'resource_key' => 'event_id', 'payment_split' => 1));
        }        
      }
      
      $this->view->eventPaidCommission = $eventPaidCommission;
    }

    // searching
    $this->view->title = $values['title'];
    $this->view->username = $values['username'];
    $this->view->order_min_amount = $values['order_min_amount'];
    $this->view->order_max_amount = $values['order_max_amount'];
    $this->view->commission_min_amount = $values['commission_min_amount'];
    $this->view->commission_max_amount = $values['commission_max_amount'];
    $this->view->starttime = $values['from'];
    $this->view->endtime = $values['to'];
    $this->view->order_id = $values['order_id'];

    $this->view->formValues = array_filter($values);
    $this->view->assign($values);
    $this->view->currency_symbol = Engine_Api::_()->siteeventticket()->getCurrencySymbol();
  }

  public function reversalCommissionAction() {
    $order_id = $this->_getParam('order_id', null);
    $this->view->order = $order = Engine_Api::_()->getItem('siteeventticket_order', $order_id);
    $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $order->event_id);
    $this->view->eventOwner = $eventOwner = Engine_Api::_()->getItem('user', $siteevent->owner_id);
    $this->view->currency_symbol = Engine_Api::_()->siteeventticket()->getCurrencySymbol();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $reversal_commission_action = $_POST['reversal_commission_action'];
    if ($reversal_commission_action == 1) {
      $order->order_status = 3;
      if (empty($order->eventbill_id)) {
        $order->payment_status = 'not_paid';
      }
      $actionName = 'approved';
    } else if ($reversal_commission_action == 2) {
      $actionName = 'declined';
    } else {
      $actionName = 'put on hold';
    }

    $order->non_payment_admin_reason = $reversal_commission_action;
    $order->non_payment_admin_message = $_POST['non_payment_admin_message'];
    $order->save();

    $newVar = (_ENGINE_SSL ? 'https://' : 'http://'). $_SERVER['HTTP_HOST'] ;
    $orderUrl = $newVar . $this->view->url(array('action' => 'view', 'event_id' => $order->event_id, 'order_id' => $order->order_id, 'menuId' => 55), 'siteeventticket_order', false);
    $order_no = '<a href="' . $orderUrl . '">#' . $order->order_id . '</a>';
    $event_name = '<a href="' . $newVar. $siteevent->getHref() . '">' . $siteevent->getTitle() . '</a>';

    //SEND EMAIL
    Engine_Api::_()->getApi('mail', 'core')->sendSystem($eventOwner, 'siteeventticket_event_commission_reversal_action', array(
     'order_id' => '#' . $order->order_id,
     'order_no' => $order_no,
     'event_title' => $siteevent->getTitle(),
     'event_name' => $event_name,
     'action' => $actionName
    ));
    
    //UPDATE SOLD COUNT OF CORRESPONDING TICKETS ID IN EVENT_OCCURRENCES TABLE.
    Engine_Api::_()->siteeventticket()->updateTicketsSoldQuantity(array('occurrence_id' => $order->occurrence_id));

    $this->_forward('success', 'utility', 'core', array(
     'smoothboxClose' => 300,
     'parentRefresh' => 300,
     'messages' => 'Your action has been submitted and email successfully sent to the event owner.'
    ));
  }

  //ACTION FOR DELETE THE TICKET
  public function deleteAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $ticket_id = $this->_getParam('ticket_id');
    $this->view->ticket_id = $ticket_id;

    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getItem('siteeventticket_ticket', $ticket_id)->delete();
      $this->_forward('success', 'utility', 'core', array(
       'smoothboxClose' => 10,
       'parentRefresh' => 10,
       'messages' => array('Deleted Succesfully.')
      ));
    }
    $this->renderScript('admin-manage/delete.tpl');
  }
}
