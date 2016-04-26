<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formRadioButtonStructure.tpl 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<style type="text/css">
	#TB_iframeContent 
	{
		height:570px !important;		
	}
</style>
<?php
$check_value = Engine_Api::_()->getApi('settings', 'core')->getSetting('userconnection.structure');
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
<div id="structure-wrapper" class="form-wrapper">
	<div id="structure-label" class="form-label">
		<label class="optional" for="structure"><?php echo $this->translate('Position of the Connection Path Widget'); ?></label>
	</div>
	<div id="structure-element" class="form-element">
		<p class="description"><?php echo $this->translate('Select the position for the Connection Path widget which will show the Connection Path between the profile owner and the profile viewer.'); ?></p>
		<ul class="form-options-wrapper" style="width:400px;">
		<?php
		echo '<li><div style="float:left;margin-top:2px;"><input ' . $check_5 . ' id="structure-5" name="userconnection_structure" type="radio" value="5" ></div>' . '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\''. $this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-sidebar-vertical.jpg\');">' . $this->translate('Sidebar-Vertical') . '</a><br><span style="font-size:10px;">[' . $this->translate('For this, please enable the Connection Path widget in the Sidebar of Member Profile page.') . ']</span></li>';
		
		echo '<li><div style="float:left;margin-top:2px;"><input '.$check_6.' id="structure-6" name="userconnection_structure" type="radio" value="6"></div>' . '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'' . $this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-sidebar-vertical-without-images.jpg\');">'.$this->translate('Sidebar - Vertical without Image').'</a><br><span style="font-size:10px;">[' . $this->translate('For this, please enable the Connection Path widget in the Sidebar of Member Profile page.') . ']</span></li>';
		
		echo '<li><div style="float:left;margin-top:2px;"><input '.$check_7.' id="structure-7" name="userconnection_structure" type="radio" value="7"></div>' . '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'' . $this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-sidebar-horizental.jpg\');">'.$this->translate('Sidebar Levelled').'</a><br><span style="font-size:10px;">[' . $this->translate('For this, please enable the Connection Path widget in the Sidebar of Member Profile page.') . ']</span></li>';
		
		echo '<li><div style="float:left;margin-top:2px;"><input '.$check_8.' id="structure-8" name="userconnection_structure" type="radio" value="8"></div>' . '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\''. $this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-profile-tab.jpg\');">'.$this->translate('Profile Tab').'</a><br><span style="font-size:10px;">[' . $this->translate('For this, please enable the Connection Path widget in the Tabbed Blocks area of Member Profile page.') . ']</span></li>';
		?>
		</ul>
	</div>
</div>