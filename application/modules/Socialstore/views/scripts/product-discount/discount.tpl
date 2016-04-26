<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<?php if ($this->invalid == 1) : ?>
<!-- assoc tips -->
<div class="tip">
  <span>
    <?php echo $this->translate('You cannot view this page.');?>
  </span>
</div>
<?php else: ?>
<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>

<div class="layout_middle">
<?php $product = $this->product;?>
<div class="product_info">
	<div class="product_title"> <?php echo $product ?></div>
	<div class="quickstats">
		<div>
			<span><?php echo $this->translate("Current Price")?>:</span>
			<span><b><?php echo $this->currency($product->getPretaxPrice()) ?></b></span>
		</div>
	</div>
</div>
		
<form method="post" action="<?php echo $this->url(array())?>">
<div style="margin-bottom: 10px;">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
            <td style="width: 100%;margin-top: 8px; padding:10px 1px;" valign="top">
                    <div align="left" style="">
<table class='admin_table' style = "width: 50%;" id = "discount">
	<thead>
		<tr>
			<th style="text-align:right">
				<?php echo $this->translate('Quantity')?>
			</th>
			<th style="text-align:right;">
				<?php echo $this->translate('Unit Price')?>
			</th>
			<th style="text-align:right;">
				<?php echo $this->translate("Options") ?>
			</th>
		</tr>
	</thead>
	<?php
 	$discount_row = 0; 
	$discounts = $this->discounts;
	if (count($discounts) != 0) :
		foreach($discounts as $discount):
	?>
	<tbody id="discount-row<?php echo $discount_row; ?>">	
		<tr>
			<td style="text-align:right">
				<input name="discount[<?php echo $discount_row;?>][quantity]" type="text" size="2" value="<?php echo $discount->quantity; ?>" />
			</td>
			<td style="text-align:right">
				<input name="discount[<?php echo $discount_row;?>][price]" type="text" size="16" value="<?php echo $discount->price; ?>" />
			</td>
			<!--  <td style="text-align:left">
				<input type="text" name="discount[<?php echo $discount_row; ?>][date_start]" value="<?php echo $discount->date_start; ?>" class="date" />
			</td>
            
            <td style="text-align:left">
	            <input type="text" name="discount[<?php echo $discount_row; ?>][date_end]" value="<?php echo $discount->date_end; ?>" class="date" />
	         </td>
			-->
			<td style="text-align:right;">
				<a onclick="javascript:removeDiscount('<?php echo $discount_row; ?>');" href="javascript:void(0);"><?php echo $this->translate('Remove'); ?></a>
			</td>
		</tr>
	</tbody>
	<?php 
			$discount_row++;
		endforeach;
	else : ?>
		<tbody id="discount-row<?php echo $discount_row; ?>">	
		<tr>
			<td style="text-align:right">
				<input name="discount[<?php echo $discount_row;?>][quantity]" type="text" size="2" value="" />
			</td>
			<td style="text-align:right">
				<input name="discount[<?php echo $discount_row;?>][price]" type="text" size="16" value="" />
			</td>
			<!--  <td style="text-align:left">
				<input type="text" name="discount[<?php echo $discount_row; ?>][date_start]" value="<?php echo $discount->date_start; ?>" class="date" />
			</td>
            
            <td style="text-align:left">
	            <input type="text" name="discount[<?php echo $discount_row; ?>][date_end]" value="<?php echo $discount->date_end; ?>" class="date" />
	         </td>
			-->
			<td style="text-align:right;">
				<a onclick="javascript:removeDiscount('<?php echo $discount_row; ?>');" href="javascript:void(0);"><?php echo $this->translate('Remove'); ?></a>
			</td>
		</tr>
		</tbody>
	<?php 
		$discount_row++;
		endif;?>
	 <tfoot id ='tfoot'>
     	<tr>
        	<td colspan="1"></td>
            <td style="text-align:right; padding-top:5px;">
            	<button type="button" onclick="javascript:addDiscount();"><?php echo $this->translate('Add Discount'); ?></button>
            </td>
            <td style="text-align:right; padding-top:5px;">
            	<button name = "submit" id = "submit" type="submit"> <?php echo $this->translate('Save Changes')?> </button>
            </td>
       	
       	</tr>
	</tfoot>
</table>


</div>
</td>
</tr>
</table>
</div>
</form>
<div id='helper' style='visibility:hidden'></div>
</div>
<?php endif;?>
<script type="text/javascript">

var discount_row = <?php echo $discount_row; ?>;
var product_type = '<?php echo $product->product_type;?>';
function addDiscount() {
	
	html  = '<tbody id="discount-row' + discount_row + '">';
	html += '  <tr>';
   	html += '    <td style="text-align:right"><input type="text" name="discount[' + discount_row + '][quantity]" value="" size="2" /></td>';
	html += '    <td style="text-align:right"><input type="text" name="discount[' + discount_row + '][price]" value="" size="16" /></td>';
    /*html += '    <td style="text-align:left"><input type="text" name="discount[' + discount_row + '][date_start]" value="" class="date" /></td>';
	html += '    <td style="text-align:left"><input type="text" name="discount[' + discount_row + '][date_end]" value="" class="date" /></td>';*/
	html += '    <td style="text-align:right"><a onclick="$(\'discount-row' + discount_row +'\').dispose();" href="javascript:void(0);"><?php echo $this->translate("Remove"); ?></a></td>';
	html += '  </tr>';	
    html += '</tbody>';
    var divele = document.createElement("table");


    var b=document.getElementById("helper");
    b.innerHTML="<table>"+html+"</table>";
    while(b.tagName!="TBODY") {
      if(b.tagName==null)
        b=b.nextSibling;
      else
        b=b.firstChild;
    }
    for(;b!=null;b=b.nextSibling) {
    	$('discount').insertBefore(b, $('tfoot'));
    }
    	
    /*divele.innerHTML = html;
	var ele = divele.firstChild;
	//$('discount').tFoot.inject(ele , 'before');
	$('discount').insertBefore(ele, $('tfoot'));	*/
	//jQuery('#discount-row' + discount_row + ' .date').datepicker({dateFormat: 'yy-mm-dd'});
	discount_row++;
}
function removeDiscount(row) {
	if (row == 0) {
		document.getElementsByName('discount[0][quantity]')[0].value = '';
		document.getElementsByName('discount[0][price]')[0].value = '';
	}
	else {
		$('discount-row' + row).dispose();
	}
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
