<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>

<div class = "layout_middle">
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<ul class="main_product_list"> 
<?php foreach( $this->paginator as $product ): ?>
        <li>
		<div class="product_info">
			<div class="product_title"> <?php echo $product ?></div>
			<div class="quickstats">
				<div>
					<span><?php echo $this->translate("Featured") ?></span>
				<span>
					<?php if ($product->featured == 1): echo $this->translate('Yes'); else: echo $this->translate('No'); endif; ?> 
				</span>

				</div>
				<div>
				<span><?php echo $this->translate("Product Rating") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($product->rate_ave).$this->translate(' Stars'); ?> 
				</span>
				</div>
				<div>
					<span><?php echo $this->translate("Total Paid Fee") ?></span>
				<span>
					<?php echo $this->currency($product->getTotalPaidFee()) ?> 
				</span>
				</div>
				<div>
				<span><?php echo $this->translate("Total Units Sold") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($product->sold_qty) ?>
				</span>
			</div>
			<div>
				<span><?php echo $this->translate("Total Income") ?></span>
				<span>
					<?php echo $this->currency($product->getAmount($product->sold_qty)) ?>
				</span>
			</div>
			</div>
		</div>
		<div style="clear: both"></div>
		</li>
      <?php endforeach; ?>
    </ul>

			
			
			

<?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have no product yet.');?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    //'query' => '',
    //'params' => $this->formValues,
  )); ?>

</div>