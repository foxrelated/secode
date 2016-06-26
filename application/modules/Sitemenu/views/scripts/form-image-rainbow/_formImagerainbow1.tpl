<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbow1.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php
$sitemenu_menu_link_color = '#ffffff' ;
?>
<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemenu/externals/scripts/mooRainbow.js" type="text/javascript"></script>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemenu/externals/styles/mooRainbow.css');
?>	
<script type="text/javascript">
    window.addEvent('domready', function() { 
        var r = new MooRainbow('myRainbow1', { 
            id: 'myDemo1',
            'startColor': ($('sitemenu_menu_link_color').value).hexToRgb(),
            'onChange': function(color) { 
                $('sitemenu_menu_link_color').value = color.hex;
            }
        });
    });	
</script>
<?php echo 
'<div id="sitemenu_menu_link_color-wrapper" class="form-wrapper">
		<div id="sitemenu_menu_link_color-label" class="form-label">
			<label for="sitemenu_menu_link_color" class="optional">Select Menu Link color</label>
		</div>
		<div id="sitemenu_menu_link_color-element" class="form-element">
			<input name="sitemenu_menu_link_color" id="sitemenu_menu_link_color" value="' .$sitemenu_menu_link_color. '" type="text">
			<input name="myRainbow1" id="myRainbow1" src="' .$this->layout()->staticBaseUrl. 'application/modules/Sitemenu/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>';

