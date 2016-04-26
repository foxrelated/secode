<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: demo.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
	var url_base = '<?php echo $this->layout()->staticBaseUrl; ?>';
  var slidetype =function(advancedslideshow_type){
    if(advancedslideshow_type == 'flom')
			$('advancedslideshow_thumb-wrapper').style.display = 'none';
		else
			$('advancedslideshow_thumb-wrapper').style.display = 'block';
  }
	window.addEvent('domready', function() { 
		var type = '<?php echo $this->type;?>';
                if ($("advancedslideshow_thumb-wrapper")){
		if( type == 'flom')
			$('advancedslideshow_thumb-wrapper').style.display = 'none';
		else
			$('advancedslideshow_thumb-wrapper').style.display = 'block';
                }
	});
</script>

<h2><?php echo $this->translate('Advanced Slideshow Plugin'); ?></h2>

<?php if( count($this->navigation) ): ?>
	<div class='tabs'>
		<?php	echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
	</div>
<?php endif; ?>

<div class='clear'>
	<div class='settings' style="float:left;">
		<?php echo $this->form->render($this) ?>
	</div>
</div>

<div style="float:right">
	<div class="admin_files_wrapper" style="width:510px;">
		<ul class="admin_files">
			<li>
				<?php echo $this->translate('Type:'); 
					if($this->type == 'fadd') echo $this->translate(' Fading');
					if($this->type == 'fold') echo $this->translate(' Fold');
					if($this->type == 'zndp') echo $this->translate(' Zooming & Panning'); 
					if($this->type == 'push') echo $this->translate(' Push');
					if($this->type == 'flas') echo $this->translate(' Flash');
					if($this->type == 'flom') echo $this->translate('  Curtain / Blind'); 
				?>
			</li>
			<?php if($this->type == 'flas'): ?>
				<li>
				<?php echo $this->translate('Color1: '); echo $this->color1; ?>
			<?php endif; ?>
			<?php if($this->type == 'flas'): ?>
				<li>
					<?php echo $this->translate('Color2: '); echo $this->color2; ?>
				</li>	
			<?php endif; ?>
			<?php if($this->type == 'fold'): ?>
				<li>
					<?php echo $this->translate('Transition: '); echo "bounce:out"; ?><br />
				</li>	
			<?php endif; ?>

			<?php if($this->type == 'push'): ?>
				<li>
					<?php echo $this->translate('Transition:'); echo "back:in:out"; ?>
				</li>
			<?php endif; ?>
			<li>   
				<?php echo $this->translate('Width: '); echo $this->width; ?>
			</li>	
			<li>
				<?php echo $this->translate('Height: '); echo $this->height; ?>
			</li>
			<?php if($this->type != 'flom'):?>
				<li>
					<?php echo $this->translate('Delay: '); echo $this->delay; ?>
				</li>
				<li>
					<?php echo $this->translate('Duration: '); echo $this->duration; ?>
				</li>
			<?php else:?>
				<li>
					<?php echo $this->translate('Blinds: '); echo $this->blinds; ?>
				</li>
				<li>
					<?php echo $this->translate('Interval: '); echo $this->interval; ?>
				</li>
			<?php endif; ?>
		</ul>	
	</div>
</div>

<div style="clear:both;margin-top:10px;float:left;">
  <?php if ($this->type == 'noob'): ?>
    <?php

          $image_count = 1;
      $isDemo = true;      
      $caption = '';
    for($tempImgFlag=1; $tempImgFlag<=5; $tempImgFlag++){
      $image_text_var .= '<div class="noob_slidebox" style="width: 938px; height: 275px;">
                          <div class="noob_slidebox_photo">
                          <img src="'.$this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/images/'.$tempImgFlag.'.jpg" alt="" />
                            <div class="noob_slidebox_caption">' . $caption . '</div>            
                          </div>
                       </div>     
                       <div class="noob_slideshow_contents">
                         <h3 style="display:none">
                          <span>' . $image_count++ . '_caption_title:' . $caption . ' _caption_link:</span>
                        </h3>
                      </div>';
    }

     include APPLICATION_PATH . '/application/modules/Advancedslideshow/views/scripts/_noobSlideshow.tpl';
