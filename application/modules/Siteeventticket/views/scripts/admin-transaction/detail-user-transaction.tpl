<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail-user-transaction.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/admin/style_siteeventticket.css');
?>

<div class="global_form_popup">
  <div id="manage_order_tab">
    <h3><?php echo 'Transaction Details'; ?></h3>
    <div class="invoice_order_details_wrap mtop10" style="border-width:1px;">
      <ul class="payment_transaction_details">
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Transaction ID'; ?></b></div>
          <div><?php echo $this->locale()->toNumber($this->transaction_obj->transaction_id) ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Order Id' ?></b></div>
          <div>
            <?php
            $order_view_url = $this->url(array('action' => 'view', 'order_id' => $this->order_id, 'menuId' => 55), 'siteeventticket_order', true);
            echo $this->htmlLink($order_view_url, "#" . $this->order_id, array('target' => '_blank'));
            ?>
          </div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Buyer Name'; ?></b></div>
          <div><?php echo empty($this->transaction_obj->user_id) ? '-' : $this->htmlLink($this->user_obj->getHref(), $this->user_obj->getTitle(), array('target' => '_blank')) ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Payment Gateway'; ?></b></div>
          <?php if (empty($this->gateway_name)): ?>
            <div><i><?php echo 'Unknown Gateway' ?></i></div>
          <?php else: ?>
            <div><?php echo $this->gateway_name ?></div>
<?php endif; ?>
        </li> 
        <!--IF PAYMENT VIA CHEQUE THEN DISPLAY CHEQUE DETAIL  -->
<?php if (!empty($this->cheque_detail)) : ?>
          <li>
            <div class="invoice_order_info fleft"><b><?php echo 'Cheque No'; ?></b></div>
            <div><?php echo $this->cheque_detail['cheque_no'] ?></div>
          </li>
          <li>
            <div class="invoice_order_info fleft"><b><?php echo 'Account Holder Name'; ?></b></div>
            <div><?php echo $this->cheque_detail['customer_signature'] ?></div>
          </li>
          <li>
            <div class="invoice_order_info fleft"><b><?php echo 'Account Number'; ?></b></div>
            <div><?php echo $this->cheque_detail['account_number'] ?></div>
          </li>
          <li>
            <div class="invoice_order_info fleft"><b><?php echo 'Bank Routing Number'; ?></b></div>
            <div><?php echo $this->cheque_detail['bank_routing_number'] ?></div>
          </li> 
<?php endif; ?>

        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Payment Type'; ?></b></div>
          <div><?php echo ucfirst($this->transaction_obj->type) ?></div>
        </li> 
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Payment State'; ?></b></div>
          <div><?php echo ucfirst($this->transaction_obj->state) ?></div>
        </li> 
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Payment Amount'; ?></b></div>
          <div><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->transaction_obj->amount) ?></div>
        </li> 
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Gateway Transaction ID'; ?></b></div>
          <div><?php
            if (!empty($this->transaction_obj->gateway_transaction_id) && $this->transaction_obj->gateway_id != 3):
              echo $this->htmlLink(array(
               'route' => 'admin_default',
               'module' => 'siteeventticket',
               'controller' => 'payment',
               'action' => 'detail-transaction',
               'transaction_id' => $this->transaction_obj->transaction_id,
                  ), $this->transaction_obj->gateway_transaction_id, array(
               'target' => '_blank',
              ));
            elseif (!empty($this->transaction_obj->gateway_transaction_id) && $this->transaction_obj->gateway_id == 3):
              echo $this->transaction_obj->gateway_transaction_id;
            else:
              echo '-';
            endif;
            ?>
          </div>
        </li> 
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Date'; ?></b></div>
          <div><?php echo gmdate('M d,Y, g:i A', strtotime($this->transaction_obj->date)) ?></div>
        </li> 
      </ul>
    </div>
  </div>
  <div class='buttons mtop10'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo "Close" ?></button>
  </div>
</div>