<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>


<div class='layout_middle'>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="product_list">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
        	<div class="product_browse_photo">
           <?php  echo $this->htmlLink($item->getHref(),
          $this->itemPhoto($item, 'thumb.normal', ''),
          array('class' => 'store_browse_photo'))           
          	?>
           </div>
          <div class='product_browse_options'>  
          	<a class="buttonlink" href="javascript:en4.store.cart.addProductBox(<?php echo $item->getIdentity()  ?>,1)"><?php echo $this->translate('Add to Cart') ?></a>
          </div>
          <div class='product_browse_info'>
            <p class='product_browse_info_title'>
              <?php echo $this->htmlLink($item->getHref(), $item->title) ?>
            </p>
            <p class='product_browse_info_date'>
               <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              - <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
            </p>
            <div class='product_browse_info_blurb'>
              <span class="product_price_label"> 
                  <?php echo $this->translate("Price:");?>
              </span>
              <span class="product_price_value">
              	<?php echo $this->currency($item->price) ?>
              </span>
              -
              <span> 
                  <?php echo $this->translate("Category:");?>
              </span>
              <span>
              	<?php echo $item->category_id?>
              </span>
              <br /> 
              <span> 
                  <?php echo $this->translate("Available:");?>
              </span>
              <span>
              	<?php 
                  	echo $this->locale()->toDateTime($item->available_date)?>
              </span>
              <br />
              <span> 
                  <?php echo $this->translate("Expire:");?>
              </span>
              <span>
              	<?php echo $this->locale()->toDateTime($item->expire_date)?>
              </span>
             
            </div>
          </div>
           
		</li>
      <?php endforeach; ?>
    </ul>


  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('No products.');?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    //'query' => '',
    //'params' => $this->formValues,
  )); ?>
</div>
