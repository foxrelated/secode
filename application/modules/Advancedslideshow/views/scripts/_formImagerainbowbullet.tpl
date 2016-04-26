<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowbullet.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$front = Zend_Controller_Front::getInstance();    
    $advancedslideshow_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('advancedslideshow_id', null);

	$action = $front->getRequest()->getActionName();

	$noob_bulletcolor = '#000'; 
	if($action == 'edit' && !empty($advancedslideshow_id)) {
		$advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);
		$noob_elements = @unserialize($advancedslideshow->noob_elements);
        if(!empty($noob_elements['noob_bulletcolor'])) {
			$noob_bulletcolor = $noob_elements['noob_bulletcolor'];
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
		var r = new MooRainbow('noobbulletcolor', { 
			id: 'myDemoBullet',
			'startColor': [58, 142, 246],
			'onChange': function(color) { 
				$('noob_bulletcolor').value = color.hex;
                
			}
		});
	});	
    
</script>

<?php
echo '
	<div id="noob_bulletcolor-wrapper" class="form-wrapper">
		<div id="noob_bulletcolor-label" class="form-label">
			<label for="noob_bulletcolor" class="optional">
				'.$this->translate('Color Of Inactive Bullets').'
			</label>
		</div>
		<div id="noob_bulletcolor-element" class="form-element">
			<p class="description">'.$this->translate('Choose the color of inactive bullets. (Click on the rainbow below to choose your color.)').'</p>
			<input name="noob_bulletcolor" id="noob_bulletcolor" value="'.$noob_bulletcolor.'" type="text">
			<input name="noobbulletcolor" id="noobbulletcolor" src="' . $this->layout()->staticBaseUrl . 'application/modules/Advancedslideshow/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>