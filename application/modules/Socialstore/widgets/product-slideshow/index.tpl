<?php if( count($this->items) >  0 ): ?>
<ul class="slideshow_container">
	<li>
		<div id ="slide-runner-widget" class='slideshow'>
			<?php foreach($this->items as $item): ?>
				<?php if(Engine_Api::_()->user()->getUser($item->owner_id)->getIdentity()!=0):?>
			<div class="slide">
				<div class="featured_products">
					<div class="featured_stores_img_wrapper">
						<div class="featured_products_img">
						<a href="<?php echo $item->getHref()?>"> 
                			<img src="<?php echo $item->getPhotoUrl("thumb.profile")?>" />
                		</a>
						</div>
					</div>
					<div class="store_info">
						<div class="store_title"><?php echo $item ?></div>
						<?php echo $this->partial('product_browse_info_date.tpl','socialstore' ,array('item'=>$item, 'show_options'=>$this->show_options)) ?>
						<p class="store_description"><?php echo $item->getDescription() ?></p>
						<?php 
							$string = '';
							$defaultType = $item->getOptions();
							$i = 0;
							if (count($defaultType) > 0) {
								$v = 0; 
								$length = count($defaultType);	
								foreach ($defaultType as $key => $type) {
									$v++;
									$default_option = Engine_Api::_()->getApi('attribute','socialstore')->getDefaultOption($item->product_id, $key);
									if ($default_option != null) {
										$type_label = Engine_Api::_()->getApi('attribute','socialstore')->getTypeLabel($key);
										$string .= $type_label.': '. $default_option->label;
										if ($default_option->adjust_price != 0) { 
											$string.= " (".$this->currency($default_option->adjust_price).")";
										}
										if ($v < $length) {
											$string.=' - ';
										}
									}
								}
							}
							if ($string != '') :
						?>
						  <p class="store_description">
						<!--<span class= "ynstore_widget_pro_opt">
							<?php echo $this->translate('Options')?>
						</span>:-->
						<span class="ynstore_widget_pro_attr">
							<?php echo $string;?>
						</span>
						</p>
						<?php endif;?>
						<div class="product_favourite">
							<?php echo $this->favourite($item) ?>
						</div>		
						<?php //echo $this->cart($item)?>
						<div class="product_slideshow">
							<span class="product_slideshow_text"> <?php echo $this->translate('Price');?></span>
							<?php $discount_price = $item->getDiscountPrice();
    	  						if ($discount_price == 0) : ?>
      								<span class = "product_slideshow_discount"><?php echo $this->currency($item->getPretaxPrice())?> </span>
							<?php else: ?>
									<span class = "product_slideshow_oldprice"><?php echo $this->currency($item->pretax_price)?></span>
									<span class = "product_slideshow_discount"><?php echo $this->currency($discount_price)?> </span>
							<?php endif;?>
						</div>		
						<div class = "store_product_cart">
      						<?php if ($item->checkStock()) : ?>
      							<a class="store_product_addtocart" href="javascript:en4.store.cart.addProductBox(<?php echo $item->product_id;?>)"><span><?php echo $this->translate("Add To Cart")?></span></a> 
							<?php  else: ?>
								<div class="store_product_outofstock"><span class = "store_outofstock_text"><?php echo $this->translate("Out Of Stock")?></span></div>
							<?php  endif;?>
						</div>
					</div>			
				</div>
			</div>
			<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</li>
</ul>

<script type="text/javascript">
   jQuery(document).ready(function(){
		var slideWidth = jQuery('.slideshow_container').width()-20;
	    /* call divSlideShow without parameters */
	    jQuery('.slideshow').divSlideShow({
	    width: slideWidth,
		height:290, 
		loop:1000, 
		arrow:'begin', 
		controlClass:'slideshow_action', 
		controlActiveClass:'slideshow_action_active'
		});
	});
</script>
<?php else: ?>
<div class="tip">
      <span>
        <?php echo $this->translate('There is no featured product yet.');?>
      </span>
    </div>
<?php endif;?>