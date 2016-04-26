<?php if($this->title) : ?><h3> <?php echo $this->translate($this->title); ?> </h3> <?php endif; ?>
<ul class="mini_product_list">
	<?php foreach($this->items as $item): ?>
		<?php if(Engine_Api::_()->user()->getUser($item->owner_id)->getIdentity()!=0):?>	
			<li>
				<div class="products_mini_img">
					<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
				</div>
				<div class="product_info">
					<div class="product_title"> 
						<?php echo $item ?> 
					</div>
					<?php echo $this->partial('product_browse_info_date.tpl','socialstore' ,array('item'=>$item, 'show_options'=>$this->show_options)) ?>
					<div class="price">
						<?php echo $this->currency($item->getPretaxPrice()) ?>
					</div>
				</div>
				<div style="clear: both"></div>	
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>