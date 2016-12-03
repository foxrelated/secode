<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
//ADDING SETTINGS IF SITEEVENTTICKET MODULE IS ENABLED.
if (Engine_Api::_()->siteevent()->hasTicketEnable()):
  ?>
  <script type="text/javascript">
    window.addEvent('domready', function () {
      showcommissionType();
    });

    function showcommissionType() {
      if (document.getElementById('commission_handling')) {
        if (document.getElementById('commission_handling').value == 1) {
          document.getElementById('commission_fee-wrapper').style.display = 'none';
          document.getElementById('commission_rate-wrapper').style.display = 'block';
        } else {
          document.getElementById('commission_fee-wrapper').style.display = 'block';
          document.getElementById('commission_rate-wrapper').style.display = 'none';
        }
      }
    }
  </script>
  <?php endif; ?>
<h2>
<?php echo 'Advanced Events Plugin'; ?>
</h2>

  <?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
  <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div>
<?php echo $this->htmlLink(array('action' => 'index', 'reset' => false), 'Back to Manage Packages', array('class' => 'icon_siteeventpaid_admin_back buttonlink')) ?>
</div>

<br />
<div class="siteeventpaid_pakage_form">
  <div class="settings">
<?php echo $this->form->render($this) ?>
  </div>
</div>

<script type="text/javascript">
  function setRenewBefore() {

    if ($('duration-select').value == "forever" || $('duration-select').value == "lifetime" || ($('recurrence-select').value !== "forever" && $('recurrence-select').value !== "lifetime")) {
      $('renew-wrapper').setStyle('display', 'none');
      $('renew_before-wrapper').setStyle('display', 'none');
    } else {
      $('renew-wrapper').setStyle('display', 'block');
      if ($('renew').checked)
        $('renew_before-wrapper').setStyle('display', 'block');
      else
        $('renew_before-wrapper').setStyle('display', 'none');
    }
  }
  $('duration-select').addEvent('change', function () {
    setRenewBefore();
  });
  $('recurrence-select').addEvent('change', function () {
    setRenewBefore();
  });
  window.addEvent('domready', function () {
    setRenewBefore();
  });
</script>