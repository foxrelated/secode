<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<?php $shipping = $this->order_shipping;
	  $billing = $this->order_billing;
?>
<style type="text/css">
 table.admin_table thead tr th {
    background-color: #E9F4FA;
    border-bottom: 1px solid #AAAAAA;
    padding: 7px 10px;
    white-space: nowrap;
    text-align: center;
    font-weight: normal;
}
table.admin_table tbody tr td {
    border-bottom: 1px solid #EEEEEE;
    font-size: 0.9em;
    padding: 7px 10px;
    vertical-align: top;
    white-space: normal;
    text-align: center;
}
#global_page_socialstore-my-orders-order-detail .profile_fields > ul > li > span + span {
	width:auto;
}
#global_page_socialstore-my-orders-order-detail .profile_fields {
	width: 50%;
}

</style>   
<div class="layout_middle">
<h2><?php echo $this->translate('Order: ') . $this->order_id;?></h2>
<br />
<div class="profile_fields ynstore_order_detail_fields">
	<h4><span><?php echo $this->translate('Billing Address');?></span></h4>
		<ul>
			<li class = "ynstore_order_detail_li_class">
				<span><?php echo $this->billing ?></span>
			</li>
		</ul>
<h4><span><?php echo $this->translate('Packages')?></span></h4>
	<ul>
	<?php
	$i = 0;
	foreach ($this->packages as $package) : $i++;?>
			<li class = "ynstore_order_detail_li_class">
				<span class="store_span_title"><?php echo $i.'. '.$this->translate("Shipping Address") ?></span>
				<span><?php echo $package['shippingaddress_id']?></span>
				<br />
				<div style = "overflow: auto">
				<table class='admin_table ynstore_order_detail_table'>
				<thead>
					<tr>
						<th style="text-align:left">
							<?php echo $this->translate('Item')?>
						</th>
						<th style="text-align:center">
							<?php echo $this->translate('Attributes')?>
						</th>
						<th style="text-align:left">
							<?php echo $this->translate('SKU')?>
						</th>
						<th style="text-align:right">
							<?php echo $this->translate('Quantity')?>
						</th>
						<th style="text-align:right">
							<?php echo $this->translate('Pretax Price')?>
						</th>
						<th style="text-align:right">
							<?php echo $this->translate('Tax')?>
						</th>
						<th style="text-align:right">
							<?php echo $this->translate('Shipping Fee')?>
						</th>
						<th style="text-align:right">
							<?php echo $this->translate('Total')?>
						</th>
						<th style="text-align:center">
							<?php echo $this->translate('Delivery Status')?>
						</th>
						<th style="text-align:center">
							<?php echo $this->translate('Options')?>
						</th>
						
					</tr>
				</thead>
				<tbody>
				<?php foreach ($package['products'] as $orderitem_id => $product_item): 
					$product = Engine_Api::_()->getItem('social_product', $product_item['product_id']);
					$item = $this->item_model->getByOrderItemId($orderitem_id);
				?>
				<tr>
					<td style="text-align:left">
						<a href="<?php echo $product->getHref() ?>"><?php echo $product->getTitle() ?></a>	
					</td>
					<td>
						<?php echo Engine_Api::_()->getApi('attribute', 'socialstore')->getAttributes($item->options) ?>	
					</td>
					<td style="text-align:left">
						<?php echo $product->sku;  ?>
					</td>
					<td style="text-align:right">
						<?php echo $item->quantity;  ?>
					</td>
					<td style="text-align:right">
						<?php echo $this->currency($item->getPretaxPrice());  ?>
					</td>
					<td style="text-align:right">
						<?php echo $this->currency(round($product->tax_percentage*$item->getPretaxPrice(),2)); ?>
					</td>
					<td style="text-align:right">
						<?php echo $this->currency(round($item->getShippingAmount()+ $item->getHandlingAmount(),2));?>
					</td>
					<td style="text-align:right">
						<?php echo $this->currency(round($item->getTotalAmount() + $item->getShippingAmount()+ $item->getHandlingAmount(),2));?>
					</td>
					<td style="text-align:center">
						<?php echo $item->delivery_status;?>
					</td>
					<td style="text-align:center">
		          
					<?php if ($item->refund_status == 0 && $item->delivery_status != 'processing') : 
					echo $this->htmlLink(array(
		           	  'module' => 'socialstore',
		        	  'controller' => 'my-orders',
					  'action' => 'refund',
		              'orderitem_id' => $item->orderitem_id,
		              'route' => 'socialstore_extended',    
		              'reset' => true,
		            ), $this->translate('refund'), array(
		              'class' => ' smoothbox ',
		            )) ;
		            else : echo $this->translate('N/A'); 
		            ?>
		            <?php endif;?>
		            </td>
				</tr>
			<?php endforeach; ?>
				</tbody>
			</table>
			</div>
			</li>
			<hr class = "ynstore_order_detail_hr">
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
<br />
<div class = "ynstore_order_detail_option">
<a href="<?php echo $this->url(array('module' => 'socialstore', 'controller'=>'my-orders', 'action'=>'index'),'default', true) ?>"><?php echo $this->translate("Back");?></a>|
<a href="<?php echo $this->url(array('module'=>'socialstore','controller'=>'my-orders', 'action'=>'downpdf','order_id'=> $this->order->order_id),'default', true)?>"><?php echo $this->translate("Download PDF");?></a>
</div>
</div>
