<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbow4.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
	window.addEvent('domready', function() { 
		var r = new MooRainbow('myRainbow4', { 
			id: 'myDemo4',
			'startColor': [58, 142, 246],
			'onChange': function(color) {
				$('like_background_haourcolor').value = color.hex;
			}
		});
	});	
</script>
<div id="like_background_haourcolor-wrapper" class="form-wrapper">
		<div id="like_background_haourcolor-label" class="form-label">
			<label for="like_background_haourcolor" class="optional">
				<?php echo $this->translate('Like Button Hover Background');?>
			</label>
		</div>
		<div id="like_text_color-element" class="form-element">
			<p class="description"><?php echo $this->translate('Select the on-hover background color of the Like button. (Click on the rainbow below to choose your color.)');?></p>
			<input name="like_background_haourcolor" id="like_background_haourcolor" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('like.background.haourcolor', '#f1f2f1');?>" type="text">
			<input name="myRainbow4" id="myRainbow4" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitelike/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>