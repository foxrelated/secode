
<?php $params = $this->params ?>

<style type="text/css">
	.ynfullslider_slider_preview{
		<?php if($params['background_option'] == 0) : ?> 
			background-color: <?php echo $params['background_color'] ?>; 
		<?php else :?>
			background-image: url('<?php echo $params['background_image_url'] ?>');
		<?php endif; ?>
				
		background-position: <?php echo $params['background_image_position'] ?>;
		background-repeat: <?php echo $params['background_image_repeat'] ?>;
		background-size: <?php echo $params['background_image_size'] ?>;
		border-width: <?php echo $params['background_border_width'] ?>px;
		border-style: <?php echo $params['background_border_style'] ?>;
		border-color: <?php echo $params['background_border_color'] ?>;
		padding-top: <?php echo $params['spacing_top'] ?>px ;
		padding-bottom: <?php echo $params['spacing_bottom'] ?>px;
		width: <?php if(($params['width_option'] == 0) || ($params['width_option'] == 2)) {echo "100%";}else{echo "960px";} ?>;
		margin:auto;
		margin-bottom: 40px;
	}
	.tp-banner-container{
		width: <?php if($params['width_option'] == 2){echo "100%";}else{echo "960px";} ?>;
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
</style>

<div class="ynfullslider_slider_preview ynfullslider_slider_navigator_<?php echo $params['navigator_id'] ?>">
	<div class="tp-banner-container">
		<div class="tp-banner">
			<ul>
				<!-- SLIDE  -->
			 	<?php for($i = 1; $i<5; $i++): ?>
				<li data-transition="<?php if ($params['random_transition'] == 1){echo 'random';}else{echo $params['transition_id'];}?>" data-slotamount="5" data-masterspeed="<?php echo $params['transition_duration'] ?>" >
					<!-- MAIN IMAGE -->
					<img src="application/modules/Ynfullslider/externals/images/bg<?php echo $i ?>.jpg"  alt="slidebg1"  data-bgfit="cover" data-bgposition="center" data-bgrepeat="no-repeat">
					<!-- LAYERS -->
					<!-- LAYER NR. 1 -->
				</li>
				<?php endfor ?>
			</ul>
		</div>
	</div>
</div>

<script type="text/javascript">
	jQuery.noConflict();
	var revapi;
	jQuery(document).ready(function() {
	   revapi = jQuery('.tp-banner').revolution({
			delay:<?php echo $params['delay_time'] ?>,
			startwidth:960,
			startheight:<?php echo $params['max_height'] ?>,
			hideThumbs:10,
			startWithSlide:2,
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

	   $$('.ynfullslider_slider_navigator_6 .bullet').each(function(el, index){el.innerHTML = index + 1;})
	});
</script>