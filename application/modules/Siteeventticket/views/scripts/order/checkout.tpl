<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: checkout.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
// IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
if (!empty($this->siteeventticket_checkout_no_payment_gateway_enable)):
  ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There is no payment gateway enable by the site administrator. So you can't process for checkout process. Please contact to site administrator."); ?>
    </span>
  </div> 
  <?php
  return;
endif;
?>

<?php
// IF NO PAYMENT GATEWAY ENABLE BY THE SELLER
if (!empty($this->siteeventticket_checkout_event_no_payment_gateway_enable)):
  ?>
  <div class="tip">
    <span>
      <?php
      echo $this->translate("Payment gateway is not enabled by Event Owner. Please contact the respective event owner to complete your purchase. Thanks for your patience!");
      ?>

    </span>
  </div> 
  <?php
  return;
endif;
?> 
<div class="generic_layout_container layout_middle">
    <section class="siteeventticket_checkout_process_form">
    <div>
      <h3 class="siteeventticket_checkout_process_normal mbot10">
        <?php echo $this->translate('Payment Method'); ?>
      </h3>
    </div>
    <?php if ($this->ticketUnavailabilityMessage) : ?>
      <div class="tip">
        <span><?php echo $this->translate("Sorry, The tickets you have selected are not available. Please go back and make your selection again according to the availability.") ?></span>
      </div>
      <div class="m10">
        <button type="button" name="place_order" onclick="window.location.href = '<?php echo $this->url(array("action" => "buy", 'event_id' => $this->event_id), "siteeventticket_ticket", true) ?>';" class="fright"><?php echo $this->translate("&laquo; Back") ?></button>
      </div>
      <?php
      return;
    endif;
    ?>
    <?php
    $isNotAllowedOnlinePayment = !empty($this->isNotAllowedOnlinePayment) ? $this->isNotAllowedOnlinePayment : null;
    $temp_online_gateway = false;
    $checkout_process = @unserialize($this->checkout_process);
    ?>
      <?php $base_url = $this->layout()->staticBaseUrl; ?>
    <div class="siteeventticket_payment_methods_wrap">
