<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowfooterBorder.tpl 2012-6-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
	$action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
  $template_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('template_id', null);
  $footer_bottomcolor = '#cccccc';

	if($action == 'edit' && is_numeric($template_id)) {
		$sitemailtemplates = Engine_Api::_()->getItem('sitemailtemplates_templates', $template_id);
		if(!empty($sitemailtemplates->footer_bottomcol)) {
			$footer_bottomcolor = $sitemailtemplates->footer_bottomcol;
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
    var s = new MooRainbow('myRainbow2', { 
      id: 'myDemo2',
      'startColor': hexcolorTonumbercolor('<?php echo $footer_bottomcolor ?>'),
      'onChange': function(color) {
        $('footer_bottomcol').value = color.hex;
      }
    });
  });

</script>

<?php

echo '
 	<div id="footer_bottomcol-wrapper" class="form-wrapper">
 		<div id="footer_bottomcol-label" class="form-label">
 			<label for="footer_bottomcol" class="optional">
 				' . $this->translate('Email Template Footer Border Color') . '
 			</label>
 		</div>
 		<div id="footer_bottomcol-element" class="form-element">
 			<p class="description">' . $this->translate('Select the color of the footer border of email template. (Click on the rainbow below to choose your color.)') . '</p>
 			<input name="footer_bottomcol" id="footer_bottomcol" value=' . $footer_bottomcolor . ' type="text">
 			<input name="myRainbow2" id="myRainbow2" src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
 		</div>
 	</div>
 '
?>