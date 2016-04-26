<!-- render my widget -->
<?php
echo $this->content()->renderWidget('socialstore.main-menu') ?>
<div class="layout_right">
	<!-- render mini menu -->
</div>
<div class="layout_middle">
<?php if ($this->order->paytype_id == 'shopping-cart') :?>
<h3><?php echo $this->translate("Review Order")?></h3>
<div class="profile_fields">
<h4><span><?php echo $this->translate('Packages')?></span></h4>
		<ul>
			<?php foreach ($this->packages as $package) :?>
			<li class = "ynstore_review_li_class">
				<div class = "ynstore_review_div_class">
				<span class="store_span_title"><?php echo $this->translate("Shipping Address") ?></span>
				<span class="ynstore_review_div_class_span"><?php echo $package['shippingaddress_id']?></span>
				</div> 
				<?php $j = 0; foreach ($package['products'] as $key => $item): $j++; 
					$product = Engine_Api::_()->getItem('social_product', $item['product_id']);
				?>
				<div class = "ynstore_review_product_package">
					<div class="ynstore_product_browse_photo">
					<?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.normal')) ?>
					</div>
					<div class = "ynstore_review_product_info">
						<div class = "ynstore_review_product">
							<span class='product_browse_info_title'><?php echo $product?></span>
							<span class='product_browse_info_date'>
				              <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($product->getOwner()->getHref(), $product->getOwner()->getTitle()) ?>
				              - <?php echo $this->translate('store: ');?> <?php echo $this->htmlLink($product->getStore()->getHref(), $product->getStore()->getTitle()) ?>
				            </span>
						</div>
						<div class = "ynstore_review_product"><span class ="ynstore_review_span_title"><?php echo $this->translate("Quantity")?></span>:<span class = "ynstore_review_span_content"><?php echo $this->locale()->toNumber($item['quantity'])?></span></div>
						<div class = "ynstore_review_product">
							<span class ="ynstore_review_span_title"><?php echo $this->translate("Price")?></span>:<span class = "ynstore_review_span_content product_price_value"><?php echo $this->currency($item['total_amount'])?></span> 
						</div>
						<?php $attributes = Engine_Api::_()->getApi('attribute', 'socialstore')->getOptionsByItemId($key);
							if ($attributes != null) :
						?>
							<div class = "ynstore_review_product">
								<span class ="ynstore_review_span_title"><?php echo $this->translate("Options")?></span>:
								<?php 
									$v = 0; 
									$length = count($attributes);
									$string = "";	
									foreach ($attributes as $attribute) : $v++;
										$string .= $attribute['label'];
										if ($attribute['adjusted_price'] != 0) { 
											$string.= " (".$this->currency($attribute['adjusted_price']).")";
										}
										if ($v < $length) {
											$string.=' - ';
										}
								?>
								<?php endforeach;?>
								<span><?php echo $string;?></span>
							</div>
						<?php endif;?>
						<div class = "ynstore_review_product">	
							<span class ="ynstore_review_span_title"><?php echo $this->translate("Shipping Fee")?></span>:<span class = "ynstore_review_span_content product_price_value"><?php echo $this->currency($item['shipping_amount'])?></span>
						</div>
					</div>
				</div>
				<?php if ($j != count($package['products'])) : ?>
					<hr class = "ynstore_review_hr">
				<?php endif; ?>
				<?php endforeach;?>
			</li>
			<?php endforeach;?>
		</ul>
	<h4><span><?php echo $this->translate("Order Summary");?></span></h4>
		<ul>
			<li class = "ynstore_review_summary_li_class">
				<span class = "ynstore_review_span_summary_title"><?php echo $this->translate("Items").' ('.$this->locale()->toNumber($this->order->quantity).')'.':'?></span><span class = "ynstore_review_span_summary product_price_value"><?php echo $this->currency(round($this->order->total_amount - ($this->order->shipping_amount + $this->order->handling_amount),2))?></span> 
			</li>
			<li class = "ynstore_review_summary_li_class">
				<span class = "ynstore_review_span_summary_title"><?php echo $this->translate("Shipping Fee").':'?></span><span class = "ynstore_review_span_summary product_price_value"><?php echo $this->currency(round(($this->order->shipping_amount + $this->order->handling_amount),2))?></span> 
			</li>
			<hr class = "ynstore_review_summary_hr">
			<li class = "ynstore_review_summary_li_class">
				<span class = "ynstore_review_span_summary_title"><?php echo $this->translate("Order Total").':'?></span><span class = "ynstore_review_span_summary product_price_value"><?php echo $this->currency($this->order->total_amount)?></span> 
			</li>
		</ul>
</div>
<?php endif;?>
<?php echo $this->form ?>

</div>