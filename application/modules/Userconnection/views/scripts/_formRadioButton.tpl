<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formRadioButton.tpl 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$check_value = Engine_Api::_()->getApi('settings', 'core')->getSetting('userconnection.indicators');
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

<div id="indicators-wrapper" class="form-wrapper">
	<div id="indicators-label" class="form-label">
		<label class="optional" for="indicators"><?php echo $this->translate('Connection Level Indicator Color Settings'); ?></label>
	</div>
	<div id="indicators-element" class="form-element">
		<p class="description"><?php echo $this->translate('Select the color of the connection level indicators.'); ?></p>
		<ul class="form-options-wrapper">
		<?php
		echo '<li><div style="float:left;margin-top:2px;"><input '.$check_5.' id="indicators-5" name="userconnection_indicators" type="radio" value="5"></div>' . '<img src="' . $this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/indicatorsYellow.png" width="18" height="18"/></li>';
		
		echo '<li><div style="float:left;margin-top:2px;"><input '.$check_6.' id="indicators-6" name="userconnection_indicators" type="radio" value="6"></div>' . '<img src="' . $this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/indicatorsGreen.png" width="18" height="18"/></li>';
		
		echo '<li><div style="float:left;margin-top:2px;"><input '.$check_7.' id="indicators-7" name="userconnection_indicators" type="radio" value="7"></div>' . '<img src="' . $this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/indicatorsBrown.png" width="18" height="18"/></li>';
		
		echo '<li><div style="float:left;margin-top:2px;"><input '.$check_8.' id="indicators-8" name="userconnection_indicators" type="radio" value="8"></div>' . '<img src="' . $this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/indicatorsBlue.png" width="18" height="18"/></li>';
		?>
		</ul>
	</div>
</div>