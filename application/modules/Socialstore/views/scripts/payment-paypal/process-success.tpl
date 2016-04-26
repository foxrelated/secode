<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<h2>
<?php echo $this->translate('Your purchase has been completed.');?>
</h2>

<p>
<?php 
	$url = $this->order->getPlugin()->getSuccessRedirectUrl();
?>
<?php if($this->order->paytype_id =='publish-product'): ?>
	<?php echo $this->translate('Click %1$shere%2$s to go back to your products page', '<a href="'.$url.'">','</a>') ?>
<?php endif; ?>
<?php if($this->order->paytype_id =='publish-store'): ?>
	<?php echo $this->translate('Click %1$shere%2$s to go back to your store', '<a href="'.$url.'">','</a>') ?>
<?php endif; ?>
<?php if($this->order->paytype_id =='shopping-cart'): ?>
	<?php echo $this->translate('Click %1$shere%2$s to go back to your shopping cart', '<a href="'.$url.'">','</a>') ?>
<?php endif; ?>
</p>

