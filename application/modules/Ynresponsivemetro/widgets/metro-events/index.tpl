<div class="widget-metro-event yn-block">
	<div class="yn-title"><?php echo $this -> translate($this -> title);?><div class="btn_link"><a href="<?php echo $this -> url(array(),'event_general', true);?>"></a></div></div>
	<div class="yn-content wrap_col3">
		<?php $firstItem = $this->paginator -> getItem(0);?>
		<div class="wrap_col3_left">
			<?php $photoUrl = $firstItem -> getPhotoUrl();
			if(!$photoUrl)
			{
				$photoUrl = $this->baseUrl()."/application/modules/Event/externals/images/nophoto_event_thumb_profile.png";;
			}?>
			<div class="event-image" style="background-image:url(<?php echo $photoUrl;?>);" class="cover-img"><?php echo $this->htmlLink($firstItem->getHref()) ?></div>
			<div class="event-title"><a href="<?php echo $firstItem->getHref()?>"><?php echo $firstItem->getTitle(); ?></a></div>
			<div class="event-posted-time"><?php echo $this->timestamp(strtotime($firstItem->creation_date)) ?></div>
			<div class="event-posted-by"><?php echo $this->translate(array('%s guest', '%s guests', $firstItem->member_count), $this->locale()->toNumber($firstItem->member_count)) ?>
		          <?php echo $this->translate('led by %1$s',
		              $this->htmlLink($firstItem->getOwner()->getHref(), $firstItem->getOwner()->getTitle())) ?></div>
			<?php
		        $desc = trim($this->string()->truncate($this->string()->stripTags($firstItem->description), 300));
		        if( !empty($desc) ): ?>
					<div class="event-desc">
			          <?php echo $desc ?>
					</div>
				<?php endif; ?>
		</div>
		<div class="wrap_col3_center">
			<?php $count = 0;
			foreach( $this->paginator as $item ): 
			if($item != $firstItem && $count < 4): 
			$count ++;?>
				<div class="yn-item wrap_col3">
					<?php $photoUrl = $item -> getPhotoUrl("thumb.normal");
						if(!$photoUrl)
						{
							$photoUrl = $this->baseUrl()."/application/modules/Event/externals/images/nophoto_event_thumb_normal.png";
						}?>
					<div class="wrap_col3_left cover-img" style="background-image:url(<?php echo $photoUrl?>)"></div>
					<div class="wrap_col3_center">
						<div class="item-title"><a href="<?php echo $item->getHref()?>"><?php echo $item->getTitle()?></a></div>
						<div class="event-posted-time"><?php echo $this->timestamp(strtotime($item->creation_date)) ?></div>
						<div class="event-posted-by"><?php echo $this->translate(array('%s guest', '%s guests', $item->member_count), $this->locale()->toNumber($item->member_count)) ?>
					          <?php echo $this->translate('led by %1$s',
					              $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ?></div>
					</div>
				</div>
			<?php endif; endforeach; ?>
		</div>
	</div>
</div>