<?php if (!empty($this->totalOrderPriceFree)) : ?>
        <div>
          <input type="radio" id="free_order" name="payment_method" value="5" checked="checked" style="display: none;" />
          <div for="free_order">
            <span><?php echo $this->translate("Free Order") ?></span>
          </div>
          <input type="hidden" id="payment_gateway_name_5" value="<?php echo $this->translate('Free Order') ?>" />
        </div>
      <?php else: ?>
        <?php 
            $paymentMethodCount = COUNT($this->payment_gateway);
            $handTip = '';
            if($paymentMethodCount == 1) {
                $handTip = 'style="cursor:default;"';
            }
        ?>
        <?php
        $otherPaymentGateways = array();
        if (!isset($this->payment_gateway)):
          return;
        else:
          foreach ($this->payment_gateway as $payment_method) :
            if (count($this->payment_gateway) == 1 && empty($this->by_cheque_enable) && empty($this->cod_enable)):
              $selected = "checked = checked style='display:none'";
            else:
              $selected = "";
            endif;


            if ((isset($payment_method['plugin']) && $payment_method['plugin'] === 'Payment_Plugin_Gateway_2Checkout') || ($payment_method == '2checkout')):
              $temp_online_gateway = true;

              if (empty($isNotAllowedOnlinePayment)) {
                $payment_method['title'] = $this->translate("2Checkout");
                $twoCheckoutEnable = true;
                echo '<div class="siteeventticket_payment_method"><input type="radio" id="2checkout" name="payment_method" value="1" ' . $selected . ' onchange="paymentMethod(this.value)"><label '.$handTip.' for="2checkout" class="mbot5"><span>' . $this->translate("2Checkout&nbsp;&nbsp;") . '</span><img src="' . $base_url . 'application/modules/Siteeventticket/externals/images/2-checkout.png" title="' . $payment_method['title'] . '" /></label>';
                echo '<input type="hidden" id="payment_gateway_name_1" value="' . $payment_method['title'] . '" >
      <span id="payment_method_message_1" class="mbot5 pleft10">&nbsp;&nbsp;' . $this->translate("You will be redirected to %s to make payment for your order.", $payment_method['title']) . '</span></div>';
              }
            endif;

            if ((isset($payment_method['plugin']) && $payment_method['plugin'] === 'Payment_Plugin_Gateway_PayPal') || ($payment_method == 'paypal')):
              $temp_online_gateway = true;

              if (empty($isNotAllowedOnlinePayment)) {
                $paypalEnable = true;
                echo '<div class="siteeventticket_payment_method"><input id="paypal" type="radio" name="payment_method" value="2" ' . $selected . ' onchange="paymentMethod(this.value)"><label '.$handTip.' for="paypal" class="mbot5"><span>' . $this->translate("PayPal&nbsp;&nbsp;") . '</span><img src="' . $base_url . 'application/modules/Siteeventticket/externals/images/paypal.png" title="PayPal" /></label>';
                echo '<input type="hidden" id="payment_gateway_name_2" value="PayPal" >
       <span id="payment_method_message_2" class="mbot5 pleft10">&nbsp;&nbsp;' . $this->translate("You will be redirected to PayPal to make payment for your order.") . '</span></div>';
              }
            endif;
            
            if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
                if ((isset($payment_method['plugin']) && strstr($payment_method['plugin'], 'Sitegateway_Plugin_Gateway_')) || Engine_Api::_()->sitegateway()->isValidGateway($payment_method)) {
                  if(is_array($payment_method)) {
                      $pluginName = $payment_method['plugin'];
                  }
                  else {
                      $pluginName = 'Sitegateway_Plugin_Gateway_'.$payment_method;
                  }
                  
                  Engine_Api::_()->sitegateway()->getGatewayColumn(array('pluginLike' => 'Sitegateway_Plugin_Gateway_', 'columnName' => 'gateway_id', 'gateway_id' => $gateway_id));
                    
                  $temp_online_gateway = true;
                  $paymentGateway = Engine_Api::_()->sitegateway()->getGatewayColumn(array('fetchRow' => true, 'plugin' => $pluginName));
                  $otherPaymentGateways[] = $paymentGatewayId = $paymentGateway->gateway_id;
                  $paymentGatewayTitle = strtolower($paymentGateway->title);
                  $paymentGatewayTitleUC = ucfirst($paymentGateway->title);

                  if (empty($isNotAllowedOnlinePayment)) {
                    $otherGatewayEnabled = true;
                    echo '<div class="siteeventticket_payment_method"><input id="'.$paymentGatewayTitle.'" type="radio" name="payment_method" value="'.$paymentGatewayId.'" ' . $selected . ' onchange="paymentMethod(this.value)"><label '.$handTip.' for="'.$paymentGatewayTitle.'" class="mbot5"><span>' . $this->translate($paymentGatewayTitleUC) . '&nbsp;&nbsp;</span><img src="' . $base_url . 'application/modules/Sitegateway/externals/images/'.$paymentGatewayTitle.'.png" title="'.$paymentGatewayTitleUC.'" /></label>';
                    echo '<input type="hidden" id="payment_gateway_name_'.$paymentGatewayId.'" value="'.$paymentGatewayTitleUC.'"><span id="payment_method_message_'.$paymentGatewayId.'" class="mbot5 pleft10">&nbsp;&nbsp;' . $this->translate("You will be able to securely make payment for your order using %s.", $paymentGatewayTitleUC) . '</span></div>';
                  }
                }               
            }

            if ($payment_method == 'cheque') :
              $byChequeEnable = true;
            endif;

            if ($payment_method == 'cod') :
              $codEnable = true;
            endif;
          endforeach;


          if (!empty($this->cod_enable) || !empty($codEnable)) :
            if (empty($twoCheckoutEnable) && empty($paypalEnable) && empty($otherGatewayEnabled) && (empty($this->by_cheque_enable) && empty($byChequeEnable))) :
              $showSelected = "checked = checked style='display:none'";
            else:
              $showSelected = "";
            endif;
            echo '<div class="siteeventticket_payment_method"><input type="radio" id="cod" name="payment_method" value="4" ' . $showSelected . ' onchange="paymentMethod(this.value);" /><label '.$handTip.' for="cod"><span>' . $this->translate("Pay at the Event&nbsp;&nbsp;") . '</span><img src="' . $base_url . 'application/modules/Siteeventticket/externals/images/cod.png" title="' . $this->translate('Pay at the Event') . '" /></label>';
            echo '<input type="hidden" id="payment_gateway_name_4" value="' . $this->translate('Pay at the Event') . '" ></div>';
            $temp_payment_gateway_flag = true;
          endif;

          if (!empty($this->by_cheque_enable) || !empty($byChequeEnable)):
            if (empty($twoCheckoutEnable) && empty($paypalEnable) && empty($otherGatewayEnabled) && (empty($this->cod_enable) && empty($codEnable))) :
              $showSelected = "checked = checked style='display:none'";
            else:
              $showSelected = "";
            endif;
            echo '<div class="siteeventticket_payment_method"><input type="radio" id="bycheque" name="payment_method" value="3" ' . $showSelected . ' onchange="paymentMethod(this.value)" /><label '.$handTip.' for="bycheque"><span>' . $this->translate("By Cheque&nbsp;&nbsp;") . '</span><img src="' . $base_url . 'application/modules/Siteeventticket/externals/images/check.png" title="' . $this->translate('By Cheque') . '" /></label>';
            echo '<input type="hidden" id="payment_gateway_name_3" value="' . $this->translate('By Cheque') . '" ></div>';
            $temp_payment_gateway_flag = true;
          endif;
        endif;
      endif;
      ?>

      <div id="cheque">
        <?php if (!empty($this->admin_cheque_detail) || (!empty($this->event_id) && empty($this->isPaymentToSiteEnable) && !empty($this->eventChequeDetail) )) : ?>
          <div class="siteeventticket_payment_l fleft"><b><?php echo $this->translate("Send Cheque to: ") ?></b></div>
          <?php if (!empty($this->admin_cheque_detail)) : ?>
            <div class="fleft"><pre><?php echo $this->admin_cheque_detail ?></pre></div><br/><br/>
          <?php elseif (!empty($this->event_id) && empty($this->isPaymentToSiteEnable) && !empty($this->eventChequeDetail)): ?>
                        <div class="fleft"><pre><?php echo $this->eventChequeDetail ?></pre></div><br/><br/>
          <?php endif; ?>
        <?php endif; ?>

    <div class="mbot10 mleft10">
        <?php echo $this->translate("Please enter the information for your payment.") ?>
    </div>
    <div class="mbot10">
    	<div class="siteeventticket_payment_l fleft"><?php echo $this->translate("Cheque No") ?></div>
      <input type="text" id="cheque_no" onkeyup="chequeInfo(this.id)">
      <span id="cheque_no_missing" class="seaocore_txt_red f_small"></span>
    </div>
  	<div class="mbot10">
    	<div class="siteeventticket_payment_l fleft"><?php echo $this->translate("Account Holder Name") ?></div>
      <input type="text" id="signature" onkeyup="chequeInfo(this.id)">
    	<span id="signature_missing" class="seaocore_txt_red f_small"></span>
    </div>
    <div class="mbot10">
    	<div class="siteeventticket_payment_l fleft"><?php echo $this->translate("Account Number") ?></div>
      <input type="text" id="account_no" onkeyup="chequeInfo(this.id)">
    	<span id="account_no_missing" class="seaocore_txt_red f_small"></span>
    </div>
    <div class="mbot10">
    	<div class="siteeventticket_payment_l fleft"><?php echo $this->translate("Bank Routing Number") ?></div>
      <input type="text" id="routing_no" onkeyup="chequeInfo(this.id)">
    	<span id="routing_no_missing" class="seaocore_txt_red f_small"></span>
    </div>
  </div>
      <?php
      if (count($this->payment_gateway) == 1 && !empty($temp_online_gateway) && $isNotAllowedOnlinePayment && empty($temp_payment_gateway_flag)):
        echo '<div class ="siteeventticket_alert_msg b_medium mbot10"><span><b>' . $this->translate('NO PAYMENT METHODS AVAILABLE.') . '</b></span></div>';
        $hideContinue = true;
      endif;

      if (!empty($temp_online_gateway) && !empty($isNotAllowedOnlinePayment)):
        //REMAINING
        $temp_buy_ticket_url = $this->url(array('action' => 'buy', 'event_id' => $this->event_id), 'siteeventticket_ticket', true);
        echo '<div class ="seaocore_txt_red">' . $this->translate('Your purchasable amount exceeds the Threshold Amount, Please %1$sclick here%2$s to enable online transaction option, by managing your ordered amount.', '<a href="' . $temp_buy_ticket_url . '">', '</a>') . '</div>';
      endif;
      ?>
  <div> <span id="payment_method_missing" class="seaocore_txt_red f_small"></span> </div>
