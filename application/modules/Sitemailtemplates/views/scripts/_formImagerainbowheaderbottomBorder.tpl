<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowheaderbottomBorder.tpl 2012-6-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
	$template_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('template_id', null);
	$action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
  $header_bottombordercolor = '#cccccc';

	if($action == 'edit' && is_numeric($template_id)) {
		$sitemailtemplates = Engine_Api::_()->getItem('sitemailtemplates_templates', $template_id);
		if(!empty($sitemailtemplates->header_bottomcolor)) {
			$header_bottombordercolor = $sitemailtemplates->header_bottomcolor;
		}
	}
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
    var s = new MooRainbow('myRainbow4', { 
      id: 'myDemo4',
      'startColor': hexcolorTonumbercolor('<?php echo $header_bottombordercolor ?>'),
      'onChange': function(color) {
        $('header_bottomcolor').value = color.hex;
      }
    });
  });

</script>

<?php

echo '
 	<div id="header_bottomcolor-wrapper" class="form-wrapper">
 		<div id="header_bottomcolor-label" class="form-label">
 			<label for="header_bottomcolor" class="optional">
 				' . $this->translate('Email Template Header Bottom Border color') . '
 			</label>
 		</div>
 		<div id="header_bottomcolor-element" class="form-element">
 			<p class="description">' . $this->translate('Select the color of the header bottom border of email template. (Click on the rainbow below to choose your color.)') . '</p>
 			<input name="header_bottomcolor" id="header_bottomcolor" value=' . $header_bottombordercolor . ' type="text">
 			<input name="myRainbow4" id="myRainbow4" src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
 		</div>
 	</div>
 '
?>