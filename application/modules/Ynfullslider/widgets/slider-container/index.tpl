<script src="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynfullslider/externals/scripts/jquery-1.9.1.min.js"></script>
<script src="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynfullslider/externals/scripts/jquery.themepunch.plugins.min.js"></script>
<script src="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynfullslider/externals/scripts/jquery.themepunch.revolution.min.js"></script>
<link rel="stylesheet" href="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynfullslider/externals/styles/navstylechange.css" />
<link rel="stylesheet" href="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynfullslider/externals/styles/settings_1.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Passion+One" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Questrial" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Yesteryear" />


<?php $params = $this->slider->getParams() ?>
<?php $slides = $this->slider->getSlides(array(), false) ?>

<style type="text/css">
	#ynfullslider_slider_wrapper_<?php echo $this->identity ?>{
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
		margin-bottom: 40px;
		margin-top: 15px;
	}

	#ynfullslider_slider_wrapper_<?php echo $this->identity ?> .tp-banner-container{
		margin: auto;
		position: relative;
	}

	#ynfullslider_slider_wrapper_<?php echo $this->identity ?> .tparrows{
		background-color: <?php echo $params['navigator_color'] ?> !important;
		opacity: 0.7;
	}

	#ynfullslider_slider_wrapper_<?php echo $this->identity ?> .tparrows:hover{
		background-color: <?php echo $params['navigator_color'] ?> !important;
		opacity: 1;
	}

	#ynfullslider_slider_wrapper_<?php echo $this->identity ?> .tp-bannershadow{
		width: 100% !important;
	}

	#ynfullslider_slider_wrapper_<?php echo $this->identity ?> .tp-bullets.simplebullets .bullet{
		border: none !important;
		background: <?php echo $params['navigator_color'] ?> !important;
		opacity: 0.5;
	}
	#ynfullslider_slider_wrapper_<?php echo $this->identity ?> .tp-bullets.simplebullets .bullet:hover, 
	#ynfullslider_slider_wrapper_<?php echo $this->identity ?> .tp-bullets.simplebullets .bullet.selected{
		background: <?php echo $params['navigator_color'] ?> !important;
		opacity: 1;
		border: none !important;
	}
	#ynfullslider_slider_wrapper_<?php echo $this->identity ?> .tp-banner ul{
		overflow: hidden;
	}
</style>

<div id="ynfullslider_slider_wrapper_<?php echo $this->identity ?>" class="ynfullslider_slider_preview ynfullslider_slider_navigator_<?php echo $params['navigator_id'] ?>">
	<div class="tp-banner-container">
		<div id="ynfullslider_slider_<?php echo $this->identity ?>" class="tp-banner">
			<ul>
				<!-- SLIDE  -->
			 	<?php foreach($slides as $slide): ?>
				<li fullscreenvideo data-transition="<?php if ($params['random_transition'] == 1){echo 'random';}else{echo $params['transition_id'];}?>" data-saveperformance="on" data-slotamount="5" data-masterspeed="<?php echo $params['transition_duration'] ?>" >
					<?php $slideParams = $slide->getParams() ?>
					<!-- MAIN IMAGE -->
					<?php if($slideParams['background_option'] == 0) :?>
						<img src="application/modules/Ynfullslider/externals/images/transparent.png" style="background-color:<?php echo $slideParams['slide_background_color'] ?>" >
					<?php endif ?>
					<?php if($slideParams['background_option'] == 1) :?>
						<img src="application/modules/Ynfullslider/externals/images/transparent.png" data-lazyload="<?php echo $slide->getPhotoUrl() ?>" alt="slidebg1"  data-bgfit="<?php echo $slideParams['background_size'] ?>" data-bgposition="<?php echo $slideParams['background_position'] ?>" data-bgrepeat="<?php echo $slideParams['background_repeat'] ?>" />
					<?php endif ?>
					<?php if($slideParams['background_option'] == 2) :?>
						<div class="tp-caption fade fullscreenvideo"
						   data-x="0"
						   data-y="0"
						   data-speed="1000"
						   data-start="1100"
						   data-easing="easeOutBack"
						   data-autoplay="<?php echo ($slideParams['autoplay'] == 1 ? 'true' : 'false') ?>"
						   data-volume="<?php echo ($slideParams['muted'] == 1 ? 'mute' : '') ?>"
						   data-forceCover="1"
					       data-forcerewind="one"
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
					<?php foreach($slide->getLayers() as $layer): ?>
						<?php echo $this->partial('_layer_'.$layer->type.'.tpl', 'ynfullslider', array(layer=>$layer)); ?>
					<?php endforeach; ?>
				</li>					
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>

