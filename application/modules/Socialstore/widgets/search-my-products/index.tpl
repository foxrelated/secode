<div>
  <?php echo $this->form->render($this) ?>
</div>
<script type = "text/javascript">
window.addEvent('domready',function(){
	var count = '<?php echo $this->level?>';
	if (count != '') {
		var route = '<?php echo $this->route?>';
		en4.store.changeCategory($('category_id_' + count),'category_id','Socialstore_Model_DbTable_Storecategories',route);
	}
});
</script>