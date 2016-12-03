<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowsignatureBgcolor.tpl 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
	$template_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('template_id', null);
	$action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
  $signature_bgcolor = '#f7f7f7';

	if($action == 'edit' && is_numeric($template_id)) {
		$sitemailtemplates = Engine_Api::_()->getItem('sitemailtemplates_templates', $template_id);
		if(!empty($sitemailtemplates->signature_bgcol)) {
			$signature_bgcolor = $sitemailtemplates->signature_bgcol;
		}
	}
?>

<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/scripts/mooRainbow.js" type="text/javascript"></script>

<?php
	$this->headLink()
			->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Seaocore/externals/styles/mooRainbow.css');
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
    var s = new MooRainbow('myRainbow6', { 
      id: 'myDemo6',
      'startColor': hexcolorTonumbercolor('<?php echo $signature_bgcolor ?>'),
      'onChange': function(color) {
        $('signature_bgcol').value = color.hex;
      }
    });
  });
</script>

<?php

echo '
<div id="signature_bgcol-wrapper" class="form-wrapper">
	<div id="signature_bgcol-label" class="form-label">
		<label for="signature_bgcol" class="optional">
			' . $this->translate('Email Signature Background Color') . '
		</label>
	</div>
	<div id="signature_bgcol-element" class="form-element">
		<p class="description">' . $this->translate('Select the background color of the email signature of the email template. (Click on the rainbow below to choose your color.)') . '</p>
		<input name="signature_bgcol" id="signature_bgcol" value=' . $signature_bgcolor . ' type="text">
		<input name="myRainbow6" id="myRainbow6" src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
	</div>
</div>
'
?>