<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbow1.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
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

	$flash_color1 = '#EC2415';
	if($action == 'edit' && is_numeric($advancedslideshow_id)) {
		$advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);
		if(!empty($advancedslideshow->flash_color1)) {
			$flash_color1 = $advancedslideshow->flash_color1;
		}
	}

?>
<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/scripts/mooRainbow.js" type="text/javascript"></script>
<?php
	$this->headLink()
	->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/styles/mooRainbow.css');
?>	
<script type="text/javascript">
	window.addEvent('domready', function() { 
		var r = new MooRainbow('myRainbow1', { 
			id: 'myDemo1',
			'startColor': [58, 142, 246],
			'onChange': function(color) { 
				$('flash_color1').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="flash_color1-wrapper" class="form-wrapper">
		<div id="flash_color1-label" class="form-label">
			<label for="flash_color1" class="optional">
				'.$this->translate('Customize flash color1').'
			</label>
		</div>
		<div id="flash_color1-element" class="form-element">
			<p class="description">'.$this->translate('Select the color1. (Click on the rainbow below to choose your color.)').'</p>
			<input name="flash_color1" id="flash_color1" value="'.$flash_color1.'" type="text">
			<input name="myRainbow1" id="myRainbow1" src="' . $this->layout()->staticBaseUrl . 'application/modules/Advancedslideshow/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>