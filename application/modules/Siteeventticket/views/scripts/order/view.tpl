<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css');

$showPrintLinks = $this->orderObj->showPrintLink();
?>
<?php if (!empty($this->siteeventticket_view_no_permission)) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("Order not available for view or you are not permitted to view the order.") ?>
    </span>
  </div>
  <?php
  return;
endif;
?>

<?php if (!empty($this->callingStatus)): ?>
  <div id='manage_order_menue'>
    <div class="clr">
      <a class="buttonlink icon_previous mbot5" href="<?php echo $this->url(array('action' => 'manage', 'event_id' => $this->orderObj->event_id), 'siteeventticket_order', true) ?>">
        <?php echo $this->translate("Back") ?>
      </a>  
    </div>

    <?php //CHECKS MOVED IN FUNCTION 
    if ($showPrintLinks) : ?>
      <div class="tabs">
        <ul class="navigation sr_siteeventticket_navigation_common">
          <li>
            <?php $tempInvoice = $this->url(array('action' => 'print-invoice', 'order_id' => Engine_Api::_()->siteeventticket()->getDecodeToEncode($this->orderObj->order_id)), 'siteeventticket_order', true);?>
            <a href="<?php echo $tempInvoice; ?>" target="_blank"><?php echo $this->translate('Print Invoice') ?></a>
          </li>&nbsp;&nbsp;&nbsp;
          <li>
              <?php $tempPrint = $this->url(array('action' => 'print-ticket', 'order_id' => Engine_Api::_()->siteeventticket()->getDecodeToEncode($this->orderObj->order_id)), 'siteeventticket_order', true); ?>          
            <a href="<?php echo $tempPrint; ?>" target="_blank"><?php echo $this->translate("Print Tickets"); ?></a>
          </li>
        </ul>
      </div>
    <?php endif; ?>
    <br/>
  </div>
