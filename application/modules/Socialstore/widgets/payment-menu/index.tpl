<div id="id-payment-menu" class="payment_menu">
	<div>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-cart","action"=>"index"), "default") ?>" class="<?php echo $this->active_menu=="info"?"active":"" ?> info"><?php echo $this->translate("My Cart") ?></a>
	</div>
	
	<?php if ($this->checkout) : ?>	
	<div>
		<a href="<?php echo $this->url(array("controller"=>"payment","action"=>"shipping-address", "id" => $this->id), "socialstore_extended") ?>" class="<?php echo $this->active_menu=="shipping-address"?"active":"" ?> shipping-address"><?php echo $this->translate("Shipping Info") ?></a>
	</div>
	<div>
		<a href="<?php echo $this->url(array("controller"=>"payment","action"=>"billing-address", "id" => $this->id), "socialstore_extended") ?>" class="<?php echo $this->active_menu=="billing-address"?"active":"" ?> billing-address"><?php echo $this->translate("Billing Info") ?></a>
	</div>
	<div>
		<a href="<?php echo $this->url(array("controller"=>"payment","action"=>"manage-package", "id" => $this->id), "socialstore_extended") ?>" class="<?php echo $this->active_menu=="manage-package"?"active":"" ?> manage-package"><?php echo $this->translate("Manage Package") ?></a>
	</div>
	<div>
		<a href="<?php echo $this->url(array("controller"=>"payment","action"=>"review-order", "id" => $this->id), "socialstore_extended") ?>" class="<?php echo $this->active_menu=="review-order"?"active":"" ?> review-order"><?php echo $this->translate("Review Order") ?></a>
	</div>
	<div>
		<a href="<?php echo $this->url(array("controller"=>"payment","action"=>"process", "id" => $this->id), "socialstore_extended") ?>" class="<?php echo $this->active_menu=="payment-method"?"active":"" ?> payment-method"><?php echo $this->translate("Payment Method") ?></a>
	</div>
	<?php endif;?>
	<?php if ($this->review) : ?>
	<div>
		<a href="" class="<?php echo $this->active_menu=="payment-confirm"?"active":"" ?> my-account"><?php echo $this->translate("Payment Confirm") ?></a> 
	</div>
	<?php endif;?>
</div>	

<style type = "text/css">
.active {
	font-weight: bold;
}
</style>