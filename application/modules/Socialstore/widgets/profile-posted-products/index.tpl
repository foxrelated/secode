<?php if($this->title) : ?><h3> <?php echo $this->translate($this->title); ?> </h3> <?php endif; ?>
<ul class="main_product_list">
	<?php foreach($this->items as $item): ?>
	<li>		
		<div class="recent_product_img">
			<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
		</div>
		
		<div class="pricecart_wrapper">
			<?php echo $this->cart($item)?>
		</div>
		<div class="product_info">
			<div class="product_title"> <?php echo $item ?></div>
			<div class="store_browse_info_date">
			<?php echo $this->translate('Posted by %s', $this->htmlLink($item->getOwner(), $item->getOwner()->getTitle())) ?>
			<?php echo $this->translate("Store: "); ?> <?php echo $this->htmlLink($item->getStore(), $item->getStore()->getTitle())?>
			</div>
			<div class="product_description"> <?php echo $item->getDescription(); ?> </div>
			<div class="product_favourite">
				<?php echo $this->favourite($item) ?>
			</div>
		</div>
		<div style="clear:both"></div>
	</li>
<?php endforeach; ?>
</ul>