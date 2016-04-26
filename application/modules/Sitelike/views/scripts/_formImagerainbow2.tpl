<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbow2.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
	window.addEvent('domready', function() { 
		var s = new MooRainbow('myRainbow2', { 
			id: 'myDemo2',
			'startColor': [58, 142, 246],
			'onChange': function(color) {
				$('like_haour_color').value = color.hex;
			}
		});
	});
</script>

	<div id="like_haour_color-wrapper" class="form-wrapper">
		<div id="like_haour_color-label" class="form-label">
			<label for="like_haour_color" class="optional">
				<?php echo $this->translate('Like Button Hover Text Color')?>
			</label>
		</div>
		<div id="like_haour_color-element" class="form-element">
			<p class="description"><?php echo $this->translate('Select the on-hover text color of the Like button. (Click on the rainbow below to choose your color.)')?></p>
			<input name="like_haour_color" id="like_haour_color" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('like.haour.color', '#666666')?>"  type="text">
			<input name="myRainbow2" id="myRainbow2" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitelike/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
