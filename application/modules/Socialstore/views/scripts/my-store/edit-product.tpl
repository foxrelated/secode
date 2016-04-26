<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<?php echo $this->form->render($this); 
?>



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
</script>
