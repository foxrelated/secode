<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: previewedit.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
	var url_base = '<?php echo $this->layout()->staticBaseUrl; ?>';
</script>

<?php if($this->error == 2): ?>
	<style type="text/css">
		.error{
			background-color:#E47C7C;
			border:2px solid #CD6262;
			color:#FFFFFF;
			padding:0.5em 0.75em;
			font-family:arial;
			font-size:13px;
		}
	</style>

	<div class="error">
		<?php echo $this->translate("You have not created any slides for your slideshow. Please create a slide from the ");?><a href='<?php echo "http://".$_SERVER['HTTP_HOST'].  $this->baseUrl()."/admin/advancedslideshow/slides/manage/advancedslideshow_id/".$this->advancedslideshow_id ?>' target="_blank"><?php echo $this->translate("Manage Slides ");?></a><?php echo $this->translate(" section.");?>
		<?php die; ?>
	</div>
<?php endif; ?>
        
  <?php if ($this->type == 'noob'):    
     include APPLICATION_PATH . '/application/modules/Advancedslideshow/views/scripts/_noobSlideshow.tpl';
   endif;
//    $this->headScript()
//            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
    ?>
        
<?php if($this->type == 'flom'): ?>

	<?php 
		$this->headLink()
       ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/styles/floom.css');
	?>

	<style type="text/css">
		#blinds-cont{width:<?php echo $this->width ?>px;height:<?php echo $this->height ?>px;background-color:#E9F4FA;border-color:#D0E2EC;}
		#blinds{width:<?php echo $this->width ?>px;height:<?php echo $this->height ?>px;}
		<?php if($this->position_caption == 1):?>
			.floom_caption{	bottom: 0px;background: <?php echo $this->colorback_caption; ?>;}
		<?php else: ?>
			.floom_caption{	top: 0px;background: <?php echo $this->colorback_caption; ?>;}
		<?php endif; ?>		

		<?php if($this->caption == 'false'):?>
			.floom_caption{display:none;}
		<?php endif; ?>
	</style>

	<?php if(!empty($this->oldversion)): ?>
		<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/scripts/oldversion/floom.js" type="text/javascript" charset="utf-8"></script>
	<?php else: ?>
		<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/scripts/floom.js" type="text/javascript" charset="utf-8"></script>
	<?php endif; ?>

	<?php include APPLICATION_PATH . '/application/modules/Advancedslideshow/views/scripts/slidesStringFloom.tpl'; ?>

	<script type="text/javascript" charset="utf-8">
		window.addEvent('domready', function(e){	

			var slides = [
				<?php echo $floom_var;?>
			];

			$('blinds').floom(slides, {
				slidesBase: '<?php echo $floom_path;?>',
				amount: <?php echo $this->blinds ?>,
				interval: <?php echo $this->interval ?>,
				progressbar: <?php echo $this->progressbar ?>,
				sliceFxIn: {
					top: 20
				}
			});
		});
	</script>
		
	<div id="blinds-cont">
		<div id="blinds">
		</div>
	</div>

