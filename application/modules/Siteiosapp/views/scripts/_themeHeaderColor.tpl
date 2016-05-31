<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    _themeHeaderColorPrimary.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$getWebsiteName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
$websiteStr = str_replace(".", "-", $getWebsiteName);
$directoryName = 'ios-' . $websiteStr . '-app-builder';
$appBuilderBaseFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/' . $directoryName . '/settings.php';
$colorPrimary = '#2979FF';
if (file_exists($appBuilderBaseFile)) {
    include $appBuilderBaseFile;
    if (isset($appBuilderParams['app_header_color']))
        $colorPrimary = $appBuilderParams['app_header_color'];
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

        var r = new MooRainbow('myRainbow3', {
            id: 'myDemo3',
            'startColor': hexcolorTonumbercolor("<?php echo $colorPrimary; ?>"),
            'onChange': function (color) {
                $('app_header_color').value = color.hex;
                $('app_header_color').style.backgroundColor = color.hex;
            }
        });
//    showfeatured("<?php // echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feature.image', 1)    ?>")
    });
</script>

<?php
echo '
	<div id="app_header_color-wrapper" class="form-wrapper">
		<div id="app_header_color-label" class="form-label">                
			<label for="app_header_color" class="optional">
				' . $this->translate('App Header\'s Color Code') . ' <span style="color:RED">*</span>
			</label>
		</div>
		<div id="app_header_color-element" class="form-element">	
                <p class="description">This will be the color of the header of your App. We will be using this color as the main one for the theme and branding of your app.</p>
			<input onkeyup="changeBGColor()" style="background-color:' . $colorPrimary . ';" name="app_header_color" id="app_header_color" value=' . $colorPrimary . ' type="text">
			<input name="myRainbow3" id="myRainbow3" src="' . $this->layout()->staticBaseUrl . 'application/modules/Siteiosapp/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
    function changeBGColor() {
        $('app_header_color').style.backgroundColor = $('app_header_color').value;
    }
</script>