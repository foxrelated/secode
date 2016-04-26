<script src="<?php echo $this->baseUrl()?>/application/modules/Ynfullslider/externals/scripts/jquery.themepunch.plugins.min.js"></script>
<script src="<?php echo $this->baseUrl()?>/application/modules/Ynfullslider/externals/scripts/jquery.themepunch.revolution.min.js"></script>
<link rel="stylesheet" href="<?php echo $this->baseUrl()?>/application/modules/Ynfullslider/externals/styles/navstylechange.css" />
<link rel="stylesheet" href="<?php echo $this->baseUrl()?>/application/modules/Ynfullslider/externals/styles/settings_1.css" />


<?php $params = $this->slider->getParams() ?>
<?php $slide = $this->slide ?>
<?php $layers = $this->layers ?>
<?php $slideParams = $slide->getParams() ?>

<style type="text/css">
	.ynfullslider_slider_preview{
		<?php if($params['background_option'] == 0) : ?> 
			background-color: <?php echo $params['background_color'] ?>; 
		<?php else :?>
			background-image: url('<?php echo $params['background_image_url'] ?>');
		<?php endif; ?>
		background-position: <?php echo $params['background_image_position'] ?>;
		background-repeat: <?php echo $params['background_image_repeat'] ?>;
		background-size: <?php echo $params['background_image_size'] ?>;;
		border-width: <?php echo $params['background_border_width'] ?>px;
		border-style: <?php echo $params['background_border_style'] ?>;
		border-color: <?php echo $params['background_border_color'] ?>;
		padding-top: <?php echo $params['spacing_top'] ?>px ;
		padding-bottom: <?php echo $params['spacing_bottom'] ?>px;
		margin:auto;
	}
	.tp-banner-container{
		margin: auto;
		position: relative;
	}

	.tparrows{
		background-color: <?php echo $params['navigator_color'] ?> !important;
		opacity: 0.7;
	}

	.tparrows:hover{
		background-color: <?php echo $params['navigator_color'] ?> !important;
		opacity: 1;
	}

	.tp-bannershadow{
		bottom: -<?php echo $params['spacing_bottom'] + '60' ?>px !important;
		width: 100% !important;
	}

	.tp-bullets.simplebullets .bullet{
		width: 16px !important;
		height: 16px !important;
		border: none !important;
		background: <?php echo $params['navigator_color'] ?> !important;
		opacity: 0.5;
	}
	.tp-bullets.simplebullets .bullet:hover, .tp-bullets.simplebullets .bullet.selected{
		background: <?php echo $params['navigator_color'] ?> !important;
		opacity: 1;
		border: none !important;
		width: 16px !important;
		height: 16px !important;
	}
	.tp-banner ul{
		overflow: hidden;
	}
	
	#slide_preview_wrapper{
	 	margin: auto;
        width: <?php if(($params['width_option'] == 0) || ($params['width_option'] == 2)) {echo "100%";}else{echo (1140 + $params['background_border_width'] * 2).'px';} ?>;
	}

	.tp-banner-container{
        width: <?php if($params['width_option'] == 2){echo "100%";}else{echo "1140px";} ?>;
        margin: auto;
	}

</style>

