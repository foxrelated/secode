<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: payment-approve.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!$this->isAllowPaymentApprove): ?>
    <div class='global_form_popup'>
        <div class="tip">
          <span>
            <?php echo $this->translate("This order payment can not be approved because it leads to tickets sold quantity higher than tickets available quantity.") ?>
            <div class="buttons mtop10">
              <button type="button" name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
            </div>
          </span>
        </div>
    </div>
    <?php return; ?>     
<?php endif; ?>

<?php $orderViewUrl = $this->htmlLink($this->url(array('action' => 'view', 'event_id' => $this->order_obj->event_id, 'order_id' => $this->order_id, 'menuId' => 55), 'siteeventticket_order', true), '#' . $this->order_id, array('target' => '_blank')); ?>

<?php if ($this->gateway_id == 3) : ?>
  <div class="global_form_popup">
    <h3><?php echo $this->translate("Approve Payment") ?></h3>
    <p><?php echo $this->translate("Here, approve payment made for the order: %s", $orderViewUrl) ?></p>
    <?php echo $this->form->render($this) ?>
  </div>
<?php else: ?>
  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Approve Payment") ?></h3>
      <p><?php echo $this->translate("Here, approve payment made for the order: %s", $orderViewUrl) ?></p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="true"/>
        <button type='submit'><?php echo $this->translate('Approve Payment'); ?></button>
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
          <?php echo $this->translate("cancel") ?>
        </a>
      </p>
    </div>
  </form>
<?php endif; ?>

