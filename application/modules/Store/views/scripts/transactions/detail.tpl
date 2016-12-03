<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: detail.tpl 2011-09-21 17:53 mirlan $
 * @author     Mirlan
 */
?>




<div class="layout_left">
<?php if (!$this->isPublic): ?>
<div id='panel_options'>
<?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
echo $this->navigation()
->menu()
->setContainer($this->navigation)
->setPartial(array('_navIcons.tpl', 'core'))
->render()
?>
</div>
<?php endif; ?>
</div>




<div class="layout_middle">
<div class="he-items">

<h3>
    <?php echo $this->htmlLink(array('route' => 'store_panel',
        'action' => 'transactions'), $this->translate('My Transactions')); ?>
    &nbsp;<img src="/application/modules/Core/externals/images/next.png">&nbsp;<?php echo $this->translate('Transaction'); ?>
</h3>



<div class="profile_fields">

  <h4>
    <?php echo $this->translate("STORE_Transaction Details") ?>
  </h4>

  <table class="store-transaction-details">
    <tr>
      <td><?php echo $this->translate('Member') ?></td>
      <td>
        <?php if ($this->user && $this->user->getIdentity()): ?>
        <?php echo $this->user->__toString(); ?>
        <?php if (!_ENGINE_ADMIN_NEUTER): ?>
          - <?php echo $this->translate('Email') ?>:<?php echo $this->translate(' <a title="Send Email" href="mailto:' .
            $this->escape($this->user->email) . '">' . $this->user->email . '</a>') ?>
          <?php endif; ?>
        <?php else: ?>
        <i><?php echo $this->translate('Deleted Member') ?></i>
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Status') ?></td>
      <td>
        <span class="shipping-text">
		<?php echo ucfirst($this->item->status); ?>
        		<?php if( in_array($this->item->status, array('processing', 'shipping')) ): ?>
	</span>
        &nbsp;
        <?php echo $this->htmlLink($this->url(array('action'=>'change-status')), $this->translate('Change'), array('class'=>'smoothbox button')); ?>
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('STORE_Product Title'); ?></td>
      <?php if ($this->product): ?>
      <td><?php echo $this->product->__toString(); ?></td>
      <?php else: ?>
      <td><?php echo $this->item->name; ?></td>
      <?php endif; ?>
    </tr>
    <tr>
      <td><?php echo $this->translate('STORE_Parameters'); ?></td>
      <td><?php echo $this->api->params_string($this->item->params); ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('STORE_Quantity'); ?></td>
      <td><?php echo $this->locale()->toNumber($this->item->qty); ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('STORE_Last Update Date'); ?></td>
      <td><?php echo $this->timestamp($this->item->update_date); ?></td>
    </tr>
  </table>
  <br/>

  <h4 class="payment_transaction_detail_headline">
    <?php echo $this->translate("STORE_Payment Details") ?>
  </h4>
  <table class="store-transaction-details">
    <tr>
      <td><?php echo $this->translate('Item Amount') ?></td>
      <td>
        <?php if ($this->item->via_credits) : ?>
          <span class="store_credit_icon">
            <span class="store-credit-price"><?php echo $this->api->getCredits($this->item->item_amt * $this->item->qty); ?></span>
          </span>
        <?php else : ?>
          <?php echo str_replace("US$","OGV",$this->locale()->toCurrency((float)($this->item->item_amt * $this->item->qty), $this->item->currency)) ?>
          
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Tax Amount') ?></td>
      <td>
        <?php if ($this->item->via_credits) : ?>
          <span class="store_credit_icon">
            <span class="store-credit-price"><?php echo $this->api->getCredits($this->item->tax_amt * $this->item->qty); ?></span>
          </span>
        <?php else : ?>
          <?php echo $this->locale()->toCurrency((float)($this->item->tax_amt * $this->item->qty), $this->item->currency) ?>
          
        <?php endif; ?>
      </td>
    </tr>
    <tr style="display: none;">
      <td><?php echo $this->translate('Shipping Amount') ?></td>
      <td>
        <?php if ($this->item->via_credits) : ?>
          <span class="store_credit_icon">
            <span class="store-credit-price"><?php echo $this->api->getCredits($this->item->shipping_amt * $this->item->qty); ?></span>
          </span>
        <?php else : ?>
          <?php echo $this->locale()->toCurrency((float)($this->item->shipping_amt * $this->item->qty), $this->item->currency) ?>
          
        <?php endif; ?>
      </td>
    </tr>
    <tr style="display: none;">
      <td><?php echo $this->translate('Commission Amount') ?></td>
      <td>
        <?php if ($this->item->via_credits) : ?>
          <span class="store_credit_icon">
            <span class="store-credit-price" style="color: red;"><?php echo $this->api->getCredits($this->item->commission_amt * $this->item->qty); ?></span>
          </span>
        <?php else : ?>
          &ndash;<?php echo $this->locale()->toCurrency((float)($this->item->commission_amt * $this->item->qty), $this->item->currency) ?>
          
        <?php endif; ?>
      </td>
    </tr>
    <tr style="display: none;">
      <td><?php echo $this->translate('Gateway fee') ?></td>
      <td>
        <?php if ($this->item->via_credits) : ?>
          <span class="store_credit_icon">
            <span class="store-credit-price" style="color: red;"><?php echo $this->api->getCredits(0); ?></span>
          </span>
        <?php else : ?>
          &ndash;<?php echo $this->locale()->toCurrency((float)($this->item->getGatewayFee()), $this->item->currency) ?>
          
        <?php endif; ?>
      </td>
    </tr>

    <tr>
      <td><?php echo $this->translate('Total Gross Amount') ?></td>
      <td>
        <?php if ($this->item->via_credits) : ?>
          <span class="store_credit_icon">
            <span class="store-credit-price"><?php echo $this->api->getCredits($this->item->total_amt * $this->item->qty - ($this->item->commission_amt * $this->item->qty)); ?></span>
          </span>
        <?php else : ?>
          <?php echo str_replace("US$","OGV",$this->locale()->toCurrency((float)($this->item->total_amt * $this->item->qty - ($this->item->commission_amt * $this->item->qty + $this->item->getGatewayFee())), $this->item->currency)) ?>
          
        <?php endif; ?>
      </td>
    </tr>

    <tr>
      <td><?php echo $this->translate('Payment Date') ?></td>
      <td><?php echo $this->timestamp($this->order->payment_date) ?></td>
    </tr>
  </table>
  <br/>

  <?php if ( !$this->item->isItemDigital() ): ?>
  <h4 class="payment_transaction_detail_headline">
    <?php echo $this->translate("STORE_Shipping Details") ?>
  </h4>
  <table class="store-transaction-details">
    <?php if (isset($this->country)): ?>
    <tr>
      <td><?php echo $this->translate('Country') ?></td>
      <td><?php echo $this->country->location; ?></td>
    </tr>
    <?php endif; ?>
    <?php if (isset($this->state)): ?>
    <tr>
      <td><?php echo $this->translate('STORE_State/Region') ?></td>
      <td><?php echo $this->state->location; ?></td>
    </tr>
    <?php endif; ?>
    <tr>
      <td><?php echo $this->translate('City'); ?></td>
      <td><?php echo $this->order->shipping_details['city']; ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Zip Code'); ?></td>
      <td><?php echo $this->order->shipping_details['zip']?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('STORE_Address Line'); ?></td>
      <td><?php echo $this->order->shipping_details['address_line_1']?></td>
    </tr>
    <?php if (isset($this->order->shipping_details['address_line_2'])): ?>
    <tr>
      <td><?php echo $this->translate('STORE_Address Line 2'); ?></td>
      <td><?php echo $this->order->shipping_details['address_line_2']?></td>
    </tr>
    <?php endif; ?>
    <tr>
      <td><?php echo $this->translate('STORE_Phone'); ?></td>
      <td><?php echo $this->order->shipping_details['phone_extension'] + '-' + $this->order->shipping_details['phone']; ?></td>
    </tr>
  </table>
  <?php endif; ?>

</div>
</div>
