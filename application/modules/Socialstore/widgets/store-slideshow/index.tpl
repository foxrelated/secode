<?php if( count($this->items) >  0 ): 	?>
<div class="slideshow_container">
			<div id ="slide-runner-widget" class='slideshow'>
			<?php foreach($this->items as $item): ?>
				<?php if(Engine_Api::_()->user()->getUser($item->owner_id)->getIdentity()!=0):?>
					<div class="slide">
						<div class="featured_stores">
							<div class="featured_stores_img_wrapper">
								<div class="featured_stores_img">
								<a href="<?php echo $item->getHref()?>"> 
		                		<img src="<?php echo $item->getPhotoUrl("thumb.profile")?>" />
		                		</a>
								</div>
							</div>
							<div class="store_info">
								<div class="store_title"> <?php echo $item ?></div>
								<?php echo $this->partial('store_browse_info_date.tpl', 'socialstore' ,array('item'=>$item, 'show_options'=>$this->show_options));  ?>
								<p class="store_description"> <?php echo $item->getSlideShowDescription() ?> </p>
								<div class="store_follow">
									<?php echo $this->follow($item);?>
								</div>
							</div>			
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function(){
		var slideWidth = jQuery('.slideshow').width()-20;
	    /* call divSlideShow without parameters */
	    jQuery('.slideshow').divSlideShow({
	    width: slideWidth,
		height:290, 
		loop:1000, 
		arrow:'begin', 
		slideContainerClass: 'control-container',
		controlClass:'slideshow_action', 
		controlActiveClass:'slideshow_action_active'
		});
	});	
</script>
<?php else: ?>
<div class="tip">
      <span>
        <?php echo $this->translate('There is no featured store yet.');?>
      </span>
    </div>
<?php endif;?>