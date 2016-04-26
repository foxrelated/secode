<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _slider.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/slider.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/slider.js');
?>

<?php
$currency_symbol = Engine_Api::_()->siteevent()->getCurrencySymbol();
$minPrice = ($this->minPrice) ? $this->minPrice : 0;
$maxPrice = ($this->maxPrice) ? $this->maxPrice : 999;
$searchMinPrice = Zend_Controller_Front::getInstance()->getRequest()->getParam('minPrice');
$searchMaxPrice = Zend_Controller_Front::getInstance()->getRequest()->getParam('maxPrice');
?>

<?php if (isset($this->locationPage) && $this->locationPage): ?>

    <div class="form-wrapper">
        <div class="form-label" id="minmax_slider-label"><label><?php echo $this->translate('Price Range'); ?></label></div>  
        <div class="form-element" >
            <div class="slide_container" >
                <div class="slider_gutter" id="minmax_slider" >
                    <div id="slider_minmax_gutter_l" class="slider_gutter_item iconsprite_controls"></div>
                    <div id="slider_minmax_gutter_m" class="slider_gutter_item gutter iconsprite_controls">
                        <div id="slider_bkg_img"></div>
                        <div id="slider_minmax_minKnobA" class="knob"></div>
                        <div id="slider_minmax_maxKnobA" class="knob"></div>
                    </div>
                    <div id="slider_minmax_gutter_r" class="slider_gutter_item iconsprite_controls"> </div>
                </div>
                <div id="slider_minmax_min" class="fleft price_range_txt"> </div>
                <div id="slider_minmax_max" class="fright price_range_txt"> </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
<?php else: ?>
    <li>
        <span id="minmax_slider-label" style="margin-bottom:7px;"><label><?php echo $this->translate('Price Range'); ?></label></span>
        <div class="slide_container" >
            <div class="slider_gutter" id="minmax_slider" >
                <div id="slider_minmax_gutter_l" class="slider_gutter_item iconsprite_controls"></div>
                <div id="slider_minmax_gutter_m" class="slider_gutter_item gutter iconsprite_controls">
                    <div id="slider_bkg_img"></div>
                    <div id="slider_minmax_minKnobA" class="knob"></div>
                    <div id="slider_minmax_maxKnobA" class="knob"></div>
                </div>
                <div id="slider_minmax_gutter_r" class="slider_gutter_item iconsprite_controls"> </div>
            </div>
            <div id="slider_minmax_min" class="fleft price_range_txt"> </div>
            <div id="slider_minmax_max" class="fright price_range_txt"> </div>
            <div class="clearfix"></div>
        </div>
    </li>
<?php endif; ?>
<!-- End Slider with two  knobs-->

<script type="text/javascript" >
// ON LOAD 
    en4.core.runonce.add(function() {
        var mySlideA = new Slider($('slider_minmax_gutter_m'), $('slider_minmax_minKnobA'), $('slider_bkg_img'), {
            start: <?php echo $minPrice ?>,
            end: <?php echo $maxPrice ?>,
            offset: 8,
            snap: false,
            onChange: function(pos) {

                $('minPrice').value = pos.minpos;
                $('maxPrice').value = pos.maxpos;
                $('slider_minmax_min').innerHTML = '<?php echo $currency_symbol; ?> ' + pos.minpos;
                $('slider_minmax_max').innerHTML = '<?php echo $currency_symbol; ?> ' + pos.maxpos;
            }
        },
        $('slider_minmax_maxKnobA')).setMin(<?php echo ($searchMinPrice) ? $searchMinPrice : $minPrice ?>).setMax(<?php echo ($searchMaxPrice) ? $searchMaxPrice : $maxPrice ?>);
    });
</script>