<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowtaglineTitle.tpl 2012-6-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
  $template_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('template_id', null);
	$action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
  $header_taglinetitlecol = '#ffffff';

	if($action == 'edit' && is_numeric($template_id)) {
		$sitemailtemplates = Engine_Api::_()->getItem('sitemailtemplates_templates', $template_id);
		if(!empty($sitemailtemplates->header_tagcolor)) {
			$header_taglinetitlecol = $sitemailtemplates->header_tagcolor;
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
    var s = new MooRainbow('myRainbow8', { 
      id: 'myDemo8',
      'startColor': hexcolorTonumbercolor('<?php echo $header_taglinetitlecol ?>'),
      'onChange': function(color) {
        $('header_tagcolor').value = color.hex;
      }
    });
  });

</script>

<?php

echo '
 	<div id="header_tagcolor-wrapper" class="form-wrapper">
 		<div id="header_tagcolor-label" class="form-label">
 			<label for="header_tagcolor" class="optional">
 				' . $this->translate('Email Template Header Tag Line Text Color') . '
 			</label>
 		</div>
 		<div id="header_tagcolor-element" class="form-element">
 			<p class="description">' . $this->translate('Select the color of the tag line text of email template. (Click on the rainbow below to choose your color.)') . '</p>
 			<input name="header_tagcolor" id="header_tagcolor" value=' . $header_taglinetitlecol . ' type="text">
 			<input name="myRainbow8" id="myRainbow8" src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
 		</div>
 	</div>
 '
?>