<?php $item = $this -> item; ?>
<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$controller = $request -> getControllerName();
?>
<!-- thumbnail -->
<div class="ynvideochannel_list_most_item_content">
	<?php $photoUrl = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_playlist_thumb_icon.png'; ?>

	<div class="ynvideochannel_wrapper" style="background-image: url(<?php echo $photoUrl ?>)">
		<div class="ynvideochannel_grid_playlist_count_video">
			<i class="fa fa-bars"></i>
			<span class="ynvideochannel-number"><?php echo $item->video_count ?></span>
			<span><?php echo $this->translate(array('video', 'videos', $item->video_count)); ?></span>
		</div>
		<div class="ynvideochannel_background_opacity"></div>
		<div class="ynvideochannel_playlist_play video-play-btn">
            <a href="<?php echo $item->getHref() ?>">
                <i class="fa fa-play"></i>
            </a>
        </div>
	</div>

	<div>
		<?php echo $this->partial('_playlist_options.tpl', 'ynvideochannel', array('playlist' => $item)); ?>
	</div>
	
	<!-- stat block, may check to show with count > 0 -->
	<div class="ynvideochannel-playlist-count-video">
		<span class="ynvideochannel-number"><?php echo $this->locale()->toNumber($item->video_count) ?></span>
		<span><?php echo $this->translate(array('video','videos', $item->video_count)); ?></span>
	</div>

	<div class="ynvideochannel_content_padding">
		<div class="ynvideochannel-playlist-title"><?php echo $item ?></div>

		<div class="ynvideochannel-playlist-owner">
			<?php echo $this->translate('by %1$s &middot; %2$s',
				$this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()),
				$this->locale()->todateTime(strtotime($item->creation_date), array('type' => 'date'))) ?>
			<ul>
		</div>

		<!-- get mini videos list -->
		<?php $videos = $item -> getVideos(3) ?>
		<?php if (count($videos)): ?>
			<div class="ynvideochannel-playlist-videos">
				<ul>
					<?php foreach($videos as $video): ?>
						<li>
							<i class="fa fa-angle-right"></i>&nbsp;<?php echo $this->htmlLink($video->getHref(), $video->getTitle(), array('class' => 'ynvideochannel_title_video', 'title' => $video->getTitle())) ?>
							<?php if ($video->duration): ?>
								<?php echo $this->partial('_video_duration.tpl', 'ynvideochannel', array('video' => $video)) ?>
							<?php endif ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif;?>
	</div>
</div>