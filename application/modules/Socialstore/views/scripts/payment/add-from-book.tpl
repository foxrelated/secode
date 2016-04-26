<?php if (count($this->addresses) > 0) :?>
<div class = "ynstore_addbook_form"> 
<span><?php echo $this->translate('Select your shipping address(es) from the list below')?></span>
<form action = "" method="post">
<?php foreach ($this->addresses as $key => $address) : ?>
  <div class = "ynstore_addshippingbook">	
	<input type="checkbox" name="address[]" value="<?php echo $key;?>">
	<span class = "ynstore_addshippingbook_value">
		<?php 
		$count = count((array)$address);
		$i = 0;
		foreach ($address as $add) :?>
			<?php 
				$i++;
				if ($add != $title) {
					if ($i == $count) {
						echo $add;
					}
					else {
						if ($add != '') {
							echo  $add. ', ';
						}
					}
				}
			?>
			<?php endforeach;?>
	</span>
  </div>
<?php endforeach;?>
	<button class ="ynstore_address_button" name="submit" type="submit" value="submit"><?php echo $this->translate('Submit')?></button>
</form>
</div>
<?php else:?>
<br />
    <div class="tip" style= "padding-left: 10px;">
      <span>
        <?php echo $this->translate('There is no address available from address book!');?>
      </span>
    </div>
<?php endif;?>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
