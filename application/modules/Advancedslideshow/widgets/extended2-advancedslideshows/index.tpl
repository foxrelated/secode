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

	<?php 
		$this->headLink()
       ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/styles/floom.css');
	?>

	<style type="text/css">
		#blindsextended2-cont{width:<?php echo $this->width ?>px;height:<?php echo $this->height ?>px;}
		#blindsextended2{width:<?php echo $this->width ?>px;height:<?php echo $this->height ?>px;}
		<?php if($this->position_caption == 1):?>
			.floom_captionextended2{	bottom: 0px;background: <?php echo $this->colorback_caption; ?>;}
		<?php else: ?>
			.floom_captionextended2{	top: 0px;background: <?php echo $this->colorback_caption; ?>;}
		<?php endif; ?>		

		<?php if($this->caption == 'false'):?>
			.floom_captionextended2{display:none;}
		<?php endif; ?>
	</style>

	<?php if(!empty($this->oldversion)): ?>
		<?php
			$this->headScript()
				->appendFile($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/oldversion/floom.js');
		?>
	<?php else: ?>
		<?php
			$this->headScript()
				->appendFile($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/floom.js');
		?>
	<?php endif; ?>

	<?php include APPLICATION_PATH . '/application/modules/Advancedslideshow/views/scripts/slidesStringFloom.tpl'; ?>

	<script type="text/javascript" charset="utf-8">
		window.addEvent('domready', function(e){	
			
			var slides = [
				<?php echo $floom_var;?>
			];

		$('blindsextended2').floom(slides, {
				slideshowName: 'extended2',
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
		
	<div id="blindsextended2-cont">
		<div id="blindsextended2">
		</div>
	</div>
<?php else: ?>

	<?php 
		 $this->headLink()
   ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/styles/slideshow.css');
	?>

	<?php include_once APPLICATION_PATH . '/application/modules/Advancedslideshow/views/scripts/admin-slideshows/extended2.tpl'; ?>

	<?php include APPLICATION_PATH . '/application/modules/Advancedslideshow/views/scripts/slidesStringOther.tpl'; ?>

		<?php if(!empty($this->oldversion)): ?>
			<?php
				$this->headScript()
					->appendFile($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/oldversion/slideshow.js');
			?>
		<?php else: ?>
			<?php
				$this->headScript()
					->appendFile($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/slideshow.js');
			?>
		<?php endif; ?>

	<?php if($this->type == 'fadd'):?>
		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShowextended2 = new Slideshow('id_advancedslideshow_extended2', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height; ?>, hu: '<?php echo $path; ?>', thumbnails: <?php echo $this->thumb;?>, width: <?php echo $this->width; ?>, titles: <?php echo $this->title?>, random: <?php echo $this->random;?>, slideshowName: 'extended2' });
				});
			//]]>
		</script>
			
	<?php elseif($this->type == 'fold'): ?>

		<?php if(!empty($this->oldversion)): ?>
			<?php
				$this->headScript()
					->appendFile($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/oldversion/slideshow.fold.js');
			?>
		<?php else: ?>
			<?php
				$this->headScript()
					->appendFile($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/slideshow.fold.js');
			?>
		<?php endif; ?>

		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShowextended2 = new Slideshow.Fold('id_advancedslideshow_extended2', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, thumbnails: <?php echo $this->thumb;?>, center: false, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', transition: '<?php echo $this->transition; ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, overlap: <?php echo $this->overlap; ?>, random: <?php echo $this->random;?>, slideshowName: 'extended2' });
				});
			//]]>
			
				function noError(){return true;}
				window.onerror = noError;
		</script>
			
	<?php elseif($this->type == 'zndp'): ?>

		<?php if(!empty($this->oldversion)): ?>
			<?php
				$this->headScript()
					->appendFile($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/oldversion/slideshow.kenburns.js');
			?>
		<?php else: ?>
			<?php
				$this->headScript()
					->appendFile($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/slideshow.kenburns.js');
			?>
		<?php endif; ?>

		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShowextended2 = new Slideshow.KenBurns('id_advancedslideshow_extended2', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, thumbnails: <?php echo $this->thumb;?>, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, slideshowName: 'extended2' });
				});
			//]]>	
		</script>
			
	<?php elseif($this->type == 'push'): ?>
		<style type="text/css">
			/* Overriding the default Slideshow styles in order to achieve a custom effect */
			
			.slideshow-imagesextended2-visible { 
				margin-left: 0;
			}	
			.slideshow-imagesextended2-prev { 
				margin-left: <?php echo $this->width; ?>px;
			}
			.slideshow-imagesextended2-next { 
				margin-left: -<?php echo $this->width; ?>px;
			}
		</style>

		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShowextended2 = new Slideshow('id_advancedslideshow_extended2', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, thumbnails: <?php echo $this->thumb;?>, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', transition: '<?php echo $this->transition; ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, overlap: <?php echo $this->overlap; ?>, random: <?php echo $this->random;?>, slideshowName: 'extended2' });
				});
			//]]>
		</script>
			
	<?php elseif($this->type == 'flas'): ?>

		<?php if(!empty($this->oldversion)): ?>
			<?php
				$this->headScript()
					->appendFile($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/oldversion/slideshow.flash.js');
			?>
		<?php else: ?>
			<?php
				$this->headScript()
					->appendFile($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/slideshow.flash.js');
			?>
		<?php endif; ?>

		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShowextended2 = new Slideshow.Flash('id_advancedslideshow_extended2', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, thumbnails: <?php echo $this->thumb;?>, color: ['<?php echo $this->color1; ?>', '<?php echo $this->color2; ?>'], controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, random: <?php echo $this->random;?>, slideshowName: 'extended2' });
				});
			//]]>
		</script>
		
	<?php endif;?>

	<style type="text/css">
		<?php if($this->position_caption == 1):?>
			.slideshow-captionsextended2{background: <?php echo $this->colorback_caption; ?>;bottom: 65px;}		
			.slideshow-thums-off .slideshow-captionsextended2{bottom:0;}
		<?php else: ?>
			.slideshow-captionsextended2{background: <?php echo $this->colorback_caption; ?>;top: 0px;}		
		<?php endif; ?>	

		.slideshow-captionsextended2-hidden {
			height: 0;
			opacity: 0;
		}
		.slideshow-captionsextended2-visible {
			height: 22px;
			opacity: .7;
		}

		.slideshow-controller-hidden { 
			opacity: 0;
		}
		.slideshow-controller-visible {
			opacity: 1;
		}
		.slideshow-thumbnailsextended2-active {
			opacity: 1;
		}
		.slideshow-thumbnailsextended2-inactive {
			opacity: .5;
		}
	</style>

	<?php if($this->thumb == 'true'): ?>
		<?php $height1 = $this->height+65; ?>
		<div id="id_advancedslideshow_extended2" class="slideshow" style="height:<?php echo $height1 ?>px;">
			<?php //if($this->total_ur != 1):?>
				<?php if($this->target == 1):?>
					<span style="display:none;"><a href="" target="_blank"></a></span>
				<?php else: ?>
					<span style="display:none;"><a href="" target="_self"></a></span>
				<?php endif; ?>
			<?php //endif; ?>
		</div>
	<?php else:?>
		<div id="id_advancedslideshow_extended2" class="slideshow slideshow-thums-off">
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