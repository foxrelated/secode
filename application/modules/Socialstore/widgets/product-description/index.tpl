<?php 
$this->store = $this->product->getStore();
?>
<ul>
<?php
if ($this->notViewable == 1) :?>
<li>	
	<div class="tip"><span><?php echo $this->translate('You cannot view this product.');?></span></div>
</li>
<?php else: ?>
<li>
<div class="product_detail">
	<h3> <?php echo $this->translate('Description') ?> </h3>
	<?php echo $this->product->body;?>
</div>
</li>
<li>
	<div class="store_comment_block">
		<?php echo $this->action("list", "comment", "core", array("type"=>"social_product", "id"=>$this->product->getIdentity())) ?>	
	</div>
</li>
 
<?php endif;?>
</ul>
