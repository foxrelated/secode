<div class="generic_layout_container layout_top">
<?php echo $this->content()->renderWidget('ynaffiliate.main-menu') ?>
</div>
<div class="generic_layout_container layout_main">
	<div class="generic_layout_container layout_middle">
		<?php 
    if ($this->account->approved == '0') {
      echo $this->translate('Your account has not been approved yet.'); 
    }
    elseif ($this->account->approved == '2') {
       echo $this->translate('Your account has been denied. Please contact Administrator for more information.');
    }
      ?>
	</div>
	
</div>