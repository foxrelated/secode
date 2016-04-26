<?php 
	$indexing_by = 'creation';
	if(isset($this->indexing_by)){
		$indexing_by =  $this->indexing_by;
	}
?>

<?php if($this->title): ?>
<h3><?php echo $this -> translate($this -> title);?></h3>

<?php endif;?>
<ul class="mini_store_list">
	<?php foreach($this->items as $item): ?>
		<?php if(Engine_Api::_()->user()->getUser($item->owner_id)->getIdentity() != 0): ?>
			<li>
				<div class="store_mini_img">
					<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
				</div>
				<div class="store_info">
					<div class="store_title"> <?php echo $item ?> </div>
					<?php echo $this->partial('store_browse_info_date.tpl','socialstore',array('item'=>$item, 'show_options'=>$this->show_options)) ?>
				</div>
				<div style="clear: both"></div>	
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>