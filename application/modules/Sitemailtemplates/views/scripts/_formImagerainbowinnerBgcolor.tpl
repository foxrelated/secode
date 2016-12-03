<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowouterBgcolor.tpl 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
	$template_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('template_id', null);
	$action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
  $inner_bgcolor = '#ffffff';

	if($action == 'edit' && is_numeric($template_id)) {
		$sitemailtemplates = Engine_Api::_()->getItem('sitemailtemplates_templates', $template_id);
		if(!empty($sitemailtemplates->body_innerbgcol)) {
			$inner_bgcolor = $sitemailtemplates->body_innerbgcol;
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
    var s = new MooRainbow('myRainbow5', { 
      id: 'myDemo5',
      'startColor': hexcolorTonumbercolor('<?php echo $inner_bgcolor ?>'),
      'onChange': function(color) {
        $('body_innerbgcol').value = color.hex;
      }
    });
  });
</script>

<?php

echo '
<div id="body_innerbgcol-wrapper" class="form-wrapper">
	<div id="body_innerbgcol-label" class="form-label">
		<label for="body_innerbgcol" class="optional">
			' . $this->translate('Email Body Inner Background Color') . '
		</label>
	</div>
	<div id="body_innerbgcol-element" class="form-element">
		<p class="description">' . $this->translate('Select the background color of the inner area in the email around the mail content. (Click on the rainbow below to choose your color.)') . '</p>
		<input name="body_innerbgcol" id="body_innerbgcol" value=' . $inner_bgcolor . ' type="text">
		<input name="myRainbow5" id="myRainbow5" src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
	</div>
</div>
'
?>