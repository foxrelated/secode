<h2><?php echo $this->translate("Store Plugin") ?></h2>
<?php $shipping = $this->order_shipping;
	  $billing = $this->order_billing;
?>
<!-- admin menu -->
<?php echo $this->content()->renderWidget('socialstore.admin-main-menu');
 		$shipping = $this->order_shipping;
	  $billing = $this->order_billing;

?>

<p>
  <?php //echo $this->translate("STORE_VIEWS_SCRIPTS_ADMINORDER_INDEX_DESCRIPTION") ?>
</p>
<br /> 
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
.search > form{width: 452px;}
table.admin_table tbody tr td {
	text-align: left;
	padding-left: 3px;
}
table.admin_table thead tr th {
	text-align: center;
}
.servBodL {
	border-left: 1px solid #EEEEEE;
}
.profile_fields {
    margin-top: 10px;
    overflow: hidden;
}
.profile_fields h4 {
    border-bottom: 1px solid #EAEAEA;
    font-weight: bold;
    margin-bottom: 10px;
    padding: 0.5em 0;
}
.profile_fields h4 > span {
    background-color: #FFFFFF;
    display: inline-block;
    margin-top: -1px;
    padding-right: 6px;
    position: absolute;
    color: #717171;
    font-weight: bold;
}
.profile_fields > ul {
    padding: 10px;
    list-style-type: none;
}
.profile_fields > ul > li {
    overflow: hidden;
}

.profile_fields > ul > li > span {
    display: block;
    float: left;
    margin-right: 15px;
    overflow: hidden;
    width: 175px;
}

.profile_fields > ul > li > span + span {
    display: block;
    float: left;
    min-width: 0;
    overflow: hidden;
    width: auto;
}

</style>   