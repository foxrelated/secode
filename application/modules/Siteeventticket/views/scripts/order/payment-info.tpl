<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: payment-info.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (!$this->only_list_content): ?>
  <?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
  <div class="siteevent_dashboard_content">
    <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
    <div class="siteevent_event_form">
      <!--<div class="global_form">--> 
    <?php endif; ?> 
    <?php if (empty($this->authenticationSuccess)) : ?> 
      <div id="payment_info_detail" class="p5 siteeventticket_dashboard_payment">
        <?php //if( empty($this->passwordError) ) : ?>
        <div class="mbot10">
          <?php echo $this->translate("Enter Account Password:") ?>
        </div>
        <div class="mbot10">
          <input type="password" id="payment_info_password" size="25" />
          <div class="clr seaocore_txt_light f_small mtop5">
            <?php echo $this->translate("Please enter the password for your account on this site to proceed.") ?>
          </div>
          <div id="payment_info_empty_password" class="mtop5 seaocore_txt_red" style="display: none">
            <?php echo $this->translate("Please enter the password.") ?>
          </div>
          <div id="payment_info_password_error" class="mtop5 seaocore_txt_red" style="display: none">
            <?php echo $this->translate("The passwords you entered did not match.") ?>
          </div>
        </div>
        <div class='buttons'>
          <button type='button' id="button" onclick="paymentInfoPassword();"><?php echo $this->translate("Submit") ?></button>
          <div id="payment_info_spinner" style="display: inline-block;"></div>
        </div>
        <?php //endif; ?>
      </div>

      <script type="text/javascript">
        function paymentInfoPassword()
        {
          document.getElementById("payment_info_empty_password").style.display = 'none';
          document.getElementById("payment_info_password_error").style.display = 'none';

          if (document.getElementById("payment_info_password").value == '')
          {
            document.getElementById("payment_info_empty_password").style.display = 'block';
            return;
          }

          en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'siteeventticket/order/payment-info',
            method: 'POST',
            onRequest: function () {
              document.getElementById("payment_info_spinner").innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Siteeventticket/externals/images/loading.gif" />';
            },
            data:
                    {
                      password: document.getElementById('payment_info_password').value,
                      event_id: '<?php echo $this->event_id ?>'
                    },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
              document.getElementById("payment_info_spinner").innerHTML = '';
              if (responseHTML == 'payment_info_password_error')
                document.getElementById("payment_info_password_error").style.display = 'block';
              else
                document.getElementById("payment_info_detail").innerHTML = responseHTML;
            }
          }));
        }
      </script>
    <?php else: ?>

      <?php if (1): ?>
        <?php if (empty($this->enablePaymentGateway)) : ?>
          <div> 
            <?php if($this->paymentMethod == 'paypal'): ?>  
                <?php echo $this->form->render($this) ?>
              
                <?php if(Engine_Api::_()->hasModuleBootstrap('sitegateway')): ?>
                    <?php if($this->showStripeConnect): ?>
                        <?php echo $this->partial('_stripeConnectButton.tpl','sitegateway',array('stripeConnected' => $this->stripeConnected, 'show_stripe_form_massges' => false, 'resource_type' => 'siteevent_event', 'resource_id' => $this->event_id));?>
                    <?php endif; ?>              
                <?php endif; ?>    
              
            <?php elseif(Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($this->paymentMethod)): ?>
              
                <?php if($this->paymentMethod == 'stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)): ?>
                    <?php echo $this->partial('_stripeConnectButton.tpl','sitegateway',array('stripeConnected' => $this->stripeConnected, 'show_stripe_form_massges' => false, 'resource_type' => 'siteevent_event', 'resource_id' => $this->event_id));?>
                <?php else: ?>
                    <?php $formName = "form".ucfirst($this->paymentMethod);?>
                    <?php echo $this->$formName->render($this) ?>
                <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <?php if (count($this->enablePaymentGateway) > 1) : ?>
            <h3><?php echo $this->translate('Payment Methods') ?></h3>
            <p class="mtop5"><?php echo $this->translate("Below, you can choose the payment methods that you want to be available to buyers during their checkout process to purchase tickets of this event."); ?></p>
          <?php else: ?>
            <h3><?php echo $this->translate('Payment Method') ?></h3>
          <?php endif; ?>
          <br/>
          <div id="event_payment_gateway" class="mbot10 siteevent_seller_payment_options">
            <div id="event_payment_gateway_success_message"></div>
            <?php foreach ($this->enablePaymentGateway as $paymentGateway) : ?>

              <?php if ($paymentGateway == 'paypal') : ?>
                <?php $isPaypalEnable = true; ?>
                <input type="checkbox" id="siteevent_gateway_paypal" onchange="selectEventPaymentGateway(this.id, this.checked);" <?php
                if (!empty($this->paypalEnable)) : echo "checked";
                endif;
                ?>>
                <label for="siteevent_gateway_paypal"><?php echo $this->translate("PayPal") ?></label><br/>
                <div id="siteevent_gateway_paypal_form" class="siteeventticket_dashboard_payment_method b_medium" <?php
                     if (empty($this->paypalEnable)) : echo 'style="display:none"';
                     endif;
                     ?>> 
                <?php echo $this->form->render($this) ?><br/>
                </div>
              <?php endif; ?>
                
              <?php if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)): ?>
                <?php   
                    $gatewayName = strtolower($paymentGateway);
                    $gatewayNameUC = ucfirst($paymentGateway);
                    $gatewayVariableName = "is".$gatewayNameUC."Enable";
                    $formName = "form$gatewayNameUC";
                    $gatewyEnabled = $gatewayName.'Enabled';
                ?>
                <?php $gatewayVariableName = true; ?>
                <input type="checkbox" id="siteevent_gateway_<?php echo $gatewayName;?>" onchange="selectEventPaymentGateway(this.id, this.checked);" <?php
                
                if (!empty($this->$gatewyEnabled)) : echo "checked";
                endif;
                
                if($paymentGateway == 'stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0) && !empty($this->showStripeConnectChecked)) {
                    echo "checked";
                    $checkedStripeConnect = true;
                }
                
                ?>>
                <label for="siteevent_gateway_<?php echo $gatewayName;?>"><?php echo $this->translate($gatewayNameUC) ?></label><br/>
                <div id="siteevent_gateway_<?php echo $gatewayName;?>_form" class="siteeventticket_dashboard_payment_method b_medium" <?php if (empty($this->stripeEnabled)) : echo 'style="display:none"';endif;?> > 
                    <?php if($paymentGateway == 'stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)): ?>
                        <?php echo $this->partial('_stripeConnectButton.tpl','sitegateway',array('stripeConnected' => $this->stripeConnected, 'show_stripe_form_massges' => true, 'resource_type' => 'siteevent_event', 'resource_id' => $this->event_id));?>
                    <?php else: ?>
                        <?php echo $this->$formName->render($this) ?><br/>
                    <?php endif; ?>   
                </div>
              <?php endif; ?>                

              <?php if ($paymentGateway == 'cheque') : ?>
                <?php $isByChequeEnable = true; ?>
                <input type="checkbox" id="siteevent_gateway_by_cheque" onchange="selectEventPaymentGateway(this.id, this.checked);" <?php
          if (!empty($this->bychequeEnable)) : echo 'checked';
          endif;
                ?>>
                <label for="siteevent_gateway_by_cheque"><?php echo $this->translate("By Cheque") ?></label><br/>
                <div id="siteevent_gateway_by_cheque_form" class="siteeventticket_dashboard_payment_method b_medium mbot5" <?php
           if (empty($this->bychequeEnable)) : echo 'style="display:none"';
           endif;
           ?>>
                  <div id="show_bycheque_error"></div>
                  <p>
                    <?php echo $this->translate('Enter your bank account details which buyers will fill in the cheques for making payments for their orders. This information will be shown when buyers choose "By Cheque" method in the "Payment Information" section during their checkout process.') ?>
                  </p>
                  <textarea id="event_bycheque_detail" rows="5" class="clr dblock mtop10" escape="false"><?php
                    if (!empty($this->bychequeDetail)) : echo trim($this->bychequeDetail);
                    else: 
                           $this->translate("Account Name") . ":" . 
                           $this->translate("Account No.") . ":" . 
                           $this->translate("Bank") . ":" . 
                           $this->translate("Bank Branch Address") . ":" ;
                    endif;
                    ?></textarea>
                </div>
              <?php endif; ?>

                     <?php if ($paymentGateway == 'cod') : ?>
                <?php $isCodEnable = true; ?>
                <input type="checkbox" id="siteevent_gateway_cod" onchange="checkEnableGateway();" <?php
                if ($this->codEnable) : echo 'checked';
                endif;
                ?>>
                <label for="siteevent_gateway_cod"><?php echo $this->translate("Pay at the Event") ?></label><br/>
        <?php endif; ?>

      <?php endforeach; ?>
          </div>

          <div class='buttons' id="event_gateway_submit" style="display: none;">
            <button type='button' name="save_gateway" onclick="saveEventGateway();"><?php echo $this->translate("Save") ?></button>
            <span id="event_gateway_submit_spinner"></span>
          </div>
    <?php endif; ?>

        <script type="text/javascript">
    <?php if (!empty($this->enablePaymentGateway)) : ?>
            function selectEventPaymentGateway(elementId, elementValue)
            {
              if (elementValue)
                $(elementId + "_form").style.display = 'block';
              else
                $(elementId + "_form").style.display = 'none';

              checkEnableGateway();
            }

            function checkEnableGateway()
            {
              var showSubmitButton = false;
      <?php if (!empty($isPaypalEnable)) : ?>
                if ($("siteevent_gateway_paypal") && $("siteevent_gateway_paypal").checked)
                  showSubmitButton = true;
      <?php endif; ?>
          
      <?php foreach ($this->enablePaymentGateway as $paymentGateway): ?>
        <?php if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)):?>  
            <?php
                $gatewayName = strtolower($paymentGateway);
                $gatewayNameUC = ucfirst($paymentGateway);
                $gatewayVariableName = "is".$gatewayNameUC."Enable";        
            ?>  
            <?php if (!empty($gatewayVariableName)) : ?>
                if ($("siteevent_gateway_<?php echo $gatewayName; ?>") && $("siteevent_gateway_<?php echo $gatewayName; ?>").checked)
                  showSubmitButton = true;
            <?php endif; ?>          
        <?php endif; ?>    
      <?php endforeach; ?>    
       
      <?php if (!empty($isByChequeEnable)) : ?>
                if ($("siteevent_gateway_by_cheque") && $("siteevent_gateway_by_cheque").checked)
                  showSubmitButton = true;
      <?php endif; ?>
      <?php if (!empty($isCodEnable)) : ?>
                if ($("siteevent_gateway_cod") && $("siteevent_gateway_cod").checked)
                  showSubmitButton = true;
      <?php endif; ?>

              if (showSubmitButton)
                $("event_gateway_submit").style.display = 'block';
              else
                $("event_gateway_submit").style.display = 'none';
            }

            function saveEventGateway()
            {
              var TempStr = '';
              var display_error = true;
              var paypalGatewayDetail = '';
              var bychequeGatewayDetail = '';
              var codGatewayDetail = '';
              var isPaypalChecked = false;
              var isByChequeChecked = false;
              var isCodChecked = false;
              
            var additionalGateways = new Array();  
            var additionalGatewaysCheckedArray = {};
            var additionalGatewayDetailArray = {};
            <?php foreach ($this->enablePaymentGateway as $paymentGateway) : ?>
                <?php if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)):?>    
                    <?php
                        $gatewayName = strtolower($paymentGateway);
                        $gatewayNameUC = ucfirst($paymentGateway);
                        $gatewayVariableName = "is".$gatewayNameUC."Enable";        
                    ?>             
                    var gatewayName = '<?php echo $gatewayName?>';
                    var gatewayNameUC = '<?php echo $gatewayNameUC ?>';

                    window['<?php echo $gatewayName?>'+ 'TempStr'] = '';
                    window['<?php echo $gatewayName?>'+ 'GatewayDetail'] = '';
                    window['is' +'<?php echo $gatewayNameUC?>'+ 'Checked'] = false;

                    <?php if (!empty($gatewayVariableName)) : ?>
                        if ($("siteevent_gateway_<?php echo $gatewayName; ?>") && $("siteevent_gateway_<?php echo $gatewayName; ?>").checked) {
                          if($("siteeventticket_payment_info_<?php echo $gatewayName; ?>")) {  
                              
                            window['<?php echo $gatewayName?>'+'GatewayDetail'] = $("siteeventticket_payment_info_<?php echo $gatewayName; ?>").toQueryString();
                            
                            additionalGatewayDetailArray['<?php echo $gatewayName?>'+'GatewayDetail'] = $("siteeventticket_payment_info_<?php echo $gatewayName; ?>").toQueryString();
                            
                          }
                          
                          window['is'+'<?php echo $gatewayNameUC; ?>'+'Checked'] = $("siteevent_gateway_<?php echo $gatewayName; ?>").checked;
                          additionalGatewaysCheckedArray['is'+'<?php echo $gatewayNameUC; ?>'+'Checked'] = $("siteevent_gateway_<?php echo $gatewayName; ?>").checked;
                        }
                    <?php endif; ?>
                    
                    if(($("siteevent_gateway_<?php echo $gatewayName; ?>") && !$("siteevent_gateway_<?php echo $gatewayName; ?>").checked)) {
                        additionalGateways.push("1");
                    }
                    else {
                        additionalGateways.push("0");
                    }                    
                    
                <?php endif; ?>
            <?php endforeach; ?>   

      <?php if (!empty($isPaypalEnable)) : ?>
                if ($("siteevent_gateway_paypal") && $("siteevent_gateway_paypal").checked) {
                  paypalGatewayDetail = $("siteeventticket_payment_info").toQueryString();
                  isPaypalChecked = $("siteevent_gateway_paypal").checked;
                }
      <?php endif; ?>

      <?php if (!empty($isByChequeEnable)) : ?>
                if ($("siteevent_gateway_by_cheque") && $("siteevent_gateway_by_cheque").checked) {
                  bychequeGatewayDetail = $("event_bycheque_detail").value;
                  isByChequeChecked = $("siteevent_gateway_by_cheque").checked;
                }
      <?php endif; ?>

      <?php if (!empty($isCodEnable)) : ?>
                if ($("siteevent_gateway_cod") && $("siteevent_gateway_cod").checked) {
                  isCodChecked = $("siteevent_gateway_cod").checked;
                }
      <?php endif; ?>

              if ((additionalGateways.contains(0)) && ($("siteevent_gateway_paypal") && !$("siteevent_gateway_paypal").checked) && ($("siteevent_gateway_by_cheque") && !$("siteevent_gateway_by_cheque").checked) && ($("siteevent_gateway_cod") && !$("siteevent_gateway_cod").checked))
                return;

              en4.core.request.send(new Request.JSON({
                url: en4.core.baseUrl + 'siteeventticket/order/set-event-gateway-info',
                method: 'POST',
                data: {
                  format: 'json',
                  isPaypalChecked: isPaypalChecked,
                  isByChequeChecked: isByChequeChecked,
                  isCodChecked: isCodChecked,
                  paypalGatewayDetail: paypalGatewayDetail,
                  additionalGatewaysCheckedArray: additionalGatewaysCheckedArray,
                  additionalGatewayDetailArray: additionalGatewayDetailArray, 
                  bychequeGatewayDetail: bychequeGatewayDetail,
                  event_id: <?php echo $this->event_id ?>
                },
                onRequest: function () {
                  $('event_gateway_submit_spinner').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Siteeventticket/externals/images/loading.gif" />';
      <?php if (!empty($isPaypalEnable)) : ?>
                    document.getElementById('show_paypal_form_massges').innerHTML = '';
      <?php endif; ?>
          
                    <?php foreach ($this->enablePaymentGateway as $paymentGateway) : ?>
                        <?php if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)):?>    
                            <?php
                                $gatewayName = strtolower($paymentGateway);
                                $gatewayVariableName = "is".$gatewayNameUC."Enable";        
                            ?>
                            <?php if (!empty($gatewayVariableName)) : ?>
                                  if(document.getElementById('show_<?php echo $gatewayName;?>_form_massges')) {
                                      document.getElementById('show_<?php echo $gatewayName;?>_form_massges').innerHTML = '';
                                  }
                            <?php endif; ?> 
                        <?php endif; ?> 
                    <?php endforeach; ?> 
          
      <?php if (!empty($isByChequeEnable)) : ?>
                    document.getElementById("show_bycheque_error").innerHTML = '';
      <?php endif; ?>
                  document.getElementById("event_payment_gateway_success_message").innerHTML = '';
                },
                onSuccess: function (responseJSON) {
                  document.getElementById('event_gateway_submit_spinner').innerHTML = '';

                  if (responseJSON.success_message) {
                    document.getElementById("event_payment_gateway_success_message").innerHTML = '<ul class="form-notices"><li>' + responseJSON.success_message + '</li></ul>';
                  }

                  // SHOW PAYPAL ERROR MESSAGE, IF ANY
                  if (responseJSON.email_error) {
                    TempStr = '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.email_error + '</li></ul></li>';

                    if (responseJSON.paypal_info_error) {
                      display_error = false;
                      if (responseJSON.paypal_info_error)
                        TempStr += '<li><ul class="error"><li>' + responseJSON.paypal_info_error + '</li></ul></li>';
                    }
                                       
                    TempStr += '</ul>';
                  }

                  if (display_error == true)
                  {
                    if (responseJSON.paypal_info_error) {
                      TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.paypal_info_error + '</li></ul></li></ul>';
                    }
                  }
                  
                  if (responseJSON.error_message) {
                    TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.error_message + '</li></ul></li></ul>';
                  }
                  
                  if (TempStr)
                    document.getElementById('show_paypal_form_massges').innerHTML = TempStr;                  
                    <?php foreach ($this->enablePaymentGateway as $paymentGateway) : ?>
                        <?php if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)):?>    
                            <?php $gatewayName = strtolower($paymentGateway);?>
                                            
                            if (responseJSON.<?php echo $gatewayName ?>_info_error) {
                                window['<?php echo $gatewayName ?>' + 'TempStr'] += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.<?php echo $gatewayName ?>_info_error + '</li></ul></li></ul>';
                            }                  

                            if (responseJSON.error_message_<?php echo $gatewayName ?>) {
                                window['<?php echo $gatewayName ?>' + 'TempStr'] += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.error_message_<?php echo $gatewayName ?> + '</li></ul></li></ul>';
                            }                  

                            if (window['<?php echo $gatewayName ?>' + 'TempStr'] && document.getElementById('show_<?php echo $gatewayName; ?>_form_massges')) {
                                document.getElementById('show_<?php echo $gatewayName; ?>_form_massges').innerHTML = window['<?php echo $gatewayName ?>' + 'TempStr'];
                            }
                        <?php endif; ?> 
                    <?php endforeach; ?> 

                  // SHOW BYCHEQUE ERROR MESSAGE, IF ANY
                  if (responseJSON.cheque_info_error)
                    document.getElementById("show_bycheque_error").innerHTML = '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.cheque_info_error + '</li></ul></li></ul>';
                }

              })
                      );
            }
    <?php endif; ?>

          en4.core.runonce.add(function () {
    <?php if (!empty($this->enablePaymentGateway)) : ?>
              checkEnableGateway();
    <?php else: ?>
            <?php $paymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.paymentmethod', 'paypal'); ?>
            <?php if($paymentGateway != 'paypal' && Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)):?>    
                <?php $gatewayName = strtolower($paymentGateway);?>        

                if(document.getElementById('siteeventticket_payment_info_<?php echo $gatewayName ?>')) {
                    document.getElementById('siteeventticket_payment_info_<?php echo $gatewayName ?>').addEvent('submit', function (e) {
                    e.stop();
                    var TempStr = '';
                    var enabled = 0;
                    var display_error = true;
                    if (document.getElementById('enabled-1').checked) {
                      enabled = 1;
                    }
                    document.getElementById('show_<?php echo $gatewayName ?>_form_massges').innerHTML = '';
                    document.getElementById('spiner-image').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Siteeventticket/externals/images/loading.gif" /></center>';
                    en4.core.request.send(new Request.JSON({
                        url: en4.core.baseUrl + 'siteeventticket/order/set-payment-info-additional-gateway',
                        method: 'POST',
                        data: {
                          format: 'json',
                          gatewayName: '<?php echo $gatewayName ?>',
                          gatewayCredentials: $("siteeventticket_payment_info_<?php echo $gatewayName; ?>").toQueryString(),
                          enabled: enabled,
                          event_id: <?php echo $this->event_id ?>
                        },
                        onSuccess: function (responseJSON) {

                            document.getElementById('spiner-image').innerHTML = '';

                            if (responseJSON.success_message) {
                              TempStr += '<ul class="form-notices"><li>' + responseJSON.success_message + '</li></ul>';
                            }

                            if (responseJSON.<?php echo $gatewayName ?>_info_error) {
                              display_error = false;
                              TempStr += '<li><ul class="error"><li>' + responseJSON.<?php echo $gatewayName ?>_info_error + '</li></ul></li></ul>';
                            }
                            else
                            {
                              TempStr += '</ul>'
                            }
                        

                        if (display_error == true)
                        {
                            if (responseJSON.<?php echo $gatewayName ?>_info_error) {
                              TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.<?php echo $gatewayName ?>_info_error + '</li></ul></li></ul>';
                            }
                        }

                        if (responseJSON.error_message) {
                            TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.error_message + '</li></ul></li></ul>';
                        }

                        document.getElementById('show_<?php echo $gatewayName ?>_form_massges').innerHTML = TempStr;
                      }

                    })
                    );
                  });              
                }
            <?php endif; ?>
                
            if(document.getElementById('siteeventticket_payment_info')) {  
                document.getElementById('siteeventticket_payment_info').addEvent('submit', function (e) {
                    e.stop();
                    var TempStr = '';
                    var enabled = 0;
                    var display_error = true;
                    if (document.getElementById('enabled-1').checked) {
                      enabled = 1;
                    }
                    document.getElementById('show_paypal_form_massges').innerHTML = '';
                    document.getElementById('spiner-image').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Siteeventticket/externals/images/loading.gif" /></center>';
                    en4.core.request.send(new Request.JSON({
                      url: en4.core.baseUrl + 'siteeventticket/order/set-payment-info',
                      method: 'POST',
                      data: {
                        format: 'json',
                        username: document.getElementById('username').value,
                        password: document.getElementById('password').value,
                        signature: document.getElementById('signature').value,
                        email: document.getElementById('email').value,
                        enabled: enabled,
                        event_id: <?php echo $this->event_id ?>
                      },
                      onSuccess: function (responseJSON) {
                        document.getElementById('spiner-image').innerHTML = '';
                        if (responseJSON.success_message) {
                          TempStr += '<ul class="form-notices"><li>' + responseJSON.success_message + '</li></ul>';
                        }

                        if (responseJSON.email_error) {
                          TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.email_error + '</li></ul></li>';

                          if (responseJSON.paypal_info_error) {
                            display_error = false;
                            TempStr += '<li><ul class="error"><li>' + responseJSON.paypal_info_error + '</li></ul></li></ul>';
                          }
                          else
                          {
                            TempStr += '</ul>'
                          }
                        }

                        if (display_error == true)
                        {
                          if (responseJSON.paypal_info_error) {
                            TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.paypal_info_error + '</li></ul></li></ul>';
                          }
                        }

                        if (responseJSON.error_message) {
                          TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.error_message + '</li></ul></li></ul>';
                        }

                        document.getElementById('show_paypal_form_massges').innerHTML = TempStr;
                      }

                    })
                            );
                  });
            }
        <?php endif; ?>
          });

        </script>
  <?php endif; ?>
<?php endif; ?>
<?php if (!$this->only_list_content): ?>
      <!--</div>-->
    </div>	
  </div>	
<?php endif; ?>
</div>

<script type="text/javascript">
    en4.core.runonce.add(function () {
        if(<?php echo ($checkedStripeConnect) ? 1 : 0; ?>) {
            selectEventPaymentGateway('siteevent_gateway_stripe', true);
        }    
    });
</script>    