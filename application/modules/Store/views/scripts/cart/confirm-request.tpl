<div class="layout_left">
<div id="accordion">
  <p><?php echo $this->translate('Confirmation')?></p>
  <div class="content">
    <ul id="store-credit-panel">
      <li class="checkout-item">
        <div class="checkout-price">
          <span><?php echo $this->translate('STORE_items'); ?>:&nbsp;</span>
          <span class="store-price">
          	<?php if ($this->credit) :?>
            <span class="store_credit_icon">
              <span class="store-credit-price"><?php echo $this->api->getCredits($this->price); ?></span>
            </span>
            <?php else: ?>

        	<span class="store_exchange">
              <span class="store-credit-price"><?php echo $this->toCurrency($this->totalPrice, $this->currency); ?></span>
            </span>
            <?php endif;?>
          </span>
        </div>
      </li>
      <li class="checkout-item">
        <div class="checkout-tax">
          <span><?php echo $this->translate('STORE_tax');?>:&nbsp;</span>
          <span class="store-price">
            <?php if ($this->credit) :?>
            <span class="store_credit_icon">
              <span class="store-credit-price"><?php echo $this->api->getCredits($this->taxesPrice); ?></span>
            </span>
            <?php else: ?>
        	<span class="store_exchange">
              <span class="store-credit-price"><?php echo $this->locale()->toCurrency($this->taxesPrice, $this->currency); ?></span>
            </span>
            <?php endif;?>
          </span>
        </div>
      </li>
      <li class="checkout-item">
        <div class="checkout-shipping-price">
          <span><?php echo $this->translate('STORE_shipping');?>:&nbsp;</span>
          <span class="store-price">
             <?php if ($this->credit) :?>
            <span class="store_credit_icon">
              <span class="store-credit-price"><?php echo $this->api->getCredits($this->shippingPrice); ?></span>
            </span>
            <?php else: ?>
        	<span class="store_exchange">
              <span class="store-credit-price"><?php echo $this->locale()->toCurrency($this->shippingPrice, $this->currency); ?></span>
            </span>
            <?php endif;?>
          </span>
        </div>
      </li>
      <div>
        <div class="checkout-total-price">
          <span class="checkout-title"><?php echo $this->translate('STORE_total');?>:&nbsp;</span>
          <span class="store-price">
            <?php if ($this->credit) :?>
            <span class="store_credit_icon">
              <span class="store-credit-price">
              	<?php echo $this->api->getCredits($this->price + $this->taxesPrice + $this->shippingPrice); ?>
              </span>
            </span>
            <?php else: ?>
        	<span class="store_exchange">
              <span class="store-credit-price"><?php echo $this->locale()->toCurrency($this->taxesPrice + $this->shippingPrice + $this->totalPrice, $this->currency); ?></span>
            </span>
            <?php endif;?>
          </span>
        </div>

        <div class="checkout-item">

          <form method="post" style="display: inline-block;">
	
          <?php if ($this->credit) :?>	
          <?php if ($this->balance >= $this->api->getCredits($this->price + $this->taxesPrice + $this->shippingPrice)) : ?>

            <span>
              <button class="button">
                <?php echo $this->translate('Confirm'); ?>
              </button>
          <?php endif; ?>
              <button><?php echo $this->htmlLink(array('route' => 'store_cart','action'=>'checkout','product_id'=>$this->product->getIdentity()), $this->translate('Cancel')); ?>
               </button>
            </span>

        	<?php else: ?>

        	<span>
              <button class="button">
                <?php echo $this->translate('Confirm'); ?>
              </button>

              <button><?php echo $this->htmlLink(array('route' => 'store_cart','action'=>'checkout','product_id'=>$this->product->getIdentity()), $this->translate('Cancel')); ?>
              </button>
         </span>

        	<?php endif; ?>

        	</form>

        </div>

      </div>
    </ul>
  </div>
</div>

</div>


<?php if ($this->credit && $this->balance < $this->api->getCredits($this->price + $this->taxesPrice + $this->shippingPrice)) : ?>
  <div class="tip" style="clear: none; font-size: 14px; padding-bottom: 10px;">
    <span>
      <?php echo $this->translate('CREDIT_not-enough-credit'); ?>
    </span>
  </div>
<?php endif; ?>

<div class="shipping-details">
  <?php if ($this->details): ?>
    <span class="float_left"><?php echo $this->translate('Shipping Details');?>&nbsp;</span>
    <?php if (isset($this->details['zip'])): ?>
      <span class="float_left">
        <?php
          echo $this->details['first_name'] . ' ' . $this->details['last_name'] . "<br />" .
            $this->details['city'] . ', ' . $this->region . ' ' . $this->details['zip'] . ', ' . $this->country . "<br />" .
            $this->details['address_line_1'] . (($this->details['address_line_2']) ? $this->translate(' or ') . $this->details['address_line_2'] : '') ."<br />" .
            $this->details['phone'] . (($this->details['phone_extension']) ? $this->translate(' or ') . $this->details['phone_extension'] : '')
          ;
        ?>
      </span>
    <?php endif; ?><br />
  <?php endif; ?>
  <?php if ($this->credit):?>
  <span class="float_left"><?php echo $this->translate('Your Balance'); ?>:&nbsp;</span>
  <span class="store_credit_icon float_left">
    <span class="store-credit-price"><?php echo ($this->balance) ? $this->balance : 0; ?></span>
  </span>
  <?php endif; ?>
</div>

<div class="layout_middle">

  <ul class="he-item-list" id="store_cart_items">
    <?php
    /**
     * @var $item    Store_Model_OrderItem
     * @var $product Store_Model_Product
     */
    $product = $this->product; ?>
      <li>
        <div class="he-item-photo">
          <?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.normal')) ?>
        </div>

        <div class="he-item-options store-item-options">
          <div class="store-price-block">
             <?php if ($this->credit) :?>
            <span class="store_credit_icon">
              <span class="store-credit-price">
              	<?php echo $this->api->getCredits($product->getPrice()); ?>
              </span>
            </span>
            <?php else: ?>
        	<span class="store_exchange">
              <span class="store-credit-price"><?php echo $this->locale()->toCurrency($product->getPrice(), $this->currency); ?></span>
            </span>
            <?php endif;?>
            <br/>
          </div>
        </div>

        <div class="he-item-info store-item-info">
          <div class="he-item-title">
            <h3><?php echo $this->htmlLink($product->getHref(), $this->string()->truncate($product->getTitle(), 20))?></h3>
          </div>
          <div style="display: inline-block;">
            <div>
              <?php echo $this->itemRate('store_product', $product->getIdentity()); ?>
            </div>
            <div class="clr"></div>
            <div class="he-item-details">
              <span class="float_left"><?php echo $this->translate('Posted'); ?>&nbsp;</span>
              <span class="float_left"><?php echo $this->timestamp($product->creation_date); ?>&nbsp;</span>
              <br />
            </div>
          </div>

          <br />
          <div class="he-item-desc">
            <?php echo $this->viewMore(Engine_String::strip_tags($product->getDescription()), 33) ?>
          </div>
        </div>
      </li>
  </ul>
</div>