</div>
<div class="clr">
<div id="checkout_place_order_error"></div>
<div class='buttons'>
        <?php
        $isPlaceOrderActivityEnabled = Engine_Api::_()->getDbTable('actionTypes', 'activity')->getActionType("siteeventticket_order_place");

        if (!empty($isPlaceOrderActivityEnabled) && !empty($isPlaceOrderActivityEnabled->enabled)):
          ?>
      <span class="clr dblock mtop10" style="display:none" >
        <input type="checkbox" id="isPrivateOrder" name="isPrivateOrder" checked="checked"><label for="isPrivateOrder"><?php echo $this->translate("Make my purchase private.") ?></label>
      </span>
        <?php endif;
        ?>
  <div class="m10 fleft">
    
    <button type="button" name="place_order" onclick="window.location.href = '<?php echo $this->url(array("action" => "buy", 'event_id' => $this->event_id), "siteeventticket_ticket", true) ?>';" class="fright"><?php echo $this->translate("&laquo; Back") ?></button>
  </div>    
  <div class="fright m10">  
        <button type="button" name="place_order" onclick="paymentInformation()" class="fright"><?php echo $this->translate("Place Order") ?></button>
        <div id="loading_image_5" class="fright m10" style="display: inline-block;"></div>
  </div>
 	<div id="loading_image_4" class="fright mtop10 ptop10" style="display: inline-block;"></div>
