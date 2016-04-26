<?php $layer = $this->layer ?>
<?php $params = $layer->params ?>

<!-- LAYER IMAGE -->
<div class="tp-caption tp-resizeme <?php echo ($params->random_transition == 1) ? randomrotate : $params->transition_id ?> <?php echo (!$params->show_all && !$params->show_mobile) ? 'ynfullslider-hidden-mb' : '' ?> <?php echo (!$params->show_all && !$params->show_desktop) ? 'ynfullslider-hidden-fs' : '' ?> <?php echo (!$params->show_all && !$params->show_tablet) ? 'ynfullslider-hidden-tbl' : '' ?>"
	data-x="<?php echo $this->layer->dimensions->left ?>"
	data-y="<?php echo $this->layer->dimensions->top ?>" 
	data-speed="<?php echo $params->transition_duration ?>"
	data-start="<?php echo $params->transition_delay ?>"
	data-easing="Power3.easeInOut"
	data-elementdelay="0.1"
	data-endelementdelay="0.1"
	style="">
	<div class="ynfullslider_element_img" style="">
		<a 
			style="
				display: block;
				min-width: <?php echo $this->layer->dimensions->width ?>px;
			   	min-height: <?php echo $this->layer->dimensions->height ?>px;
			   	background-size: 100% 100%;
			   	background-repeat: no-repeat;
			   	background-position: center;
			   	background-image: url('<?php echo $params->image_path ?>');
			   	border: <?php echo $params->{'css_border-width'} ?>px solid <?php echo $params->{'css_border-color'} ?>;
			   	border-radius: <?php echo $params->{'css_border-radius'} ?>px;
			" 
			href="<?php if($params->link_to == ""){echo "javascript:void(0)";}else{echo $params->link_to;} ?>" target="<?php if($params->link_to == ""){echo "_self";}else{echo "_blank";} ?>" >
		</a>
	</div>
</div>