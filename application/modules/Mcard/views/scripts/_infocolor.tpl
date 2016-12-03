<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _infocolor.tpl 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Mcard/externals/scripts/mooRainbow.js" type="text/javascript"></script>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Mcard/externals/styles/mooRainbow.css');
?> 
<script type="text/javascript">
    window.addEvent('domready', function() {
        var r = new MooRainbow('myRainbowinfo', {
            id: 'myDemoinfo',
            'startColor': [58, 142, 246],
            'onChange': function(color) {
                $('info_color').value = color.hex;
            }
        });
    });
</script>

<?php
	$mptype_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('mptype_id');
$level_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('level_id');
$infoTable = Engine_Api::_()->getItemTable('mcard_info');
$data = $infoTable->getVal($level_id, $mptype_id);
	$info_color="";
	if (isset($data['info_color'])) {
            $info_color = $data['info_color'];
        }
echo '
<div id="info_color-wrapper" class="form-wrapper">
  <div id="info_color-label" class="form-label">
    <label for="info_color" class="optional">
				' . $this->translate('Customize Information Color') . '
    </label>
  </div>
  <div id="info_color-element" class="form-element">
    <p class="description">' . $this->translate('Select the color of the card data. (Click on the rainbow below to choose your color.)') . '</p>
    <input name="info_color" id="info_color" type="text" value='.$info_color.'>
    <input name="myRainbowinfo" id="myRainbowinfo" src="application/modules/Mcard/externals/images/rainbow.png" link="true" type="image">
  </div>
</div>
'
?>
