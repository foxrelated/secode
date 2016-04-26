<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>


<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form store_browse_filters">
  <div>
    <div>
      <h3>
        <?php echo $this->translate($this->form->getTitle()) ?>
      </h3>
    
      <div class="form-elements">
        <?php echo $this->form->getDecorator('FormErrors')->setElement($this->form)->render();?>
        <?php echo $this->form->title; ?>
        <?php echo $this->form->sku; ?>
        <?php echo $this->form->product_type;?>
        <?php if ($this->product->product_type == 'downloadable') : ?>
        	<?php //echo $this->form->download_url; ?>
        <?php endif; ?>	
        <?php echo $this->form->{'category_id'}; ?>	
        <?php
        	if(isset($this->form->currency)):
				echo $this->form->currency; 
			endif; 
        ?>
       	<?php echo $this->form->view_status; ?>
       	<?php echo $this->form->description; ?>
       	<?php echo $this->form->body; ?>
      	<?php echo $this->form->available_quantity; ?>
      	<?php echo $this->form->min_qty_purchase; ?>
	   	<?php echo $this->form->max_qty_purchase; ?>
        <?php echo $this->form->pretax_price; ?>
        <?php echo $this->form->vat_id; ?>
        <?php echo $this->form->shipping_option; ?>
        <?php echo $this->form->deliver_days; ?>
        <?php echo $this->form->discount_price;?>
        <?php
        	if(isset($this->form->available_date)):
				echo $this->form->available_date; 
			endif; 
        ?>
        <?php
        	if(isset($this->form->expire_date)):
				echo $this->form->expire_date; 
			endif; 
        ?>
        <?php
        	if(isset($this->form->video_url)):
				echo $this->form->video_url; 
			endif; 
        ?>
        <?php if($this->form->store_authview)echo $this->form->store_authview; ?>
        <?php if($this->form->store_authcom)echo $this->form->store_authcom; ?>
        
     <?php if(Count($this->paginator) > 0): ?>
      <?php echo $this->form->store_id; ?>
      <ul class='store_editphotos'>        
        <?php foreach( $this->paginator as $photo ): ?>
          <li>
            <div class="store_editphotos_photo">
              <?php echo $this->itemPhoto($photo, 'thumb.normal')  ?>
            </div>
            <div class="store_editphotos_info">
              <?php
                $key = $photo->getGuid();
                echo $this->form->getSubForm($key)->render($this);
              ?>
              <div class="store_editphotos_cover">
                <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->deal->photo_id == $photo->file_id ): ?> checked="checked"<?php endif; ?> />
              </div>
              <div class="store_editphotos_label">
                <label><?php echo $this->translate('Main Photo');?></label>
              </div>
            </div>
            <br/>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php echo $this->form->execute->render(); ?>
       <?php echo $this->form->cancel; ?>
      <?php else: ?>
      <div class="form-wrapper">
      <div class="form-label" id="buttons-label">&nbsp;</div>
      <?php echo $this->form->execute->render(); ?>
       <?php echo $this->form->cancel; ?>
       </div>
      <?php endif; ?>
        </div>
      
    </div>
  </div>
</form>




<script type="text/javascript">
window.addEvent('domready',function(){
	$('product_type-wrapper').hide();
	var downloadable = '<?php echo $this->downloadable;?>';
	if (downloadable == 1) {
		$('available_quantity-wrapper').hide();
		$('shipping_option-wrapper').hide();
		$('min_qty_purchase-wrapper').hide();
		$('max_qty_purchase-wrapper').hide();
		$('deliver_days-wrapper').hide();
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