</div>
</div>
</section>
</div>

<script>
  var otherPaymentGateways = '<?php echo (!empty($otherPaymentGateways) ? json_encode($otherPaymentGateways) : '[]'); ?>';
  if ($('payment_method_message_1'))
    new Fx.Slide('payment_method_message_1').hide();
  if ($('payment_method_message_2'))
    new Fx.Slide('payment_method_message_2').hide();
  
  var index;
  for(index = 0; index < otherPaymentGateways.length; index++) {
    if ($('payment_method_message_'+otherPaymentGateways[index]))
      new Fx.Slide('payment_method_message_'+otherPaymentGateways[index]).hide();
  }

  if ($('cheque'))
    new Fx.Slide('cheque').hide();


  if ($$('input[name=payment_method]:checked').get('value'))
    paymentMethod($$('input[name=payment_method]:checked').get('value'));

//If user fill the required field of cheque method then this function will remove the error message
  function chequeInfo(id)
  {
    $(id + '_missing').innerHTML = '';
  }

  function paymentMethod(payment_method_value)
  {
    $('payment_method_missing').innerHTML = '';

    if (payment_method_value == 1)
      new Fx.Slide('payment_method_message_1', {mode: 'vertical', resetHeight: true}).slideIn().toggle();
    else if (document.getElementById('payment_method_message_1'))
      new Fx.Slide('payment_method_message_1').slideOut().toggle();

    if (payment_method_value == 2)
      new Fx.Slide('payment_method_message_2', {mode: 'vertical', resetHeight: true}).slideIn().toggle();
    else if (document.getElementById('payment_method_message_2'))
      new Fx.Slide('payment_method_message_2').slideOut().toggle();
  
    var index;
    for(index = 0; index < otherPaymentGateways.length; index++) {
        if (payment_method_value == otherPaymentGateways[index])
          new Fx.Slide('payment_method_message_'+otherPaymentGateways[index], {mode: 'vertical', resetHeight: true}).slideIn().toggle();
        else if (document.getElementById('payment_method_message_'+otherPaymentGateways[index]))
          new Fx.Slide('payment_method_message_'+otherPaymentGateways[index]).slideOut().toggle();  
    }
    
    if (payment_method_value == 3)
      new Fx.Slide('cheque', {resetHeight: true}).toggle();
    else
      new Fx.Slide('cheque').slideOut().toggle();
  }

  function paymentInformation()
  {
    var payment_method = $$('input[name=payment_method]:checked').get('value');

    //If viewer not select any payment method then show error message.
    if (payment_method.length == 0)
    {
      $('payment_method_missing').innerHTML = '<?php echo $this->translate("Please choose a payment method.") ?>';
      return;
    }
    var checkout_process_payment_gateway = '<?php echo $this->translate("Gateway: ") ?>' + $('payment_gateway_name_' + payment_method).value + '<br />';
    if (payment_method == 3)
    {
      var cheque_info_missing = 0;
      if ($('cheque_no').value == "")
      {
        $('cheque_no_missing').innerHTML = '<?php echo $this->translate("Enter Cheque No") ?>';
        cheque_info_missing = 1;
      }
      if ($('signature').value == "")
      {
        $('signature_missing').innerHTML = '<?php echo $this->translate("Enter Account Holder Name") ?>';
        cheque_info_missing = 1;
      }
      if ($('account_no').value == "")
      {
        $('account_no_missing').innerHTML = '<?php echo $this->translate("Enter Account Number") ?>';
        cheque_info_missing = 1;
      }
      if ($('routing_no').value == "")
      {
        $('routing_no_missing').innerHTML = '<?php echo $this->translate("Enter Bank Routing Number") ?>';
        cheque_info_missing = 1;
      }

      if (cheque_info_missing == 1)
      {
        return;
      }
      else
      {
        checkout_process_payment_gateway += '<?php echo $this->translate("Cheque No: ") ?>' + $('cheque_no').value + '<br />' + '<?php echo $this->translate("Account Holder: ") ?>' + $('signature').value + '<br />' + '<?php echo $this->translate("Account No: ") ?>' + $('account_no').value + '<br />' + '<?php echo $this->translate("Routing No: ") ?>' + $('routing_no').value;
        payment_method += ',' + $('cheque_no').value + ',' + $('signature').value + ',' + $('account_no').value + ',' + $('routing_no').value;
      }
    }
    siteeventticket_checkout_process_payment_information = checkout_process_payment_gateway;

    placeOrder(String(payment_method));
  }

  function placeOrder(param)
  {
    var placeOrderUrl;
    placeOrderUrl = "siteeventticket/order/place-order/event_id/<?php echo $this->event_id ?>";

    var isPrivateOrder = 0;
    if ($('isPrivateOrder') && $('isPrivateOrder').checked)
      isPrivateOrder = 1;
    else if (!$('isPrivateOrder'))
      isPrivateOrder = 1;

    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + placeOrderUrl,
      method: 'POST',
      onRequest: function () {
        $('loading_image_5').innerHTML = '<img src=' + en4.core.staticBaseUrl + 'application/modules/Siteeventticket/externals/images/loading.gif height=15 width=15>';
      },
      data: {
        format: 'json',
        checkout_process: '<?php echo serialize($checkout_process); ?>',
        param: param,
        formValues: <?php echo json_encode($this->formValues); ?>,
        isPrivateOrder: isPrivateOrder
      },
      onSuccess: function (responseJSON)
      { 
        $('loading_image_5').innerHTML = '';
        if (responseJSON.checkout_place_order_error)
        {
          $('checkout_place_order_error').innerHTML = responseJSON.checkout_place_order_error;
          return;
        }
        if (responseJSON.gateway_id == 1 || responseJSON.gateway_id == 2 || otherPaymentGateways.contains(responseJSON.gateway_id))
        {

<?php $payment_url = $this->url(array('action' => 'payment', 'event_id' => $this->event_id), 'siteeventticket_order', true) ?>
          window.location = '<?php echo $payment_url ?>/gateway_id/' + responseJSON.gateway_id + '/occurrence_id/' + responseJSON.checkoutOccurrenceId + '/order_id/' + responseJSON.order_id;
        }
        else
        {
<?php $success_url = $this->url(array('action' => 'success', 'event_id' => $this->event_id), 'siteeventticket_order', true) ?>
          window.location = '<?php echo $success_url ?>/success_id/' + responseJSON.order_id;
        }
      }
    })
            );
  }

</script>