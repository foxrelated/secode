<?php 
	$indexing_by = 'creation';
	if(isset($this->indexing_by)){
		$indexing_by =  $this->indexing_by;
	}
?>

<?php if( count($this->items) >  0 ): 
$key = Engine_Api::_()->socialstore()->getWidgetName($this->identity);
$viewtype = widgetGetCookie($key);
?>
<?php if($this->title) : ?><h3> <?php echo $this->translate($this->title); ?> </h3> <?php endif; ?>
<ul id ="ynul-<?php echo $key?>" class="main_store_list <?php echo $viewtype?>">
	<?php foreach($this->items as $item): ?>
		<?php if(Engine_Api::_()->user()->getUser($item->owner_id)->getIdentity() != 0): ?>
	<li>		
		<div class="recent_product_img">
			<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
		</div>
		<div class="store_info">
			<div class="product_title"> <?php echo $item ?></div>
			<?php echo $this->partial('store_browse_info_date.tpl','socialstore' ,array('item'=>$item, 'show_options'=>$this->show_options)) ?>
			<div class="store_description"> <?php echo $item->getDescription() ?> </div>
			<div class="store_follow">
				<?php echo $this->follow($item);?>
			</div>
		</div>
		<div style="clear: both"></div>
	</li>
	<?php endif;?>
	
	<?php endforeach; ?>
</ul>
<?php else: ?>
<div class="tip">
      <span>
        <?php echo $this->translate('There is no store.');?>
      </span>
    </div>
<?php endif;?>
<script type="text/javascript"> 
window.addEvent('domready',function(){
	className = '<?php echo $this->className?>';
	key = '<?php echo Engine_Api::_()->socialstore()->getWidgetName($this->identity)?>';
	viewtype = '<?php echo $viewtype?>';
	en4.socialstore.addClass(className, key, viewtype);
});
</script>