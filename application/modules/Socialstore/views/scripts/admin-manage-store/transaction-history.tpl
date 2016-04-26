<h2><?php echo $this->translate("Store Plugin") ?></h2>

<!-- admin menu -->
<?php echo $this->content()->renderWidget('socialstore.admin-main-menu') ?>

<div>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<table class='admin_table'>
	<thead>
		<tr>
			
			<th style="text-align:center">
				<?php echo $this->translate('Order ID')?>
			</th>
			<th style="text-align:center">
				<?php echo $this->translate('Item')?>
			</th>
			<th style="text-align:center">
				<?php echo $this->translate('User')?>
			</th>
			<th style="text-align:center">
				<?php echo $this->translate('Quantity')?>
			</th>
			<th style="text-align:center">
				<?php echo $this->translate('Pretax Price')?>
			</th>
			<th style="text-align:center">
				<?php echo $this->translate('VAT (%)')?>
			</th>
			<th style="text-align:center">
				<?php echo $this->translate('Final Price')?>
			</th>
			<th style="text-align:center">
				<?php echo $this->translate('Sub Total')?>
			</th>
			<th style="text-align:center">
				<?php echo $this->translate('Total')?>
			</th>

			
		</tr>
	</thead>
	<tbody>
	<?php
	foreach($this->paginator as $item) :	
		$product = $item->getObject();
		if (!is_object($product)) {
			continue;
		}
		
		$product->setQuantity($item->getItemQuantity());
	
		?>	
		<tr>
			<td style="text-align:center">
				<?php echo $item->order_id; ?>	
			</td>
			<td style="text-align:center">
				<a href="<?php echo $product->getHref() ?>"><?php echo $product->getTitle() ?></a>	
			</td>
			<td>
       		<?php if (($owner_id = $item->getOrder()->owner_id) != 0) : ?>
         		<a href="<?php echo $this->user($owner_id)->getHref() ?>">
         		<?php echo $this->user($owner_id)->getTitle() ?>
        	 </a>
        	 <?php else: ?>
        	 	<?php echo $this->translate('Guest');?>
        	 <?php endif;?>
         	</td>
			<td style="text-align:center">
				<?php echo $item->quantity;  ?>
			</td>
			<td style="text-align:center">
				<?php echo $this->currency($item->getPretaxPrice());?>
			</td>
			<td style="text-align:center">
				<?php echo $product->tax_percentage; ?>
			</td>
			<td style="text-align:center">
				<?php echo $this->currency($item->getPrice()); ?>
			</td>
			<td style="text-align:center">
				<?php echo $this->currency($item->getSubAmount());?>
			</td>
			<td style="text-align:center">
				<?php echo $this->currency($item->getTotalAmount());?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<br />
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
</div>

<?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There is no purchase of this product yet!');?>
      </span>
    </div>
  <?php endif; ?>
</div>
<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
  height: 65px;
}

</style>   