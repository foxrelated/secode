<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: OrderController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_OrderController extends Seaocore_Controller_Action_Standard {

    public function init() {

        //TICKET SETTING DISABLED FROM ADMIN SIDE
        $ticket = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1);
        if (empty($ticket)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //LOGGED IN USER VALIDATON 
        if (!$this->_helper->requireUser()->isValid())
            return;
        
        $siteeventticketEventType = Zend_Registry::isRegistered('siteeventticketEventType') ? Zend_Registry::get('siteeventticketEventType') : null;
        if(empty($siteeventticketEventType) || (!empty($siteeventticketEventType) && ($siteeventticketEventType != 'global'))){
          return $this->_forward('notfound', 'error', 'core');
        }
        
        //SET SUBJECT
        $event_id = $this->_getParam('event_id', null);

        if ($event_id) {
            $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
            if ($siteevent && !Engine_Api::_()->core()->hasSubject('siteevent_event')) {
                Engine_Api::_()->core()->setSubject($siteevent);
            }
        }
        //END - SET SUBJECT 
    }

    //ACTION TO DISPLAY AN ORDER DETAILS
    public function viewAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->order_id = $order_id = $this->_getParam('order_id', null);
        $this->view->orderObj = $orderObj = Engine_Api::_()->getItem('siteeventticket_order', $order_id);

        if (empty($order_id) || empty($orderObj)) {
            $this->view->siteeventticket_view_no_permission = true;
            return;
        }
     
        $occurrenceObj = Engine_Api::_()->getItem('siteevent_occurrence', $orderObj->occurrence_id);

        //  WHEN OCCURRENCE HAS BEEN DELETED
        if ($occurrenceObj) {
            $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $occurrenceObj->event_id);
            $this->view->eventTitle = $siteevent->getTitle();
        }

        $this->view->event_id = $orderObj->event_id;
        $event_owner_id = $siteevent->owner_id;

        if ($viewer->level_id == 1 || $viewer_id == $event_owner_id) {
            $this->view->displayAllDetails = true;
        }

        if ($viewer_id != $orderObj->user_id && $viewer->level_id != 1 && $viewer_id != $event_owner_id) {
            $this->view->siteeventticket_view_no_permission = true;
            return;
        }

        //TAX ID NO.
        if ($orderObj->tax_amount) {
            $this->view->tax_id_no = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($orderObj->event_id, 'tax_id_no');
        }

        //CHEQUE DETAILS
        if ($orderObj->gateway_id == 3) {
            $this->view->cheque_info = Engine_Api::_()->getDbtable('ordercheques', 'siteeventticket')->getChequeDetail($orderObj->cheque_id);
        }

        $this->view->callingStatus = $this->_getParam('menuId', 0);
        //DETAILS ORDER_ID
        $this->view->user = Engine_Api::_()->getItem('user', $orderObj->user_id);
        $this->view->orderTickets = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket')->getOrderTicketsDetail(array('order_id' => $order_id));

        $this->view->coupon_details = array();
        $this->view->fixedDiscount = 1;
        if (!empty($orderObj->coupon_detail)) {
            $coupon_details = unserialize($orderObj->coupon_detail);
            if (is_array($coupon_details)) {
                foreach ($coupon_details as $coupon_detail) {
                    $this->view->coupon_details = $coupon_detail;
                    $this->view->fixedDiscount = !empty($coupon_detail['coupon_type']) ? 1 : 0;
                    if ($this->view->fixedDiscount) {
                        $this->view->orderObj->grand_total -= $coupon_detail['coupon_amount'];
                    }
                    break;
                }
            }
        }

        $this->view->isOrderHavingDiscount = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket')->isOrderHavingDiscount(array('order_id' => $order_id));
        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
        $this->view->admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.send.cheque.to', null);
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteevent/View/Helper', 'Siteevent_View_Helper');
        $this->_helper->content
                //->setNoRender()
                ->setEnabled();
    }

    //ACTION TO TAKE PRINT OF INVOICE
    public function printInvoiceAction() {

        //PAYMENT FLOW CHECK
        $this->view->paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);

        $order_id = Engine_Api::_()->siteeventticket()->getEncodeToDecode($this->_getParam('order_id', null));
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if (empty($order_id)) {
            $this->view->siteeventticket_view_no_permission = true;
            return;
        }

        //USER IS BUYER OR NOT
        $this->view->orderObj = $orderObj = Engine_Api::_()->getItem('siteeventticket_order', $order_id);
        $event_owner_id = Engine_Api::_()->getDbtable('events', 'siteevent')->getEventAttribute($orderObj->event_id, 'owner_id');
        if ($viewer_id != $orderObj->user_id && $viewer->level_id != 1 && $viewer_id != $event_owner_id) {
            //IS USER IS EVENT ADMIN OR NOT
            $this->view->siteeventticket_print_invoice_no_permission = true;
            return;
        }
        
        $this->_helper->layout->setLayout('default-simple');
        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');

        if (!empty($orderObj->user_id)) {
            $user_table = Engine_Api::_()->getDbtable('users', 'user');
            $select = $user_table->select()->from($user_table->info('name'), array("email", "displayname"))->where('user_id =?', $orderObj->user_id);
            $this->view->user_detail = $user_table->fetchRow($select);
        }

        // FETCH SITE LOGO OR TITLE
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_pages')->where('name = ?', 'header')->limit(1);

        $info = $select->query()->fetch();
        if (!empty($info)) {
            $page_id = $info['page_id'];

            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_content', array("params"))
                    ->where('page_id = ?', $page_id)
                    ->where("name LIKE '%core.menu-logo%'")
                    ->limit(1);
            $info = $select->query()->fetch();
            $params = json_decode($info['params']);

            if (!empty($params->logo))
                $this->view->logo = $params->logo;
        }

        $this->view->occurrence_id = $orderObj->occurrence_id;

        $this->view->occurrenceObj = $occurrenceObj = Engine_Api::_()->getItem('siteevent_occurrence', $orderObj->occurrence_id);

        //TAX ID NO.
        if ($orderObj->tax_amount) {
            $this->view->tax_id_no = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($orderObj->event_id, 'tax_id_no');
        }
        if ($occurrenceObj) {
            $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $occurrenceObj->event_id);
        }
        
        if ($orderObj->gateway_id == 3)
            $this->view->cheque_info = Engine_Api::_()->getDbtable('ordercheques', 'siteeventticket')->getChequeDetail($orderObj->cheque_id);
        if (empty($this->view->paymentToSiteadmin)) {
            $this->view->eventTitle = $siteevent->title;
            $chequeParams['event_id'] = $orderObj->event_id;
            $this->view->eventChequeDetail = Engine_Api::_()->getDbtable('eventGateways', 'siteeventticket')->getEventChequeDetail($chequeParams);
        } else {
            $this->view->admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.send.cheque.to', null);
        }
        
        $this->view->orderTickets = $orderTickets = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket')->getOrderTicketsDetail(array('order_id' => $order_id));

        $this->view->coupon_details = array();
        $this->view->fixedDiscount = 1;
        if (!empty($orderObj->coupon_detail)) {
            $coupon_details = unserialize($orderObj->coupon_detail);
            if (is_array($coupon_details)) {
                foreach ($coupon_details as $coupon_detail) {
                    $this->view->coupon_details = $coupon_detail;
                    $this->view->fixedDiscount = !empty($coupon_detail['coupon_type']) ? 1 : 0;
                    if ($this->view->fixedDiscount) {
                        $this->view->orderObj->grand_total -= $coupon_detail['coupon_amount'];
                    }
                    break;
                }
            }
        }

        $this->view->isOrderHavingDiscount = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket')->isOrderHavingDiscount(array('order_id' => $order_id));

        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteevent/View/Helper', 'Siteevent_View_Helper');
    }

    //ACTION TO DISPLAY OREDRS OF AN USER
    public function myTicketsAction() {

        //GET VIEWER DETAILS
        $viewer = Engine_Api::_()->user()->getViewer();

        //MUST BE ABLE TO VIEW EVENTS
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "view")) {
           return $this->_forwardCustom('requireauth', 'error', 'core');
        }
        //END

        $this->_helper->content
                ->setNoRender()
                ->setEnabled();
    }

    //ACTION TO DISPLAY CHECKOUT PAGE (BUY TICKETS)
    public function checkoutAction() {

        // Render
        $this->_helper->content
                //->setNoRender()
                ->setEnabled()
        ;

        $isPaymentToSiteEnable = true;

        $this->view->isPaymentToSiteEnable = $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);

        $this->view->event_id = $event_id = $this->_getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $session = new Zend_Session_Namespace('siteeventticket_cart_formvalues');
        $formValues = $session->formValues;
        if (!empty($formValues)) {

            if (!empty($formValues['date_filter_occurrence'])) {
                $occurrence_id = $formValues['date_filter_occurrence'];
            } else {
                $occurrence_id = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurrence();
            }
        }

        // MANAGE COMPLETE CHECKOUT PROCESS
        $checkout_process = array();

        // DIRECT PAYMENT TO SELLER ENABLED
        if (empty($isPaymentToSiteEnable)) {
            $eventEnabledgateway = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($event_id, 'event_gateway');

            if (!empty($eventEnabledgateway)) {
                $siteAdminEnablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.allowed.payment.gateway', array('paypal', 'cheque', 'cod'));
                $eventEnabledgateway = Zend_Json_Decoder::decode($eventEnabledgateway);

                foreach ($eventEnabledgateway as $gatewayName => $gatewayTableId) {
                    if (in_array($gatewayName, $siteAdminEnablePaymentGateway)) {
                        $finalEventEnableGateway[] = $gatewayName;
                    }
                }

                $this->view->payment_gateway = $finalEventEnableGateway;
                if (count($finalEventEnableGateway) == 1 && in_array('cod', $finalEventEnableGateway))
                    $isOnlyCodGatewayEnable = true;
            }

            // IF NO PAYMENT GATEWAY ENABLE
            if (empty($eventEnabledgateway) || empty($finalEventEnableGateway))
                $no_payment_gateway_enable = true;

            if (isset($eventEnabledgateway['cheque']) && !empty($eventEnabledgateway['cheque'])) {
                $chequeParams['event_id'] = $event_id;
                $chequeParams['eventgateway_id'] = $eventEnabledgateway['cheque'];
                $this->view->eventChequeDetail = Engine_Api::_()->getDbtable('eventGateways', 'siteeventticket')->getEventChequeDetail($chequeParams);
            }
        } else {
            $gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
            $enable_gateway = $gateway_table->select()
                    ->from($gateway_table->info('name'), array('gateway_id', 'title', 'plugin'))
                    ->where('enabled = 1')
                    ->query()
                    ->fetchAll();

            $admin_payment_gateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.admin.gateway', array('cheque'));

            if (!empty($admin_payment_gateway)) {
                foreach ($admin_payment_gateway as $payment_gateway) {
                    if ($payment_gateway == 'cheque') {
                        $this->view->by_cheque_enable = true;
                        $this->view->admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.send.cheque.to', null);
                    } else if ($payment_gateway == 'cod') {
                        $this->view->cod_enable = true;
                    }
                }
            }

            if (empty($enable_gateway) && !empty($admin_payment_gateway) && empty($this->view->by_cheque_enable) && !empty($this->view->cod_enable)) {
                $isOnlyCodGatewayEnable = true;
            }
            // IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
            if (empty($enable_gateway) && empty($admin_payment_gateway)) {
                $no_payment_gateway_enable = true;
            }

            $this->view->payment_gateway = $enable_gateway;
        }

        /* Start Coupon Code Work */
        $coupon_session = new Zend_Session_Namespace('siteeventticket_coupon');
        if (!empty($coupon_session->siteeventticketCouponDetail)) {
            //$couponDetail = unserialize($coupon_session->siteeventticketCouponDetail);
            $coupon_event_id = array();
            $coupon_amount = 0;
        }
        /* End Coupon Code Work */

        if (!empty($coupon_event_id))
            $this->view->coupon_event_id = serialize($coupon_event_id);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (empty($_POST)) {
            return;
        } elseif (isset($session->formValues) && $this->_getParam('buyer_details')) {
            /* BUYER DETAIL CASE - FORMVALUES 
             * Array having cart details in **$session->formValues** & Buyer Details in **$_POST**
             * merged in formValues
             */

            $this->view->formValues = $values = array_merge($session->formValues, $_POST);
            $session->siteeventticket_buyer_details = $_POST;
        } else {
            $this->view->formValues = $values = $_POST;
        }

        if (isset($values['date_filter_occurrence']) && $values['date_filter_occurrence']) {
            $selected_occurrence_id = $values['date_filter_occurrence'];
        } else {
            $selected_occurrence_id = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurrence();
        }

        $occurrenceObj = Engine_Api::_()->getItem('siteevent_occurrence', $selected_occurrence_id);
        //CHECK TICKETS AVAILABILITY

        foreach ($values['ticket_column'] as $key => $row) {
            $ticket_id = $key;
            //IF - row[1] QUANTITY IS SELECTED & GREATER THAN ZERO 
            if (isset($row[1]) && $row[1] > 0) {
                $ticketObj = Engine_Api::_()->getItem('siteeventticket_ticket', $ticket_id);
                $ticketDetailArray = $occurrenceObj->ticket_id_sold;
                $ticket_sold = $ticketDetailArray['tid_' . $ticket_id];
                $quantity = $row[1];
                $remainingTickets = $ticketObj->quantity - $ticket_sold;
                if ($quantity > $remainingTickets) {
                    $this->view->ticketUnavailabilityMessage = true;
                    //break foreach
                }
            }
        }

        
        $totalOrderPrice = $values['grandtotal'];

        if (!empty($coupon_amount) && ($totalOrderPrice <= $coupon_amount))
            $totalOrderPrice = 0;

        //FREE ORDER WORK
        if (empty($totalOrderPrice) || $totalOrderPrice == '0.00') {
            $this->view->totalOrderPriceFree = true;
        }

        if (($totalOrderPrice > '0.00' && !empty($no_payment_gateway_enable))) {
            if (empty($isPaymentToSiteEnable)) {
                Zend_Registry::set('siteeventticket_checkout_event_no_payment_gateway_enable', true);
                $this->view->siteeventticket_checkout_event_no_payment_gateway_enable = true;
            } else {
                Zend_Registry::set('siteeventticket_checkout_no_payment_gateway_enable', true);
                $this->view->siteeventticket_checkout_no_payment_gateway_enable = true;
            }
            return;
        }
    }

    public function placeOrderAction() {

        // GET VIEWER
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //PAYMENT FLOW CHECK
        $directPayment = 0;
        $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
        if (empty($isPaymentToSiteEnable)) {
            $directPayment = 1;
        }

        $this->view->event_id = $event_id = $this->_getParam('event_id', null);

        $checkout_process = @unserialize($_POST['checkout_process']);
        if (!empty($_POST['param'])) {
            $payment_info = array();
            $payment_information = array();
            $payment_info = @explode(',', $_POST['param']);

            if (($payment_info[0] != 3)) {
                $payment_information['method'] = $payment_info[0];
            } else {
                $payment_information['method'] = $payment_info[0];
                $payment_information['cheque_no'] = $payment_info[1];
                $payment_information['signature'] = $payment_info[2];
                $payment_information['account_no'] = $payment_info[3];
                $payment_information['routing_no'] = $payment_info[4];
            }
            $checkout_process['payment_information'] = $payment_information;
        }

        global $getEnabledPaymentGateways;
        $setOrderTicketInfo = Engine_Api::_()->siteeventticket()->setOrderTicketInfo();
        $order_table = Engine_Api::_()->getDbtable('orders', 'siteeventticket');
        $order_ticket_table = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket');
        $siteeventBuyEventSteps = Zend_Registry::isRegistered('siteeventBuyEventSteps') ? Zend_Registry::get('siteeventBuyEventSteps') : null;
        if(empty($siteeventBuyEventSteps)) 
          return;

        // PROCESS
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        // GET IP ADDRESS
        $ipObj = new Engine_IP();
        $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));

        try {
            // PAYMENT VIA BY CHEQUE
            if ($checkout_process['payment_information']['method'] == 3) {
                Engine_Api::_()->getDbtable('ordercheques', 'siteeventticket')->insert(array(
                    'cheque_no' => $checkout_process['payment_information']['cheque_no'],
                    'customer_signature' => $checkout_process['payment_information']['signature'],
                    'account_number' => $checkout_process['payment_information']['account_no'],
                    'bank_routing_number' => $checkout_process['payment_information']['routing_no'],
                ));
                $cheque_id = Engine_Api::_()->getDbtable('ordercheques', 'siteeventticket')->getAdapter()->lastInsertId();
            }

            //check if value of grandtotal is 0
            if (isset($_POST['formValues'])) {
                $values = $_POST['formValues'];
            } else {
                return $this->_forward('notfound', 'error', 'core');
            }
            if (isset($values['date_filter_occurrence']) && $values['date_filter_occurrence']) {
                $this->view->checkoutOccurrenceId = $selected_occurrence_id = $values['date_filter_occurrence'];
            } else {
                $this->view->checkoutOccurrenceId = $selected_occurrence_id = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurrence();
            } 
            
            if(empty($setOrderTicketInfo) && empty($getEnabledPaymentGateways)) {
              return $this->_forward('notfound', 'error', 'core');
            }
            
            $occurrenceObj = Engine_Api::_()->getItem('siteevent_occurrence', $selected_occurrence_id);

            try {
                //SAVE ORDER TICKETS DETAILS IN TICKET TABLE.
                $table = Engine_Api::_()->getDbtable('orders', 'siteeventticket');
                $order = $table->createRow();

                $order->user_id = $viewer_id;
                $order->event_id = $event_id;
                $order->occurrence_id = $selected_occurrence_id; //current occurrence_id selected
                $order->order_status = 1;
                $order->occurrence_starttime = $occurrenceObj->starttime;
                $order->occurrence_endtime = $occurrenceObj->endtime;

                //status
                if ($checkout_process['payment_information']['method'] == 3) {
                    $order_status = 0;  // APPROVAL PENDING
                    $payment_status = 'initial';
                } else if ($checkout_process['payment_information']['method'] == 5) {
                    $order_status = 2;  // PROCESSING
                    $payment_status = 'active';
                } else {
                    $order_status = 1;  // PAYMENT PENDING
                    $payment_status = 'initial';
                }
                //status
                $order->order_status = $order_status;
                $order->payment_status = $payment_status;
                $order->creation_date = date('Y-m-d H:i:s');

                $order->gateway_id = $checkout_process['payment_information']['method'];
                $order->ip_address = $ipExpr;
                $order->direct_payment = $directPayment;
                if ($checkout_process['payment_information']['method'] == 3) {
                    $order->cheque_id = $cheque_id;
                }

                $order->save();
            } catch (Exception $e) {
                throw $e;
            }

            try {
                $orderTicketTable = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket');

                $ticket_qty = 0;
                $subtotal = 0;

                foreach ($values['ticket_column'] as $key => $row) {
                    $ticket_id = $key;
                    //IF - row[1] QUANTITY IS SELECTED & GREATER THAN ZERO 
                    if (isset($row[1]) && $row[1] > 0) {
                        $ticketObj = Engine_Api::_()->getItem('siteeventticket_ticket', $ticket_id);
                        $ticketDetailArray = $occurrenceObj->ticket_id_sold;
                        $ticket_sold = $ticketDetailArray['tid_' . $ticket_id];
                        $quantity = $row[1];
                        $discountedPrice = $price = $row[0];
                        if (isset($row['coupon_id']) && !empty($row['coupon_id'])) {
                            $couponObj = Engine_Api::_()->getItem('siteeventticket_coupon', $row['coupon_id']);
                            if (!empty($couponObj)) {
                                if (!empty($couponObj->discount_type)) {
                                    $discountedPrice = $price;
                                } else {
                                    $discountedPrice = $price - (($price * $couponObj->discount_amount) / 100);
                                }
                            }
                        }
                        $remainingTickets = $ticketObj->quantity - $ticket_sold;
                        if ($quantity > $remainingTickets) {
                            $this->view->checkout_place_order_error = '<div style="color:red">' . Zend_Registry::get('Zend_Translate')->_('Sorry, The tickets you have selected are not available. Please go back and make your selection again according to the availability.') . '</div>';
                            //break foreach
                        }

                        //SAVE DETAILS IN ORDER TICKETS TABLE
                        $orderRow = $orderTicketTable->createRow();
                        $orderRow->order_id = $order->order_id;
                        $orderRow->ticket_id = $ticket_id;
                        $orderRow->title = $ticketObj->title; //current occurrence_id selected
                        $orderRow->price = $price;
                        $orderRow->quantity = $quantity;
                        $orderRow->discounted_price = $discountedPrice;
                        $orderRow->save();

                        $ticket_qty = $ticket_qty + $quantity;
                        $subtotal = $subtotal + ($discountedPrice * $quantity);
                    }
                }

                //VALUE OF GRAND TOTAL & ITEM COUNT
                $order->tax_amount = $total_tax = isset($values['tax']) ? @round($values['tax'], 2) : 0;
                $grandtotal = $subtotal + $total_tax;
                $order->ticket_qty = $ticket_qty;
                $order->sub_total = @round($subtotal, 2);
                $order->grand_total = @round($grandtotal, 2);

                $session = new Zend_Session_Namespace('siteeventticket_cart_coupon');
                if (!empty($session->siteeventticketCouponDetail)) {
                    $order->coupon_detail = $session->siteeventticketCouponDetail;
                }

                $order->is_private_order = $_POST['isPrivateOrder'];
                //COMMISSION WORK
                $commission = Engine_Api::_()->siteeventticket()->getOrderCommission($event_id);
                $commission_type = $commission[0];
                $commission_rate = $commission[1];

                // IF COMMISSION VALUE IS FIX.
                if ($commission_type == 0) :
                    $commission_value = $commission_rate;
                else :
                    $commission_value = (@round($subtotal, 2) * $commission_rate) / 100;
                endif;
                $order->commission_type = $commission_type;
                $order->commission_value = $commission_value;
                $order->commission_rate = $commission_rate;
                
                if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)) {
                    $stripeGatewayId = Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => 'Sitegateway_Plugin_Gateway_Stripe', 'columnName' => 'gateway_id'));
                    if($stripeGatewayId == $order->gateway_id) {
                        $order->payment_split = 1;
                    }
                }                    

                //END - COMMISSION WORK
                $order->save();
            } catch (Exception $e) {
                throw $e;
            }

            // COMMIT
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->order_id = Engine_Api::_()->siteeventticket()->getDecodeToEncode($order->order_id);

        $this->view->gateway_id = $checkout_process['payment_information']['method'];
    }

    public function paymentAction() { 
        $gateway_id = $this->_getParam('gateway_id');

        //PAYMENT FLOW CHECK
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);

        if (empty($paymentToSiteadmin)) {
            
            $event_id = $this->_getParam('event_id', null);
            if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
                $validGatewayId = Engine_Api::_()->sitegateway()->getGatewayColumn(array('pluginLike' => 'Sitegateway_Plugin_Gateway_', 'columnName' => 'gateway_id', 'gateway_id' => $gateway_id));
                if (empty($event_id) || ($gateway_id != 2 && !$validGatewayId)) {
                    return $this->_forward('notfound', 'error', 'core');
                }                
            }
            else {
                if (empty($event_id) || ($gateway_id != 2)) {
                    return $this->_forward('notfound', 'error', 'core');
                }                
            }

        }
        $occurrence_id = $this->_getParam('occurrence_id', null);
        $order_id = $order_id = (int) Engine_Api::_()->siteeventticket()->getEncodeToDecode($this->_getParam('order_id'));
        if (empty($gateway_id) || empty($order_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->_session = new Zend_Session_Namespace('Payment_Siteeventticket');
        $this->_session->unsetAll();
        $this->_session->user_order_id = $order_id;


        if (empty($paymentToSiteadmin)) {
            $this->_session->checkout_event_id = $event_id;
            $this->_session->checkout_occurrence_id = $occurrence_id;
        }
        // IF PAYMENT GATEWAY IS 2CHECKOUT
        if ($gateway_id == 1) {
            $order = Engine_Api::_()->getItem('siteeventticket_order', $order_id);
            $gateway = Engine_Api::_()->getItem('siteeventticket_usergateway', 1);
            // Get gateway plugin
            $gatewayPlugin = $gateway->getGateway();
            $gatewayPlugin->createProduct($order->getGatewayParams());
        }

        return $this->_forwardCustom('process', 'payment', 'siteeventticket', array());
    }

    public function successAction() {

        // Render
        $this->_helper->content
                //->setNoRender()
                ->setEnabled()
        ;        
        
        $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if ($this->_getParam('success_id')) {
            $order_id = (int) Engine_Api::_()->siteeventticket()->getEncodeToDecode($this->_getParam('success_id'));
            $state = $error = '';
        } else {
            $session = new Zend_Session_Namespace('Siteeventticket_Order_Payment_Detail');

            if (empty($session->siteeventticketOrderPaymentDetail['success_id']))
                return $this->_forward('notfound', 'error', 'core');

            $order_id = $session->siteeventticketOrderPaymentDetail['success_id'];
            $this->view->state = $state = $session->siteeventticketOrderPaymentDetail['state'];
            $this->view->error = $error = $session->siteeventticketOrderPaymentDetail['errorMessage'];
        }
        $this->view->order_id = $order_id;

        $order_obj = Engine_Api::_()->getItem('siteeventticket_order', $order_id);
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $order_obj->event_id);
        if (empty($order_id) || empty($order_obj)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $orderTicketTable = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket');
        $order_table = Engine_Api::_()->getDbtable('orders', 'siteeventticket');

        $success_message = '<b>' . $this->view->translate("Thanks for your purchase!") . '</b><br/><br/>';
        $success_message .= $this->view->translate("Your Order ID is") . ' ';


        // IF PAYMENT IS SUCCESSFULLY DONE FOR THE ORDER
        if (($order_obj->payment_status == 'active' || $order_obj->gateway_id == 3 || $order_obj->gateway_id == 4) && empty($error)) {
            
            //UPDATE SOLD COUNT OF CORRESPONDING TICKETS ID IN EVENT_OCCURRENCES TABLE.
            Engine_Api::_()->siteeventticket()->updateTicketsSoldQuantity(array('occurrence_id' => $order_obj->occurrence_id));

            // CHANGE EACH ORDER STATUS
            if ($order_obj->gateway_id != 3 && $order_obj->gateway_id != 4) {
                $order_table->update(array('order_status' => 2), array('order_id = ?' => $order_id));
            }
        }
        // SUCCESS MESSAGE

        $tempViewUrl = $this->view->url(array('action' => 'view', 'order_id' => $order_id, 'event_id' => $order_obj->event_id), 'siteeventticket_order', true);
        $viewer_order = '<a href="' . $tempViewUrl . '">#' . $order_id . '</a>';
        $success_message .= $viewer_order . '. <br><br> ';

        if (!empty($viewer_id)) {
            $success_message .= $this->view->translate('%1$sClick here%2$s to view your order.', "<a href='$tempViewUrl'>", "</a>");
        }

        $this->view->success_message = $success_message;

        $order_table->update(array('payment_status' => $order_obj->payment_status), array('order_id = ?' => $order_id));

        //BUYER DETAIL FORM SAVE
        $session = new Zend_Session_Namespace('siteeventticket_cart_formvalues');
        $values = $session->siteeventticket_buyer_details;
        // PROCESS
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $buyerDetails = !empty($values['buyer_detail']) ? $values['buyer_detail'] : array();

        try {
            //SAVE ORDER TICKETS DETAILS IN TICKET TABLE.
            $buyerDetail_table = Engine_Api::_()->getDbtable('buyerdetails', 'siteeventticket');
            foreach ($buyerDetails as $key => $buyers) {
                $ticket_id = $key;
                foreach ($buyers as $buyer) {
                    $buyerDetail = $buyerDetail_table->createRow();
                    $buyerDetail->ticket_id = $ticket_id;
                    $buyerDetail->order_id = $order_id;
                    if (isset($values) && isset($values['buyer_detail']) && !empty($values['buyer_detail'])) {
                        if (isset($buyer['fname'])) {
                            $buyerDetail->first_name = $buyer['fname'];
                        }
                        if (isset($buyer['lname'])) {
                            $buyerDetail->last_name = $buyer['lname'];
                        }
                        if (isset($buyer['email'])) {
                            $buyerDetail->email = $buyer['email'];
                        }
                    }
                    //SAVE A RANDOM ALPHANUMERIC NO. AS BUYER TICKET ID
                    $buyerDetail->buyer_ticket_id = Engine_Api::_()->siteeventticket()->buyerTicketIdGenerate();
                    $buyerDetail->save();
                }
            }
            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //END - BUYER DETAIL SAVE WORK
        
        //THRESHOLD AMOUNT EMAIL NOTIFICATION
        if(Engine_Api::_()->siteeventticket()->isAllowThresholdNotifications(array('event_id' => $order_obj->event_id))) {
                
            $notificationType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.thresholdnotify');    
            $admin_email_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact', null);
            $newVar = (_ENGINE_SSL ? 'https://' : 'http://'). $_SERVER['HTTP_HOST'];
            $event_title_with_link = '<a href="' . $newVar. $siteevent->getHref() . '">' . $siteevent->getTitle() . '</a>';
            $dashboard_commission_bills = '<a href="' . $newVar. $this->view->url(array('action' => 'your-bill', 'event_id' => $siteevent->event_id), 'siteeventticket_order', true) . '">' . $this->view->translate('Click here') . '</a>';
            
            $optionsArray = array(
                    'event_title' => $siteevent->getTitle(),
                    'event_title_with_link' => $event_title_with_link,
                    'dashboard_commission_bills' => $dashboard_commission_bills
                );

            if(in_array('admin', $notificationType) && $admin_email_id) {
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($admin_email_id, 'SITEEVENTTICKET_THRESHOLD_COMMISSION_ADMIN', $optionsArray);             
            }

            if(in_array('owner', $notificationType) && !empty($siteevent->getOwner()->email)) {
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($siteevent->getOwner()->email, 'SITEEVENTTICKET_THRESHOLD_COMMISSION_OWNER', $optionsArray);             
            }        
        }

        $showPrintLink = $order_obj->showPrintLink();
        if ($showPrintLink) {
            Engine_Api::_()->siteeventticket()->orderPlaceMailAndNotification(array('order_id' => $order_id, 'activity_feed' => 1, 'seller_email' => 1, 'admin_email' => 1, 'buyer_email' => 1, 'notification_seller' => 1));
            $this->_helper->layout->setLayout('default');
        }
    }

    public function manageAction() {

        // EVENT ID 
        $this->view->event_id = $event_id = $this->_getParam('event_id', null);
        $viewer = Engine_Api::_()->user()->getViewer();

        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }
        
        $this->view->isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);

        $this->view->canEdit = 1;
        //SEND TAB TO TPL FILE
        $this->view->tab_selected_id = $this->_getParam('tab');
        $this->view->call_same_action = $this->_getParam('call_same_action', 0);

        $params = array();
        $params['event_id'] = $event_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;

        $isSearch = $this->_getParam('search', null);

        if (!empty($isSearch)) {
            $params['search'] = 1;
            $this->view->newOrderStatus = $params['order_status'] = $this->_getParam('status');
        }

        if (isset($_POST['search']) || isset($_POST['showDashboardEventContent'])) {
            $this->_helper->layout->disableLayout();
            $this->view->only_list_content = true;
            if (isset($_POST['search'])) {
                $params['search'] = 1;
                $params['order_id'] = $_POST['order_id'];
                $params['username'] = $_POST['username'];
                $params['creation_date_start'] = $_POST['creation_date_start'];
                $params['creation_date_end'] = $_POST['creation_date_end'];
                $params['order_min_amount'] = $_POST['order_min_amount'];
                $params['order_max_amount'] = $_POST['order_max_amount'];
                $params['commission_min_amount'] = $_POST['commission_min_amount'];
                $params['commission_max_amount'] = $_POST['commission_max_amount'];
                $params['order_status'] = $_POST['order_status'];
            }
        }

        //MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->getOrdersPaginator($params);
        $this->view->total_item = $this->view->paginator->getTotalItemCount();
    }

    public function detailAction() {

        $order_id = $this->_getParam('order_id', null);
        $this->view->orderObj = $orderObj = Engine_Api::_()->getItem('siteeventticket_order', $order_id);

        if (empty($order_id) || empty($orderObj)) {
            $this->view->siteeventticket_view_detail_no_permission = true;
            return;
        }

        $this->_helper->layout->setLayout('default-simple');
        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
        $this->view->eventAddress = Engine_Api::_()->siteeventticket()->getEventAddress($orderObj->event_id);
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $orderObj->event_id);
        $this->view->order_tickets = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket')->getOrderTicketsDetail(array('order_id' => $order_id));

        if (!empty($orderObj->user_id)) {
            $user_table = Engine_Api::_()->getDbtable('users', 'user');
            $select = $user_table->select()->from($user_table->info('name'), array("email", "displayname"))->where('user_id =?', $orderObj->user_id);
            $this->view->user_detail = $user_table->fetchRow($select);
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_pages')->where('name = ?', 'header')->limit(1);

        $info = $select->query()->fetch();
        if (!empty($info)) {
            $page_id = $info['page_id'];

            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_content', array("params"))
                    ->where('page_id = ?', $page_id)
                    ->where("name LIKE '%core.menu-logo%'")
                    ->limit(1);
            $info = $select->query()->fetch();
            $params = json_decode($info['params']);

            if (!empty($params) && !empty($params->logo))
                $this->view->logo = $params->logo;
        }

        $this->view->coupon_details = array();
        $this->view->fixedDiscount = 1;
        if (!empty($orderObj->coupon_detail)) {
            $coupon_details = unserialize($orderObj->coupon_detail);
            if (is_array($coupon_details)) {
                foreach ($coupon_details as $coupon_detail) {
                    $this->view->coupon_details = $coupon_detail;
                    $this->view->fixedDiscount = !empty($coupon_detail['coupon_type']) ? 1 : 0;
                    if ($this->view->fixedDiscount) {
                        $this->view->orderObj->grand_total -= $coupon_detail['coupon_amount'];
                    }
                    break;
                }
            }
        }

        $this->view->isOrderHavingDiscount = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket')->isOrderHavingDiscount(array('order_id' => $order_id));

        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteevent/View/Helper', 'Siteevent_View_Helper');
        $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($siteevent);
    }

    public function transactionAction() {

        //EVENT ID 
        $this->view->event_id = $event_id = $this->_getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }

        $this->view->call_same_action = $this->_getParam('call_same_action', 0);
        $this->view->transaction_state = Engine_Api::_()->getDbtable('transactions', 'siteeventticket')->getTransactionState(true, $event_id);

        $params = array();
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;
        $params['event_id'] = $event_id;

        if (isset($_POST['search'])) {
            $this->_helper->layout->disableLayout();
            $params['search'] = 1;
            $params['date'] = $_POST['date'];
            $params['response_min_amount'] = $_POST['response_min_amount'];
            $params['response_max_amount'] = $_POST['response_max_amount'];
            $params['state'] = $_POST['state'];
            $this->view->only_list_content = true;
        }

        //MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('paymentrequests', 'siteeventticket')->getAllAdminTransactionsPaginator($params);
        $this->view->total_item = $this->view->paginator->getTotalItemCount();
    }

    public function eventTransactionAction() {

        $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);

        if ($isPaymentToSiteEnable) {
            return;
        }
        
        $this->view->event_id = $event_id = $this->_getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }
        //EVENT ID   
        $this->view->tab = $tab = $this->_getParam('tab', 0);

        $commission = Engine_Api::_()->siteeventticket()->getOrderCommission($event_id);

        if (empty($commission[1])) {
            $this->view->commissionFreePackage = true;
        }

        $this->view->call_same_action = $this->_getParam('call_same_action', 0);

        $params = array();
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;
        $params['event_id'] = $event_id;

        if (isset($_POST['search'])) {
            $params['search'] = 1;
            if ($_POST['starttime'] == 'From') {
                $params['from'] = '';
            } else {
                $params['from'] = $_POST['starttime'];
            }

            if ($_POST['endtime'] == 'To') {
                $params['to'] = '';
            } else {
                $params['to'] = $_POST['endtime'];
            }
        }

        if (isset($_POST['is_ajax']) && $_POST['is_ajax']) {
            $this->view->only_list_content = true;
        }
        // ORDER RELATED TRANSACTIONS
        if (empty($tab)) {
            // FETCH EVENT ENABLE GATEWAY
            if (empty($isPaymentToSiteEnable)) {
                $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.allowed.payment.gateway', array('paypal', 'cheque', 'cod'));
                $eventEnabledgateway = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($event_id, 'event_gateway');
                if (!empty($eventEnabledgateway))
                    $this->view->eventEnabledgateway = Zend_Json_Decoder::decode($eventEnabledgateway);
            }

            if (isset($_POST['search'])) {
                $this->_helper->layout->disableLayout();
                $params['username'] = $_POST['username'];
                $params['order_min_amount'] = $_POST['order_min_amount'];
                $params['order_max_amount'] = $_POST['order_max_amount'];
                $params['gateway'] = $_POST['gateway'];
                $this->view->only_list_content = true;
            }

            $this->view->paginator = Engine_Api::_()->getDbtable('transactions', 'siteeventticket')->getOrderTransactionsPaginator($params);
            $this->view->total_item = $this->view->paginator->getTotalItemCount();
        } else {
            if (!isset($_POST['search'])) {
                $session = new Zend_Session_Namespace('Siteeventticket_Event_Bill_Payment_Detail');
                if (!empty($session->siteeventticketEventBillPaymentDetail)) {
                    $this->view->isPayment = true;
                    $paymentDetail = $session->siteeventticketEventBillPaymentDetail;
                    if (isset($paymentDetail['errorMessage']) && !empty($paymentDetail['errorMessage'])) {
                        $this->view->errorMessage = $paymentDetail['errorMessage'];
                    }
                    $this->view->state = $paymentDetail['state'];
                    $session->unsetAll();
                }
            }

            if (isset($_POST['search'])) {
                $this->_helper->layout->disableLayout();
                $params['bill_min_amount'] = $_POST['bill_min_amount'];
                $params['bill_max_amount'] = $_POST['bill_max_amount'];
                $params['payment'] = $_POST['payment'];
                $this->view->only_list_content = true;
            }

            //MAKE PAGINATOR
            $this->view->paginator = Engine_Api::_()->getDbtable('eventbills', 'siteeventticket')->getEventBillPaginator($params);
            $this->view->total_item = $this->view->paginator->getTotalItemCount();
        }
    }

    public function viewOrderTransactionDetailAction() {

      if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, 'edit')->isValid())
          return;

      $this->view->allParams = $this->_getAllParams();
    }

    public function viewTransactionDetailAction() {

        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, 'edit')->isValid())
            return;

        $this->view->allParams = $this->_getAllParams();
    }

    public function paymentApproveAction() {

        $this->view->order_id = $order_id = $this->_getParam('order_id', null);
        $this->view->order_obj = $order_obj = Engine_Api::_()->getItem('siteeventticket_order', $order_id);
        $this->view->gateway_id = $gateway_id = $this->_getParam("gateway_id", null);  

        if (empty($order_id) || empty($order_obj) || empty($gateway_id) || ( empty($order_obj->cheque_id) && $gateway_id == 3 ) || ($gateway_id == 1)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        
        $this->view->isAllowPaymentApprove = Engine_Api::_()->siteeventticket()->isAllowPaymentApprove(array('order_id' => $order_id));        

        if ($gateway_id == 3) {
            $cheque_detail = Engine_Api::_()->getDbtable('ordercheques', 'siteeventticket')->getChequeDetail($order_obj->cheque_id);
            $this->view->form = $form = new Siteeventticket_Form_PaymentApprove($cheque_detail);
        }

        if ($this->getRequest()->isPost()) {
            if ($gateway_id == 3) {
                $form->populate($cheque_detail);
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                if ($gateway_id == 3) {
                    $gateway_transaction_id = $_POST['transaction_no'];
                    $type = 'cheque';
                } else if ($gateway_id == 4) {
                    $gateway_transaction_id = 0;
                    $type = 'pay at the event';
                } else {
                    $gateway_transaction_id = 0;
                    $type = 'payment';
                }

                Engine_Api::_()->siteeventticket()->setOrderTicketInfo();
                
                $transactionData = array(
                    'user_id' => $order_obj->user_id,
                    'gateway_id' => $order_obj->gateway_id,
                    'date' => new Zend_Db_Expr('NOW()'),
                    'payment_order_id' => 0,
                    'order_id' => $order_obj->order_id,
                    'gateway_transaction_id' => $gateway_transaction_id,
                    'type' => $type,
                    'state' => 'okay',
                    'amount' => @round($order_obj->grand_total, 2),
                    'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'),
                    'cheque_id' => $order_obj->cheque_id
                ); 
                
                Engine_Api::_()->getDbtable('transactions', 'siteeventticket')->insert($transactionData);
        
                if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
                    $transactionParams = array_merge($transactionData, array('resource_type' => 'siteeventticket_order'));
                    Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
                }                   

                // UPDATE ORDER STATUS  - STATUS 2 MEANS COMPLETE 
                Engine_Api::_()->getDbtable('orders', 'siteeventticket')->update(array("payment_status" => "active", 'order_status' => 2), array('order_id = ?' => $order_id));

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
                'parentRedirect' => $this->view->url(array('action' => 'manage', 'event_id' => $order_obj->event_id), 'siteeventticket_order', true),
                'format' => 'smoothbox',
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Payment approved successfully.'))
            ));
        }
    }

    public function nonPaymentOrderAction() {
        $order_id = $this->_getParam('order_id', null);
        $this->view->form = $form = new Siteeventticket_Form_Order_NonPayment();

        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        $order = Engine_Api::_()->getItem('siteeventticket_order', $order_id);
        $order->non_payment_seller_reason = $values['non_payment_seller_reason'];
        $order->non_payment_seller_message = $values['non_payment_seller_message'];
        $order->save();

        // SEND MAIL TO SITE ADMIN FOR THIS PAYMENT REQUEST
        $admin_email_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact', null);

        if (!empty($admin_email_id)) {
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $order->event_id);
            $newVar = _ENGINE_SSL ? 'https://' : 'http://';
            $orderUrl = $newVar . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'view', 'event_id' => $order->event_id, 'order_id' => $order->order_id, 'menuId' => 55), 'siteeventticket_order', false);
            $order_no = '<a href="' . $orderUrl . '">#' . $order->order_id . '</a>';
            $event_name = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $siteevent->getHref() . '">' . $siteevent->getTitle() . '</a>';
            $event_owner = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $siteevent->getOwner()->getHref() . '">' . $siteevent->getOwner()->getTitle() . '</a>';

            Engine_Api::_()->getApi('mail', 'core')->sendSystem($admin_email_id, 'siteeventticket_non_payment_order', array(
                'order_id' => '#' . $order->order_id,
                'order_no' => $order_no,
                'event_name' => $event_name,
                'owner_name' => $event_owner,
            ));
        }
        
        //UPDATE SOLD COUNT OF CORRESPONDING TICKETS ID IN EVENT_OCCURRENCES TABLE.
        Engine_Api::_()->siteeventticket()->updateTicketsSoldQuantity(array('occurrence_id' => $order->occurrence_id));        

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => true,
            'parentRedirect' => $this->view->url(array('action' => 'view', 'event_id' => $order->event_id, 'order_id' => $order_id, 'menuId' => 55), 'siteeventticket_order', true),
            'parentRedirectTime' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Order non-payment reported successfully.'))
        ));
    }

    public function adminChequeDetailAction() {
        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
        $this->view->admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.send.cheque.to', null);
    }

    public function eventChequeDetailAction() {
        $this->view->event_id = $event_id = $this->_getParam('event_id', null);
        $this->view->title = $this->_getParam('title', null);
        $chequeParams['event_id'] = $event_id;
        $this->view->eventChequeDetail = Engine_Api::_()->getDbtable('eventGateways', 'siteeventticket')->getEventChequeDetail($chequeParams);
    }

    public function paymentInfoAction() {

        $this->view->event_id = $event_id = $this->_getParam('event_id', null);

        $viewer = Engine_Api::_()->user()->getViewer();
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }

        $isPasswordCorrect = false;
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer) && !empty($viewer_id) && (($viewer->level_id == 1) || empty($viewer->username)))
            $isPasswordCorrect = true;
        
        if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0) && isset($_SESSION['redirect_stripe_connect_oauth_process'])) {
            $isPasswordCorrect = true;
            $this->view->showStripeConnectChecked = true;
            $session = new Zend_Session_Namespace('redirect_stripe_connect_oauth_process');
            $session->unsetAll();
        }

        if (empty($isPasswordCorrect)) {
            if (!$this->getRequest()->isPost())
                return;

            if (empty($_POST['password'])) {
                echo 'payment_info_password_error';
                die;
            }
            if (isset($_POST['password'])) {
                $this->_helper->layout->disableLayout(true);
                $this->view->only_list_content = true;
            }

            $eventOwnerObj = $siteevent->getOwner();

            // MAKING ENCODED PASSWORD STRING
            $passwordString = md5(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.secret', 'staticSalt') . $_POST['password'] . $eventOwnerObj->salt);
            if ($passwordString === $eventOwnerObj->password)
                $isPasswordCorrect = true;
        }

        if (!empty($isPasswordCorrect)) { 
            $this->view->authenticationSuccess = true;
            $this->view->form = $form = new Siteeventticket_Form_Order_PayPal();

            //PAYMENT FLOW CHECK
            $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
            
            $this->view->paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.paymentmethod', 'paypal');

            $directPaymentEnable = false;
            if (empty($paymentToSiteadmin)) {
                $directPaymentEnable = true;
                $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.allowed.payment.gateway', array('paypal', 'cheque', 'cod'));
                $eventEnabledgateway = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($event_id, 'event_gateway');
                if (!empty($eventEnabledgateway))
                    $eventEnabledgateway = Zend_Json_Decoder::decode($eventEnabledgateway);
            }
            
            if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) { 
                
                $getEnabledGateways = Engine_Api::_()->sitegateway()->getAdditionalEnabledGateways(array('pluginLike' => 'Sitegateway_Plugin_Gateway_'));
                $otherGateways = array();
                foreach($getEnabledGateways as $getEnabledGateway) {
                    
                    $gatewyPlugin = explode('Sitegateway_Plugin_Gateway_', $getEnabledGateway->plugin);
                    $gatewayKey = strtolower($gatewyPlugin[1]);
                    $gatewayKeyUC = ucfirst($gatewyPlugin[1]);

                    $this->view->showStripeConnect = 0;
                    if($gatewayKey == 'stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)) {
                        $this->view->showStripeConnect = 1;
                        $this->view->stripeConnected = 0;
                        $eventGatewayObj = Engine_Api::_()->getDbtable('gateways', 'siteeventticket')->fetchRow(array('event_id = ?' => $event_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_Stripe\''));
                        if (!empty($eventGatewayObj)) {

                            $gateway_id = $eventGatewayObj->gateway_id;

                            if (!empty($gateway_id)) {

                                // Get gateway
                                $gateway = Engine_Api::_()->getItem("siteeventticket_gateway", $gateway_id);
                                if (is_array($gateway->config) && !empty($gateway->config['stripe_user_id'])) {
                                   $this->view->stripeConnected = 1;
                                }
                            }
                        }              

                        if ((!empty($eventEnabledgateway['stripe']) || empty($directPaymentEnable))) {
                            $eventGatewayObj = Engine_Api::_()->getDbtable('gateways', 'siteeventticket')->fetchRow(array('event_id = ?' => $event_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_Stripe\''));
                            if (!empty($eventGatewayObj)) {

                                $gateway_id = $eventGatewayObj->gateway_id;

                                if (!empty($gateway_id)) {
                                    $this->view->stripeEnabled = true;
                                }
                            }
                        }                    
                    }
                    else {
                        $formName = "form$gatewayKeyUC";
                        $formClass = "Sitegateway_Form_Order_$gatewayKeyUC";
                        $this->view->$formName = $form =  new $formClass();
                        
                        $form->setName("siteeventticket_payment_info_$gatewayKey");
                        if ((!empty($eventEnabledgateway[$gatewayKey]) || empty($directPaymentEnable))) {
                            $eventGatewayObj = Engine_Api::_()->getDbtable('gateways', 'siteeventticket')->fetchRow(array('event_id = ?' => $event_id, 'plugin = ?' => $getEnabledGateway->plugin));
                            if (!empty($eventGatewayObj)) {

                                $gateway_id = $eventGatewayObj->gateway_id;

                                if (!empty($gateway_id)) {
                                    $gatewyEnabled = $gatewayKey.'Enabled';
                                    $this->view->$gatewyEnabled = true;

                                    // Get gateway
                                    $gateway = Engine_Api::_()->getItem("siteeventticket_gateway", $gateway_id);
                                    // Populate form
                                    $form->populate($gateway->toArray());

                                    if (is_array($gateway->config)) {
                                        $form->populate($gateway->config);
                                    }
                                }
                            }
                        }                     
                    }                      
                }
            }

            if (!empty($eventEnabledgateway['paypal']) || empty($directPaymentEnable)) {
                $eventGatewayObj = Engine_Api::_()->getDbtable('gateways', 'siteeventticket')->fetchRow(array('event_id = ?' => $event_id, 'plugin LIKE \'Payment_Plugin_Gateway_PayPal\''));
                if (!empty($eventGatewayObj)) {
                    $gateway_id = $eventGatewayObj->gateway_id;

                    if (!empty($gateway_id)) {
                        $this->view->paypalEnable = true;
                        // Get gateway
                        $gateway = Engine_Api::_()->getItem("siteeventticket_gateway", $gateway_id);

                        // Populate form
                        $form->populate($gateway->toArray());
                        if (is_array($gateway->config)) {
                            $form->populate($gateway->config);
                        }
                    }
                }
            }

            if (isset($eventEnabledgateway) && !empty($eventEnabledgateway['cheque'])) {
                $this->view->bychequeEnable = true;
                $this->view->bychequeDetail = Engine_Api::_()->getDbtable('eventGateways', 'siteeventticket')->fetchRow(array('event_id = ?' => $event_id, "title = 'ByCheque'"))->details;
            }

            if (isset($eventEnabledgateway) && !empty($eventEnabledgateway['cod'])) {
                $this->view->codEnable = true;
            }
        } else {
            echo 'payment_info_password_error';
            die;
        }
    }

    public function setPaymentInfoAction() {
        $values = array();
        $values['username'] = $_POST['username'];
        $values['password'] = $_POST['password'];
        $values['signature'] = $_POST['signature'];
        $values['enabled'] = $_POST['enabled'];
        $event_id = $_POST['event_id'];
        $email = $_POST['email'];

        $form = new Siteeventticket_Form_Order_PayPal();

        $payment_info_error = false;

        if (!$form->isValid(array('email' => $email))) {
            $payment_info_error = true;
            $this->view->email_error = $this->view->translate('Please enter a valid email address.');
        }

        if (empty($values['username']) || empty($values['password']) || empty($values['signature'])) {
            $payment_info_error = true;
            $this->view->paypal_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
        }

        $setOrderTicketInfo = Engine_Api::_()->siteeventticket()->setOrderTicketInfo();
        if (empty($setOrderTicketInfo) || !empty($payment_info_error)) {
            return;
        }


        $siteeventticket_gateway_table = Engine_Api::_()->getDbtable('gateways', 'siteeventticket');
        $gateway_id = $siteeventticket_gateway_table->fetchRow(array('event_id = ?' => $event_id, 'plugin = \'Payment_Plugin_Gateway_PayPal\''))->gateway_id;

        $enabled = (bool) $values['enabled'];
        $success_message = $error_message = false;
        unset($values['enabled']);

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        // Process
        try {
            //GET VIEWER ID
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            if (empty($gateway_id)) {
                $row = $siteeventticket_gateway_table->createRow();
                $row->event_id = $event_id;
                $row->user_id = $viewer_id;
                $row->email = $email;
                $row->title = 'Paypal';
                $row->description = '';
                $row->plugin = 'Payment_Plugin_Gateway_PayPal';
                $obj = $row->save();

                $gateway = $row;
            } else {
                $gateway = Engine_Api::_()->getItem("siteeventticket_gateway", $gateway_id);
                $gateway->email = $email;
                $gateway->save();
            }
            $db->commit();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        // Validate gateway config
        if ($enabled) {
            $gatewayObject = $gateway->getGateway();

            try {
                $gatewayObject->setConfig($values);
                $response = $gatewayObject->test();
            } catch (Exception $e) {
                $enabled = false;
                // $form->populate(array('enabled' => false));
                $error_message = $this->view->translate(sprintf('Gateway login failed. Please double check your connection information. The gateway has been disabled. The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
            }
        } else {
            $error_message = $this->view->translate('Gateway is currently disabled.');
        }

        // Process
        $message = null;
        try {
            $values = $gateway->getPlugin()->processAdminGatewayForm($values);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $values = null;
        }

        if (empty($values['username']) || empty($values['password']) || empty($values['signature'])) {
            $values = null;
        }

        if (null !== $values) {
            $gateway->setFromArray(array(
                'enabled' => $enabled,
                'config' => $values,
            ));
            $gateway->save();
            $success_message = $this->view->translate('Changes saved.');
        } else {
            if (!$error_message) {
                $error_message = $message;
            }
        }

        $this->view->success_message = $success_message;
        $this->view->error_message = $error_message;
    }
    
    public function setPaymentInfoAdditionalGatewayAction() {
        
        $values = array();
        @parse_str($_POST['gatewayCredentials'], $values);
        $gatewayCredentials = $values;
        $values['enabled'] = $_POST['enabled'];
        $event_id = $_POST['event_id'];
        $gatewayName = $_POST['gatewayName'];
        $gatewayNameUC = ucfirst($gatewayName);
        
        $siteeventticket_gateway_table = Engine_Api::_()->getDbtable('gateways', 'siteeventticket');
        $gatewayRow = $siteeventticket_gateway_table->fetchRow(array('event_id = ?' => $event_id, 'plugin = ?' => "Sitegateway_Plugin_Gateway_$gatewayNameUC"))->gateway_id; 
        
        $payment_info_error = false;
        
        $showInfoError = false;
        foreach($gatewayCredentials as $gatewayParam) {
            if(empty($gatewayParam)) {
                $showInfoError = true;
                break;
            }
        }        

        if ($showInfoError) {
            $payment_info_error = true;
            $gateway_info_error = $gatewayName."_info_error";
            $this->view->$gateway_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
        }

        $setOrderTicketInfo = Engine_Api::_()->siteeventticket()->setOrderTicketInfo();
        if (empty($setOrderTicketInfo) || !empty($payment_info_error)) {
            return;
        }

        $enabled = (bool) $values['enabled'];
        $success_message = $error_message = false;
        unset($values['enabled']);

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        // Process
        try {
            //GET VIEWER ID
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();
            $gateway_id = $gatewayRow->gateway_id;
            if (empty($gateway_id)) {
                $row = $siteeventticket_gateway_table->createRow();
                $row->event_id = $event_id;
                $row->user_id = $viewer_id;
                $row->email = $viewer->email;
                $row->title = "$gatewayNameUC";
                $row->description = '';
                $row->plugin = "Sitegateway_Plugin_Gateway_$gatewayNameUC";
                $row->save();

                $gateway = $row;
            } else {
                $gateway = Engine_Api::_()->getItem("siteeventticket_gateway", $gateway_id);
                $gateway->email = $viewer->email;
                $gateway->save();
            }
            $db->commit();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        // Validate gateway config
        if ($enabled) {
            $gatewayObject = $gateway->getGateway();

            try {
                $gatewayObject->setConfig($values);
                $gatewayObject->test();

            } catch (Exception $e) {
                $enabled = false;
                $error_message = $this->view->translate(sprintf('Gateway login failed. Please double check your connection information. The gateway has been disabled. The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
            }
        } else {
            $error_message = $this->view->translate('Gateway is currently disabled.');
        }

        // Process
        $message = null;
        try {
            $values = $gateway->getPlugin()->processAdminGatewayForm($values);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $values = null;
        }

        if (!$showInfoError) {
            $gateway->setFromArray(array(
                'enabled' => $enabled,
                'config' => $values,
            ));
            $gateway->save();
            $success_message = $this->view->translate('Changes saved.');
        } else {
            if (!$error_message) {
                $error_message = $message;
            }
        }

        $this->view->success_message = $success_message;
        $this->view->error_message = $error_message;
    }    

    public function setEventGatewayInfoAction() {

        if (!empty($_POST)) {
            $isPaypalChecked = $_POST['isPaypalChecked'];
            $isByChequeChecked = $_POST['isByChequeChecked'];
            $isCodChecked = isset($_POST['isCodChecked']) ? $_POST['isCodChecked'] : false;
            $event_id = $_POST['event_id'];
            $eventChequeInfo = @trim($_POST['bychequeGatewayDetail']);
        }

        if (!empty($event_id))
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (empty($siteevent))
            return;

        global $getEnabledPaymentGateways;
        $payment_info_error = false;
        if ($isPaypalChecked == "true") {
            $paypalDetails = array();
            @parse_str($_POST['paypalGatewayDetail'], $paypalDetails);

            $paypalEmail = $paypalDetails['email'];
            unset($paypalDetails['email']);

            if (!empty($paypalDetails)) {
                $form = new Siteeventticket_Form_Order_PayPal();

                if (!$form->isValid(array('email' => $paypalEmail))) {
                    $payment_info_error = true;
                    $this->view->email_error = $this->view->translate('Please enter a valid email address.');
                }

                if (empty($paypalDetails['username']) || empty($paypalDetails['password']) || empty($paypalDetails['signature'])) {
                    $payment_info_error = true;
                    $this->view->paypal_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
                }
            } else {
                $payment_info_error = true;
                $this->view->paypal_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
            }
        }
        
        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $gatewayDatasValidation = array();
            foreach($_POST['additionalGatewaysCheckedArray'] as $key => $additionalGatewaysCheckedArray) {

                if($additionalGatewaysCheckedArray) {
                    $gatewayKey = ltrim($key, 'is');
                    $gatewayKeyFinal = substr($gatewayKey, 0, -7);
                    $gatewayKeyFinal = strtolower($gatewayKeyFinal);
                    $gatewayKeyFinalUC = ucfirst($gatewayKeyFinal);
                    
                    $gatewayDetailsArray = array();
                    
                    if($gatewayKeyFinal == 'stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)) {
                        $siteeventticket_gateway_table = Engine_Api::_()->getDbtable('gateways', 'siteeventticket');
                        $siteeventticket_gateway_table_obj = $siteeventticket_gateway_table->fetchRow(array('event_id = ?' => $event_id, 'plugin = \'Sitegateway_Plugin_Gateway_Stripe\''));
                        if(empty($siteeventticket_gateway_table_obj->config['stripe_user_id'])) {
                                $payment_info_error = true;
                                $this->view->stripe_info_error = $this->view->translate("Please click on 'Connect with Stripe' button before saving the changes.");
                        } else {
                           $gatewayDatasValidation['stripe']['eventGatewayId'] = $siteeventticket_gateway_table_obj->gateway_id;
                            $gatewayDatasValidation['stripe']['gatewayEnabled'] = true;
                        }
                    }
                    else {
                        
                        @parse_str($_POST['additionalGatewayDetailArray'][$gatewayKeyFinal."GatewayDetail"], $gatewayDetailsArray[$gatewayKeyFinal]);
                        
                        $showInfoError = false;
                        foreach($gatewayDetailsArray[$gatewayKeyFinal] as $gatewayParam) {
                            if(empty($gatewayParam)) {
                                $showInfoError = true;
                                break;
                            }
                        }

                        $gateway_info_error = $gatewayKeyFinal."_info_error";
                        if (!empty($gatewayDetailsArray[$gatewayKeyFinal])) {
                            $formClass = "Sitegateway_Form_Order_$gatewayKeyFinalUC";
                            $form = new $formClass();

                            if ($showInfoError) {
                                $payment_info_error = true;
                                $this->view->$gateway_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
                            }
                        } else {
                            $payment_info_error = true;
                            $this->view->$gateway_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
                        }                        
                    }
                }
            }            
        }        
        
        $setOrderTicketInfo = Engine_Api::_()->siteeventticket()->setOrderTicketInfo();
        if ($isByChequeChecked == "true") {
            if (empty($eventChequeInfo)) {
                $payment_info_error = true;
                $this->view->cheque_info_error = $this->view->translate('Please enter your cheque details.');
            }
        }

        if (empty($setOrderTicketInfo) || empty($getEnabledPaymentGateways) || !empty($payment_info_error))
            return;

        // IF PAYPAL GATEWAY IS ENABLE, THEN INSERT PAYPAL ENTRY IN ENGINE4_SITEEVENTTICKET_GATEWAY TABLE
        if ($isPaypalChecked == "true" && !empty($paypalDetails)) {
            $siteeventticket_gateway_table = Engine_Api::_()->getDbtable('gateways', 'siteeventticket');
            $siteeventticket_gateway_table_obj = $siteeventticket_gateway_table->fetchRow(array('event_id = ?' => $event_id, 'plugin = \'Payment_Plugin_Gateway_PayPal\''));
            if (!empty($siteeventticket_gateway_table_obj))
                $gateway_id = $siteeventticket_gateway_table_obj->gateway_id;
            else
                $gateway_id = 0;

            $success_message = $error_message = false;

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            $paypalEnabled = true;
            // Process
            try {
                //GET VIEWER ID
                $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                if (empty($gateway_id)) {
                    $row = $siteeventticket_gateway_table->createRow();
                    $row->event_id = $event_id;
                    $row->user_id = $viewer_id;
                    $row->email = $paypalEmail;
                    $row->title = 'Paypal';
                    $row->description = '';
                    $row->plugin = 'Payment_Plugin_Gateway_PayPal';
                    $obj = $row->save();

                    $gateway = $row;
                } else {
                    $gateway = Engine_Api::_()->getItem("siteeventticket_gateway", $gateway_id);
                    $gateway->email = $paypalEmail;
                    $gateway->save();
                }
                $db->commit();
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            // Validate gateway config
            $gatewayObject = $gateway->getGateway();

            try {
                $gatewayObject->setConfig($paypalDetails);
                $response = $gatewayObject->test();
            } catch (Exception $e) {
                $paypalEnabled = false;
                // $form->populate(array('enabled' => false));
                $error_message = $this->view->translate(sprintf('Gateway login failed. Please double check your connection information. The gateway has been disabled. The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
            }

            // Process
            $message = null;
            try {
                $values = $gateway->getPlugin()->processAdminGatewayForm($paypalDetails);
            } catch (Exception $e) {
                $message = $e->getMessage();
                $values = null;
            }

            if (empty($paypalDetails['username']) || empty($paypalDetails['password']) || empty($paypalDetails['signature'])) {
                $paypalDetails = null;
            }

            if (null !== $paypalDetails) {
                $gateway->setFromArray(array(
                    'enabled' => $paypalEnabled,
                    'config' => $paypalDetails,
                ));
                $gateway->save();
                $eventPaypalId = $gateway->gateway_id;
            } else {
                if (!$error_message) {
                    $error_message = $message;
                }
            }

            $this->view->error_message = $error_message;
        }
        
        if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && !empty($gatewayDetailsArray)) {
            
            foreach($gatewayDetailsArray as $key => $gatewayDetails) {
                
                $gatewayKeyFinalUC = ucfirst($key);
                
                $siteeventticket_gateway_table = Engine_Api::_()->getDbtable('gateways', 'siteeventticket');
                $siteeventticket_gateway_table_obj = $siteeventticket_gateway_table->fetchRow(array('event_id = ?' => $event_id, 'plugin = ?' => "Sitegateway_Plugin_Gateway_$gatewayKeyFinalUC"));
                
                if (!empty($siteeventticket_gateway_table_obj))
                    $gateway_id = $siteeventticket_gateway_table_obj->gateway_id;
                else
                    $gateway_id = 0;

                $error_message_additional_gateway = false;

                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();

                $gatewayDatasValidation[$key]['gatewayEnabled'] = true;
                // Process
                try {
                    //GET VIEWER ID
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $viewer_id = $viewer->getIdentity();
                    if (empty($gateway_id)) {
                        $row = $siteeventticket_gateway_table->createRow();
                        $row->event_id = $event_id;
                        $row->user_id = $viewer_id;
                        $row->email = $viewer->email;
                        $row->title = "$gatewayKeyFinalUC";
                        $row->description = '';
                        $row->plugin = "Sitegateway_Plugin_Gateway_$gatewayKeyFinalUC";
                        $obj = $row->save();

                        $gateway = $row;
                    } else {
                        $gateway = Engine_Api::_()->getItem("siteeventticket_gateway", $gateway_id);
                        $gateway->email = $viewer->email;;
                        $gateway->save();
                    }
                    $db->commit();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                // Validate gateway config
                $gatewayObject = $gateway->getGateway();

                try {
                    $gatewayObject->setConfig($gatewayDetails);
                    $response = $gatewayObject->test();
                } catch (Exception $e) {
                    
                    $gatewayDatasValidation[$key]['gatewayEnabled'] = false;
                    $error_message_additional_gateway = $this->view->translate(sprintf('Gateway login failed. Please double check your connection information. The gateway has been disabled. The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
                }

                // Process
                $message_additional_gateway = null;
                try {
                    $values = $gateway->getPlugin()->processAdminGatewayForm($gatewayDetails);
                } catch (Exception $e) {
                    $message_additional_gateway = $e->getMessage();
                    $values = null;
                }
                
                $formValuesValidation = true;
                foreach($gatewayDetails as $gatewayParam) {
                    if(empty($gatewayParam)) {
                        $formValuesValidation = false;
                        break;
                    }
                }                

                if ($formValuesValidation) {
                    $gateway->setFromArray(array(
                        'enabled' => $gatewayDatasValidation[$key]['gatewayEnabled'],
                        'config' => $gatewayDetails,
                    ));
                    $gateway->save();
                    $gatewayDatasValidation[$key]['eventGatewayId'] = $gateway->gateway_id;
                } elseif (!$error_message_additional_gateway) {
                        $error_message_additional_gateway = $message_additional_gateway;
                }

                $error_message_gateway = "error_message_$key";
                $this->view->$error_message_gateway = $error_message_additional_gateway;
            }
        }        

        // IF BYCHEQUE OR COD ENABLED, THEN SAVE THEIR ENTRY IN EVENTGATEWAY TABLE
        if ($isByChequeChecked == "true" || $isCodChecked == "true") {
            $siteeventticket_event_gateway_table = Engine_Api::_()->getDbtable('eventGateways', 'siteeventticket');

            if (!empty($eventChequeInfo))
                $siteeventticket_event_gateway_table->update(array("details" => $eventChequeInfo, "title" => "ByCheque"), array('event_id =?' => $event_id));

            $eventByChequeDetail = $siteeventticket_event_gateway_table->fetchRow(array('event_id = ?' => $event_id, "title = 'ByCheque'"));
            if (!empty($eventByChequeDetail))
                $eventByChequeId = $eventByChequeDetail->eventgateway_id;
            else
                $eventByChequeId = '';

            if ($isByChequeChecked == "true") {
                if (!empty($eventByChequeId)) {
                    $eventByChequeDetail->enabled = 1;
                    $eventByChequeDetail->save();
                } else {
                    $siteeventticket_event_gateway_table->insert(array(
                        'event_id' => $event_id,
                        'title' => 'ByCheque',
                        'details' => $eventChequeInfo,
                        'enabled' => 1
                    ));
                    $eventByChequeId = $siteeventticket_event_gateway_table->getAdapter()->lastInsertId();
                }
            } else if (!empty($eventByChequeId)) {
                $eventByChequeDetail->enabled = 0;
                $eventByChequeDetail->save();
            }

            $eventCodDetail = $siteeventticket_event_gateway_table->fetchRow(array('event_id = ?' => $event_id, "title = 'Pay at the Event'"));
            if (!empty($eventCodDetail))
                $eventCodId = $eventCodDetail->eventgateway_id;
            else
                $eventCodId = '';

            if ($isCodChecked == "true") {
                if (!empty($eventCodId)) {
                    $eventCodDetail->enabled = 1;
                    $eventCodDetail->save();
                } else {
                    $siteeventticket_event_gateway_table->insert(array(
                        'event_id' => $event_id,
                        'title' => 'Pay at the Event',
                        'enabled' => 1
                    ));
                    $eventCodId = $siteeventticket_event_gateway_table->getAdapter()->lastInsertId();
                }
            } else if (!empty($eventCodId)) {
                $eventByChequeDetail->enabled = 0;
                $eventByChequeDetail->save();
            }
        }

        // INSERT ALL ENABLED GATEWAY ENTRY IN EVENT TABLE
        $eventGateway = array();
        if ($isPaypalChecked == "true" && !empty($paypalEnabled)) {
            $eventGateway['paypal'] = $eventPaypalId;
        }
        
        foreach($_POST['additionalGatewaysCheckedArray'] as $key => $additionalGatewaysCheckedArray) {
                
            if($additionalGatewaysCheckedArray && !empty($gatewayDatasValidation)) {

                $gatewayKey = ltrim($key, 'is');
                $gatewayKeyFinal = substr($gatewayKey, 0, -7);
                $gatewayKeyFinal = strtolower($gatewayKeyFinal);

                if($gatewayDatasValidation[$gatewayKeyFinal]['gatewayEnabled']) {
                    $eventGateway[$gatewayKeyFinal] = $gatewayDatasValidation[$gatewayKeyFinal]['eventGatewayId'];
                }      
            }
        }
        
        if ($isByChequeChecked == "true") {
            $eventGateway['cheque'] = $eventByChequeId;
        }
        if ($isCodChecked == "true") {
            $eventGateway['cod'] = $eventCodId;
        }
        $siteeventOtherInfo = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getOtherinfo($siteevent->event_id);
        $siteeventOtherInfo->event_gateway = Zend_Json_Encoder::encode($eventGateway);
        $siteeventOtherInfo->save();

        $this->view->success_message = $this->view->translate('Changes saved.');
    }

    //PAYMENT REQUEST
    public function paymentToMeAction() {

        $this->view->event_id = $event_id = $this->_getParam('event_id', null);

        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, 'edit')->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }

        $this->view->minimum_requested_amount = $minimum_requested_amount = @round(Engine_Api::_()->siteeventticket()->getTransferThreshold($event_id), 2);
        $total_event_amount = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->getTotalAmount($event_id);
        if (empty($total_event_amount['sub_total']) && empty($total_event_amount['order_count'])) {
            $total_amount = 0;
        } else {
            $total_amount = $total_event_amount['sub_total'] + $total_event_amount['tax_amount'] - $total_event_amount['commission_value'];
        }
        $this->view->total_amount = @round($total_amount, 2);
        $this->view->order_count = $total_event_amount['order_count'];
        $this->view->threshold_amount = Engine_Api::_()->siteeventticket()->getTransferThreshold($event_id);

        $remaining_amount_table = Engine_Api::_()->getDbtable('remainingamounts', 'siteeventticket');
        $remaining_amount_obj = $remaining_amount_table->fetchRow(array('event_id = ?' => $event_id));
        $paymentRequestTable = Engine_Api::_()->getDbtable('paymentrequests', 'siteeventticket');
        $requested_amount = $paymentRequestTable->getRequestedAmount($event_id);

        if (empty($remaining_amount_obj->event_id)) {
            $remaining_amount_table->insert(array('event_id' => $event_id, 'remaining_amount' => 0));
            $remaining_amount = 0;
        } else {
            $remaining_amount = $remaining_amount_obj->remaining_amount;
        }

        $this->view->remaining_amount = @round($remaining_amount, 2);
        $this->view->requesting_amount = empty($requested_amount) ? 0 : @round($requested_amount, 2);

        $this->view->call_same_action = $this->_getParam('call_same_action', 0);

        $params = array();
        $params['event_id'] = $event_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;

        if (isset($_POST['search'])) {
            $this->_helper->layout->disableLayout(true);
            $params['search'] = 1;
            $params['request_date'] = $_POST['request_date'];
            $params['response_date'] = $_POST['response_date'];
            $params['request_min_amount'] = $_POST['request_min_amount'];
            $params['request_max_amount'] = $_POST['request_max_amount'];
            $params['response_min_amount'] = $_POST['response_min_amount'];
            $params['response_max_amount'] = $_POST['response_max_amount'];
            $params['request_status'] = $_POST['request_status'];
            $this->view->only_list_content = true;
        }

        //MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('paymentrequests', 'siteeventticket')->getEventPaymentRequestPaginator($params);
        $this->view->total_item = $this->view->paginator->getTotalItemCount();
    }

    public function paymentRequestAction() {

        $this->view->event_id = $event_id = $this->_getParam('event_id', null);
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, 'edit')->isValid())
            return;

        $minimum_requested_amount = @round(Engine_Api::_()->siteeventticket()->getTransferThreshold($event_id), 2);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $remaining_amount = Engine_Api::_()->getDbtable('remainingamounts', 'siteeventticket')->fetchRow(array('event_id = ?' => $event_id))->remaining_amount;
        $total_event_amount = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->getTotalAmount($event_id);
        $total_amount = empty($total_event_amount['sub_total']) ? 0 : $total_event_amount['sub_total'] + $total_event_amount['tax_amount'] - $total_event_amount['commission_value'];
        $this->view->user_max_requested_amount = $user_requested_amount = @round(($remaining_amount + $total_amount), 2);
        $order_count = $this->_getParam('order_count');

        $gateway_id = Engine_Api::_()->getDbtable('gateways', 'siteeventticket')->getEventGateway($event_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($gateway_id)) {
            if ($viewer_id == $siteevent->owner_id)
                $this->view->req_page_owner = true;
            $this->view->gateway_disable = 1;
        } else if ($minimum_requested_amount > $user_requested_amount) {
            $this->view->not_allowed_for_payment_request = 1;
            $this->view->minimun_requested_amount = $minimum_requested_amount;
            $this->view->gross_amount = $user_requested_amount;
        } else {
            $this->view->form = $form = new Siteeventticket_Form_Paymentrequest(array('requestedAmount' => $user_requested_amount, 'totalAmount' => $total_amount, 'remainingAmount' => $remaining_amount, 'amounttobeRequested' => $user_requested_amount));

            $localeObject = Zend_Registry::get('Locale');
            $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
            $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

            $form->total_amount->setLabel($this->view->translate('New Sales <br /> (%s)', $currencyName));
            $form->total_amount->getDecorator('Label')->setOption('escape', false);

            $form->remaining_amount->setLabel($this->view->translate('Remaining Amount <br /> (%s)', $currencyName));
            $form->remaining_amount->getDecorator('Label')->setOption('escape', false);

            $form->amount_to_be_requested->setLabel($this->view->translate('Balance Amount <br /> (%s)', $currencyName));
            $form->amount_to_be_requested->getDecorator('Label')->setOption('escape', false);

            $form->amount->setLabel($this->view->translate('Amount to be Requested <br /> (%s)', $currencyName));
            $form->amount->getDecorator('Label')->setOption('escape', false);

            $form->removeElement('last_requested_amount');
            $this->view->user_requested_amount = $user_requested_amount;

            if (!$this->getRequest()->isPost()) {
                return;
            }
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }

            $values = array('total_amount' => @round($total_amount, 2), 'remaining_amount' => @round($remaining_amount, 2), 'amount_to_be_requested' => @round($user_requested_amount, 2));
            $temp_values = $form->getValues();
            $values['amount'] = $temp_values['amount'];
            $values['message'] = $temp_values['message'];

            $form->populate($values);

            if ($values['amount'] < $minimum_requested_amount && $values['amount'] > 0) {
                $error = Zend_Registry::get('Zend_Translate')->_('You are requesting for a less amount (%s) than the minimun request payment amount (%s) set by site administrator. Please request for an amount equal or greater than (%s)');
                $error = sprintf($error, Engine_Api::_()->siteeventticket()->getPriceWithCurrency($values['amount']), Engine_Api::_()->siteeventticket()->getPriceWithCurrency($minimum_requested_amount), Engine_Api::_()->siteeventticket()->getPriceWithCurrency($minimum_requested_amount));
                $form->addError($error);
                return;
            }

            if ($values['amount'] > $user_requested_amount) {
                $error = Zend_Registry::get('Zend_Translate')->_('You are requesting a amount for which you are not able. Please request for a amount equal or less than %s');
                $error = sprintf($error, Engine_Api::_()->siteeventticket()->getPriceWithCurrency($user_requested_amount));
                $form->addError($error);
                return;
            }

            $setOrderTicketInfo = Engine_Api::_()->siteeventticket()->setOrderTicketInfo();
            $remaining_amount = @round($user_requested_amount - $values['amount'], 2);
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
              if(!empty($setOrderTicketInfo)) {
                  $payment_req_table = Engine_Api::_()->getDbtable('paymentrequests', 'siteeventticket');
                  $payment_req_table->insert(array(
                      'event_id' => $event_id,
                      'order_count' => $order_count,
                      'request_amount' => @round($values['amount'], 2),
                      'request_date' => date('Y-m-d H:i:s'),
                      'request_message' => $values['message'],
                      'remaining_amount' => $remaining_amount,
                      'request_status' => '0',
                  ));

                  $request_id = $payment_req_table->getAdapter()->lastInsertId();
                  $payment_req_obj = Engine_Api::_()->getItem('siteeventticket_paymentrequest', $request_id);

                  //UPDATE PAYMENT REQUEST ID IN ORDER TABLE
                  Engine_Api::_()->getDbtable('orders', 'siteeventticket')->update(
                          array('payment_request_id' => $request_id), array('event_id =? AND payment_request_id = 0 AND direct_payment = 0' => $event_id));

                  //UPDATE REMAINING AMOUNT
                  Engine_Api::_()->getDbtable('remainingamounts', 'siteeventticket')->update(
                          array('remaining_amount' => $remaining_amount), array('event_id =? ' => $event_id));

                  $newVar = _ENGINE_SSL ? 'https://' : 'http://';
                  $event_name = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $siteevent->getHref() . '">' . $siteevent->getTitle() . '</a>';
                  $event_owner = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $siteevent->getOwner()->getHref() . '">' . $siteevent->getOwner()->getTitle() . '</a>';

                  //Removed Case: SEND MAIL TO EVENT OWNER ABOUT PAYMENT REQUEST
                  // SEND MAIL TO SITE ADMIN FOR THIS PAYMENT REQUEST
                  $admin_email_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact', null);

                  if (!empty($admin_email_id)) {
                      Engine_Api::_()->getApi('mail', 'core')->sendSystem($admin_email_id, 'siteeventticket_payment_request_to_admin', array(
                          'event_name' => $event_name,
                          'request_amount' => Engine_Api::_()->siteeventticket()->getPriceWithCurrency($values['amount']),
                          'event_owner' => $event_owner,
                          'event_title' => $siteevent->getTitle(),
                          'event_owner_title' => $siteevent->getOwner()->getTitle(),
                      ));
                  }
              }
              $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your payment request has been successfully sent.'))
            ));
        }
    }

    public function editPaymentRequestAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $request_id = $this->_getParam('request_id', null);
        $payment_req_obj = Engine_Api::_()->getItem('siteeventticket_paymentrequest', $request_id);
        if (empty($request_id) || empty($payment_req_obj))
            return $this->_forward('notfound', 'error', 'core');

        $this->view->event_id = $event_id = $payment_req_obj->event_id;

        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, 'edit')->isValid())
            return;

        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $gateway_id = Engine_Api::_()->getDbtable('gateways', 'siteeventticket')->getEventGateway($event_id);
        $payment_req_table_obj = Engine_Api::_()->getDbtable('paymentrequests', 'siteeventticket');

        if ($payment_req_obj->request_status == 1) {
            $this->view->siteeventticket_payment_request_deleted = true;
            return;
        } else if ($payment_req_obj->request_status == 2) {
            $this->view->siteeventticket_payment_request_completed = true;
            return;
        }

        if (!empty($payment_req_obj->payment_flag)) {
            $time_diff = abs(time() - strtotime($payment_req_obj->response_date));
            if ($time_diff > 3600) {
                $payment_req_obj->payment_flag = 0;
                $payment_req_obj->save();
            } else {
                $this->view->siteeventticket_admin_responding_request = true;
                return;
            }
        }

        if (empty($gateway_id)) {
            if ($viewer_id == $siteevent->owner_id) {
                $this->view->req_page_owner = true;
            }
            $this->view->gateway_disable = 1;
            return;
        }

        $remaining_amount_table_obj = Engine_Api::_()->getDbtable('remainingamounts', 'siteeventticket');
        $remaining_amount = $remaining_amount_table_obj->fetchRow(array('event_id = ?' => $event_id))->remaining_amount;
        $total_event_amount = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->getTotalAmount($event_id);
        $total_amount = empty($total_event_amount['sub_total']) ? 0 : $total_event_amount['sub_total'] + $total_event_amount['tax_amount'] - $total_event_amount['commission_value'];
        $amount_to_be_requested = $remaining_amount + $total_amount + $payment_req_obj->request_amount;

        $this->view->form = $form = new Siteeventticket_Form_Paymentrequest(array('requestedAmount' => @round($payment_req_obj->request_amount, 2), 'totalAmount' => $total_amount, 'remainingAmount' => $remaining_amount, 'amounttobeRequested' => $amount_to_be_requested));
        $form->last_requested_amount->setValue(@round($payment_req_obj->request_amount, 2));
        $form->message->setValue($payment_req_obj->request_message);

        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

        $form->total_amount->setLabel($this->view->translate('New Sales <br /> (%s)', $currencyName));
        $form->total_amount->getDecorator('Label')->setOption('escape', false);

        $form->remaining_amount->setLabel($this->view->translate('Remaining Amount <br /> (%s)', $currencyName));
        $form->remaining_amount->getDecorator('Label')->setOption('escape', false);

        $form->last_requested_amount->setLabel($this->view->translate('Last Requested Amount <br /> (%s)', $currencyName));
        $form->last_requested_amount->getDecorator('Label')->setOption('escape', false);

        $form->amount_to_be_requested->setLabel($this->view->translate('Balance Amount <br /> (%s)', $currencyName));
        $form->amount_to_be_requested->getDecorator('Label')->setOption('escape', false);

        $form->amount->setLabel($this->view->translate('New Amount to be Requested <br /> (%s)', $currencyName));
        $form->amount->getDecorator('Label')->setOption('escape', false);

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = array('total_amount' => @round($total_amount, 2), 'remaining_amount' => @round($remaining_amount, 2), 'last_requested_amount' => @round($payment_req_obj->request_amount, 2), 'amount_to_be_requested' => @round($amount_to_be_requested, 2));
        $temp_values = $form->getValues();
        $values['amount'] = $temp_values['amount'];
        $values['message'] = $temp_values['message'];

        $form->populate($values);
        $minimum_requested_amount = @round(Engine_Api::_()->siteeventticket()->getTransferThreshold($event_id), 2);

        if (@round($values['amount'], 2) != @round($payment_req_obj->request_amount, 2)) {
            $user_max_requested_amount = @round($payment_req_obj->request_amount, 2) + @round($remaining_amount, 2) + @round($total_amount, 2);

            if ($values['amount'] < $minimum_requested_amount) {
                $error = Zend_Registry::get('Zend_Translate')->_('You are requesting for a less amount (%s) than the minimun request payment amount (%s) set by site administrator. Please request for an amount equal or greater than (%s)');
                $error = sprintf($error, Engine_Api::_()->siteeventticket()->getPriceWithCurrency($values['amount']), Engine_Api::_()->siteeventticket()->getPriceWithCurrency($minimum_requested_amount), Engine_Api::_()->siteeventticket()->getPriceWithCurrency($minimum_requested_amount));
                $form->addError($error);
                return;
            }
            if ($values['amount'] > $user_max_requested_amount) {
                $form->addError('You are requesting a amount for which you are not able. Please request for a amount equal or less than in your shopping account.');
                return;
            }

            $remaining_amount = @round(($user_max_requested_amount - $values['amount']), 2);

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $payment_req_obj->request_amount = @round($values['amount'], 2);
                $payment_req_obj->request_message = $values['message'];
                $payment_req_obj->remaining_amount = $remaining_amount;
                $payment_req_obj->save();

                //UPDATE REMAINING AMOUNT
                $remaining_amount_table_obj->update(array('remaining_amount' => $remaining_amount), array('event_id =? ' => $event_id));

                // UPDATE ORDERS FOR WHICH PAYMENT REQUEST HAS BEEN SENT
                Engine_Api::_()->getDbtable('orders', 'siteeventticket')->update(
                        array('payment_request_id' => 1), array("event_id =? AND payment_request_id = 0 AND direct_payment = 0 AND payment_status LIKE 'active' AND order_status = 2" => $event_id)
                );

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } else {
            $payment_req_obj->request_message = $values['message'];
            $payment_req_obj->save();
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Payment request edited successfully.'))
        ));
    }

    public function deletePaymentRequestAction() {

        $request_id = $this->_getParam('request_id', null);
        $payment_req_obj = Engine_Api::_()->getItem('siteeventticket_paymentrequest', $request_id);
        if (empty($request_id) || empty($payment_req_obj)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $event_id = $payment_req_obj->event_id;

        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, 'edit')->isValid())
            return;

        if ($payment_req_obj->request_status == 1) {
            $this->view->siteeventticket_payment_request_deleted = true;
            return;
        } else if ($payment_req_obj->request_status == 2) {
            $this->view->siteeventticket_payment_request_completed = true;
            return;
        }

        if (!empty($payment_req_obj->payment_flag)) {
            $time_diff = abs(time() - strtotime($payment_req_obj->response_date));
            if ($time_diff > 3600) {
                $payment_req_obj->payment_flag = 0;
                $payment_req_obj->save();
            } else {
                $this->view->siteeventticket_admin_responding_request = true;
                return;
            }
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $remaining_amount_table_obj = Engine_Api::_()->getDbtable('remainingamounts', 'siteeventticket');
        $remaining_amount = $remaining_amount_table_obj->fetchRow(array('event_id = ?' => $event_id))->remaining_amount;
        $remaining_amount += $payment_req_obj->request_amount;

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $payment_req_obj->request_status = 1;
            $payment_req_obj->save();

            //UPDATE REMAINING AMOUNT
            $remaining_amount_table_obj->update(array('remaining_amount' => $remaining_amount), array('event_id =? ' => $event_id));
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Payment request deleted successfully.'))
        ));
    }

    public function viewPaymentRequestAction() {

        $this->view->event_id = $event_id = $this->_getParam('event_id', null);
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, 'view')->isValid())
            return;

        $this->view->request_id = $request_id = $this->_getParam('request_id');
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $this->view->userObj = Engine_Api::_()->getItem('user', $siteevent->owner_id);
        $this->view->payment_req_obj = Engine_Api::_()->getItem('siteeventticket_paymentrequest', $request_id);
    }

    public function yourBillAction() {

        $this->view->event_id = $event_id = $this->_getParam('event_id', null);
        $this->view->call_same_action = $this->_getParam('call_same_action', 0);
Engine_Api::_()->siteeventticket()->isAllowThresholdNotifications(array('event_id' => $event_id));
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, 'edit')->isValid())
            return;

        $remainingBillAmount = Engine_Api::_()->siteeventticket()->getRemainingBillAmount($event_id);
        $this->view->paidBillAmount = Engine_Api::_()->getDbtable('eventbills', 'siteeventticket')->totalPaidBillAmount($event_id);
        $this->view->newBillAmount = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->getEventBillAmount($event_id);
        $this->view->remainingBillAmount = round($remainingBillAmount, 2);
        $this->view->totalBillAmount = round(($this->view->remainingBillAmount + $this->view->newBillAmount), 2);

        $params = array();
        $params['event_id'] = $event_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;

        if (isset($_POST['search']) || isset($_POST['showDashboardEventContent'])) {
            if (isset($_POST['search'])) {
                $params['search'] = 1;
                $params['bill_date'] = $_POST['bill_date'];
                $params['bill_min_amount'] = $_POST['bill_min_amount'];
                $params['bill_max_amount'] = $_POST['bill_max_amount'];
                $params['status'] = $_POST['status'];
            }
            $this->_helper->layout->disableLayout();
            $this->view->only_list_content = true;
        }

        //MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->getEventBillPaginator($params);
    }

    public function billPaymentAction() {

        $this->view->event_id = $event_id = $this->_getParam('event_id', null);

        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, 'edit')->isValid())
            return;
        
        $where = "plugin = 'Payment_Plugin_Gateway_PayPal'";
        if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $additionalGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.paymentmethod', 'paypal');
            $additionalGateway = ucfirst($additionalGateway);
            $where = "plugin = 'Payment_Plugin_Gateway_PayPal' OR plugin = 'Sitegateway_Plugin_Gateway_$additionalGateway'";
        }

        $gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
        $isPaypalEnabled = $gateway_table->select()
                ->from($gateway_table->info('name'), array('gateway_id'))
                ->where($where)
                ->where('enabled = 1')
                ->query()
                ->fetchColumn();

        if (empty($isPaypalEnabled)) {
            $this->view->noAdminGateway = true;
            return;
        }

        $siteeventpaidListPackage = Zend_Registry::isRegistered('siteeventpaidListPackage') ? Zend_Registry::get('siteeventpaidListPackage') : null;
        if(empty($siteeventpaidListPackage))
          return;
        
        $remainingBillTable = Engine_Api::_()->getDbtable('remainingbills', 'siteeventticket');
        $orderTable = Engine_Api::_()->getDbtable('orders', 'siteeventticket');

        $remainingBillAmount = $remainingBillTable->fetchRow(array('event_id = ?' => $event_id))->remaining_bill;
        $newBillAmount = $orderTable->getEventBillAmount($event_id);

        $totalBillAmount = round(($remainingBillAmount + $newBillAmount), 2);

        $this->view->form = $form = new Siteeventticket_Form_BillPayment(array('totalBillAmount' => $totalBillAmount));

        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

        $form->total_bill_amount->setLabel($this->view->translate('Total Bill Amount <br /> (%s)', $currencyName));
        $form->total_bill_amount->getDecorator('Label')->setOption('escape', false);
        $form->total_bill_amount->setAttribs(array('disabled' => 'disabled'));
        $form->total_bill_amount->setValue($totalBillAmount);

        $form->bill_amount_pay->setLabel($this->view->translate('Amount to Pay <br /> (%s)', $currencyName));
        $form->bill_amount_pay->getDecorator('Label')->setOption('escape', false);
        $form->bill_amount_pay->setValue($totalBillAmount);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $form->total_bill_amount->setAttribs(array('disabled' => 'disabled'));
        $form->total_bill_amount->setValue($totalBillAmount);

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if (round($values['bill_amount_pay'], 2) > $totalBillAmount) {
            $error = Zend_Registry::get('Zend_Translate')->_("You can't pay commission more than your total bill amount. Please enter an amount equal to or less than your total bill amount.");
            $form->addError($error);
            return;
        }

        $newRemainingBillAmount = round($totalBillAmount - $values['bill_amount_pay'], 2);
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $eventBillTable = Engine_Api::_()->getDbtable('eventbills', 'siteeventticket');
            $eventBillTable->insert(array(
                'event_id' => $event_id,
                'amount' => round($values['bill_amount_pay'], 2),
                'remaining_amount' => round($newRemainingBillAmount, 2),
                'message' => $values['message'],
                'creation_date' => new Zend_Db_Expr('NOW()'),
                'status' => 'initial',
            ));

            $eventBillId = $eventBillTable->getAdapter()->lastInsertId();

            // MANAGE REMAINING BILL AMOUNT
            $isEventRemainingBillExist = $remainingBillTable->isEventRemainingBillExist($event_id);
            if (empty($isEventRemainingBillExist)) {
                $remainingBillTable->insert(array(
                    'event_id' => $event_id,
                    'remaining_bill' => $newRemainingBillAmount,
                ));
            } else {
                $remainingBillTable->update(array('remaining_bill' => $newRemainingBillAmount), array('event_id =? ' => $event_id));
            }

            //UPDATE EVENT BILL ID IN ORDER TABLE
            $orderTable->update(array('eventbill_id' => $eventBillId), array('event_id =? AND eventbill_id = 0 AND direct_payment = 1' => $event_id));

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefreshTime' => '10',
            'parentRedirect' => $this->view->url(array('module' => 'siteeventticket', 'controller' => 'order', 'action' => 'bill-process', 'event_id' => $event_id, 'bill_id' => $eventBillId), '', true),
            'format' => 'smoothbox',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('You will be redirected to make payment for your bill.'))
        ));
    }

    public function billProcessAction() {

        $event_id = $this->_getParam('event_id', null);
        $bill_id = $this->_getParam('bill_id', null);

        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, 'edit')->isValid())
            return;

        $siteeventpaidListPackage = Zend_Registry::isRegistered('siteeventpaidListPackage') ? Zend_Registry::get('siteeventpaidListPackage') : null;
        if(empty($siteeventpaidListPackage))
          return;
        
        $setOrderTicketInfo = Engine_Api::_()->siteeventticket()->setOrderTicketInfo();
        if(!empty($setOrderTicketInfo)) {
          $this->_session = new Zend_Session_Namespace('Event_Bill_Payment_Siteeventticket');
          if (!empty($this->_session)) {
              $this->_session->unsetAll();
          }
          $this->_session->event_id = $event_id;
          $this->_session->bill_id = $bill_id;
        }

        return $this->_forwardCustom('process', 'event-bill-payment', 'siteeventticket', array());
    }

    public function billDetailsAction() {
        $bill_id = $this->_getParam('bill_id', null);
        $event_id = $this->_getParam('event_id', null);
        $this->view->eventBillObj = Engine_Api::_()->getItem('siteeventticket_eventbill', $bill_id);
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $this->view->userObj = Engine_Api::_()->getItem('user', $siteevent->owner_id);
        $this->view->transaction = Engine_Api::_()->getDbtable('transactions', 'siteeventticket')->fetchRow(array('order_id = ?' => $bill_id, 'sender_type =?' => 2, 'gateway_id = ?' => $this->view->eventBillObj->gateway_id));
    }

    public function monthlyBillDetailAction() {

        $this->view->event_id = $event_id = $this->_getParam('event_id', null);
        $this->view->search = $this->_getParam('search', 0);

        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, 'edit')->isValid())
            return;

        $params = array();
        $this->view->month = $params['month'] = $this->_getParam('month');
        $this->view->year = $params['year'] = $this->_getParam('year');

        $this->view->monthName = date("F", mktime(0, 0, 0, $params['month']));

        $params['event_id'] = $event_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;

        //MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->getEventMonthlyBillPaginator($params);
        $this->view->total_item = $this->view->paginator->getTotalItemCount();
    }

    public function buyerDetailsAction() {

        // Render
        $this->_helper->content
                //->setNoRender()
                ->setEnabled()
        ;

        // GET VIEWER 
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->event_id = $event_id = $this->_getParam('event_id');
        $this->view->siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (empty($_POST)) {
            return;
        }
        $siteeventBuyEventSteps = Zend_Registry::isRegistered('siteeventBuyEventSteps') ? Zend_Registry::get('siteeventBuyEventSteps') : null;
        if(empty($siteeventBuyEventSteps)) 
          return;
        
        $session = new Zend_Session_Namespace('siteeventticket_cart_formvalues');
        $session->formValues = $this->view->formValues = $_POST;

        $this->view->tax_rate = false;
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.tax.enabled', 0) && Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($event_id, 'is_tax_allow') && Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($event_id, 'tax_rate') > 0) {
            $this->view->tax_rate = true;
        }
    }

    //ACTION TO TAKE PRINT OF INVOICE
    public function printTicketAction() {

        //PAYMENT FLOW CHECK
        $this->view->paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);

        $order_id = Engine_Api::_()->siteeventticket()->getEncodeToDecode($this->_getParam('order_id', null));
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if (empty($order_id)) {
            $this->view->siteeventticket_view_no_permission = true;
            return;
        }
        
        $siteeventBuyEventSteps = Zend_Registry::isRegistered('siteeventBuyEventSteps') ? Zend_Registry::get('siteeventBuyEventSteps') : null;
        if(empty($siteeventBuyEventSteps)) 
          return;

        //USER IS BUYER OR NOT
        $this->view->orderObj = $orderObj = Engine_Api::_()->getItem('siteeventticket_order', $order_id);
        $this->view->occurrence_id = $orderObj->occurrence_id;
        $this->view->occurrenceObj = $occurrenceObj = Engine_Api::_()->getItem('siteevent_occurrence', $orderObj->occurrence_id);
        
        if ($occurrenceObj) {
            $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $occurrenceObj->event_id);
            $event_owner_id = $siteevent->owner_id;
        }        
        
        if (empty($occurrenceObj) && $viewer_id != $orderObj->user_id && $viewer->level_id != 1 && $viewer_id != $event_owner_id) {
            //IS USER IS EVENT ADMIN OR NOT
            $this->view->siteeventticket_print_invoice_no_permission = true;
            return;
        }

        $this->view->generatePdf = $this->_getParam('generatePdf', null);

        $this->_helper->layout->setLayout('default-simple');

        // FETCH SITE LOGO OR TITLE
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_pages')->where('name = ?', 'header')->limit(1);

        $info = $select->query()->fetch();
        if (!empty($info)) {
            $page_id = $info['page_id'];

            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_content', array("params"))
                    ->where('page_id = ?', $page_id)
                    ->where("name LIKE '%core.menu-logo%'")
                    ->limit(1);
            $info = $select->query()->fetch();
            $params = json_decode($info['params']);

            if (!empty($params) && !empty($params->logo)) {
                $this->view->logo = $params->logo;
            }
        }

        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');

        //TAX ID NO.
        if ($orderObj->tax_amount) {
            $this->view->tax_id_no = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($orderObj->event_id, 'tax_id_no');
        }

        $this->view->orderTickets = $orderTickets = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket')->getOrderTicketsDetail(array('order_id' => $order_id));
        $this->view->isOrderHavingDiscount = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket')->isOrderHavingDiscount(array('order_id' => $order_id));

        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteevent/View/Helper', 'Siteevent_View_Helper');


        //NEW ADDED
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $local_language = $view->locale()->getLocale()->__toString();
        //$local_language = explode('_', $local_language);

        $content = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.format.bodyhtml.default');
        $bodyHTML = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.format.bodyhtml.' . $local_language, $content);

        $terms_of_use = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($orderObj->event_id, 'terms_of_use');
        $terms_of_use_heading = '';
        if (empty($terms_of_use)) {
            $terms_of_use = $siteevent->body;
        }

        if (empty($terms_of_use)) {
            $terms_of_use = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($orderObj->event_id, 'overview');
        }

        $terms_of_use = Engine_Api::_()->seaocore()->seaocoreTruncateText($terms_of_use, 700);
        $ads_image = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.adsimage');


        if ($this->view->generatePdf) { //GENERATE PDF
            $ads_image = !empty($ads_image) ? "<img alt='' src='$ads_image' style='width:285px;height:400px;'>" : '';
            $bodyHTML .= '<div style="width: 650px; padding: 15px 0; font-family: arial; margin:0 auto;"><table style="width:100%;"><tr><td style="text-align: left; width: 325px; float: left; box-sizing: border-box; height: 380px; vertical-align: top; "><div style="overflow:hidden;box-sizing: border-box;padding: 10px;height: 380px;width: 325px;border: 2px solid rgb(204, 204, 204);border-radius: 10px;color: #535353; font-size: 14px; line-height: 21px;">[terms_of_use]</div></td><td style="text-align: left; width: 300px; overflow: hidden; box-sizing: border-box; float: right; padding-left: 15px;height: 380px; vertical-align: top;">[ads_image]</td></tr><tr><td style="text-align: right; font-size: 24px; padding: 20px 0px;" colspan="2"><div style="float: right; font-size: 20px !important;border-radius: 10px;"><b>[site_logo]</b></div></td></tr></table></div>';
        } else { //PRINT TICKET
            $ads_image = !empty($ads_image) ? "<img alt='' src='$ads_image' style='width:100%;height:100%;'>" : '';
            $bodyHTML .= '<div style="width: 650px; padding: 15px 0; font-family: arial; margin:0 auto;"><table style="width:100%;"><tr><td style="text-align: left; width: 325px; float: left; box-sizing: border-box; height: 380px; vertical-align: top; "><div style="overflow:hidden;box-sizing: border-box;padding: 10px;height: 380px;width: 325px;border: 2px solid rgb(204, 204, 204);border-radius: 10px;color: #535353; font-size: 14px; line-height: 21px;">[terms_of_use]</div></td><td style="text-align: left; width: 300px; overflow: hidden; box-sizing: border-box; float: right; padding-left: 15px;height: 380px; vertical-align: top;">[ads_image]</td></tr><tr><td style="text-align: right; font-size: 24px; padding: 20px 0px;" colspan="2"><div style="float: right; font-size: 20px !important;border-radius: 10px;"><b>[site_logo]</b></div></td></tr></table></div>';
        }


        $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');

        $occurrence_date_time = $view->locale()->toDate($orderObj->occurrence_starttime, array('format' => 'EEEE')) . ', ' . $view->locale()->toDate($orderObj->occurrence_starttime, array('size' => $datetimeFormat));
        $occurrence_date_time = $occurrence_date_time . " " . $view->locale()->toEventTime($orderObj->occurrence_starttime, array('size' => $datetimeFormat));
        $ticket_date = $view->locale()->toDate($orderObj->creation_date, array('size' => $datetimeFormat));
        $ticket_date_time = $ticket_date . " " . $view->locale()->toEventTime($orderObj->creation_date, array('size' => $datetimeFormat));
        $placehoders = array("[event_name]", "[event_date_time]", "[event_location]", "[event_venue]", "[ticket_date_time]", "[terms_of_use]", "[ads_image]");

        $eventTitle = 'Deleted Event';
        if(!empty($siteevent)) {
            $eventTitle = $siteevent->getTitle();
        }
        
        $commonValues = array($eventTitle, $occurrence_date_time, $siteevent->location, $siteevent->venue_name, $ticket_date_time, $terms_of_use, $ads_image);

        $this->view->bodyHTML = str_replace($placehoders, $commonValues, $bodyHTML);

        $this->view->buyerRows = Engine_Api::_()->getDbtable('buyerdetails', 'siteeventticket')->getBuyerDetails(array('order_id' => $order_id));
    }

      
  public function sendEmailAction() {

    $this->_helper->layout->setLayout('default-simple');
    $this->view->order_id = $order_id = $order_id = Engine_Api::_()->siteeventticket()->getEncodeToDecode($this->_getParam('order_id', null));    
    $siteeventBuyEventSteps = Zend_Registry::isRegistered('siteeventBuyEventSteps') ? Zend_Registry::get('siteeventBuyEventSteps') : null;
    if(empty($siteeventBuyEventSteps)) 
      return;
    
    $this->view->adminCall = $this->_getParam('adminCall', 0);

    if ($this->getRequest()->isPost() && !empty($order_id)) {
        
      Engine_Api::_()->siteeventticket()->orderPlaceMailAndNotification(array('order_id' => $order_id,'activity_feed' => 0, 'seller_email' => 0, 'admin_email' => 0, 'buyer_email' => 1, 'notification_seller' => 0));  
        
      Engine_Api::_()->getItem('siteeventticket_order', $order_id);
      $this->_forward('success', 'utility', 'core', array(
       'smoothboxClose' => 10,
       'messages' => array('Email has been sent succesfully.')
      ));
    }
    $this->renderScript('order/send-email.tpl');
  }  
  
}
