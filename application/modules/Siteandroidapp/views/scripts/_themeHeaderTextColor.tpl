<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    _themeHeaderTextColor.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$getWebsiteName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
$websiteStr = str_replace(".", "-", $getWebsiteName);
$directoryName = 'android-' . $websiteStr . '-app-builder';
$appBuilderBaseFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/' . $directoryName . '/settings.php';
$colorPrimary = '#ffffff';
if (file_exists($appBuilderBaseFile)) {
    include $appBuilderBaseFile;
    if (isset($appBuilderParams['app_header_text_color']))
        $colorPrimary = $appBuilderParams['app_header_text_color'];
}
?>
<script type="text/javascript">
    function hexcolorTonumbercolor(hexcolor) {
        var hexcolorAlphabets = "0123456789ABCDEF";
        var valueNumber = new Array(3);
        var j = 0;
        if (hexcolor.charAt(0) == "#")
            hexcolor = hexcolor.slice(1);
        hexcolor = hexcolor.toUpperCase();
        for (var i = 0; i < 6; i += 2) {
            valueNumber[j] = (hexcolorAlphabets.indexOf(hexcolor.charAt(i)) * 16) + hexcolorAlphabets.indexOf(hexcolor.charAt(i + 1));
            j++;
        }
        return(valueNumber);
    }

    window.addEvent('domready', function () {

        var r = new MooRainbow('myRainbow4', {
            id: 'myDemo4',
            'startColor': hexcolorTonumbercolor("<?php echo $colorPrimary; ?>"),
            'onChange': function (color) {
                $('app_header_text_color').value = color.hex;
                $('app_header_text_color').style.backgroundColor = color.hex;
            }
        });
//    showfeatured("<?php // echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feature.image', 1)  ?>")
    });
</script>

<?php
echo '
	<div id="app_header_text_color-wrapper" class="form-wrapper">
		<div id="app_header_text_color-label" class="form-label">
			<label for="app_header_text_color" class="optional">
				' . $this->translate('Header Text Color') . ' <span style="color: RED">*</span>
			</label>
		</div>
		<div id="app_header_text_color-element" class="form-element">
                <p class="description">Choose the color for the text in header.<a target="_blank" class="mleft5" title="View Screenshot" href="application/modules/Siteandroidapp/externals/images/app-description-ss.png" target="_blank"> <img src="application/modules/Siteandroidapp/externals/images/eye.png" /></a></p>
			<input onkeyup="changeBGColor()" style="background-color:' . $colorPrimary . ';" name="app_header_text_color" id="app_header_text_color" value=' . $colorPrimary . ' type="text">
			<input name="myRainbow4" id="myRainbow4" src="' . $this->layout()->staticBaseUrl . 'application/modules/Siteandroidapp/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>