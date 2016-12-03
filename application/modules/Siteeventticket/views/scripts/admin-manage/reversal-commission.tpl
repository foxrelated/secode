<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: reversal-commission.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup siteevent_claim_action_popup">
  <div class="settings">
    <form class="global_form" method="POST">
      <div>
        <?php if ($this->order->non_payment_admin_reason == 1): ?>
          <h3><?php echo "Details"; ?></h3>
          <p><?php echo "Below are the details of the reversal commission request that was approved." ?></p>
        <?php elseif ($this->order->non_payment_admin_reason == 2): ?>
          <h3><?php echo "Details"; ?></h3>
          <p><?php echo "Below are the details of the reversal commission request that was declined." ?></p>
        <?php else: ?>	
          <h3><?php echo "Take an Action"; ?></h3>
          <p><?php echo "Please take an appropriate action on the reversal commission request for this order. Once you take an action, an email will be sent to event owner stating the action taken by you." ?></p><br />
        <?php endif; ?>
        <div class="form-wrapper">
          <div class="form-label">
            <label><?php echo "Order Id:" ?></label>
          </div>
          <div class="form-element">
            <a href="<?php echo $this->url(array('action' => 'view', 'event_id' => $this->order->event_id, 'order_id' => $this->order->order_id, 'menuId' => 55), 'siteeventticket_order', false) ?>" target="_blank">
              <?php echo '#' . $this->order->order_id; ?>
            </a>
          </div>
        </div>
        <div class="form-wrapper">
          <div class="form-label">
            <label><?php echo "Event Name:" ?></label>
          </div>
          <div class="form-element">
            <?php echo $this->htmlLink($this->siteevent->getHref(), $this->siteevent->getTitle(), array('target' => '_blank')); ?>
          </div>
        </div>
        <div class="form-wrapper">
          <div class="form-label">
            <label><?php echo "Owner Name:" ?></label>
          </div>
          <div class="form-element">
            <?php echo $this->htmlLink($this->eventOwner->getHref(), $this->eventOwner->getTitle(), array('target' => '_blank')); ?>
          </div>
        </div>
        <div class="form-wrapper">
          <div class="form-label">
            <label><?php echo "Order Amount:" ?></label>
          </div>
          <div class="form-element">
            <?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->order->grand_total); ?>
          </div>
        </div>		
        <div class="form-wrapper">
          <div class="form-label">
            <label><?php echo "Commission Amount:" ?></label>
          </div>
          <div class="form-element">
            <?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->order->commission_value); ?>
          </div>
        </div>
        <div class="form-wrapper">
          <div class="form-label">
            <label><?php echo "Seller Reason:" ?></label>
          </div>
          <div class="form-element">
            <?php if ($this->order->non_payment_seller_reason == 1) : ?>
              <?php echo 'Chargeback' ?>
            <?php elseif ($this->order->non_payment_seller_reason == 2) : ?>
              <?php echo 'Payment not received' ?>
            <?php elseif ($this->order->non_payment_seller_reason == 3) : ?>
              <?php echo 'Canceled payment' ?>
            <?php endif; ?>
          </div>
        </div>
        <div class="form-wrapper">
          <div class="form-label">
            <label><?php echo "Seller Message:" ?></label>
          </div>
          <div class="form-element">
            <?php echo empty($this->order->non_payment_seller_message) ? '-' : $this->order->non_payment_seller_message ?>
          </div>
        </div>
        <div class="form-wrapper">
          <div class="form-label">
            <label><?php echo "Action:" ?> </label>
          </div>
          <div class="form-element">
            <?php if ($this->order->non_payment_admin_reason == 1) : ?>
              <?php echo "Approved" ?>
            <?php elseif ($this->order->non_payment_admin_reason == 2) : ?>
              <?php echo "Declined" ?>
            <?php else: ?>
              <select name="reversal_commission_action">
                <option value="1"><?php echo "Approved" ?></option>
                <option value="2"><?php echo "Declined" ?></option>
<!--                <option value="3" <?php if ($this->order->non_payment_admin_reason == 3) : echo 'selected';
            endif; ?>><?php echo "Hold" ?></option>-->
              </select>
<?php endif; ?>
          </div>
        </div>
        <div class="form-wrapper">
          <div class="form-label">
            <label><?php echo "Admin's Comments:" ?> </label>
          </div>
          <div class="form-element">
            <?php if ($this->order->non_payment_admin_reason == 1 || $this->order->non_payment_admin_reason == 2) : ?>
              <?php if (!empty($this->order->non_payment_admin_message)): ?>
                <?php echo $this->order->non_payment_admin_message; ?>
              <?php else: ?>
                <?php echo '---'; ?>
              <?php endif; ?>
            <?php else: ?>		
              <textarea name="non_payment_admin_message"><?php echo $this->order->non_payment_admin_message; ?></textarea>
<?php endif; ?>
          </div>
        </div>
        <div class="form-wrapper">
          <div class="form-label">
            <label>&nbsp;</label>
          </div>
          <div class="form-element">
            <?php if ($this->order->non_payment_admin_reason == 1) : ?>
              <button onclick='javascript:parent.Smoothbox.close()'><?php echo "Close" ?></button>
            <?php elseif ($this->order->non_payment_admin_reason == 2) : ?>
              <button onclick='javascript:parent.Smoothbox.close()'><?php echo "Close" ?></button>
            <?php else: ?>
              <button type='submit'><?php echo 'Save'; ?></button>
              <?php echo " or " ?> 
              <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo "cancel" ?></a>
<?php endif; ?>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>