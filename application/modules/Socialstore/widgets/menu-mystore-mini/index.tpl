<ul class="store_quicklinks_menu">
	<!--  <li>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-store","action"=>"index"), "default") ?>" class="<?php echo $this->active_menu=="info"?"active":"" ?> info"><?php echo $this->translate("General") ?></a>
	</li>-->
	<li>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-store","action"=>"edit-store"), "default") ?>" class="<?php echo $this->active_menu=="edit-store"?"active":"" ?> my-products"><?php echo $this->translate("Edit Store") ?></a>
	</li>
	
	<li>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"store-photo","action"=>"list-photo", 'store_id'=>$this->store_id ), "default") ?>" class="<?php echo $this->active_menu=="edit-photo"?"active":"" ?> edit-photo"><?php echo $this->translate("Edit Photos") ?></a>
	</li>
	
	<li>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-store","action"=>"manage-tax"), "default") ?>" class="<?php echo $this->active_menu=="manage-tax"?"active":"" ?> manage-tax"><?php echo $this->translate("Manage Taxes") ?></a>
	</li>
	<li>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-store","action"=>"custom-categories"), "default") ?>" class="<?php echo $this->active_menu=="custom-categories"?"active":"" ?> custom-categories"><?php echo $this->translate("Manage Categories") ?></a>
	</li>
	<li>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-store","action"=>"my-products"), "default") ?>" class="<?php echo $this->active_menu=="my-products"?"active":"" ?> my-products"><?php echo $this->translate("My Products") ?></a>
	</li>
	<li>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-store","action"=>"create-product","store"=>$this->store_id), "default") ?>" class="<?php echo $this->active_menu=="create-products"?"active":"" ?> create-product"><?php echo $this->translate("Post Product") ?></a>
	</li>
	<li>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-store","action"=>"sold-products"),"default")?>" class="<?php echo $this->active_menu=="sold-products"?"active":"" ?> sold-products"><?php echo $this->translate("Sold Products") ?></a>
	</li>
	<li>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-account","action"=>"index"), "default") ?>" class="<?php echo $this->active_menu=="my-account"?"active":"" ?> my-account"><?php echo $this->translate("My Account") ?></a>
	</li>
	<li>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-store","action"=>"statistic"),"default")?>" class="<?php echo $this->active_menu=="statistic"?"active":"" ?> statistic"><?php echo $this->translate("Store Statistics") ?></a>
	</li>
	<li>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-store","action"=>"product-statistic"),"default")?>" class="<?php echo $this->active_menu=="product-statistic"?"active":"" ?> statistic"><?php echo $this->translate("Product Statistics") ?></a>
	</li>
	<li>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-store","action"=>"shipping-method"),"default")?>" class="<?php echo $this->active_menu=="shipping-method"?"active":"" ?> statistic"><?php echo $this->translate("Shipping Methods") ?></a>
	</li>
	<li>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-store","action"=>"free-shipping"),"default")?>" class="<?php echo $this->active_menu=="free-shipping"?"active":"" ?> statistic"><?php echo $this->translate("Free Shipping") ?></a>
	</li>
	<li>
		<a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-store","action"=>"attribute-set"),"default")?>" class="<?php echo $this->active_menu=="attribute-set"?"active":"" ?> statistic"><?php echo $this->translate("Attributes Sets") ?></a>
	</li>
    <?php
    if(Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection()):
    ?>
	<li>
        <a href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"gda","action"=>"manage-gda"),"default")?>" class="<?php echo $this->active_menu=="manage-gda"?"active":"" ?> manage-gda"><?php echo $this->translate("Deal Requests") ?></a>
    </li>
    <?php endif; ?>
</ul>
<style type = "text/css">
.active {
	font-weight: bold;
}
</style>