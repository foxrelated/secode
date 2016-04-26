<?php
	$key = Engine_Api::_()->socialstore()->getWidgetName($this->identity);
	$viewtype = widgetGetCookie($key);
	if($this->title) : ?><h3> <?php echo $this->translate($this->title); ?> </h3> <?php endif; ?>
<ul id ="ynul-<?php echo $key?>" class="main_store_list <?php echo $viewtype?>">
	<?php 
	foreach($this->items as $item): ?>
	<li>		
		<div class="recent_product_img">
			<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
		</div>
		<div class="store_info">
			<div class="store_title"> <?php echo $item ?></div>
			<div class="store_browse_info_date">
				<?php echo $this->translate('Posted by %s', $item->getOwner()) ?>
	            <?php echo $this->timestamp($item->creation_date) ?>
	            -
	            <?php echo $this->translate(array('%s follow', '%s follows', $item->follow_count), $this->locale()->toNumber($item->follow_count)) ?>
	            -
	            <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
			</div>
			<div class="store_description"> <?php echo $item->getDescription() ?> </div>
			<div class="store_follow">
				<?php echo $this->follow($item);?>
			</div>
		</div>
		<div style="clear: both"></div>
	</li>
	<?php endforeach; ?>
</ul>
<script type="text/javascript"> 
window.addEvent('domready',function(){
	className = '<?php echo $this->className?>';
	key = '<?php echo Engine_Api::_()->socialstore()->getWidgetName($this->identity)?>';
	viewtype = '<?php echo $viewtype?>';
	en4.socialstore.addClass(className, key, viewtype);
});
</script>