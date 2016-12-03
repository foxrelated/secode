<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: process-payment.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/admin/style_siteeventticket.css'); ?>

<h2 class="fleft">
  <?php echo 'Advanced Events Plugin'; ?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'payment'), "Back to Payment Requests from Sellers", array('class' => 'seaocore_icon_back buttonlink mbot10')) ?>

<?php
if (!empty($this->siteeventticket_payment_req_delete)) :
  echo '<div class="tip">
          <span>
            ' . $this->siteevent->getTitle() . ' event has cancelled this payment request.
          </span>
        </div>';
  return;
endif;

if (!empty($this->gateway_disable)) :
  echo '<div class="tip">
          <span>
            ' . $this->siteevent->getTitle() . ' event has currently disabled its payment gateway, so you can not approve the payment request currently.
          </span>
        </div>';
else :
  if (empty($this->payment_req_obj->request_status)):
    $request_status = 'Requested';
  else:
    echo '<div class="tip">
          <span>
             This payment request has been already processed for approval.
          </span>
        </div>';
    return;
  endif;
  echo $this->htmlLink($this->url(array('action' => 'payment-to-me', 'event_id' => $this->payment_req_obj->event_id), 'siteeventticket_order', true), "event payment details", array('target' => '_blank', 'class' => 'buttonlink siteeventticket_icon_payment'));
  ?>
  <div>
    <div class="invoice_order_details_wrap mtop10" style="border-width:1px;">
      <ul class="payment_transaction_details">
        <li>
          <div class="invoice_order_info fleft"> <b><?php echo 'Event Owner'; ?> </b> </div>
          <div>: &nbsp;<?php echo $this->htmlLink($this->userObj->getHref(), $this->userObj->getTitle(), array('target' => "_blank")) ?> </div>
        </li>
        <li>
          <div class="invoice_order_info fleft"> <b><?php echo 'Event Name'; ?></b> </div>
          <div>: &nbsp;<?php echo $this->htmlLink($this->siteevent->getHref(), $this->siteevent->getTitle(), array('target' => "_blank")) ?> </div>
        </li>
        <li>
          <div class="invoice_order_info fleft"> <b><?php echo 'Status'; ?></b> </div>
          <div>: &nbsp;<?php echo $request_status; ?> </div>
        </li>
        <li>
          <div class="invoice_order_info fleft"> <b><?php echo 'Requested Amount'; ?></b> </div>
          <div>: &nbsp;<?php echo $this->locale()->toCurrency($this->payment_req_obj->request_amount, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?> </div>
        </li>
        <li>
          <div class="invoice_order_info fleft"> <b><?php echo 'Request Date'; ?></b> </div>
          <div>: &nbsp;<?php echo $this->payment_req_obj->request_date ?> </div>
        </li>
        <?php if (!empty($this->payment_req_obj->request_message)): ?>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo 'Request Message'; ?></b> </div>
            <div>: &nbsp;<?php echo $this->payment_req_obj->request_message; ?> </div>
          </li>
        <?php endif; ?>
      </ul>
    </div>
    <div class="settings mtop10"> 
      <?php echo $this->form->render($this); ?>
    </div>
  <?php endif; ?>
</div>
