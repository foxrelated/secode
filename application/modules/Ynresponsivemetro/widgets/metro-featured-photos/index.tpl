<div class="widget-metro-feature-photo">
	<ul class="slider">
		<?php foreach( $this->paginator as $item ):
			$href = "javascript:void(0);";
			if($this -> type == 'album')
			{
				$photoUrl = $item -> getPhotoUrl("thumb.main");
				$href = $item->getHref();
			}
			else 
			{
				$photoUrl = $item -> getPhotoUrl("thumb.main", 8);
			}
			if(!$photoUrl)
			{
				$photoUrl = $this->baseUrl()."/application/modules/Ynresponsivemetro/externals/images/nophoto_metro_8.png";
			}?>
		<li class="cover-img" style="background-image:url(<?php echo $photoUrl?>)">
			<?php echo $this->htmlLink($href,'') ?>
			<div class="info">
				<div class="item-info">
					<div class="yn-title"><i class="yn-icon yn-photo-big"></i> <?php echo $this->htmlLink($href, $this->string()->truncate($item->getTitle(), 50)) ?></div>
					<div class="yn-desc"> <?php echo trim($this->string()->truncate($this->string()->stripTags($item->description), 200));?></div>
				</div>
				<?php if($this -> type == 'album'):?>
				<div class="action">
					<span class="photo"><?php echo $item->count();?> <i class="yn-icon yn-photo"></i></span>
					<span class="like"><?php echo $item->like_count;?> <i class="yn-icon yn-like"></i></span>
					<span class="comment"><?php echo $item->comment_count;?> <i class="yn-icon yn-comment"></i></span>
				</div>
				<?php endif;?>
			</div>
			<?php echo $this->htmlLink($href, $this->string()->truncate('', 50)) ?>
		</li>
	<?php endforeach; ?>
	</ul>
</div>
<script>
	(function( $ ) {
	  $(function() {
		var slider = $('.widget-metro-feature-photo .slider').bxSlider({
            minSlides: 1,
            maxSlides: 1,
            slideMargin: 0,
            pager: false,
            moveSlides: 1,
            autoHover: true,
            auto: true,
        });
		$(window).resize(function(){
			slider.reloadSlider();
		});
	  });
	})(jQuery);
</script>