<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<h2><?php echo $this->translate('Error!');?></h2>
<p class="description">
	<?php 

		echo $this->response->getMessage();
?>
</p>
<br />
<p>
<?php 
	
$url = $this->order->getPlugin()->getSuccessRedirectUrl();

 echo $this->translate('Click %1$shere%2$s to redirect to your shopping cart', '<a href="'.$url.'">','</a>') ?>
</p>