<?php else: ?>
  <div class="invoice_wrap">
    <div>
      <div class="clr">
        <a href="<?php echo $this->url(array('action' => 'my-tickets', 'order_id' => $this->order_id, 'event_id' => $this->orderObj->event_id), 'siteeventticket_order', true) ?>" id="siteeventticket_menu_1" class="buttonlink icon_previous mbot5"><?php echo $this->translate('Back to My Tickets') ?></a>        
      </div>
      <div class="tabs">
        <ul class="navigation sr_siteeventticket_navigation_common mbot15">
            <?php if ($this->orderObj->gateway_id == 3 && !empty($this->admin_cheque_detail) && empty($this->orderObj->direct_payment)) : ?>&nbsp;
              <?php $chequeDetailUrl = $this->url(array('module' => 'siteeventticket', 'controller' => 'order', 'action' => 'admin-cheque-detail'), 'default', true); ?>
              <li class="active"><a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $chequeDetailUrl ?>')" class="siteeventticket_icon_cheque buttonlink"><?php echo $this->translate("%s's Bank Account Details", $this->site_title) ?></a></li>
            <?php elseif (!empty($this->orderObj->direct_payment) && $this->orderObj->gateway_id == 3) : ?>
              <?php $eventChequeDetail = Engine_Api::_()->getDbtable('eventGateways', 'siteeventticket')->getEventChequeDetail(array('event_id = ?' => $this->orderObj->event_id, "title = 'ByCheque'", "enabled = 1")); ?>
              <?php if (!empty($eventChequeDetail)) : ?>&nbsp;
                <?php $chequeDetailUrl = $this->url(array('module' => 'siteeventticket', 'controller' => 'order', 'action' => 'event-cheque-detail', 'event_id' => $this->orderObj->event_id, 'title' => $this->eventTitle), 'default', true); ?>
                <li class="active"><a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $chequeDetailUrl ?>')" ><?php echo $this->translate("%s event's Bank Account Details", $this->eventTitle) ?></a></li>
              <?php endif; ?>
            <?php endif; ?>
            
            <?php $tempInvoice = $this->url(array('action' => 'print-invoice', 'order_id' => Engine_Api::_()->siteeventticket()->getDecodeToEncode($this->order_id), 'event_id' => $this->orderObj->event_id), 'siteeventticket_order', true); ?>
          <?php if ($showPrintLinks) : ?>
            <?php $tempInvoice = $this->url(array('action' => 'print-invoice', 'order_id' => Engine_Api::_()->siteeventticket()->getDecodeToEncode($this->orderObj->order_id), 'event_id' => $this->orderObj->event_id), 'siteeventticket_order', true);?>
            <li class="active"><a href="<?php echo $tempInvoice; ?>" target="_blank"><?php echo $this->translate('Print Invoice') ?></a></li>
            <?php $tempPrint = $this->url(array('action' => 'print-ticket', 'order_id' => Engine_Api::_()->siteeventticket()->getDecodeToEncode($this->orderObj->order_id), 'event_id' => $this->orderObj->event_id), 'siteeventticket_order', true); ?>          
            <li class="active"><a href="<?php echo $tempPrint; ?>" target="_blank"><?php echo $this->translate("Print Tickets"); ?></a></li> 
          <?php endif; ?>             
      </ul>
    </div>
    </div>
    <?php endif; ?>
  <div class="clr">
    <div class="fleft"><h2><?php echo $this->translate('Order Id: #%s', $this->order_id) ?></h2></div>
    <div class="fright"><h2><?php echo $this->translate(' [ Total: %s ]', Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->orderObj->grand_total)) ?></h2></div>
  </div>
  <br>

  <div class="invoice_details_wrap clr">

    <!-- Billing Information -->
    <div class="invoice_add_details_wrap fleft">
      <div class="invoice_add_details">
        <b><?php echo $this->translate("Ordered For:") ?></b>
        <div>
          <?php if ($this->siteevent): ?>
            <?php echo $this->htmlLink($this->siteevent->getHref(), $this->siteevent->getTitle()) ?>
          <?php else: ?>
            <i><?php echo $this->translate("Deleted Event Occurrence") ?></i>
          <?php endif; ?>
        </div>
        <div>
          <?php
          echo $this->locale()->toDateTime($this->orderObj->occurrence_starttime);
          echo " - ";
          echo $this->locale()->toDateTime($this->orderObj->occurrence_endtime);
          ?>
        </div>
      </div>

      <div class="invoice_add_details">
        <b><?php echo $this->translate('Ordered by:') ?></b>
        <ul>
          <li>
            <div><?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle()) ?></div>
          </li>
          <?php if (isset($this->user->email)): ?>
            <li>
              <div><?php echo $this->translate($this->user->email); ?></div>
            </li>
          <?php endif; ?>
        </ul>
      </div>
      <!-- Payment Information -->
      <div class="invoice_add_details">
        <b><?php echo $this->translate('Payment Information') ?></b>
        <ul>
          <?php if (!empty($this->orderObj->direct_payment)) : ?>
            <?php if ($this->orderObj->order_status == 3) : ?>
              <li class="o_hidden">
                <div class="invoice_order_info fleft seaocore_txt_red"><?php echo $this->translate('marked as non-payment') ?></div>
              </li>
            <?php endif; ?>
          <?php endif; ?>
          <li>
            <div class="invoice_order_info fleft"><?php echo $this->translate('Payment Method') ?></div>
            <div><?php echo $this->translate(Engine_Api::_()->siteeventticket()->getGatwayName($this->orderObj->gateway_id)); ?></div>
          </li>
          <?php if (!empty($this->orderObj->direct_payment)) : ?>
            <?php if (!empty($this->orderObj->non_payment_seller_reason)) : ?>
              <li>
                <div class="invoice_order_info fleft"><?php echo $this->translate("Non-Payment Reason") ?></div>
                <?php if ($this->orderObj->non_payment_seller_reason == 1) : ?>
                  <div><?php echo $this->translate("Chargeback") ?></div>
                <?php elseif ($this->orderObj->non_payment_seller_reason == 2) : ?>
                  <div><?php echo $this->translate("Payment not received") ?></div>
                <?php elseif ($this->orderObj->non_payment_seller_reason == 3) : ?>
                  <div><?php echo $this->translate("Cancelled payment") ?></div>
                <?php endif; ?>
              </li>
            <?php endif; ?>
            <?php if (!empty($this->orderObj->non_payment_seller_message)) : ?>
              <li>
                <div class="invoice_order_info fleft"><?php echo $this->translate("Seller Message") ?></div>
                <div class="o_hidden"><?php echo $this->orderObj->non_payment_seller_message ?></div>
              </li>
            <?php endif; ?>
            <?php if (!empty($this->orderObj->non_payment_admin_reason)) : ?>
              <li>
                <div class="invoice_order_info fleft"><?php echo $this->translate("Non-Payment Action") ?></div>
                <?php if ($this->orderObj->non_payment_admin_reason == 1) : ?>
                  <div><?php echo $this->translate("Approved") ?></div>
                <?php elseif ($this->orderObj->non_payment_admin_reason == 2) : ?>
                  <div><?php echo $this->translate("Declined") ?></div>
                <?php endif; ?>
              </li>
            <?php endif; ?>
            <?php if (!empty($this->orderObj->non_payment_admin_message)) : ?>
              <li>
                <div class="invoice_order_info fleft"><?php echo $this->translate("Site Administrator Message") ?></div>
                <div class="o_hidden"><?php echo $this->orderObj->non_payment_admin_message ?></div>
              </li>
            <?php endif; ?>
          <?php endif; ?>
          <?php if ($this->orderObj->gateway_id == 3): ?>
            <li>
              <div class="invoice_order_info fleft"><?php echo $this->translate('Cheque No') ?></div>
              <div><?php echo $this->cheque_info['cheque_no'] ?></div>
            </li>
            <li>
              <div class="invoice_order_info fleft"><?php echo $this->translate('Account Holder Name') ?></div>
              <div><?php echo $this->cheque_info['customer_signature'] ?></div>
            </li>
            <li>
              <div class="invoice_order_info fleft"><?php echo $this->translate('Account Number') ?></div>
              <div><?php echo $this->cheque_info['account_number'] ?></div>
            </li>
            <li>
              <div class="invoice_order_info fleft"><?php echo $this->translate('Bank Rounting Number') ?></div>
              <div><?php echo $this->cheque_info['bank_routing_number'] ?></div>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <div class="invoice_order_details_wrap fright">
      <!-- Order Information -->
      <ul class="o_hidden">
        <li>
          <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Order Date') ?></strong></div>
          <div>: &nbsp;<?php echo $this->locale()->toDateTime($this->orderObj->creation_date); ?></div>
        </li>       
        <?php if (isset($this->displayAllDetails)) : ?>
        <li>
          <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Order Status') ?></strong></div>
          <?php if (!empty($this->orderObj->direct_payment) && $this->orderObj->order_status == 3) : ?>
            <div>-</div>
          <?php else: ?>
            <div>: &nbsp;<?php echo $this->getTicketOrderStatus($this->orderObj->order_status); ?></div>
          <?php endif; ?>
        </li>
          <li>
            <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Commission Type') ?></strong></div>
            <div>: &nbsp;<?php echo empty($this->orderObj->commission_type) ? $this->translate('Fixed') : $this->translate('Percentage'); ?></div>
          </li>
          <?php if (!empty($this->orderObj->commission_type)) : ?>
            <li>
              <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Commission Rate') ?></strong></div>
              <div>: &nbsp;<?php echo number_format($this->orderObj->commission_rate, 2) . ' %'; ?></div>
            </li>
          <?php endif; ?>
            <?php if ($this->orderObj->commission_value): ?>
          <li>
            <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Commission Amount') ?></strong></div>
            <div>: &nbsp;<?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->orderObj->commission_value); ?></div>
          </li>
          <?php endif; ?>
        <?php endif; ?>
        <?php if ($this->orderObj->tax_amount): ?>
          <li>
            <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Tax Amount') ?></strong></div>
            <div>: &nbsp;<?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->orderObj->tax_amount); ?></div>
          </li>        
          <li>
            <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Taxpayer ID No. (TIN)') ?></strong></div>
            <div>: &nbsp;<?php echo $this->tax_id_no; ?></div>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>   

  <b class="clr dblock mtop10 mbot5"><?php echo $this->translate("Order Details") ?></b>
  <div id="manage_order_tab">
    <div class="siteevent_detail_table">
      <table class="mbot10">
        <tr class="siteevent_detail_table_head">
          <th><?php echo $this->translate("Ticket"); ?></th>
          <th class="txt_right"><?php echo $this->translate('Price') ?></th>
          <?php if($this->isOrderHavingDiscount && !$this->fixedDiscount): ?>
            <th class="txt_right"><?php echo $this->translate('Discounted Price') ?></th>
          <?php endif; ?>
          <th class="txt_center"><?php echo $this->translate("Quantity"); ?></th>
          <th class="txt_right"><?php echo $this->translate('Subtotal') ?></th>
        </tr>