<script type="text/javascript">
   //Set width for slider
  	jQuery.noConflict();
  	var revapi;
  	jQuery(document).ready(function() {
		// SLIDER WRAPPER
		var slider_wrapper = jQuery("#ynfullslider_slider_wrapper_<?php echo $this->identity ?>"),

		// WIDGET CONTAINER
			slider_container = slider_wrapper.parents('.layout_ynfullslider_slider_container'),

		// SLIDER IS IN HEADER OR TOP OR IN MAIN AND THEIR ARE NO LEFT, RIGHT
			slider_in_header = slider_container.parents('#global_header,.layout_top').length
				|| (slider_container.parents('.layout_main').length && !jQuery('.layout_left').length && !jQuery('.layout_right').length),

		// SLIDER IN FOOTER OR BOTTOM
			slider_in_footer = slider_container.parents('#global_footer,.layout_bottom').length,

		// PARENT WIDTH TO BE USED IN NORMAL WIDTH CASE
			slider_parent_width = slider_container.parent().width(),

		// GLOBAL CONTENT WIDHT TO BE USED AS SLIDES WIDTH FOR NORMAL WIDTH SLIDES
			global_content_width = jQuery('#global_content,#global_content_simple').width(),
			slider_width = slider_container,
			slide_width = slider_wrapper.children('.tp-banner-container'),
			global_wrapper = jQuery("#global_wrapper");

	   	revapi = jQuery("#ynfullslider_slider_<?php echo $this->identity ?>.tp-banner").revolution({
			delay:<?php echo $params['delay_time'] ?>,
			startwidth:1140,
			startheight:<?php echo $params['max_height'] ?>,
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
			shadow: <?php echo $params['background_shadow_id']; ?>,

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

	   $$('#ynfullslider_slider_wrapper_<?php echo $this->identity ?>.ynfullslider_slider_navigator_6 .bullet').each(function(el, index){el.innerHTML = index + 1;})

		if(slider_in_header == 1){
			if((<?php echo $params['width_option'] ?> == 0) || (<?php echo $params['width_option'] ?> == 2)){
			//full width
			slider_width.css('width','100%');
			slider_container.insertBefore(global_wrapper);
			} else {
				//normal width
				slider_width.css('width',global_content_width + (<?php echo $params['background_border_width'] ?> * 2) + 'px');
			}

			if(<?php echo $params['width_option'] ?> == 2){
				slide_width.css('width', '100%');
			}else{
				slide_width.css('width',global_content_width + 'px');
			}

		} else if(slider_in_footer == 1){
			if((<?php echo $params['width_option'] ?> == 0) || (<?php echo $params['width_option'] ?> == 2)){
			//full width
				slider_width.css('width','100%');
				slider_container.insertAfter(global_wrapper);
			}else{
				//normal width
				slider_width.css('width',global_content_width + (<?php echo $params['background_border_width'] ?> * 2) + 'px');
			}

			if(<?php echo $params['width_option'] ?> == 2){
				slide_width.css('width', '100%');
			}else{
				slide_width.css('width',global_content_width + 'px');
			}

		}else{
			slider_width.css('width',slider_parent_width - (<?php echo $params['background_border_width'] ?> * 2) - 0.01 + 'px');
			slide_width.css('width','100%');
		}

		var sliderHeight = slider_wrapper.children('.tp-banner-container').height();
		var sliderSpacingTop = Math.round(sliderHeight * parseInt(<?php echo $params['spacing_top'] ?>) / parseInt(<?php echo $params['max_height'] ?>));
		var sliderSpacingBottom = Math.round(sliderHeight * parseInt(<?php echo $params['spacing_bottom'] ?>) / parseInt(<?php echo $params['max_height'] ?>));
		slider_wrapper.css('padding-top', sliderSpacingTop);
		slider_wrapper.css('padding-bottom', sliderSpacingBottom);
		slider_wrapper.find('.tp-bannershadow').css('bottom', 0 - sliderSpacingBottom - 60);

		/*----------  CHECK MOBILE VIEW  ----------*/
		if(jQuery('.layout_page_header_ynmobileview').length){
			var mb_slider_container = jQuery('.layout_ynfullslider_slider_container');
			var mb_slider_inheader = slider_container.parents('#global_header').length;
			if(mb_slider_inheader){
				mb_slider_container.insertBefore(jQuery('#global_content'));
				slider_width.css('width','100%');
				slide_width.css('width','100%');
				jQuery('#ynfullslider_slider_wrapper_<?php echo $this->identity ?>').css('margin-bottom','20px');
			}else{
				mb_slider_container.hide();
			}
		}
	});
</script>