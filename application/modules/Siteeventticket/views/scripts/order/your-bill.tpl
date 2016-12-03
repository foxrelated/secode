<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: your-bill.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript"> 
  Asset.css('<?php echo $this->layout()->staticBaseUrl
	    . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'?>');
</script>

<?php if (!$this->only_list_content): ?>
  <?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
  <div class="siteevent_dashboard_content">
    <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
    <div class="siteevent_event_form">
      <div id="siteevent_manage_order_content"> 
      <?php endif; ?> 
      <div class="siteeventticket_payment_to_me">
        <h3><?php echo $this->translate('Your Bill of Commissions') ?></h3>
        <p class="mbot10 mtop5">
            <?php if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)): ?>
                <?php echo $this->translate("Below, you can view the history of commissions paid by you so far for your event's ticket sales and can also pay the unpaid commissions. [Note: The payments made using 'Stripe Connect' gateway are not added here because those commissions are already paid at the time of payment.]"); ?>
            <?php else: ?>
                <?php echo $this->translate("Below, you can view the history of commissions paid by you so far for your event's ticket sales and can also pay the unpaid commissions."); ?>
            <?php endif; ?>
        </p>
        
        <?php if(Engine_Api::_()->siteeventticket()->isAllowThresholdNotifications(array('event_id' => $this->event_id))):?>
            <div class="tip">
                <span class="seaocore_txt_red">
                    <?php echo $this->translate("Threshold amount for admin commission bill has been exceeded. Please pay your bill for availing uninterrupted services.");?>
                </span>
            </div>
        <?php endif; ?>

        <table class="siteeventticket_amount_table siteeventticket_data_table">
          <tr>
            <td class="highlight txt_center">
              <span><?php echo $this->translate('Total Bill Amount [A = B+C]') ?></span>
              <span class="txt_center bold f_small dblock"><?php echo $this->translate('(Amount to be paid)') ?></span>
              <div class="txt_center bold"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->totalBillAmount); ?></div>
            </td>
            <td class="txt_center">
              <span><?php echo $this->translate('New Bill Amount [B]') ?></span>
              <span class="txt_center bold f_small dblock"><?php echo $this->translate('(Since last bill)'); ?></span>
              <div class="txt_center bold"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->newBillAmount); ?></div>
            </td>
            <td class="txt_center">
              <span><?php echo $this->translate('Remaining Bill Amount [C]') ?></span>
              <div class="txt_center bold"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->remainingBillAmount) ?></div>
            </td>
            <td class="txt_center">
              <span><?php echo $this->translate('Paid Bill Amount') ?></span>
              <div class="txt_center bold"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->paidBillAmount) ?></div>
            </td>
          </tr> 
        </table>

        <?php if (!empty($this->totalBillAmount)) : ?>
          <div class="clr mbot10">
            <a class="buttonlink siteeventticket_icon_payment" href="javascript:void(0)" onClick="Smoothbox.open('siteeventticket/order/bill-payment/event_id/<?php echo $this->event_id ?>');"><?php echo $this->translate("Pay your bill") ?></a>
          </div>
        <?php endif; ?>

        <div id="event_bill_details">
          <h4><?php echo $this->translate('Monthly Statements') ?></h4>
          <?php if (count($this->paginator)): ?>
            <div class="siteevent_detail_table">
              <table>
                <tr class="siteevent_detail_table_head">
                  <th><?php echo $this->translate("Month") ?></th>
                  <th><?php echo $this->translate("Order Count") ?></th>
                  <th><?php echo $this->translate("Order Amount") ?></th>
                  <th><?php echo $this->translate("Commission Amount") ?></th>
                  <th class="txt_center"><?php echo $this->translate("Options") ?></th>
                </tr>
                <?php foreach ($this->paginator as $payment) : ?>        
                  <tr>
                    <td>
                      <a href="javascript:void(0)" onclick="manage_event_dashboard(56, 'monthly-bill-detail/month/<?php echo $payment->month_no; ?>/year/<?php echo $payment->year ?>', 'order')">
                        <?php echo $this->translate("%s %s", $payment->month, $payment->year) ?>
                      </a>
                    </td>
                    <td><?php echo $payment->order_count ?></td>
                    <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($payment->grand_total) ?></td>
                    <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($payment->commission) ?></td>
                    <td class="event_actlinks txt_center">
                      <a href="javascript:void(0)" onclick="manage_event_dashboard(56, 'monthly-bill-detail/month/<?php echo $payment->month_no; ?>/year/<?php echo $payment->year ?>', 'order')" title="<?php echo $this->translate("details") ?>" class="siteevent_icon_detail"> 
                      </a>
                    </td>   
                  </tr>
                <?php endforeach; ?>  
              </table>
            </div>
          </div>
        <?php else: ?>
          <div class="tip">
            <span>
              <?php echo $this->translate("You have not paid any commission for this event yet."); ?>
            </span>
          </div>
        <?php endif; ?>
      </div>
      <?php if (!$this->only_list_content): ?>
      </div>
    </div>	
  </div>	
<?php endif; ?>
</div>