//    $this->headScript()

 elseif($this->type == 'flom'): ?>
	
	<?php 
		$this->headLink()
       ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/styles/floom.css');
	?>

	<?php if(!empty($this->oldversion)): ?>
		<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/scripts/oldversion/floom.js" type="text/javascript" charset="utf-8"></script>
	<?php else: ?>
		<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/scripts/floom.js" type="text/javascript" charset="utf-8"></script>
	<?php endif; ?>

	<style type="text/css">
		#blinds-cont{width:<?php echo $this->width ?>px;height:<?php echo $this->height ?>px;background-color:#E9F4FA;border-color:#D0E2EC;}
		#blinds{width:<?php echo $this->width ?>px;height:<?php echo $this->height ?>px;}
		.floom_caption{	bottom: 0px;background:#FFF; color: #000000;width:918px;}
	</style> 

	<?php 
		$floom_image_count = 0;
		$floom_path = $this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/images/';
	?>
	<script type="text/javascript" charset="utf-8">
		window.addEvent('domready', function(e){	

			var slides = [
				{
					image: '1.jpg',
					url : 'http://www.socialengineaddons.com',
					caption: '<p><h1>Welcome Aboard!</h1></p><p><span style="font-size: small;">Welcome to the SocialEngine demo community at SocialEngineAddOns, featuring our quality plugins.</span></p>'
				},
				{
					image: '2.jpg',
					url : 'http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin',
					caption: '<p><h1>Have fun!</h1></p><p><span style="font-size: small;">Explore content, find friends and stay connected with the happenings around!!</span></p>'
				},
				{
					image: '3.jpg',
					caption: '<p><h1>Explore!</h1></p><p><span style="font-size: small;">Find the best restaurants and places to hang out.</span></p>'
				},
				{
					image: '4.jpg',
					url : 'http://www.socialengineaddons.com/socialengine-people-you-may-know-friend-suggestions-inviter',
					caption: '<p><h1>Connect & Share!</h1></p><p><span style="font-size: small;">Expand your social graph; find the people you know and help friends grow their networks.</span></p>'
				},
				{
					image: '5.jpg',
					caption: '<p><h1>Collaborate!</h1></p><p><span style="font-size: small;">Create & Join groups. Share and collaborate with people having similar interests.</span></p>'
				}
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

	<div id="blinds-cont" style="margin-bottom:0px;">
		<div id="blinds">
		</div>
	</div>

<?php else: ?>
	<?php
		$this->headLink()
   ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/styles/slideshow.css');
	?>	
	<?php include_once APPLICATION_PATH . '/application/modules/Advancedslideshow/views/scripts/admin-slideshows/slideshow.tpl'; ?>

	<?php 
		$var = ''; 
		$image_count = 0;

		$var = "'1.jpg':{caption:'";
		$var .= '<p><h1>Welcome Aboard!</h1></p><p><span style="font-size: small;">Welcome to the SocialEngine demo community at SocialEngineAddOns, featuring our quality plugins.</span></p>';
		$var .= "', href:'http://www.socialengineaddons.com'},";

		$var .= "'2.jpg':{caption:'";
		$var .= '<p><h1>Have fun!</h1></p><p><span style="font-size: small;">Explore content, find friends and stay connected with the happenings around!!</span></p>';
		$var .= "', href:' http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin'},";

		$var .= "'3.jpg':{caption:'";
		$var .= '<p><h1>Explore!</h1></p><p><span style="font-size: small;">Find the best restaurants and places to hang out.</span></p>';
		$var .= "'},";

		$var .= "'4.jpg':{caption:'";
		$var .= '<p><h1>Connect & Share!</h1></p><p><span style="font-size: small;">Expand your social graph; find the people you know and help friends grow their networks.</span></p>';
		$var .= "', href:'http://www.socialengineaddons.com/socialengine-people-you-may-know-friend-suggestions-inviter'},";

		$var .= "'5.jpg':{caption:'";
		$var .= '<p><h1>Collaborate!</h1></p><p><span style="font-size: small;">Create & Join groups. Share and collaborate with people having similar interests.</span></p>';
		$var .= "'}";
		
		$path = $this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/images/';
	?>

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
					var myShow = new Slideshow('id_advancedslideshow_demo', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height; ?>, hu: '<?php echo $path; ?>', thumbnails: <?php echo $this->thumb;?>, width: <?php echo $this->width; ?>, titles: <?php echo $this->title?>, random: <?php echo $this->random;?> });
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
					var myShow = new Slideshow.Fold('id_advancedslideshow_demo', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, center: false, controller: <?php echo $this->controller; ?>, thumbnails: <?php echo $this->thumb;?>, delay: <?php echo $this->delay; ?>, duration: <?php echo $this->duration; ?>, transition: 'bounce:out', height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, overlap: <?php echo $this->overlap; ?>, random: <?php echo $this->random;?> });
				});
			//]]>
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
					var myShow = new Slideshow.KenBurns('id_advancedslideshow_demo', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, thumbnails: <?php echo $this->thumb;?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, random: <?php echo $this->random;?> });
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
					var myShow = new Slideshow('id_advancedslideshow_demo', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, thumbnails: <?php echo $this->thumb;?>, transition: 'back:in:out', duration: <?php echo $this->duration; ?>, height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, overlap: <?php echo $this->overlap; ?>, random: <?php echo $this->random;?> });
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
					var myShow = new Slideshow.Flash('id_advancedslideshow_demo', data, { slide: <?php echo $this->start_index; ?>, captions: <?php echo $this->caption; ?>, color: ['#EC2415', '#7EBBFF'], controller: <?php echo $this->controller; ?>, delay: <?php echo $this->delay; ?>, thumbnails: <?php echo $this->thumb;?>, duration: <?php echo $this->duration; ?>, height: <?php echo $this->height?>, hu: '<?php echo $path; ?>', width: <?php echo $this->width?>, titles: <?php echo $this->title?>, random: <?php echo $this->random;?> });
				});
			//]]>
		</script>
		
	<?php endif;?>

	<style type="text/css">
		.slideshow{background-color:#E9F4FA;border-color:#D0E2EC;}
		.slideshow-captions{background: #FFF;bottom: 65px; color: #000000;}		
		.slideshow-thums-off .slideshow-captions{bottom:0;}		
		.slideshow-thumbnails{width:938px;}

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
		<div id="id_advancedslideshow_demo" class="slideshow" style="height:<?php echo $height1 ?>px;"></div>
	<?php else:?>
		<div id="id_advancedslideshow_demo" class="slideshow slideshow-thums-off"></div>
	<?php endif; ?> 
<?php endif;?>
</div>