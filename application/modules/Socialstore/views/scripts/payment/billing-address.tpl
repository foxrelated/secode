<!-- widget render -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ;?>
<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.payment-menu') ?>
</div>
<div class="layout_middle">
<!-- form render-->
<div class="settings">
<?php echo $this->form->render($this) ?>
</div> 
</div>

<script type="text/javascript">
window.addEvent('domready',function(){
	if ($('country').value != 'US') { 
		$('region-wrapper').setStyle('display','block');
		$('state-wrapper').setStyle('display','none');
	}
	else if ($('country').value == 'US') { 
		$('region-wrapper').setStyle('display','none');
		$('state-wrapper').setStyle('display','block');
	}
});
</script>