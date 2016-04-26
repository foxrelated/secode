<?php 
$trans = array(
			'none' => $this->translate('None'),
			'remove' => $this->translate('Remove'),
			'na' => $this->translate("N/A"),
			'multi' => $this->translate("Multi Shipping")
		);
$this->headScript() -> appendScript('var ynstorePackageTrans=' . (Zend_Json::encode($trans)));
?>
<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.payment-menu') ?>
</div>
<div class="layout_middle">
<div id ="ynstore_package_tip" class="tip" style="display: none">
  <span>
    <?php echo $this->translate('Invalid shipping addresses and/or shipping methods!');?>
  </span>
</div>
<div style = "overflow:auto">
<form name = "ynstore_package_form" onsubmit="return validateFormOnSubmit(this)" method="post" action="<?php echo $this->url(array())?>">
<table id = "ynstore_package_table" class='admin_table' style = "width: 100%;">
	<thead>
		<tr>
			<th class = "ynstore_table_text">
				<?php echo $this->translate('Item')?>
			</th>
			<th class = "ynstore_table_text">
				<?php echo $this->translate('Attributes')?>
			</th>
			<th class = "ynstore_table_number">
				<?php echo $this->translate('Quantity')?>
			</th>
			<th class = "ynstore_table_text">
				<?php echo $this->translate('Ship to address')?>
			</th>
			<th class = "ynstore_table_text">
				<?php echo $this->translate('Shipping method')?>
			</th>
			<th class = "ynstore_table_text">
				<?php echo $this->translate('Options')?>
			</th>
		</tr>
	</thead>
	<tbody id = "ynstore_package_table_body">
	<?php
	$i = 0;
	$temp = $this->temp;
	foreach(@$this->cart_items as $item) :	
		$i++;
		$product = $item->getObject();
		if (!is_object($product)) {
			continue;
		}
		$product->setQuantity($item->getItemQuantity());
		$product->setOptions($item->options);
		if ($item->options) {
			if (count($temp[$item->options][$item->object_id] > 2)) {
				if ($temp[$item->options][$item->object_id][0] != $i) {
					$orderitem_id = $temp[$item->options][$item->object_id][1];		
				}
				else {
					$orderitem_id = $item->orderitem_id;
				} 
			}
		}
		else {
		if (count($temp[0][$item->object_id] > 2)) {
				if ($temp[0][$item->object_id][0] != $i) {
					$orderitem_id = $temp[0][$item->object_id][1];		
				}
				else {
					$orderitem_id = $item->orderitem_id;
				} 
			}
		}
		$qty = $item->getTotalProQtyByOpt($product->product_id,$item->options);
	?>	
		<tr id = "ynstore_orderitem_<?php echo $orderitem_id?>_<?php echo $i?>">
			<td class = "ynstore_table_text">
				<a href="<?php echo $product->getHref() ?>"><?php echo $product->getTitle() ?></a>
			</td>
			<td class = "ynstore_table_text">
				<?php echo $product->getAttributes() ?>	
			</td>
			<td id = "ynstore_package_quantity_<?php echo $i?>" class = "ynstore_table_number ynstore_package_quantity_<?php echo $i?>">
				<?php
					if ($item->options) { 	
						if (count($temp[$item->options][$item->object_id] > 2)) {
							if ($temp[$item->options][$item->object_id][0] != $i) {
								$m = $temp[$item->options][$item->object_id][0];
							}
							else {
								$m = 0;
							}
						}
					}
					else {
						if (count($temp[0][$item->object_id] > 2)) {
							if ($temp[0][$item->object_id][0] != $i) {
								$m = $temp[0][$item->object_id][0];
							}
							else {
								$m = 0;
							}
						}
					}
				?>
				<?php if ($product->product_type == 'default') :?>
				<input id = "ynstore_package_quantity_<?php echo $i?>_<?php echo $m?>" name="cartitem_qty[<?php echo $product->getIdentity()?>][<?php echo $i;?>][quantity]" type="text" size="6" onblur="javascript:en4.socialstore.packages.updatePackage('<?php echo $item->options?>',<?php echo $orderitem_id?>,this.parentNode,<?php echo $qty?>,this.value, <?php echo $i?>)" value="<?php echo $item->getItemQuantity() ?>" />
				<?php elseif ($product->product_type == 'downloadable') :?>
				<input id = "ynstore_package_quantity_<?php echo $i?>_<?php echo $m?>" name="cartitem_qty[<?php echo $product->getIdentity()?>][<?php echo $i;?>][quantity]" type="text" size="6" value="<?php echo $item->getItemQuantity() ?>" />
			<?php endif;?>
			</td>
			<td class = "ynstore_table_text">
			<?php if ($product->product_type == 'default') :?>
				<select name = "cartitem_qty[<?php echo $product->getIdentity()?>][<?php echo $i;?>][address]" id = "ynstore_shipping_address_select_<?php echo $i?>" onchange = "javascript:en4.socialstore.packages.changeAddress(<?php echo $product->store_id?>,this.options[selectedIndex].value,<?php echo $product->category_id?>,'<?php echo $this->order_id?>',<?php echo $orderitem_id?>,this)">
					<option value = "0"></option>
				<?php foreach ($this->shippings as $key => $shipping) : ?>
					<option <?php if ($item->shippingaddress_id != 0 && $item->shippingaddress_id == $key):echo 'selected = "selected"'; endif;?> value="<?php echo $key?>"><?php echo $shipping?></option>
				<?php endforeach;?>
				</select>
			<?php elseif ($product->product_type == 'downloadable') :?>
				<span><?php echo $this->translate('Downloadable Product')?></span>
			<?php endif;?>
			</td>
			<td class = "ynstore_table_text">
			<?php if ($product->product_type == 'default') :?>
				<select name = "cartitem_qty[<?php echo $product->getIdentity()?>][<?php echo $i;?>][rule]" id = "ynstore_shipping_method_select_<?php echo $orderitem_id?>_<?php echo $i?>">
					<?php if ($item->shippingaddress_id == 0 || $item->shippingrule_id == 0) :?>
						<option value = "0"><?php echo $this->translate('None')?></option>
					<?php else: ?>
						<?php $rules = Engine_Api::_()->getApi('shipping', 'socialstore')->getRules($product->store_id,$item->shippingaddress_id,$product->category_id,$this->order_id);
							  foreach ($rules as $rule) : ?>
							  <option <?php if ($item->shippingrule_id != 0 && $item->shippingrule_id == $rule['id']):echo 'selected = "selected"'; endif;?> value = "<?php echo $rule['id']?>"><?php echo $rule['name'];?></option>
							  <?php endforeach;?>
					<?php endif;?>
				</select>
			<?php elseif ($product->product_type == 'downloadable') :?>
				<span><?php echo $this->translate('Downloadable Product')?></span>
			<?php endif;?>
			<input type="hidden" name = "cartitem_qty[<?php echo $product->getIdentity()?>][<?php echo $i;?>][options]" value = "<?php echo $item->options?>" />
			</td>	
			<td class = "ynstore_table_text">
			<?php if ($product->product_type == 'default') :?>
				<?php if ($item->options): ?>
					<?php if (count($temp[$item->options][$item->object_id] > 2)) : ?>
						<?php if ($temp[$item->options][$item->object_id][0] == $i) : ?>
							<?php if ($product->getQuantity() != 1) : ?>
								<a href="javascript:void(0);" onclick="javascript:en4.socialstore.packages.addPackage('<?php echo $orderitem_id?>',<?php echo $i?>);"><?php echo $this->translate('Multi Shipping'); ?></a> | 
							<?php endif;?>	
						<?php else: ?>
							<?php if ($product->getQuantity() > 1) : ?>
								<a href="javascript:void(0);" onclick="javascript:en4.socialstore.packages.addPackage('<?php echo $orderitem_id?>',<?php echo $i?>);"><?php echo $this->translate('Multi Shipping'); ?></a> | 
							<?php endif;?>	
								<a onclick="javascript:en4.socialstore.packages.removePackage('ynstore_orderitem_<?php echo $temp[$item->options][$item->object_id][1]?>_<?php echo $i?>',<?php echo $temp[$item->options][$item->object_id][0]?>,<?php echo $i?>,<?php echo $orderitem_id?>);" href="javascript:void(0);"><?php echo $this->translate('Remove'); ?></a> | 
						<?php endif;?>				
					<?php else :?>
						<?php if ($product->getQuantity() != 1) : ?>
							<a href="javascript:void(0);" onclick="javascript:en4.socialstore.packages.addPackage('<?php echo $orderitem_id?>',<?php echo $i?>);"><?php echo $this->translate('Multi Shipping'); ?></a> | 
						<?php endif;?>
					<?php endif;?>
				<?php else :?>
					<?php if (count($temp[0][$item->object_id] > 2)) : ?>
						<?php if ($temp[0][$item->object_id][0] == $i) : ?>
							<?php if ($product->getQuantity() != 1) : ?>
								<a href="javascript:void(0);" onclick="javascript:en4.socialstore.packages.addPackage('<?php echo $orderitem_id?>',<?php echo $i?>);"><?php echo $this->translate('Multi Shipping'); ?></a> | 
							<?php endif;?>	
						<?php else: ?>
							<?php if ($product->getQuantity() > 1) : ?>
								<a href="javascript:void(0);" onclick="javascript:en4.socialstore.packages.addPackage('<?php echo $orderitem_id?>',<?php echo $i?>);"><?php echo $this->translate('Multi Shipping'); ?></a> | 
							<?php endif;?>	
								<a onclick="javascript:en4.socialstore.packages.removePackage('ynstore_orderitem_<?php echo $temp[0][$item->object_id][1]?>_<?php echo $i?>',<?php echo $temp[0][$item->object_id][0]?>,<?php echo $i?>,<?php echo $orderitem_id?>);" href="javascript:void(0);"><?php echo $this->translate('Remove'); ?></a> | 
						<?php endif;?>				
					<?php else :?>
						<?php if ($product->getQuantity() != 1) : ?>
							<a href="javascript:void(0);" onclick="javascript:en4.socialstore.packages.addPackage('<?php echo $orderitem_id?>',<?php echo $i?>);"><?php echo $this->translate('Multi Shipping'); ?></a> | 
						<?php endif;?>
					<?php endif;?>	
				<?php endif;?>
			<?php endif;?>
			<?php echo $this->htmlLink(array(
						'route' => 'socialstore_extended',
						'controller' => 'payment',
              			'action' => 'remove-package',
              			'order-id' => $item -> order_id,
		              	'orderitem-id' => $orderitem_id,
            		), $this->translate('Remove'), array(
              				'class' => ' smoothbox ',
            			)) ?>
			</td>
		</tr>
	<?php endforeach;?>
	</tbody>
</table>
<button name = "submit" id = "submit" type="submit" style="margin-top:10px"> <?php echo $this->translate('Continue')?> </button>
</form>
</div>
</div>

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
 </style>
 
 <script type="text/javascript">
function validateFormOnSubmit(theForm) {
	var checks = document.getElementsByName('ynstore_package_form')[0].getElementsByTagName('select');
	var flag = true;
	for (i = 0; i < checks.length; i++) {
		if (checks[i].value == 0) {
			flag = false;
			break;
		}
	};
	if (flag) {
		return true;
	}
	else {
		$('ynstore_package_tip').setStyle('display','block');
		return false;
	}
}
</script>