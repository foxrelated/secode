<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print-invoice.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (!empty($this->siteeventticket_view_no_permission)) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("You don't have permission to print the invoice of this order.") ?>
    </span>
  </div>
  <?php
  return;
endif;
?>

<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket_print.css');
?>
<link href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket_print.css' ?>" type="text/css" rel="stylesheet" media="print">

<div class="invoice_wrap">
  <div class="invoice_head_wrap">
    <div class="invoice_head">
      <div class="logo fleft">
        <b><?php echo ($this->logo) ? $this->htmlImage($this->logo) : $this->site_title; ?></b>
      </div>
      <div class="name fright">
        <strong><?php echo $this->translate('INVOICE') ?></strong>
      </div>
    </div>
  </div>

  <div class="invoice_details_wrap"> <!--Address Details outer div-->
    <div class="invoice_add_details_wrap fleft">
      <div class="invoice_add_details"> <!--Event Address-->
        <b><?php echo $this->translate("Event Name") ?></b><br/>
        <div>
          <?php if ($this->siteevent): ?>
            <?php echo $this->translate($this->siteevent->getTitle()) ?>
          <?php else: ?>
            <i><?php echo $this->translate("Deleted Event Occurrence") ?></i>
          <?php endif; ?>
        </div>
        <?php
        echo $this->locale()->toDateTime($this->orderObj->occurrence_starttime);
        echo " - ";
        echo $this->locale()->toDateTime($this->orderObj->occurrence_endtime);
        ?>
      </div>
      <?php if (isset($this->user_detail->email) && $this->user_detail->email): ?>
        <div class="invoice_add_details">
          <?php echo '<b>' . $this->translate("Contact Information") . '</b><br />'; ?>
          <?php echo $this->translate($this->user_detail->email); ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="invoice_order_details_wrap fright">
      <ul>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate("Name"); ?></b></div>
          <div>: &nbsp;<?php if (isset($this->user_detail) && $this->user_detail): ?><?php echo $this->translate($this->user_detail->displayname); ?><?php else: ?><i><?php echo $this->translate("Deleted Member"); ?></i><?php endif; ?>
          </div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate("Order Id") ?></b></div> 
          <div>: &nbsp;<?php echo $this->translate("#%s", $this->orderObj->order_id); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Purchased on'); ?></b> </div>
          <div class="o_hidden">: &nbsp;<?php echo $this->locale()->toDateTime($this->orderObj->creation_date) . '<br/>'; ?> </div>
        </li>
        <li>
          <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Payment Method'); ?></b> </div>
          <div>: &nbsp;<?php echo Engine_Api::_()->siteeventticket()->getGatwayName($this->orderObj->gateway_id) . '<br/>'; ?> </div>
        </li>

        <?php if ($this->orderObj->gateway_id == 3) : ?>
          <?php if ($this->paymentToSiteadmin && !empty($this->site_title) && !empty($this->admin_cheque_detail)) : ?>
            <li>
              <b><?php echo $this->translate("%s's Bank Account Details", $this->site_title) ?></b>
              <div><pre><?php echo $this->admin_cheque_detail ?></pre></div>
            </li>
          <?php elseif (empty($this->paymentToSiteadmin) && !empty($this->eventTitle) && !empty($this->eventChequeDetail)) : ?>
            <li>
              <b><?php echo $this->translate("%s event's Bank Account Details", $this->eventTitle) ?></b>
              <div><pre><?php echo $this->eventChequeDetail ?></pre></div>
            </li>
          <?php endif; ?>
          <li>
            <b><?php echo $this->translate('Buyer Account Info') ?></b>
            <div class="o_hidden">
              <div class="clr">
                <div class="invoice_order_info fleft"><?php echo $this->translate('Cheque No') ?></div>
                <div>: &nbsp;<?php echo $this->cheque_info['cheque_no'] ?></div>
              </div>
              <div class="clr">
                <div class="invoice_order_info fleft"><?php echo $this->translate('Account Holder Name') ?></div>
                <div>: &nbsp;<?php echo $this->cheque_info['customer_signature'] ?></div>
              </div>
              <div class="clr">
                <div class="invoice_order_info fleft"><?php echo $this->translate('Account Number') ?></div>
                <div>: &nbsp;<?php echo $this->cheque_info['account_number'] ?></div>
              </div>
              <div class="clr">
                <div class="invoice_order_info fleft"><?php echo $this->translate('Bank Rounting Number') ?></div>
                <div>: &nbsp;<?php echo $this->cheque_info['bank_routing_number'] ?></div>
              </div>
            </div>
          </li>
        <?php endif; ?>
        <!--TAX ID NO DISPLAY WORK-->
        <?php if ($this->orderObj->tax_amount): ?>
          <li>
            <div class="invoice_order_info fleft"><b><?php echo $this->translate('TIN No.') ?></b></div>
            <div>: &nbsp;<?php echo $this->tax_id_no; ?></div>
          </li>
        <?php endif; ?>
        <!--TAX ID NO DISPLAY WORK-->
      </ul>
    </div>
  </div>

  <b class="dblock clr mtop10 mbot5"><?php echo $this->translate("Order Details") ?></b>
  <div id="manage_order_tab">
    <div class="siteevent_detail_table siteeventticket_data_table fleft mbot10">
      <table>
        <tr class="siteevent_detail_table_head">
          <th class="ticket"><?php echo $this->translate("Title"); ?></th>
          <th class="quantity txt_right"><?php echo $this->translate("Quantity"); ?></th>
          <th class="price txt_right"><?php echo $this->translate("Price"); ?></th>
          <?php if($this->isOrderHavingDiscount && !$this->fixedDiscount): ?>
                <th class="txt_right"><?php echo $this->translate('Discounted Price') ?></th>
          <?php endif; ?>          
          <th class="total txt_right"><?php echo $this->translate("Total"); ?></th>
        </tr>
        <?php foreach ($this->orderTickets as $ticket) : ?>
          <tr>
            <td title="<?php echo $this->translate($ticket->title); ?>">
              <?php echo $this->translate($ticket->title); ?>
              <?php
              if (!empty($ticket->configuration)):
                $configuration = Zend_Json::decode($ticket->configuration);
                echo '<br/>';
                foreach ($configuration as $config_name => $config_value):
                  echo "<b>" . $config_name . "</b>: $config_value<br/>";
                endforeach;
              endif;
              ?>
            </td>
            <td class="txt_right"><?php echo $ticket->quantity; ?></td>
            <td class="txt_right"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($ticket->price); ?></td>
            <?php if($this->isOrderHavingDiscount && !$this->fixedDiscount): ?>
                <td class="txt_right">
                    <?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($ticket->discounted_price); ?>
                </td>
            <?php endif; ?>
            <td class="txt_right"><b><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($ticket->discounted_price * $ticket->quantity); ?></b></td>
          </tr>
        <?php endforeach; ?>  
      </table>
    </div>
  </div>

  <b class="dblock clr mtop10 mbot5"><?php echo $this->translate("Order Summary") ?></b>
  <div class="invoice_ttlamt_box_wrap mbot10 fright">
    <div class="invoice_ttlamt_box fleft">
        <?php $orderCouponAmount = 0; ?>
        <?php if (!empty($this->coupon_details)) : ?>
            <?php $orderCouponAmount = $this->coupon_details['coupon_amount']; ?>
        <?php endif; ?>
      <div class="clr">
        <div class="fleft"><?php echo $this->translate('Subtotal'); ?></div>
        <div class="fright"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency(($this->orderObj->sub_total)) . '<br/>'; ?></div>
      </div>
      <?php if (!empty($orderCouponAmount) && $this->fixedDiscount) : ?>
        <div>
          <div  class="clr">
            <div class="fleft"><?php echo $this->translate("Discount"); ?></div>
            <div class="fright"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($orderCouponAmount); ?></div>
          </div>
        </div>
<!--        <div>
          <div  class="clr">
            <div class="fleft"><strong><?php echo $this->translate("Total"); ?>&nbsp;&nbsp;</strong></div>
            <div class="fright"><strong><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->orderObj->sub_total); ?></strong></div>
          </div>
        </div>-->
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

<script type="text/javascript">
  window.print();
</script>