<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbow6.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
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

	$caption_backcolor = '#000000';
	if($action == 'edit' && is_numeric($advancedslideshow_id)) {
		$advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);
		if(!empty($advancedslideshow->caption_backcolor)) {
			$caption_backcolor = $advancedslideshow->caption_backcolor;
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
		var r = new MooRainbow('myRainbow6', { 
			id: 'myDemo6',
			'startColor': [58, 142, 246],
			'onChange': function(color) { 
				$('caption_backcolor').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="caption_backcolor-wrapper" class="form-wrapper">
		<div id="caption_backcolor-label" class="form-label">
			<label for="caption_backcolor" class="optional">
				'.$this->translate('Background color of captions').'
			</label>
		</div>
		<div id="caption_backcolor-element" class="form-element">
			<p class="description">'.$this->translate('Choose the background color of captions. (Click on the rainbow below to choose your color.)').'</p>
			<input name="caption_backcolor" id="caption_backcolor" value="'.$caption_backcolor.'" type="text">
			<input name="myRainbow6" id="myRainbow6" src="' . $this->layout()->staticBaseUrl . 'application/modules/Advancedslideshow/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>