<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formtransition.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$front = Zend_Controller_Front::getInstance();
	$curr_url = $front->getRequest()->getRequestUri();
	$group_str = explode("/", $curr_url);
	$get_last_key = count($group_str) - 1;
	$tab_value = explode("?", $group_str[$get_last_key]);
	$advancedslideshow_id = $tab_value[0];

	$action = $front->getRequest()->getActionName();

	$transition1 = 'Bounce';
	$transition2 = ':out';
	if($action == 'edit' && is_numeric($advancedslideshow_id)) {
		$advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);

		if(!empty($advancedslideshow->transition1)) {
			$transition1 = $advancedslideshow->transition1;
		}
		if(!empty($advancedslideshow->transition2)) {
			$transition2 = $advancedslideshow->transition2;
		}
	}
?>
<?php

echo '
<div id="transition1-wrapper" class="form-wrapper"><div id="transition1-label" class="form-label"><label for="transition1" class="optional">'.$this->translate("Transition type").'</label></div>
<div id="transition1-element" class="form-element">
<select name="transition1" id="transition1">';

if($transition1 == 'linear')
	echo '<option value="linear" label="Linear" selected = "selected" >'.$this->translate("Linear").'</option>';
else
	echo '<option value="linear" label="Linear" >'.$this->translate("Linear").'</option>';

if($transition1 == 'Quad')
	echo '<option value="Quad" label="Quadratic" selected = "selected" >'.$this->translate("Quadratic").'</option>';
else
	echo '<option value="Quad" label="Quadratic">'.$this->translate("Quadratic").'</option>';

if($transition1 == 'Cubic')
	echo '<option value="Cubic" label="Cubic" selected = "selected" >'.$this->translate("Cubic").'</option>';
else
	echo '<option value="Cubic" label="Cubic">'.$this->translate("Cubic").'</option>';

if($transition1 == 'Quart')
	echo '<option value="Quart" label="Quartic" selected = "selected" >'.$this->translate("Quartic").'</option>';
else
	echo '<option value="Quart" label="Quartic">'.$this->translate("Quartic").'</option>';

if($transition1 == 'Quint')
	echo '<option value="Quint" label="Quintic" selected = "selected" >'.$this->translate("Quintic").'</option>';
else
	echo '<option value="Quint" label="Quintic">'.$this->translate("Quintic").'</option>';

if($transition1 == 'Sine')
	echo '<option value="Sine" label="Sinusoidal" selected = "selected" >'.$this->translate("Sinusoidal").'</option>';
else
	echo '<option value="Sine" label="Sinusoidal">'.$this->translate("Sinusoidal").'</option>';

if($transition1 == 'Expo')
	echo '<option value="Expo" label="Exponential" selected = "selected" >'.$this->translate("Exponential").'</option>';
else
	echo '<option value="Expo" label="Exponential">'.$this->translate("Exponential").'</option>';

if($transition1 == 'Circ')
	echo '<option value="Circ" label="Circular" selected = "selected" >'.$this->translate("Circular").'</option>';
else
	echo '<option value="Circ" label="Circular">'.$this->translate("Circular").'</option>';

if($transition1 == 'Bounce')
	echo '<option value="Bounce" label="Bouncing" selected = "selected" >'.$this->translate("Bouncing").'</option>';
else
	echo '<option value="Bounce" label="Bouncing">'.$this->translate("Bouncing").'</option>';

if($transition1 == 'Back')
	echo '<option value="Back" label="Back" selected = "selected" >'.$this->translate("Back").'</option>';
else
	echo '<option value="Back" label="Back">'.$this->translate("Back").'</option>';

if($transition1 == 'Elastic')
	echo '<option value="Elastic" label="Elastic" selected = "selected" >'.$this->translate("Elastic").'</option>';
else
	echo '<option value="Elastic" label="Elastic">'.$this->translate("Elastic").'</option>';

echo '
</select>

<select name="transition2" id="transition2">';

if($transition2 == ':in')
	echo '<option value=":in" label="easeIn" selected = "selected" >'.$this->translate("easeIn").'</option>';
else
	echo '<option value=":in" label="easeIn">'.$this->translate("easeIn").'</option>';

if($transition2 == ':out')
	echo '<option value=":out" label="easeOut" selected = "selected" >'.$this->translate("easeOut").'</option>';
else
	echo '<option value=":out" label="easeOut">'.$this->translate("easeOut").'</option>';

if($transition2 == ':in:out')
	echo '<option value=":in:out" label="easeInOut" selected = "selected" >'.$this->translate("easeInOut").'</option>';
else
	echo '<option value=":in:out" label="easeInOut">'.$this->translate("easeInOut").'</option>';
echo '
</select></div></div>'

?>