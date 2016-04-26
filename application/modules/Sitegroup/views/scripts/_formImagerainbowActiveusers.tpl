<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowActiveuserstpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
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

    var r = new MooRainbow('myRainbow3', {
   
      id: 'myDemo3',
      'startColor':hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.graphuser.color', '#458B00') ?>"),
      'onChange': function(color) {
        $('sitegroup_graphuser_color').value = color.hex;
      }
    });
  });	
</script>

<?php
echo '
	<div id="sitegroup_graphuser_color-wrapper" class="form-wrapper">
		<div id="sitegroup_graphuser_color-label" class="form-label">
			<label for="sitegroup_graphuser_color" class="optional">
				' . $this->translate('Active Users line Color') . '
			</label>
		</div>
		<div id="sitegroup_graphuser_color-element" class="form-element">
			<p class="description">' . $this->translate('Select the color of the line which is used to represent Active Users in the graph.(Click on the rainbow below to choose your color.)') . '</p>
			<input name="sitegroup_graphuser_color" id="sitegroup_graphuser_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.graphuser.color', '#458B00') . ' type="text">
			<input name="myRainbow3" id="myRainbow3" src="application/modules/Sitegroup/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>