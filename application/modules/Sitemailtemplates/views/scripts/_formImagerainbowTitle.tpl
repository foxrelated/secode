<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowTitle.tpl 2012-6-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
  $template_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('template_id', null);
	$action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
  $header_titlecol = '#ffffff';

	if($action == 'edit' && is_numeric($template_id)) {
		$sitemailtemplates = Engine_Api::_()->getItem('sitemailtemplates_templates', $template_id);
		if(!empty($sitemailtemplates->header_titlecolor)) {
			$header_titlecol = $sitemailtemplates->header_titlecolor;
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
    var s = new MooRainbow('myRainbow10', { 
      id: 'myDemo10',
      'startColor': hexcolorTonumbercolor('<?php echo $header_titlecol ?>'),
      'onChange': function(color) {
        $('header_titlecolor').value = color.hex;
      }
    });
  });

</script>

<?php

echo '
 	<div id="header_titlecolor-wrapper" class="form-wrapper">
 		<div id="header_titlecolor-label" class="form-label">
 			<label for="header_titlecolor" class="optional">
 				' . $this->translate('Email Template Header Title Text Color') . '
 			</label>
 		</div>
 		<div id="header_titlecolor-element" class="form-element">
 			<p class="description">' . $this->translate('Select the color of the header title text of email template. (Click on the rainbow below to choose your color.)') . '</p>
 			<input name="header_titlecolor" id="header_titlecolor" value=' . $header_titlecol . ' type="text">
 			<input name="myRainbow10" id="myRainbow10" src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
 		</div>
 	</div>
 '
?>