<?php $layer = $this->layer ?>
<?php $params = $layer->params ?>

<!-- LAYER BUTTON -->
<div class="tp-caption  <?php echo ($params->random_transition == 1) ? randomrotate : $params->transition_id ?> tp-resizeme <?php echo (!$params->show_all && !$params->show_desktop) ? 'ynfullslider-hidden-fs' : '' ?> <?php echo (!$params->show_all && !$params->show_tablet) ? 'ynfullslider-hidden-tbl' : '' ?> <?php echo (!$params->show_all && !$params->show_mobile) ? 'ynfullslider-hidden-mb' : '' ?> "
	data-x="<?php echo $this->layer->dimensions->left ?>"
	data-y="<?php echo $this->layer->dimensions->top ?>" 
	data-speed="<?php echo $params->transition_duration ?>"
	data-start="<?php echo $params->transition_delay ?>"
	data-easing="Power3.easeInOut"
	data-elementdelay="0.1"
	data-endelementdelay="0.1"
	style="">
	<a class="ynfullslider_element_button" href="<?php if($params->link_to == ""){echo "javascript:void(0)";}else{echo $params->link_to;} ?>" target="<?php if($params->link_to == ""){echo "_self";}else{echo "_blank";} ?>" 
		 style="background-color: <?php echo $params->{'css_background-color'} ?>; 
				display: block;
				border-radius: <?php echo $params->{'css_border-radius'} ?>px;
				border:<?php echo $params->{'css_border-width'} ?>px solid;
				border-color: <?php echo $params->{'css_border-color'} ?>;
				padding: <?php echo $params->{'padding-top'} ?>px <?php echo $params->{'padding-left'} ?>px;
	"> 
				<?php echo $params->body ?>
	</a>
</div>

