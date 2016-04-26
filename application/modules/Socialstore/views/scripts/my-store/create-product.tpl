<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<!-- form render-->
<?php echo $this->form->render($this); 

$this->headScript()
    ->appendFile($this->baseUrl().'/application/modules/Socialstore/externals/scripts/core.js');
   
?>
<?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
          
?>

<script type="text/javascript">
window.addEvent('domready',function(){
	if ($('product_type').value == 'downloadable') {
		$('downloadable_file-wrapper').show();
		$('preview_file-wrapper').show();
		$('available_quantity-wrapper').hide();
		$('min_qty_purchase-wrapper').hide();
		$('max_qty_purchase-wrapper').hide();
		$('weight-wrapper').hide();
		$('weight_unit-wrapper').hide();
	}
	else {
		$('downloadable_file-wrapper').hide();
		$('preview_file-wrapper').hide();
	}
	if ($('discount_price').value == 0) {
		$('available_date-wrapper').hide();
		$('expire_date-wrapper').hide();
	}
});
function removeSubmit()
{
	//$('buttons-wrapper').hide(); 
}
function showDownloadUrl() 
{
	if ($('product_type').value == 'downloadable') {
		$('downloadable_file-wrapper').show();
		$('preview_file-wrapper').show();
		$('available_quantity-wrapper').hide();
		$('min_qty_purchase-wrapper').hide();
		$('max_qty_purchase-wrapper').hide();
		$('weight-wrapper').hide();
		$('weight_unit-wrapper').hide();
	}
	if ($('product_type').value == 'default') {
		$('downloadable_file-wrapper').hide();
		$('preview_file-wrapper').hide();
		$('downloadable_file').value = '';
		$('preview_file').value = '';
		$('available_quantity-wrapper').show();
		$('min_qty_purchase-wrapper').show();
		$('max_qty_purchase-wrapper').show();
		$('weight-wrapper').show();
		$('weight_unit-wrapper').show();
	}
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