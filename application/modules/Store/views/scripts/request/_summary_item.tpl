<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: _checkout.tpl  4/25/12 6:23 PM teajay $
 * @author     Taalay
 */
?>

<div id="accordion">
  <p><?php echo $this->translate('Confirmation')?></p>
  <div class="content" id="store_payments">
    <ul id="store-checkout-panel">
      <li class="checkout-item">
        <div class="checkout-price">
          <span><?php echo $this->translate('STORE_items'); ?>:&nbsp;</span>
          <span class="store-price"
                id="store-cart-total-price"><?php echo $this->toCurrency($this->totalPrice, $this->currency); ?>
          </span>
        </div>
      </li>
      <li class="checkout-item">
        <div class="checkout-tax">
          <span><?php echo $this->translate('STORE_tax');?>:&nbsp;</span>
          <span class="store-price"><?php echo $this->toCurrency($this->taxesPrice, $this->currency); ?></span>
        </div>
      </li>
      <li class="checkout-item">
        <div class="checkout-shipping-price">
          <span><?php echo $this->translate('STORE_shipping');?>:&nbsp;</span>
          <span class="store-price"><?php echo $this->toCurrency($this->shippingPrice, $this->currency); ?></span>
        </div>
      </li>

      <div>
        <div class="checkout-total-price">
          <span class="checkout-title"><?php echo $this->translate('STORE_total');?>:&nbsp;</span>
          <span class="store-price">
            <?php echo $this->toCurrency($this->taxesPrice + $this->shippingPrice + $this->totalPrice, $this->currency); ?>
          </span>
        </div>
		<div class="checkout-item">
          <button onclick="Smoothbox.open('<?php echo $this->url(array('action'=>'confirm','id'=>$this->request->getIdentity()),'store_request',true)?>');" class="gateway-button button">
            <?php echo $this->translate('Confirm'); ?>
          </button>
        </div>
      </div>
    </ul>
  </div>
</div>
