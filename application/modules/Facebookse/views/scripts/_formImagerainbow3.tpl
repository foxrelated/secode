<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbow3.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
	window.addEvent('domready', function() { 
		var r = new MooRainbow('myRainbow3', { 
			id: 'myDemo3',
			'startColor': [58, 142, 246],
			'onChange': function(color) {
				$('fblike_text_color').value = color.hex;
			}
		});
	});	
</script>
	<div id="fblike_text_color-wrapper" class="form-wrapper">
		<div id="fblike_text_color-label" class="form-label">
			<label for="fblike_text_color" class="optional">
				<?php echo $this->translate('Like Button Text Color');?>
			</label>
		</div>
		<div id="fblike_text_color-element" class="form-element">
			<p class="description"><?php echo $this->translate('Select the text color of the Like button. (Click on the rainbow below to choose your color.)')?></p>
			<input name="fblike_text_color" id="fblike_text_color" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('fblike.text.color', '#666666');?>" type="text">
			<input name="myRainbow3" id="myRainbow3" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Facebookse/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