<div class="ynfullslider_slider_preview ynfullslider_slider_navigator_<?php echo $params['navigator_id'] ?>">
	<div class="tp-banner-container">
		<div class="tp-banner">
			<ul>
				<!-- SLIDE  -->
				<?php for($i = 0; $i<2; $i++): ?>
				<li fullscreenvideo data-transition="<?php if ($params['random_transition'] == 1){echo 'random';}else{echo $params['transition_id'];}?>" data-saveperformance="on" data-slotamount="5" data-masterspeed="20000" >
					<!-- MAIN IMAGE -->
					<?php if($slideParams['background_option'] == 0) :?>
						<img src="application/modules/Ynfullslider/externals/images/transparent.png" style="background-color:<?php echo $slideParams['slide_background_color'] ?>" >
					<?php endif ?>
					<?php if($slideParams['background_option'] == 1) :?>
						<img src="<?php echo $slideParams['background_image_url'] ?>" alt="slidebg1"  data-bgfit="<?php echo $slideParams['background_size'] ?>" data-bgposition="<?php echo $slideParams['background_position'] ?>" data-bgrepeat="<?php echo $slideParams['background_repeat'] ?>" />
					<?php endif ?>
					<?php if($slideParams['background_option'] == 2) :?>
						<div class="tp-caption <?php if ($params['random_transition'] == 1){echo 'random';}else{echo $params['transition_id'];}?> fullscreenvideo"
						   data-x="0"
						   data-y="0"
						   data-speed="1000"
						   data-start="1100"
						   data-easing="<?php if ($params['random_transition'] == 1){echo 'random';}else{echo $params['transition_id'];}?>"
						   data-autoplay="<?php echo ($slideParams['autoplay'] == 1 ? 'true' : 'false') ?>"
						   data-volume="<?php echo ($slideParams['muted'] == 1 ? 'mute' : '') ?>"
						   data-forceCover="1"
					       data-forcerewind="on"
						   data-aspectratio="16:9">
						 
							<video <?php echo ($slideParams['loop'] == 1 ? 'loop' : '') ?> class="" preload="none" width="100%" height="100%">
							   <source src="<?php echo $slideParams['background_video_url'] ?>" type='video/mp4' />
							   <source src="<?php echo $slideParams['background_video_url'] ?>" type='video/webm' />
							   <source src="<?php echo $slideParams['background_video_url'] ?>" type='video/ogg' />
							</video>
						</div>
					<?php endif ?>
					<!-- LAYERS -->
					<!-- LAYER NR. 1 -->
					<?php foreach($layers as $layer): ?>
						<?php echo $this->partial('_layer_'.$layer->type.'.tpl', 'ynfullslider', array(layer=>$layer)); ?>
					<?php endforeach; ?>
				</li>
				<?php endfor; ?>
			</ul>
		</div>
	</div>
</div>

<script type="text/javascript">
   //Set width for slider
  	jQuery.noConflict();
  	var revapi;
  	jQuery(document).ready(function() {

	   	revapi = jQuery('.tp-banner').revolution({
			delay:<?php echo $params['delay_time'] ?>,
			startwidth: 1140,
			startheight:<?php echo $params['max_height']?>,
			hideThumbs:10,
			startWithSlide:0,
			shuffle:
				<?php 
					if($params['shuffle'] == 1){
						echo '"on"';
					}else{
						echo '"off"';
					}
				?>,

			navigationType:
				<?php  
					if(($params['navigator_id'] == 5)||($params['navigator_id'] == 6)) {
						echo '"bullet"';
					}else{
						echo '"none"';
					}
				?>
			,
			navigationArrows:
				<?php  
					if(($params['navigator_id'] == 5)||($params['navigator_id'] == 6)) {
						echo '"none"';
					}else{
						echo '"solo"';
					}
				?>
			,
			navigationStyle:"round",

			//Manage Bullet Navigator
			navigationHAlign:
				<?php  
					if($params['navigator_id'] == 6) {
						echo '"right"';
					}else{
						echo '"center"';
					}
				?>
			,
			navigationVAlign:"bottom",
			navigationHOffset:15,
			navigationVOffset:0,

			//Manage Arrow Left.
			soloArrowLeftHalign:
				<?php  
					if($params['navigator_id'] == 3) {
						echo '"right"';
					}else{
						echo '"left"';
					}
				?>
			,
			soloArrowLeftValign:
				<?php  
					if($params['navigator_id'] == 3) {
						echo '"bottom"';
					}else{
						echo '"center"';
					}
				?>
			,
			soloArrowLeftHOffset:
				<?php  
					if($params['navigator_id'] == 1) {
						echo '15';
					}elseif($params['navigator_id'] == 3){
						echo '42';
					}
					else{
						echo '0';
					}
				?>
			,
			soloArrowLeftVOffset:0,

			//Manage Arrow Right.
			soloArrowRightHalign:"right",
			soloArrowRightValign:
				<?php  
					if($params['navigator_id'] == 3) {
						echo '"bottom"';
					}else{
						echo '"center"';
					}
				?>
			,
			soloArrowRightHOffset:
				<?php  
					if($params['navigator_id'] == 1) {
						echo '15';
					}else{
						echo '0';
					}
				?>
			,
			soloArrowRightVOffset:0,
		});

	   $$('.ynfullslider_slider_navigator_6 .bullet').each(function(el, index){el.innerHTML = index + 1;})
	});
</script>