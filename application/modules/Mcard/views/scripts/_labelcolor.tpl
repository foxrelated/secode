<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _labelcolor.tpl 2010-10-13 9:40:21Z SocialEngineAddOns $
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
        var r = new MooRainbow('myRainbowlabel', {
            id: 'myDemolabel',
            'startColor': [58, 142, 246],
            'onChange': function(color) {
                $('label_color').value = color.hex;
            }
        });
    });
</script>

<?php
$mptype_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('mptype_id');
$level_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('level_id');
$infoTable = Engine_Api::_()->getItemTable('mcard_info');
$data = $infoTable->getVal($level_id, $mptype_id);
	$label_color="";
	if (isset($data['label_color'])) {
            $label_color = $data['label_color'];
        }
echo '
<div id="label_color-wrapper" class="form-wrapper">
  <div id="label_color-label" class="form-label">
    <label for="label_color" class="optional">
				' . $this->translate('Customize Label / Title Color') . '
    </label>
  </div>
  <div id="label_color-element" class="form-element">
    <p class="description">' . $this->translate('Select the color of the label / title for the cards. (Click on the rainbow below to choose your color.)') . '</p>
    <input name="label_color" id="label_color" type="text" value='.$label_color.'>
    <input name="myRainbowlabel" id="myRainbowlabel" src="application/modules/Mcard/externals/images/rainbow.png" link="true" type="image">
  </div>
</div>
'
?>
