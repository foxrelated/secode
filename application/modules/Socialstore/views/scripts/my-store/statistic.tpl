<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>

<div class="layout_middle">
<h3><?php echo $this->translate('Store Statistics')?></h3>
<br />
	<div class="profile_fields">
		<h4><span><?php echo $this->translate('Store Statistics');?></span></h4>
		<ul>
			<li>
				<span><?php echo $this->translate("Summary") ?></span>
				<span>
					<?php echo $this->translate(array('%s follow', '%s follows', $this->store->follow_count), $this->locale()->toNumber($this->store->follow_count)) ?>
					-
					<?php echo $this->translate(array('%s view', '%s views', $this->store->view_count), $this->locale()->toNumber($this->store->view_count)) ?>
					-
					<?php echo $this->translate(array('%s comment', '%s comments', $this->store->comment_count), $this->locale()->toNumber($this->store->comment_count)) ?>					
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Store Rating") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->store->rate_ave)." ".$this->translate('Stars'); ?> 
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Products") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->store->getTotalProduct()) ?> 
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Available Products") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->store->getAvailableProduct()) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Featured Products") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->store->getFeaturedProduct()) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Units Sold") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->store->sold_products) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Income") ?></span>
				<span>
					<?php echo $this->currency($this->store->getTotalAmount()) ?>
				</span>
			</li>
		
			<li>
				<span><?php echo $this->translate("Total Publish Fee") ?></span>
				<span>
					<?php echo $this->currency($this->store->getPublishedFee()) ?> 
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Feature Fee") ?></span>
				<span>
					<?php echo $this->currency($this->store->getFeaturedFee()) ?> 
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Commission Rate") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->store->getCommissionRate()).'%' ?>
				</span>
			</li>
		</ul>
	</div>
</div>