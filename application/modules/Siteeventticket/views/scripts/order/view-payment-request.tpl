<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-payment-request.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'); ?>
<?php
if ($this->payment_req_obj->request_status == 0):
  $request_status = 'Requested';
elseif ($this->payment_req_obj->request_status == 1):
  $request_status = '<i><font color="red">Deleted</font></i>';
elseif ($this->payment_req_obj->request_status == 2):
  $request_status = '<i><font color="green">Completed</font></i>';
endif;

if ($this->payment_req_obj->payment_status != 'active'):
  $payment_status = 'No';
else:
  $payment_status = 'Yes';
endif;
?>
<div class="global_form_popup" style="width:600px;">
  <div id="manage_order_tab">
    <div class="invoice_order_details_wrap mtop10" style="border-width:1px;width:600px;">
      <ul>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Request Id'); ?></b></div>
          <div><?php echo $this->request_id; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Event Name'); ?></b></div>
          <div><?php echo $this->htmlLink($this->siteevent->getHref(), $this->siteevent->getTitle(), array('onclick' => 'redirectLink(\'' . $this->siteevent->getHref() . '\')')); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Owner Name'); ?></b></div>
          <div><?php echo $this->htmlLink($this->userObj->getHref(), $this->userObj->getTitle(), array('onclick' => 'redirectLink(\'' . $this->siteevent->getHref() . '\')')); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Requested Amount'); ?></b></div>
          <div><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->payment_req_obj->request_amount); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Requested Message'); ?></b></div>
          <div><?php echo empty($this->payment_req_obj->request_message) ? '-' : $this->payment_req_obj->request_message; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Requested Date'); ?></b></div>
          <div><?php echo $this->payment_req_obj->request_date; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Response Amount'); ?></b></div>
          <div><?php echo empty($this->payment_req_obj->response_amount) ? '-' : Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->payment_req_obj->response_amount); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Response Message'); ?></b></div>
          <div><?php echo empty($this->payment_req_obj->response_message) ? '-' : $this->payment_req_obj->response_message; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Response Date'); ?></b></div>
          <div><?php echo empty($this->payment_req_obj->response_amount) ? '-' : $this->payment_req_obj->response_date; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Remaining Amount'); ?></b></div>
          <div><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->payment_req_obj->remaining_amount); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Status'); ?></b></div>
          <div><?php echo $this->translate($request_status); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Payment Status'); ?></b></div>
          <div><?php echo $this->translate($payment_status); ?></div>
        </li>
      </ul> 
    </div>
  </div>
  <div class='buttons mtop10'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Cancel") ?></button>
  </div>
</div>

<script type="text/javascript">
  function redirectLink(url)
  {
    parent.window.location.href = url;
    parent.Smoothbox.close();
  }
</script>