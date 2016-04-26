<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowLightBoxFont.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script src="application/modules/Sitegroup/externals/scripts/mooRainbow.js" type="text/javascript"></script>

<?php
	$baseUrl = $this->layout()->staticBaseUrl;
  $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitegroup/externals/styles/mooRainbow.css');
?>
<script type="text/javascript">
	window.addEvent('domready', function() { 
		var s = new MooRainbow('myRainbow2', {
			id: 'myDemo2',
			'startColor': hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.photolightbox.fontcolor','#FFFFFF') ?>"),
			'onChange': function(color) {
				$('sitegroupalbum_photolightbox_fontcolor').value = color.hex;
			}
		});
		
		showphotolightboxFont("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.photolightbox.show', 1) ?>")
		
	});
</script>

<?php
echo '
	<div id="sitegroupalbum_photolightbox_fontcolor-wrapper" class="form-wrapper">
		<div id="sitegroupalbum_photolightbox_fontcolor-label" class="form-label">
			<label for="sitegroupalbum_photolightbox_fontcolor" class="optional">
				'. $this->translate('Photos Lightbox Font Color').'
			</label>
		</div>
		<div id="sitegroupalbum_photolightbox_fontcolor-element" class="form-element">
			<p class="description">'.$this->translate('Select a font color for the text in the lightbox displaying photos. (Click on the rainbow below to choose your color.)').'</p>
			<input name="sitegroupalbum_photolightbox_fontcolor" id="sitegroupalbum_photolightbox_fontcolor" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.photolightbox.fontcolor', '#FFFFFF') . ' type="text">
			<input name="myRainbow2" id="myRainbow2" src="application/modules/Sitegroup/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
	function showphotolightboxFont(option) {
		if(option == 1) {
			$('sitegroupalbum_photolightbox_fontcolor-wrapper').style.display = 'block';
		}
		else {
			$('sitegroupalbum_photolightbox_fontcolor-wrapper').style.display = 'none';
		}
	}
</script>