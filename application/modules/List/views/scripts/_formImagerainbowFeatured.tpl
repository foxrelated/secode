<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: _formImagerainbowFeatured.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>

<script type="text/javascript">
  function hexcolorTonumbercolor(hexcolor) {
		var hexcolorAlphabets = "0123456789ABCDEF";
		var valueNumber = new Array(3);
		var j = 0;
		if(hexcolor.charAt(0) == "#")
		hexcolor = hexcolor.slice(1);
		hexcolor = hexcolor.toUpperCase();
		for(var i=0;i<6;i+=2) {
			valueNumber[j] = (hexcolorAlphabets.indexOf(hexcolor.charAt(i)) * 16) + hexcolorAlphabets.indexOf(hexcolor.charAt(i+1));
			j++;
		}
		return(valueNumber);
	}

	window.addEvent('domready', function() {

		var r = new MooRainbow('myRainbow1', {
    
			id: 'myDemo1',
			'startColor':hexcolorTonumbercolor("<?php echo $settings->getSetting('list.featured.color', '#0cf523') ?>"),
			'onChange': function(color) {
				$('list_featured_color').value = color.hex;
			}
		});
		showfeatured("<?php echo $settings->getSetting('list.feature.image',1)?>")
	});	
</script>

<?php
echo '
	<div id="list_featured_color-wrapper" class="form-wrapper">
		<div id="list_featured_color-label" class="form-label">
			<label for="list_featured_color" class="optional">
				'.$this->translate('Featured Label Color').'
			</label>
		</div>
		<div id="list_featured_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the "FEATURED" labels. (Click on the rainbow below to choose your color.)').'</p>
			<input name="list_featured_color" id="list_featured_color" value=' . $settings->getSetting('list.featured.color', '#0CF523') . ' type="text">
			<input name="myRainbow1" id="myRainbow1" src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
	function showfeatured(option) {
		if(option == 1) {
			$('list_featured_color-wrapper').style.display = 'block';
		}
		else {
			$('list_featured_color-wrapper').style.display = 'none';
		}
	}
</script>