<?php foreach ($this->orderTickets as $item): ?>
          <tr>
            <td title=" <?php echo $this->translate($item->title); ?>">
              <?php echo $this->translate($item->title); ?>
              <?php if (!empty($order_ticket_info) && !empty($order_ticket_info['calendarDate']) && !empty($order_ticket_info['calendarDate']['starttime']) && !empty($order_ticket_info['calendarDate']['endtime'])) : ?>
                <?php echo '</br><b>' . $this->translate("From:") . '</b>' . ' ' . $this->locale()->toDate($order_ticket_info['calendarDate']['starttime']) . '</br>'; ?>
                <?php echo '<b>' . $this->translate("To:") . '</b>' . ' ' . $this->locale()->toDate($order_ticket_info['calendarDate']['endtime']); ?>
              <?php endif; ?>
            </td>
            <td class="txt_right">
              <?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($item->price); ?>
              <?php if (!empty($order_ticket_info) && !empty($order_ticket_info['price_range_text'])) : ?>
                <?php echo $this->translate($order_ticket_info['price_range_text']) ?>
  <?php endif; ?>
            </td>
            <?php if($this->isOrderHavingDiscount && !$this->fixedDiscount): ?>
                <td class="txt_right">
                    <?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($item->discounted_price); ?>
                </td>
            <?php endif; ?>
            <td class="txt_center"><?php echo $item->quantity; ?></td>
            <td class="txt_right"><b><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency(($item->discounted_price * $item->quantity)); ?></b></td>
          </tr>
