<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbow_recommend.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Facebookse/externals/scripts/mooRainbow.js" type="text/javascript"></script>
<?php 
	$baseUrl = $this->layout()->staticBaseUrl;
  $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Facebookse/externals/styles/mooRainbow.css'); 
?> 
<script type="text/javascript">
	window.addEvent('domready', function() { 
		var r = new MooRainbow('myRainbow', { 
			id: 'myDemo',
			'startColor': [58, 142, 246],
			'onChange': function(color) {
				$('widget_border_color').value = color.hex;
			}
		});
	});	
</script>
	<div id="widget_border_color-wrapper" class="form-wrapper">
		<div id="widget_border_color-label" class="form-label">
			<label for="widget_border_color" class="optional">
				<?php echo $this->translate('Border Color');?>
			</label>
		</div>
		<div id="widget_border_color-element" class="form-element">
			<p class="description"><?php echo $this->translate('The border color of the plugin. [Click on the rainbow icon below to select your color.]')?></p>
			<input name="widget_border_color" id="widget_border_color" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('recommendation.border.color', '#f1f2f1')?>" type="text">
			<input name="myRainbow" id="myRainbow" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Facebookse/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>