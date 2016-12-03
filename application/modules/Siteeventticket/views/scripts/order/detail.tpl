<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!empty($this->siteeventticket_view_detail_no_permission)) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("Order not available or you don't have permission to view this order detail.") ?>
    </span>
  </div>
  <?php
  return;
endif;
?>

<script type="text/javascript"> 
  Asset.css('<?php echo $this->layout()->staticBaseUrl
	    . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'?>');
</script>

<div class="global_form_popup">
  <div class="invoice_wrap">
    <div class="invoice_head_wrap">
      <div class="invoice_head">
        <div class="logo fleft">
          <b><?php echo ($this->logo) ? $this->htmlImage($this->logo) : $this->site_title; ?></b>
        </div>
        <div class="name fright">
          <strong><?php echo $this->translate("Order #%s", $this->orderObj->order_id) ?></strong>
        </div>
      </div>
    </div>

    <div class="invoice_details_wrap"> <!--Address Details outer div-->
      <div class="invoice_add_details_wrap fleft">
        <div class="invoice_add_details"> <!--Event Address-->
          <b><?php echo $this->translate("Event Name & Address") ?></b><br/>
<?php echo $this->eventAddress; ?>
        </div>
      </div>
      <div class="invoice_order_details_wrap fright" style="width:398px;">
        <ul>
<?php if (!empty($this->orderObj->user_id)): ?>
            <li>
              <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Name'); ?></b> </div>
              <div>: &nbsp;<?php echo $this->user_detail->displayname . '<br/>'; ?> </div>
            </li>
<?php endif; ?>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate("Order Id"); ?> </b> </div>
            <div>: &nbsp;<?php echo '#' . $this->orderObj->order_id . '<br/>'; ?> </div>
          </li>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Status'); ?> </b> </div>
            <?php if (!empty($this->orderObj->direct_payment) && $this->orderObj->order_status == 3) : ?>
              <div>: &nbsp;-</div>
            <?php else: ?>
              <div>: &nbsp;<?php echo $this->getTicketOrderStatus($this->orderObj->order_status) . '<br/>'; ?> </div>
<?php endif; ?>
          </li>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Payment Method'); ?></b> </div>
            <div>: &nbsp;<?php echo $this->translate(Engine_Api::_()->siteeventticket()->getGatwayName($this->orderObj->gateway_id)) . '<br/>'; ?> </div>
          </li>
        </ul>
      </div>
    </div>

    <b class="clr dblock mtop10 mbot5"><?php echo $this->translate("Order Details") ?></b>
    <div id="manage_order_tab">
      <div class="siteevent_detail_table siteeventticket_data_table fleft">
        <table>
          <tr class="siteevent_detail_table_head">
            <th><?php echo $this->translate("Ticket(s)"); ?></th>
            <th class="txt_center"><?php echo $this->translate("Quantity"); ?></th>
            <th class="txt_right"><?php echo $this->translate("Unit Price"); ?></th>
            <?php if($this->isOrderHavingDiscount && !$this->fixedDiscount): ?>
                  <th class="txt_right"><?php echo $this->translate('Discounted Price') ?></th>
            <?php endif; ?>                  
            <th class="txt_right"><?php echo $this->translate("Total"); ?></th>
          </tr>
<?php foreach ($this->order_tickets as $ticket) : ?>
            <tr>
              <td title="<?php echo $ticket->title; ?>">
                <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($ticket->title, 40); ?>
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
              <td class="txt_center"><?php echo $ticket->quantity; ?></td>
              <td class="txt_right"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($ticket->price); ?></td>
                <?php if($this->isOrderHavingDiscount && !$this->fixedDiscount): ?>
                    <td class="txt_right">
                        <?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($ticket->discounted_price); ?>
                    </td>
                <?php endif; ?>              
              <td class="txt_right"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($ticket->price * $ticket->quantity); ?></td>
            </tr>
<?php endforeach; ?>  
        </table>
      </div>
    </div>

    <b class="clr dblock mtop10 mbot5"><?php echo $this->translate("Order Summary") ?></b>
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
        <?php endif; ?>
        <?php if($this->orderObj->tax_amount): ?>  
            <div class="clr">  
              <div class="fleft"><?php echo $this->translate('Taxes'); ?></div>
              <div class="fright"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->orderObj->tax_amount) . '<br/>'; ?> </div>
            </div>
        <?php endif; ?>
        <div  class="clr">
          <div class="fleft"><strong><?php echo $this->translate('Grand Total'); ?></strong></div>
          <div class="fright"><strong><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->orderObj->grand_total); ?></strong></div>
        </div>
      </div> 
    </div>
  </div>
  <br/><br/>

  <div class='buttons clr mleft10'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
  </div>
</div>