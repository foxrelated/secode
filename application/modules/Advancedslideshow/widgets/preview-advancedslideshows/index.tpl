<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-08-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var slidetype =function(slideshow_type){
    if(slideshow_type == 'flom')
			$('slideshow_thumb').style.display = 'none';
		else
			$('slideshow_thumb').style.display = 'block';
  }
	window.addEvent('domready', function() { 
		var type = '<?php echo $this->type;?>';
		if( type == 'flom')
			$('slideshow_thumb').style.display = 'none';
		else
			$('slideshow_thumb').style.display = 'block';
	});
</script>
<ul class="slideshow_slides_filter_widget">
	<li>
	  <form id="filter_form" enctype="application/x-www-form-urlencoded" action="" method="post">
	  	<div><b><?php echo $this->translate('Slideshow types available with Slideshow Plugin.');?></b> </div><br />
	  	<div style="float:left;padding:3px 5px 0 0;"><label for="slideshow_type" class="optional">Slideshow type</label></div>
	    <div style="float:left;">
	      <select name="slideshow_type" id="slideshow_type" onChange="javascript:slidetype(this.value);">

					<?php if($this->type == 'fadd'):?>
						<option value="fadd" label="Fading" selected="selected"><?php echo $this->translate("Fading"); ?></option>
					<?php else: ?>
						<option value="fadd" label="Fading" ><?php echo $this->translate("Fading"); ?></option>
					<?php endif; ?>

					<?php if($this->type == 'flom'):?>
						<option value="flom" label="Curtain / Blind" selected="selected"><?php echo $this->translate("Curtain / Blind"); ?></option>
					<?php else: ?>
						<option value="flom" label="Curtain / Blind"><?php echo $this->translate("Curtain / Blind"); ?></option>
					<?php endif; ?>

					<?php if($this->type == 'zndp'):?>
						<option value="zndp" label="Zooming &amp; Panning" selected="selected"><?php echo $this->translate("Zooming & Panning"); ?></option>
					<?php else: ?>
						<option value="zndp" label="Zooming &amp; Panning"><?php echo $this->translate("Zooming & Panning"); ?></option>
					<?php endif; ?>

					<?php if($this->type == 'push'):?>
						<option value="push" label="Push" selected="selected"><?php echo $this->translate("Push"); ?></option>
					<?php else: ?>
						<option value="push" label="Push"><?php echo $this->translate("Push"); ?></option>
					<?php endif; ?>

					<?php if($this->type == 'flas'):?>
						<option value="flas" label="Flash" selected="selected"><?php echo $this->translate("Flash"); ?></option>
					<?php else: ?>
						<option value="flas" label="Flash"><?php echo $this->translate("Flash"); ?></option>
					<?php endif; ?>

					<?php if($this->type == 'fold'):?>
	        <option value="fold" label="Fold" selected="selected"><?php echo $this->translate("Fold"); ?></option>
					<?php else: ?>
					<option value="fold" label="Fold"><?php echo $this->translate("Fold"); ?></option>
					<?php endif; ?>

	      </select>
	     </div> 
			<div style="float:left;width:150px;padding:3px 0 0 50px;" id="slideshow_thumb">
				<input type="hidden" name="slideshow_thumb" value="0" />
				<?php if($this->thumb == 1): ?>
					<input type="checkbox" name="slideshow_thumb" value="1" checked="checked" style="float:left;" />
				<?php else: ?>
					<input type="checkbox" name="slideshow_thumb" value="1" style="float:left;" />
				<?php endif; ?>
	      <label for="slideshow_thumb" class="optional"><?php echo $this->translate("Show thumbnails"); ?></label>
			</div>
			<div style="float:right;">
				<button name="submit" id="submit" type="submit"><?php echo $this->translate("View"); ?></button>
			</div>
			<div style="clear:both;"></div>
	  </form>
  </li>
</ul>
