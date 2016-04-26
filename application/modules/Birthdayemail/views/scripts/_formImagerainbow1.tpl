<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthdayemail
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: _formImagerainbow1.tpl 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<?php 
	$baseUrl = $this->layout()->staticBaseUrl; 
	$this->headScript()->appendFile($baseUrl . 'application/modules/Birthdayemail/externals/scripts/mooRainbow.js'); 
  $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Birthdayemail/externals/styles/mooRainbow.css'); 
?> 
<script type="text/javascript">
	window.addEvent('domready', function() { 
		var r = new MooRainbow('myRainbow1', { 
			id: 'myDemo1',
			'startColor': [58, 142, 246],
			'onChange': function(color) {
				$('birthdayemail_color').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="birthdayemail_color-wrapper" class="form-wrapper">
		<div id="birthdayemail_color-label" class="form-label">
			<label for="birthdayemail_color" class="optional">
				'.$this->translate('Email Template Header Background Color').'
			</label>
		</div>
		<div id="birthdayemail_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the header background of email template. (Click on the rainbow below to choose your color.)').'</p>
			<input name="birthdayemail_color" id="birthdayemail_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.color', '#79b4d4') . ' type="text">
			<input name="myRainbow1" id="myRainbow1" src="' . $this->layout()->staticBaseUrl . 'application/modules/Birthdayemail/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>
