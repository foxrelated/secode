<?php $layer = $this->layer ?>
<?php $params = $layer->params ?>

<!-- LAYER TEXT -->
<div class="tp-caption tp-resizeme <?php echo ($params->random_transition == 1) ? randomrotate : $params->transition_id ?> <?php echo (!$params->show_all && !$params->show_mobile) ? 'ynfullslider-hidden-mb' : '' ?> <?php echo (!$params->show_all && !$params->show_desktop) ? 'ynfullslider-hidden-fs' : '' ?> <?php echo (!$params->show_all && !$params->show_tablet) ? 'ynfullslider-hidden-tbl' : '' ?>"
	data-x="<?php echo $this->layer->dimensions->left ?>"
	data-y="<?php echo $this->layer->dimensions->top ?>" 
	data-speed="<?php echo $params->transition_duration ?>"
	data-start="<?php echo $params->transition_delay ?>"
	data-easing="Power3.easeInOut"
	data-elementdelay="0.1"
	data-endelementdelay="0.1"
	style="">
	<div class="ynfullslider_element_text" style="background: <?php echo $params->css_background ?>; 
				border-radius: <?php echo $params->{'css_border-radius'} ?>px;
				letter-spacing: <?php echo $params->{'css_letter-spacing'} ?>;
				max-width: <?php echo $this->layer->dimensions->width ?>px; 
				max-height: <?php echo $this->layer->dimensions->height ?>px; 
				min-width: <?php echo $this->layer->dimensions->width ?>px; 
				min-height: <?php echo $this->layer->dimensions->height ?>px;
				">
				<?php echo $params->body ?>
	</div>
</div>