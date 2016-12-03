<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: payment.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  window.addEvent('domready', function () {
    showPaymentForOrders();
    thresholdNotification();
  });

  // START FUNCTIONS TO MANAGE PAYMENT FLOW FOR ORDERS AND PAYMNET GATEWAYS
  function showPaymentForOrders() {
    if ($("siteeventticket_payment_to_siteadmin-wrapper")) {
      $("siteeventticket_payment_to_siteadmin-wrapper").style.display = 'block';
      showPaymentForOrdersGateway();
    }
  }

  function showPaymentForOrdersGateway() {
    if ($("siteeventticket_allowed_payment_gateway-wrapper")) {
      if ($("siteeventticket_payment_to_siteadmin-0").checked) {
        $("siteeventticket_allowed_payment_gateway-wrapper").style.display = 'block';
        $("siteeventticket_thresholdnotification-wrapper").style.display = 'block';
      }
      else {
        $("siteeventticket_allowed_payment_gateway-wrapper").style.display = 'none';
        $("siteeventticket_thresholdnotification-wrapper").style.display = 'none';
      }
    }

    if ($("siteeventticket_admin_gateway-wrapper")) {
      if ($("siteeventticket_payment_to_siteadmin-1").checked)
        $("siteeventticket_admin_gateway-wrapper").style.display = 'block';
      else
        $("siteeventticket_admin_gateway-wrapper").style.display = 'none';
    }
    
    billPaymentSettings();
        
    showAdminChequeInformation();
    
    thresholdNotification();
  }
  
  function billPaymentSettings() {
      
    if ($("siteeventticket_payment_to_siteadmin-0").checked && $("siteeventticket_paymentmethod-wrapper")) {
      $("siteeventticket_paymentmethod-label").innerHTML = "Payment for 'Commissions Bill'";
      $("siteeventticket_paymentmethod-element").children[0].innerHTML = "Select the payment gateway to be available to sellers for admin ‘Commissions Bill’ payment, if ‘Direct Payment to Sellers’ is selected.";
      if(<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0);?> && $("siteeventticket_paymentmethod-stripe")) {
          $("siteeventticket_paymentmethod-stripe").getParent().getElement('label').innerHTML = "Stripe [Here, normal Stripe account will be used by sellers to pay admin 'Commissions Bill' which are collected through other than Stripe Connect payment gateway.]";
      }      
    }
    else if($("siteeventticket_paymentmethod-wrapper")) {
      $("siteeventticket_paymentmethod-label").innerHTML = "Payment for Sellers 'Payment Requests'";
      $("siteeventticket_paymentmethod-element").children[0].innerHTML = "Select the payment gateway to be available to site admin for making payments against the 'Payment Requests' made by sellers, if ‘Payment to Website / Site Admin’ is selected.";
      if(<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0);?> && $("siteeventticket_paymentmethod-stripe")) {
          $("siteeventticket_paymentmethod-stripe").getParent().getElement('label').innerHTML = "Stripe [Here, normal Stripe account will be used by admin to pay seller's payments which are collected through other than Stripe Connect payment gateway.]";
      }        
    }       
  }  
  
  function thresholdNotification() {
      
    //THRESHOLD AMOUNT NOTIFICATION WORK
    if ($("siteeventticket_payment_to_siteadmin-0").checked && $("siteeventticket_thresholdnotification-1").checked) {
      $("siteeventticket_thresholdnotificationamount-wrapper").style.display = 'block';
      $("siteeventticket_thresholdnotify-wrapper").style.display = 'block';
    }
    else {
      $("siteeventticket_thresholdnotificationamount-wrapper").style.display = 'none';
      $("siteeventticket_thresholdnotify-wrapper").style.display = 'none';  
    }      
      
  }

  function showAdminChequeInformation() {
    if ($("siteeventticket_payment_to_siteadmin-1").checked)
      $("siteeventticket_send_cheque_to-wrapper").style.display = 'block';
    else
      $("siteeventticket_send_cheque_to-wrapper").style.display = 'none';
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

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php
    echo $this->form->render($this);
    ?>
  </div>
</div>
