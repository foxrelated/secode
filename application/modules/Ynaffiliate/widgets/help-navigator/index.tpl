<ul class="ynaffiliate_quicklinks_menu">
	<?php foreach($this->items as $item): ?>
	<li>
		<a class = "<?php echo $item -> getIdentity() == $this->active?"active":""?>" href="<?php echo $this->url(array('controller'=>'help','action'=>'detail','id'=> $item -> getIdentity())) ?>"><?php echo $item -> title ?></a>
	</li>
	<?php endforeach; ?>
</ul>
<style type = "text/css">
.active {
	font-weight: bold;
}
</style>