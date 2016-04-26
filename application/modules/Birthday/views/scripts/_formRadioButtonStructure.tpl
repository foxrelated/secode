<?php

/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: _formRadioButtonStructure.tpl 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<style type="text/css">
	#TB_iframeContent 
	{
		height:400px !important;
		width:400px !important;
	}
</style>
<script type="text/javascript">
  function onClick_radio(id) {
    if(id == 3) {
      alert('<?php echo $this->string()->escapeJavascript($this->translate("This widget will be always visible to members if they have friends with birthdays anytime in the year.")) ?>');
    }
    else {
      alert('<?php echo $this->string()->escapeJavascript($this->translate("This widget will be visible to a member only if he/she has friends with birthday on the current date.")) ?>');
    }
  }
</script>
<?php
$check_value = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthday.widget', 3);
if($check_value == 0)
{
	$check_0 = 'checked';
}
else {
	$check_0 = 'unchecked';
}
if($check_value == 1)
{
	$check_1 = 'checked';
}
else {
	$check_1 = 'unchecked';
}
if($check_value == 2)
{
	$check_2 = 'checked';
}
else {
	$check_2 = 'unchecked';
}
if($check_value == 3)
{
	$check_3 = 'checked';
}
else {
	$check_3 = 'unchecked';
}
?>
<div id="Widget-wrapper" class="form-wrapper">
	<div id="Widget-label" class="form-label">
		<label class="optional" for="Widget"><?php echo $this->translate('Birthdays Widget Format'); ?></label>
	</div>
	<div id="Widget-element" class="form-element">
		<p class="description"><?php echo $this->translate('Select how you want information to be shown in the Birthdays widget. While the Calendar format display makes the widget to be always visible, and shows birthdays in the previous, current and next years, the other formats make the widget to be visible only if there are birthdays in the current date, and show birthdays in the current date with a link to the birthdays listing page. All formats have good-looking tooltips allowing you to easily wish or message the persons having their birthdays.'); ?></p>
		<ul class="form-options-wrapper" style="width:400px;">
		<?php
		echo '<li><div style="float:left;margin-top:2px;"><input ' . $check_0 . ' id="Widget-0" name="birthday_widget" type="radio" value="0" onclick="onClick_radio(0);" ></div>' . '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Birthday/externals/images/4.jpg\');">' . $this->translate('Names only layout') . '</a>';
		
		echo '<li><div style="float:left;margin-top:2px;"><input '.$check_1.' id="Widget-1" name="birthday_widget" type="radio" value="1" onclick="onClick_radio(1);" ></div>' . '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Birthday/externals/images/2.jpg\');">'.$this->translate('Profile pictures only layout') . '</a>';
		
		echo '<li><div style="float:left;margin-top:2px;"><input '.$check_2.' id="Widget-2" name="birthday_widget" type="radio" value="2" onclick="onClick_radio(2);" ></div>' . '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Birthday/externals/images/3.jpg\');">'.$this->translate('Profile pictures and names layout'). '</a>';
		
		echo '<li><div style="float:left;margin-top:2px;"><input '.$check_3.' id="Widget-3" name="birthday_widget" type="radio" value="3" onclick="onClick_radio(3);" ></div>' . '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Birthday/externals/images/1.jpg\');">'.$this->translate('Calendar format'). '</a>';
		?>
		</ul>
	</div>
</div>