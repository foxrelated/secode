<table>
<?php foreach($this->deals as $item): ?>
<div>
	<div><strong><?php echo $item->title ?></strong></div>
	<div>
		<span><?php echo $this->view->translate("Value") ?></span>:
		<span><?php echo $this->view->currencyadvgroup($item->value_deal, $item->currency)?></span>
		-
		<span><?php echo $this->view->translate("Discount") ?></span>:
		<span><?php echo $this->view->discount($item->value_deal) ?></span>
		-
		<span><?php echo $this->view->translate("Price") ?></span>:
		<span><?php echo $this->view->currencyadvgroup($item->price,$item->currency)?></span>
	</div>
	<p>
		<span><?php echo $item->address; ?></span>
		<br/>
		<?php if($item->category_id): ?>
		<span><?php echo $this->view->translate("Location")?></span>: <?php echo $item->getLocation(); ?> <br/>
		<?php endif ?>
		<?php if($item->location_id): ?>
		<span><?php echo $this->view->translate("Category")?></span>: <?php echo $item->getCategory(); ?>
		<?php endif ?>		
	</p>
</div>
<?php endforeach; ?>
</table>