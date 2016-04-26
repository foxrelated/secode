<?php $layer = $this->layer ?>
<?php $params = $layer->params ?>

<!-- LAYER VIDEOS -->
<div class="tp-caption <?php echo ($params->random_transition == 1) ? randomrotate : $params->transition_id ?> tp-resizeme <?php echo (!$params->show_all && !$params->show_mobile) ? 'ynfullslider-hidden-mb' : '' ?> <?php echo (!$params->show_all && !$params->show_desktop) ? 'ynfullslider-hidden-fs' : '' ?> <?php echo (!$params->show_all && !$params->show_tablet) ? 'ynfullslider-hidden-tbl' : '' ?>"
	data-autoplayonlyfirsttime="false"
	data-thumbimage="yourpath/yourimage"
	data-nextslideatend="true"
	data-x="<?php echo $this->layer->dimensions->left ?>"
	data-y="<?php echo $this->layer->dimensions->top ?>" 
	data-speed="<?php echo $params->transition_duration ?>"
	data-start="<?php echo $params->transition_delay ?>"
	data-easing="Power3.easeInOut"
	style="">
	
	<?php if($params->video_type == "youtube") :?>
		<iframe style="border: <?php echo $params->{'css_border-width'} ?>px solid;border-color: <?php echo $params->{'css_border-color'} ?> ;overflow: hidden;border-radius: <?php echo $params->{'css_border-radius'} ?>px;" width="<?php echo $this->layer->dimensions->width ?>px" height="<?php echo $this->layer->dimensions->height ?>px" src="http://www.youtube-nocookie.com/embed/<?php echo $params->youtube_code ?>?controls=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
	<?php else :?>
		<video style="background: #000; border: <?php echo $params->{'css_border-width'} ?>px solid;border-color: <?php echo $params->{'css_border-color'} ?> ;overflow: hidden;border-radius: <?php echo $params->{'css_border-radius'} ?>px;" width="<?php echo $this->layer->dimensions->width ?>px" height="<?php echo $this->layer->dimensions->height ?>px" controls preload="none">
		 
		   <source src="<?php echo $params->video_file_path ?>.mp4" type='video/mp4' />
		   <source src="<?php echo $params->video_file_path ?>.webm" type='video/webm' />
		   <source src="<?php echo $params->video_file_path ?>.ogv" type='video/ogg' />
		</video>
	<?php endif; ?>

</div>