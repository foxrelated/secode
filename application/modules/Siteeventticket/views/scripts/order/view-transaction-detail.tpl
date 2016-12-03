<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-transaction-detail.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript"> 
  Asset.css('<?php echo $this->layout()->staticBaseUrl
	    . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'?>');
</script>

<div class="siteevent_details_view global_form_popup">
	<h3><?php echo $this->translate('Transaction Detail'); ?></h3>
	
  <table class="clr siteeventticket_transaction_details">
    <tbody>
      <tr>
        <td width="200"><strong><?php echo $this->translate('Transaction Id') ?></strong></td>
        <td> <?php echo $this->locale()->toNumber($this->allParams['transaction_id']) ?></td>
      </tr>
      <tr>
        <td width="200"><strong><?php echo $this->translate('Request Id') ?></strong></td>
        <td> <?php echo $this->locale()->toNumber($this->allParams['request_id']) ?></td>
      </tr>
      <tr>
        <td width="200"><strong><?php echo $this->translate('Payment Gateway') ?></strong></td>
        <td> 
          <?php if ($this->allParams['payment_gateway']): ?>
            <?php echo $this->translate($this->allParams['payment_gateway']) ?>
          <?php else: ?>
            <i><?php echo $this->translate('Unknown Gateway') ?></i>
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <td width="200"><strong><?php echo $this->translate('Payment Type') ?></strong></td>
        <td> <?php echo $this->translate(ucfirst($this->allParams['payment_type'])) ?></td>
      </tr>
      <tr>
        <td width="200"><strong><?php echo $this->translate('Payment State') ?></strong></td>
        <td> <?php echo $this->translate(ucfirst($this->allParams['payment_state'])) ?></td>
      </tr>
      <tr>
        <td width="200"><strong><?php echo $this->translate('Payment Amount') ?></strong></td>
        <td> <?php echo $this->locale()->toCurrency($this->allParams['response_amount'], Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) ?></td>
      </tr>
      <tr>
        <td width="200"><strong><?php echo $this->translate('Gateway Transaction Id') ?></strong></td>
        <td>
        	<?php if (!empty($this->allParams['gateway_transaction_id'])): ?>
            <a href="siteeventticket/payment/detail-transaction/transaction_id/<?php echo $this->allParams['transaction_id'] ?>" target="_blank"><?php echo $this->allParams['gateway_transaction_id'] ?></a>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <td width="200"><strong><?php echo $this->translate('Date') ?></strong></td>
        <td> <?php echo gmdate('M d,Y, g:i A', strtotime($this->allParams['response_date'])); ?></td>
      </tr>
    </tbody>
  </table>
  <div class='buttons mtop10'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Cancel") ?></button>
  </div>
</div>