<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<style type="text/css">
.layout_advancedslideshow_fullwidth4_advancedslideshows{
	margin:0 auto;
	width:<?php echo $this->width + 2 ?>px;
}
</style>

<?php if (empty($this->slideshow_status) && $this->level_id == 1):?>
	<div class="tip">
		<span>
			<?php echo $this->translate('Advancedslideshow_widgets warning message', '<a href="'.$this->url(array('module' => 'advancedslideshow', 'controller' => 'slideshows', 'action' => 'create'), 'admin_default').'">', '</a>'); ?>
		</span>
	</div>
<?php return; endif; ?>

<script type="text/javascript">
	var url_base = '<?php echo $this->layout()->staticBaseUrl; ?>';
</script>

<?php if($this->type == 'noob'):
  include APPLICATION_PATH . '/application/modules/Advancedslideshow/views/scripts/_noobSlideshow.tpl';
elseif($this->type == 'flom'): ?>

	<style type="text/css">
		#blindsfullwidth4-cont{width:<?php echo $this->width ?>px;height:<?php echo $this->height ?>px;}
		#blindsfullwidth4{width:<?php echo $this->width ?>px;height:<?php echo $this->height ?>px;}
		<?php if($this->position_caption == 1):?>
			.floom_captionfullwidth4{	bottom: 0px;background: <?php echo $this->colorback_caption; ?>;}
		<?php else: ?>
			.floom_captionfullwidth4{	top: 0px;background: <?php echo $this->colorback_caption; ?>;}
		<?php endif; ?>		

		<?php if($this->caption == 'false'):?>
			.floom_captionfullwidth4{display:none;}
		<?php endif; ?>
	</style>

	<?php include APPLICATION_PATH . '/application/modules/Advancedslideshow/views/scripts/slidesStringFloom.tpl'; ?>

	<script type="text/javascript" charset="utf-8">
		window.addEvent('domready', function(e){	
			
			var slides = [
				<?php echo $floom_var;?>
			];

		$('blindsfullwidth4').floom(slides, {
				slideshowName: 'fullwidth4',
				slidesBase: '<?php echo $floom_path;?>',
				amount: 25,
				interval: <?php echo $this->interval ?>,
				progressbar: <?php echo $this->progressbar ?>,
				sliceFxIn: {
					top: 20
				}
			});
		});
	</script>
		
	<div id="blindsfullwidth4-cont">
		<div id="blindsfullwidth4">
		</div>
	</div>
<?php else: ?>

	<?php include_once APPLICATION_PATH . '/application/modules/Advancedslideshow/views/scripts/admin-slideshows/fullwidth4.tpl'; ?>

	<?php include APPLICATION_PATH . '/application/modules/Advancedslideshow/views/scripts/slidesStringOther.tpl'; ?>

	<?php if($this->type == 'fadd'):?>
		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShowfullwidth4 = new Slideshow('id_advancedslideshow_fullwidth4', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height; ?>, hu: '<?php echo $path; ?>', thumbnails: <?php echo $this->thumb;?>, width: <?php echo $this->width; ?>, titles: <?php echo $this->title?>, random: <?php echo $this->random;?>, slideshowName: 'fullwidth4' });
				});
			//]]>
		</script>
			
	<?php elseif($this->type == 'fold'): ?>

		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShowfullwidth4 = new Slideshow.Fold('id_advancedslideshow_fullwidth4', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, thumbnails: <?php echo $this->thumb;?>, center: false, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', transition: '<?php echo $this->transition; ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, overlap: <?php echo $this->overlap; ?>, random: <?php echo $this->random;?>, slideshowName: 'fullwidth4' });
				});
			//]]>
			
				function noError(){return true;}
				window.onerror = noError;
		</script>
			
	<?php elseif($this->type == 'zndp'): ?>

		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShowfullwidth4 = new Slideshow.KenBurns('id_advancedslideshow_fullwidth4', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, thumbnails: <?php echo $this->thumb;?>, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, slideshowName: 'fullwidth4' });
				});
			//]]>	
		</script>
			
	<?php elseif($this->type == 'push'): ?>
		<style type="text/css">
			/* Overriding the default Slideshow styles in order to achieve a custom effect */
			
			.slideshow-imagesfullwidth4-visible { 
				margin-left: 0;
			}	
			.slideshow-imagesfullwidth4-prev { 
				margin-left: <?php echo $this->width; ?>px;
			}
			.slideshow-imagesfullwidth4-next { 
				margin-left: -<?php echo $this->width; ?>px;
			}
		</style>

		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShowfullwidth4 = new Slideshow('id_advancedslideshow_fullwidth4', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, thumbnails: <?php echo $this->thumb;?>, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', transition: '<?php echo $this->transition; ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, overlap: <?php echo $this->overlap; ?>, random: <?php echo $this->random;?>, slideshowName: 'fullwidth4' });
				});
			//]]>
		</script>
			
	<?php elseif($this->type == 'flas'): ?>

		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShowfullwidth4 = new Slideshow.Flash('id_advancedslideshow_fullwidth4', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, thumbnails: <?php echo $this->thumb;?>, color: ['<?php echo $this->color1; ?>', '<?php echo $this->color2; ?>'], controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, random: <?php echo $this->random;?>, slideshowName: 'fullwidth4' });
				});
			//]]>
		</script>
		
	<?php endif;?>

	<style type="text/css">
		<?php if($this->position_caption == 1):?>
			.slideshow-captionsfullwidth4{background: <?php echo $this->colorback_caption; ?>;bottom: 65px;}		
			.slideshow-thums-off .slideshow-captionsfullwidth4{bottom:0;}
		<?php else: ?>
			.slideshow-captionsfullwidth4{background: <?php echo $this->colorback_caption; ?>;top: 0px;}		
		<?php endif; ?>	

		.slideshow-captionsfullwidth4-hidden {
			height: 0;
			opacity: 0;
		}
		.slideshow-captionsfullwidth4-visible {
			height: 22px;
			opacity: .7;
		}

		.slideshow-controller-hidden { 
			opacity: 0;
		}
		.slideshow-controller-visible {
			opacity: 1;
		}
		.slideshow-thumbnailsfullwidth4-active {
			opacity: 1;
		}
		.slideshow-thumbnailsfullwidth4-inactive {
			opacity: .5;
		}
	</style>

	<?php if($this->thumb == 'true'): ?>
		<?php $height1 = $this->height+65; ?>
		<div id="id_advancedslideshow_fullwidth4" class="slideshow" style="height:<?php echo $height1 ?>px;">
			<?php //if($this->total_ur != 1):?>
				<?php if($this->target == 1):?>
					<span style="display:none;"><a href="" target="_blank"></a></span>
				<?php else: ?>
					<span style="display:none;"><a href="" target="_self"></a></span>
				<?php endif; ?>
			<?php //endif; ?>
		</div>
	<?php else:?>
		<div id="id_advancedslideshow_fullwidth4" class="slideshow slideshow-thums-off">
			<?php //if($this->total_ur != 1):?>
				<?php if($this->target == 1):?>
					<span style="display:none;"><a href="" target="_blank"></a></span>
				<?php else: ?>
					<span style="display:none;"><a href="" target="_self"></a></span>
				<?php endif; ?>
			<?php //endif; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>

<div style="clear:both;"></div>