<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TicketOrderInvoice.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_View_Helper_TicketOrderInvoice extends Zend_View_Helper_Abstract {

    public function ticketOrderInvoice($order) {

        $order_tickets = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket')->getOrderTicketsDetail(array('order_id' => $order->order_id));
        
        $coupon_details = array();
        $fixedDiscount = 1;
        if (!empty($order->coupon_detail)) {
            $coupon_details = unserialize($order->coupon_detail);
            if (is_array($coupon_details)) {
                foreach ($coupon_details as $coupon_detail) {
                    $coupon_details = $coupon_detail;
                    $fixedDiscount = !empty($coupon_detail['coupon_type']) ? 1 : 0;
                    if ($fixedDiscount) {
                        $order->grand_total -= $coupon_detail['coupon_amount'];
                    }
                    break;
                }
            }
        }     
        
        $isOrderHavingDiscount = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket')->isOrderHavingDiscount(array('order_id' => $order->order_id));        

        $siteeventticketApi = Engine_Api::_()->siteeventticket();

        //PAYMENT FLOW CHECK
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);

        $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
        
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $order->event_id);
        
        if (empty($paymentToSiteadmin)) {
            $this->view->eventTitle = $siteevent->title;
            $this->view->eventChequeDetail = Engine_Api::_()->getDbtable('eventGateways', 'siteeventticket')->getEventChequeDetail(array('event_id = ?' => $order->event_id, "title = 'ByCheque'", "enabled = 1"));
        } else {
            $this->view->admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('send.cheque.to', null);
        }

        $invoice = '<div style="overflow:hidden"><div style="width:600px;margin:20px auto;"><div style="font-family:arial;font-size:10pt;background-color:#EAEAEA;border:1px solid #CCCCCC;height:40px;line-height:40px;padding:2px 10px;"><div><div style="height:40px;width:450px;font-size: 13pt;display: inline-block;"><b> ';

        $getHost = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        
        $invoice .= $siteevent->getOwner()->displayname;
        $user = Engine_Api::_()->getItem('user', $order->user_id);        

        $order_link = $getHost. $this->view->url(array('controller' => 'order', 'action' => 'view', 'order_id' => $order->order_id, 'event_id' => $order->event_id), 'siteeventticket_order', true);
        $order_link = '<a target="_blank" href="' . $order_link . '">#' . $order->order_id . '</a>';
        $invoice .= '</b></div><div style="float:right;font-size: 13pt;"><strong> ' . $this->view->translate("INVOICE") . ' </strong></div></div></div><div style="float: left;font-family: arial; font-size: 13px;"><ul style="padding:0;margin:0;border: 1px solid #ccc;"><li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><b>' . $this->view->translate("Order %s", $order_link) . '</b></li><li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><div style="width: 170px;float:left;"> <b>' . $this->view->translate("Status") . '  </b> </div><div>: &nbsp;' . $this->view->getTicketOrderStatus($order->order_status) . '<br/> </div></li><li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><div style="width: 170px;float:left;"> <b>' . $this->view->translate("Ordered by") . '  </b> </div><div>: &nbsp;' . $user->getTitle().'&nbsp;('.$user->email.')'. '<br/> </div></li><li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><div style="width: 170px;float:left;"> <b> ' . $this->view->translate("Placed on") . ' </b> </div><div>: &nbsp;' . $this->view->locale()->toDateTime($order->creation_date) . '<br/> </div></li>';
        
        if($order->tax_amount) {
            $tax_id_no = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($order->event_id, 'tax_id_no');
            $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><div style="width: 170px;float:left;"> <b> ' . $this->view->translate("TIN No.") . ' </b> </div><div>: &nbsp;' . $tax_id_no . ' <br/> </div></li>';
        }
        
        $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><div style="width: 170px;float:left;"> <b> ' . $this->view->translate("Payment Method") . ' </b> </div><div>: &nbsp;' . $this->view->translate($siteeventticketApi->getGatwayName($order->gateway_id)) . ' <br/> </div></li>';        

        if ($order->gateway_id == 3) {
            $admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('send.cheque.to', null);
            $eventTitle = $siteevent->title;
            $eventChequeDetail = Engine_Api::_()->getDbtable('eventGateways', 'siteeventticket')->getEventChequeDetail(array('event_id = ?' => $order->event_id));
            $cheque_info = Engine_Api::_()->getDbtable('ordercheques', 'siteeventticket')->getChequeDetail($order->cheque_id);
            if (empty($order->direct_payment) && !empty($site_title) && !empty($admin_cheque_detail)) {
                $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><b>' . $this->view->translate("%s's Bank Account Details", $site_title) . '</b><div>' . $admin_cheque_detail . '</div></li>';
            } elseif (!empty($order->direct_payment) && !empty($eventTitle) && !empty($eventChequeDetail)) {
                $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><b>' . $this->view->translate("%s event's Bank Account Details", $eventTitle) . '</b><pre style="font-family: arial; margin: 0px;">' . $eventChequeDetail . '</pre></li>';
            }
            $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><b>' . $this->view->translate("Buyer Account Info") . '</b><div style="overflow:hidden;"><div style="clear:both;"><div style="width:170px; float:left">' . $this->view->translate("Cheque No") . '</div><div>: &nbsp;' . $cheque_info["cheque_no"] . '</div></div><div style="clear:both;"><div style="width:170px; float:left">' . $this->view->translate("Account Holder Name") . '</div><div>: &nbsp;' . $cheque_info["customer_signature"] . '</div></div><div style="clear:both;"><div style="width:170px; float:left">' . $this->view->translate("Account Number") . '</div><div>: &nbsp;' . $cheque_info["account_number"] . '</div></div><div style="clear:both;"><div style="width:170px; float:left">' . $this->view->translate("Bank Rounting Number") . '</div><div>: &nbsp;' . $cheque_info["bank_routing_number"] . '</div></div></div></li>';
        }

        $invoice .= '</ul><b style="margin:10px 0 5px;display:block;">' . $this->view->translate("Order Details") . '</b><div id="manage_order_tab" style="font-family:tahoma,arial,verdana,sans-serif;font-size:10pt;overflow-x:auto;width: 100%;"><div style="border:none;margin:0 0 10px;float:left;"><table cellspacing="0" style="border: 1px solid #CCCCCC;margin-top: 1px;width: 100%;font-size: 13px;"><tr style="background-color:#EAEAEA;"><th style="text-align:center;padding:7px 10px;width:252px;border-right:1px solid #ccc;"> ' . $this->view->translate("Ticket") . ' </th><th style="text-align:center;padding:7px 10px;width:128px;border-right:1px solid #ccc;">' . $this->view->translate("Quantity") . '</th><th style="text-align:center;padding:7px 10px;width:128px;border-right:1px solid #ccc;"> ' . $this->view->translate("Unit Price") . ' </th>'; 
        
        if($isOrderHavingDiscount && !$fixedDiscount) {
          $invoice .= '<th style="text-align:center;padding:7px 10px;width:128px;border-right:1px solid #ccc;"> ' . $this->view->translate("Discounted Price") . ' </th>';
        }
        
        $invoice .= '<th style="text-align:center;padding:7px 10px;width:128px;"> ' . $this->view->translate("Total") . ' </th></tr>';

        foreach ($order_tickets as $ticket) {

            $temp_lang_title = $ticket->title;

            $invoice .= '<tr><td title="' . $temp_lang_title . '" style="padding:7px 10px;border-right:1px solid #ccc;border-top:1px solid #ccc;">' . Engine_Api::_()->seaocore()->seaocoreTruncateText($temp_lang_title, 40);
            if (!empty($ticket->order_ticket_info)) {
                $order_ticket_info = unserialize($ticket->order_ticket_info);
            }
            if (!empty($order_ticket_info) && !empty($order_ticket_info['calendarDate']) && !empty($order_ticket_info['calendarDate']['starttime']) && !empty($order_ticket_info['calendarDate']['endtime'])) {
                $invoice .= '<br /><b>' . $this->view->translate("From: ") . '</b>' . $this->view->locale()->toDate($order_ticket_info['calendarDate']['starttime']) . '<br />';
                $invoice .= '<b>' . $this->view->translate("To: ") . '</b>' . $this->view->locale()->toDate($order_ticket_info['calendarDate']['endtime']);
            }

            $downPaymentPrice = '';

            if (!empty($order_ticket_info) && !empty($order_ticket_info['price_range_text'])) {
                $priceRangeText = $this->view->translate($order_ticket_info['price_range_text']);
            } else {
                $priceRangeText = '';
            }

            if (!empty($ticket->configuration)) {
                $configuration = Zend_Json::decode($ticket->configuration);
                $invoice .= '<br/>';
                foreach ($configuration as $config_name => $config_value)
                    $invoice .= "<b>" . $config_name . "</b>: $config_value<br/>";
            }

            $invoice .= '</td><td style="text-align:center;padding:7px 10px;border-right:1px solid #ccc;border-top:1px solid #ccc;"> ' . $ticket->quantity . ' </td><td style="text-align:right;padding:7px 10px;border-right:1px solid #ccc;border-top:1px solid #ccc;"> ' . $siteeventticketApi->getPriceWithCurrency($ticket->price) . ' ' . $priceRangeText . ' </td>' . $downPaymentPrice;
            
            if($isOrderHavingDiscount && !$fixedDiscount) {
              $invoice .= '<td style="text-align:right;padding:7px 10px;border-top:1px solid #ccc;border-right:1px solid #ccc;"><b>' . $siteeventticketApi->getPriceWithCurrency($ticket->discounted_price) . ' </b></td>';
            }     
            
            $invoice .= '<td style="text-align:right;padding:7px 10px;border-top:1px solid #ccc;"><b>' . $siteeventticketApi->getPriceWithCurrency($ticket->discounted_price * $ticket->quantity) . ' </b></td></tr>';               
        }

        $style = (!$order->sub_total || !$order->tax_amount) ? 'display:none' : 'display:block';
        $invoice .= '</table></div></div><div><b style="margin:10px 0 5px;display:block;">' . $this->view->translate("Order Summary") . '</b></div><div style="font-family:tahoma,arial,verdana,sans-serif;font-size:10pt;border-top:1px solid #CCCCCC;border-bottom:1px solid #CCCCCC;padding:10px;margin-bottom:10px;float:right;width: 100%;box-sizing: border-box;"><div style="margin-bottom:5px;overflow:hidden;"><div style="clear:both;"><div style="float: left; width: 80%; text-align: right;"> ' . $this->view->translate("Subtotal") . ' </div><div style="float:right;">' . $siteeventticketApi->getPriceWithCurrency($order->sub_total) . '<br/></div></div>';
      
        $orderCouponAmount = 0;
        if (!empty($coupon_details)) {
            $orderCouponAmount = $coupon_details['coupon_amount'];
        }        
        
        if(!empty($orderCouponAmount) && $fixedDiscount) {
            $invoice .= '<div style="clear:both;' . $style . '"><div style="float: left; width: 80%; text-align: right;"> ' . $this->view->translate("Discount") . ' </div><div style="float:right;">' . $siteeventticketApi->getPriceWithCurrency($orderCouponAmount) . '<br/> </div></div>';
        }        
        
        if($order->tax_amount > 0 && $order->sub_total > 0) {
            $invoice .= '<div style="clear:both;' . $style . '"><div style="float: left; width: 80%; text-align: right;"> ' . $this->view->translate("Taxes") . ' </div><div style="float:right;">' . $siteeventticketApi->getPriceWithCurrency($order->tax_amount) . '<br/> </div></div>';
        }
        
        $invoice .= '<div style="clear:both;"><div style="float: left; width: 80%; text-align: right;"><b> ' . $this->view->translate("Grand Total") . ' </b></div><div style="float:right;"><b>' . $siteeventticketApi->getPriceWithCurrency($order->grand_total) . '</b></div></div></div></div>';

        $invoice .= '</div></div></div>';

        //WORK FOR SHOWING THE PROFILE FIELDS OF EVENT
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteevent/View/Helper', 'Siteevent_View_Helper');

        return $invoice;
    }

}
