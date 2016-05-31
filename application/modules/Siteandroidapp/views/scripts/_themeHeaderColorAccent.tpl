<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    _themeHeaderColorAccent.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$getWebsiteName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
$websiteStr = str_replace(".", "-", $getWebsiteName);
$directoryName = 'android-' . $websiteStr . '-app-builder';
$appBuilderBaseFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/' . $directoryName . '/settings.php';
$colorPrimary = '#2196F3';
if (file_exists($appBuilderBaseFile)) {
    include $appBuilderBaseFile;
    if (isset($appBuilderParams['app_header_color_accent']))
        $colorPrimary = $appBuilderParams['app_header_color_accent'];
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

        var r = new MooRainbow('myRainbow1', {
            id: 'myDemo1',
            'startColor': hexcolorTonumbercolor("<?php echo $colorPrimary; ?>"),
            'onChange': function (color) {
                $('app_header_color_accent').value = color.hex;
                $('app_header_color_accent').style.backgroundColor = color.hex;
            }
        });
//    showfeatured("<?php // echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feature.image', 1)  ?>")
    });
</script>

<?php
echo '
	<div id="app_header_color_accent-wrapper" class="form-wrapper">
		<div id="app_header_color_accent-label" class="form-label">                
			<label for="app_header_color_accent" class="optional">
				' . $this->translate('Loading Image Color') . ' <span style="color: RED">*</span>
			</label>
		</div>
		<div id="app_header_color_accent-element" class="form-element">		
                <p class="description">Choose the loading image color of your App. [Note: It will also reflect on other places like select boxes, etc.]<a target="_blank" class="mleft5" title="View Screenshot" href="application/modules/Siteandroidapp/externals/images/app-description-ss.png" target="_blank"> <img src="application/modules/Siteandroidapp/externals/images/eye.png" /></a></p>
			<input onkeyup="changeBGColor()" style="background-color:' . $colorPrimary . ';" name="app_header_color_accent" id="app_header_color_accent" value=' . $colorPrimary . ' type="text">
			<input name="myRainbow1" id="myRainbow1" src="' . $this->layout()->staticBaseUrl . 'application/modules/Siteandroidapp/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
    function changeBGColor() {
        $('app_header_color_accent').style.backgroundColor = $('app_header_color_accent').value;
        $('app_header_color_dark').style.backgroundColor = $('app_header_color_dark').value;
        $('app_header_color_primary').style.backgroundColor = $('app_header_color_primary').value;
        $('app_header_text_color').style.backgroundColor = $('app_header_text_color').value;
    }
</script>