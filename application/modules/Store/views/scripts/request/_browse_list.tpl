
<?php if ($this->justHtml): ?>
<div>
<?php endif; ?>

<?php if (!$this->details && !$this->request->credit): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate(
        'STORE_You have not set your shipping details yet. Please, follow the following url to set your details: %1s',
        $this->htmlLink(array(
            'route'     => 'store_extended',
            'controller'=> 'panel',
            'action'    => 'address'),
          $this->translate('Shipping Details'))
      ); ?>
    </span>
  </div>
<?php endif; ?>

  <div class="cart_products he-items" style="position: relative;">
    <a id="cart_loader_browse" class="cart_loader_browse hidden"><?php echo $this->htmlImage($this->layout()->staticBaseUrl.'application/modules/Credit/externals/images/loader.gif', ''); ?></a>
    <ul class="he-item-list" id="store_cart_items">
      <?php if ($this->request->credit):?>
      <li>
      <?php echo $this->translate('Receive OGV %s', $this->request->credit_value)?>
      </li>

      <span class="ogv-browse"><img src="/application/modules/Store/externals/images/OGV_modal.png" alt="OGV"></span>

      <?php else:?>
      <?php
      /**
       * @var $item    Store_Model_Cartitem
       * @var $product Store_Model_Product
       */
       $products = $this->request->getProducts();
      foreach ($products as $product):  ?>

        <li id="store-cart-product-<?php echo $product->getIdentity(); ?>">
          <div class="he-item-photo">
            <?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.normal')) ?>
          </div>

          <div class="he-item-options store-item-options">
            <div class="store-price-block">
              <?php
                echo $this->getPrice($product);
              ?> <br/>
              <?php if ($product->type == 'simple'): ?>
                <div class="store_products_count"><?php echo $this->translate('STORE_Quantity') . ': ' . '1'; ?></div>
              <?php endif; ?>
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
                <?php echo $this->translate('Posted'); ?>
                <?php echo $this->timestamp($product->creation_date); ?>
                <?php if ($product->hasStore()): ?>
                  <?php echo $this->translate('in %s store', $this->htmlLink($product->getStore()->getHref(), $this->string()->truncate($product->getStore()->getTitle(), 20), array('target' => '_blank', 'title' => $product->getStore()->getTitle()))); ?>
                <?php endif; ?>
                <br>
              </div>
            </div>

            <br />
            <div class="he-item-desc">
              <?php echo $this->viewMore(Engine_String::strip_tags($product->getDescription()), 33) ?>
            </div>
          </div>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
    </ul>
  </div>

<?php if ($this->justHtml): ?>
</div>
<?php endif; ?>