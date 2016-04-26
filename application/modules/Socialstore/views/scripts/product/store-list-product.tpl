<!-- render my widget  -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class ="layout_right">
<?php // echo $this->content()->renderWidget('socialstore.search-product-in-store')?>
</div>
<div class="layout_middle">
<?php var_dump($this->items_per_page)?>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<ul class="main_product_list">
	<?php foreach($this->paginator as $item): ?>
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

<br/>
<?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There is no product meets your criteria.');?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->products, null, null, array(
    'pageAsQuery' => true,
    //'query' => '',
    //'params' => $this->formValues,
  )); ?>
</div>