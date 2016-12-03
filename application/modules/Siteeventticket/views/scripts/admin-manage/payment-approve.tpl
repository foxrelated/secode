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
            <?php echo "This order payment can not be approved because it leads to tickets sold quantity higher than tickets available quantity." ?>
            <div class="buttons mtop10">
              <button type="button" name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo "Close" ?></button>
            </div>
          </span>
        </div>
    </div>
    <?php return; ?>     
<?php endif; ?>

<?php if (empty($this->paymentPending)) : ?>
  <div class="global_form_popup">
    <h3><?php echo "Approve Payment" ?></h3>
    <p><?php echo "Here, approve payment made for the order(s): " . $this->payment_approve_message ?></p>
    <?php echo $this->form->render($this) ?>
  </div>
<?php else: ?>
  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo "Approve Payment" ?></h3>
      <p><?php echo "Here, approve payment made for the order(s): " . $this->payment_approve_message ?></p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="true"/>
        <button type='submit'><?php echo 'Approve Payment'; ?></button>
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
          <?php echo "cancel" ?>
        </a>
      </p>
    </div>
  </form>
<?php endif; ?>

