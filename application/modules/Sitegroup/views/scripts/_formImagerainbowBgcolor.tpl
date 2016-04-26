<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowHeader.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/mooRainbow.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/styles/mooRainbow.css');
?> 

<script type="text/javascript">
  window.addEvent('domready', function() { 
    var r = new MooRainbow('myRainbow3', { 
      id: 'myDemo3',
      'startColor': [58, 142, 246],
      'onChange': function(color) {
        $('sitegroup_bg_color').value = color.hex;
      }
    });
  });	
</script>

<?php

echo '
<div id="sitegroup_bg_color-wrapper" class="form-wrapper">
	<div id="sitegroup_bg_color-label" class="form-label">
		<label for="sitegroup_bg_color" class="optional">
			' . $this->translate('Email Body Outer Background') . '
		</label>
	</div>
	<div id="sitegroup_bg_color-element" class="form-element">
		<p class="description">' . $this->translate('Select the background color of the outer area in the email around the mail content. (Click on the rainbow below to choose your color.)') . '</p>
		<input name="sitegroup_bg_color" id="sitegroup_bg_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.bg.color', '#f7f7f7') . ' type="text">
		<input name="myRainbow3" id="myRainbow3" src="application/modules/Sitegroup/externals/images/rainbow.png" link="true" type="image">
	</div>
</div>
'
?>