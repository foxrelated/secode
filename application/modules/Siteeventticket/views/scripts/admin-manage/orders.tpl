<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: orders.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/scripts/core.js'); 

?>

<script type="text/javascript">

  var detail_id;
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function (order, default_direction) {
    if (order == currentOrder) {
      $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
    }
    else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

  function findApprovalPendingOrders()
  {
    $('order_status').set('value', 1);
    $('display_only_site_payment_orders').set('value', 1);
    document.getElementById('manage_orders_search_form').submit();
  }



</script>


<h2>
  <?php echo 'Advanced Events Plugin'; ?>
</h2>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if (count($this->navigationGeneral)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
  </div>
<?php endif; ?>

<h3 style="margin-bottom:6px;"><?php echo "Manage Orders"; ?></h3>
<p>
  <?php echo "Below, you can manage and monitor ticket orders placed by the buyers on
your site. [Note: You also need to approve the payment here, if you have selected default payment flow for orders as 'Payment to Website / Site Admin' and buyers have placed thier orders by using 'By Cheque' and 'Pay at Event' payment methods]"; ?>
</p>
<br style="clear:both;" />

<div class="admin_search siteeventticket_admin_search">
  <div class="search">
    <form name="manage_orders_search_form" id="manage_orders_search_form" method="post" class="global_form_box">
      <input type="hidden" name="post_search" />
      <input type="hidden" name="display_only_site_payment_orders" value="0" id="display_only_site_payment_orders" /> 
      <div>
        <label>
          <?php echo "Event"; ?>
        </label>
        <?php if (empty($this->title)): ?>
          <input type="text" name="title" /> 
        <?php else: ?>
          <input type="text" name="title" value="<?php echo $this->title ?>"/>
        <?php endif; ?>
      </div>

      <div>
        <label>
          <?php echo "Buyer Name"; ?>
        </label>
        <?php if (empty($this->username)): ?>
          <input type="text" name="username" /> 
        <?php else: ?>
          <input type="text" name="username" value="<?php echo $this->username ?>"/>
        <?php endif; ?>
      </div>

      <div>
        <label><?php echo "Order Date: ex (2000-12-25)"; ?></label>
        <div>
          <?php if ($this->creation_date_start == ''): ?>
            <input type="text" name="creation_date_start" placeholder="from" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="creation_date_start" placeholder="from" value="<?php echo $this->creation_date_start ?>" class="input_field_small" />
          <?php endif; ?>

          <?php if ($this->creation_date_end == ''): ?>
            <input type="text" name="creation_date_end" placeholder="to" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="creation_date_end" placeholder="to" value="<?php echo $this->creation_date_end ?>" class="input_field_small" />
          <?php endif; ?>
        </div>   
      </div>

      <div>
        <label><?php echo "Order Amount"; ?></label>
        <div>
          <?php if ($this->order_min_amount == ''): ?>
            <input type="text" name="order_min_amount" placeholder="min" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="order_min_amount" placeholder="min" value="<?php echo $this->order_min_amount ?>" class="input_field_small" />
          <?php endif; ?>

          <?php if ($this->order_max_amount == ''): ?>
            <input type="text" name="order_max_amount" placeholder="max" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="order_max_amount" placeholder="max" value="<?php echo $this->order_max_amount ?>" class="input_field_small" />
          <?php endif; ?>
        </div>   
      </div>

      <div>
        <label><?php echo "Commission"; ?></label>
        <div>
          <?php if ($this->commission_min_amount == ''): ?>
            <input type="text" name="commission_min_amount" placeholder="min" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="commission_min_amount" placeholder="min" value="<?php echo $this->commission_min_amount ?>" class="input_field_small" />
          <?php endif; ?>

          <?php if ($this->commission_max_amount == ''): ?>
            <input type="text" name="commission_max_amount" placeholder="max" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="commission_max_amount" placeholder="max" value="<?php echo $this->commission_max_amount ?>" class="input_field_small" />
          <?php endif; ?>
        </div>
      </div>

      <div>
        <label><?php echo "Status"; ?></label>
        <select id="order_status" name="order_status">
          <option value="0" ><?php echo "Select" ?></option>
          <?php
          for ($index = 0; $index < 3;):
            if ($this->order_status == ($index + 1)):
              $selected = "selected";
            else:
              $selected = "";
            endif;

            echo '<option value="' . ($index + 1) . '" ' . $selected . '>' . $this->getTicketOrderStatus($index++) . '</option>';
          endfor;
          ?>

        </select>
      </div>
      
        <?php $otherOptionsString = $selected = '';?>
        <?php if(Engine_Api::_()->hasModuleBootstrap('sitegateway')): ?>
            <?php $enableGateways = Engine_Api::_()->getDbTable('gateways', 'payment')->getEnabledGateways();?>
            <?php foreach($enableGateways as $gateway):?>
                <?php if(!strstr($gateway->plugin, 'Sitegateway_')) continue; ?>
                <?php if($this->payment_gateway == $gateway->gateway_id): ?>
                    <?php $selected = 'selected'?>
                <?php endif;?>
                <?php $otherOptionsString .= "<option value='$gateway->gateway_id' $selected>$gateway->title</option>";?>
                
            <?php endforeach;?>
        <?php endif; ?>           

      <div>
        <label><?php echo "Payment Gateway" ?>	</label>
        <select id="payment_gateway" name="payment_gateway">
<?php if ($this->paymentToSiteadmin) : ?>
            <option value="0" ><?php echo "Select"; ?></option>
            <option value="1" <?php if ($this->payment_gateway == 1) echo "selected"; ?> ><?php echo "2Checkout"; ?></option>
            <option value="2" <?php if ($this->payment_gateway == 2) echo "selected"; ?> ><?php echo "PayPal"; ?></option>
            
            <?php echo $otherOptionsString;?>
            
            <option value="3" <?php if ($this->payment_gateway == 3) echo "selected"; ?> ><?php echo "By Cheque"; ?></option>
            <option value="4" <?php if ($this->payment_gateway == 4) echo "selected"; ?> ><?php echo "Pay at the Event"; ?></option>
            <option value="5" <?php if ($this->payment_gateway == 5) echo "selected"; ?> ><?php echo "Free Order"; ?></option>
<?php else: ?>
            <option value="0" ><?php echo "Select"; ?></option>
            <option value="2" <?php if ($this->payment_gateway == 2) echo "selected"; ?> ><?php echo "PayPal"; ?></option>
            
            <?php echo $otherOptionsString;?>            
            
            <option value="3" <?php if ($this->payment_gateway == 3) echo "selected"; ?> ><?php echo "By Cheque"; ?></option>
            <option value="4" <?php if ($this->payment_gateway == 4) echo "selected"; ?> ><?php echo "Pay at the Event"; ?></option>
            <option value="5" <?php if ($this->payment_gateway == 5) echo "selected"; ?> ><?php echo "Free Order"; ?></option>
<?php endif; ?>
        </select>
      </div>

      <div>
        <label><?php echo "Cheque No"; ?></label>
        <input type="text" name="cheque_no" id="cheque_no" value="<?php echo empty($this->cheque_no) ? "" : $this->cheque_no; ?>"/> 
      </div>

      <div style="margin-top:16px;">
        <button type="submit" name="search" ><?php echo "Search"; ?></button>
      </div>

    </form>
  </div>
</div>

<div class='admin_search'>
<?php echo $this->formFilter->render($this) ?>
</div>
<br />

  <?php if (!empty($this->order_approve_count) && $this->paymentToSiteadmin) : ?>
  <h3>
  <?php echo $this->translate("There %s awaiting your approval.", $this->translate(array('is %s ticket order', 'are %s ticket orders', $this->order_approve_count), '<a href="javascript:void(0); " onclick="findApprovalPendingOrders()"> ' . $this->order_approve_count . '</a>'), $this->translate(array('that', 'they', $this->order_approve_count))); ?>
  </h3>
<?php endif; ?>


<div class='admin_members_results'>
  <?php
  if (!empty($this->paginator)) {
    $counter = $this->paginator->getTotalItemCount();
  }
  if (!empty($counter)):
    ?>
    <div class="">
    <?php echo $this->translate(array('%s order found.', '%s orders found.', $counter), $this->locale()->toNumber($counter)) ?>
    </div>
      <?php else: ?>
    <div class="tip"><span>
    <?php echo "There are no orders available."; ?></span>
    </div>
<?php endif; ?> 
</div>
<br />

<?php
if (!empty($counter)):
  $orderTicketTable = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket');
  ?>
  <div style="overflow-x:auto;">
    <table class='admin_table seaocore_admin_table' width="100%">
      <thead>
        <tr>
          <?php $class = ( $this->order == 'order_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th style="width: 5%;" class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('order_id', 'DESC');"><?php echo 'Id'; ?></a></th>

          <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th style="width: 15%;" class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo 'Event Name'; ?></a></th>

  <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th style="width: 10%;" class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo 'Buyer'; ?></a></th>

          <th style="width: 6%;" class='admin_table_short admin_table_centered'><?php echo 'Ticket Qty'; ?></th>

          <?php $class = ( $this->order == 'grand_total' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th style="width: 10%;" class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('grand_total', 'DESC');"><?php echo 'Order Total'; ?></a></th>

  <?php $class = ( $this->order == 'commission_value' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th style="width: 10%;" class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('commission_value', 'DESC');"><?php echo 'Commission'; ?></a></th>

          <th style="width: 10%;" class='admin_table_short'><?php echo 'Status'; ?></th>
          <th style="width: 6%;" class='admin_table_short admin_table_centered'><?php echo 'Payment'; ?></th>

          <th style="width: 14%;" class='admin_table_short'><?php echo 'Purchased'; ?></th>
          <th style="width: 14%;" class='admin_table_short'><?php echo 'Options'; ?></th>
        </tr>	
      </thead>
        <?php foreach ($this->paginator as $item):
          $showPrintLinks = $item->showPrintLink();?>
        <tbody>
        <td>
          <?php
          $eventItem = $this->item('siteevent_event', $item->event_id);
          if (empty($eventItem)) :
            echo "#" . $item->order_id;
          else:
            $order_view_url = $this->url(array('action' => 'view', 'order_id' => $item->order_id, 'admin_calling' => 1), 'siteeventticket_order', false);
            echo $this->htmlLink($order_view_url, "#" . $item->order_id, array('target' => '_blank'));
          endif;
          ?>          
        </td>
        <?php if (empty($eventItem)): ?>
          <td><i>Event Deleted</i></td>
          <?php else: ?>
          <td><?php echo $this->htmlLink($eventItem->getHref(), $this->string()->truncate($this->string()->stripTags($item->getTitle()), 10), array('title' => $item->getTitle(), 'target' => '_blank')) ?></td>
          <?php endif; ?>
        <td>
          <?php
          if ($item->user_id) :
            echo $this->htmlLink($item->getOwner()->getHref(), $this->string()->truncate($this->string()->stripTags($item->getOwner()->getTitle()), 10), array('title' => $item->getOwner()->getTitle(), 'target' => '_blank'));
          endif;

          if ($item->order_status == 3) :
            $payment_status = 'marked as non-payment';
          elseif ($item->payment_status == 'active') :
            $payment_status = 'Yes';
          else:
            $payment_status = 'No';
          endif;
          ?>
        </td>
        <td class="admin_table_centered"><?php echo $this->locale()->toNumber($item->ticket_qty); ?></td>
        <?php
            if(!empty($item->coupon_detail)) {
                $coupon_details = unserialize($item->coupon_detail);   
                if(is_array($coupon_details)) {
                    foreach($coupon_details as $coupon_detail) {
                        $fixedDiscount = !empty($coupon_detail['coupon_type']) ? 1 : 0;
                        if($fixedDiscount) {
                            $item->grand_total -=  $coupon_detail['coupon_amount'];
                        }
                        break;
                    }    
                }
            }        
        ?>
        <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($item->grand_total); ?></td>
        <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($item->commission_value); ?></td>

        <td>
    <?php if ($item->order_status == 3) : ?>
            <i>-</i>
          <?php else: ?>
            <?php $tempStatus = $this->getTicketOrderStatus($item->order_status, false, true); ?>
            <div style="min-width:100px">
              <div class="<?php echo $tempStatus['class'] ?> fleft" id="current_order_status_<?php echo $item->order_id ?>"><i><?php echo $tempStatus['title']; ?></i></div></div>
          <?php endif; ?>
        </td>
        <td class="admin_table_centered"><?php echo $payment_status; ?></td>
        <td><?php echo $this->locale()->toDateTime($item->creation_date); ?></td>
        <td>
          <?php if (!empty($eventItem)): ?>
            <?php if (empty($item->direct_payment) && $item->order_status != 3) : ?>
              <?php
              if (($item->gateway_id == 3) && empty($item->order_status)) :
                echo '<font color="red"><a href="javascript:void(0)" onclick="Smoothbox.open(\'' . $this->url(array('action' => 'payment-approve', 'order_id' => $item->order_id)) . '\')">approve payment</a></font> | ';
              elseif ($item->order_status == 1) :
                echo '<font color="red"><a href="javascript:void(0)" onclick="Smoothbox.open(\'' . $this->url(array('action' => 'payment-approve', 'order_id' => $item->order_id, 'payment_pending' => 1)) . '\')">approve payment</a></font> | ';
              endif;
              ?>
            <?php elseif (!empty($item->direct_payment) && ($item->gateway_id == 3 && empty($item->order_status))): ?>
              <font class="seaocore_txt_light"><?php echo 'seller approval pending |'; ?></font>
            <?php endif; ?>
            <?php echo '<a href="javascript:void(0)" onclick="Smoothbox.open(\'' . $this->url(array('module' => 'siteeventticket', 'controller' => 'order', 'action' => 'detail', 'order_id' => $item->order_id), 'default', true) . '\')">details</a> '; ?>

            <?php if (!empty($eventItem)) : ?>
              <?php echo ' | ' . $this->htmlLink($order_view_url, 'view', array('target' => '_blank')) ?> 
            <?php endif; ?>
              <?php if ($showPrintLinks): ?>

                <?php echo ' | ' . $this->htmlLink($this->url(array('action' => 'print-invoice', 'order_id' => Engine_Api::_()->siteeventticket()->getDecodeToEncode($item->order_id)), 'siteeventticket_order', true), 'print invoice', array('target' => '_blank')) ?> 
<!--                | <?php $tempPrint = $this->url(array('action' => 'print-ticket', 'order_id' => Engine_Api::_()->siteeventticket()->getDecodeToEncode($item->order_id)), 'siteeventticket_order', true); ?><a href="<?php echo $tempPrint; ?>" target="_blank"><?php echo "print tickets"; ?></a>-->
                <?php if($this->showSendTicketLink): ?>
                    <?php echo ' | ' . $this->htmlLink($this->url(array('action' => 'send-email', 'adminCall' => 1, 'order_id' => Engine_Api::_()->siteeventticket()->getDecodeToEncode($item->order_id)), 'siteeventticket_order', true), 'Send Tickets', array('class' => 'smoothbox')) ?><?php endif; ?> 
              <?php endif; ?>
      <?php else: ?>
            -
      <?php endif; ?>
        </td>
        </tbody>
    <?php endforeach; ?>
    </table>
  </div>
  <div class="clr mtop10">
  <?php
  echo $this->paginationControl($this->paginator, null, null, array(
   'pageAsQuery' => true,
   'query' => $this->formValues,
  ));
  ?>
  </div>
<?php endif; ?>