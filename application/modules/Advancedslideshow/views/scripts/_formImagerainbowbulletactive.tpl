<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowbulletactive.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$front = Zend_Controller_Front::getInstance();    
    $advancedslideshow_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('advancedslideshow_id', null);

	$action = $front->getRequest()->getActionName();

	$noob_bulletactivecolor = '#808080'; // Give RED Color Number.
	if($action == 'edit' && !empty($advancedslideshow_id)) {
		$advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);
		$noob_elements = @unserialize($advancedslideshow->noob_elements);
        if(!empty($noob_elements['noob_bulletactivecolor'])) {
			$noob_bulletactivecolor = $noob_elements['noob_bulletactivecolor'];
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
		var r = new MooRainbow('noobbulletactivecolor', { 
			id: 'myDemoBulletactive',
			'startColor': [58, 142, 246],
			'onChange': function(color) { 
				$('noob_bulletactivecolor').value = color.hex;
                
			}
		});
	});	
    
</script>

<?php
echo '
	<div id="noob_bulletactivecolor-wrapper" class="form-wrapper">
		<div id="noob_bulletactivecolor-label" class="form-label">
			<label for="noob_bulletactivecolor" class="optional">
				'.$this->translate('Color Of Active Bullets').'
			</label>
		</div>
		<div id="noob_bulletactivecolor-element" class="form-element">
			<p class="description">'.$this->translate('Choose the color of active bullets. (Click on the rainbow below to choose your color.)').'</p>
			<input name="noob_bulletactivecolor" id="noob_bulletactivecolor" value="'.$noob_bulletactivecolor.'" type="text">
			<input name="noobbulletactivecolor" id="noobbulletactivecolor" src="' . $this->layout()->staticBaseUrl . 'application/modules/Advancedslideshow/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>