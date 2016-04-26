<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.payment-menu') ?>
</div>
<div class="layout_middle">
<?php $cart =  Socialstore_Api_Cart::getInstance();
	$order =  $cart->getOrder();
	$cart_items =  $cart->getCartItems();
?>
<form method="post" action="<?php echo $this->url(array('cmd' =>'checkout', 'order_id' => $order->order_id))?>">
<div style="margin-bottom: 10px;">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
            <td style="width: 100%;margin-top: 8px; padding:10px 1px;" valign="top">
                    <div align="left" style="">
<table class='admin_table' style = "width: 100%;">
	<thead>
		<tr>
			<th class='admin_table_short'>
				<input type='checkbox' onchange="selectAll()" class='checkbox' id='parentcheckbox' checked />
			</th>
			<th style="text-align:center">
				<?php echo $this->translate('Item')?>
			</th>
			<th style="text-align:center">
				<?php echo $this->translate('Attributes')?>
			</th>
			<th style="text-align:center">
				<?php echo $this->translate('Quantity')?>
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
				<?php echo $this->translate("Options") ?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach($cart_items as $item) :	
		$product = $item->getObject();
		if (!is_object($product)) {
			continue;
		}
		$product->setQuantity($item->getItemQuantity());
		$product->setOptions($item->options);
	?>	
		<tr>
			<td class="item-check">
				<input name="cartitem_check[<?php echo $product->getIdentity().'_'.$item->options;?>]" id = "<?php echo $product->getIdentity();?>" type='checkbox' class='checkbox' value="<?php  echo $product->getTotalAmount() ?>" onclick = "calTotal2()" checked/>
			</td>
			<td style="text-align:center">
				<a href="<?php echo $product->getHref() ?>"><?php echo $product->getTitle() ?></a>	
			</td>
			<td style="text-align:center">
				<?php echo $product->getAttributes() ?>	
			</td>

			<td style="text-align:center">
				<?php if ($product->product_type == 'default') :?>
					<input rel="<?php echo $product->getIdentity()?>" onchange="checkQuantity(this)" class="product-quantity" name="cartitem_qty[<?php echo $product->getIdentity().'_'.$item->options;?>][qty]" type="text" size="6" value="<?php echo $product->getQuantity(); ?>" />
					<?php elseif ($product->product_type == 'downloadable') :?>
					<input rel="<?php echo $product->getIdentity()?>" onchange="checkQuantity(this)" class="product-quantity" readonly = "readonly" name="cartitem_qty[<?php echo $product->getIdentity().'_'.$item->options;?>][qty]" type="text" size="6" value="<?php echo $product->getQuantity(); ?>" />
				<?php endif;?>
				<input type="hidden" class="options" name="cartitem_qty[<?php echo $product->getIdentity().'_'.$item->options;?>][options]" value="<?php echo $item->options;?>">
			</td>
			<td style="text-align:center">
				<?php echo $this->currency($product->getPretaxPrice());  ?>
			</td>
			<td style="text-align:center">
				<?php echo $product->tax_percentage; ?>
			</td>
			<td style="text-align:center">
				<?php echo $this->currency($product->getPrice()); ?>
				<input type="hidden" class="price" value="<?php echo $product->getPrice();?>" />
			</td>
			<td class="sub-total" style="text-align:center">
				<?php echo $this->currency($product->getTotalAmount());?>
			</td>
			<td style="text-align:center">
				<?php echo $this->htmlLink(array(
						'route' => 'socialstore_cart',
              			'action' => 'remove-item',
		              	'cartitem-id' => $product->getIdentity(),
            		), $this->translate('Remove'), array(
              				'class' => ' smoothbox ',
            			)) ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<button name = "updatecart_submit" id = "updatecart_submit" type="submit" style="margin-top:10px"> <?php echo $this->translate('Update Cart')?> </button>
<div style = "float: right;">
	<div style = "float: right;">
	<span><?php echo $this->translate('Total');?></span>
		<input id="total" type="text" value="0" name="total" class = "total_input" readonly="readonly">
	</div>	
<div style = "float: right; clear: both;">
<button name = "checkout_submit" id = "checkout_submit" type="submit" style="margin-top:10px"> <?php echo $this->translate('Check Out')?> </button>
</div>
</div>


</div>
</td>
</tr>
</table>
</div>
</form>
</div>
<script type="text/javascript">
	window.addEvent('domready', function() {
		calTotal2();
	});
	function calTotal2() {
		var total = 0;
		$$('.item-check .checkbox:checked').each(function(el) {
			var tr = el.getParent('tr');
			if (tr) {
				var quantity = tr.getElement('.product-quantity').get('value');
				quantity = parseInt(quantity);
				var price = tr.getElement('.price').get('value');
				price = parseFloat(price);
				total += price*quantity;
			}
		});
		total = total.toFixed(2);
		$('total').value = total;
	}
	
	function selectAll() {
		var i;
		var inputs = $$('input[type=checkbox].checkbox');
		for (i = 1; i < inputs.length; i++) {
			if (!inputs[i].disabled) {
				inputs[i].checked = inputs[0].checked;
			}
		}
		calTotal2();
	}
	
	function checkQuantity(obj) {
		var product_id = obj.get('rel');
		var quantity = obj.get('value');
		var option = obj.getParent().getElement('.options').get('value');
		var url = '<?php echo $this->url(array('action'=>'check-quantity'), 'socialstore_cart', true)?>';
	    new Request.JSON({
	        url: url,
	        method: 'post',
	        data: {
	            'product_id': product_id,
	            'quantity': quantity,
				'option': option
	        },
	        'onSuccess' : function(responseJSON, responseText) {
				if (responseJSON.status) {
					var tr = obj.getParent('tr');
					var subTotal = tr.getElement('.sub-total');
					subTotal.innerHTML = responseJSON.total;
					calTotal2();
				}
				else {
					alert(responseJSON.message);
					if (responseJSON.quantity) {
						obj.set('value', responseJSON.quantity);
						calTotal2();
					}
				}
	        }
	    }).send();
	}
</script>


<style type="text/css">
 table.admin_table thead tr th {
    background-color: #E9F4FA;
    border-bottom: 1px solid #AAAAAA;
    font-weight: bold;
    padding: 7px 10px;
    white-space: nowrap;
}
table.admin_table tbody tr td {
    border-bottom: 1px solid #EEEEEE;
    font-size: 0.9em;
    padding: 7px 10px;
    vertical-align: top;
    white-space: normal;
}
.total_input {
	margin-top: 10px;
	margin-left: 10px;
	width: 130px;
}
 </style>


