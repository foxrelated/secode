<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbow4.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
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

	$thumb_bordcolor = '#DDDDDD';
	if($action == 'edit' && is_numeric($advancedslideshow_id)) {
		$advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);
		if(!empty($advancedslideshow->thumb_bordcolor)) {
			$thumb_bordcolor = $advancedslideshow->thumb_bordcolor;
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
		var r = new MooRainbow('myRainbow4', { 
			id: 'myDemo4',
			'startColor': [58, 142, 246],
			'onChange': function(color) { 
				$('thumb_bordcolor').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="thumb_bordcolor-wrapper" class="form-wrapper">
		<div id="thumb_bordcolor-label" class="form-label">
			<label for="thumb_bordcolor" class="optional">
				'.$this->translate('Active border color of thumbnails').'
			</label>
		</div>
		<div id="thumb_bordcolor-element" class="form-element">
			<p class="description">'.$this->translate('Choose the border color of active thumbnails. (Click on the rainbow below to choose your color.)').'</p>
			<input name="thumb_bordcolor" id="thumb_bordcolor" value="'.$thumb_bordcolor.'" type="text">
			<input name="myRainbow4" id="myRainbow4" src="' . $this->layout()->staticBaseUrl . 'application/modules/Advancedslideshow/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>