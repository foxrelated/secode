
<?php $shipping = $this->address;?>
<table class='admin_table' style = "margin-top: 10px; margin-left: 20px">
		<thead><tr><th><strong><?php echo $this->translate('Shipping Info')?></strong></th><th></th></tr></thead>
		<tbody>
		<tr>
			<td>
				<?php echo $this->translate('Full Name')?>
			</td>
			<td class = "servBodL">
				<?php echo $shipping['fullname']?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $this->translate('Address')?>
			</td>
			<td class = "servBodL">
				<?php echo $shipping['street']?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $this->translate('Alternate Address')?>
			</td>
			<td class = "servBodL">
				<?php echo $shipping['street2']?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $this->translate('Phone')?>
			</td>
			<td class = "servBodL">
				<?php echo $shipping['phone']?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $this->translate('Postal Code')?>
			</td>
			<td class = "servBodL">
				<?php echo $shipping['postcode']?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $this->translate('City')?>
			</td>
			<td class = "servBodL">
				<?php echo $shipping['city']?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $this->translate('Region')?>
			</td>
			<td class = "servBodL">
				<?php echo $shipping['region']?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $this->translate('Country')?>
			</td>
			<td class = "servBodL">
				<?php echo $shipping['country']?>
			</td>
		</tr>
		</tbody>
	</table>
	
<style type="text/css">
table.admin_table tbody tr td {
	text-align: left;
	padding-left: 3px;
}
table.admin_table thead tr th {
	text-align: center;
}
</style>