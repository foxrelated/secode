<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>

<div class = "layout_middle">
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
				<?php echo $this->translate('Status')?>
			</th>
			<th style="text-align:center">
				<?php echo $this->translate('Qty')?>
			</th>
			<th style="text-align:center">
				<?php echo $this->translate('Pretax Price')?>
			</th>
			<th style="text-align:center">
				<?php echo $this->translate('VAT(%)')?>
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
		//$product->setQuantity($item->getItemQuantity());
	
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
				<?php echo $item->payment_status  ?>
			</td>
			<td style="text-align:center">
				<?php echo $item->quantity;  ?>
			</td>
			<td style="text-align:center">
				<?php echo $this->currency($product->pretax_price);  ?>
			</td>
			<td style="text-align:center">
				<?php echo $product->tax_percentage; ?>
			</td>
			<td style="text-align:center">
				<?php echo $this->currency($product->getPrice()); ?>
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
 table.admin_table thead tr th {
    background-color: #E9F4FA;
    border-bottom: 1px solid #AAAAAA;
    font-weight: bold;
    padding: 7px 10px;
    white-space: nowrap;
    text-align: center;
}
table.admin_table tbody tr td {
    border-bottom: 1px solid #EEEEEE;
    font-size: 0.9em;
    padding: 7px 10px;
    vertical-align: top;
    white-space: normal;
    text-align: center;
}

</style>   