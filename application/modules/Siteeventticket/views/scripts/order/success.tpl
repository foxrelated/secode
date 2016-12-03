<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: success.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'); 
?>

<div class="generic_layout_container layout_middle">
  <div class="siteevent_alert_msg b_medium">
    <?php if (empty($this->state) || ($this->state == 'active')): ?>
      <p>
        <?php echo $this->success_message; ?>
      </p>
  
    <?php elseif ($this->state == 'pending'): ?>
      <h3>
        <?php echo $this->translate('Payment Pending') ?>
      </h3>
      <p>
        <?php echo $this->translate('Thank you for submitting your payment. Your payment is currently pending.') ?>
      </p>
  
    <?php else: ?>
      <h3>
        <?php echo $this->translate('Payment Failed') ?>
      </h3>
    <!--    <p>
      <?php echo $this->translate('There was an error processing your transaction for the order: %s.', $this->viewerOrder) ?>
      <?php if (!empty($this->viewer_id)) : ?>
        <?php echo $this->translate('We suggest that you please try again with another payment method after clicking on "make payment" from my orders page.') ?>
      <?php endif; ?>
      </p>-->
  
    <?php endif; ?>
  </div>
  
  <?php if (empty($this->state) || ($this->state == 'active') || ($this->state == 'pending')): ?>
    <?php if (!empty($this->viewer_id)) : ?>
        <div class="clr">
          <button class="mtop10 fright" onclick="viewYourOrder()">
            <?php echo $this->translate('Go to My Tickets') ?>
          </button>
        </div>
    <?php endif; ?>
  <?php else :?>
     <?php if (!empty($this->viewer_id)) : ?>
        <div class="clr">
          <button class="mtop10 fright" onclick="backToEvent()">
            <?php echo $this->translate('Back to Event') ?>
          </button>
        </div>
    <?php endif; ?>
  <?php endif; ?>
</div>


<script type="text/javascript">
  function viewYourOrder()
  {
    window.location.href = '<?php echo $this->url(array('action' => 'my-tickets'), 'siteeventticket_order', true) ?>';
  }
  
  function backToEvent() {
      window.location.href = '<?php echo $this->siteevent->getHref(); ?>';
  }
</script>
