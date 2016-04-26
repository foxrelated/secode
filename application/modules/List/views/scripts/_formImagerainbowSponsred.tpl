<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: _formImagerainbowSponsored.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php
	$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');

  $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
?>

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>

<script type="text/javascript">
	window.addEvent('domready', function() { 
		var s = new MooRainbow('myRainbow2', { 
			id: 'myDemo2',
			'startColor': hexcolorTonumbercolor("<?php echo $settings->getSetting('list.sponsored.color', '#FC0505') ?>"),
			'onChange': function(color) {
				$('list_sponsored_color').value = color.hex;
			}
		});
		
		showsponsored("<?php echo $settings->getSetting('list.sponsored.image',1)?>")
		
	});
</script>

<?php
echo '
	<div id="list_sponsored_color-wrapper" class="form-wrapper">
		<div id="list_sponsored_color-label" class="form-label">
			<label for="list_sponsored_color" class="optional">
				'. $this->translate('Sponsored Label Color').'
			</label>
		</div>
		<div id="list_sponsored_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the "SPONSORED" labels. (Click on the rainbow below to choose your color.)').'</p>
			<input name="list_sponsored_color" id="list_sponsored_color" value=' . $settings->getSetting('list.sponsored.color', '#FC0505') . ' type="text">
			<input name="myRainbow2" id="myRainbow2" src="'. $this->layout()->staticBaseUrl . 'application/modules/List/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
	function showsponsored(option) {
		if(option == 1) {
			$('list_sponsored_color-wrapper').style.display = 'block';
		}
		else {
			$('list_sponsored_color-wrapper').style.display = 'none';
		}
	}
</script>