<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowBgcolor.tpl 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
	$action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
  $lr_bordercolor = '#cccccc';
	$template_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('template_id', null);
	if($action == 'edit' && is_numeric($template_id)) {
		$sitemailtemplates = Engine_Api::_()->getItem('sitemailtemplates_templates', $template_id);
		if(!empty($sitemailtemplates->lr_bordercolor)) {
			$lr_bordercolor = $sitemailtemplates->lr_bordercolor;
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
    var s = new MooRainbow('myRainbow3', { 
      id: 'myDemo3',
      'startColor': hexcolorTonumbercolor('<?php echo $lr_bordercolor ?>'),
      'onChange': function(color) {
        $('lr_bordercolor').value = color.hex;
      }
    });
  });
</script>

<?php

echo '
<div id="lr_bordercolor-wrapper" class="form-wrapper">
	<div id="lr_bordercolor-label" class="form-label">
		<label for="lr_bordercolor" class="optional">
			' . $this->translate('Email Template Left and Right Border Color') . '
		</label>
	</div>
	<div id="lr_bordercolor-element" class="form-element">
		<p class="description">' . $this->translate('Select the color of the left and right borders of the email template. (Click on the rainbow below to choose your color.)') . '</p>
		<input name="lr_bordercolor" id="lr_bordercolor" value=' . $lr_bordercolor . ' type="text">
		<input name="myRainbow3" id="myRainbow3" src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
	</div>
</div>
'
?>