<?php else: ?>

	<?php if($this->error == 1): ?>
		<style type="text/css">
			.error{
				background-color:#E47C7C;
				border:2px solid #CD6262;
				color:#FFFFFF;
				padding:0.5em 0.75em;
				font-family:arial;
				font-size:13px;
			}
		</style>

		<div class="error">
			<?php echo $this->translate("The starting index cannot be negative and can at maximum be one less that the number of slides."); die;?>
		</div>
	<?php endif; ?>

	<?php
		$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl .'application/modules/Advancedslideshow/externals/styles/slideshow.css');
	?>	

	<?php include_once APPLICATION_PATH . '/application/modules/Advancedslideshow/views/scripts/admin-slideshows/slideshow.tpl'; ?>	

	<?php include APPLICATION_PATH . '/application/modules/Advancedslideshow/views/scripts/slidesStringOther.tpl'; ?>

	<?php if(!empty($this->oldversion)): ?>
		<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/scripts/oldversion/slideshow.js" type="text/javascript"></script>
	<?php else: ?>
		<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/scripts/slideshow.js" type="text/javascript"></script>
	<?php endif; ?>

	<?php if($this->type == 'fadd'):?>
		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShow = new Slideshow('id_advancedslideshow_previewedit', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height; ?>, hu: '<?php echo $path; ?>', thumbnails: <?php echo $this->thumb;?>, width: <?php echo $this->width; ?>, titles: <?php echo $this->title?>, random: <?php echo $this->random;?> });
				});
			//]]>
		</script>
		
	<?php elseif($this->type == 'fold'): ?>
		
		<?php if(!empty($this->oldversion)): ?>
			<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/scripts/oldversion/slideshow.fold.js" type="text/javascript"></script>
		<?php else: ?>
			<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/scripts/slideshow.fold.js" type="text/javascript"></script>
		<?php endif; ?>

		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShow = new Slideshow.Fold('id_advancedslideshow_previewedit', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, center: false, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, thumbnails: <?php echo $this->thumb;?>, height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', transition: '<?php echo $this->transition ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, overlap: <?php echo $this->overlap; ?>, random: <?php echo $this->random;?> });
				});
			//]]>

				function noError(){return true;}
				window.onerror = noError;
		</script>
		
	<?php elseif($this->type == 'zndp'): ?>

		<?php if(!empty($this->oldversion)): ?>
			<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/scripts/oldversion/slideshow.kenburns.js" type="text/javascript"></script>
		<?php else: ?>
			<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/scripts/slideshow.kenburns.js" type="text/javascript"></script>
		<?php endif; ?>

		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShow = new Slideshow.KenBurns('id_advancedslideshow_previewedit', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', thumbnails: <?php echo $this->thumb;?>, width: <?php echo $this->width?>, titles: <?php echo $this->title?>, random: <?php echo $this->random;?> });
				});
			//]]>
		</script>
		
	<?php elseif($this->type == 'push'): ?>
		<style type="text/css">
			/* Overriding the default Slideshow styles in order to achieve a custom effect */
			
			.slideshow-images-visible { 
				margin-left: 0;
			}	
			.slideshow-images-prev { 
				margin-left: <?php echo $this->width; ?>px;
			}
			.slideshow-images-next { 
				margin-left: -<?php echo $this->width; ?>px;
			}
		</style>

		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShow = new Slideshow('id_advancedslideshow_previewedit', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, transition: '<?php echo $this->transition; ?>', height: <?php echo $this->height?>, thumbnails: <?php echo $this->thumb;?>, hu: '<?php echo $path; ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, overlap: <?php echo $this->overlap; ?>, random: <?php echo $this->random;?> });
				});
			//]]>
		</script>
		
	<?php elseif($this->type == 'flas'): ?>

		<?php if(!empty($this->oldversion)): ?>
			<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/scripts/oldversion/slideshow.flash.js" type="text/javascript"></script>
		<?php else: ?>
			<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/scripts/slideshow.flash.js" type="text/javascript"></script>
		<?php endif; ?>

		<script type="text/javascript">		
			//<![CDATA[
				window.addEvent('domready', function(){
					var data = {
						<?php echo $var;?>
					};
					var myShow = new Slideshow.Flash('id_advancedslideshow_previewedit', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, color: ['<?php echo $this->color1; ?>', '<?php echo $this->color2; ?>'], controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, thumbnails: <?php echo $this->thumb;?>, height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, random: <?php echo $this->random;?> });
				});
			//]]>
		</script>
		
	<?php endif;?>
	<style type="text/css">
		.slideshow{background-color:#E9F4FA;border-color:#D0E2EC;}
		<?php if($this->position_caption == 1):?>
			.slideshow-captions{background: <?php echo $this->colorback_caption; ?>;bottom: 65px;}		
			.slideshow-thums-off .slideshow-captions{bottom:0;}
		<?php else: ?>
			.slideshow-captions{background: <?php echo $this->colorback_caption; ?>;top: 0px;}		
		<?php endif; ?>

		.slideshow-captions-hidden {
			height: 0;
			opacity: 0;
		}
		.slideshow-captions-visible {
			height: 22px;
			opacity: .7;
		}

		.slideshow-controller-hidden { 
			opacity: 0;
		}
		.slideshow-controller-visible {
			opacity: 1;
		}
		.slideshow-thumbnails-active {
			opacity: 1;
		}
		.slideshow-thumbnails-inactive {
			opacity: .5;
		}
	</style>
	<?php if($this->thumb == 'true'): ?>
		<?php $height1 = $this->height+65; ?>
		<div id="id_advancedslideshow_previewedit" class="slideshow" style="height:<?php echo $height1 ?>px;">
			<?php //if($this->total_ur != 1):?>
				<?php if($this->target == 1):?>
					<a href="" target="_blank"></a>
				<?php else: ?>
					<a href="" target="_self"></a>
				<?php endif; ?>
			<?php //endif; ?>
		</div>
	<?php else:?>
		<div id="id_advancedslideshow_previewedit" class="slideshow slideshow-thums-off">
			<?php //if($this->total_ur != 1):?>
				<?php if($this->target == 1):?>
					<a href="" target="_blank"></a>
				<?php else: ?>
					<a href="" target="_self"></a>
				<?php endif; ?>
			<?php //endif; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>