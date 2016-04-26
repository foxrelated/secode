<?php if (count($this->presets) > 0) :?>
<div class = "ynstore_addbook_form"> 
<span><?php echo $this->translate('Select your presets from the list below')?></span>
<form action = "" method="post">
<?php foreach ($this->presets as $preset) : ?>
  <div class = "ynstore_addshippingbook">	
	<input type="checkbox" name="preset[]" value="<?php echo $preset->attributepreset_id;?>">
	<span class = "ynstore_addshippingbook_value">
		<?php echo $preset->preset_name?>
	</span>
  </div>
<?php endforeach;?>
	<button class ="ynstore_address_button" name="submit" type="submit" value="submit"><?php echo $this->translate('Delete Selected')?></button>
</form>
</div>
<?php else:?>
<br />
    <div class="tip" style= "padding-left: 10px;">
      <span>
        <?php echo $this->translate('There is no attribute preset!');?>
      </span>
    </div>
<?php endif;?>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