<?php endforeach; ?>  
      </table>
    </div>
  </div>

  <b class="clr dblock mtop10 mbot5"><?php echo $this->translate("Order Summary") ?></b>
  <div class="clr o_hidden">
    <div class="invoice_ttlamt_box_wrap fright mbot10">
      <div class="invoice_ttlamt_box fleft">
        <div class="clr">
          <div class="fleft"><?php echo $this->translate('Subtotal'); ?></div>
          <div class="fright"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency(($this->orderObj->sub_total)) . '<br/>'; ?></div>
        </div>
        <?php $orderCouponAmount = 0; ?>
        <?php if (!empty($this->coupon_details)) : ?>
            <?php $orderCouponAmount = $this->coupon_details['coupon_amount']; ?>
        <?php endif; ?>          
        <?php if (!empty($orderCouponAmount) && $this->fixedDiscount) : ?>
          <div>
            <div  class="clr">
              <div class="fleft"><?php echo $this->translate("Discount"); ?></div>
              <div class="fright"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($orderCouponAmount); ?></div>
            </div>
          </div>
        <?php endif; ?>
        <?php if ($this->orderObj->tax_amount): ?>
        <div class="clr">  
          <div class="fleft"><?php echo $this->translate('Taxes'); ?></div>
          <div class="fright"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency(($this->orderObj->tax_amount)) . '<br/>'; ?> </div>
        </div>
        <?php endif; ?>
         <div  class="clr">
          <div class="fleft"><strong><?php echo $this->translate('Grand Total'); ?></strong></div>
          <div class="fright"><strong><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->orderObj->grand_total); ?></strong></div>
         </div>
      </div>
    </div>
  </div>
</div>
