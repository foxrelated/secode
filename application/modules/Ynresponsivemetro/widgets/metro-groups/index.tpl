<div class="widget-metro-group yn-block">
	<div class="yn-title">
		<?php echo $this -> translate($this -> title)?>
		<div class="btn_link"><a href="<?php echo $this -> url(array(),'group_general', true)?>"></a></div>
	</div>
	<div class="yn-content">
		 <?php foreach( $this->paginator as $item ): 
		 	$photoUrl = $item -> getPhotoUrl("thumb.icon");
			if(!$photoUrl)
			{
				$photoUrl = $this->baseUrl()."/application/modules/Group/externals/images/nophoto_group_thumb_normal.png";
			}
		 	?>
		<div class="yn-item wrap_col3">
			<div class="wrap_col3_left cover-img" style="background-image:url(<?php echo $photoUrl?>)"></div>
			<div class="wrap_col3_center">
				<div class="item-title"><a href="<?php echo $item->getHref()?>"><?php echo $item->getTitle()?></a></div>
				<div class="event-posted-time"><?php echo $this->translate(array('%s member', '%s members', $item->member_count), $this->locale()->toNumber($item->member_count)) ?></div>
				<div class="event-posted-by"><?php echo $this->translate('led by %1$s',
              		$this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ?></div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
</div>