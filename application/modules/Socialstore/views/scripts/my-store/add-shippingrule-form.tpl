<?php echo $this->form->setAttrib('class', 'global_form')->render($this) ?>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
<script type="text/javascript">
window.addEvent('domready',function(){
	var cat_val = 0;
	var country_val = 0;
	for( i = 0; i < document.getElementsByName('all_cats').length; i++ ){
		if( document.getElementsByName('all_cats')[i].checked == true ){
			cat_val = document.getElementsByName('all_cats')[i].value;
		}
	}
	
	if(cat_val == '0') {
		document.getElementById('category-wrapper').setStyle('display','block');
	}
	else {
		document.getElementById('category-wrapper').setStyle('display','none');
	}
	for( i = 0; i < document.getElementsByName('all_countries').length; i++ ){
		if( document.getElementsByName('all_countries')[i].checked == true ){
			country_val = document.getElementsByName('all_countries')[i].value;
		}
	}
	if(country_val == '0') {
		document.getElementById('country-wrapper').setStyle('display','block');
	}
	else {
		document.getElementById('country-wrapper').setStyle('display','none');
	}
})
</script>