<h2><?php echo $this->translate("Store Plugin") ?></h2>

<!-- admin menu -->
<?php echo $this->content()->renderWidget('socialstore.admin-main-menu') ?>

<div class='clear'>
<div class = "settings">
<?php echo $this->form->render();?>
</div>

</div>

<script type="text/javascript">
window.addEvent('domready',function(){
	$('product_type-wrapper').hide();
	var downloadable = '<?php echo $this->downloadable;?>';
	if (downloadable == 1) {
		$('available_quantity-wrapper').hide();
		$('min_qty_purchase-wrapper').hide();
		$('max_qty_purchase-wrapper').hide();
		$('weight-wrapper').hide();
		$('weight_unit-wrapper').hide();
	}
	if ($('discount_price').value == 0) {
		$('available_date-wrapper').hide();
		$('expire_date-wrapper').hide();
	}
});
function removeSubmit(){
   $('execute').hide(); 
}
function discountPriceChange() {
	if ($('discount_price').value != 0) {
		$('available_date-wrapper').show();
		$('expire_date-wrapper').show();
	}
	else {
		$('available_date-wrapper').hide();
		$('expire_date-wrapper').hide();
	}
}
//$(document).addEvent('domready',function(){initMap(true)});
</script>
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