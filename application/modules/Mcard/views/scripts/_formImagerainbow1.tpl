<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerrainbow1.tpl 2010-10-13 9:40:21Z SocialEngineAddOns $
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
        var r = new MooRainbow('myRainbow1', {
            id: 'myDemo1',
            'startColor': [58, 142, 246],
            'onChange': function(color) {
                $('card_bg_color').value = color.hex;
            }
        });
    });
</script>

<?php
	$mptype_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('mptype_id');
$level_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('level_id');
$infoTable = Engine_Api::_()->getItemTable('mcard_info');
$data = $infoTable->getVal($level_id, $mptype_id);
	$bg_color="";
	if (isset($data['card_bg_color'])) {
            $bg_color = $data['card_bg_color'];
        }
echo '
<div id="card_bg_color-wrapper" class="form-wrapper">
  <div id="card_bg_color-label" class="form-label">
    <label for="card_bg_color" class="optional">
				' . $this->translate('Customize Background Color') . '
    </label>
  </div>
  <div id="card_bg_color-element" class="form-element">
    <p class="description">' . $this->translate('Select the color of the membership card. (Click on the rainbow below to choose your color.)') . '</p>
    <input name="card_bg_color" id="card_bg_color" type="text" value='.$bg_color.'>
    <input name="myRainbow1" id="myRainbow1" src="application/modules/Mcard/externals/images/rainbow.png" link="true" type="image">
  </div>
</div>
'
?>
