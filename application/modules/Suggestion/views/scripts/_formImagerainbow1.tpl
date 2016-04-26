<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbow1.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<script src="<?php echo $baseUrl; ?>application/modules/Suggestion/externals/scripts/mooRainbow.js" type="text/javascript"></script>
<?php	
  $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Suggestion/externals/styles/mooRainbow.css'); 
?> 
<script type="text/javascript">
	window.addEvent('domready', function() { 
		var r = new MooRainbow('myRainbow1', { 
			id: 'myDemo1',
			'startColor': [58, 142, 246],
			'onChange': function(color) {
				$('sugg_bg_color').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="sugg_bg_color-wrapper" class="form-wrapper">
		<div id="sugg_bg_color-label" class="form-label">
			<label for="sugg_bg_color" class="optional">
				'.$this->translate('Customize the background of the introduction popup').'
			</label>
		</div>
		<div id="sugg_bg_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the background color of the introduction popup. [Click on the rainbow icon below to choose your color.]').'</p>
			<input name="sugg_bg_color" id="sugg_bg_color" value="' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sugg.bg.color') . '" type="text">
			<input name="myRainbow1" id="myRainbow1" src="' . $baseUrl . 'application/modules/Suggestion/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>
