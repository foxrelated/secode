<div class="widget-metro-video-album-info">
	<?php if($this -> total_videos):?>
		<div class="yn-item video" <?php if($this -> video_background_color && $this -> video_background_color != "transparent"):?> style = "background: #<?php echo $this -> video_background_color?>"<?php endif;?>>
			<div class="yn-type-icon" <?php if($this -> video_icon):?> style = "background: url(<?php echo $this -> video_icon;?>) no-repeat center center"<?php endif;?>></div>
			<div class="yn-info">
				<a href="<?php echo $this -> url(array(),'video_general', true)?>">
					<div class="number" <?php if($this -> video_text_color && $this -> video_text_color != "transparent"):?> style = "color: #<?php echo $this -> video_text_color?>"<?php endif;?>><?php echo $this -> total_videos;?></div>
					<div class="name" <?php if($this -> video_text_color && $this -> video_text_color != "transparent"):?> style = "color: #<?php echo $this -> video_text_color?>"<?php endif;?>><?php echo $this -> translate(array("Video ","Videos ", $this -> total_videos), $this -> total_videos)?></div>
				</a>
			</div>
		</div>
	<?php endif;?>
	<?php if($this -> total_albums):?>
	<div class="yn-item album" <?php if($this -> album_background_color && $this -> album_background_color != "transparent"):?> style = "background: #<?php echo $this -> album_background_color?>"<?php endif;?>>
		<div class="yn-type-icon" <?php if($this -> album_icon):?> style = "background: url(<?php echo $this -> album_icon;?>) no-repeat center center"<?php endif;?>></div>
		<div class="yn-info">
			<a href="<?php echo $this -> url(array(),'album_general', true)?>">
				<div class="number" <?php if($this -> album_text_color && $this -> album_text_color != "transparent"):?> style = "color: #<?php echo $this -> album_text_color?>"<?php endif;?>><?php echo $this -> total_albums;?></div>
				<div class="name" <?php if($this -> album_text_color && $this -> album_text_color != "transparent"):?> style = "color: #<?php echo $this -> album_text_color?>"<?php endif;?>><?php echo $this -> translate(array("Album ","Albums ", $this -> total_albums), $this -> total_albums)?></div>
			</a>
		</div>
	</div>
	<?php endif;?>
</div>
<script>
	(function( $ ) {
	  $(function() {
		if ( $.browser.mozilla ) {
			if (navigator.userAgent.toLowerCase().indexOf('firefox') != -1) {
				$('.layout_right').css('margin-top','-16px');
			}
		}
	  });
	})(jQuery);
</script>