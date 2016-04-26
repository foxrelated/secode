<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formRadioButtonArrow.tpl 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$check_value = Engine_Api::_()->getApi('settings', 'core')->getSetting('userconnection.arrow');
if($check_value == 5)
{
	$check_5 = 'checked';
}
else {
	$check_5 = 'unchecked';
}
if($check_value == 6)
{
	$check_6 = 'checked';
}
else {
	$check_6 = 'unchecked';
}
if($check_value == 7)
{
	$check_7 = 'checked';
}
else {
	$check_7 = 'unchecked';
}
if($check_value == 8)
{
	$check_8 = 'checked';
}
else {
	$check_8 = 'unchecked';
}
?>

<div id="arrow-wrapper" class="form-wrapper">
	<div id="arrow-label" class="form-label">
		<label class="optional" for="arrow"><?php echo $this->translate('Connection Arrow Color Settings'); ?></label>
	</div>
	<div id="arrow-element" class="form-element">
		<p class="description"><?php echo $this->translate('Select the color of the connection arrow indicators.'); ?></p>
		<ul class="form-options-wrapper">
		<?php
		echo '<li><div style="float:left;margin-top:2px;"><input '.$check_5.' id="arrow-5" name="userconnection_arrow" type="radio" value="5"></div>' . '<img src="' . $this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-yellow.gif" width="18" height="18"/></li>';
		
		echo '<li><div style="float:left;margin-top:2px;"><input '.$check_6.' id="arrow-6" name="userconnection_arrow" type="radio" value="6"></div>' . '<img src="' . $this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-green.gif" width="18" height="18"/></li>';
		
		echo '<li><div style="float:left;margin-top:2px;"><input '.$check_7.' id="arrow-7" name="userconnection_arrow" type="radio" value="7"></div>' . '<img src="' . $this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-orange.gif" width="18" height="18"/></li>';
		
		echo '<li><div style="float:left;margin-top:2px;"><input '.$check_8.' id="arrow-8" name="userconnection_arrow" type="radio" value="8"></div>' . '<img src="' . $this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-blue.gif" width="18" height="18"/></li>';
		?>
		</ul>
	</div>
</div>